# Changelog

## [Unreleased]

- Security: require onelogin/php-saml ^4.3.1 (CVE-2025-66475, GHSA-5j8p-438x-rgg5) and robrichards/xmlseclibs ^3.1.5
- Port the SPID patch to php-saml 4.3 (`patches/php-saml-4.3-spid.patch`) and serve it from a raw URL that resolves
- Add Laravel 13 compatibility (Testbench 11, PHPUnit 12)
- CircleCI: apply the SPID patch during tests, add PHP 8.4/8.5 and Laravel 13
- Document SPID Validator and SPID Demo setup (#107)
- Add SPID strict-compliance tests for the AuthnRequest (#107)

## [v2.1.0-beta] - 2025-12-18

- Add SPID IdP certificate sync script and update config
- Add Laravel 12 compatibility
- Update Illuminate dependencies to support Laravel 9, 10, 11, and 12
- Update development dependencies for Laravel 12 testing
- Fix Carbon dependency conflict for Laravel 12 (support Carbon ^2.66|^3.0)
- Add multi-version testing scripts and CircleCI matrix (Laravel 9-12)

## [v2.0.4-beta] - 2025-01-25

- Update IdP

## [v2.0.3-beta] - 2025-01-10

- Update IdP
- Update dependencies

## [v2.0.2-beta] - 2024-07-30

- Use full namespace in routes definition

## [v2.0.1-beta] - 2024-02-27

- Update IdP

## [v1.4.1-beta] - 2024-02-27

- Update IdP

## [v2.0.0-beta] - 2023-06-21

- Support Laravel 9/10
- Require PHP 8.2 (breaking change)
- Fix metadata signature (thanks to [@madbob](https://github.com/madbob))
- Update expired test certificates

## [v0.9.1-beta - v1.4.0-beta]

Refer to [releases descriptions](https://github.com/italia/spid-laravel/releases).
