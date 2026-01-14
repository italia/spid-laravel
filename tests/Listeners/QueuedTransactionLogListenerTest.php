<?php

namespace Italia\SPIDAuth\Tests\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Listeners\QueuedTransactionLogListener;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;
use Mockery;

class QueuedTransactionLogListenerTest extends SPIDAuthBaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testImplementsShouldQueue()
    {
        $listener = new QueuedTransactionLogListener();
        $this->assertInstanceOf(ShouldQueue::class, $listener);
    }

    public function testHandleRequestEventCallsStoreRequest()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationRequestEvent('test-idp', '<xml>request</xml>');

        $store->shouldReceive('storeRequest')
            ->once()
            ->with($event);

        // Mock app container to return our mock store
        app()->instance(TransactionStoreContract::class, $store);

        $listener = new QueuedTransactionLogListener();
        $listener->handle($event);
    }

    public function testHandleResponseEventCallsStoreResponse()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationResponseEvent('test-idp', '<xml>response</xml>');

        $store->shouldReceive('storeResponse')
            ->once()
            ->with($event);

        // Mock app container to return our mock store
        app()->instance(TransactionStoreContract::class, $store);

        $listener = new QueuedTransactionLogListener();
        $listener->handle($event);
    }

    public function testHandleRequestEventHandlesExceptionWithoutThrowing()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationRequestEvent('test-idp', '<xml>request</xml>');

        $store->shouldReceive('storeRequest')
            ->once()
            ->andThrow(new Exception('Storage failed'));

        // Mock app container to return our mock store
        app()->instance(TransactionStoreContract::class, $store);

        $listener = new QueuedTransactionLogListener();

        // Should not throw exception, should handle it gracefully
        $listener->handle($event);

        $this->assertTrue(true);
    }

    public function testHandleResponseEventHandlesExceptionWithoutThrowing()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationResponseEvent('test-idp', '<xml>response</xml>');

        $store->shouldReceive('storeResponse')
            ->once()
            ->andThrow(new Exception('Storage failed'));

        // Mock app container to return our mock store
        app()->instance(TransactionStoreContract::class, $store);

        $listener = new QueuedTransactionLogListener();

        // Should not throw exception, should handle it gracefully
        $listener->handle($event);

        $this->assertTrue(true);
    }

    public function testConstructorSetsConnectionFromConfig()
    {
        config(['spid-auth.transaction_log.queue_connection' => 'redis']);
        $listener = new QueuedTransactionLogListener();
        $this->assertSame('redis', $listener->connection);
    }

    public function testConstructorSetsConnectionToNullWhenNotConfigured()
    {
        config(['spid-auth.transaction_log.queue_connection' => null]);
        $listener = new QueuedTransactionLogListener();
        $this->assertNull($listener->connection);
    }
}
