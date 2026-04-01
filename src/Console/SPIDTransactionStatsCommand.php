<?php
/**
 * Artisan command to display SPID transaction logging statistics.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Console;

use Illuminate\Console\Command;
use Italia\SPIDAuth\Models\SPIDTransaction;

class SPIDTransactionStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spid:transaction-stats
                            {--idp= : Filter by Identity Provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display statistics about SPID transaction logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $idp = $this->option('idp_entity_id');

        $query = SPIDTransaction::query();
        if ($idp) {
            $query->where('idp_entity_id', $idp);
        }

        $total = $query->count();
        $withResponse = $query->clone()->whereNotNull('response_xml')->count();
        $withoutResponse = $total - $withResponse;

        $oldest = $query->clone()->orderBy('created_at', 'asc')->first();
        $newest = $query->clone()->orderBy('created_at', 'desc')->first();

        $this->info('SPID Transaction Log Statistics');
        $this->line('');

        if ($idp) {
            $this->line("Filtered by IdP: {$idp}");
            $this->line('');
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Transactions', number_format($total)],
                ['With Response', number_format($withResponse)],
                ['Without Response', number_format($withoutResponse)],
                ['Completion Rate', $total > 0 ? number_format(($withResponse / $total) * 100, 2) . '%' : 'N/A'],
            ]
        );

        if ($oldest && $newest) {
            $this->line('');
            $this->info('Time Range:');
            $this->line("  Oldest: {$oldest->created_at->format('Y-m-d H:i:s')}");
            $this->line("  Newest: {$newest->created_at->format('Y-m-d H:i:s')}");

            $days = $oldest->created_at->diffInDays($newest->created_at);
            if ($days > 0) {
                $avgPerDay = $total / max($days, 1);
                $this->line('  Average per day: ' . number_format($avgPerDay, 2));
            }
        }

        // Group by IdP if not filtered
        if (!$idp) {
            $byIdp = SPIDTransaction::selectRaw('idp_entity_id, COUNT(*) as count')
                ->groupBy('idp_entity_id')
                ->orderByDesc('count')
                ->get();

            if ($byIdp->isNotEmpty()) {
                $this->line('');
                $this->info('Transactions by IdP:');
                $this->table(
                    ['IdP', 'Count'],
                    $byIdp->map(fn ($row) => [$row->idp_entity_id, number_format($row->count)])->toArray()
                );
            }
        }

        return 0;
    }
}
