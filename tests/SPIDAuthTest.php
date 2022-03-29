<?php

namespace Italia\SPIDAuth\Tests;

use DOMDocument;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Italia\SPIDAuth\Events\LoginEvent;
use Italia\SPIDAuth\Events\LogoutEvent;
use Italia\SPIDAuth\Exceptions\SPIDLoginException;
use Italia\SPIDAuth\Exceptions\SPIDLogoutException;
use Italia\SPIDAuth\Exceptions\SPIDMetadataException;
use Italia\SPIDAuth\SPIDUser;
use OneLogin\Saml2\Utils as SAMLUtils;

class SPIDAuthTest extends SPIDAuthBaseTestCase
{
    public function testLogin()
    {
        $response = $this->get($this->loginURL);

        $response->assertViewIs($this->loginView);
    }

    public function testLoginIfAuthenticated()
    {
        $response = $this->withSession([
            'spid_sessionIndex' => 'sessionIndex',
        ])->get($this->loginURL);

        $response->assertRedirect($this->afterLoginURL);
    }

    public function testLoginIfAuthenticatedWithIntendedURL()
    {
        $response = $this->withSession([
            'spid_sessionIndex' => 'sessionIndex',
            'url.intended' => 'intendedURL',
        ])->get($this->loginURL);

        $response->assertRedirect('intendedURL');
    }

    public function testDoLoginIfAuthenticated()
    {
        $response = $this->withSession([
            'spid_sessionIndex' => 'sessionIndex',
        ])->post($this->doLoginURL);

        $response->assertRedirect($this->afterLoginURL);
    }

    public function testDoLoginIfAuthenticatedWithIntendedURL()
    {
        $response = $this->withSession([
            'spid_sessionIndex' => 'sessionIndex',
            'url.intended' => 'intendedURL',
        ])->post($this->doLoginURL);

        $response->assertRedirect('intendedURL');
    }

