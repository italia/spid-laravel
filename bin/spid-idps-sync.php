#!/usr/bin/env php
<?php

/**
 * SPID IdP certificates sync/check.
 *
 * This script compares `config/spid-idps.php` against the official SPID registry JSON.
 * It enforces that each real IdP in the config:
 * - exists in the registry (matched by `entityId` == `entity_id`)
 * - uses the signing certificate with the furthest expiration date (latest notAfter)
 *
 * Modes:
 * - check: exits non-zero if mismatches are found
 * - update: rewrites `config/spid-idps.php` updating only `x509cert` values
 *
 * Usage:
 *   php scripts/spid-idps-sync.php check
 *   php scripts/spid-idps-sync.php update
 */

declare(strict_types=1);

const DEFAULT_SOURCE_URL = 'https://registry.spid.gov.it/entities-idp?&output=json';

function stderr(string $message): void
{
    fwrite(STDERR, $message . PHP_EOL);
}

function stdout(string $message): void
{
    fwrite(STDOUT, $message . PHP_EOL);
}

function normalizeB64(string $b64): string
{
    return preg_replace('/\s+/', '', $b64) ?? '';
}

function pemFromB64(string $b64): string
{
    $b64 = normalizeB64($b64);
    $wrapped = trim(chunk_split($b64, 64, "\n"));

    return "-----BEGIN CERTIFICATE-----\n" . $wrapped . "\n-----END CERTIFICATE-----\n";
}

function parseCertValidToTimeT(string $b64): int
{
    $pem = pemFromB64($b64);
    $x509 = openssl_x509_read($pem);
    if (false === $x509) {
        throw new RuntimeException('Invalid X509 certificate');
    }

    $parsed = openssl_x509_parse($x509);
    if (!is_array($parsed) || !isset($parsed['validTo_time_t'])) {
        throw new RuntimeException('Unable to parse X509 certificate');
    }

    return (int) $parsed['validTo_time_t'];
}

function fetchUrl(string $url, int $timeoutSeconds = 25): string
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if (false === $ch) {
            throw new RuntimeException('Unable to init cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $timeoutSeconds,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: italia/spid-laravel (cert-sync)',
            ],
        ]);

        $body = curl_exec($ch);
        $errNo = curl_errno($ch);
        $err = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if (false === $body || 0 !== $errNo) {
            throw new RuntimeException('HTTP request failed: ' . ($err ?: ('curl_errno=' . $errNo)));
        }
        if ($status < 200 || $status >= 300) {
            throw new RuntimeException('Unexpected HTTP status: ' . $status);
        }

        return (string) $body;
    }

    $ctx = stream_context_create([
        'http' => [
            'timeout' => $timeoutSeconds,
            'header' => "Accept: application/json\r\nUser-Agent: italia/spid-laravel (cert-sync)\r\n",
        ],
    ]);

    $body = @file_get_contents($url, false, $ctx);
    if (false === $body) {
        throw new RuntimeException('HTTP request failed (file_get_contents)');
    }

    return $body;
}

/**
 * @return array<string, array{certs: list<string>, latest_cert: string, latest_validTo: int}>
 */
function loadRegistryMap(string $sourceUrl): array
{
    $raw = fetchUrl($sourceUrl);
    $json = json_decode($raw, true);
    if (!is_array($json)) {
        throw new RuntimeException('Invalid JSON from registry');
    }

    $map = [];

    foreach ($json as $row) {
        if (!is_array($row)) {
            continue;
        }

        // Ignore deleted/disabled entities if those flags are present.
        if (($row['_deleted'] ?? 'N') === 'Y' || ($row['_disabled'] ?? 'N') === 'Y') {
            continue;
        }

        $entityId = $row['entity_id'] ?? null;
        $certs = $row['signing_certificate_x509'] ?? null;

        if (!is_string($entityId) || '' === $entityId) {
            continue;
        }
        if (!is_array($certs) || [] === $certs) {
            continue;
        }

        $clean = [];
        foreach ($certs as $c) {
            if (!is_string($c)) {
                continue;
            }
            $c = normalizeB64($c);
            if ('' !== $c) {
                $clean[] = $c;
            }
        }
        if ([] === $clean) {
            continue;
        }

        $latestCert = null;
        $latestValidTo = null;
        foreach ($clean as $c) {
            $validTo = parseCertValidToTimeT($c);
            if (null === $latestValidTo || $validTo > $latestValidTo) {
                $latestValidTo = $validTo;
                $latestCert = $c;
            }
        }

        if (null === $latestCert || null === $latestValidTo) {
            continue;
        }

        $map[$entityId] = [
            'certs' => $clean,
            'latest_cert' => $latestCert,
            'latest_validTo' => $latestValidTo,
        ];
    }

    return $map;
}

/**
 * @return array<string, mixed>
 */
function loadConfigIdps(string $configPath): array
{
    $cfg = require $configPath;
    if (!is_array($cfg)) {
        throw new RuntimeException('Config file did not return an array');
    }

    return $cfg;
}

function findIdpBlockOffsets(string $content, string $key): array
{
    $pattern = "/(^|\n)(\s*)'" . preg_quote($key, '/') . "'\s*=>\s*\[/";
    if (!preg_match($pattern, $content, $m, PREG_OFFSET_CAPTURE)) {
        throw new RuntimeException("Unable to locate IdP block for key '{$key}'");
    }

    $startOffset = $m[0][1];

    if (!preg_match("/\n\s{2}\],/", $content, $endMatch, PREG_OFFSET_CAPTURE, $startOffset)) {
        throw new RuntimeException("Unable to locate end of IdP block for key '{$key}'");
    }

    $endOffset = $endMatch[0][1];
    $endLen = strlen($endMatch[0][0]);

    return [$startOffset, $endOffset + $endLen];
}

