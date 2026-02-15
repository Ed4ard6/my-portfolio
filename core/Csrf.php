<?php

declare(strict_types=1);

class Csrf
{
    private const SESSION_KEY = 'csrf_token';
    private const FIELD_NAME = '_csrf';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function fieldName(): string
    {
        return self::FIELD_NAME;
    }

    public static function validate(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';
        return $sessionToken !== '' && hash_equals($sessionToken, $token);
    }
}
