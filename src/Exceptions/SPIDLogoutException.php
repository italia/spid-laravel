<?php
/**
 * This class implements a SPIDLogoutException for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

use Exception;

class SPIDLogoutException extends Exception
{
    const SAML_LOGOUT_ERROR = 0;
    const SAML_VALIDATION_ERROR = 1;

    /**
     * SPIDMetadataException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, SPIDLogoutException::SAML_LOGOUT_ERROR, null);
    }
}
