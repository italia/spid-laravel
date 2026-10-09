<?php

namespace Italia\SPIDAuth\Tests;

use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Queue;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Helpers\TransactionLogHelper;
use Italia\SPIDAuth\Listeners\QueuedTransactionLogListener;
use Orchestra\Testbench\TestCase;

class ServiceProviderQueuedListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        TransactionLogHelper::resetCache();
        parent::tearDown();
    }

    public function testQueuedListenerIsRegisteredWhenQueueEnabled()
    {
        Queue::fake();

        event(new SPIDAuthenticationRequestEvent(
            'test-idp',
            '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="_id"/>'
        ));

        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return QueuedTransactionLogListener::class === $job->class;
        });
    }

    protected function getPackageProviders($app)
    {
        return ['Italia\SPIDAuth\ServiceProvider'];
    }

    protected function defineEnvironment($app)
    {
        // Enable transaction logging with the queued driver before the provider boots.
        $app['config']->set('spid-auth.transaction_log.enabled', true);
        $app['config']->set('spid-auth.transaction_log.queue.enabled', true);

        TransactionLogHelper::resetCache();
    }
}
