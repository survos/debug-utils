<?php

declare(strict_types=1);

namespace Survos\DebugUtils;

/**
 * Assertion helpers that show *acceptable* values on failure — not just the
 * missing key. Far more useful than a plain "key not found" when debugging
 * CSV headers, API payloads, or config arrays where the mistake is a typo,
 * underscore vs hyphen, or wrong capitalisation.
 *
 * Intentionally a dev-only dependency. Do not require this in production code.
 */
final class Assert
{
    /**
     * Assert that $key exists in $array, showing all available keys on failure.
     *
     * @param array<mixed>|object $array
     */
    public static function keyExists(string|int $key, array|object $array, string $message = ''): void
    {
        if (is_object($array)) {
            $array = (array) $array;
        }

        if (!array_key_exists($key, $array)) {
            $keys = array_keys($array);
            sort($keys, SORT_STRING);
            throw new \InvalidArgumentException(
                sprintf("Missing key [%s]:\n%s", $key, implode("\n", $keys))
                . ($message ? "\n$message" : '')
            );
        }
    }

    /**
     * Assert that $value is in $allowed, showing all allowed values on failure.
     *
     * @param array<mixed> $allowed
     */
    public static function inArray(mixed $value, array $allowed, string $message = ''): void
    {
        if (!in_array($value, $allowed, strict: true)) {
            $display = array_map(static fn($v) => var_export($v, true), $allowed);
            sort($display, SORT_STRING);
            throw new \InvalidArgumentException(
                sprintf("Unexpected value %s. Allowed: %s", var_export($value, true), implode(', ', $display))
                . ($message ? "\n$message" : '')
            );
        }
    }

    /**
     * Assert that every key in $required exists in $array.
     *
     * @param list<string>         $required
     * @param array<mixed>|object  $array
     */
    public static function keysExist(array $required, array|object $array, string $message = ''): void
    {
        if (is_object($array)) {
            $array = (array) $array;
        }

        $missing = array_diff($required, array_keys($array));
        if ($missing) {
            $available = array_keys($array);
            sort($available, SORT_STRING);
            throw new \InvalidArgumentException(
                sprintf("Missing keys [%s]:\n%s", implode(', ', $missing), implode("\n", $available))
                . ($message ? "\n$message" : '')
            );
        }
    }
}
