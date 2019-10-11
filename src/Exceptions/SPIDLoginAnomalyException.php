<?php
/**
 * This class implements a SPIDLoginAnomalyException for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

use Exception;

class SPIDLoginAnomalyException extends SPIDLoginException
{
    /**
     * Map error codes to error messages.
     *
     * @var array the mapping
     */
    public const ERROR_CODES = [
        19 => 'ErrorCode nr19',
        20 => 'ErrorCode nr20',
        21 => 'ErrorCode nr21',
        22 => 'ErrorCode nr22',
        23 => 'ErrorCode nr23',
        25 => 'ErrorCode nr25',
    ];

    /**
     * The error code of this anomaly exception.
     *
     * @var int the error code as defined in the SPID technical rules
     */
    protected $errorCode;

    /**
     * Create a new exception instance.
     *
     * @param int $errorCode the anomaly error code as defined in the SPID technical rules
     */
    public function __construct(int $errorCode)
    {
        parent::__construct('SPID user-generated anomaly', $errorCode);
        $this->errorCode = $errorCode;
    }

    /**
     * Get the error code for this anomaly.
     *
     * @return int the error code
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Get a user-friendly error message for this anomaly.
     */
    public function getUserMessage(): string
    {
        return __('spid-auth::messages.anomalies.' . $this->errorCode);
    }
}
