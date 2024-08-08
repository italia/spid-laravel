<?php

namespace Italia\SPIDAuth\Tests;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Italia\SPIDAuth\SPIDAuth;
use Mockery as m;
use OneLogin\Saml2\Auth as SAMLAuth;
use OneLogin\Saml2\Constants as SAMLConstants;
use OneLogin\Saml2\Error as SAMLError;
use OneLogin\Saml2\Utils as SAMLUtils;
use Orchestra\Testbench\TestCase;

class SPIDAuthBaseTestCase extends TestCase
{
    protected $loginURL;
    protected $acsURL;
    protected $logoutURL;
    protected $afterLoginURL;
    protected $afterLogoutURL;
    protected $loginView;
    protected $metadataURL;
    protected $providersURL;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginURL = URL::route('spid-auth_login');
        $this->doLoginURL = URL::route('spid-auth_do-login');
        $this->acsURL = URL::route('spid-auth_acs');
        $this->afterLoginURL = $this->app['config']->get('spid-auth.after_login_url');
        $this->afterLogoutURL = $this->app['config']->get('spid-auth.after_logout_url');
        $this->logoutURL = URL::route('spid-auth_logout');
        $this->loginView = $this->app['config']->get('spid-auth.login_view');
        $this->metadataURL = URL::route('spid-auth_metadata');
        $this->providersURL = URL::route('spid-auth_providers');
    }

    protected function tearDown(): void
    {
        m::close();
    }

    protected function getPackageProviders($app)
    {
        return ['Italia\SPIDAuth\ServiceProvider'];
    }

    protected function setSPIDAuthMock(?array $testSettings = [])
    {
        $testRedirectURL = $this->app['config']->get('spid-idps.test.singleSignOnService.url');
        $responseXMLFile = ($testSettings['responseXmlFile'] ?? false) ? $testSettings['responseXmlFile'] : 'valid_level1.xml';
        $responseXML = file_get_contents(__DIR__ . '/responses/' . $responseXMLFile);
        $compiledResponseXML = str_replace('{{IssueInstant}}', SAMLUtils::parseTime2SAML(time()), $responseXML);
        $compiledResponseXML = str_replace('{{ResponseIssueInstant}}', SAMLUtils::parseTime2SAML(time()), $compiledResponseXML);
        $compiledResponseXML = str_replace('{{AssertionIssueInstant}}', SAMLUtils::parseTime2SAML(time()), $compiledResponseXML);
        $SPIDAuth = m::mock(SPIDAuth::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $SAMLAuth = m::mock(SAMLAuth::class);

        $SAMLAuth->shouldReceive('login')->andReturn(
            Response::redirectTo($testRedirectURL)
        );
        $SAMLAuth->shouldReceive('login')->with(null, [], true, false, true)->andReturn('redirectUrl');
        $SAMLAuth->shouldReceive('processResponse')->andReturn(true)->byDefault();
        $SAMLAuth->shouldReceive('processSLO')->andReturn(true)->byDefault();
        $SAMLAuth->shouldReceive('getLastAssertionId')->andReturn('assertionId');
        $SAMLAuth->shouldReceive('getLastAssertionNotOnOrAfter')->andReturn(time() + 300)->byDefault();
        $SAMLAuth->shouldReceive('getLastErrorReason')->andReturn('errorReason')->byDefault();
        $SAMLAuth->shouldReceive('getLastRequestID')->andReturn('UNIQUE_ID');
        $SAMLAuth->shouldReceive('isAuthenticated')->andReturn(true)->byDefault();
        $SAMLAuth->shouldReceive('getAttributes')->andReturn([
            'spidCode' => ['TEST0123456789'],
            'name' => ['Nome'],
            'familyName' => ['Cognome'],
            'fiscalNumber' => ['FSCLNB17A01H501X'],
        ])->byDefault();
        $SAMLAuth->shouldReceive('getLastRequestXML')->andReturn(
            '<?xml version="1.0" encoding="UTF-8"?>
            <samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" IssueInstant="' . SAMLUtils::parseTime2SAML(time()) . '" />'
        );
        $SAMLAuth->shouldReceive('getLastResponseXML')->andReturn($compiledResponseXML)->byDefault();
        $SAMLAuth->shouldReceive('getLastMessageId')->andReturn('sessionId');
        $SAMLAuth->shouldReceive('getNameId')->andReturn('nameId');
        $SAMLAuth->shouldReceive('logout')->with(URL::to($this->afterLogoutURL), [], 'nameId', 'sessionId', false, SAMLConstants::NAMEID_TRANSIENT, 'spid-testenv')->andReturn(
            Response::redirectTo($this->logoutURL)
        )->byDefault();
        $SAMLAuth->shouldReceive('getErrors')->andReturn(false)->byDefault();
        $SAMLAuth->shouldReceive('getSPMetadata')->andReturn()->byDefault();
        $SAMLAuth->shouldReceive('getSettings')->andReturn($SAMLAuth);

        $SAMLAuth->shouldReceive('withErrors')->andReturnUsing(function () {
            return m::self()->shouldReceive('logout')->with(URL::to($this->afterLogoutURL), [], 'nameId', 'sessionId', false, SAMLConstants::NAMEID_TRANSIENT, 'spid-testenv')->andThrow(
                new SAMLError(
                    'The IdP does not support Single Log Out',
                    SAMLError::SAML_SINGLE_LOGOUT_NOT_SUPPORTED
                )
            )->getMock()->shouldReceive('getSPMetadata')->andThrow(
                new SAMLError(
                    'Invalid metadata syntax',
                    SAMLError::SETTINGS_INVALID_SYNTAX
                )
            )->getMock()->shouldReceive('getErrors')->andReturn(['test-error'])->getMock();
        });

        $SAMLAuth->shouldReceive('withErrorReason')->andReturnUsing(function ($errorReason) {
            return m::self()->shouldReceive('getErrors')->andReturn([
                'test-error',
            ])->getMock()->shouldReceive('getLastErrorReason')->andReturn($errorReason);
        });

        $SAMLAuth->shouldReceive('withInvalidBinding')->andReturnUsing(function () {
            return m::self()->shouldReceive('processResponse')->andThrow(
                new SAMLError(
                    'SAML Response not found, Only supported HTTP_POST Binding',
                    SAMLError::SAML_RESPONSE_NOT_FOUND
                )
            )->getMock()->shouldReceive('processSLO')->andThrow(
                new SAMLError(
                    'SAML LogoutRequest/LogoutResponse not found. Only supported HTTP_REDIRECT Binding',
                    SAMLError::SAML_LOGOUTMESSAGE_NOT_FOUND
                )
            )->getMock();
        });

        $SAMLAuth->shouldReceive('unauthenticated')->andReturnUsing(function () {
            return m::self()->shouldReceive('isAuthenticated')->andReturn(false)->getMock();
        });

        $SAMLAuth->shouldReceive('withInvalidAssertionNotOnOrAfter')->andReturnUsing(function () {
            return m::self()->shouldReceive('getLastAssertionNotOnOrAfter')->andReturn('invalid-not-on-or-after')->getMock();
        });

        $SAMLAuth->shouldReceive('withLessAttributes')->andReturnUsing(function () {
            return m::self()->shouldReceive('getAttributes')->andReturn([
                'spidCode' => ['TEST0123456789'],
            ])->getMock();
        });

        $SAMLAuth->shouldReceive('withEmptyAttribute')->andReturnUsing(function () {
            return m::self()->shouldReceive('getAttributes')->andReturn([
                'spidCode' => ['TEST0123456789'],
                'name' => ['Nome'],
                'familyName' => ['Cognome'],
                'fiscalNumber' => '',
            ])->getMock();
        });

        $SAMLAuth->shouldReceive('withLastResponseXML')->andReturnUsing(function ($responseXML) {
            return m::self()->shouldReceive('getLastResponseXML')->andReturn($responseXML)->getMock();
        });

        $SPIDAuth->shouldReceive('getSAML')->andReturn($SAMLAuth);
        $SPIDAuth->shouldReceive('getRandomString')->andReturn('RANDOM_STRING');
        $this->app->instance('Italia\SPIDAuth\SPIDAuth', $SPIDAuth);

        return $SAMLAuth;
    }
}
