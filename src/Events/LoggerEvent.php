<?php
/**
 * This class implements a Laravel Event for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Events;

use Italia\SPIDAuth\SPIDLogger;

class LoggerEvent
{
    /** The current selected Identity Provider. */
    protected $idp;

    /** The SPIDLogger with current event data. */
    protected $SPIDLogger;

    /**
     * Create a new event instance.
     *
     * @param SPIDLogger current log data
     * @param string current selected Identity Provider
     */
    public function __construct(string $idp, SPIDLogger $SPIDLogger)
    {
        $this->idp = $idp;
        $this->SPIDLogger = $SPIDLogger;
    }

    /**
     * Return idp.
     *
     * @return string Identity Provider used to login
     */
    public function getIdp(): string
    {
        return $this->idp;
    }

    /**
     * Return SPIDLogger.
     *
     * @return SPIDLogger current log data
     */
    public function getSPIDLogger(): SPIDLogger
    {
        return $this->SPIDLogger;
    }
}
