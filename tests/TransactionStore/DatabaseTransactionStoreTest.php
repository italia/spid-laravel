<?php

namespace Italia\SPIDAuth\Tests\TransactionStore;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Models\SPIDTransaction;
use Italia\SPIDAuth\Tests\SPIDAuthBaseTestCase;
use Italia\SPIDAuth\TransactionStore\DatabaseTransactionStore;

class DatabaseTransactionStoreTest extends SPIDAuthBaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the table using Schema directly
        if (!Schema::hasTable('spid_transactions')) {
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
    }

    public function testStoreRequestCreatesRecord()
    {
        $store = new DatabaseTransactionStore();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());

        $store->storeRequest($event);

        $this->assertDatabaseHas('spid_transactions', [
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => '_test-request-id-123',
            'authn_request_issue_instant' => '2024-01-15 12:00:00',
        ]);

        $transaction = SPIDTransaction::first();
        $this->assertStringContainsString('_test-request-id-123', $transaction->authn_request_xml);
    }

    public function testStoreResponseUpdatesExistingRecord()
    {
        $store = new DatabaseTransactionStore();

        // First create a request
        $requestEvent = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());
        $store->storeRequest($requestEvent);

        // Then store the response
        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());
        $store->storeResponse($responseEvent);

        $this->assertDatabaseHas('spid_transactions', [
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => '_test-request-id-123',
            'response_id' => '_test-response-id-456',
            'response_issuer' => 'https://idp.example.com',
            'assertion_id' => '_test-assertion-id-789',
            'assertion_subject' => 'user@example.com',
            'assertion_subject_name_qualifier' => 'https://idp.example.com',
        ]);

        // Should still be just one record
        $this->assertSame(1, SPIDTransaction::count());

        $transaction = SPIDTransaction::first();
        $this->assertStringContainsString('_test-request-id-123', $transaction->authn_request_xml);
        $this->assertStringContainsString('_test-response-id-456', $transaction->response_xml);
    }

    public function testStoreResponseWithoutMatchingRequestCreatesNewRecord()
    {
        $store = new DatabaseTransactionStore();

        // Store response without a matching request
        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());
        $store->storeResponse($responseEvent);

        $this->assertDatabaseHas('spid_transactions', [
            'idp_entity_id' => 'test-idp',
            'authn_request_id' => '_test-request-id-123',
            'response_id' => '_test-response-id-456',
        ]);

        $transaction = SPIDTransaction::first();
        $this->assertNull($transaction->authn_request_xml);
        $this->assertNotNull($transaction->response_xml);
    }

    public function testStoreRequestWithMalformedXmlStoresRawXml()
    {
        $store = new DatabaseTransactionStore();
        $malformedXml = 'This is not valid XML';
        $event = new SPIDAuthenticationRequestEvent('test-idp', $malformedXml);

        $store->storeRequest($event);

        // Should still create a record even with malformed XML
        $this->assertDatabaseHas('spid_transactions', [
            'idp_entity_id' => 'test-idp',
        ]);

        $transaction = SPIDTransaction::first();
        // Extracted fields will be null due to malformed XML
        $this->assertNull($transaction->authn_request_id);
        $this->assertNull($transaction->authn_request_issue_instant);
        // But raw XML should still be stored
        $this->assertSame($malformedXml, $transaction->authn_request_xml);
    }

    public function testStoreResponseWithNullInResponseToCreatesNewRecord()
    {
        $store = new DatabaseTransactionStore();

        // Response XML without InResponseTo
        $xml = <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-999"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
</samlp:Response>
XML;

        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $xml);
        $store->storeResponse($responseEvent);

        $this->assertSame(1, SPIDTransaction::count());
        $transaction = SPIDTransaction::first();
        $this->assertNull($transaction->authn_request_id);
        $this->assertSame('_test-response-id-999', $transaction->response_id);
    }

    public function testStoreRequestLogsErrorOnException()
    {
        $store = new DatabaseTransactionStore();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());

        // Force database error by dropping the table
        Schema::dropIfExists('spid_transactions');

        $this->expectException(Exception::class);

        try {
            $store->storeRequest($event);
        } finally {
            // Restore table for other tests
            if (!Schema::hasTable('spid_transactions')) {
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
        }
    }

    public function testStoreResponseLogsErrorOnException()
    {
        $store = new DatabaseTransactionStore();
        $event = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());

        // Force database error by dropping the table
        Schema::dropIfExists('spid_transactions');

        $this->expectException(Exception::class);

        try {
            $store->storeResponse($event);
        } finally {
            // Restore table for other tests
            if (!Schema::hasTable('spid_transactions')) {
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
        }
    }

    public function testStoreRequestStoresSpEntityId()
    {
        $store = new DatabaseTransactionStore();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());

        $store->storeRequest($event);

        $transaction = SPIDTransaction::first();
        $this->assertSame('https://sp.example.com', $transaction->sp_entity_id);
    }

    public function testStoreRequestGeneratesUuid()
    {
        $store = new DatabaseTransactionStore();
        $event = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());

        $store->storeRequest($event);

        $transaction = SPIDTransaction::first();
        $this->assertNotNull($transaction->uuid);
        $this->assertTrue(\Illuminate\Support\Str::isUuid($transaction->uuid));
    }

    public function testStoreResponseStoresStatusCode()
    {
        $store = new DatabaseTransactionStore();

        // First create a request
        $requestEvent = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());
        $store->storeRequest($requestEvent);

        // Then store the response
        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());
        $store->storeResponse($responseEvent);

        $transaction = SPIDTransaction::first();
        $this->assertSame('urn:oasis:names:tc:SAML:2.0:status:Success', $transaction->status_code);
    }

    public function testStoreResponseStoresSpidLevel()
    {
        $store = new DatabaseTransactionStore();

        // First create a request
        $requestEvent = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());
        $store->storeRequest($requestEvent);

        // Then store the response with AuthnStatement
        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getResponseXmlWithAuthnStatement());
        $store->storeResponse($responseEvent);

        $transaction = SPIDTransaction::first();
        $this->assertSame('https://www.spid.gov.it/SpidL2', $transaction->spid_level);
    }

    public function testStoreResponseWithoutMatchingRequestStoresStatusCodeAndSpidLevel()
    {
        $store = new DatabaseTransactionStore();

        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getResponseXmlWithAuthnStatement());
        $store->storeResponse($responseEvent);

        $transaction = SPIDTransaction::first();
        $this->assertSame('urn:oasis:names:tc:SAML:2.0:status:Success', $transaction->status_code);
        $this->assertSame('https://www.spid.gov.it/SpidL2', $transaction->spid_level);
        $this->assertSame('test-idp', $transaction->idp_entity_id);
    }

    public function testStoreResponseUpdatesStatusCodeAndSpidLevelOnExistingRecord()
    {
        $store = new DatabaseTransactionStore();

        // First create a request
        $requestEvent = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());
        $store->storeRequest($requestEvent);

        // Verify initial state
        $transaction = SPIDTransaction::first();
        $this->assertNull($transaction->status_code);
        $this->assertNull($transaction->spid_level);

        // Store response with AuthnStatement
        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getResponseXmlWithAuthnStatement());
        $store->storeResponse($responseEvent);

        $transaction->refresh();
        $this->assertSame('urn:oasis:names:tc:SAML:2.0:status:Success', $transaction->status_code);
        $this->assertSame('https://www.spid.gov.it/SpidL2', $transaction->spid_level);
    }

    public function testStoreResponseWithErrorStatusCode()
    {
        $store = new DatabaseTransactionStore();

        $xml = <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-error"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
    <samlp:Status>
        <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Responder"/>
    </samlp:Status>
</samlp:Response>
XML;

        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $xml);
        $store->storeResponse($responseEvent);

        $transaction = SPIDTransaction::first();
        $this->assertSame('urn:oasis:names:tc:SAML:2.0:status:Responder', $transaction->status_code);
        $this->assertNull($transaction->spid_level);
    }

    public function testStoreResponsePreservesUuidFromRequest()
    {
        $store = new DatabaseTransactionStore();

        // First create a request
        $requestEvent = new SPIDAuthenticationRequestEvent('test-idp', $this->getValidAuthnRequestXml());
        $store->storeRequest($requestEvent);

        $originalUuid = SPIDTransaction::first()->uuid;

        // Then store the response
        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());
        $store->storeResponse($responseEvent);

        // UUID should remain the same after update
        $transaction = SPIDTransaction::first();
        $this->assertSame($originalUuid, $transaction->uuid);
    }

    public function testOrphanResponseGetsOwnUuid()
    {
        $store = new DatabaseTransactionStore();

        $responseEvent = new SPIDAuthenticationResponseEvent('test-idp', $this->getValidResponseXml());
        $store->storeResponse($responseEvent);

        $transaction = SPIDTransaction::first();
        $this->assertNotNull($transaction->uuid);
        $this->assertTrue(\Illuminate\Support\Str::isUuid($transaction->uuid));
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // Use in-memory SQLite for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    private function getValidAuthnRequestXml(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-request-id-123"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:00Z"
    Destination="https://idp.example.com/sso">
    <saml:Issuer>https://sp.example.com</saml:Issuer>
</samlp:AuthnRequest>
XML;
    }

    private function getValidResponseXml(): string
    {
        return <<<XML
<?xml version="1.0"?>
<samlp:Response
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_test-response-id-456"
    InResponseTo="_test-request-id-123"
    Version="2.0"
    IssueInstant="2024-01-15T12:00:05Z"
    Destination="https://sp.example.com/acs">
    <saml:Issuer>https://idp.example.com</saml:Issuer>
    <samlp:Status>
        <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
    </samlp:Status>
    <saml:Assertion ID="_test-assertion-id-789" IssueInstant="2024-01-15T12:00:05Z" Version="2.0">
        <saml:Issuer>https://idp.example.com</saml:Issuer>
        <saml:Subject>
            <saml:NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient" NameQualifier="https://idp.example.com">user@example.com</saml:NameID>
        </saml:Subject>
    </saml:Assertion>
</samlp:Response>
XML;
    }
}