    public function testDoLoginWithoutProvider()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::MALFORMED_IDP_IN_USER_REQUEST);

        $response = $this->post($this->doLoginURL);

        $response->assertSessionMissing('spid_idp');
        $response->assertSessionMissing('spid_lastRequestId');
        $response->assertStatus(500);
    }

    public function testDoLoginWithMalformedProvider()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::MALFORMED_IDP_IN_USER_REQUEST);

        $response = $this->post($this->doLoginURL, ['provider' => ['fake' => 'nonexistent']]);

        $response->assertSessionMissing('spid_idp');
        $response->assertSessionMissing('spid_lastRequestId');
        $response->assertStatus(500);
    }

    public function testDoLoginWithNonExistentProvider()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::NONEXISTENT_IDP_IN_USER_REQUEST);

        $response = $this->post($this->doLoginURL, ['provider' => 'nonexistent']);

        $response->assertSessionMissing('spid_idp');
        $response->assertSessionMissing('spid_lastRequestId');
        $response->assertStatus(500);
    }

    public function testDoLogin()
    {
        $this->setSPIDAuthMock();

        $response = $this->withSession([
            'url.intended' => 'intendedURL',
        ])->post($this->doLoginURL, ['provider' => 'test']);

        $this->assertTrue(cache()->has('RANDOM_STRING'));
        $response->assertCookie('spid_idp', 'test');
        $response->assertCookie('spid_lastRequestId');
        $response->assertCookie('spid_lastRequestIssueInstant');
        $response->assertRedirect();
    }

    public function testAcs()
    {
        Event::fake();
        $this->setSPIDAuthMock();

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertSessionHas('spid_idpEntityName', 'Test IdP');
        $response->assertSessionHas('spid_sessionIndex', 'sessionIndex');
        $response->assertSessionHas('spid_nameId', 'nameId');
        $response->assertSessionHas('spid_user');
        $response->assertRedirect($this->afterLoginURL);
        Event::assertDispatched(LoginEvent::class, function ($e) {
            $SPIDUser = $e->getSPIDUser();
            $isSPIDUserReturned = SPIDUser::class === get_class($SPIDUser);
            $areSPIDUserFieldsSet =
                'Nome' === $SPIDUser->name &&
                'Cognome' === $SPIDUser->familyName &&
                'FSCLNB17A01H501X' === $SPIDUser->fiscalNumber &&
                'TEST0123456789' === $SPIDUser->spidCode &&
                null === $SPIDUser->nonExistent;
            $isIdpReturned = 'Test IdP' === $e->getIdp();

            return $isSPIDUserReturned && $areSPIDUserFieldsSet && $isIdpReturned;
        });
        $this->assertInstanceOf(SPIDUser::class, $this->app->make('SPIDAuth')->getSPIDUser());
    }

    public function testAcsWithIntendedURL()
    {
        $this->setSPIDAuthMock();

        cache(['RANDOM_STRING' => [
            'url.intended' => 'intendedURL',
        ]], 300);

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL, [
            'RelayState' => 'RANDOM_STRING',
        ]);

        $this->assertFalse(cache()->has('RANDOM_STRING'));
        $response->assertSessionHas('spid_idpEntityName', 'Test IdP');
        $response->assertSessionHas('spid_sessionIndex', 'sessionIndex');
        $response->assertSessionHas('spid_nameId', 'nameId');
        $response->assertSessionHas('spid_user');
        $response->assertRedirect('intendedURL');
    }

    public function testAcsWithMissingRequestId()
    {
        $this->setSPIDAuthMock();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_REQUEST_ID_MISSING);

        $response = $this->withCookies([
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testAcsWithMalformedSAMLResponse()
    {
        $this->setSPIDAuthMock()->withErrors();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage('SAML response validation error: errorReason');

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testAcsWithInvalidBinding()
    {
        $this->setSPIDAuthMock()->withInvalidBinding();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage('SAML response validation error: SAML Response not found, Only supported HTTP_POST Binding');

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testAcsWithFailedSAMLAuthentication()
    {
        $this->setSPIDAuthMock()->unauthenticated();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_AUTHENTICATION_ERROR);
        $this->expectExceptionMessage('SAML authentication error: errorReason');

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testAcsWithReplayAttack()
    {
        $this->setSPIDAuthMock();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_RESPONSE_ALREADY_PROCESSED);

        $lastRequestIssueInstant = SAMLUtils::parseTime2SAML(time());

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => $lastRequestIssueInstant,
            'spid_idp' => 'test',
        ])->post($this->acsURL);
        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => $lastRequestIssueInstant,
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testLogout()
    {
        $this->testAcs();

        $response = $this->get($this->logoutURL);

        $response->assertSessionMissing('spid_sessionIndex');
        $response->assertSessionMissing('spid_nameId');
        $response->assertRedirect($this->logoutURL);
    }

    public function testSLO()
    {
        Event::fake();
        $this->testLogout();

        $response = $this->get($this->logoutURL . '?SAMLResponse');

        $response->assertSessionMissing('spid_idpEntityName');
        $response->assertSessionMissing('spid_idp');
        $response->assertRedirect($this->afterLogoutURL);
        Event::assertDispatched(LogoutEvent::class, function ($e) {
            $SPIDUser = $e->getSPIDUser();
            $isSPIDUserReturned = SPIDUser::class == get_class($SPIDUser);
            $areSPIDUserFieldsSet =
                'Nome' == $SPIDUser->name &&
                'Cognome' == $SPIDUser->familyName &&
                'FSCLNB17A01H501X' == $SPIDUser->fiscalNumber &&
                'TEST0123456789' == $SPIDUser->spidCode &&
                null == $SPIDUser->nonExistent;
            $isIdpReturned = 'Test IdP' == $e->getIdp();

            return $isSPIDUserReturned && $areSPIDUserFieldsSet && $isIdpReturned;
        });
        $this->assertNull($this->app->make('SPIDAuth')->getSPIDUser());
    }

    public function testSLOWithInvalidBinding()
    {
        $this->setSPIDAuthMock()->withInvalidBinding();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLogoutException::class);
        $this->expectExceptionCode(SPIDLogoutException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage('SAML response validation error: SAML LogoutRequest/LogoutResponse not found. Only supported HTTP_REDIRECT Binding');

        $response = $this->withSession([
            'spid_idpEntityName' => 'spid_idpEntityName',
            'spid_user' => new SPIDUser([]),
            'spid_idp' => 'test',
        ])->get($this->logoutURL . '?SAMLResponse');

        $response->assertStatus(500);
    }

    public function testSLOWithMalformedSAMLResponse()
    {
        $this->setSPIDAuthMock()->withErrors();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLogoutException::class);
        $this->expectExceptionCode(SPIDLogoutException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage('SAML response validation error: errorReason');

        $response = $this->withSession([
            'spid_idpEntityName' => 'spid_idpEntityName',
            'spid_user' => new SPIDUser([]),
            'spid_idp' => 'test',
        ])->get($this->logoutURL . '?SAMLResponse');

        $response->assertStatus(500);
    }

    public function testLogoutIfNotAuthenticated()
    {
        $response = $this->get($this->logoutURL);

        $response->assertRedirect($this->afterLogoutURL);
    }

    public function testLogoutSpOnly()
    {
        $this->app['config']->set('spid-auth.only_sp_logout', true);
        $this->setSPIDAuthMock()->shouldReceive('logout')->never();

        $this->testAcs();

        $response = $this->get($this->logoutURL);

        $response->assertSessionMissing('spid_sessionIndex');
        $response->assertSessionMissing('spid_nameId');
        $response->assertRedirect($this->afterLogoutURL);
        Event::assertDispatched(LogoutEvent::class, function ($e) {
            $SPIDUser = $e->getSPIDUser();
            $isSPIDUserReturned = SPIDUser::class == get_class($SPIDUser);
            $areSPIDUserFieldsSet =
                'Nome' == $SPIDUser->name &&
                'Cognome' == $SPIDUser->familyName &&
                'FSCLNB17A01H501X' == $SPIDUser->fiscalNumber &&
                'TEST0123456789' == $SPIDUser->spidCode &&
                null == $SPIDUser->nonExistent;
            $isIdpReturned = 'Test IdP' == $e->getIdp();

            return $isSPIDUserReturned && $areSPIDUserFieldsSet && $isIdpReturned;
        });
        $this->assertNull($this->app->make('SPIDAuth')->getSPIDUser());
    }

    public function testLogoutWithErrors()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLogoutException::class);
        $this->expectExceptionCode(SPIDLogoutException::SAML_LOGOUT_ERROR);
        $this->expectExceptionMessage('The IdP does not support Single Log Out');
        $this->testAcs();
        $this->setSPIDAuthMock()->withErrors();

        $response = $this->withSession(['spid_idp' => 'test'])->get($this->logoutURL);

        $response->assertStatus(500);
    }

    public function testMetadataSPPrivate()
    {
        $metadata = new DOMDocument();

        $response = $this->get($this->metadataURL);

        $response->assertStatus(200);
        $metadata->loadXML($response->getContent());

        libxml_use_internal_errors(true);
        $ret = $metadata->schemaValidate('tests/xml-schemas/saml-schema-metadata-SPID-SP.xsd');
        $this->libxml_display_errors();

        $this->assertTrue($ret);
    }

    public function testMetadataSPPublic()
    {
        Config::set('spid-auth.sp_contact_persons', [
            'other' => [
                'Public' => true,
                'IPACode' => 'IPACODE',
                'EmailAddress' => 'public_sp@public.org',
            ],
        ]);
        $metadata = new DOMDocument();

        $response = $this->get($this->metadataURL);

        $response->assertStatus(200);
        $metadata->loadXML($response->getContent());

        libxml_use_internal_errors(true);
        $ret = $metadata->schemaValidate('tests/xml-schemas/saml-schema-metadata-SPID-SP.xsd');
        $this->libxml_display_errors();

        $this->assertTrue($ret);
    }

    public function testNotValidMetadata()
    {
        $this->setSPIDAuthMock();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDMetadataException::class);
        $this->expectExceptionMessage('Invalid SP metadata: error');

        $response = $this->get($this->metadataURL);

        $response->assertStatus(500);
    }

    public function testMalformedMetadata()
    {
        $this->setSPIDAuthMock()->withErrors();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDMetadataException::class);
        $this->expectExceptionMessage('Invalid SP metadata: Invalid metadata syntax');

        $response = $this->get($this->metadataURL);

        $response->assertStatus(500);
    }

    public function testMetadataNotExposed()
    {
        $this->app['config']->set('spid-auth.expose_sp_metadata', false);

        $response = $this->get($this->metadataURL);

        $response->assertStatus(404);
    }

    public function testProvidersWithTestIdp()
    {
        $response = $this->get($this->providersURL);

        $expectedIdps = $this->app['config']->get('spid-idps');
        unset($expectedIdps['empty']);
        $response->assertJson(['spidProviders' => array_values($expectedIdps)]);
        $response->assertJsonFragment(['entityId' => $this->app['config']->get('spid-idps.test')['entityId']]);
    }

    public function testProvidersWithoutTestIdp()
    {
        $this->app['config']->set('spid-auth.test_idp', false);

        $response = $this->get($this->providersURL);

        $response->assertJsonMissing(['entityId' => $this->app['config']->get('spid-idps.test')['entityId']]);
    }

    public function testProvidersWithValidator()
    {
        $response = $this->get($this->providersURL);

        $expectedIdps = $this->app['config']->get('spid-idps');
        unset($expectedIdps['empty']);
        $response->assertJson(['spidProviders' => array_values($expectedIdps)]);
        $response->assertJsonFragment(['entityId' => $this->app['config']->get('spid-idps.validator')['entityId']]);
    }

    public function testProvidersWithoutValidator()
    {
        $this->app['config']->set('spid-auth.validator_idp', false);

        $response = $this->get($this->providersURL);

        $response->assertJsonMissing(['entityId' => $this->app['config']->get('spid-idps.validator')['entityId']]);
    }

    public function testProvidersNotExposed()
    {
        $this->app['config']->set('spid-auth.expose_idps_json', false);

        $response = $this->get($this->providersURL);

        $response->assertStatus(404);
    }

    private function libxml_display_errors()
    {
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            echo $this->libxml_display_error($error);
        }
        libxml_clear_errors();
    }

    private function libxml_display_error($error)
    {
        $return = "<br/>\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $error->code</b>: ";
                break;
        }
        $return .= trim($error->message);
        if ($error->file) {
            $return .= " in <b>$error->file</b>";
        }
        $return .= " on line <b>$error->line</b>\n";

        return $return;
    }
}
