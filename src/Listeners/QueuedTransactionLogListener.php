<?php
/**
 * Queueable listener for SPID authentication events.
 * Delegates transaction storage to the configured store implementation.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;

class QueuedTransactionLogListener implements ShouldQueue
{
    /**
     * The connection name for the queue job.
     *
     * @var string|null
     */
    public $connection;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->connection = config('spid-auth.transaction_log.queue.connection');
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
        $store = app(TransactionStoreContract::class);

        try {
            if ($event instanceof SPIDAuthenticationRequestEvent) {
                $store->storeRequest($event);
            } elseif ($event instanceof SPIDAuthenticationResponseEvent) {
                $store->storeResponse($event);
            }
        } catch (Exception $e) {
            // Store already logged the error. Swallow here to protect authentication flow.
        }
    }
}
