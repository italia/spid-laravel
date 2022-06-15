<?php

namespace Italia\SPIDAuth\Tests;

use Italia\SPIDAuth\Exceptions\SPIDLoginAnomalyException;
use Italia\SPIDAuth\Exceptions\SPIDLoginException;
use OneLogin\Saml2\Utils as SAMLUtils;

class ResponseValidationTest extends SPIDAuthBaseTestCase
{
    public function testMissingRequestIssueInstant()
    {
        $this->setSPIDAuthMock();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_AUTHENTICATION_ERROR);
        $this->expectExceptionMessage('SAML authentication error: missing spid_lastRequestIssueInstant');

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testInvalidAssertionNotOnOrAfter()
    {
        $this->setSPIDAuthMock()->withInvalidAssertionNotOnOrAfter();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage('SAML response validation error: invalid NotOnOrAfter attribute');

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testLessAttributes()
    {
        $this->setSPIDAuthMock()->withLessAttributes();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage('SAML response validation error: requested attributes not present');

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testEmptyAttribute()
    {
        $this->setSPIDAuthMock()->withEmptyAttribute();
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage('SAML response validation error: empty attribute');

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }

    public function testMissingDestination()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'missing_destination.xml',
            'exceptionMessage' => 'SAML response validation error: missing Destination attribute',
        ]);
    }

    public function testMissingInResponseTo()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'missing_inresponseto.xml',
            'exceptionMessage' => 'SAML response validation error: missing InResponseTo attribute',
        ]);
    }

    public function testWrongIssuerFormat()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'wrong_issuer_format.xml',
            'exceptionMessage' => 'SAML response validation error: wrong Issuer Format attribute',
        ]);
    }

    public function testWrongAssertionVersion()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'wrong_assertion_version.xml',
            'exceptionMessage' => 'SAML response validation error: wrong Assertion Version attribute',
        ]);
    }

    public function testEmptyNameId()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'empty_nameid.xml',
            'exceptionMessage' => 'SAML response validation error: empty NameID',
        ]);
    }

    public function testWrongNameIdFormat()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'wrong_nameid_format.xml',
            'exceptionMessage' => 'SAML response validation error: wrong NameID Format attribute',
        ]);
    }

    public function testMissingNameIdNameQualifier()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'missing_nameid_namequalifier.xml',
            'exceptionMessage' => 'SAML response validation error: empty or missing NameID NameQualifier attribute',
        ]);
    }

    public function testEmptySubjectConfirmationDataRecipient()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'empty_subjectconfirmationdata_recipient.xml',
            'exceptionMessage' => 'SAML response validation error: empty or missing SubjectConfirmationData Recipient attribute',
        ]);
    }

    public function testMissingSubjectConfirmationDataInResponseTo()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'missing_subjectconfirmationdata_inresponseto.xml',
            'exceptionMessage' => 'SAML response validation error: missing SubjectConfirmationData InResponseTo attribute',
        ]);
    }

    public function testWrongAssertionIssuerFormat()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'wrong_assertion_issuer_format.xml',
            'exceptionMessage' => 'SAML response validation error: wrong Assertion Issuer Format attribute',
        ]);
    }

    public function testEmptyConditions()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'empty_conditions.xml',
            'exceptionMessage' => 'SAML response validation error: empty Conditions',
        ]);
    }

    public function testEmptyConditionsNotBefore()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'empty_conditions_notbefore.xml',
            'exceptionMessage' => 'SAML response validation error: empty Conditions',
        ]);
    }

    public function testEmptyConditionsNotOnOrAfter()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'empty_conditions_notonorafter.xml',
            'exceptionMessage' => 'SAML response validation error: empty Conditions',
        ]);
    }

    public function testEmptyAudienceRestriction()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'empty_audiencerestriction.xml',
            'exceptionMessage' => 'SAML response validation error: empty or missing AudienceRestriction element',
        ]);
    }

    public function testWrongAuthContextClassRef()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'wrong_authcontextclassref.xml',
            'exceptionMessage' => 'SAML response validation error: wrong AuthContextClassRef element',
        ]);
    }

    public function testInvalidResponseIssueInstant()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'valid_level1.xml',
            'responseIssueInstant' => 'invalid-response-issue-instant',
            'exceptionMessage' => 'SAML response validation error: invalid IssueInstant attribute',
        ]);
    }

    public function testInvalidAssertionIssueInstant()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'valid_level1.xml',
            'assertionIssueInstant' => 'invalid-assertion-issue-instant',
            'exceptionMessage' => 'SAML response validation error: invalid IssueInstant attribute',
        ]);
    }

    public function testWrongResponseIssueInstant()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'valid_level1.xml',
            'responseIssueInstant' => SAMLUtils::parseTime2SAML(time() + 400),
            'exceptionMessage' => 'SAML response validation error: wrong Response IssueInstant attribute',
        ]);

        $this->runValidationTest([
            'responseXmlFile' => 'valid_level1.xml',
            'responseIssueInstant' => SAMLUtils::parseTime2SAML(time() - 400),
            'exceptionMessage' => 'SAML response validation error: wrong Response IssueInstant attribute',
        ]);
    }

    public function testWrongAssertionIssueInstant()
    {
        $this->runValidationTest([
            'responseXmlFile' => 'valid_level1.xml',
            'assertionIssueInstant' => SAMLUtils::parseTime2SAML(time() + 400),
            'exceptionMessage' => 'SAML response validation error: wrong Assertion IssueInstant attribute',
        ]);

        $this->runValidationTest([
            'responseXmlFile' => 'valid_level1.xml',
            'assertionIssueInstant' => SAMLUtils::parseTime2SAML(time() - 400),
            'exceptionMessage' => 'SAML response validation error: wrong Assertion IssueInstant attribute',
        ]);
    }

    public function testSpidAnomalies()
    {
        foreach (SPIDLoginAnomalyException::ERROR_CODES as $errorCode => $errorMessage) {
            $this->setSPIDAuthMock()->withErrorReason($errorMessage);
            $this->withoutExceptionHandling();

            try {
                $response = $this->withCookies([
                    'spid_lastRequestId' => 'UNIQUE_ID',
                    'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
                    'spid_idp' => 'test',
                ])->post($this->acsURL);
            } catch (SPIDLoginAnomalyException $e) {
                $this->assertEquals($errorCode, $e->getErrorCode());
                $this->assertEquals(__('spid-auth::messages.anomalies.' . $errorCode), $e->getUserMessage());

                return;
            }

            $this->fail();
        }
    }

    protected function runValidationTest(array $testSettings)
    {
        $responseXML = file_get_contents(__DIR__ . '/responses/' . $testSettings['responseXmlFile']);
        $compiledResponseXML = str_replace('{{IssueInstant}}', SAMLUtils::parseTime2SAML(time()), $responseXML);
        $compiledResponseXML = str_replace('{{ResponseIssueInstant}}', $testSettings['responseIssueInstant'] ?? SAMLUtils::parseTime2SAML(time()), $compiledResponseXML);
        $compiledResponseXML = str_replace('{{AssertionIssueInstant}}', $testSettings['assertionIssueInstant'] ?? SAMLUtils::parseTime2SAML(time()), $compiledResponseXML);
        $this->setSPIDAuthMock()->withLastResponseXML($compiledResponseXML);
        $this->withoutExceptionHandling();
        $this->expectException(SPIDLoginException::class);
        $this->expectExceptionCode(SPIDLoginException::SAML_VALIDATION_ERROR);
        $this->expectExceptionMessage($testSettings['exceptionMessage']);

        $response = $this->withCookies([
            'spid_lastRequestId' => 'UNIQUE_ID',
            'spid_lastRequestIssueInstant' => SAMLUtils::parseTime2SAML(time()),
            'spid_idp' => 'test',
        ])->post($this->acsURL);

        $response->assertStatus(500);
    }
}
