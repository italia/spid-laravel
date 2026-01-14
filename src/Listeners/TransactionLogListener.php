<?php
/**
 * Listener for SPID authentication events.
 * Delegates transaction storage to the configured store implementation.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
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
     * @return void
     */
    public function handle($event): void
    {
        if ($event instanceof SPIDAuthenticationRequestEvent) {
            $this->store->storeRequest($event);
        } elseif ($event instanceof SPIDAuthenticationResponseEvent) {
            $this->store->storeResponse($event);
        }
    }
}
