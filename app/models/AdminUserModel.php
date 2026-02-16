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

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS cnt
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?'
        );
        $stmt->execute(['admin_users']);

        $this->tableExists = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
        return $this->tableExists;
    }

    private function hasEmailColumn(PDO $pdo): bool
    {
        if ($this->emailColumnExists !== null) {
            return $this->emailColumnExists;
        }

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS cnt
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?'
        );
        $stmt->execute(['admin_users', 'email']);

        $this->emailColumnExists = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
        return $this->emailColumnExists;
    }

    private function normalizeRow(array $row): array
    {
        $passwordHash = (string)($row['password_hash'] ?? '');
        $isActiveRaw = $row['is_active'] ?? 0;
        $email = (string)($row['email'] ?? '');

        $passwordLooksLikeHash = str_starts_with($passwordHash, '$2y$');
        $isActiveLooksLikeHash = is_string($isActiveRaw) && str_starts_with($isActiveRaw, '$2y$');

        if (!$passwordLooksLikeHash && $isActiveLooksLikeHash) {
            $row['password_hash'] = (string)$isActiveRaw;

            if (filter_var($passwordHash, FILTER_VALIDATE_EMAIL)) {
                $row['email'] = $passwordHash;
            }

            $createdRaw = $row['created_at'] ?? null;
            $row['is_active'] = (int)$createdRaw === 1 ? 1 : 0;
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $row['email'] = null;
        }

        $row['is_active'] = (int)($row['is_active'] ?? 0) === 1 ? 1 : 0;

        return $row;
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

        $stmt = $pdo->prepare(
            "SELECT id, username, {$emailSelect}, password_hash, is_active, created_at
             FROM admin_users
             WHERE username = ?
             LIMIT 1"
        );
        $stmt->execute([$username]);

        $row = $stmt->fetch();
        return is_array($row) ? $this->normalizeRow($row) : null;
    }

    public function findByEmail(string $email): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo) || !$this->hasEmailColumn($pdo)) {
            return null;
        }

        $stmt = $pdo->prepare(
            'SELECT id, username, email, password_hash, is_active, created_at
             FROM admin_users
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);

        $row = $stmt->fetch();
        return is_array($row) ? $this->normalizeRow($row) : null;
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

        $stmt = $pdo->query(
            "SELECT id, username, {$emailSelect}, password_hash, is_active, created_at
             FROM admin_users
             {$where}
             ORDER BY id DESC"
        );

        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => $this->normalizeRow($row), $rows);
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return null;
        }

        $emailSelect = $this->hasEmailColumn($pdo) ? 'email' : 'NULL AS email';

        $stmt = $pdo->prepare(
            "SELECT id, username, {$emailSelect}, password_hash, is_active, created_at
             FROM admin_users
             WHERE id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        return is_array($row) ? $this->normalizeRow($row) : null;
    }

    public function usernameExists(string $username, ?int $ignoreId = null): bool
    {
        $pdo = Database::connect();

        if (!$this->hasAdminUsersTable($pdo)) {
            return false;
        }

        if ($ignoreId !== null) {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) AS cnt
                 FROM admin_users
                 WHERE username = ? AND id <> ?'
            );
            $stmt->execute([$username, $ignoreId]);
        } else {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) AS cnt
                 FROM admin_users
                 WHERE username = ?'
            );
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
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) AS cnt
                 FROM admin_users
                 WHERE email = ? AND id <> ?'
            );
            $stmt->execute([$email, $ignoreId]);
        } else {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) AS cnt
                 FROM admin_users
                 WHERE email = ?'
            );
            $stmt->execute([$email]);
        }

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function create(string $username, string $email, string $plainPassword, bool $isActive): int
    {
        $pdo = Database::connect();

        if ($this->hasEmailColumn($pdo)) {
            $stmt = $pdo->prepare(
                'INSERT INTO admin_users (username, email, password_hash, is_active)
                 VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $username,
                $email,
                password_hash($plainPassword, PASSWORD_DEFAULT),
                $isActive ? 1 : 0,
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO admin_users (username, password_hash, is_active)
                 VALUES (?, ?, ?)'
            );
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
            $stmt = $pdo->prepare(
                'UPDATE admin_users
                 SET username = ?, email = ?, is_active = ?
                 WHERE id = ?'
            );
            $stmt->execute([$username, $email, $isActive ? 1 : 0, $id]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE admin_users
                 SET username = ?, is_active = ?
                 WHERE id = ?'
            );
            $stmt->execute([$username, $isActive ? 1 : 0, $id]);
        }
    }

    public function updatePassword(int $id, string $plainPassword): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
            'UPDATE admin_users
             SET password_hash = ?
             WHERE id = ?'
        );
        $stmt->execute([password_hash($plainPassword, PASSWORD_DEFAULT), $id]);
    }

    public function delete(int $id): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
        $stmt->execute([$id]);
    }
}
