<?php
/**
 * Helper class for transaction logging functionality.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Helpers;

class TransactionLogHelper
{
    /**
     * Cached value for transaction log enabled status.
     *
     * @var bool|null
     */
    private static ?bool $enabledCache = null;

    /**
     * Check if transaction logging is enabled.
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        if (self::$enabledCache === null) {
            self::$enabledCache = config('spid-auth.transaction_log.enabled', false);
        }

        return self::$enabledCache;
    }

    /**
     * Reset the enabled cache (useful for testing).
     *
     * @return void
     */
    public static function resetCache(): void
    {
        self::$enabledCache = null;
    }
}
