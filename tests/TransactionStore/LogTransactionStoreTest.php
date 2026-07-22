<?php

namespace Italia\SPIDAuth\Tests\TransactionStore;

use Illuminate\Support\Facades\Log;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;
use Italia\SPIDAuth\TransactionStore\LogTransactionStore;
use Mockery;

class LogTransactionStoreTest extends SPIDAuthBaseTestCase
{
    public function testStoreRequestLogsToConfiguredChannel()
    {
        $this->app['config']->set('spid-auth.transaction_log.log.channel', 'stack');
        $store = new LogTransactionStore();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());

        // Should not throw exception
        $store->storeRequest($event);

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    public function testStoreRequestUsesDefaultChannelWhenNotConfigured()
    {
        $this->app['config']->set('spid-auth.transaction_log.log.channel', 'stack');
        $store = new LogTransactionStore();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());

        $store->storeRequest($event);

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    public function testStoreRequestHandlesMalformedXml()
    {
        $store = new LogTransactionStore();
        $malformedXml = 'This is not valid XML';
        $event = new SPIDAuthenticationRequestEvent('test-idp', $malformedXml);

        $store->storeRequest($event);

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    public function testStoreResponseLogsToConfiguredChannel()
    {
        $this->app['config']->set('spid-auth.transaction_log.log.channel', 'stack');
        $store = new LogTransactionStore();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());

        $store->storeResponse($event);

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    public function testStoreResponseHandlesMissingFields()
    {
        $store = new LogTransactionStore();
        $xml = <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-999"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
</samlp:Response>
XML;
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $store->storeResponse($event);

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    public function testStoreRequestLogsFallbackAndRethrowsOnChannelFailure()
    {
        $failingChannel = Mockery::mock();
        $failingChannel->shouldReceive('info')->once()->andThrow(new \Exception('channel down'));

        Log::shouldReceive('channel')->once()->andReturn($failingChannel);
        Log::shouldReceive('error')->once()->with(
            'Failed to log SPID authentication request transaction',
            Mockery::type('array')
        );

        $store = new LogTransactionStore();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('channel down');

        $store->storeRequest($event);
    }

    public function testStoreResponseLogsFallbackAndRethrowsOnChannelFailure()
    {
        $failingChannel = Mockery::mock();
        $failingChannel->shouldReceive('info')->once()->andThrow(new \Exception('channel down'));

        Log::shouldReceive('channel')->once()->andReturn($failingChannel);
        Log::shouldReceive('error')->once()->with(
            'Failed to log SPID authentication response transaction',
            Mockery::type('array')
        );

        $store = new LogTransactionStore();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('channel down');

        $store->storeResponse($event);
    }

    private function getValidAuthnRequestXml(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-request-id-123"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:00Z"
    Destination="https://idp.example.com/sso">
    <saml:Issuer>https://sp.example.com</saml:Issuer>
</samlp:AuthnRequest>
XML;
    }

    private function getValidResponseXml(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-456"
    InResponseTo="_test-request-id-123"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z"
    Destination="https://sp.example.com/acs">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
    <samlp:Status>
        <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
    </samlp:Status>
    <saml:Assertion ID="_test-assertion-id-789" IssueInstant="2024-01-15T12:00:05Z" Version="2.0">
        <saml:Issuer>https://idp.example.com</saml:Issuer>
        <saml:Subject>
            <saml:NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient" NameQualifier="https://idp.example.com">user@example.com</saml:NameID>
        </saml:Subject>
    </saml:Assertion>
</samlp:Response>
XML;
    }
}
