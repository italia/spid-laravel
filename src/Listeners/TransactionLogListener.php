<?php
/**
 * Listener for SPID authentication events.
 * Delegates transaction storage to the configured store implementation.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Listeners;

use Exception;
use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;

class TransactionLogListener
{
    /** The transaction store instance. */
    protected TransactionStoreContract $store;

    /**
     * Create the event listener.
     *
     * @param TransactionStoreContract $store
     */
    public function __construct(TransactionStoreContract $store)
    {
        $this->store = $store;
    }

    /**
     * Handle the event.
     *
     * @param SPIDAuthenticationRequestEvent|SPIDAuthenticationResponseEvent $event
     *
     * @return void
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof SPIDAuthenticationRequestEvent) {
                $this->store->storeRequest($event);
            } elseif ($event instanceof SPIDAuthenticationResponseEvent) {
                $this->store->storeResponse($event);
            }
        } catch (Exception $e) {
            // Store already logged the error. Swallow here to protect authentication flow.
        }
    }
}
