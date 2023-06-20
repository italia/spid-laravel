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
    'sp_certificate' => 'MIIDgzCCAmugAwIBAgIULAp1YQlWoEPUrugRqWqbUf3ES3wwDQYJKoZIhvcNAQELBQAwUTELMAkGA1UEBhMCSVQxCzAJBgNVBAgMAlJNMQ0wCwYDVQQHDARSb21lMSYwJAYDVQQKDB1BZ2VuemlhIHBlciBsJ0l0YWxpYSBEaWdpdGFsZTAeFw0yMzA1MjkxMDUzNDNaFw0zMDEyMzExMDUzNDNaMFExCzAJBgNVBAYTAklUMQswCQYDVQQIDAJSTTENMAsGA1UEBwwEUm9tZTEmMCQGA1UECgwdQWdlbnppYSBwZXIgbCdJdGFsaWEgRGlnaXRhbGUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCri1uwE3WfwGiTpkovorKlrcsAI7YrP55QqS3WLZbe+byYx219SsW0Ompe7rzChfmsNUsien3c4BGJN0zG1a2xJpfLJRhi9npJ228Aa+KAhPtMJ/uP2axhg6GxP6GG1LgoQavEF6gLDT/isQNCrEMKr8FNeVNPM5k+6Q03pqvZPvyNuLGkDGfDMHEg0RdHhPb/cJsa9dCULkkNi5y/SY68X58JOWdEpWYBjn83ES1G1hiPxr/DluJTebjourL5CdtiOHNoi3UoGJkBSTtrKtOg6Dq4nHwOU8qyHxQTYjxy8hp/cS3MNnXOgZIqc6mqiGicvLEhwDZBQHbXfjXa7N+NAgMBAAGjUzBRMB0GA1UdDgQWBBQsxX06P7VqgQ7FluV4X1Ep9C3P/zAfBgNVHSMEGDAWgBQsxX06P7VqgQ7FluV4X1Ep9C3P/zAPBgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAj4EwJeS52rzJXwCQvaQzP9LAx2gf9RPciztM32u88t3urucgAfUq+7aIDi8nKkpPfKgUFFL397jjKy1bq/i8o5GiAVe4RwOjGw3zma9L8WUY79BPId+sOuAhAtaEqgO2gu8cuXs4u7rAnDXbFJTPClUD6eum2JD+gHd61Yyvu3dJx/6bUNNcpg//XkwUEA9KoldQ7xju5oVLJ2aPv4TCeYXrhK5lunh3l/l3JhjyEhi3zIJYIAWiEVX/qIghYWM0yJ31j2GkZYUm1Z0GVv1VsUPxUModDAZH7zudE58Uyi8TyjVp6nDCqy+dgn7ugnSLTxBv/J9nOtSrxH1cPJES5',
    'sp_private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCri1uwE3WfwGiTpkovorKlrcsAI7YrP55QqS3WLZbe+byYx219SsW0Ompe7rzChfmsNUsien3c4BGJN0zG1a2xJpfLJRhi9npJ228Aa+KAhPtMJ/uP2axhg6GxP6GG1LgoQavEF6gLDT/isQNCrEMKr8FNeVNPM5k+6Q03pqvZPvyNuLGkDGfDMHEg0RdHhPb/cJsa9dCULkkNi5y/SY68X58JOWdEpWYBjn83ES1G1hiPxr/DluJTebjourL5CdtiOHNoi3UoGJkBSTtrKtOg6Dq4nHwOU8qyHxQTYjxy8hp/cS3MNnXOgZIqc6mqiGicvLEhwDZBQHbXfjXa7N+NAgMBAAECggEAAj61pCKXuc/RI+Aw569aZSlWwcQLNe+u3mFdvzLONFF9CAbRcP/8gbT4qrthxFiagYgaWUjUbYKP2DAyv1tmhYNQHebOCzrXM4Y9TU/GzBBxdXgEe8fJJhGMmSmQWe0IvUYhaFSNl29vSZkwG2buYwJqe4gTRghn1gHuHASrX7waHnfI/Us/NOU9hPBE0DlIwjyScDFg1KsksXu5KXdjoSW283P0LCjSOZ1Yc7qd+S6vMMOKJOl2ojqO9ZuEodiBirhMALL8PdhkgkfNahjUoi2EA3k108OnR/E9bLQpiUHt8ktXjYq+nqI03wEkIimm4ZCEh8IjesBSykaZn53OhQKBgQDCOYtcX95J5+RHJfBrejIPSafyt40dAEEv8L5aU05BJIUrSYSizz17JS7I/g0bxTkj39afkIgzn+D7k+TCesfLRSUfLM422HD7i/KdV/JNPEGwzoxLsJVBOdLlonddIGwVxdZDbZeb+3yCPtlANqRHBkAcZEVZwlEwY0B4KDx7SwKBgQDiGxiAbGesls+r91vrbmBxDm81pDwe6zDYc+8xjPLqu/d5qyOd65uKOIjh+mm9yqUaQxJhIlwgHjKaD4tYp7TDoljb8gRmW82EL0z1QzQgznfrBkDAiqwEd3IUMPdWStKrA3GSQHv6vmByW25+WvtmX6U+/iaeJT/NyX2/BruxhwKBgDGdzbItc3Xh3XknggGS19L7+AGTTmNIO17p21brzSPrFnH2eAG3e3dICmmiLOsZOP2nIwbkP0cg4rKiF2BvSTbuVQtfwNZV+JbqK3Jykx2T9Rwbx89WLurYwL2VAXUy4Sm2voO/LWnVlvsW3xz+WH0kZaBbJTRG5cK8uC156el9AoGBAJbwi6y/sX4Pt1cPbDeAmbUfc7IVj2T8kMEOs/EaLNfy30RDsSUtMcpV71sb9hcS/qDHga+CZomyXwllsxpd6lhnOnap27IROuMxSGi6kkQMURV+OR/P8wGQU4GTJqpejMBJLjn5/knnaS8uRlbnmcg/tWqV82XDbBKOnfk2H3plAoGAASfqKiFCSvMnHKSRI4LZZXXWdHvx2jDOPBy4Gobclm5Y2Fw/OMjFDsXJSYGreITKSeLaXL/c8MXi1KbxDm7L8Ml78f/4nFPoT7fsnKSjK4qs5pd8iT3wRtijldiroPoEyrZZvZB7kMzHdCsoiYM0ba0Xc14AggrCsfKPXMxMhFs=',
    'sp_spid_level' => 'https://www.spid.gov.it/SpidL1',
    'sp_contact_persons' => [
        'other' => [
            'Private' => true,
            'EmailAddress' => 'other@other.org',
            'VATNumber' => '12345678901',
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
            'VATNumber' => '12345678901',
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
