<?php
/**
 * This class implements a MetadataException for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Exceptions;

use Exception;

class MetadataException extends Exception
{
    const SAML_METADATA_VALIDATION_ERROR = 0;

    /**
     * MetadataException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, MetadataException::SAML_METADATA_VALIDATION_ERROR, null);
    }
}
