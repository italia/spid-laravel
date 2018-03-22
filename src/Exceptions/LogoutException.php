<?php
/**
 * This class implements a LogoutException for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

use Exception;

class LogoutException extends Exception
{
    const SAML_LOGOUT_ERROR = 0;

    /**
     * MetadataException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, LogoutException::SAML_LOGOUT_ERROR, null);
    }
}
