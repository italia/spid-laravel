<?php
/**
 * Database-backed transaction store implementation.
 * Stores SPID transactions in the spid_transactions table via Eloquent.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\TransactionStore;

use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Models\SPIDTransaction;

class DatabaseTransactionStore implements TransactionStoreContract
{
    /**
     * Store an AuthnRequest transaction.
     *
     * @param SPIDAuthenticationRequestEvent $event Event containing request data
     * @return void
     */
    public function storeRequest(SPIDAuthenticationRequestEvent $event): void
    {
        SPIDTransaction::create([
            'idp' => $event->getIdp(),
            'authn_request_id' => $event->getAuthnRequestId(),
            'authn_request_issue_instant' => $event->getAuthnRequestIssueInstant(),
            'authn_request_xml' => $event->getAuthnRequestXml(),
        ]);
    }

    /**
     * Store a Response transaction.
     * Attempts to correlate with existing request using InResponseTo field.
     * If no matching request is found, creates a new record with partial data.
     *
     * @param SPIDAuthenticationResponseEvent $event Event containing response data
     * @return void
     */
    public function storeResponse(SPIDAuthenticationResponseEvent $event): void
    {
        $inResponseTo = $event->getResponseInResponseTo();

        // Try to find and update existing request record
        if ($inResponseTo !== null) {
            $transaction = SPIDTransaction::where('authn_request_id', $inResponseTo)->first();

            if ($transaction !== null) {
                $transaction->update([
                    'response_id' => $event->getResponseId(),
                    'response_issue_instant' => $event->getResponseIssueInstant(),
                    'response_issuer' => $event->getResponseIssuer(),
                    'response_xml' => $event->getResponseXml(),
                    'assertion_id' => $event->getAssertionId(),
                    'assertion_subject' => $event->getAssertionSubject(),
                    'assertion_subject_name_qualifier' => $event->getAssertionSubjectNameQualifier(),
                ]);
                return;
            }
        }

        // If no matching request found, create a new record with response data only
        SPIDTransaction::create([
            'idp' => $event->getIdp(),
            'authn_request_id' => $inResponseTo,
            'response_id' => $event->getResponseId(),
            'response_issue_instant' => $event->getResponseIssueInstant(),
            'response_issuer' => $event->getResponseIssuer(),
            'response_xml' => $event->getResponseXml(),
            'assertion_id' => $event->getAssertionId(),
            'assertion_subject' => $event->getAssertionSubject(),
            'assertion_subject_name_qualifier' => $event->getAssertionSubjectNameQualifier(),
        ]);
    }
}
