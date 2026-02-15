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

            $values = parse_ini_file($file, false, INI_SCANNER_TYPED);
            if (!is_array($values)) {
                continue;
            }

            foreach ($values as $key => $value) {
                $normalized = is_bool($value) ? ($value ? 'true' : 'false') : (string)$value;

                if (getenv($key) === false) {
                    putenv($key . '=' . $normalized);
                }

                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $normalized;
                }

                if (!isset($_SERVER[$key])) {
                    $_SERVER[$key] = $normalized;
                }
            }

            break;
        }

        self::$loaded = true;
    }
}
