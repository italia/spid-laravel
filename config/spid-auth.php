<?php
/**
 * This file contains the configuration options needed for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

return [
      'sp_entity_id' => 'https://laravel-test',
      'sp_base_url' => 'https://localhost',
      'sp_service_name' => 'SPID service name',
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
      'sp_certificate_file' => null,
      'sp_private_key_file' => null,
      'sp_certificate' => 'MIIEKjCCAxKgAwIBAgIJAOlEQZ6zeWkPMA0GCSqGSIb3DQEBBQUAMGsxCzAJBgNVBAYTAklUMQswCQYDVQQIEwJSTTENMAsGA1UEBxMEUm9tZTEmMCQGA1UEChMdQWdlbnppYSBwZXIgbCdJdGFsaWEgRGlnaXRhbGUxGDAWBgNVBAMTD3d3dy5hZ2lkLmdvdi5pdDAeFw0xNzEyMTExMTE2NTJaFw0yNzEyMTExMTE2NTJaMGsxCzAJBgNVBAYTAklUMQswCQYDVQQIEwJSTTENMAsGA1UEBxMEUm9tZTEmMCQGA1UEChMdQWdlbnppYSBwZXIgbCdJdGFsaWEgRGlnaXRhbGUxGDAWBgNVBAMTD3d3dy5hZ2lkLmdvdi5pdDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALooizGSq3EdEzrpBJxf82RPxke+WlUZze6AcCaRBXaHDZFgcGLiUIldqfNP3qcoXLGKtvgULbiaETemYCCdVQRNWNAoJQrRa8osDtueuMbRS+fEQeueVseMAZFcqeSfCPD1jXmuoAW3+2BDUsug5f8C21VK470IYgNftz6q24JPFap8E9gaBxnjYnc248J5E/yyGs+Q3kdStDxDCkPSixXAiFPUTlzXbLZJB/9NFoG8iSN3xdX0gT7t9+nrbgWNAEzHkbcjcB7xAN4+gMOI/E70CHOAMnFUTsonCFKq++fzv1BmAQxz724O+M4m22UxG+w117Ia+xlVHzqZmVaQiDcCAwEAAaOB0DCBzTAdBgNVHQ4EFgQULUBdclYE+qoz6/E7kOIKUJpfWC0wgZ0GA1UdIwSBlTCBkoAULUBdclYE+qoz6/E7kOIKUJpfWC2hb6RtMGsxCzAJBgNVBAYTAklUMQswCQYDVQQIEwJSTTENMAsGA1UEBxMEUm9tZTEmMCQGA1UEChMdQWdlbnppYSBwZXIgbCdJdGFsaWEgRGlnaXRhbGUxGDAWBgNVBAMTD3d3dy5hZ2lkLmdvdi5pdIIJAOlEQZ6zeWkPMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBACARHgz22mJeQetKife5OWFG1G4sgDJi/MBHIf//0TKcRbcoWh+mF0nnvHs0NlGX0DoXTjL5LFgpoJK43yxHmmdbW8imj8gsGwmWwulJaJDWwq+zKJT07j+GwV8swBFwk0wzcckoqn8Rm1dIeJbtkaid0Za064uFZT5MOpUsQpvrQ8KxXbjZFAhwBx06G9DqCiSEafOANpn99/ewBcO2aKElWOhCajwMqLhr9oW9MbA6U0ujyibfv4uQKECm2BHCnmBf4sGJf0K9zNn9dReoZ7wXjnmW1iaKgjwBEEk4m1zactSkzPxs4EqqZkprkmWTGyhhoD1HziLbcgAHi3ioDAw=',
      'sp_private_key' => 'MIIEpAIBAAKCAQEAuiiLMZKrcR0TOukEnF/zZE/GR75aVRnN7oBwJpEFdocNkWBwYuJQiV2p80/epyhcsYq2+BQtuJoRN6ZgIJ1VBE1Y0CglCtFryiwO2564xtFL58RB655Wx4wBkVyp5J8I8PWNea6gBbf7YENSy6Dl/wLbVUrjvQhiA1+3Pqrbgk8VqnwT2BoHGeNidzbjwnkT/LIaz5DeR1K0PEMKQ9KLFcCIU9ROXNdstkkH/00WgbyJI3fF1fSBPu336etuBY0ATMeRtyNwHvEA3j6Aw4j8TvQIc4AycVROyicIUqr75/O/UGYBDHPvbg74zibbZTEb7DXXshr7GVUfOpmZVpCINwIDAQABAoIBAQCfkvWObv2LHrNHQktziERo7oE3KpLgdBg0o+B/Dr0yFx6oSZTNDtaeia2PJh3kCBM9FX02NoXiwh6UJ4grLPKdl3fUJzVpio7tZMrvs6UMuIhqia9APCCDOR527omrsi/F7ZdygSAnBsjygYNNjgTZjidZe7Kwbakm3zuC+o7jqVLEWWZCaSjL+WoiQmfl8443aNSlujEnpPzGORQ2lz53oduZtRSKXdZLi2G7+afk1GcerCYoUtvFGPa5uX92gTDKwIpPYQpdxVqqhFrZcV73tP0VqE3kG5FWaCbSKO/nVd3YlswabXsPEMNth17vs/g7RtbWWkWE4nv9bQzG2lpBAoGBAPJw1NusIXSzOwauDvDY9gCgr2WpDedhJu1p+ibqO86TvaM3J55/oUCYT3e2JyIo1nixf3njJp9PyAcbBq5bEAl0cqgxHMDY8FiBblQ9ELoFZi1HxcMgfo3pdmYID1O1pRlfGJHNQPZxGns6NWA88a1A7T6y/tdDGF4XqbqCFwixAoGBAMSR4k1Ox5HzArQ6ecXL+fTXexMRBrz2fkq4oTodnN/R90mROEiD4CSxLLt2Dh9MfksykWNSy9KIKsNA+u0uQ0t1aZ+L5f6K105TuNO4cLFKIOVgG8qbkkItMbxWZwrRECYeRHjylJ8DaJJm3MNaQGYPVLpLEbIwxlDDPIbDTtlnAoGASuzv+8vgsw5JKbFVUL3cmSkPy91JPL7bpvffpXMydI9YRj7fca6ECVCJDrgus/HnBnnkqai34PhpGzkRAYWHGUTxwmUbO8ZP0Sp/DG2q47KTd179EWbTdcw3GSVYh0hV89dd4oGKmB8wTfEZWdq73g14xK3Q8Hn95+ZsQAXAVKECgYAvKLy9RVg12LJeYOUUIjKunf/F/EC8lvqHnLH5il83h10nhjKVmyXsR6FuvAz5T1XBXrlotdV2hfqUa4OH4aB9ewbDI0IjhlXPUeC20roenqUzwpIIUriNLeCPyb7g5nVUX9CXn8MuPxgYf5sZNw7aLXVrWFU/hdoeSCi+QB8rowKBgQDHaCH7NAXpo3yDtdN+PPuZPnXnTMHQctLbXytuzqqDNbC5z4EVPv8PEOVl8h6CEk7IhdQBnpxd8s6IODRNy7csqoQduvaLhNJiahVSbsZFsjLs4IaO3kRfaUL7I8BaW+1I4pfa2JLSZFfEv4BguqW3QEOH7BuxaSi78GvNwnvaag==',
      
      #'test_idp' => false,
      'test_idp' => [
        'entityId' => 'spid-testenv',
        'sso_endpoint' => 'https://spid-testenv/sso',
        'slo_endpoint' => 'https://spid-testenv/slo',
        'x509cert' => 'spid-testenv-cert'
      ],

      'middleware_group' => 'web',
      'routes_prefix' => 'spid',
      'login_view' => 'spid-auth::login-spid',
      'after_login_url' => '/',
      'after_logout_url' => '/'
];
