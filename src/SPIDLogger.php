<?php
/**
 * This class implements a SPIDUser for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth;

class SPIDLogger
{
    /**
     * Action type. Can be either LOGIN or LOGOUT.
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $requestId;

    /**
     * @var string
     */
    protected $requestIssueInstant;

    /**
     * @var string
     */
    protected $requestXML;

    /**
     * @var string
     */
    protected $responseId;

    /**
     * @var string
     */
    protected $responseIssueInstant;

    /**
     * @var string
     */
    protected $responseXML;

    /**
     * @var string
     */
    protected $assertionId;

    /**
     * @var string
     */
    protected $assertionNameId;

    /**
     * @var string
     */
    protected $assertionNameIdNameQualifier;

    /**
     * @var string
     */
    protected $error;


    /**
     * SPIDLogger constructor.
     *
     * @param string $action
     * @param string $requestId
     */
    public function __construct(string $action, string $requestId)
    {
        $this->action = $action;
        $this->requestId = $requestId;
    }

    /**
     * Set request data.
     *
     * @param string $requestIssueInstant
     * @param string $requestXML
     */
    public function setRequestData(string $requestIssueInstant, string $requestXML)
    {
        $this->requestIssueInstant = $requestIssueInstant;
        $this->requestXML = $requestXML;
    }

    /**
     * Set response data.
     *
     * @param string|null $responseId
     * @param string|null $responseXML
     * @param string|null $responseIssueInstant
     * @param string|null $assertionId
     * @param string|null $assertionNameId
     * @param string|null $assertionNameIdNameQualifier
     */
    public function setResponseData(
        ?string $responseId,
        ?string $responseXML,
        ?string $responseIssueInstant,
        ?string $assertionId = null,
        ?string $assertionNameId = null,
        ?string $assertionNameIdNameQualifier = null) {

        $this->responseId = $responseId;
        $this->responseXML = $responseXML;
        $this->responseIssueInstant = $responseIssueInstant;
        $this->assertionId = $assertionId;
        $this->assertionNameId = $assertionNameId;
        $this->assertionNameIdNameQualifier = $assertionNameIdNameQualifier;
    }

    /**
     * Set error data.
     *
     * @param string $error
     */
    public function setErrorData(string $error)
    {
        $this->error = $error;
    }


    /**
     * Get action parameter.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get requestId parameter.
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Get requestIssueInstant parameter.
     *
     * @return string
     */
    public function getRequestIssueInstant()
    {
        return $this->requestIssueInstant;
    }

    /**
     * Get requestXML parameter.
     *
     * @return string
     */
    public function getRequestXML()
    {
        return $this->requestXML;
    }

    /**
     * Get responseId parameter.
     *
     * @return string
     */
    public function getResponseId()
    {
        return $this->responseId;
    }

    /**
     * Get responseXML parameter.
     *
     * @return string
     */
    public function getResponseXML()
    {
        return $this->responseXML;
    }

    /**
     * Get responseIssueInstant parameter.
     *
     * @return string
     */
    public function getResponseIssueInstant()
    {
        return $this->responseIssueInstant;
    }

    /**
     * Get assertionId parameter.
     *
     * @return string
     */
    public function getAssertionId()
    {
        return $this->assertionId;
    }

    /**
     * Get assertionNameId parameter.
     *
     * @return string
     */
    public function getAssertionNameId()
    {
        return $this->assertionNameId;
    }

    /**
     * Get assertionNameIdNameQualifier parameter.
     *
     * @return string
     */
    public function getAssertionNameIdNameQualifier()
    {
        return $this->assertionNameIdNameQualifier;
    }

    /**
     * Get error parameter.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
