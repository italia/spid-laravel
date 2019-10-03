<?php
/**
 * This class implements a SPIDLogoutException for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

use Exception;

class SPIDLogoutException extends Exception
{
    const SAML_LOGOUT_ERROR = 0;
    const SAML_VALIDATION_ERROR = 1;
}
