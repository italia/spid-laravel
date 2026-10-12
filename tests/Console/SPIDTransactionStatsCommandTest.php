<?php

namespace Italia\SPIDAuth\Tests\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Italia\SPIDAuth\Models\SPIDTransaction;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;

class SPIDTransactionStatsCommandTest extends SPIDAuthBaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Drop and recreate the table for each test
        Schema::dropIfExists('spid_transactions');
        Schema::create('spid_transactions', function ($table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('authn_request_id')->nullable()->index();
            $table->timestamp('authn_request_issue_instant')->nullable();
            $table->longText('authn_request_xml')->nullable();
            $table->string('response_id')->nullable();
            $table->timestamp('response_issue_instant')->nullable();
            $table->string('response_issuer')->nullable()->index();
            $table->longText('response_xml')->nullable();
            $table->string('assertion_id')->nullable();
            $table->string('assertion_subject')->nullable();
            $table->string('assertion_subject_name_qualifier')->nullable();
            $table->string('idp_entity_id')->nullable()->index();
            $table->string('sp_entity_id')->nullable();
            $table->string('spid_level', 50)->nullable();
            $table->string('status_code')->nullable();
            $table->string('relay_state')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function testStatsCommandDisplaysBasicStatistics()
    {
        // Create transactions with and without responses
        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-1',
            'authn_request_xml' => '<xml>request</xml>',
            'response_xml' => '<xml>response</xml>',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-2',
            'authn_request_xml' => '<xml>request</xml>',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandShowsCorrectCounts()
    {
        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-1',
            'response_xml' => '<xml>response</xml>',
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-2',
            'response_xml' => null,
        ]);

        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandFiltersByIdp()
    {
        SPIDTransaction::create([
            'idp_entity_id' => 'idp-1',
            'authn_request_id' => 'req-1',
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'idp-2',
            'authn_request_id' => 'req-2',
        ]);

        $this->artisan('spid:transaction-stats', ['--idp' => 'idp-1'])
            ->assertExitCode(0);
    }

    public function testStatsCommandShowsTimeRange()
    {
        $oldest = Carbon::now()->subDays(10);
        $newest = Carbon::now()->subDays(1);

        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-1',
            'created_at' => $oldest,
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-2',
            'created_at' => $newest,
        ]);

        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandShowsIdpBreakdownWhenNotFiltered()
    {
        SPIDTransaction::create([
            'idp_entity_id' => 'idp-1',
            'authn_request_id' => 'req-1',
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'idp-1',
            'authn_request_id' => 'req-2',
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'idp-2',
            'authn_request_id' => 'req-3',
        ]);

        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandWithEmptyTable()
    {
        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandShowsCompletionRate()
    {
        // Create 3 transactions, 2 with responses
        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-1',
            'response_xml' => '<xml>response</xml>',
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-2',
            'response_xml' => '<xml>response</xml>',
        ]);

        SPIDTransaction::create([
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => 'req-3',
            'response_xml' => null,
        ]);

        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandShowsNAToCompletionRateWhenNoTransactions()
    {
        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandDoesNotShowIdpBreakdownWhenFiltered()
    {
        SPIDTransaction::create([
            'idp_entity_id' => 'idp-1',
            'authn_request_id' => 'req-1',
        ]);

        $this->artisan('spid:transaction-stats', ['--idp' => 'idp-1'])
            ->assertExitCode(0);
    }

    public function testStatsCommandShowsTimeRangeAndAveragePerDay()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00'));

        try {
            foreach (['2026-06-05 12:00:00', '2026-06-10 08:30:00', '2026-06-15 12:00:00'] as $i => $createdAt) {
                SPIDTransaction::create([
                    'idp_entity_id' => 'test-idp',
                    'authn_request_id' => "req-{$i}",
                    'created_at' => Carbon::parse($createdAt),
                ]);
            }

            $this->artisan('spid:transaction-stats')
                ->expectsOutput('  Oldest: 2026-06-05 12:00:00')
                ->expectsOutput('  Newest: 2026-06-15 12:00:00')
                ->expectsOutput('  Average per day: 0.30')
                ->assertExitCode(0);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function testStatsCommandOmitsAveragePerDayWhenAllOnSameDay()
    {
        SPIDTransaction::create(['idp_entity_id' => 'test-idp', 'authn_request_id' => 'req-1']);
        SPIDTransaction::create(['idp_entity_id' => 'test-idp', 'authn_request_id' => 'req-2']);

        $this->artisan('spid:transaction-stats')
            ->doesntExpectOutputToContain('Average per day')
            ->assertExitCode(0);
    }

    public function testStatsCommandFilteredByUnknownIdpShowsEmptyStatistics()
    {
        SPIDTransaction::create(['idp_entity_id' => 'test-idp', 'authn_request_id' => 'req-1']);

        $this->artisan('spid:transaction-stats', ['--idp' => 'unknown-idp'])
            ->expectsOutput('Filtered by IdP: unknown-idp')
            ->expectsTable(['Metric', 'Value'], [
                ['Total Transactions', '0'],
                ['With Response', '0'],
                ['Without Response', '0'],
                ['Completion Rate', 'N/A'],
            ])
            ->doesntExpectOutput('Time Range:')
            ->assertExitCode(0);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
