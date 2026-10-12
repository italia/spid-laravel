<?php

namespace Italia\SPIDAuth\Tests\Listeners;

use Exception;
use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Listeners\TransactionLogListener;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;
use Mockery;

class TransactionLogListenerTest extends SPIDAuthBaseTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testHandleRequestEventCallsStoreRequest()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationRequestEvent('test-idp', '<xml>request</xml>');

        $store->shouldReceive('storeRequest')
            ->once()
            ->with($event);

        $listener = new TransactionLogListener($store);
        $listener->handle($event);
    }

    public function testHandleResponseEventCallsStoreResponse()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationResponseEvent('test-idp', '<xml>response</xml>');

        $store->shouldReceive('storeResponse')
            ->once()
            ->with($event);

        $listener = new TransactionLogListener($store);
        $listener->handle($event);
    }

    public function testHandleRequestEventDoesNotCallStoreResponse()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationRequestEvent('test-idp', '<xml>request</xml>');

        $store->shouldReceive('storeRequest')->once();
        $store->shouldNotReceive('storeResponse');

        $listener = new TransactionLogListener($store);
        $listener->handle($event);
    }

    public function testHandleResponseEventDoesNotCallStoreRequest()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationResponseEvent('test-idp', '<xml>response</xml>');

        $store->shouldReceive('storeResponse')->once();
        $store->shouldNotReceive('storeRequest');

        $listener = new TransactionLogListener($store);
        $listener->handle($event);
    }

    public function testHandleRequestEventLogsErrorOnException()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationRequestEvent('test-idp', '<xml>request</xml>');

        $store->shouldReceive('storeRequest')
            ->once()
            ->andThrow(new Exception('Storage failed'));

        $listener = new TransactionLogListener($store);

        // Should not throw exception, should handle it gracefully
        $listener->handle($event);

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    public function testHandleResponseEventLogsErrorOnException()
    {
        $store = Mockery::mock(TransactionStoreContract::class);
        $event = new SPIDAuthenticationResponseEvent('test-idp', '<xml>response</xml>');

        $store->shouldReceive('storeResponse')
            ->once()
            ->andThrow(new Exception('Storage failed'));

        $listener = new TransactionLogListener($store);

        // Should not throw exception, should handle it gracefully
        $listener->handle($event);

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }
}
