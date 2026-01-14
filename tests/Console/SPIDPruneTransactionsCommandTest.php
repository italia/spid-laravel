<?php

namespace Italia\SPIDAuth\Tests\Console;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Italia\SPIDAuth\Models\SPIDTransaction;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;

class SPIDPruneTransactionsCommandTest extends SPIDAuthBaseTestCase
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

    public function testPruneDeletesOldTransactions()
    {
        // Create old transactions (older than 24 months) with explicit timestamps
        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'old-1',
            'created_at' => Carbon::now()->subMonths(25),
            'updated_at' => Carbon::now()->subMonths(25),
        ]);

        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'old-2',
            'created_at' => Carbon::now()->subMonths(30),
            'updated_at' => Carbon::now()->subMonths(30),
        ]);

        // Create recent transaction
        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'recent-1',
            'created_at' => Carbon::now()->subMonths(10),
            'updated_at' => Carbon::now()->subMonths(10),
        ]);

        $this->assertSame(3, SPIDTransaction::count());

        $this->artisan('spid:prune-transactions')
            ->assertExitCode(0);

        $this->assertSame(1, SPIDTransaction::count());
        $this->assertDatabaseMissing('spid_transactions', ['authn_request_id' => 'old-1']);
        $this->assertDatabaseMissing('spid_transactions', ['authn_request_id' => 'old-2']);
        $this->assertDatabaseHas('spid_transactions', ['authn_request_id' => 'recent-1']);
    }

    public function testPruneWithCustomMonthsOption()
    {
        // Create transactions of various ages
        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'very-old',
            'created_at' => Carbon::now()->subMonths(13),
        ]);

        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'recent',
            'created_at' => Carbon::now()->subMonths(5),
        ]);

        $this->assertSame(2, SPIDTransaction::count());

        // Prune with 12 months retention
        $this->artisan('spid:prune-transactions', ['--months' => 12])
            ->expectsOutput('Deleted 1 transaction(s).')
            ->assertExitCode(0);

        $this->assertSame(1, SPIDTransaction::count());
        $this->assertDatabaseMissing('spid_transactions', ['authn_request_id' => 'very-old']);
        $this->assertDatabaseHas('spid_transactions', ['authn_request_id' => 'recent']);
    }

    public function testPruneWithNoOldTransactionsDeletesNone()
    {
        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'recent-1',
            'created_at' => Carbon::now()->subMonths(5),
        ]);

        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'recent-2',
            'created_at' => Carbon::now()->subMonths(10),
        ]);

        $this->artisan('spid:prune-transactions')
            ->expectsOutput('Deleted 0 transaction(s).')
            ->assertExitCode(0);

        $this->assertSame(2, SPIDTransaction::count());
    }

    public function testPruneWithInvalidMonthsReturnsError()
    {
        $this->artisan('spid:prune-transactions', ['--months' => 0])
            ->expectsOutput('Invalid retention period. Must be a positive number of months.')
            ->assertExitCode(1);

        $this->artisan('spid:prune-transactions', ['--months' => -5])
            ->expectsOutput('Invalid retention period. Must be a positive number of months.')
            ->assertExitCode(1);

        $this->artisan('spid:prune-transactions', ['--months' => 'invalid'])
            ->expectsOutput('Invalid retention period. Must be a positive number of months.')
            ->assertExitCode(1);
    }

    public function testPruneUsesConfigRetentionByDefault()
    {
        config(['spid-auth.transaction_log.retention_months' => 6]);

        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'old',
            'created_at' => Carbon::now()->subMonths(7),
        ]);

        SPIDTransaction::create([
            'idp' => 'test',
            'authn_request_id' => 'recent',
            'created_at' => Carbon::now()->subMonths(5),
        ]);

        $this->artisan('spid:prune-transactions')
            ->expectsOutput('Deleted 1 transaction(s).')
            ->assertExitCode(0);

        $this->assertSame(1, SPIDTransaction::count());
        $this->assertDatabaseHas('spid_transactions', ['authn_request_id' => 'recent']);
    }
}
