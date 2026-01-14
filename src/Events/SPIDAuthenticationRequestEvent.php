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
use Italia\SPIDAuth\Events\Concerns\SafeXmlExtraction;
use OneLogin\Saml2\Utils as SAMLUtils;

class SPIDAuthenticationRequestEvent
{
    use SafeXmlExtraction;
    /** The Identity Provider identifier. */
    protected string $idp;

    /** The raw AuthnRequest XML. */
    protected string $authnRequestXml;

    /** Parsed DOMDocument (null if parsing failed or not yet parsed). */
    protected ?DOMDocument $document = null;

    /** Whether the document has been parsed. */
    protected bool $documentParsed = false;

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
        // Lazy loading: parse only when needed
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
        return $this->safeXPathQuery($this->getDocument(), '//samlp:AuthnRequest', 'ID');
    }

    /**
     * Extract and return the AuthnRequest IssueInstant.
     *
     * @return string|null AuthnRequest IssueInstant or null if not found
     */
    public function getAuthnRequestIssueInstant(): ?string
    {
        return $this->safeXPathQuery($this->getDocument(), '//samlp:AuthnRequest', 'IssueInstant');
    }

    /**
     * Get the parsed DOMDocument, parsing it if necessary (lazy loading).
     *
     * @return DOMDocument|null
     */
    protected function getDocument(): ?DOMDocument
    {
        if ($this->documentParsed) {
            return $this->document;
        }

        $this->documentParsed = true;

        // Check for empty XML before attempting to parse
        if (empty($this->authnRequestXml)) {
            $this->document = null;

            return null;
        }

        try {
            $this->document = new DOMDocument();
            // Suppress warnings from OneLogin library when parsing malformed XML
            $oldErrorReporting = error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
            SAMLUtils::loadXML($this->document, $this->authnRequestXml);
            error_reporting($oldErrorReporting);
        } catch (Exception $e) {
            // Restore error reporting if exception occurs
            if (isset($oldErrorReporting)) {
                error_reporting($oldErrorReporting);
            }
            // If XML parsing fails, document stays null and all extraction methods return null
            $this->document = null;
        }

        return $this->document;
    }
}
