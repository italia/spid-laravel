<?php
/**
 * This file contains the config settings for your SPID Service Provider.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

 // Service Provider Data that we are deploying
return [
  'sp' => [
    // Identifier of the SP entity  (must be a URI)
    'entityId' => '',
    // Specifies info about where and how the <AuthnResponse> message MUST be
    // returned to the requester, in this case our SP.
    'assertionConsumerService' => [
        // URL Location where the <Response> from the IdP will be returned
        'url' => '',
        // SAML protocol binding to be used when returning the <Response>
        // message.  Onelogin Toolkit supports for this endpoint the
        // HTTP-POST binding only
        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        'index' => '',
    ],
    // If you need to specify requested attributes, set a
    // attributeConsumingService. nameFormat, attributeValue and
    // friendlyName can be omitted. Otherwise remove this section.
    'attributeConsumingService' => [
        'serviceName' => '',
        'requestedAttributes' => [],
        'index' => '',
    ],
    // Specifies info about where and how the <Logout Response> message MUST be
    // returned to the requester, in this case our SP.
    'singleLogoutService' => [
        // URL Location where the <Response> from the IdP will be returned
        'url' => '',
        // SAML protocol binding to be used when returning the <Response>
        // message.  Onelogin Toolkit supports for this endpoint the
        // HTTP-Redirect binding only
        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    // Specifies constraints on the name identifier to be used to
    // represent the requested subject.
    // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',

    // Usually x509cert and privateKey of the SP are provided by files placed at
    // the certs folder. But we can also provide them with the following parameters
    'x509cert' => '',
    'privateKey' => '',
  ],
  'security' => [
    'authnRequestsSigned' => true,
    'logoutRequestSigned' => true,
    'signMetadata' => true,
    'wantAssertionsSigned' => true,
    'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
    'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
    'requestedAuthnContext' => [],
    'requestedAuthnContextComparison' => 'minimum',
    'destinationStrictlyMatches' => true,
    'rejectUnsolicitedResponsesWithInResponseTo' => true,
  ],
  'strict' => true,

  // Organization information template, the info in en_US lang is recomended, add more if required
  'organization' => [
    'en' => [
      'name' => '',
      'displayname' => '',
      'url' => '',
    ],
    'it' => [
      'name' => '',
      'displayname' => '',
      'url' => '',
    ],
  ],
];
