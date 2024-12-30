<?php
/**
 * This file contains the config settings for SPID Identity Providers.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

 // SPID IdP data
return [
  'infocert' => [
    'provider' => 'infocert',
    'title' => 'Infocert',
    'entityName' => 'Infocert ID',
    'logo' => 'spid-idp-infocertid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://identity.infocert.it',
    'singleSignOnService' => [
      'url' => 'https://identity.infocert.it/spid/samlsso',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://identity.infocert.it/spid/samlslo',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => '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',
  ],
  'poste' => [
    'provider' => 'poste',
    'title' => 'Poste',
    'entityName' => 'Poste ID',
    'logo' => 'spid-idp-posteid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://posteid.poste.it',
    'singleSignOnService' => [
      'url' => 'https://posteid.poste.it/jod-fs/ssoserviceredirect',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://posteid.poste.it/jod-fs/sloserviceredirect',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIFizCCA3OgAwIBAgIIFYLDd7sRdIowDQYJKoZIhvcNAQELBQAwZTELMAkGA1UEBhMCSVQxHjAcBgNVBAoMFVBvc3RlIEl0YWxpYW5lIFMucC5BLjEaMBgGA1UEYQwRVkFUSVQtMDExMTQ2MDEwMDYxGjAYBgNVBAMMEVBvc3RlIEl0YWxpYW5lIENBMB4XDTI0MDIwODEzNTQzOVoXDTI3MDIwODEzNTQzOVowRzELMAkGA1UEBhMCSVQxHjAcBgNVBAoMFVBvc3RlIEl0YWxpYW5lIFMucC5BLjEYMBYGA1UEAwwPaWRwLXBvc3RlaWQyMDI0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlAUwk/CYRRsQSTfczqFAZ9TF/6Jwnr82pNfx/hcHXqDn/MrIkhg4t9vWf8N+cPW/CfcvFuySdo+ecs07KCm2A8stUQ+zRH49/GK0v3rHmEs5/lPacwuXTdWPdKL1/f7APQBoebk5y13QVH1yO4RHW0UItKBtqF16eaI9ceiS7O4tn+M7QQyj5wftVHrlRNZlFhHoXeLpsum0WLesjSrceUHntbUtyXyQGmX6tT0tcmwJwdbK9H670uK/xioenaGoU3pfnd/6W0glHh8G1fztTPfrazMjHT2+Ii+aKX4hFeVIu19xaNbNGFL5RpIG1XEKGL/EoJXKCtnKVn/8PveztwIDAQABo4IBWzCCAVcwHwYDVR0jBBgwFoAUbNNNuRe4R3dHflC8gGhCz2e8P3kwPwYIKwYBBQUHAQEEMzAxMC8GCCsGAQUFBzABhiNodHRwOi8vcG9zdGVjZXJ0LnBvc3RlLml0L3BpLW9jc3BDQTArBgNVHREEJDAigSBpZHAtcG9zdGVpZDIwMjRAcG9zdGVpdGFsaWFuZS5pdDA+BgNVHSAENzA1MDMGCCtMMAEFAQEEMCcwJQYIKwYBBQUHAgEWGWh0dHA6Ly9wb3N0ZWNlcnQucG9zdGUuaXQwHQYDVR0lBBYwFAYIKwYBBQUHAwIGCCsGAQUFBwMEMDgGA1UdHwQxMC8wLaAroCmGJ2h0dHA6Ly9wb3N0ZWNlcnQucG9zdGUuaXQvcGktQ0EvY3JsLmNybDAdBgNVHQ4EFgQUm2Mfz8pVslcPa8X1rgKVQjJRczcwDgYDVR0PAQH/BAQDAgWgMA0GCSqGSIb3DQEBCwUAA4ICAQBORueMtFdRkTK8iIXHWZiLlOxgWI9vKC5xcuJJpkSGjnu4kb80nim3m5FPo9VhlDrl6vGsxbZbOK8E3qNLMh+uUYJOmryUULjh+rlth4meWLcQ+W6IWwbM7Of2yG4wEE8UIB8tgoNZmI4MQWxk5kZU2gzPjbYpGk+3gHvVODloQBrwG+C0yhQt3BBBtdB26W6ebESGUyBEBhOGuszzyBVGuWiHUBcyHsEH/qoS0Mh6AMpPbGjoFcgGQv6dQ5Rm1s+mt184kfl/k9kDHH8a9NgD5eVLenArTW8yqiX23ck0qepKDC0dn9wq9hNFD+UquT1fHrzZhxJhk93r6AY6yCHMN8sIHTY8zg8TNTGSE7PkeWmXxvjiw3o7vWS/79qbePrCfy/7ICvI/ys5/Z0pATWBMGXkEN+Mee4Ivf+YPDpxfcZLj3DQ/XJ5FscYM2uTOZAwYT+9LcLonpJUexazCWfTNsF//Knyo/ZhcslEhoPT5F1b8yO5uwXeM/PRRkaX1ACl2ApLKobHyG0qG8lYGMFuIAsHtsC+tJfZrX4F55kcTzVfR/cp7S50XyFhBqs4piM/ZsF4jSPXhVDCF9Nw/XC8VvVOWq8I5kVmuf8/32Nqk9bFaevGvv7ExcZKVsUbSfu8kB/t9eePoNtjqHAUxoxWs807k9vQE3d3DlYGa3M0Cw==',
  ],
  'tim' => [
    'provider' => 'tim',
    'title' => 'Tim',
    'entityName' => 'Tim ID',
    'logo' => 'spid-idp-timid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://login.id.tim.it/affwebservices/public/saml2sso',
    'singleSignOnService' => [
      'url' => 'https://login.id.tim.it/affwebservices/public/saml2sso',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://login.id.tim.it/affwebservices/public/saml2slo',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIID0jCCArqgAwIBAgIUXDUOKL3WuolxDw96Fk9es8rIt6kwDQYJKoZIhvcNAQELBQAwgYsxCzAJBgNVBAYTAklUMS4wLAYDVQQKDCVUZWxlY29tIEl0YWxpYSBUcnVzdCBUZWNobm9sb2dpZXMgc3JsMSgwJgYDVQQLDB9TZXJ2aXppIHBlciBsJ2lkZW50aXRhIGRpZ2l0YWxlMSIwIAYDVQQDDBlUSSBUcnVzdCBUZWNobm9sb2dpZXMgc3JsMB4XDTIxMTExMTE3MDMyMFoXDTI1MTExMDE3MDMyMFowgYsxCzAJBgNVBAYTAklUMS4wLAYDVQQKDCVUZWxlY29tIEl0YWxpYSBUcnVzdCBUZWNobm9sb2dpZXMgc3JsMSgwJgYDVQQLDB9TZXJ2aXppIHBlciBsJ2lkZW50aXRhIGRpZ2l0YWxlMSIwIAYDVQQDDBlUSSBUcnVzdCBUZWNobm9sb2dpZXMgc3JsMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6sIS3+3iZSaAIyVywahlbpua2uJ/XmpV68P1e1STJpHoaj32STdHhqZnnb4Y/FshP1NUolzNolPXAYDmDduW1OnGndJZ+G9Hjh1PCkdiRw+p0FjhQAsGJkn8NdgTIHLJjqN1qQwtOsVGab8ScyA3mtmj3xKYuBhUoweuATzC7f5r7FfIoc3cy6N5lgrpZpfeAChxLwoHVjoAVgIBuemi6HAzmd4/BI06KzOcR7+dBVi4+uiseldxrJ5bhnjZKIwgkX14y9UA84Y+e+rMtyT8cT3XXi9NazZl5Ej5/bQPqqVsbg6tXzQSfEJD6JEjuYeC0RUKMS/EJn3hL5VLzTJ1NwIDAQABoywwKjAdBgNVHQ4EFgQUfctFZ8bRtmEvXPRlqgVDuggY/ZwwCQYDVR0TBAIwADANBgkqhkiG9w0BAQsFAAOCAQEA0lszHadknPfE17IWGWsgvlXOdKMnWcl9H5rEYmsWwDB9FJG9XAZvPMcVv1kkWi6XZI/8N2Twhu1BdZkdvntDRscuck8wxxIpkRV7CwlcqNFZ/IwjDBxOBa8Q1J850p+qP8A9apsLLPUlu/oLygNDWIXzcOjMqnPkEP+XXUNYPto5iV+OyDzLLacCYqDDHcvDewWLmEjt35X967KcM+m7K2zGRLWfqcZPIjJJOkpNjgcs+MaisMrGDyOKiD16v0LpwVyIpTqXvDk7KHo8CUNXDxyLxZzB6WffgnOgjXTfU3vluweOx0qQy/VxIupDlNBKiZB4gnt1oAfnaMbqla9wcw==',
  ],
  'sielte' => [
    'provider' => 'sielte',
    'title' => 'Sielte',
    'entityName' => 'Sielte ID',
    'logo' => 'spid-idp-sielteid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://identity.sieltecloud.it',
    'singleSignOnService' => [
      'url' => 'https://identity.sieltecloud.it/simplesaml/saml2/idp/SSO.php',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://identity.sieltecloud.it/simplesaml/saml2/idp/SLS.php',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIDczCCAlugAwIBAgIJAMsX0iEKQM6xMA0GCSqGSIb3DQEBCwUAMFAxCzAJBgNVBAYTAklUMQ4wDAYDVQQIDAVJdGFseTEgMB4GA1UEBwwXU2FuIEdyZWdvcmlvIGRpIENhdGFuaWExDzANBgNVBAoMBlNpZWx0ZTAeFw0xNTEyMTQwODE0MTVaFw0yNTEyMTMwODE0MTVaMFAxCzAJBgNVBAYTAklUMQ4wDAYDVQQIDAVJdGFseTEgMB4GA1UEBwwXU2FuIEdyZWdvcmlvIGRpIENhdGFuaWExDzANBgNVBAoMBlNpZWx0ZTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBANIRlOjM/tS9V9jYjJreqZSctuYriLfPTDgX2XdhWEbMpMpwA9p0bsbLQoC1gP0piLO+qbCsIh9+boPfb4/dLIA7E+Vmm5/+evOtzvjfHG4oXjZK6jo08QwkVV8Bm1jkakJPVZ57QFbyDSr+uBbIMY7CjA2LdgnIIwKN/kSfFhrZUMJ6ZxwegM100X5psfNPSV9WUtgHsvqlIlvydPo2rMm21sg+2d3Vtg8DthNSYRLqgazCc0NTsigrH7niSbJCO0nq/svMX2rSFdh5GFK7/pxT+c3OFWqIR8r+RX4qW+auJqkbTuNRwxV22Sm6r69ZJwV0WspvsVJi+FYqiyoWhgUCAwEAAaNQME4wHQYDVR0OBBYEFCUx063GwUhEFDllwCBe/+jdeW+XMB8GA1UdIwQYMBaAFCUx063GwUhEFDllwCBe/+jdeW+XMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQELBQADggEBADF94c3JwyBM86QBLeoUZxRYKPniba8B39FfJk0pb+LejKfZMvspOrOFgYQQ9UrS8IFkBX9Xr7/tjRbr2cPwZNjrEZhoq+NfcE09bnaWTyEl1IEKK8TWOupJj9UNVpYXX0LfIRrMwNEzAPQykOaqPOnyHxOCPTY957xXSo3jXOyvugtvPHbd+iliAzUoPm1tgiTKWS+EkQ/e22eFv5NEyT+oHiKovrQ+voPWOIvJVMjiTyxRic8fEnI9zzV0SxWvFvty77wgcYbeEuFZa3iidhojUge8o1uY/JUyQjFxcvvfAgWSIZwdHiNyWaAgwzLPmPCPsvBdR3xrlcDg/9Bd3D0=',
  ],
  'aruba' => [
    'provider' => 'aruba',
    'title' => 'Aruba',
    'entityName' => 'Aruba ID',
    'logo' => 'spid-idp-arubaid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://loginspid.aruba.it',
    'singleSignOnService' => [
      'url' => 'https://loginspid.aruba.it/ServiceLoginWelcome',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://loginspid.aruba.it/ServiceLogoutRequest',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIExTCCA62gAwIBAgIQH32A70kY92tuXB8AGi2DdDANBgkqhkiG9w0BAQsFADBsMQswCQYDVQQGEwJJVDEYMBYGA1UECgwPQXJ1YmFQRUMgUy5wLkEuMSEwHwYDVQQLDBhDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eUIxIDAeBgNVBAMMF0FydWJhUEVDIFMucC5BLiBORyBDQSAyMB4XDTIwMDEyMjAwMDAwMFoXDTI1MDEyMTIzNTk1OVowgaAxCzAJBgNVBAYTAklUMRYwFAYDVQQKDA1BcnViYSBQRUMgc3BhMREwDwYDVQQLDAhQcm9kb3R0bzEWMBQGA1UEAwwNcGVjLml0IHBlYy5pdDEZMBcGA1UEBRMQWFhYWFhYMDBYMDBYMDAwWDEPMA0GA1UEKgwGcGVjLml0MQ8wDQYDVQQEDAZwZWMuaXQxETAPBgNVBC4TCDIwODc2Mzc5MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqt2oHJhcp03l73p+QYpEJ+f3jYYj0W0gos0RItZx/w4vpsiKBygaqDNVWSwfo1aPdVDIX13f62O+lBki29KTt+QWv5K6SGHDUXYPntRdEQlicIBh2Z0HfrM7fDl+xeJrMp1s4dsSQAuB5TJOlFZq7xCQuukytGWBTvjfcN/os5aEsEg+RbtZHJR26SbbUcIqWb27Swgj/9jwK+tvzLnP4w8FNvEOrNfR0XwTMNDFrwbOCuWgthv5jNBsVZaoqNwiA/MxYt+gTOMj/o5PWKk8Wpm6o/7/+lWAoxh0v8x9OkbIi+YaFpIxuCcUqsrJJk63x2gHCc2nr+yclYUhsKD/AwIDAQABo4IBLDCCASgwDgYDVR0PAQH/BAQDAgeAMB0GA1UdDgQWBBTKQ3+NPGcXFk8nX994vMTVpba1EzBHBgNVHSAEQDA+MDwGCysGAQQBgegtAQEBMC0wKwYIKwYBBQUHAgEWH2h0dHBzOi8vY2EuYXJ1YmFwZWMuaXQvY3BzLmh0bWwwWAYDVR0fBFEwTzBNoEugSYZHaHR0cDovL2NybC5hcnViYXBlYy5pdC9BcnViYVBFQ1NwQUNlcnRpZmljYXRpb25BdXRob3JpdHlCL0xhdGVzdENSTC5jcmwwHwYDVR0jBBgwFoAU8v9jQBwRQv3M3/FZ9m7omYcxR3kwMwYIKwYBBQUHAQEEJzAlMCMGCCsGAQUFBzABhhdodHRwOi8vb2NzcC5hcnViYXBlYy5pdDANBgkqhkiG9w0BAQsFAAOCAQEAZKpor1MrrYwPw+IuPZElQAuNzXsaSWSnn/QQwJtW49c4rFM4mEud9c61p9XxIIbgQKmDmNbzC+DmwJSZ8ILdCAyBHmY3BehVRAy3KRA2KQhS9kd4vywf5KVYd1L5hQa9DBrusxF7i1X/SEeLQgoKkov0R8v43UncqXS/ql50ovJFxi938Rv4rVwa8o0hqqc6WUcjkidB6M9aNJLIbOZN3xNUgC28qIr8y7N8lbxWbwVrGxqKDtpaA9J0hOOXxwuTfSd1zOtT0KSSSUQ53QGOPnxyjxYDQbJu60/lBPuUV5wb/Z2rgpeUH1/n7limHV5sVmOZgSnf18T+0STANCfkXg==',
  ],
  'namirial' => [
    'provider' => 'namirial',
    'title' => 'Namirial',
    'entityName' => 'Namirial ID',
    'logo' => 'spid-idp-namirialid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://idp.namirialtsp.com/idp',
    'singleSignOnService' => [
      'url' => 'https://idp.namirialtsp.com/idp/profile/SAML2/Redirect/SSO',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://idp.namirialtsp.com/idp/profile/SAML2/Redirect/SLO',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIDNzCCAh+gAwIBAgIUNGvDUjTpLSPlP4sEfO0+JARITnEwDQYJKoZIhvcNAQELBQAwHjEcMBoGA1UEAwwTaWRwLm5hbWlyaWFsdHNwLmNvbTAeFw0xNzAzMDgwOTE3NTZaFw0zNzAzMDgwOTE3NTZaMB4xHDAaBgNVBAMME2lkcC5uYW1pcmlhbHRzcC5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDrcJvYRh49nNijgzwL1OOwgzeMDUWcMSwoWdtMpx3kDhZwMFQ3ITDmNvlz21I0QKaP0BDg/UAjfCbDtLqUy6wHtI6NWVJoqIziw+dLfg7S5Sr2nOzJ/sKhzadWH1kDsetIenOLU2ex+7Vf/+4P7nIrS0c+xghi9/zN8dH6+09wWYnloGmcW3qWRFMKJjR3ctBmsmqCKWNIIq2QfeFszSSeG0xaNlLKBrj6TyPDxDqPAskq038W1fCuh7aejCk7XTTOxuuIwDGJiYsc8rfXSG9/auskAfCziGEm304/ojy5MRcNjekz4KgWxT9anMCipv0I2T7tCAivc1z9QCsEPk5pAgMBAAGjbTBrMB0GA1UdDgQWBBQi8+cnv0Nw0lbuICzxlSHsvBw5SzBKBgNVHREEQzBBghNpZHAubmFtaXJpYWx0c3AuY29thipodHRwczovL2lkcC5uYW1pcmlhbHRzcC5jb20vaWRwL3NoaWJib2xldGgwDQYJKoZIhvcNAQELBQADggEBAEp953KMWY7wJbJqnPTmDkXaZJVoubcjW86IY494RgVBeZ4XzAGOifa3ScDK6a0OWfIlRTbaKKu9lEVw9zs54vLp9oQI4JulomSaL805Glml4bYqtcLoh5qTnKaWp5qvzBgcQ7i2GcDC9F+qrsJYreCA7rbHXzF0hu5yIfz0BrrCRWvuWiop92WeKvtucI4oBGfoHhYOZsLuoTT3hZiEFJT60xS5Y2SNdz+Eia9Dgt0cvAzoOVk93Cxg+XBdyyEEiZn/zvhjus29KyFrzh3XYznh+4jq3ymt7Os4JKmY0aJm7yNxw+LyPjkdaB0icfo3+hD7PiuUjC3Y67LUWQ8YgOc=',
  ],
  'spiditalia' => [
    'provider' => 'spiditalia',
    'title' => 'SPIDItalia Register.it',
    'entityName' => 'SPIDItalia Register.it',
    'logo' => 'spid-idp-spiditalia.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://spid.register.it',
    'singleSignOnService' => [
      'url' => 'https://spid.register.it/login/sso',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://spid.register.it/login/singleLogout',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIDazCCAlOgAwIBAgIED8R+MDANBgkqhkiG9w0BAQsFADBmMQswCQYDVQQGEwJJVDELMAkGA1UECBMCRkkxETAPBgNVBAcTCGZsb3JlbmNlMREwDwYDVQQKEwhyZWdpc3RlcjERMA8GA1UECxMIcmVnaXN0ZXIxETAPBgNVBAMTCHJlZ2lzdGVyMB4XDTE3MDcxMDEwMzM0OVoXDTI3MDcwODEwMzM0OVowZjELMAkGA1UEBhMCSVQxCzAJBgNVBAgTAkZJMREwDwYDVQQHEwhmbG9yZW5jZTERMA8GA1UEChMIcmVnaXN0ZXIxETAPBgNVBAsTCHJlZ2lzdGVyMREwDwYDVQQDEwhyZWdpc3RlcjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBANkYXHbm3q6xt3wrLAXnytswtj2JE1MM8aYmNXkTgDMCwO/+ahQOoQru6IBTbjfWH9jr+Woy54FDdX6bHl+5/mO6l/yAB/bKgwe5HmUjZJ5oakJjWucsSm+VkEwN2HquBZoN+mktju00xvLX5VAjmDHvZc/b8NhNr/FRKlYITboygkhGiUwGI3wLf3IaB76J0o7ugpW2WNLcywpX+p1VWZAMCdHBveBe/e42hh6WnWPqdwYUWHOgJ8HX4IzCHifiS1n6eUMgtoTQOmSvTQDwSjD0WWJE8tWSYt+txXg1t+3A3tbZOFu7T442wE7DtMdUL4+8gimQS+e8PxDK1uTqIPUCAwEAAaMhMB8wHQYDVR0OBBYEFMCgo1gzCIcUThQIs5g5ikfv1D7eMA0GCSqGSIb3DQEBCwUAA4IBAQBnGw3i3hQ37L8vyelkyZMeO3tLK65Cqti4oVrQZxClGV5zNA6fIMDY8Mci1UhLwjzp29POd/sez0vuHZ/Vmmygzoye4jTKr6c3jAh0u81FTzefBU+vIietm9RuV3sd7D9xq6EqOY1NDL+rkvBcTFtiwLEUm2kHYu/U67jk73pxOtmqxQvQeMU8oi42tehMZGLIGp3U5lGS8YGGl+GtkkQ2Z5/PSm67HGP81kTArG/QX+bX+ykypTJVg9hfb9zOFQidp1HkCRIez6YhDiP/ZLurd6Grt/wVfZPNBO8EOgy25AkRZlp+UD686BFg7qq5KKEbz3qmPrj8deHL3duacZcp',
  ],
  'intesa' => [
    'provider' => 'intesa',
    'title' => 'Intesa ID',
    'entityName' => 'Intesa ID',
    'logo' => 'spid-idp-intesaid.svg',
    'isActive' => false,
    'real' => true,
    'entityId' => 'https://spid.intesa.it',
    'singleSignOnService' => [
      'url' => 'https://spid.intesa.it/Time4UserServices/services/idp/AuthnRequest/',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://spid.intesa.it/Time4UserServices/services/idp/SingleLogout',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIEDjCCAvagAwIBAgIIIT1A+ywbIQAwDQYJKoZIhvcNAQELBQAwXjEzMDEGA1UEAwwqSU4uVEUuUy5BLiBTLnAuQSAtIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MRowGAYDVQQKDBFJTi5URS5TLkEuIFMucC5BLjELMAkGA1UEBhMCSVQwHhcNMTcwOTE1MTMyMzQ1WhcNMzYwNzAxMTk1OTAwWjBQMSUwDwYDVQQuEwgyMDA3OTc5NzASBgNVBAMMC1NBTUwgU2lnbmVyMRowGAYDVQQKDBFJTi5URS5TLkEuIFMucC5BLjELMAkGA1UEBhMCSVQwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDhYXkP+eQBURgmslDXBjG0ad+DkSAkWt7hUoaTyiK0e34QiyArq043plqTrt+6FzTGeX7960Qr3tCLGCiVOi47QuE09IKfJmKGEaUQnJQehHYZs/XV0OYQl18WrCxUX6ALOcqPs+4ypCbJV1WzSosfBcPBzivJER8kvrynMXI3or18e9XPTGBn8qNFyNF1E3BJ5UhrDvk5W2gKyYKz0M/CIu9PiHuO/ne6HbeNrCS/xzXtjsTusk41AOxIQoFbEzS08xcRY+QDE8oLcAmecSjT3xv3r9dWke6KTTAahS3K+5mOYRcBXj2FFegiUp+xh4OAWdH1+gGDYm+3aAmMpaLtAgMBAAGjgd0wgdowHQYDVR0OBBYEFEw9xWg4qvQGdlGMCqmJcVDgdE8aMAwGA1UdEwEB/wQCMAAwHwYDVR0jBBgwFoAUySnWJ2sw0ljDpJVrtrxCCP0b1CYwGgYDVR0QBBMwEYAPMjAxNzA5MTUxMzIzNDVaMD8GA1UdHwQ4MDYwNKAyoDCGLmh0dHA6Ly9lLXRydXN0Y29tLmludGVzYS5pdC9DUkwvSU5URVNBX25DQS5jcmwwDgYDVR0PAQH/BAQDAgSwMB0GA1UdJQQWMBQGCCsGAQUFBwMCBggrBgEFBQcDBDANBgkqhkiG9w0BAQsFAAOCAQEAVRHyFRZZFpW/qjJpKftd86h3wOdUqOhc2W8ZHv0st8ptG+mZk3l1iWAsEPqKMIBhksgTvalnHC1lHUt11xsZ2mzUjVpiG8XiWXYXQnY2D+q7Dc4n20kJ717qf4SDN8wX1A6XvT3Wrsfh87vg3ZFD56/eyur2snWu4OilsFqAyLhnExG4puJ4JKBWnlwAGXD9SFgkSZ8FC66KQs6CAwVkvCIom3IwJeU/VrYQF6XHkVCQgr5mojXgCkrlRNl53WAKfQHCT4QH+oQVP97PCEL/wQ1zi0UzWauKT6u2wDym9rcpch+WLa0GUtYNhuoLU2SregPKwTWg2DfINJObyWRpww==',
  ],
  'lepida' => [
    'provider' => 'lepida',
    'title' => 'Lepida ID',
    'entityName' => 'Lepida ID',
    'logo' => 'spid-idp-lepidaid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://id.lepida.it/idp/shibboleth',
    'singleSignOnService' => [
      'url' => 'https://id.lepida.it/idp/profile/SAML2/Redirect/SSO',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://id.lepida.it/idp/profile/SAML2/Redirect/SLO',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIDHDCCAgSgAwIBAgIVALisbudTRxLy3vlMcEDfaqr3iW89MA0GCSqGSIb3DQEBCwUAMBcxFTATBgNVBAMMDGlkLmxlcGlkYS5pdDAeFw0xODA4MDgxMDIzMTJaFw0zODA4MDgxMDIzMTJaMBcxFTATBgNVBAMMDGlkLmxlcGlkYS5pdDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMOFERgxPEYPqAjN7oW6y8oSSY6tGm2OCIU+VyKhb2OqfNLpF8tPrytX17pgwVYHzjxRCNMTC83frbmtBapABtm9KuX7qaSPvaJx0+UqYk9FdKCKQOEkmWcNOJfwzNMP65B+cDxP3sa1JoAMeAO0x95bnYoX0ZHcssKkwpgMb8/JHZHzqu3odxADtO5PaT3xaCyMIcqIp1O2nVn7SizUE1gNucLAdaP4kh0o7nU61pz4pG3gQXK+uROteDD8cTU2Nxi7W1T73tQSuwst54BS2p9IBXzWrA9V0Ck10oiQTcIC8X9McepCrNzgCOBdap00Tifusb30t74BREARgwjp1N8CAwEAAaNfMF0wHQYDVR0OBBYEFL32/n7uf1Re14pW+gwGxZQHUZBCMDwGA1UdEQQ1MDOCDGlkLmxlcGlkYS5pdIYjaHR0cHM6Ly9pZC5sZXBpZGEuaXQvaWRwL3NoaWJib2xldGgwDQYJKoZIhvcNAQELBQADggEBAK80B1mEWKOTJkVJOJot2xU79Lhs1+domUSYQiA+tlS46IAfWwDZqI1llIjgL85n7qMsKFvYTIskInoG51Iezv2dTxlB6IMI8NPRfiFXo2s8NYjbzWyETbdXzCbDR0tKNke0TFE0oxunNfE5YRsmH4bPnjhPUjCSHX7wIhlNrLae3FjMQp1OLDs7HmJo3AhuAVmHCoG7QV/ly4ZHcVYx4F7HUsFg5uxNYjZbo+XMutJz4nZFOFE+uRzTwwfdR2sxny+ppkruTwIhEXyzknoiw1mGIEWZc6scnOAiwZeqTccUYVNHp+PSFs9SD8l+2PO4Oh8Y3dYT+5ojv+S6T7vy5xE=',
  ],
  'eht' => [
    'provider' => 'eht',
    'title' => 'EtnaID',
    'entityName' => 'Etna ID',
    'logo' => 'spid-idp-etnaid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://id.eht.eu',
    'singleSignOnService' => [
      'url' => 'https://id.eht.eu/SSO',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://id.eht.eu/SLS',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => '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',
  ],
  'infocamere' => [
    'provider' => 'infocamere',
    'title' => 'ID InfoCamere',
    'entityName' => 'ID InfoCamere',
    'logo' => 'spid-idp-infocamereid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://loginspid.infocamere.it',
    'singleSignOnService' => [
      'url' => 'https://loginspid.infocamere.it/ServiceLoginWelcome',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://loginspid.infocamere.it/ServiceLogoutRequest',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIIRDCCBiygAwIBAgIINO3vGmIYBP0wDQYJKoZIhvcNAQENBQAwgfsxCzAJBgNVBAYTAklUMQ0wCwYDVQQHDARSb21lMSYwJAYDVQQKDB1BZ2VuemlhIHBlciBsJ0l0YWxpYSBEaWdpdGFsZTEwMC4GA1UECwwnU2Vydml6aW8gQWNjcmVkaXRhbWVudG8gZSBwcm9nZXR0byBTUElEMTwwOgYDVQQDDDNQcm9nZXR0byBTUElEIC0gR2VzdG9yaSBkaSBJZGVudGl0w6AgRGlnaXRhbGUgKElkUCkxKTAnBgkqhkiG9w0BCQEWGnByb3RvY29sbG9AcGVjLmFnaWQuZ292Lml0MRowGAYDVQQFExFWQVRJVC05NzczNTAyMDU4NDAeFw0yMzAyMjIwMDAwMDBaFw0zMzAyMjEyMzU5NTlaMIG3MQswCQYDVQQGEwJJVDEOMAwGA1UECAwFSXRhbHkxDzANBgNVBAcMBlBhZG92YTEcMBoGA1UECgwTSW5mb0NhbWVyZSBTLkMucC5BLjElMCMGA1UEAwwcSW5mb0NhbWVyZSBJZGVudGl0eSBQcm92aWRlcjEaMBgGA1UEYQwRVkFUSVQtMDIzMTM4MjEwMDcxJjAkBgNVBFMMHWh0dHBzOi8vaWRzcGlkLmluZm9jYW1lcmUuaXQvMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAo8Div4aLGUtoDoP5RWbRwqvEtjnDcCCUS+SzChAsJP+UYjWl+R4R4Y7Lz+WId3LJqey+QIyvviD6vH/QloqzVRG/JabW70NZylk1UX2isss8mRvtceK7nYVxjTIoQpasg0OsCevgljjnFxRm8c3zUpYfjC5zzr/jZ9HjFKghGCZGjBavNNgiGIo7e7jbdmGH5N9z+uQ8KRG/p2JRxD0YeVy2+EV2o0cQO2duE383EganLKPcQ9AnxkLE1K0cpP7XQDtUgWTPqsL9+OLTl13KhVM2TMK7EkAm00WCOl1aX3E7g9Qgw+4fUm308v77OSDe77dY8hohZWPRTwjemaHA2QIDAQABo4IDDDCCAwgwDAYDVR0TAQH/BAIwADAdBgNVHQ4EFgQUddqeUDWjVXqV3PSfTyzAmlFDTDMwHwYDVR0jBBgwFoAUyF8jl8Jbn9TohwSTF77f5QNJd18wDgYDVR0PAQH/BAQDAgbAMBEGA1UdEQQKMAiCBmlkcC5pdDAWBgNVHRIEDzANggtzcGlkLmdvdi5pdDA/BgNVHR8EODA2MDSgMqAwhi5odHRwczovL2VpZGFzLmFnaWQuZ292Lml0L2NybC9jcmxfU1BJRF9JZFAuY3JsMGoGCCsGAQUFBwEBBF4wXDBEBggrBgEFBQcwAoY4aHR0cDovL2VpZGFzLmFnaWQuZ292Lml0L2NlcnRpZmljYXRpL1N1Yl9DQV9TUElEX0lkUC5jZXIwFAYIKwYBBQUHMAGGCGh0dHBzOi8vMIIBzgYDVR0gBIIBxTCCAcEwCQYHBACORgEGAjCBlQYEK0wQBjCBjDBEBggrBgEFBQcCAjA4GjZFbGVjdHJvbmljIGNlcnRpZmljYXRlIGNvbmZvcm1pbmcgd2l0aCBBR0lEIEd1aWRlbGluZXMwRAYIKwYBBQUHAgIwOBo2Q2VydGlmaWNhdG8gZWxldHRyb25pY28gY29uZm9ybWUgYWxsZSBMaW5lZSBndWlkYSBBZ0lEMHIGBitMEAQBAjBoMDkGCCsGAQUFBwICMC0aK1NQSUQ6IGdlc3RvcmUgZGVsbGUgaWRlbnRpdOAgZGlnaXRhbGkgKElkUCkwKwYIKwYBBQUHAgIwHxodU1BJRDogSWRlbnRpdHkgUHJvdmlkZXIgKElkUCkwCAYGBACPegEDME0GBCtMEAQwRTBDBggrBgEFBQcCARY3aHR0cHM6Ly9laWRhcy5hZ2lkLmdvdi5pdC9jcHMvQWdJRF9lSURBU19yb290Q0FfY3BzLnBkZjBPBgYEAI5GAQUwRTBDBggrBgEFBQcCARY3aHR0cHM6Ly9laWRhcy5hZ2lkLmdvdi5pdC9jcHMvQWdJRF9lSURBU19yb290Q0FfY3BzLnBkZjANBgkqhkiG9w0BAQ0FAAOCAgEAoYZlSArAwFZDknzUG5Z3NQQUT3JKaOTT8TrNi/F8yL4mz0qjaJaJURMQauKZeNQiGlGvNyGp3SlgGYFHasZ9FrtpxbxGXVkNreer61kFhY/I3ZdU4DjGW2qPs9csP+W06R4k3OFFhua7DFyyoxAWQYIFisucT3E3+N32XuLQPDqjMwnvSdT4FLE6c4QIpJl3fQYlCsyhAxrNWlrndP1Q1f97oF6oB7tWR5Ae1/ixDN0q5QJeEnapNaDjvS2wEzVNRYW/RzbHPPZQ1Zs0jLEfXsuwD3A0iJiyD0GSgXYUibqH3VExCqQ1yjEDwjq3zF8bcSaoAQm2fRY3KIYSbI18kpPhFmNTJWbv303dQe6MzIORLUzs0tSHfB+mtclrHgqqaKwZZmHiGUYTV3bziWjMDacG9gRJtyS04LYZdkSBcSOn3dYXSM18F58pbKifcdajFmUicUWlI/2TFArDguh5TUekLQKsTi4tMnmk5RWA4oMLjZ+q2r4jMNVuoZ0+FGFbrfdhz+Kyo3gWdyZyY+Uqr1aiL+QTnht8hVTVrgOf4RJW/3z5hgYLSyx3INT6GDtaSr5V+orYfSpbvU1Xlinz+iP4vfYKmpFdF1cxjTYkNQB7/DW9nXYC4PwXjI5253rha8g/BLdsIEWD73Q1GM1HieSVX+tNPBbjHpKLz2UVZEM=',
  ],
  'teamsystem' => [
    'provider' => 'teamsystem',
    'title' => 'TeamSystem ID',
    'entityName' => 'TeamSystem ID',
    'logo' => 'spid-idp-teamsystemid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://spid.teamsystem.com/idp',
    'singleSignOnService' => [
      'url' => 'https://spid.teamsystem.com/idp/sso/redirect',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://spid.teamsystem.com/idp/logout/redirect',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => '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',
  ],
  'intesigroup' => [
    'provider' => 'intesigroup',
    'title' => 'Intesi Group SPID',
    'entityName' => 'Intesi Group SPID',
    'logo' => 'spid-idp-intesigroupspid.svg',
    'isActive' => true,
    'real' => true,
    'entityId' => 'https://idp.intesigroup.com',
    'singleSignOnService' => [
      'url' => 'https://spid.intesigroup.com/saml/public/income',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://spid.intesigroup.com/saml/public/logout',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => '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',
  ],
  'test' => [
    'provider' => 'test',
    'title' => 'Test IdP',
    'entityName' => 'Test IdP',
    'logo' => 'spid-idp-test.svg',
    'isActive' => true,
    'real' => false,
    'entityId' => 'spid-testenv',
    'singleSignOnService' => [
      'url' => 'https://spid-testenv/sso',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://spid-testenv/slo',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'spid-testenv-cert',
  ],
  'validator' => [
    'provider' => 'validator',
    'title' => 'SPID Validator',
    'entityName' => 'SPID Validator',
    'logo' => 'spid-validator.svg',
    'isActive' => true,
    'real' => false,
    'entityId' => 'spid-validator',
    'singleSignOnService' => [
      'url' => 'https://spid-validator/sso',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
      'url' => 'https://spid-validator/slo',
      'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'spid-validator-cert',
  ],
  'empty' => [
    'provider' => 'empty',
    'entityId' => 'empty',
    'real' => false,
    'singleSignOnService' => [
      'url' => 'https://empty',
    ],
    'x509cert' => 'empty',
  ],
];
