<?php
/**
 * Private .update.blb manifest reader (Builder Languages for Breakdance).
 *
 * The distributed file is compact + HMAC-signed. Decoding the payload does not
 * require the secret; verifying authenticity does (define BLB_MANIFEST_SECRET
 * only on your update server / private tooling — never ship it in public ZIPs).
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Absolute path to the private update manifest.
 */
function breakdance_languages_blb_manifest_path(): string
{
    return BREAKDANCE_LANGUAGES_PATH . '.update.blb';
}

/**
 * @return array<string, mixed>|null
 */
function breakdance_languages_read_blb_manifest(bool $require_valid_signature = false): ?array
{
    $path = breakdance_languages_blb_manifest_path();

    if (!is_readable($path)) {
        return null;
    }

    $line = trim((string) file_get_contents($path));
    $parts = explode('.', $line);

    if (count($parts) !== 3 || $parts[0] !== 'BLB1') {
        return null;
    }

    [$magic, $body, $signature] = $parts;

    if ($require_valid_signature) {
        $secret = breakdance_languages_blb_manifest_secret();

        if ($secret === null) {
            return null;
        }

        $expected = hash_hmac('sha256', $magic . '.' . $body, $secret);

        if (!hash_equals($expected, $signature)) {
            return null;
        }
    }

    $padding = str_repeat('=', (4 - strlen($body) % 4) % 4);
    $compressed = base64_decode(strtr($body, '-_', '+/') . $padding, true);

    if ($compressed === false) {
        return null;
    }

    $json = @gzuncompress($compressed);

    if ($json === false) {
        return null;
    }

    $payload = json_decode($json, true);

    return is_array($payload) ? $payload : null;
}

/**
 * Secret used only on private update infrastructure.
 */
function breakdance_languages_blb_manifest_secret(): ?string
{
    if (defined('BLB_MANIFEST_SECRET') && is_string(BLB_MANIFEST_SECRET) && BLB_MANIFEST_SECRET !== '') {
        return BLB_MANIFEST_SECRET;
    }

    return null;
}

/**
 * Version reported by .update.blb, falling back to the plugin constant.
 */
function breakdance_languages_blb_reported_version(): string
{
    $manifest = breakdance_languages_read_blb_manifest(false);
    $version = is_array($manifest) ? ($manifest['version'] ?? null) : null;

    if (is_string($version) && $version !== '') {
        return $version;
    }

    return BREAKDANCE_LANGUAGES_VERSION;
}
