<?php
/**
 * This class implements a SPIDLoginException for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

class SPIDLoginException extends SPIDException
{
    const SAML_VALIDATION_ERROR = 0;
    const SAML_RESPONSE_ALREADY_PROCESSED = 1;
    const SAML_AUTHENTICATION_ERROR = 2;
    const SAML_REQUEST_ID_MISSING = 3;
    const MALFORMED_IDP_IN_USER_REQUEST = 4;
    const NONEXISTENT_IDP_IN_USER_REQUEST = 5;
}
