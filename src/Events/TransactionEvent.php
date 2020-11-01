<?php
/**
 * This class implements a Laravel Event for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Events;

use DOMDocument;
use OneLogin\Saml2\Utils as SAMLUtils;

class TransactionEvent
{
    /**
     * AuthnRequest type.
     *
     * @var string the AuthnRequest type
     */
    public const AUTHNREQUEST = 'AuthnRequest';

    /**
     * Response type.
     *
     * @var string the Response type
     */
    public const RESPONSE = 'Response';

    /**
     * The transaction type in the current event.
     *
     * @var string the type
     */
    protected $transactionType;

    /**
     * The actual SAML message in the current event.
     *
     * @var string the SAML message
     */
    protected $SAMLMessage;

    /**
     * The actual SAML message document in the current event.
     *
     * @var DOMDocument the SAML message document
     */
    protected $SAMLMessageDocument;

    /**
     * Create a new event instance.
     *
     * @param string $transactionType current transaction type
     * @param string $SAMLMessage current SAML message
     */
    public function __construct(string $transactionType, string $SAMLMessage)
    {
        $this->transactionType = $transactionType;
        $this->SAMLMessage = $SAMLMessage;
        $this->SAMLMessageDocument = new DOMDocument();
        $this->SAMLMessageDocument->loadXML($SAMLMessage);
    }

    /**
     * Return transactionType.
     *
     * @return string the transacion type
     */
    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    /**
     * Return SAMLMessage.
     *
     * @return string the actual SAML message
     */
    public function getSAMLMessage(): string
    {
        return $this->SAMLMessage;
    }

    /**
     * Return AuthnRequest Id.
     *
     * @return string the id attribute of the AuthnRequest
     */
    public function getAuthnRequestId(): string
    {
        return SAMLUtils::query($this->SAMLMessageDocument, '/samlp:AuthnRequest')->item(0)->getAttribute('id');
    }
}
