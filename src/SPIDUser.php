<?php
/**
 * This class implements a SPIDUser for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth;

use Italia\SPIDAuth\SPIDUser;

class SPIDUser
{

   /**
    * SPID attributes set in spid-auth config
    */
    protected $attributes;

    /**
     * Create a new SPIDUser instance.
     *
     * @param array $SPIDUser
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute][0];
        }
        
        return null;
    }
}
