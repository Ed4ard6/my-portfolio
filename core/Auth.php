<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/models/AdminUserModel.php';

class Auth
{
    private const SESSION_KEY = 'auth_user';

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
        $model = new AdminUserModel();
        $user = $model->findByUsername($username);

        if (!$user || (int)($user['is_active'] ?? 0) !== 1) {
            return false;
        }

        if (!password_verify($password, (string)$user['password_hash'])) {
            return false;
        }

        $_SESSION[self::SESSION_KEY] = (string)$user['username'];
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
