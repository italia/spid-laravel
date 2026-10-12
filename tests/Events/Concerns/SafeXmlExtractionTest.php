<?php

namespace Italia\SPIDAuth\Tests\Events\Concerns;

use DOMDocument;
use Italia\SPIDAuth\Events\Concerns\SafeXmlExtraction;
use PHPUnit\Framework\TestCase;

class SafeXmlExtractionTestDouble
{
    use SafeXmlExtraction;

    public function callSafeXPathQuery(?DOMDocument $document, string $xpath, ?string $attribute = null): ?string
    {
        return $this->safeXPathQuery($document, $xpath, $attribute);
    }
}

class SafeXmlExtractionTest extends TestCase
{
    private SafeXmlExtractionTestDouble $subject;

    protected function setUp(): void
    {
        $this->subject = new SafeXmlExtractionTestDouble();
    }

    public function testNullDocumentReturnsNull()
    {
        $this->assertNull($this->subject->callSafeXPathQuery(null, '/samlp:AuthnRequest'));
    }

    public function testValidXPathWithAttributeExtraction()
    {
        $document = $this->loadDocument($this->getValidAuthnRequestXml());

        $this->assertSame(
            '_test-request-id-123',
            $this->subject->callSafeXPathQuery($document, '/samlp:AuthnRequest', 'ID')
        );
    }

    public function testValidXPathWithTextContentExtraction()
    {
        $document = $this->loadDocument($this->getValidAuthnRequestXml());

        $this->assertSame(
            'https://sp.example.com',
            $this->subject->callSafeXPathQuery($document, '/samlp:AuthnRequest/saml:Issuer')
        );
    }

    public function testXPathMatchingNothingReturnsNull()
    {
        $document = $this->loadDocument($this->getValidAuthnRequestXml());

        $this->assertNull(
            $this->subject->callSafeXPathQuery($document, '/samlp:AuthnRequest/saml:NonExistentElement')
        );
    }

    public function testMissingAttributeOnMatchedNodeReturnsNull()
    {
        $document = $this->loadDocument($this->getValidAuthnRequestXml());

        $this->assertNull(
            $this->subject->callSafeXPathQuery($document, '/samlp:AuthnRequest', 'NonExistentAttribute')
        );
    }

    public function testEmptyAttributeValueReturnsNull()
    {
        $xml = '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="" Version="2.0"/>';
        $document = $this->loadDocument($xml);

        $this->assertNull(
            $this->subject->callSafeXPathQuery($document, '/samlp:AuthnRequest', 'ID')
        );
    }

    public function testWhitespaceOnlyTextContentReturnsNull()
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
        $document = $this->loadDocument($xml);

        $this->assertNull(
            $this->subject->callSafeXPathQuery($document, '/samlp:AuthnRequest/saml:Issuer')
        );
    }

    public function testInvalidXPathTriggersExceptionAndReturnsNull()
    {
        $document = $this->loadDocument($this->getValidAuthnRequestXml());

        // An intentionally malformed XPath causes DOMXPath::query() to return false,
        // and accessing ->length on false throws a TypeError caught by the catch block.
        $this->assertNull(
            @$this->subject->callSafeXPathQuery($document, '///invalid[[')
        );
    }

    private function loadDocument(string $xml): DOMDocument
    {
        $document = new DOMDocument();
        $document->loadXML($xml);

        return $document;
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
}
