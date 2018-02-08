<?php
/**
 * This file contains the configuration options needed for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

return [
      'sp_entity_id' => 'laravel-test',
      'sp_organization_name' => 'Example',
      'sp_organization_display_name' => 'Example Organization',
      'sp_organization_url' => 'https://example.org',
      'sp_requested_attributes' => [
          'spidCode',
          'name',
          'familyName',
          #'placeOfBirth',
          #'countyOfBirth',
          #'dateOfBirth',
          #'gender',
          'fiscalNumber',
          #'companyName',
          #'registeredOffice',
          #'ivaCode',
          #'idCard',
          #'mobilePhone',
          #'email',
          #'address',
          #'digitalAddress',
          #'expirationDate'
      ],
      
      'test_idp' => true,

      'middleware_group' => 'web',
      'routes_prefix' => 'spid',
      'login_view' => 'spid-auth::login-spid',
      'after_login_url' => '/',
      'after_logout_url' => '/'
];
