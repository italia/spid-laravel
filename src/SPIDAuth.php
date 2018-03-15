<?php
/**
 * This class implements a Laravel Controller for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth;

use Italia\SPIDAuth\Events\LoginEvent;
use Italia\SPIDAuth\Events\LogoutEvent;

use Illuminate\Routing\Controller;

use Carbon\Carbon;

use OneLogin_Saml2_Auth;
use OneLogin_Saml2_Error;
use OneLogin_Saml2_Utils;

use DOMDocument;

class SPIDAuth extends Controller
{

    /**
     * OneLogin_Saml2_Auth instance.
     *
     * @var OneLogin_Saml2_Auth
     */
    private $saml;

    /**
     * Show a view with a SPID button, if authenticated redirect to after_login_url.
     *
     * @return View Display the page for the Identity Provider selection, if not authenticated.
     * @return Response Redirect to Redirect to the intended or configured URL if authenticated.
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
     */
    public function doLogin()
    {
        if (!$this->isAuthenticated()) {
            $idp = request('provider');
            if (empty($idp)) {
                abort(400, 'Malformed request: "provider" parameter not present');
            }
            session(['spid_idp' => $idp]);
            session()->save();

            return $this->getSAML()->login(null, [], true);
        }

        return redirect()->intended(config('spid-auth.after_login_url'));
    }

    /**
     * Attribute Consuming Service.
     *
     * Process the POST response from Identity Providers, set session variables
     * and redirect to the intended or configured URL.
     * Fire LoginEvent with SPIDUser (also stored in session).
     *
     * @return Response Redirect to the intended or configured URL.
     */
    public function acs()
    {
        $this->getSAML()->processResponse();

        $errors = $this->getSAML()->getErrors();
        $assertionId = $this->getSAML()->getLastAssertionId();
        $assertionNotOnOrAfter = $this->getSAML()->getLastAssertionNotOnOrAfter();

        if (!empty($errors)) {
            logger()->error('SAML Response error: '.$this->getSAML()->getLastErrorReason());
            abort(500, 'SAML Response error: '.$this->getSAML()->getLastErrorReason());
        }
        if (cache()->has($assertionId)) {
            logger()->error('SAML Response error: assertion with id ' . $assertionId . ' was already processed');
            abort(500, 'SAML Response error: assertion with id ' . $assertionId . ' was already processed');
        }
        if (!$this->getSAML()->isAuthenticated()) {
            logger()->error('SAML Authentication error: '.$this->getSAML()->getLastErrorReason());
            abort(500, 'SAML Authentication error: '.$this->getSAML()->getLastErrorReason());
        }

        $assertionExpiry = Carbon::createFromTimestampUTC($assertionNotOnOrAfter);
        $assertionExpiry->timezone = config('app.timezone');
        cache([$assertionId => ''], $assertionExpiry);

        $SPIDUser = new SPIDUser($this->getSAML()->getAttributes());
        $idpEntityName = $this->getIdpEntityName($this->getSAML()->getLastResponseXML());

        session(['spid_idp_entity_name' => $idpEntityName]);
        session(['spid_sessionIndex' => $this->getSAML()->getSessionIndex()]);
        session(['spid_user' => $SPIDUser]);

        event(new LoginEvent($SPIDUser, session('spid_idp_entity_name')));

        session()->reflash();
        return redirect()->intended(config('spid-auth.after_login_url'));
    }

    /**
     * Attempt logout with the selected SPID Identity Provider.
     *
     * @return Response Redirect to after_logout_url.
     */
    public function logout()
    {
        if ($this->isAuthenticated()) {
            $sessionIndex = session()->pull('spid_sessionIndex');
            session()->forget('spid_idp');
            $idpEntityName = session()->pull('spid_idp_entity_name');
            $SPIDUser = session()->pull('spid_user');
            session()->save();

            $returnTo = url(config('spid-auth.after_logout_url'));
            event(new LogoutEvent($SPIDUser, $idpEntityName));

            return $this->getSAML()->logout($returnTo, [], null, $sessionIndex);
        }

        session()->reflash();
        return redirect(config('spid-auth.after_logout_url'));
    }

    /**
     * Check if the current session is authenticated with SPID.
     *
     * @return boolean Whether the current session is authenticated with SPID.
     */
    public function isAuthenticated()
    {
        return session()->has('spid_sessionIndex');
    }

    /**
     * Metadata endpoint for this Service Provider.
     *
     * @return Response XML metadata of this Service Provider.
     */
    public function metadata()
    {
        $metadata = $this->getSAML()->getSettings()->getSPMetadata();
        $errors = $this->getSAML()->getSettings()->validateMetadata($metadata);
        if (empty($errors)) {
            return response($metadata, '200')->header('Content-Type', 'text/xml');
        }
        else {
            throw new OneLogin_Saml2_Error('Invalid SP metadata: ' . implode(', ', $errors), OneLogin_Saml2_Error::METADATA_SP_INVALID);
        }
    }

    /**
     * Identity Providers list endpoint for this Service Provider.
     * This is used by the SPID smart button.
     *
     * @return Response JSON list of Identity Providers configured for this Service Provider.
     */
    public function providers()
    {
        $idps = config('spid-idps');
        
        if (!config('spid-auth.test_idp')) {
            unset($idps['test']);
        }

        $idps_values = array_values($idps);
        return response()->json(['spidProviders' => $idps_values]);
    }

    /**
     * Return the current authenticated SPIDUser.
     *
     * @return SPIDUser|null The current authenticated SPIDUser or null if not authenticated.
     */
    public function getSPIDUser()
    {
        return session()->has('spid_user') ? session()->get('spid_user') : null;
    }


    /**
     * Return configuration array for OneLogin_Saml2_Auth.
     *
     * @param string Identity Provider name.
     * @return array Configuration array for OneLogin_Saml2_Auth.
     */
    protected function getSAMLConfig($idp)
    {
        $config = config('spid-saml');

        $config['sp']['entityId'] = config('spid-auth.sp_entity_id');
        $config['sp']['attributeConsumingService']['serviceName'] = config('spid-auth.sp_service_name');
        $config['sp']['assertionConsumerService']['url'] = url('/').'/'.config('spid-auth.routes_prefix').'/acs';
        $config['sp']['singleLogoutService']['url'] = url('/').'/'.config('spid-auth.routes_prefix').'/logout';
        $config['sp']['x509cert'] = config('spid-auth.sp_certificate');
        $config['sp']['privateKey'] = config('spid-auth.sp_private_key');

        foreach (config('spid-auth.sp_requested_attributes') as $attr) {
            $config['sp']['attributeConsumingService']['requestedAttributes'][] = ['name' => $attr];
        }

        $config['organization']['it']['name'] = $config['organization']['en']['name'] = config('spid-auth.sp_organization_name');
        $config['organization']['it']['displayname'] = $config['organization']['en']['displayname'] = config('spid-auth.sp_organization_display_name');
        $config['organization']['it']['url'] = $config['organization']['en']['url'] = config('spid-auth.sp_organization_url');

        $idps = config('spid-idps');

        $config['idp'] = $idps[$idp];

        return $config;
    }

    /**
     * Return the SAML instance configured for the current selected Identity Provider.
     *
     * @return OneLogin_Saml2_Auth The SAML instance configured for the current selected Identity Provider.
     */
    protected function getSAML()
    {
        $idp = session('spid_idp') ?: 'test';

        if (empty($this->saml)) {
            $this->saml = new OneLogin_Saml2_Auth($this->getSAMLConfig($idp));
        }

        return $this->saml;
    }

    /**
    * Return the IdP entityName associated with a given SAML response in XML format (issuer).
    *
    * @return string|null The entityName associated with the given SAML response in XML format (issuer).
    */
    protected function getIdpEntityName(string $responseXML)
    {
        $responseDOM = new DOMDocument();
        $responseDOM->loadXML($responseXML);
        $responseIssuer = OneLogin_Saml2_Utils::query($responseDOM, '/samlp:Response/saml:Issuer')->item(0)->textContent;
        $idps = config('spid-idps');
        $idpEntityName = '';
        $idp = '';
        foreach($idps as $idp) {
            if ($idp['entityId'] == $responseIssuer) {
                $idpEntityName = $idp['entityName'];
            }
         }
         return $idpEntityName;
    }
}
