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

    public static function attempt(string $username, string $password): array
    {
        $model = new AdminUserModel();
        $user = $model->findByUsername($username);

        if (!$user) {
            return ['ok' => false, 'reason' => 'invalid_credentials'];
        }

        if ((int)($user['is_active'] ?? 0) !== 1) {
            return ['ok' => false, 'reason' => 'inactive_user'];
        }

        if (!password_verify($password, (string)$user['password_hash'])) {
            return ['ok' => false, 'reason' => 'invalid_credentials'];
        }

        $_SESSION[self::SESSION_KEY] = (string)$user['username'];
        session_regenerate_id(true);
        return ['ok' => true, 'reason' => 'ok'];
    }

    public static function login(string $username, string $password): bool
    {
        return (bool)(self::attempt($username, $password)['ok'] ?? false);
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
