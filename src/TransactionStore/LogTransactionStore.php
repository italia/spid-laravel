<?php
/**
 * Log-based transaction store implementation.
 * Writes SPID transactions as structured JSON to a configured log channel.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\TransactionStore;

use Illuminate\Support\Facades\Log;
use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;

class LogTransactionStore implements TransactionStoreContract
{
    /**
     * Store an AuthnRequest transaction.
     *
     * @param SPIDAuthenticationRequestEvent $event Event containing request data
     * @return void
     */
    public function storeRequest(SPIDAuthenticationRequestEvent $event): void
    {
        $channel = config('spid-auth.transaction_log.log.channel', 'stack');

        Log::channel($channel)->info('SPID Authentication Request', [
            'type' => 'authn_request',
            'idp' => $event->getIdp(),
            'authn_request_id' => $event->getAuthnRequestId(),
            'authn_request_issue_instant' => $event->getAuthnRequestIssueInstant(),
            'authn_request_xml' => $event->getAuthnRequestXml(),
        ]);
    }

    /**
     * Store a Response transaction.
     *
     * @param SPIDAuthenticationResponseEvent $event Event containing response data
     * @return void
     */
    public function storeResponse(SPIDAuthenticationResponseEvent $event): void
    {
        $channel = config('spid-auth.transaction_log.log.channel', 'stack');

        Log::channel($channel)->info('SPID Authentication Response', [
            'type' => 'authn_response',
            'idp' => $event->getIdp(),
            'response_id' => $event->getResponseId(),
            'response_issue_instant' => $event->getResponseIssueInstant(),
            'response_issuer' => $event->getResponseIssuer(),
            'response_in_response_to' => $event->getResponseInResponseTo(),
            'response_xml' => $event->getResponseXml(),
            'assertion_id' => $event->getAssertionId(),
            'assertion_subject' => $event->getAssertionSubject(),
            'assertion_subject_name_qualifier' => $event->getAssertionSubjectNameQualifier(),
        ]);
    }
}
