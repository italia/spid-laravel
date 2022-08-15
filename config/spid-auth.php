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
    'sp_acs_index' => 0,
    'sp_attributes_index' => 0,
    'sp_requested_attributes' => [
        'spidCode',
        'name',
        'familyName',
        // 'placeOfBirth',
        // 'countyOfBirth',
        // 'dateOfBirth',
        // 'gender',
        'fiscalNumber',
        // 'companyName',
        // 'registeredOffice',
        // 'ivaCode',
        // 'idCard',
        // 'mobilePhone',
        // 'email',
        // 'address',
        // 'digitalAddress',
        // 'expirationDate',
    ],
    'sp_certificate_file' => null,
    'sp_private_key_file' => null,
    'sp_certificate' => 'MIIDazCCAlOgAwIBAgIUFxfuhfLq6WDpoPcSFG35JcaVvC0wDQYJKoZIhvcNAQELBQAwRTELMAkGA1UEBhMCQVUxEzARBgNVBAgMClNvbWUtU3RhdGUxITAfBgNVBAoMGEludGVybmV0IFdpZGdpdHMgUHR5IEx0ZDAeFw0yMjA4MTUxNDIyMDZaFw0zMjA4MTIxNDIyMDZaMEUxCzAJBgNVBAYTAkFVMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBXaWRnaXRzIFB0eSBMdGQwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDpX8TlZf+q6zqP6oULoXf8B5/egBbqtaKtZ/z02NOan/cAiDaiVxXvIVf3iQ0xjngtKOyWwIliPNy/Eu/2ujA19DiFOOk4RTpXn7/V7AWCnu9tpHY3Ii7TXXraQmRmX0eIGA9c3NTUQA2igNBhy1NI5wFSWWhnNSgASnihzIuG9dJ4bSvWCvxZHCNbCWfjkhzIXwZtNsYKunNSTl9vUYz7JQoAfXd+OI1khhJOzwtsDhnyiyqUJjG5IBcJoUZbNP+SGBbs2V2yq53Uj/QsKWa/lclweEvI3r7cxNyfa7VRRLvavBUX7PGNU9BcSbfYHdcSfWw5QDeSmyPBPxdQ8KgdAgMBAAGjUzBRMB0GA1UdDgQWBBSFWrPZcBQvSKeW5/fbcYWF1FVkfzAfBgNVHSMEGDAWgBSFWrPZcBQvSKeW5/fbcYWF1FVkfzAPBgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQDG2DI4elQ7cK49ggc34AHYxO5TSsHn2Yt6lKxqVT4IWzxPJCoidcGKVO3JmE28kT1t7Z7kF2C2YpYrP05nzJ3k+ZBCYvNsyYZ+v4SjYAY+mXD7gcHhVXiZlW/himb6ITPnQpXOlSWK/egBjNjPc9RhbJGRuwk9CiPbtl7IIuVjUrPxock+slaVKGct1Y9J0h5PyXWzEdoaCchnW90d8CVeBTKNcPKvqVOsHK/gpEaMTUV6k8/jOlE7F0duzMcGFJZLrW6AUofLL6KUiJp40JfyxJi3J0HpQgML6U4jHLrk4knc1whEFCPIZ5lH7nDww/rOrz2rjVXmpwJ2LOUbym88',
    'sp_private_key' => 'MIIEpAIBAAKCAQEA6V/E5WX/qus6j+qFC6F3/Aef3oAW6rWirWf89NjTmp/3AIg2olcV7yFX94kNMY54LSjslsCJYjzcvxLv9rowNfQ4hTjpOEU6V5+/1ewFgp7vbaR2NyIu01162kJkZl9HiBgPXNzU1EANooDQYctTSOcBUlloZzUoAEp4ocyLhvXSeG0r1gr8WRwjWwln45IcyF8GbTbGCrpzUk5fb1GM+yUKAH13fjiNZIYSTs8LbA4Z8osqlCYxuSAXCaFGWzT/khgW7Nldsqud1I/0LClmv5XJcHhLyN6+3MTcn2u1UUS72rwVF+zxjVPQXEm32B3XEn1sOUA3kpsjwT8XUPCoHQIDAQABAoIBAEMI8FzYfKUS5oJ6YfciTX3GjuRxtKN8wxq+WQnX0oiC1IeBmPfuve0vjb4bzv9iJiEKtVLkQVJHmz0DaoxblQeafDXAUDWKpSSQTaFcJ4UKtEmxSQezOPIb0vMoyQAi9EAicR9Ci5vgPkpkZ/b/WZACUv3dksC6ox/aRGZtg9Ki83hswXGNXVhOThziTJyBrTU3/S6r6/tBJN8Op2yBFssxku1x7cKt9NU2tq2wm1aE1Ucyg0KjOj4UqOI/5p3XnRIAYo1WWbtg2KVfGvRRvTwEJPsmx5JtynFYMHa7HK38hGQ+kx6jKSp+ReYZ2bU79aLDvi+7k0WaX6W3iB4FukECgYEA+oOKfxSO+pZOzFIUhOP+C8mfDJZMAJIflQGj6bIQFZpDrx4jKyZ1gw8WS6PxfOx+5witFPJRmBjjUKz5s7SXhrtgrwNYGwlCQsRMlk7n8XysUs460n7RTrkdWumoGA7q7/UMgM78NaLTHauXUQDnR1Zyn2F5ltnQ6S5otsycFjECgYEA7nwjLScco5DKO9WkZsZSrEhmKcDRQDnweNZLpD7YvKJMvoJ+6tgA8wlUXK94u9Xel5z+3YVZAEC/Ot2B59aMWiay/gjaD34eRpEkxzkbag8mNbxQA01TLNYXSuTPAS7OcWnCWrs41poSmH5bZfgdDTc3WdgCo85uQXsJbf/g+a0CgYAnrWXmMs9iiSP54JY3ZhT6sPhr+fIXtQ0jgJsBjJ5XjZNizRcs4m6CT4VsfY7mBl8jIJCpXKfnuyllwb/a4qONR2hMz+7IeXLxOD4ZIG5EJUb15owSgtuL6G3p1FA3X0V+tAVC8YWoyR0++cqBne8DsYF+FOY5Kk6YYC1dppKiQQKBgQDp/4PEWgwPsZG9A/M7pHyZ7q/PaBHpIvCAoEO39LJU5CrJAuHlK+xCi3TrPIEKTN8LhE/Oq3iYdu8rowxDnpA4JKCcAbN0YBr0wpWDpNTxS8wCDbO0ibqC084m/jrevj5xqYO54tnyN1rnpZOoibp3rSXyUbDFaVdliIPIGrl0QQKBgQCflBiB64LoAue0qS+xIHdqcBCZlJl5ByuvH3YUHjTuqaqGQUqZkqQ1QVNmIZdqO+4ynH4T1d33w5wCJ2ZWcxpDz37yCdcSVRVTgtWFgfSzpeGqBdTrQCBdPllvkMPGoQ4JJ0efxmhlWDAp2sbng1R/md5fj96+i7ZRgWXGxIz4sg==',
    'sp_spid_level' => 'https://www.spid.gov.it/SpidL1',
    'sp_contact_persons' => [
        'other' => [
            'Private' => true,
            'EmailAddress' => 'other@other.org',
            'VATNumber' => 'IT12345678901',
            'FiscalCode' => 'FNTCMS73M25L224C',
            // for public SP example:
            // 'Public' => true,
            // 'IPACode' => 'public administration code: mandatory for public SP'
        ],
        'billing' => [
            'Private' => true,
            'EmailAddress' => 'billing@other.org',
            'TelephoneNumber' => '+39329000001',
            'CessionarioCommittente' => [
                'DatiAnagrafici' => [
                    'IdFiscaleIVA' => [
                        'IdPaese' => 'IT',
                        'IdCodice' => '12345678901',
                    ],
                    'Anagrafica' => [
                        'Denominazione' => 'Società dei collaudi',
                    ],
                ],
                'Sede' => [
                    'Indirizzo' => 'via dei test',
                    'NumeroCivico' => '1756',
                    'CAP' => '20212',
                    'Comune' => 'Milano',
                    'Provincia' => 'MI',
                    'Nazione' => 'IT',
                ]
            ],
            'VATNumber' => 'IT12345678901',
        ]
    ],

    'hide_real_idps' => false,
    'expose_sp_metadata' => true,
    'expose_idps_json' => true,
    'only_sp_logout' => false,
    // 'test_idp' => false,
    'test_idp' => [
        'entityId' => 'spid-testenv',
        'sso_endpoint' => 'https://spid-testenv/sso',
        'slo_endpoint' => 'https://spid-testenv/slo',
        'x509cert' => 'spid-testenv-cert',
    ],

    // 'validator_idp' => false,
    'validator_idp' => [
        'entityId' => 'spid-validator',
        'sso_endpoint' => 'https://spid-validator/sso',
        'slo_endpoint' => 'https://spid-validator/slo',
        'x509cert' => 'spid-validator-cert',
    ],

    'middleware_group' => 'web',
    'routes_prefix' => 'spid',
    'login_view' => 'spid-auth::login-spid',
    'after_login_url' => '/',
    'after_logout_url' => '/',
];
