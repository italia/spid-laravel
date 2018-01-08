<?php
/**
 * This class implements a Laravel Event for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Events;

use Italia\SPIDAuth\SPIDUser;

class LoginEvent
{

    /** The SPIDUser in the current event. */
    protected $SPIDUser;

    /** The current selected Identity Provider. */
    protected $idp;

    /**
     * Create a new event instance.
     *
     * @param SPIDUser Current authenticated SPIDUser.
     * @param string Current selected Identity Provider.
     */
    public function __construct(SPIDUser $SPIDUser, $idp)
    {
        $this->SPIDUser = $SPIDUser;
        $this->idp = $idp;
    }

    /**
     * Return SPIDUser.
     *
     * @return SPIDUser SPIDUser logging in.
     */
    public function getSPIDUser()
    {
        return $this->SPIDUser;
    }

    /**
     * Return idp.
     *
     * @return string Identity Provider used to login.
     */
    public function getIdp()
    {
        return $this->idp;
    }
}
