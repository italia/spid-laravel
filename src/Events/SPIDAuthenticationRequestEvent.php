<?php
/**
 * This class implements a Laravel Event for SPIDAuth Package.
 * Fired when an AuthnRequest is generated during SPID authentication flow.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Events;

use DOMDocument;
use Exception;
use OneLogin\Saml2\Utils as SAMLUtils;

class SPIDAuthenticationRequestEvent
{
    /** The Identity Provider identifier. */
    protected string $idp;

    /** The raw AuthnRequest XML. */
    protected string $authnRequestXml;

    /** Parsed DOMDocument (null if parsing failed). */
    protected ?DOMDocument $document = null;

    /**
     * Create a new event instance.
     *
     * @param string $idp Identity Provider identifier
     * @param string $authnRequestXml Raw AuthnRequest XML
     */
    public function __construct(string $idp, string $authnRequestXml)
    {
        $this->idp = $idp;
        $this->authnRequestXml = $authnRequestXml;

        // Check for empty XML before attempting to parse
        if (empty($authnRequestXml)) {
            $this->document = null;
            return;
        }

        try {
            $this->document = new DOMDocument();
            SAMLUtils::loadXML($this->document, $authnRequestXml);
        } catch (Exception $e) {
            // If XML parsing fails, document stays null and all extraction methods return null
            $this->document = null;
        }
    }

    /**
     * Return the Identity Provider identifier.
     *
     * @return string Identity Provider used for this request
     */
    public function getIdp(): string
    {
        return $this->idp;
    }

    /**
     * Return the raw AuthnRequest XML.
     *
     * @return string Raw AuthnRequest XML
     */
    public function getAuthnRequestXml(): string
    {
        return $this->authnRequestXml;
    }

    /**
     * Extract and return the AuthnRequest ID.
     *
     * @return string|null AuthnRequest ID or null if not found
     */
    public function getAuthnRequestId(): ?string
    {
        return $this->safeXPathQuery('//samlp:AuthnRequest', 'ID');
    }

    /**
     * Extract and return the AuthnRequest IssueInstant.
     *
     * @return string|null AuthnRequest IssueInstant or null if not found
     */
    public function getAuthnRequestIssueInstant(): ?string
    {
        return $this->safeXPathQuery('//samlp:AuthnRequest', 'IssueInstant');
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
