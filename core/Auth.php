<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/models/AdminUserModel.php';

class Auth
{
    private const SESSION_KEY = 'auth_user';
    private const SESSION_USER_ID_KEY = 'auth_user_id';
    private const ATTEMPT_KEY = 'auth_attempts';
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 900;

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public static function user(): ?string
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function userId(): ?int
    {
        $id = $_SESSION[self::SESSION_USER_ID_KEY] ?? null;
        return is_numeric($id) ? (int)$id : null;
    }

    private static function throttleBucket(string $username): string
    {
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        return strtolower($username) . '|' . $ip;
    }

    private static function getAttempt(string $bucket): array
    {
        $all = $_SESSION[self::ATTEMPT_KEY] ?? [];
        return is_array($all[$bucket] ?? null) ? $all[$bucket] : ['count' => 0, 'locked_until' => 0];
    }

    private static function storeAttempt(string $bucket, array $data): void
    {
        if (!isset($_SESSION[self::ATTEMPT_KEY]) || !is_array($_SESSION[self::ATTEMPT_KEY])) {
            $_SESSION[self::ATTEMPT_KEY] = [];
        }

        $_SESSION[self::ATTEMPT_KEY][$bucket] = $data;
    }

    public static function attempt(string $username, string $password): array
    {
        $bucket = self::throttleBucket($username);
        $attempt = self::getAttempt($bucket);

        if ((int)($attempt['locked_until'] ?? 0) > time()) {
            $retryAfter = (int)$attempt['locked_until'] - time();
            return ['ok' => false, 'reason' => 'too_many_attempts', 'retry_after' => $retryAfter];
        }

        $model = new AdminUserModel();
        $user = $model->findByUsername($username);

        if (!$user) {
            self::registerFailure($bucket, $attempt);
            return ['ok' => false, 'reason' => 'invalid_credentials'];
        }

        if ((int)($user['is_active'] ?? 0) !== 1) {
            self::registerFailure($bucket, $attempt);
            return ['ok' => false, 'reason' => 'inactive_user'];
        }

        if (!password_verify($password, (string)$user['password_hash'])) {
            self::registerFailure($bucket, $attempt);
            return ['ok' => false, 'reason' => 'invalid_credentials'];
        }

        self::storeAttempt($bucket, ['count' => 0, 'locked_until' => 0]);
        $_SESSION[self::SESSION_KEY] = (string)$user['username'];
        $_SESSION[self::SESSION_USER_ID_KEY] = (int)$user['id'];
        session_regenerate_id(true);

        return ['ok' => true, 'reason' => 'ok'];
    }

    private static function registerFailure(string $bucket, array $attempt): void
    {
        $count = (int)($attempt['count'] ?? 0) + 1;
        $lockedUntil = 0;

        if ($count >= self::MAX_ATTEMPTS) {
            $lockedUntil = time() + self::LOCK_SECONDS;
            $count = 0;
        }

        self::storeAttempt($bucket, ['count' => $count, 'locked_until' => $lockedUntil]);
    }

    public static function login(string $username, string $password): bool
    {
        return (bool)(self::attempt($username, $password)['ok'] ?? false);
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::SESSION_USER_ID_KEY]);
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
