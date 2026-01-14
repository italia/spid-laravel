<?php
/**
 * This class implements a Laravel Event for SPIDAuth Package.
 * Fired when a SAML Response is received during SPID authentication flow.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Events;

use DOMDocument;
use Exception;
use OneLogin\Saml2\Utils as SAMLUtils;

class SPIDAuthenticationResponseEvent
{
    /** The Identity Provider identifier. */
    protected string $idp;

    /** The raw Response XML. */
    protected string $responseXml;

    /** Parsed DOMDocument (null if parsing failed). */
    protected ?DOMDocument $document = null;

    /**
     * Create a new event instance.
     *
     * @param string $idp Identity Provider identifier
     * @param string $responseXml Raw Response XML
     */
    public function __construct(string $idp, string $responseXml)
    {
        $this->idp = $idp;
        $this->responseXml = $responseXml;

        // Check for empty XML before attempting to parse
        if (empty($responseXml)) {
            $this->document = null;
            return;
        }

        try {
            $this->document = new DOMDocument();
            SAMLUtils::loadXML($this->document, $responseXml);
        } catch (Exception $e) {
            // If XML parsing fails, document stays null and all extraction methods return null
            $this->document = null;
        }
    }

    /**
     * Return the Identity Provider identifier.
     *
     * @return string Identity Provider used for this response
     */
    public function getIdp(): string
    {
        return $this->idp;
    }

    /**
     * Return the raw Response XML.
     *
     * @return string Raw Response XML
     */
    public function getResponseXml(): string
    {
        return $this->responseXml;
    }

    /**
     * Extract and return the Response ID.
     *
     * @return string|null Response ID or null if not found
     */
    public function getResponseId(): ?string
    {
        return $this->safeXPathQuery('//samlp:Response', 'ID');
    }

    /**
     * Extract and return the Response IssueInstant.
     *
     * @return string|null Response IssueInstant or null if not found
     */
    public function getResponseIssueInstant(): ?string
    {
        return $this->safeXPathQuery('//samlp:Response', 'IssueInstant');
    }

    /**
     * Extract and return the Response Issuer.
     *
     * @return string|null Response Issuer or null if not found
     */
    public function getResponseIssuer(): ?string
    {
        return $this->safeXPathQuery('//samlp:Response/saml:Issuer');
    }

    /**
     * Extract and return the Response InResponseTo (for correlation with request).
     *
     * @return string|null Response InResponseTo or null if not found
     */
    public function getResponseInResponseTo(): ?string
    {
        return $this->safeXPathQuery('//samlp:Response', 'InResponseTo');
    }

    /**
     * Extract and return the Assertion ID.
     *
     * @return string|null Assertion ID or null if not found
     */
    public function getAssertionId(): ?string
    {
        return $this->safeXPathQuery('//samlp:Response/saml:Assertion', 'ID');
    }

    /**
     * Extract and return the Assertion Subject (NameID).
     *
     * @return string|null Assertion Subject or null if not found
     */
    public function getAssertionSubject(): ?string
    {
        return $this->safeXPathQuery('//samlp:Response/saml:Assertion/saml:Subject/saml:NameID');
    }

    /**
     * Extract and return the Assertion Subject NameQualifier.
     *
     * @return string|null Assertion Subject NameQualifier or null if not found
     */
    public function getAssertionSubjectNameQualifier(): ?string
    {
        return $this->safeXPathQuery('//samlp:Response/saml:Assertion/saml:Subject/saml:NameID', 'NameQualifier');
    }

    /**
     * Safely query XPath and return attribute or text content.
     * Never throws exceptions - returns null if anything fails.
     *
     * @param string $xpath XPath query
     * @param string|null $attribute Attribute name to extract (null for text content)
     * @return string|null Extracted value or null if not found/failed
     */
    private function safeXPathQuery(string $xpath, ?string $attribute = null): ?string
    {
        if ($this->document === null) {
            return null;
        }

        try {
            $nodes = SAMLUtils::query($this->document, $xpath);
            if ($nodes->length === 0) {
                return null;
            }

            $node = $nodes->item(0);
            if ($node === null) {
                return null;
            }

            if ($attribute !== null) {
                if (!$node->hasAttribute($attribute)) {
                    return null;
                }
                $value = $node->getAttribute($attribute);
                return $value !== '' ? $value : null;
            }

            $textContent = trim($node->textContent);
            return $textContent !== '' ? $textContent : null;
        } catch (Exception $e) {
            return null;
        }
    }
}
