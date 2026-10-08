<?php

namespace Italia\SPIDAuth\Tests;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Config;
use OneLogin\Saml2\Utils as SAMLUtils;

/**
 * Checks the AuthnRequest produced by the real (non-mocked) login flow against
 * the SPID strict rules enforced by spid-sp-test, the tool behind SPID
 * Validator and SPID Demo.
 */
class SPIDAuthnRequestTest extends SPIDAuthBaseTestCase
{
    public function testAuthnRequestIsSPIDCompliant()
    {
        $request = $this->decodeAuthnRequest($this->doLoginLocation());
        $metadata = $this->fetchMetadata();
        $root = $request->documentElement;
        $requestXpath = $this->xpath($request);
        $metadataXpath = $this->xpath($metadata);
        $entityId = $metadata->documentElement->getAttribute('entityID');

        $this->assertSame('urn:oasis:names:tc:SAML:2.0:protocol', $root->namespaceURI);
        $this->assertSame('AuthnRequest', $root->localName);
        $this->assertNotEmpty($root->getAttribute('ID'));
        $this->assertSame('2.0', $root->getAttribute('Version'));
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $root->getAttribute('IssueInstant'));
        $this->assertSame(config('spid-auth.test_idp.sso_endpoint'), $root->getAttribute('Destination'));
        $this->assertFalse($root->hasAttribute('IsPassive'));

        // AssertionConsumerServiceURL + HTTP-POST binding, matching the metadata ACS.
        $this->assertSame(
            $metadataXpath->query('//md:SPSSODescriptor/md:AssertionConsumerService/@Location')->item(0)->value,
            $root->getAttribute('AssertionConsumerServiceURL')
        );
        $this->assertSame('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST', $root->getAttribute('ProtocolBinding'));

        $this->assertTrue($root->hasAttribute('AttributeConsumingServiceIndex'));
        $this->assertGreaterThanOrEqual(0, (int) $root->getAttribute('AttributeConsumingServiceIndex'));
        $this->assertSame(
            $metadataXpath->query('//md:SPSSODescriptor/md:AttributeConsumingService/@index')->item(0)->value,
            $root->getAttribute('AttributeConsumingServiceIndex')
        );

        $issuers = $requestXpath->query('/samlp:AuthnRequest/saml:Issuer');
        $this->assertSame(1, $issuers->length);
        $this->assertSame($entityId, trim($issuers->item(0)->textContent));
        $this->assertSame('urn:oasis:names:tc:SAML:2.0:nameid-format:entity', $issuers->item(0)->getAttribute('Format'));
        $this->assertSame($entityId, $issuers->item(0)->getAttribute('NameQualifier'));

        $nameIdPolicies = $requestXpath->query('/samlp:AuthnRequest/samlp:NameIDPolicy');
        $this->assertSame(1, $nameIdPolicies->length);
        $this->assertSame('urn:oasis:names:tc:SAML:2.0:nameid-format:transient', $nameIdPolicies->item(0)->getAttribute('Format'));
        $this->assertFalse($nameIdPolicies->item(0)->hasAttribute('AllowCreate'));

