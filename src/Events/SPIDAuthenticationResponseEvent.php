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
use Italia\SPIDAuth\Events\Concerns\SafeXmlExtraction;
use OneLogin\Saml2\Utils as SAMLUtils;

class SPIDAuthenticationResponseEvent
{
    use SafeXmlExtraction;
    /** The Identity Provider identifier. */
    protected string $idp;

    /** The raw Response XML. */
    protected string $responseXml;

    /** Parsed DOMDocument (null if parsing failed or not yet parsed). */
    protected ?DOMDocument $document = null;

    /** Whether the document has been parsed. */
    protected bool $documentParsed = false;

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
        // Lazy loading: parse only when needed
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
        if (empty($this->responseXml)) {
            $this->document = null;
            return null;
        }

        try {
            $this->document = new DOMDocument();
            // Suppress warnings from OneLogin library when parsing malformed XML
            $oldErrorReporting = error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
            SAMLUtils::loadXML($this->document, $this->responseXml);
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
        return $this->safeXPathQuery($this->getDocument(), '//samlp:Response', 'ID');
    }

    /**
     * Extract and return the Response IssueInstant.
     *
     * @return string|null Response IssueInstant or null if not found
     */
    public function getResponseIssueInstant(): ?string
    {
        return $this->safeXPathQuery($this->getDocument(), '//samlp:Response', 'IssueInstant');
    }

    /**
     * Extract and return the Response Issuer.
     *
     * @return string|null Response Issuer or null if not found
     */
    public function getResponseIssuer(): ?string
    {
        return $this->safeXPathQuery($this->getDocument(), '//samlp:Response/saml:Issuer');
    }

    /**
     * Extract and return the Response InResponseTo (for correlation with request).
     *
     * @return string|null Response InResponseTo or null if not found
     */
    public function getResponseInResponseTo(): ?string
    {
        return $this->safeXPathQuery($this->getDocument(), '//samlp:Response', 'InResponseTo');
    }

    /**
     * Extract and return the Assertion ID.
     *
     * @return string|null Assertion ID or null if not found
     */
    public function getAssertionId(): ?string
    {
        return $this->safeXPathQuery($this->getDocument(), '//samlp:Response/saml:Assertion', 'ID');
    }

    /**
     * Extract and return the Assertion Subject (NameID).
     *
     * @return string|null Assertion Subject or null if not found
     */
    public function getAssertionSubject(): ?string
    {
        return $this->safeXPathQuery($this->getDocument(), '//samlp:Response/saml:Assertion/saml:Subject/saml:NameID');
    }

    /**
     * Extract and return the Assertion Subject NameQualifier.
     *
     * @return string|null Assertion Subject NameQualifier or null if not found
     */
    public function getAssertionSubjectNameQualifier(): ?string
    {
        return $this->safeXPathQuery($this->getDocument(), '//samlp:Response/saml:Assertion/saml:Subject/saml:NameID', 'NameQualifier');
    }
}
