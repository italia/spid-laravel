<?php

namespace Italia\SPIDAuth\Tests\Events;

use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use PHPUnit\Framework\TestCase;

class SPIDAuthenticationResponseEventTest extends TestCase
{
    public function testGetIdpReturnsIdp()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('test-idp', $event->getIdp());
    }

    public function testGetResponseXmlReturnsXml()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame($xml, $event->getResponseXml());
    }

    public function testGetResponseIdExtractsId()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('_test-response-id-456', $event->getResponseId());
    }

    public function testGetResponseIssueInstantExtractsIssueInstant()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('2024-01-15T12:00:05Z', $event->getResponseIssueInstant());
    }

    public function testGetResponseIssuerExtractsIssuer()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('https://idp.example.com', $event->getResponseIssuer());
    }

    public function testGetResponseInResponseToExtractsInResponseTo()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('_test-request-id-123', $event->getResponseInResponseTo());
    }

    public function testGetAssertionIdExtractsAssertionId()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('_test-assertion-id-789', $event->getAssertionId());
    }

    public function testGetAssertionSubjectExtractsSubject()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('user@example.com', $event->getAssertionSubject());
    }

    public function testGetAssertionSubjectNameQualifierExtractsNameQualifier()
    {
        $xml = $this->getValidResponseXml();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertSame('https://idp.example.com', $event->getAssertionSubjectNameQualifier());
    }

    public function testMissingIssuerReturnsNull()
    {
        $xml = $this->getResponseXmlWithoutIssuer();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertNull($event->getResponseIssuer());
    }

    public function testMissingAssertionReturnsNullForAssertionFields()
    {
        $xml = $this->getResponseXmlWithoutAssertion();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertNull($event->getAssertionId());
        $this->assertNull($event->getAssertionSubject());
        $this->assertNull($event->getAssertionSubjectNameQualifier());
    }

    public function testMissingSubjectReturnsNull()
    {
        $xml = $this->getResponseXmlWithoutSubject();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertNull($event->getAssertionSubject());
        $this->assertNull($event->getAssertionSubjectNameQualifier());
    }

    public function testMissingNameQualifierReturnsNull()
    {
        $xml = $this->getResponseXmlWithoutNameQualifier();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertNull($event->getAssertionSubjectNameQualifier());
    }

    public function testMalformedXmlReturnsNullForAllExtractions()
    {
        $xml = 'Not valid XML at all!';
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertNull($event->getResponseId());
        $this->assertNull($event->getResponseIssueInstant());
        $this->assertNull($event->getResponseIssuer());
        $this->assertNull($event->getResponseInResponseTo());
        $this->assertNull($event->getAssertionId());
        $this->assertNull($event->getAssertionSubject());
        $this->assertNull($event->getAssertionSubjectNameQualifier());
        $this->assertSame($xml, $event->getResponseXml());
    }

    public function testEmptyXmlReturnsNullForAllExtractions()
    {
        $xml = '';
        $event = new SPIDAuthenticationResponseEvent('test-idp', $xml);

        $this->assertNull($event->getResponseId());
        $this->assertNull($event->getResponseIssueInstant());
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

    private function getResponseXmlWithoutIssuer(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-456"
    InResponseTo="_test-request-id-123"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z">
    <samlp:Status>
        <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
    </samlp:Status>
</samlp:Response>
XML;
    }

    private function getResponseXmlWithoutAssertion(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-456"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
</samlp:Response>
XML;
    }

    private function getResponseXmlWithoutSubject(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-456"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
    <saml:Assertion ID="_test-assertion-id-789" IssueInstant="2024-01-15T12:00:05Z" Version="2.0">
        <saml:Issuer>https://idp.example.com</saml:Issuer>
    </saml:Assertion>
</samlp:Response>
XML;
    }

    private function getResponseXmlWithoutNameQualifier(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-456"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
    <saml:Assertion ID="_test-assertion-id-789" IssueInstant="2024-01-15T12:00:05Z" Version="2.0">
        <saml:Issuer>https://idp.example.com</saml:Issuer>
        <saml:Subject>
            <saml:NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient">user@example.com</saml:NameID>
        </saml:Subject>
    </saml:Assertion>
</samlp:Response>
XML;
    }
}
