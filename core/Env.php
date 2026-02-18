<?php

declare(strict_types=1);

class Env
{
    private static bool $loaded = false;

    public static function load(string $basePath): void
    {
        if (self::$loaded) {
            return;
        }

        $candidates = [
            $basePath . '/.env',
            $basePath . '/.emp',
        ];

        foreach ($candidates as $file) {
            if (!is_readable($file)) {
                continue;
            }

            $values = self::parseEnvFile($file);
            if ($values === []) {
                continue;
            }

            foreach ($values as $key => $value) {
                if (getenv($key) === false) {
                    putenv($key . '=' . $value);
                }

                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                }

                if (!isset($_SERVER[$key])) {
                    $_SERVER[$key] = $value;
                }
            }

            break;
        }

        self::$loaded = true;
    }

    private static function parseEnvFile(string $file): array
    {
        $rawContent = @file_get_contents($file);
        if (!is_string($rawContent) || $rawContent === '') {
            return [];
        }

        if (!self::contains($rawContent, "\n") && self::contains($rawContent, '\\n')) {
            $rawContent = str_replace(['\\r\\n', '\\n', '\\r'], ["\n", "\n", "\n"], $rawContent);
        }

        $lines = preg_split('/\r?\n/', $rawContent) ?: [];
        $values = [];

        foreach ($lines as $line) {
            $trimmed = trim((string)$line);
            if ($trimmed === '' || self::startsWith($trimmed, '#') || self::startsWith($trimmed, ';')) {
                continue;
            }

            if (self::startsWith($trimmed, 'export ')) {
                $trimmed = trim((string)substr($trimmed, 7));
            }

            $equalPos = strpos($trimmed, '=');
            if ($equalPos === false || $equalPos === 0) {
                continue;
            }

            $key = trim((string)substr($trimmed, 0, $equalPos));
            if ($key === '') {
                continue;
            }

            $rawValue = trim((string)substr($trimmed, $equalPos + 1));
            $values[$key] = self::normalizeValue($rawValue);
        }

        return $values;
    }

    private static function normalizeValue(string $rawValue): string
    {
        $length = strlen($rawValue);
        if ($length >= 2) {
            $first = $rawValue[0];
            $last = $rawValue[$length - 1];

            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $unwrapped = substr($rawValue, 1, -1);
                if ($first === '"') {
                    $unwrapped = strtr($unwrapped, [
                        '\\n' => "\n",
                        '\\r' => "\r",
                        '\\t' => "\t",
                        '\\"' => '"',
                        '\\\\' => '\\',
                    ]);
                }

                return $unwrapped;
            }
        }

        return $rawValue;
    }

    private static function startsWith(string $haystack, string $needle): bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    private static function contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}