        $contexts = $requestXpath->query('/samlp:AuthnRequest/samlp:RequestedAuthnContext');
        $this->assertSame(1, $contexts->length);
        $this->assertSame(config('spid-saml.security.requestedAuthnContextComparison'), $contexts->item(0)->getAttribute('Comparison'));
        $this->assertContains($contexts->item(0)->getAttribute('Comparison'), ['exact', 'minimum', 'better', 'maximum']);
        $classRefs = $requestXpath->query('/samlp:AuthnRequest/samlp:RequestedAuthnContext/saml:AuthnContextClassRef');
        $this->assertSame(1, $classRefs->length);
        $this->assertContains(trim($classRefs->item(0)->textContent), [
            'https://www.spid.gov.it/SpidL1',
            'https://www.spid.gov.it/SpidL2',
            'https://www.spid.gov.it/SpidL3',
        ]);
    }

    public function testAuthnRequestIndexesFollowConfig()
    {
        Config::set('spid-auth.sp_acs_index', 2);
        Config::set('spid-auth.sp_attributes_index', 2);

        $root = $this->decodeAuthnRequest($this->doLoginLocation())->documentElement;
        $metadataXpath = $this->xpath($this->fetchMetadata());

        $this->assertSame('2', $root->getAttribute('AttributeConsumingServiceIndex'));
        $this->assertSame('2', $metadataXpath->query('//md:SPSSODescriptor/md:AttributeConsumingService/@index')->item(0)->value);
        $this->assertSame('2', $metadataXpath->query('//md:SPSSODescriptor/md:AssertionConsumerService/@index')->item(0)->value);
    }

    public function testAuthnRequestRedirectSignatureIsValid()
    {
        $params = $this->redirectParameters($this->doLoginLocation());
        $publicKey = openssl_pkey_get_public(SAMLUtils::formatCert(config('spid-auth.sp_certificate')));

        $this->assertSame('http://www.w3.org/2001/04/xmldsig-more#rsa-sha256', $params['SigAlg']);
        $this->assertSame(1, openssl_verify(
            $this->signedQuery($params['SAMLRequest'], $params['RelayState'], $params['SigAlg']),
            base64_decode($params['Signature']),
            $publicKey,
            OPENSSL_ALGO_SHA256
        ));

        $tamperedRelayState = ('a' === $params['RelayState'][0] ? 'b' : 'a') . substr($params['RelayState'], 1);
        $this->assertSame(0, openssl_verify(
            $this->signedQuery($params['SAMLRequest'], $tamperedRelayState, $params['SigAlg']),
            base64_decode($params['Signature']),
            $publicKey,
            OPENSSL_ALGO_SHA256
        ));
    }

    public function testAuthnRequestForceAuthnWithSpidL1()
    {
        $this->assertForceAuthnWithLevel('https://www.spid.gov.it/SpidL1');
    }

    public function testAuthnRequestForceAuthnWithSpidL2()
    {
        $this->assertForceAuthnWithLevel('https://www.spid.gov.it/SpidL2');
    }

    public function testAuthnRequestForceAuthnWithSpidL3()
    {
        $this->assertForceAuthnWithLevel('https://www.spid.gov.it/SpidL3');
    }

    public function testAuthnRequestRelayStateIsOpaque()
    {
        $response = $this->withSession(['url.intended' => 'intendedURL'])->post($this->doLoginURL, ['provider' => 'test']);
        $relayState = $this->redirectParameters($response->headers->get('Location'))['RelayState'];

        $this->assertSame(32, strlen($relayState));
        $this->assertStringNotContainsStringIgnoringCase('http', $relayState);
        $this->assertSame(['url.intended' => 'intendedURL'], cache($relayState));
    }

    public function testAuthnRequestValidatesAgainstProtocolSchema()
    {
        $request = $this->decodeAuthnRequest($this->doLoginLocation());

        libxml_use_internal_errors(true);
        $valid = $request->schemaValidate(__DIR__ . '/xml-schemas/saml-schema-protocol-2.0.xsd');
        $errors = array_map(function ($error) {
            return trim($error->message) . ' (line ' . $error->line . ')';
        }, libxml_get_errors());
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        $this->assertTrue($valid, implode("\n", $errors));
    }

    /**
     * The route caches the controller (and the controller caches its SAML
     * settings), so each SPID level needs a fresh application: one test each.
     */
    private function assertForceAuthnWithLevel(string $level): void
    {
        Config::set('spid-auth.sp_spid_level', $level);

        $request = $this->decodeAuthnRequest($this->doLoginLocation());

        $this->assertSame('true', $request->documentElement->getAttribute('ForceAuthn'));
        $this->assertSame(
            $level,
            trim($this->xpath($request)->query('/samlp:AuthnRequest/samlp:RequestedAuthnContext/saml:AuthnContextClassRef')->item(0)->textContent)
        );
    }

    private function doLoginLocation(): string
    {
        $response = $this->post($this->doLoginURL, ['provider' => 'test']);
        $response->assertStatus(302);

        return $response->headers->get('Location');
    }

    private function redirectParameters(string $location): array
    {
        parse_str(parse_url($location, PHP_URL_QUERY), $params);

        return $params;
    }

    private function signedQuery(string $samlRequest, string $relayState, string $sigAlg): string
    {
        return 'SAMLRequest=' . urlencode($samlRequest) . '&RelayState=' . urlencode($relayState) . '&SigAlg=' . urlencode($sigAlg);
    }

    private function decodeAuthnRequest(string $location): DOMDocument
    {
        $document = new DOMDocument();
        SAMLUtils::loadXML($document, gzinflate(base64_decode($this->redirectParameters($location)['SAMLRequest'])));

        return $document;
    }

    private function fetchMetadata(): DOMDocument
    {
        $response = $this->get($this->metadataURL);
        $response->assertStatus(200);

        $document = new DOMDocument();
        $document->loadXML($response->getContent());

        return $document;
    }

    private function xpath(DOMDocument $document): DOMXPath
    {
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
        $xpath->registerNamespace('md', 'urn:oasis:names:tc:SAML:2.0:metadata');

        return $xpath;
    }
}
