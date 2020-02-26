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
    'sp_certificate' => 'MIIDHjCCAgYCCQCNcAzVXDXSVDANBgkqhkiG9w0BAQsFADBRMQswCQYDVQQGEwJJVDELMAkGA1UECAwCUk0xDTALBgNVBAcMBFJvbWExJjAkBgNVBAoMHUFnZW56aWEgcGVyIGwnSXRhbGlhIERpZ2l0YWxlMB4XDTE5MTAxMDEwMDMyM1oXDTIwMTAwOTEwMDMyM1owUTELMAkGA1UEBhMCSVQxCzAJBgNVBAgMAlJNMQ0wCwYDVQQHDARSb21hMSYwJAYDVQQKDB1BZ2VuemlhIHBlciBsJ0l0YWxpYSBEaWdpdGFsZTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALKWS64M27P2Ra+ia7qoHUbXTgoVXyTfPjHNzbEU2TkVNP9jOpN/ew0NIoAjFNZP4kFnygmHR53GhFI3q2cQhReP8xT2DC7zIaJcZxhzR4UfEYR7FGkBJ9GpdNX0qtDKpgfvPJLThw4zqykK2ZrYPntvUzXe6iKepyZSiV/r7mcnC/BUU8ZlDWWBvPWAt8xJu6Bt6DkAbI9bnLbFLcETTUm//a7F67tGYXfs2GcvUCvkjvxE5krBvWt+QWign74VmDPTgCGHJFvYR4OraCBjGCOxk7RdlwAs/e+ErJL94tzosm0+gkmMmCJSU+t8I0XCnQfR6MswY339nJfIstZ5++UCAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAdfDu5i3VMFDQGU67xwQ4OSIJtvzxKkstMr5KifWThYRLZUZiDUDa4pRhLfZX1jhXemnJF1IyCOUaSE3B0nL5LaTTfs1HeGiSxU8GhOG1eLnbemnMm7i7NB6UEmmx0teXL9UT2re1psjPCbF6MinpVFKdy4OGj/DhG+gOZNFKN8EWf2nAxGk29DtVXIsaabXVzBTBPTqyWF4tkgfHy0uSd0UgBrG983NdPtTVWzm2sUf4sZJqelhuksokkCJ6s13/0MdTumXd9E3OEgFUKU/UvriKHMgCVhqBxCQbaV2McNbEEtS1TJeraZTOxMt4K/M1EKZuCszdN4lkgNShLc07xg==',
    'sp_private_key' => 'MIIEpQIBAAKCAQEAspZLrgzbs/ZFr6JruqgdRtdOChVfJN8+Mc3NsRTZORU0/2M6k397DQ0igCMU1k/iQWfKCYdHncaEUjerZxCFF4/zFPYMLvMholxnGHNHhR8RhHsUaQEn0al01fSq0MqmB+88ktOHDjOrKQrZmtg+e29TNd7qIp6nJlKJX+vuZycL8FRTxmUNZYG89YC3zEm7oG3oOQBsj1uctsUtwRNNSb/9rsXru0Zhd+zYZy9QK+SO/ETmSsG9a35BaKCfvhWYM9OAIYckW9hHg6toIGMYI7GTtF2XACz974Sskv3i3OiybT6CSYyYIlJT63wjRcKdB9HoyzBjff2cl8iy1nn75QIDAQABAoIBAQCAFREELI6qHFfQZqHzBj2xIBwFWVyamk4F9D+w4G4G5bHT9Lv/K2/6ZVA4LHD2X+a62BBsEw0HGGiweuLAkXWS95f0kZ8dURzvUGxUeeFylVy/8nIp+T0wvuNfzBFym9TGXgvIllMHmYEJd5Gn3624Y5h/S3gLClSBRLLoC9M5QPFTDSOPDxibg/8R8WD/GCgib3u+U+l0wiwuFWA6xTJFqtp1UuuuXd7tUJZpQ4u0DL3YVyUpb1us616fofaZdRDxAZGo2CU3WPTeVxN8DdXfJ7AKCp9sHzQt1BGTNRWA4Qx4XVJ/2NtZF2n+wkQe3xKixEUvroka7fvAVGbISvzFAoGBAObEuquPwfGzb2FhSwO1EtrwmxQn4svc4yiU3YGuCPlw+S0MiBzzuYMrzDvc/1GXimndCUj112NWUNoG3sO6yACnnI+Enhbup7zN2MJcWkMUIMMa6wZDrBVOIlllQDqBwxZ1q0Tq6WhoXfqBs78ITNot/1l7vpwkVjDCa862PwNvAoGBAMYc/uiunn3MouG/VRjiwNMLr4YA9qHahTcMinnfoP4kW0Hw4uJtwymaxX/HIM9VanUt2ITYwU0ijbobr3L5wdxhM30YwMfutJyk0b0pvW6O91wYe4yst18mkgtriGX1XEYCQFhnrTe+bWV55yMynA7RqKTjtD43eLVrO3cv/PvrAoGBAOREt43bDvKCFuvRrL1HERanKA+BANO9dKoAzzdmqpPrj19jZQSWhevdmBGjIp9X8l+TeIrGO7+Uczen0hT0nsAfDDve5+4xNpUJXZ+scNCniVzVNKQmroMIneulvyngj9SYosjyQd24VzXjtKSDllm/ZDXktQCI5QUoXDHGjoBbAoGBAK16voyqPAcUppzfmnjMNuWXmf/R4CYi3/wwcyH9fVCOLYs0kLiOPRO1f7RAfq6PsUUi/DJ4S+xrkYeirqQTqHp+c0Pb5RQuJuH0QeVPI54oetqeZbHtnDUp5UEyzKHblQ2I8yd2wevKSKlPceMUcitX1EFvU2oX0Q+r8sqPrww5AoGAIS36V9MGV+Ln1Q0s0Jn57PYWsDmuTgME6QNFhM8kAHTMTblv+dS09uOE6jw+MtYhQMkpS2KEKFb9IV9b2nKNZg8MoupJ0l2YWcYiDwWrecNXMFgO4CSR+JnInbJ6l9thzOJE7X4+/s+35WRJEmgfeK1Eq9IhSdnf6iFI2r1i8jY=',
    'sp_spid_level' => 'https://www.spid.gov.it/SpidL1',

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
