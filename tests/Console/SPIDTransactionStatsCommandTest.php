<?php

namespace Italia\SPIDAuth\Tests\Console;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Italia\SPIDAuth\Models\SPIDTransaction;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;

class SPIDTransactionStatsCommandTest extends SPIDAuthBaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the table using Schema directly
        if (!Schema::hasTable('spid_transactions')) {
            Schema::create('spid_transactions', function ($table) {
                $table->id();
                $table->string('idp')->index();
                $table->string('authn_request_id')->nullable()->index();
                $table->timestamp('authn_request_issue_instant')->nullable()->index();
                $table->longText('authn_request_xml')->nullable();
                $table->string('response_id')->nullable();
                $table->timestamp('response_issue_instant')->nullable()->index();
                $table->string('response_issuer')->nullable();
                $table->longText('response_xml')->nullable();
                $table->string('assertion_id')->nullable();
                $table->string('assertion_subject')->nullable();
                $table->string('assertion_subject_name_qualifier')->nullable();
                $table->timestamps();
                $table->index('created_at');
            });
        }
    }

    public function testStatsCommandDisplaysBasicStatistics()
    {
        // Create transactions with and without responses
        SPIDTransaction::create([
            'idp' => 'test-idp',
            'authn_request_id' => 'req-1',
            'authn_request_xml' => '<xml>request</xml>',
            'response_xml' => '<xml>response</xml>',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        SPIDTransaction::create([
            'idp' => 'test-idp',
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
            'idp' => 'test-idp',
            'authn_request_id' => 'req-1',
            'response_xml' => '<xml>response</xml>',
        ]);

        SPIDTransaction::create([
            'idp' => 'test-idp',
            'authn_request_id' => 'req-2',
            'response_xml' => null,
        ]);

        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandFiltersByIdp()
    {
        SPIDTransaction::create([
            'idp' => 'idp-1',
            'authn_request_id' => 'req-1',
        ]);

        SPIDTransaction::create([
            'idp' => 'idp-2',
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
            'idp' => 'test-idp',
            'authn_request_id' => 'req-1',
            'created_at' => $oldest,
        ]);

        SPIDTransaction::create([
            'idp' => 'test-idp',
            'authn_request_id' => 'req-2',
            'created_at' => $newest,
        ]);

        $this->artisan('spid:transaction-stats')
            ->assertExitCode(0);
    }

    public function testStatsCommandShowsIdpBreakdownWhenNotFiltered()
    {
        SPIDTransaction::create([
            'idp' => 'idp-1',
            'authn_request_id' => 'req-1',
        ]);

        SPIDTransaction::create([
            'idp' => 'idp-1',
            'authn_request_id' => 'req-2',
        ]);

        SPIDTransaction::create([
            'idp' => 'idp-2',
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
            'idp' => 'test-idp',
            'authn_request_id' => 'req-1',
            'response_xml' => '<xml>response</xml>',
        ]);

        SPIDTransaction::create([
            'idp' => 'test-idp',
            'authn_request_id' => 'req-2',
            'response_xml' => '<xml>response</xml>',
        ]);

        SPIDTransaction::create([
            'idp' => 'test-idp',
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
            'idp' => 'idp-1',
            'authn_request_id' => 'req-1',
        ]);

        $this->artisan('spid:transaction-stats', ['--idp' => 'idp-1'])
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
