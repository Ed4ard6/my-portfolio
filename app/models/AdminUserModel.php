<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class AdminUserModel
{
    private ?bool $tableExists = null;
    private ?bool $emailColumnExists = null;

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

    private function hasEmailColumn(PDO $pdo): bool
    {
        if ($this->emailColumnExists !== null) {
            return $this->emailColumnExists;
        }

        $stmt = $pdo->prepare('
            SELECT COUNT(*) AS cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ');
        $stmt->execute(['admin_users', 'email']);

        $this->emailColumnExists = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
        return $this->emailColumnExists;
    }

    public function supportsEmail(): bool
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return false;
        }

        return $this->hasEmailColumn($pdo);
    }

    public function findByUsername(string $username): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return null;
        }

        $emailSelect = $this->hasEmailColumn($pdo) ? 'email' : 'NULL AS email';

        $stmt = $pdo->prepare("\n            SELECT id, username, {$emailSelect}, password_hash, is_active, created_at\n            FROM admin_users\n            WHERE username = ?\n            LIMIT 1\n        ");
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

        $emailSelect = $this->hasEmailColumn($pdo) ? 'email' : 'NULL AS email';
        $where = '';

        if ($status === 'active') {
            $where = 'WHERE is_active = 1';
        } elseif ($status === 'inactive') {
            $where = 'WHERE is_active = 0';
        }

        $stmt = $pdo->query("\n            SELECT id, username, {$emailSelect}, is_active, created_at\n            FROM admin_users\n            {$where}\n            ORDER BY id DESC\n        ");

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return null;
        }

        $emailSelect = $this->hasEmailColumn($pdo) ? 'email' : 'NULL AS email';

        $stmt = $pdo->prepare("\n            SELECT id, username, {$emailSelect}, password_hash, is_active, created_at\n            FROM admin_users\n            WHERE id = ?\n            LIMIT 1\n        ");
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
            $stmt = $pdo->prepare('\n                SELECT COUNT(*) AS cnt\n                FROM admin_users\n                WHERE username = ? AND id <> ?\n            ');
            $stmt->execute([$username, $ignoreId]);
        } else {
            $stmt = $pdo->prepare('\n                SELECT COUNT(*) AS cnt\n                FROM admin_users\n                WHERE username = ?\n            ');
            $stmt->execute([$username]);
        }

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo) || !$this->hasEmailColumn($pdo)) {
            return false;
        }

        if ($ignoreId !== null) {
            $stmt = $pdo->prepare('\n                SELECT COUNT(*) AS cnt\n                FROM admin_users\n                WHERE email = ? AND id <> ?\n            ');
            $stmt->execute([$email, $ignoreId]);
        } else {
            $stmt = $pdo->prepare('\n                SELECT COUNT(*) AS cnt\n                FROM admin_users\n                WHERE email = ?\n            ');
            $stmt->execute([$email]);
        }

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function create(string $username, string $email, string $plainPassword, bool $isActive): int
    {
        $pdo = Database::connect();

        if ($this->hasEmailColumn($pdo)) {
            $stmt = $pdo->prepare('\n                INSERT INTO admin_users (username, email, password_hash, is_active)\n                VALUES (?, ?, ?, ?)\n            ');
            $stmt->execute([
                $username,
                $email,
                password_hash($plainPassword, PASSWORD_DEFAULT),
                $isActive ? 1 : 0,
            ]);
        } else {
            $stmt = $pdo->prepare('\n                INSERT INTO admin_users (username, password_hash, is_active)\n                VALUES (?, ?, ?)\n            ');
            $stmt->execute([
                $username,
                password_hash($plainPassword, PASSWORD_DEFAULT),
                $isActive ? 1 : 0,
            ]);
        }

        return (int)$pdo->lastInsertId();
    }

    public function update(int $id, string $username, string $email, bool $isActive): void
    {
        $pdo = Database::connect();

        if ($this->hasEmailColumn($pdo)) {
            $stmt = $pdo->prepare('\n                UPDATE admin_users\n                SET username = ?, email = ?, is_active = ?\n                WHERE id = ?\n            ');
            $stmt->execute([$username, $email, $isActive ? 1 : 0, $id]);
        } else {
            $stmt = $pdo->prepare('\n                UPDATE admin_users\n                SET username = ?, is_active = ?\n                WHERE id = ?\n            ');
            $stmt->execute([$username, $isActive ? 1 : 0, $id]);
        }
    }

    public function updatePassword(int $id, string $plainPassword): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('\n            UPDATE admin_users\n            SET password_hash = ?\n            WHERE id = ?\n        ');
        $stmt->execute([password_hash($plainPassword, PASSWORD_DEFAULT), $id]);
    }

    public function delete(int $id): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
        $stmt->execute([$id]);
    }
}
