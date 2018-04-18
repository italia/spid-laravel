<?php
/**
 * This class implements a SPIDLoginException for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

use Exception;

class SPIDLoginException extends Exception
{
    const SAML_VALIDATION_ERROR = 0;
    const SAML_RESPONSE_ALREADY_PROCESSED = 1;
    const SAML_AUTHENTICATION_ERROR = 2;

    /**
     * SPIDLoginException constructor.
     * @param $message
     * @param int $code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, $code, null);
    }
}
