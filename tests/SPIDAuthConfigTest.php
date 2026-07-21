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
}
