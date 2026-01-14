<?php

namespace Italia\SPIDAuth\Tests\Listeners;

use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Listeners\TransactionLogListener;
use Mockery;
use PHPUnit\Framework\TestCase;

class TransactionLogListenerTest extends TestCase
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
}
