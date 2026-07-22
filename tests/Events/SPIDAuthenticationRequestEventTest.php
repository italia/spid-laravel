<?php

namespace Italia\SPIDAuth\Tests\Events;

use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use PHPUnit\Framework\TestCase;

class SPIDAuthenticationRequestEventTest extends TestCase
{
    public function testGetIdpReturnsIdp()
    {
        $xml = $this->getValidAuthnRequestXml();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertSame('test-idp', $event->getIdp());
    }

    public function testGetAuthnRequestXmlReturnsXml()
    {
        $xml = $this->getValidAuthnRequestXml();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertSame($xml, $event->getAuthnRequestXml());
    }

    public function testGetAuthnRequestIdExtractsId()
    {
        $xml = $this->getValidAuthnRequestXml();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertSame('_test-request-id-123', $event->getAuthnRequestId());
    }

    public function testGetAuthnRequestIssueInstantExtractsIssueInstant()
    {
        $xml = $this->getValidAuthnRequestXml();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertSame('2024-01-15T12:00:00Z', $event->getAuthnRequestIssueInstant());
    }

    public function testGetAuthnRequestIdReturnsNullWhenMissing()
    {
        $xml = $this->getAuthnRequestXmlWithoutId();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertNull($event->getAuthnRequestId());
    }

    public function testGetAuthnRequestIssueInstantReturnsNullWhenMissing()
    {
        $xml = $this->getAuthnRequestXmlWithoutIssueInstant();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertNull($event->getAuthnRequestIssueInstant());
    }

    public function testGetAuthnRequestIssuerExtractsIssuer()
    {
        $xml = $this->getValidAuthnRequestXml();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertSame('https://sp.example.com', $event->getAuthnRequestIssuer());
    }

    public function testGetAuthnRequestIssuerReturnsNullWhenMissing()
    {
        $xml = $this->getAuthnRequestXmlWithoutIssuer();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertNull($event->getAuthnRequestIssuer());
    }

    public function testMalformedXmlReturnsNullForAllExtractions()
    {
        $xml = 'This is not valid XML at all!';
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertNull($event->getAuthnRequestId());
        $this->assertNull($event->getAuthnRequestIssueInstant());
        $this->assertNull($event->getAuthnRequestIssuer());
        $this->assertSame($xml, $event->getAuthnRequestXml());
    }

    public function testEmptyXmlReturnsNullForAllExtractions()
    {
        $xml = '';
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertNull($event->getAuthnRequestId());
        $this->assertNull($event->getAuthnRequestIssueInstant());
        $this->assertNull($event->getAuthnRequestIssuer());
    }

    public function testPartiallyMalformedXmlDoesNotThrow()
    {
        $xml = '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"><unclosed>';
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        // Should not throw, just return nulls
        $this->assertNull($event->getAuthnRequestId());
        $this->assertNull($event->getAuthnRequestIssueInstant());
    }

    public function testEmptyAttributeValuesReturnNull()
    {
        $xml = '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="" IssueInstant=""/>';
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        $this->assertNull($event->getAuthnRequestId());
        $this->assertNull($event->getAuthnRequestIssueInstant());
    }

    public function testTextContentWithOnlyWhitespaceReturnsNull()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-request-id-123"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:00Z">
    <saml:Issuer>   </saml:Issuer>
</samlp:AuthnRequest>
XML;
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        // This tests the textContent trimming in SafeXmlExtraction
        // The Issuer element has only whitespace, so it should return null
        $this->assertNotNull($event->getAuthnRequestId());
        $this->assertNull($event->getAuthnRequestIssuer());
    }

    public function testXmlWithDoctypeIsCaughtAndReturnsNull()
    {
        $xml = '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY x "y">]>'
             . '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="_id"/>';
        $event = new SPIDAuthenticationRequestEvent('test-idp', $xml);

        // loadXML throws on DOCTYPE (XXE guard); getDocument() catches and nulls the document.
        $this->assertNull($event->getAuthnRequestId());
        $this->assertNull($event->getAuthnRequestIssueInstant());
        $this->assertNull($event->getAuthnRequestIssuer());
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

    private function getAuthnRequestXmlWithoutId(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:00Z">
    <saml:Issuer>https://sp.example.com</saml:Issuer>
</samlp:AuthnRequest>
XML;
    }

    private function getAuthnRequestXmlWithoutIssueInstant(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-request-id-123"
    Version="2.0">
    <saml:Issuer>https://sp.example.com</saml:Issuer>
</samlp:AuthnRequest>
XML;
    }

    private function getAuthnRequestXmlWithoutIssuer(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-request-id-123"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:00Z">
</samlp:AuthnRequest>
XML;
    }
}
