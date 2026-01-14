<?php

namespace Italia\SPIDAuth\Tests\Helpers;

use Italia\SPIDAuth\Helpers\TransactionLogHelper;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;

class TransactionLogHelperTest extends SPIDAuthBaseTestCase
{
    protected function tearDown(): void
    {
        // Reset cache after each test to avoid interference
        TransactionLogHelper::resetCache();
        parent::tearDown();
    }

    public function testIsEnabledReturnsTrueWhenConfigIsTrue()
    {
        config(['spid-auth.transaction_log.enabled' => true]);
        TransactionLogHelper::resetCache();

        $this->assertTrue(TransactionLogHelper::isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenConfigIsFalse()
    {
        config(['spid-auth.transaction_log.enabled' => false]);
        TransactionLogHelper::resetCache();

        $this->assertFalse(TransactionLogHelper::isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenConfigIsNotSet()
    {
        // Remove the config key entirely
        config(['spid-auth.transaction_log' => []]);
        TransactionLogHelper::resetCache();

        // Should default to false when config key doesn't exist
        $this->assertFalse(TransactionLogHelper::isEnabled());
    }

    public function testIsEnabledCachesResult()
    {
        config(['spid-auth.transaction_log.enabled' => true]);
        TransactionLogHelper::resetCache();

        // First call should read from config
        $firstResult = TransactionLogHelper::isEnabled();
        $this->assertTrue($firstResult);

        // Change config
        config(['spid-auth.transaction_log.enabled' => false]);

        // Second call should return cached value (true)
        $secondResult = TransactionLogHelper::isEnabled();
        $this->assertTrue($secondResult);
    }

    public function testResetCacheClearsCache()
    {
        config(['spid-auth.transaction_log.enabled' => true]);
        TransactionLogHelper::resetCache();

        // First call caches true
        TransactionLogHelper::isEnabled();

        // Change config
        config(['spid-auth.transaction_log.enabled' => false]);

        // Reset cache
        TransactionLogHelper::resetCache();

        // Now should read new value
        $this->assertFalse(TransactionLogHelper::isEnabled());
    }
}
