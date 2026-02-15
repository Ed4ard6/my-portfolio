<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class AdminUserModel
{
    private ?bool $tableExists = null;

    private function hasAdminUsersTable(PDO $pdo): bool
    {
        if ($this->tableExists !== null) {
            return $this->tableExists;
        }

        $stmt = $pdo->prepare('
            SELECT COUNT(*) AS cnt
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
        ');
        $stmt->execute(['admin_users']);

        $this->tableExists = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
        return $this->tableExists;
    }

    public function findByUsername(string $username): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return null;
        }

        $stmt = $pdo->prepare('
            SELECT id, username, email, password_hash, is_active, created_at
            FROM admin_users
            WHERE username = ?
            LIMIT 1
        ');
        $stmt->execute([$username]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(string $status = 'all'): array
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return [];
        }

        if ($status === 'active') {
            $stmt = $pdo->query('
                SELECT id, username, email, is_active, created_at
                FROM admin_users
                WHERE is_active = 1
                ORDER BY id DESC
            ');
        } elseif ($status === 'inactive') {
            $stmt = $pdo->query('
                SELECT id, username, email, is_active, created_at
                FROM admin_users
                WHERE is_active = 0
                ORDER BY id DESC
            ');
        } else {
            $stmt = $pdo->query('
                SELECT id, username, email, is_active, created_at
                FROM admin_users
                ORDER BY id DESC
            ');
        }

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return null;
        }

        $stmt = $pdo->prepare('
            SELECT id, username, email, password_hash, is_active, created_at
            FROM admin_users
            WHERE id = ?
            LIMIT 1
        ');
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function usernameExists(string $username, ?int $ignoreId = null): bool
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return false;
        }

        if ($ignoreId !== null) {
            $stmt = $pdo->prepare('
                SELECT COUNT(*) AS cnt
                FROM admin_users
                WHERE username = ? AND id <> ?
            ');
            $stmt->execute([$username, $ignoreId]);
        } else {
            $stmt = $pdo->prepare('
                SELECT COUNT(*) AS cnt
                FROM admin_users
                WHERE username = ?
            ');
            $stmt->execute([$username]);
        }

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return false;
        }

        if ($ignoreId !== null) {
            $stmt = $pdo->prepare('
                SELECT COUNT(*) AS cnt
                FROM admin_users
                WHERE email = ? AND id <> ?
            ');
            $stmt->execute([$email, $ignoreId]);
        } else {
            $stmt = $pdo->prepare('
                SELECT COUNT(*) AS cnt
                FROM admin_users
                WHERE email = ?
            ');
            $stmt->execute([$email]);
        }

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function create(string $username, string $email, string $plainPassword, bool $isActive): int
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('
            INSERT INTO admin_users (username, email, password_hash, is_active)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $username,
            $email,
            password_hash($plainPassword, PASSWORD_DEFAULT),
            $isActive ? 1 : 0,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public function update(int $id, string $username, string $email, bool $isActive): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('
            UPDATE admin_users
            SET username = ?, email = ?, is_active = ?
            WHERE id = ?
        ');
        $stmt->execute([$username, $email, $isActive ? 1 : 0, $id]);
    }

    public function updatePassword(int $id, string $plainPassword): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('
            UPDATE admin_users
            SET password_hash = ?
            WHERE id = ?
        ');
        $stmt->execute([password_hash($plainPassword, PASSWORD_DEFAULT), $id]);
    }

    public function delete(int $id): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
        $stmt->execute([$id]);
    }
}
