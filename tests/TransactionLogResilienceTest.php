<?php

namespace Italia\SPIDAuth\Tests;

use DateTimeInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Helpers\TransactionLogHelper;
use Italia\SPIDAuth\Models\SPIDTransaction;
use Mockery as m;
use OneLogin\Saml2\Utils as SAMLUtils;
use RuntimeException;

class TransactionLogResilienceTest extends SPIDAuthBaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        TransactionLogHelper::resetCache();

        Schema::dropIfExists('spid_transactions');
        $this->stubMigration()->up();
    }

    protected function tearDown(): void
    {
        TransactionLogHelper::resetCache();

        parent::tearDown();
    }

    public function testMigrationStubCreatesTableMatchingModel()
    {
        $attributes = [
            'idp_entity_id' => 'test',
            'sp_entity_id' => 'https://sp.example.org',
            'authn_request_id' => '_request',
            'authn_request_issue_instant' => '2026-06-15 12:00:00',
            'authn_request_xml' => '<samlp:AuthnRequest/>',
            'response_id' => '_response',
            'response_issue_instant' => '2026-06-15 12:00:05',
            'response_issuer' => 'spid-testenv',
            'response_xml' => '<samlp:Response/>',
            'assertion_id' => '_assertion',
            'assertion_subject' => '_nameid',
            'assertion_subject_name_qualifier' => 'spid-testenv',
            'spid_level' => 'https://www.spid.gov.it/SpidL2',
            'status_code' => 'urn:oasis:names:tc:SAML:2.0:status:Success',
            'relay_state' => 'relay',
        ];

        // Every fillable column (besides the ones Eloquent manages) exists in the stub.
        $fillable = array_values(array_diff((new SPIDTransaction())->getFillable(), ['uuid', 'created_at', 'updated_at']));
        $this->assertEqualsCanonicalizing($fillable, array_keys($attributes));

        $transaction = SPIDTransaction::create($attributes)->fresh();

        foreach ($attributes as $column => $value) {
            $stored = $transaction->{$column};
            $this->assertSame($value, $stored instanceof DateTimeInterface ? $stored->format('Y-m-d H:i:s') : $stored, $column);
        }

        $this->stubMigration()->down();
        $this->assertFalse(Schema::hasTable('spid_transactions'));
    }

    public function testLoginStillRedirectsToIdpWhenTransactionTableIsMissing()
    {
        // e.g. logging enabled but the published migration was never run.
        Schema::drop('spid_transactions');
        Log::spy();
        $this->setSPIDAuthMock();

        $response = $this->post($this->doLoginURL, ['provider' => 'test']);

        $response->assertRedirect();
        Log::shouldHaveReceived('error')
            ->with('Failed to store SPID authentication request transaction', m::type('array'))
            ->once();
    }

    public function testAcsStillLogsUserInWhenTransactionStoreFails()
    {
        $this->app->bind(TransactionStoreContract::class, function () {
            $store = m::mock(TransactionStoreContract::class);
            $store->shouldReceive('storeRequest', 'storeResponse')->andThrow(new RuntimeException('store down'));

            return $store;
        });
        $this->setSPIDAuthMock();

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertRedirect($this->afterLoginURL);
        $response->assertSessionHas('spid_user');
    }

    public function testFailedAuthenticationDoesNotLogResponse()
    {
        Event::fake([SPIDAuthenticationResponseEvent::class]);
        $this->setSPIDAuthMock()->unauthenticated();

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
        Event::assertNotDispatched(SPIDAuthenticationResponseEvent::class);
        $this->assertSame(0, SPIDTransaction::whereNotNull('response_xml')->count());
    }

    public function testLoginStoresExactlyOneRequestTransaction()
    {
        $this->setSPIDAuthMock();

        $this->post($this->doLoginURL, ['provider' => 'test'])->assertRedirect();

        $this->assertSame(1, SPIDTransaction::count());
        $transaction = SPIDTransaction::first();
        $this->assertSame('test', $transaction->idp_entity_id);
        $this->assertStringContainsString('AuthnRequest', $transaction->authn_request_xml);
        $this->assertNull($transaction->response_xml);
        $this->assertNotEmpty($transaction->uuid);
    }

    public function testDisabledLoggingStoresNothingDuringLoginFlow()
    {
        config(['spid-auth.transaction_log.enabled' => false]);
        TransactionLogHelper::resetCache();
        Event::fake([SPIDAuthenticationRequestEvent::class, SPIDAuthenticationResponseEvent::class]);
        $this->setSPIDAuthMock();

        $this->post($this->doLoginURL, ['provider' => 'test'])->assertRedirect();
        $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL)->assertRedirect($this->afterLoginURL);

        Event::assertNotDispatched(SPIDAuthenticationRequestEvent::class);
        Event::assertNotDispatched(SPIDAuthenticationResponseEvent::class);
        $this->assertSame(0, SPIDTransaction::count());
    }

    protected function stubMigration()
    {
        return require dirname(__DIR__) . '/database/migrations/create_spid_transactions_table.php.stub';
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('spid-auth.transaction_log.enabled', true);
        $app['config']->set('spid-auth.transaction_log.driver', 'database');

        TransactionLogHelper::resetCache();
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
