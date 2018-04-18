<?php
/**
 * This class implements a SPIDMetadataException for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

use Exception;

class SPIDMetadataException extends Exception
{
    const SAML_METADATA_VALIDATION_ERROR = 0;

    /**
     * SPIDMetadataException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, SPIDMetadataException::SAML_METADATA_VALIDATION_ERROR, null);
    }
}
