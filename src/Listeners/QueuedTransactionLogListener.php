<?php
/**
 * Queueable listener for SPID authentication events.
 * Delegates transaction storage to the configured store implementation.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
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
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->connection = config('spid-auth.transaction_log.queue_connection');
    }

    /**
     * Handle the event.
     *
     * @param SPIDAuthenticationRequestEvent|SPIDAuthenticationResponseEvent $event
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
        } catch (\Exception $e) {
            // Log error but don't interrupt authentication flow
            Log::error('Failed to store SPID transaction log', [
                'event_type' => get_class($event),
                'idp' => $event->getIdp(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
