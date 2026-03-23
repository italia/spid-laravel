<?php
/**
 * Artisan command to prune old SPID transaction logs.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Italia\SPIDAuth\Models\SPIDTransaction;

class SPIDPruneTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spid:prune-transactions
                            {--months= : Override retention period in months}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune SPID transaction logs older than the configured retention period';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $months = $this->option('months')
            ?? config('spid-auth.transaction_log.retention_months', 24);

        if (!is_numeric($months) || $months < 1) {
            $this->error('Invalid retention period. Must be a positive number of months.');

            return 1;
        }

        $cutoffDate = Carbon::now()->subMonths((int) $months);

        $this->info("Pruning SPID transactions older than {$months} months (before {$cutoffDate->toDateString()})...");

        // Bulk-delete in chunks to avoid long table locks while remaining efficient.
        $deletedCount = 0;
        $chunkSize = 1000;

        do {
            $ids = SPIDTransaction::olderThan($cutoffDate)
                ->limit($chunkSize)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $chunkDeleted = SPIDTransaction::whereIn('id', $ids)->delete();
            $deletedCount += $chunkDeleted;

            if ($this->output->isVerbose()) {
                $this->line("Deleted {$chunkDeleted} transaction(s) in this batch...");
            }
        } while ($chunkDeleted === $chunkSize);

        $this->info("Deleted {$deletedCount} transaction(s).");

        return 0;
    }
}
