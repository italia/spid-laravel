<?php
/**
 * This class implements a Laravel Controller for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth;

use Carbon\Carbon;
use DOMDocument;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Italia\SPIDAuth\Events\LoginEvent;
use Italia\SPIDAuth\Events\LogoutEvent;
use Italia\SPIDAuth\Exceptions\SPIDConfigurationException;
use Italia\SPIDAuth\Exceptions\SPIDLoginException;
use Italia\SPIDAuth\Exceptions\SPIDLogoutException;
use Italia\SPIDAuth\Exceptions\SPIDMetadataException;
use OneLogin\Saml2\Auth as SAMLAuth;
use OneLogin\Saml2\Constants as SAMLConstants;
use OneLogin\Saml2\Error as SAMLError;
use OneLogin\Saml2\Utils as SAMLUtils;

class SPIDAuth extends Controller
{
    /**
     * SAMLAuth instance.
     *
     * @var SAMLAuth
     */
    private $saml;

    /**
     * Show the configured login_view with a SPID button, if authenticated redirect to after_login_url.
     *
     * @return \Illuminate\Support\Facades\View display the page for the Identity Provider selection, if not authenticated
     * @return Response redirect to the intended or configured URL if authenticated
     */
    public function login()
    {
        if (!$this->isAuthenticated()) {
            return view(config('spid-auth.login_view'));
        }

        return redirect()->intended(config('spid-auth.after_login_url'));
    }

    /**
     * Attempt login with the selected SPID Identity Provider.
     *
     * @return RedirectResponse redirect to the intended or configured after_login_url if authenticated
     */
    public function doLogin(): RedirectResponse
    {
        if (!$this->isAuthenticated()) {
            $idp = request('provider');
            if (empty($idp)) {
                abort(400, 'Malformed request: "provider" parameter not present');
            }
            session(['spid_idp' => $idp]);
            session()->save();

            return $this->getSAML()->login();
        }

        return redirect()->intended(config('spid-auth.after_login_url'));
    }

    /**
     * Attribute Consuming Service.
     *
     * Process the POST response from Identity Providers, set session variables
     * and redirect to the intended or configured after_login_url.
     * Fire LoginEvent with SPIDUser (also stored in session).
     *
     * @throws SPIDLoginException
     *
     * @return RedirectResponse redirect to the intended or configured URL
     */
    public function acs(): RedirectResponse
    {
        try {
            $this->getSAML()->processResponse();
        } catch (SAMLError $e) {
            throw new SPIDLoginException('SAML response validation error: ' . $e->getMessage(), SPIDLoginException::SAML_VALIDATION_ERROR);
        }

        $errors = $this->getSAML()->getErrors();
        $assertionId = $this->getSAML()->getLastAssertionId();
        $assertionNotOnOrAfter = $this->getSAML()->getLastAssertionNotOnOrAfter();

        if (!empty($errors)) {
            logger()->error('SAML Response error: ' . $this->getSAML()->getLastErrorReason());
            throw new SPIDLoginException('SAML response validation error: ' . implode(', ', $errors), SPIDLoginException::SAML_VALIDATION_ERROR);
        }
        if (cache()->has($assertionId)) {
            logger()->error('SAML Response error: assertion with id ' . $assertionId . ' was already processed');
            throw new SPIDLoginException('SAML Response error: assertion with id ' . $assertionId . ' was already processed', SPIDLoginException::SAML_RESPONSE_ALREADY_PROCESSED);
        }
        if (!$this->getSAML()->isAuthenticated()) {
            logger()->error('SAML Authentication error: ' . $this->getSAML()->getLastErrorReason());
            throw new SPIDLoginException('SAML Authentication error: ' . $this->getSAML()->getLastErrorReason(), SPIDLoginException::SAML_AUTHENTICATION_ERROR);
        }

        $assertionExpiry = Carbon::createFromTimestampUTC($assertionNotOnOrAfter);
        $assertionExpiry->timezone = config('app.timezone');
        cache([$assertionId => ''], $assertionExpiry);

        $SPIDUser = new SPIDUser($this->getSAML()->getAttributes());
        $idpEntityName = $this->getIdpEntityName($this->getSAML()->getLastResponseXML());

        session(['spid_idp_entity_name' => $idpEntityName]);
        session(['spid_sessionIndex' => $this->getSAML()->getSessionIndex()]);
        session(['spid_nameId' => $this->getSAML()->getNameId()]);
        session(['spid_user' => $SPIDUser]);

        event(new LoginEvent($SPIDUser, session('spid_idp_entity_name')));

        session()->reflash();

        return redirect()->intended(config('spid-auth.after_login_url'));
    }

    /**
     * Attempt logout with the selected SPID Identity Provider.
     *
     * @throws SPIDLogoutException
     *
     * @return RedirectResponse redirect to after_logout_url
     */
    public function logout(): RedirectResponse
    {
        if ($this->isAuthenticated()) {
            $sessionIndex = session()->pull('spid_sessionIndex');
            $nameId = session()->pull('spid_nameId');
            $returnTo = url(config('spid-auth.after_logout_url'));
            $idpEntityId = $this->getIdps()[session()->get('spid_idp')]['entityId'];
            session()->save();

            try {
                return $this->getSAML()->logout($returnTo, [], $nameId, $sessionIndex, false, SAMLConstants::NAMEID_TRANSIENT, $idpEntityId);
            } catch (SAMLError $e) {
                throw new SPIDLogoutException($e->getMessage(), SPIDLogoutException::SAML_LOGOUT_ERROR);
            }
        }

        if (request()->has('SAMLResponse')) {
            $idpEntityName = session()->pull('spid_idp_entity_name');
            $SPIDUser = session()->pull('spid_user');
            $idp = session()->pull('spid_idp');
            event(new LogoutEvent($SPIDUser, $idpEntityName));
            try {
                $this->getSAML($idp)->processSLO();
            } catch (SAMLError $e) {
                throw new SPIDLogoutException('SAML response validation error: ' . $e->getMessage(), SPIDLogoutException::SAML_VALIDATION_ERROR);
            }

            $errors = $this->getSAML()->getErrors();

            if (!empty($errors)) {
                logger()->error('SAML Response error: ' . $this->getSAML()->getLastErrorReason());
                throw new SPIDLogoutException('SAML response validation error: ' . implode(', ', $errors), SPIDLogoutException::SAML_VALIDATION_ERROR);
            }
        }

        session()->reflash();

        return redirect(config('spid-auth.after_logout_url'));
    }

    /**
     * Check if the current session is authenticated with SPID.
     *
     * @return bool whether the current session is authenticated with SPID
     */
    public function isAuthenticated(): bool
    {
        return session()->has('spid_sessionIndex');
    }

    /**
     * Metadata endpoint for this Service Provider.
     *
     * @throws SPIDMetadataException
     *
     * @return Response XML metadata of this Service Provider
     */
    public function metadata(): Response
    {
        try {
            $metadata = $this->getSAML()->getSettings()->getSPMetadata();
        } catch (Exception $e) {
            throw new SPIDMetadataException('Invalid SP metadata: ' . $e->getMessage());
        }
        $errors = $this->getSAML()->getSettings()->validateMetadata($metadata);
        if (empty($errors)) {
            return response($metadata, '200')->header('Content-Type', 'text/xml');
        } else {
            throw new SPIDMetadataException('Invalid SP metadata: ' . implode(', ', $errors));
        }
    }

    /**
     * Identity Providers list endpoint for this Service Provider.
     * This is used by the SPID smart button.
     *
     * @return JsonResponse JSON list of Identity Providers configured for this Service Provider
     */
    public function providers(): JsonResponse
    {
        $idps_values = array_values($this->getIdps());

        return response()->json(['spidProviders' => $idps_values]);
    }

    /**
     * Return the current authenticated SPIDUser.
     *
     * @return SPIDUser|null the current authenticated SPIDUser or null if not authenticated
     */
    public function getSPIDUser()
    {
        return session()->get('spid_user', null);
    }

    /**
     * Return configured IdPs array.
     *
     * @return array configured Identity providers
     */
    protected function getIdps(): array
    {
        $idps = config('spid-idps');

        if (!config('spid-auth.test_idp')) {
            unset($idps['test']);
        } else {
            $idps['test']['entityId'] = config('spid-auth.test_idp.entityId');
            $idps['test']['singleSignOnService']['url'] = config('spid-auth.test_idp.sso_endpoint');
            $idps['test']['singleLogoutService']['url'] = config('spid-auth.test_idp.slo_endpoint');
            $idps['test']['x509cert'] = config('spid-auth.test_idp.x509cert');
        }

        return $idps;
    }

    /**
     * Check wether SPIDAuth package is properly configured.
     *
     * @return string|bool true if no configuration issues are found, error string otherwise
     */
    protected function isConfigured()
    {
        if (!is_string(config('spid-auth.sp_entity_id')) || empty(config('spid-auth.sp_entity_id'))) {
            return 'Service provider entity ID not set';
        }

        if (!is_string(config('spid-auth.sp_base_url')) || empty(config('spid-auth.sp_base_url'))) {
            return 'Service provider base URL not set';
        }

        if (!is_string(config('spid-auth.sp_service_name')) || empty(config('spid-auth.sp_service_name'))) {
            return 'Service provider service name not set';
        }

        if (!is_array(config('spid-auth.sp_requested_attributes')) || empty(config('spid-auth.sp_requested_attributes'))) {
            return 'Service provider requested attributes not set';
        }

        if (!is_string(config('spid-auth.sp_certificate')) || empty(config('spid-auth.sp_certificate'))) {
            if (!is_readable(config('spid-auth.sp_certificate_file'))) {
                return 'Service provider certificate not set';
            }
        }

        if (!is_string(config('spid-auth.sp_private_key')) || empty(config('spid-auth.sp_private_key'))) {
            if (!is_readable(config('spid-auth.sp_private_key_file'))) {
                return 'Service provider private key not set';
            }
        }

        return true;
    }

    /**
     * Return configuration array for SAMLAuth.
     *
     * @param string Identity Provider name
     *
     * @return array configuration array for SAMLAuth
     */
    protected function getSAMLConfig(string $idp): array
    {
        $configStatus = $this->isConfigured();

        if (true !== $configStatus) {
            throw new SPIDConfigurationException($configStatus);
        }

        $config = config('spid-saml');

        $config['sp']['entityId'] = config('spid-auth.sp_entity_id');
        $config['sp']['attributeConsumingService']['serviceName'] = config('spid-auth.sp_service_name');
        $config['sp']['assertionConsumerService']['url'] = config('spid-auth.sp_base_url') . '/' . config('spid-auth.routes_prefix') . '/acs';
        $config['sp']['singleLogoutService']['url'] = config('spid-auth.sp_base_url') . '/' . config('spid-auth.routes_prefix') . '/logout';

        if (!is_string(config('spid-auth.sp_private_key')) || empty(config('spid-auth.sp_private_key'))) {
            $keyData = file_get_contents(config('spid-auth.sp_private_key_file'));
            $key = openssl_pkey_get_private($keyData);

            if (!$key) {
                throw new SPIDConfigurationException('Private key not valid');
            }

            $config['sp']['privateKey'] = SAMLUtils::formatPrivateKey($keyData, false);
        } else {
            $config['sp']['privateKey'] = config('spid-auth.sp_private_key');
        }

        if (!is_string(config('spid-auth.sp_certificate')) || empty(config('spid-auth.sp_certificate'))) {
            $certificateData = file_get_contents(config('spid-auth.sp_certificate_file'));
            $certificate = openssl_pkey_get_public($certificateData);

            if (!$certificate) {
                throw new SPIDConfigurationException('Certificate not valid');
            }

            $config['sp']['x509cert'] = SAMLUtils::formatCert($certificateData, false);
        } else {
            $config['sp']['x509cert'] = config('spid-auth.sp_certificate');
        }

        foreach (config('spid-auth.sp_requested_attributes') as $attr) {
            $config['sp']['attributeConsumingService']['requestedAttributes'][] = ['name' => $attr];
        }

        $config['organization']['it']['name'] = $config['organization']['en']['name'] = config('spid-auth.sp_organization_name');
        $config['organization']['it']['displayname'] = $config['organization']['en']['displayname'] = config('spid-auth.sp_organization_display_name');
        $config['organization']['it']['url'] = $config['organization']['en']['url'] = config('spid-auth.sp_organization_url');

        $idps = $this->getIdps();

        $config['idp'] = $idps[$idp];

        return $config;
    }

    /**
     * Return the SAML instance configured for the current selected Identity Provider.
     *
     * @return SAMLAuth SAML instance configured for the current selected Identity Provider
     */
    protected function getSAML(string $idp = null): SAMLAuth
    {
        $session_idp = session('spid_idp') ?: 'test';
        $idp = $idp ?: $session_idp;

        if (empty($this->saml) || $this->saml->getSettings()->getIdPData()['provider'] != $idp) {
            $this->saml = new SAMLAuth($this->getSAMLConfig($idp));
        }

        return $this->saml;
    }

    /**
     * Return the IdP entityName associated with a given SAML response in XML format (issuer).
     *
     * @param string XML response from IdP
     *
     * @return string|null entityName associated with the given SAML response in XML format (issuer)
     */
    protected function getIdpEntityName(string $responseXML)
    {
        $responseDOM = new DOMDocument();
        $responseDOM->loadXML($responseXML);
        $responseIssuer = SAMLUtils::query($responseDOM, '/samlp:Response/saml:Issuer')->item(0)->textContent;
        $idps = $this->getIdps();
        $idpEntityName = '';
        foreach ($idps as $idp) {
            if ($idp['entityId'] == $responseIssuer) {
                $idpEntityName = $idp['entityName'];
            }
        }

        return $idpEntityName;
    }
}
