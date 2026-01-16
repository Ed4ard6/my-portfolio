<?php

declare(strict_types=1);

class Auth
{
    private const SESSION_KEY = 'auth_user';

    private const DEFAULT_USERNAME = 'admin';
    private const DEFAULT_PASSWORD_HASH = '$2y$12$JPo8hC0c..ix566Xxt4T3eLPJT74q9LBRp7VY4Z0GH9oOp4dnhV32';

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public static function user(): ?string
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function login(string $username, string $password): bool
    {
        $expectedUser = getenv('PORTFOLIO_ADMIN_USER') ?: self::DEFAULT_USERNAME;
        $expectedHash = getenv('PORTFOLIO_ADMIN_HASH') ?: self::DEFAULT_PASSWORD_HASH;

        if ($username !== $expectedUser) {
            return false;
        }

        if (!password_verify($password, $expectedHash)) {
            return false;
        }

        $_SESSION[self::SESSION_KEY] = $username;
        session_regenerate_id(true);
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }

    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }

        header('Location: /auth/login');
        exit;
    }
}
