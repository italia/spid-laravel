<?php
/**
 * Contract for storing SPID transaction logs.
 * Implementations can use different storage backends (database, log files, external systems, etc.).
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Contracts;

use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;

interface TransactionStoreContract
{
    /**
     * Store an AuthnRequest transaction.
     *
     * @param SPIDAuthenticationRequestEvent $event Event containing request data
     * @return void
     */
    public function storeRequest(SPIDAuthenticationRequestEvent $event): void;

    /**
     * Store a Response transaction.
     * Should correlate with existing request using InResponseTo field if possible.
     *
     * @param SPIDAuthenticationResponseEvent $event Event containing response data
     * @return void
     */
    public function storeResponse(SPIDAuthenticationResponseEvent $event): void;
}