function updateX509CertInContent(string $content, string $key, string $newCertB64): string
{
    [$start, $end] = findIdpBlockOffsets($content, $key);
    $block = substr($content, $start, $end - $start);

    $pattern = "/('x509cert'\s*=>\s*')([^']*)(')/";
    $count = 0;
    $replacement = '$1' . $newCertB64 . '$3';
    $updatedBlock = preg_replace($pattern, $replacement, $block, 1, $count);
    if (!is_string($updatedBlock) || 1 !== $count) {
        throw new RuntimeException("Unable to update x509cert for key '{$key}' (count={$count})");
    }

    return substr($content, 0, $start) . $updatedBlock . substr($content, $end);
}

function formatDate(int $unixTs): string
{
    return gmdate('Y-m-d', $unixTs);
}

function main(array $argv): int
{
    $mode = $argv[1] ?? null;
    if (!in_array($mode, ['check', 'update'], true)) {
        stderr('Usage: php scripts/spid-idps-sync.php <check|update>');

        return 2;
    }

    $repoRoot = realpath(__DIR__ . '/..') ?: getcwd();
    $configPath = $repoRoot . '/config/spid-idps.php';

    $sourceUrl = getenv('SPID_REGISTRY_IDP_JSON_URL');
    if (!is_string($sourceUrl) || '' === $sourceUrl) {
        $sourceUrl = DEFAULT_SOURCE_URL;
    }

    $registry = loadRegistryMap($sourceUrl);
    $idps = loadConfigIdps($configPath);

    $structuralIssues = [];
    $certIssues = [];
    $updates = [];

    // Build the set of active real IdPs from config (must match registry 1:1).
    $activeConfigByEntityId = [];
    $activeConfigByKey = [];
    foreach ($idps as $key => $cfg) {
        if (!is_string($key) || !is_array($cfg)) {
            continue;
        }
        if (($cfg['real'] ?? false) !== true || ($cfg['isActive'] ?? false) !== true) {
            continue;
        }

        $entityId = $cfg['entityId'] ?? null;
        if (!is_string($entityId) || '' === $entityId || !str_starts_with($entityId, 'http')) {
            continue;
        }

        if (isset($activeConfigByEntityId[$entityId])) {
            $structuralIssues[] = "Duplicate active entityId in config: {$entityId}";
            continue;
        }

        $activeConfigByEntityId[$entityId] = $key;
        $activeConfigByKey[$key] = $cfg;
    }

    // Bidirectional membership check.
    foreach ($registry as $entityId => $_) {
        if (!isset($activeConfigByEntityId[$entityId])) {
            $structuralIssues[] = "Missing active IdP in config for registry entity_id: {$entityId}";
        }
    }
    foreach ($activeConfigByEntityId as $entityId => $key) {
        if (!isset($registry[$entityId])) {
            $structuralIssues[] = "Extra active IdP in config not present in registry: {$key} ({$entityId})";
        }
    }

    // Certificate check/update for active real IdPs.
    foreach ($activeConfigByKey as $key => $cfg) {
        $entityId = (string) ($cfg['entityId'] ?? '');
        if ('' === $entityId || !isset($registry[$entityId])) {
            // Structural checks already report this.
            continue;
        }

        $latest = $registry[$entityId]['latest_cert'];
        $latestValidTo = (int) $registry[$entityId]['latest_validTo'];

        $current = normalizeB64((string) ($cfg['x509cert'] ?? ''));
        if ('' === $current && 'check' === $mode) {
            $certIssues[] = "{$key}: missing x509cert in config";
            continue;
        }

        if ($current !== $latest) {
            if ('check' === $mode) {
                $currentValidTo = null;
                try {
                    if ('' !== $current) {
                        $currentValidTo = parseCertValidToTimeT($current);
                    }
                } catch (Throwable $e) {
                    // ignore, just show mismatch
                }

                $certIssues[] = sprintf(
                    '%s: certificate mismatch (config not latest). config_notAfter=%s registry_notAfter=%s',
                    $key,
                    is_int($currentValidTo) ? formatDate($currentValidTo) : 'unknown',
                    formatDate($latestValidTo)
                );
            } else {
                $updates[$key] = $latest;
            }
        }
    }

    if ('check' === $mode) {
        $issues = array_merge($structuralIssues, $certIssues);
        if ([] !== $issues) {
            stderr('SPID IdP registry sync check FAILED:');
            foreach ($issues as $e) {
                stderr('- ' . $e);
            }

            return 1;
        }

        stdout('SPID IdP registry sync check OK.');

        return 0;
    }

    // update
    if ([] !== $structuralIssues) {
        stderr('No updates performed (fail-fast: structural issues found):');
        foreach ($structuralIssues as $e) {
            stderr('- ' . $e);
        }

        return 1;
    }

    $content = file_get_contents($configPath);
    if (!is_string($content)) {
        throw new RuntimeException('Unable to read config file');
    }

    $changed = 0;
    foreach ($updates as $key => $newCert) {
        $content = updateX509CertInContent($content, $key, $newCert);
        ++$changed;
    }

    if (0 === $changed) {
        stdout('No changes needed.');

        return 0;
    }

    $ok = file_put_contents($configPath, $content);
    if (false === $ok) {
        throw new RuntimeException('Unable to write config file');
    }

    stdout("Updated {$changed} IdP certificate(s) in config/spid-idps.php");

    return 0;
}

try {
    exit(main($argv));
} catch (Throwable $e) {
    stderr('ERROR: ' . $e->getMessage());

    return 1;
}
