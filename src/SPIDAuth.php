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

        if (!empty($errors)) {
            abort(500, 'SAML Response error: '.$this->getSAML()->getLastErrorReason());
        }
        if (!$this->getSAML()->isAuthenticated()) {
            abort(500, 'SAML Authentication error: '.$this->getSAML()->getLastErrorReason());
        }

        $SPIDUser = new SPIDUser($this->getSAML()->getAttributes());

        $idp = $this->getIdpEntityName($this->getSAML()->getLastResponseXML());

        session(['spid_idp' => $idp]);
        session(['spid_sessionIndex' => $this->getSAML()->getSessionIndex()]);
        session(['spid_user' => $SPIDUser]);

        event(new LoginEvent($SPIDUser, session('spid_idp')));

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
            $idp = session()->pull('spid_idp');
            $SPIDUser = session()->pull('spid_user');
            session()->save();

            $returnTo = url(config('spid-auth.after_logout_url'));
            event(new LogoutEvent($SPIDUser, $idp));

            return $this->getSAML()->logout($returnTo, [], null, $sessionIndex);
        }

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
        $idps = array_values(config('spid-idps'));
        return response()->json(['spidProviders' => $idps]);
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
        $config['sp']['assertionConsumerService']['url'] = url('/').'/'.config('spid-auth.routes_prefix').'/acs';
        $config['sp']['singleLogoutService']['url'] = url('/').'/'.config('spid-auth.routes_prefix').'/logout';

        foreach (config('spid-auth.sp_requested_attributes') as $attr) {
            $config['sp']['attributeConsumingService']['requestedAttributes'][] = ['name' => $attr];
        }

        $config['organization']['it-IT']['name'] = $config['organization']['en-US']['name'] = config('spid-auth.sp_organization_name');
        $config['organization']['it-IT']['displayname'] = $config['organization']['en-US']['displayname'] = config('spid-auth.sp_organization_display_name');
        $config['organization']['it-IT']['url'] = $config['organization']['en-US']['url'] = config('spid-auth.sp_organization_url');

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
        $entityName = "";
        foreach($idps as $idp => $info) {
            if ($info['entityId'] == $responseIssuer) {
                $entityName = $info['entityName'];
            }
         }
         return $entityName;
    }
}
