<?php
/**
 * This class implements a SPIDLoginException for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

class SPIDLoginException extends SPIDException
{
    public const SAML_VALIDATION_ERROR = 0;
    public const SAML_RESPONSE_ALREADY_PROCESSED = 1;
    public const SAML_AUTHENTICATION_ERROR = 2;
    public const SAML_REQUEST_ID_MISSING = 3;
    public const MALFORMED_IDP_IN_USER_REQUEST = 4;
    public const NONEXISTENT_IDP_IN_USER_REQUEST = 5;
}
