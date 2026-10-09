<?php

namespace Italia\SPIDAuth\Tests;

use Italia\SPIDAuth\Exceptions\SPIDConfigurationException;
use Orchestra\Testbench\TestCase;
use ReflectionClass;

class SPIDAuthConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        \OneLogin\Saml2\Utils::setProxyVars(false);
        \OneLogin\Saml2\Utils::setBaseURL('');
        unset(
            $_SERVER['HTTP_X_FORWARDED_PROTO'],
            $_SERVER['HTTP_X_FORWARDED_HOST'],
            $_SERVER['HTTP_X_FORWARDED_PORT']
        );

        parent::tearDown();
    }

    public function testMissingEntityId()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider entity ID not set');
        config(['spid-auth.sp_entity_id' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingAcsIndex()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider AssertionConsumerService index not set');
        config(['spid-auth.sp_acs_index' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingAttributeIndex()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider AttributeConsumingService index not set');
        config(['spid-auth.sp_attributes_index' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingBaseURL()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider base URL not set');
        config(['spid-auth.sp_base_url' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingServiceName()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider service name not set');
        config(['spid-auth.sp_service_name' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingRequestedAttributes()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider requested attributes not set');
        config(['spid-auth.sp_requested_attributes' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingCertificate()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider certificate not set');
        config(['spid-auth.sp_certificate' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingPrivateKey()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Service provider private key not set');
        config(['spid-auth.sp_private_key' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testCertificateFile()
    {
        $certificate = config('spid-auth.sp_certificate');
        config(['spid-auth.sp_certificate' => null]);
        config(['spid-auth.sp_certificate_file' => __DIR__ . '/secrets/certificate.crt']);
        $SPIDAuthConfig = $this->getSPIDAuthConfig();
        $this->assertEquals($certificate, $SPIDAuthConfig['sp']['x509cert']);
    }

    public function testPrivateKeyFile()
    {
        $privateKey = config('spid-auth.sp_private_key');
        config(['spid-auth.sp_private_key' => null]);
        config(['spid-auth.sp_private_key_file' => __DIR__ . '/secrets/private.key']);
        $SPIDAuthConfig = $this->getSPIDAuthConfig();
        $this->assertEquals($privateKey, $SPIDAuthConfig['sp']['privateKey']);
    }

    public function testInvalidCertificateFile()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Certificate not valid');
        config(['spid-auth.sp_certificate' => null]);
        config(['spid-auth.sp_certificate_file' => __DIR__ . '/secrets/certificate_invalid.crt']);
        $SPIDAuthConfig = $this->getSPIDAuthConfig();
    }

    public function testInvalidPrivateKeyFile()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('Private key not valid');
        config(['spid-auth.sp_private_key' => null]);
        config(['spid-auth.sp_private_key_file' => __DIR__ . '/secrets/private_invalid.key']);
        $SPIDAuthConfig = $this->getSPIDAuthConfig();
    }

    public function testMissingSpidLevel()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('SPID authentication level name wrong or not set');
        config(['spid-auth.sp_spid_level' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testMissingContactPerson()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('SPID contact persons not set');
        config(['spid-auth.sp_contact_persons' => null]);
        $this->getSPIDAuthConfig();
    }

    public function testPrivatePublicInconsistency()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('SPID Public and Private are mutually exclusive');
        config(['spid-auth.sp_contact_persons' => [
            'other' => [
                'Public' => true,
            ],
            'billing' => [
                'Private' => true,
            ],
        ]]);
        $this->getSPIDAuthConfig();
    }

    public function testPrivateRequiresVATNumber()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('SPID Private requires VATNumber');
        config(['spid-auth.sp_contact_persons' => [
            'other' => [
                'Private' => true,
            ],
            'billing' => [
                'Private' => true,
            ],
        ]]);
        $this->getSPIDAuthConfig();
    }

    public function testPublicRequiresIPACode()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('SPID Public requires IPACode');
        config(['spid-auth.sp_contact_persons' => [
            'other' => [
                'Public' => true,
            ],
        ]]);
        $this->getSPIDAuthConfig();
    }

    public function testContactRequiresEmailAddress()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('SPID missing email address for this contacts: other');
        config(['spid-auth.sp_contact_persons' => [
            'other' => [
                'Public' => true,
                'IPACode' => '12345',
            ],
        ]]);
        $this->getSPIDAuthConfig();
    }

    public function testInvalidSpidLevel()
    {
        $this->withoutExceptionHandling();
        $this->expectException(SPIDConfigurationException::class);
        $this->expectExceptionMessage('SPID authentication level name wrong or not set');
        config(['spid-auth.sp_spid_level' => 'https://www.spid.gov.it/SpidL4']);
        $this->getSPIDAuthConfig();
    }

    public function testProxyConfigDefaults()
    {
        $this->assertFalse(config('spid-auth.proxy.vars'));
        $this->assertNull(config('spid-auth.proxy.base_url'));
        $this->assertNull(config('spid-auth.proxy.protocol'));
        $this->assertNull(config('spid-auth.proxy.host'));
        $this->assertNull(config('spid-auth.proxy.port'));
        $this->assertNull(config('spid-auth.proxy.base_url_path'));
    }

    public function testProxyVarsEnabledResolvesHttpsSelfUrl()
    {
        config(['spid-auth.proxy.vars' => true]);
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';

        $this->getSPIDAuthConfig();

        $this->assertTrue(\OneLogin\Saml2\Utils::getProxyVars());
        $this->assertSame('https', \OneLogin\Saml2\Utils::getSelfProtocol());
        $this->assertSame('example.org', \OneLogin\Saml2\Utils::getSelfHost());
        $this->assertSame('https://example.org', \OneLogin\Saml2\Utils::getSelfURLhost());
    }

    public function testProxyVarsDisabledIgnoresForwardedHeaders()
    {
        config(['spid-auth.proxy.vars' => false]);
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example.org';

        $this->getSPIDAuthConfig();

        $this->assertFalse(\OneLogin\Saml2\Utils::getProxyVars());
        $this->assertNotSame('example.org', \OneLogin\Saml2\Utils::getSelfHost());
    }

    public function testProxyBaseUrlSetsSelfUrlComponents()
    {
        config(['spid-auth.proxy.base_url' => 'https://example.org/app']);

        $this->getSPIDAuthConfig();

        $this->assertSame('https', \OneLogin\Saml2\Utils::getSelfProtocol());
        $this->assertSame('example.org', \OneLogin\Saml2\Utils::getSelfHost());
        $this->assertSame('443', (string) \OneLogin\Saml2\Utils::getSelfPort());
        $this->assertSame('/app/', \OneLogin\Saml2\Utils::getBaseURLPath());
    }

    public function testProxyProtocolOverride()
    {
        config(['spid-auth.proxy.protocol' => 'https']);

        $this->getSPIDAuthConfig();

        $this->assertSame('https', \OneLogin\Saml2\Utils::getSelfProtocol());
    }

    public function testProxyHostOverride()
    {
        config(['spid-auth.proxy.host' => 'sp.example.org']);

        $this->getSPIDAuthConfig();

        $this->assertSame('sp.example.org', \OneLogin\Saml2\Utils::getSelfHost());
    }

    public function testProxyPortOverride()
    {
        config(['spid-auth.proxy.port' => '8443']);

        $this->getSPIDAuthConfig();

        $this->assertSame('8443', (string) \OneLogin\Saml2\Utils::getSelfPort());
    }

    public function testProxyBaseUrlPathOverride()
    {
        config(['spid-auth.proxy.base_url_path' => '/gateway']);

        $this->getSPIDAuthConfig();

        $this->assertSame('/gateway/', \OneLogin\Saml2\Utils::getBaseURLPath());
    }

    public function testExplicitOverridesWinOverBaseUrl()
    {
        config([
            'spid-auth.proxy.base_url' => 'https://example.org:443/app',
            'spid-auth.proxy.host' => 'override.example.org',
            'spid-auth.proxy.protocol' => 'http',
            'spid-auth.proxy.port' => '9000',
            'spid-auth.proxy.base_url_path' => '/override',
        ]);

        $this->getSPIDAuthConfig();

        // Explicit setters run after setBaseURL, so they win.
        $this->assertSame('override.example.org', \OneLogin\Saml2\Utils::getSelfHost());
        $this->assertSame('http', \OneLogin\Saml2\Utils::getSelfProtocol());
        $this->assertSame('9000', (string) \OneLogin\Saml2\Utils::getSelfPort());
        $this->assertSame('/override/', \OneLogin\Saml2\Utils::getBaseURLPath());
    }

    public function testBaseUrlWinsOverForwardedDetection()
    {
        config([
            'spid-auth.proxy.vars' => true,
            'spid-auth.proxy.base_url' => 'https://canonical.example.org',
        ]);
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'forwarded.example.org';

        $this->getSPIDAuthConfig();

        // base_url set an explicit $_host, so getRawHost never reaches the
        // X-Forwarded branch.
        $this->assertSame('canonical.example.org', \OneLogin\Saml2\Utils::getSelfHost());
    }

    public function testDefaultProxyConfigDoesNotAlterUtilsState()
    {
        // Default config: vars=false, all others null.
        $config = $this->getSPIDAuthConfig();

        $this->assertFalse(\OneLogin\Saml2\Utils::getProxyVars());
        $this->assertNull(\OneLogin\Saml2\Utils::getBaseURLPath());
        $this->assertArrayNotHasKey('baseurl', $config);
        $this->assertArrayNotHasKey('proxy', $config);
    }

    public function testEmptyStringProxyValuesAreIgnored()
    {
        // Baseline self-URL state with no proxy settings.
        $this->getSPIDAuthConfig();
        $protocol = \OneLogin\Saml2\Utils::getSelfProtocol();
        $host = \OneLogin\Saml2\Utils::getSelfHost();
        $port = \OneLogin\Saml2\Utils::getSelfPort();

        // An empty env var (e.g. SPID_AUTH_PROXY_HOST=) yields '' rather than null.
        config([
            'spid-auth.proxy.base_url' => '',
            'spid-auth.proxy.protocol' => '',
            'spid-auth.proxy.host' => '',
            'spid-auth.proxy.port' => '',
            'spid-auth.proxy.base_url_path' => '',
        ]);

        $this->getSPIDAuthConfig();

        $this->assertSame($protocol, \OneLogin\Saml2\Utils::getSelfProtocol());
        $this->assertSame($host, \OneLogin\Saml2\Utils::getSelfHost());
        $this->assertSame($port, \OneLogin\Saml2\Utils::getSelfPort());
        $this->assertNull(\OneLogin\Saml2\Utils::getBaseURLPath());
    }

    public function testProxyVarsIsCastToBool()
    {
        foreach (['1' => true, 'true' => true, '0' => false, '' => false] as $value => $expected) {
            config(['spid-auth.proxy.vars' => (string) $value]);

            $this->getSPIDAuthConfig();

            $this->assertSame($expected, \OneLogin\Saml2\Utils::getProxyVars(), "proxy.vars = '{$value}'");
        }
    }

    public function testForwardedNonStandardPortIsKeptInSelfUrlHost()
    {
        config(['spid-auth.proxy.vars' => true]);
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '8443';

        $this->getSPIDAuthConfig();

        $this->assertSame('https://example.org:8443', \OneLogin\Saml2\Utils::getSelfURLhost());
    }

    public function testExplicitOverridesWinOverForwardedDetection()
    {
        config([
            'spid-auth.proxy.vars' => true,
            'spid-auth.proxy.protocol' => 'http',
            'spid-auth.proxy.host' => 'internal.example.org',
            'spid-auth.proxy.port' => '8080',
        ]);
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'forwarded.example.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';

        $this->getSPIDAuthConfig();

        $this->assertSame('http://internal.example.org:8080', \OneLogin\Saml2\Utils::getSelfURLhost());
    }

    public function testSelfUrlMatchesPublicAcsUrlBehindSslOffloadingProxy()
    {
        // php-saml compares the Response Destination against this URL.
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? null;
        $_SERVER['SCRIPT_NAME'] = '/spid/acs';
        config(['spid-auth.proxy.vars' => true]);
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';

        try {
            $this->getSPIDAuthConfig();

            $this->assertSame('https://example.org/spid/acs', \OneLogin\Saml2\Utils::getSelfURLNoQuery());
        } finally {
            $_SERVER['SCRIPT_NAME'] = $scriptName;
        }
    }

    public function testProxySettingsAreAppliedWhenServingMetadata()
    {
        config([
            'spid-auth.proxy.protocol' => 'https',
            'spid-auth.proxy.host' => 'metadata.example.org',
        ]);

        $this->get(route('spid-auth_metadata'))->assertOk();

        $this->assertSame('https://metadata.example.org', \OneLogin\Saml2\Utils::getSelfURLhost());
    }

    public function testResponseDestinationIsAcceptedBehindSslOffloadingProxy()
    {
        config(['spid-auth.proxy.vars' => true]);
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';

        $error = $this->validateResponseDestinedTo('https://example.org/spid/acs');

        // Destination check passed; validation only stops at the (expected)
        // missing signature of this unsigned fixture.
        $this->assertStringNotContainsString('The response was received at', $error);
        $this->assertStringContainsString('is not signed', $error);
    }

    public function testResponseDestinationIsAcceptedWithProxyBaseUrl()
    {
        config(['spid-auth.proxy.base_url' => 'https://example.org']);

        $error = $this->validateResponseDestinedTo('https://example.org/spid/acs');

        $this->assertStringNotContainsString('The response was received at', $error);
        $this->assertStringContainsString('is not signed', $error);
    }

    public function testResponseDestinationIsAcceptedWithProxyBaseUrlUnderSubpath()
    {
        config(['spid-auth.proxy.base_url' => 'https://example.org/app/']);

        $error = $this->validateResponseDestinedTo('https://example.org/app/spid/acs');

        $this->assertStringNotContainsString('The response was received at', $error);
        $this->assertStringContainsString('is not signed', $error);
    }

    public function testResponseDestinationIsAcceptedWithProxyBaseUrlPath()
    {
        config([
            'spid-auth.proxy.protocol' => 'https',
            'spid-auth.proxy.host' => 'example.org',
            'spid-auth.proxy.base_url_path' => '/app',
        ]);

        $error = $this->validateResponseDestinedTo('https://example.org/app/spid/acs');

        $this->assertStringNotContainsString('The response was received at', $error);
        $this->assertStringContainsString('is not signed', $error);
    }

    public function testProxyBaseUrlUsesCustomRoutesPrefix()
    {
        config([
            'spid-auth.routes_prefix' => 'auth/spid',
            'spid-auth.proxy.base_url' => 'https://example.org',
        ]);

        $server = $_SERVER;
        $_SERVER['REQUEST_URI'] = '/auth/spid/acs';

        try {
            $this->getSPIDAuthConfig();

            $this->assertSame('https://example.org/auth/spid/acs', \OneLogin\Saml2\Utils::getSelfRoutedURLNoQuery());
        } finally {
            $_SERVER = $server;
        }
    }

    public function testResponseDestinationIsRejectedBehindProxyWithoutProxyVars()
    {
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';

        $error = $this->validateResponseDestinedTo('https://example.org/spid/acs');

        $this->assertStringContainsString('The response was received at http://', $error);
    }

    protected function getPackageProviders($app)
    {
        return ['Italia\SPIDAuth\ServiceProvider'];
    }

    protected function getSPIDAuthConfig()
    {
        $SPIDAuth = $this->app->make('SPIDAuth');
        $reflectedSPIDAuth = new ReflectionClass(get_class($SPIDAuth));
        $getSAMLConfig = $reflectedSPIDAuth->getMethod('getSAMLConfig');
        $getSAMLConfig->setAccessible(true);

        return $getSAMLConfig->invokeArgs($SPIDAuth, ['test']);
    }

    /**
     * Run php-saml's own Response validation on an unsigned response addressed
     * to $destination, as if it had been POSTed to /spid/acs, and return the
     * validation error.
     */
    protected function validateResponseDestinedTo(string $destination): string
    {
        $server = $_SERVER;
        $_SERVER['SCRIPT_NAME'] = $_SERVER['REQUEST_URI'] = '/spid/acs';
        unset($_SERVER['HTTPS'], $_SERVER['PATH_INFO']);

        try {
            $settings = new \OneLogin\Saml2\Settings($this->getSPIDAuthConfig());
            $now = time();
            $xml = strtr(file_get_contents(__DIR__ . '/responses/valid_level1.xml'), [
                '{{AssertionConsumerURL}}' => $destination,
                '{{ResponseID}}' => '_response',
                '{{AssertionID}}' => '_assertion',
                '{{AuthnRequestID}}' => '_request',
                '{{IssueInstant}}' => \OneLogin\Saml2\Utils::parseTime2SAML($now),
                '{{ResponseIssueInstant}}' => \OneLogin\Saml2\Utils::parseTime2SAML($now),
                '{{AssertionIssueInstant}}' => \OneLogin\Saml2\Utils::parseTime2SAML($now),
                '{{AuthnIstant}}' => \OneLogin\Saml2\Utils::parseTime2SAML($now),
                '{{NotOnOrAfter}}' => \OneLogin\Saml2\Utils::parseTime2SAML($now + 300),
                '{{Audience}}' => config('spid-auth.sp_entity_id'),
                '{{NameID}}' => '_nameid',
                '{{NameIDNameQualifier}}' => 'spid-testenv',
                '{{Attributes}}' => '<saml:Attribute Name="spidCode"><saml:AttributeValue>TEST0123456789</saml:AttributeValue></saml:Attribute>',
            ]);

            $response = new \OneLogin\Saml2\Response($settings, base64_encode($xml));
            $response->isValid('_request');

            return (string) $response->getError();
        } finally {
            $_SERVER = $server;
        }
    }
}
