<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class PasswordResetTokenModel
{
    private ?bool $tableExists = null;

    private function hasTable(PDO $pdo): bool
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
        $stmt->execute(['admin_password_resets']);
        $this->tableExists = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;

        return $this->tableExists;
    }

    public function supported(): bool
    {
        return $this->hasTable(Database::connect());
    }

    public function create(int $adminId, string $plainToken, int $minutes = 30): bool
    {
        $pdo = Database::connect();

        if (!$this->hasTable($pdo)) {
            return false;
        }

        $hash = hash('sha256', $plainToken);

        $stmt = $pdo->prepare('
            INSERT INTO admin_password_resets (admin_user_id, token_hash, expires_at)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE))
        ');

        return $stmt->execute([$adminId, $hash, $minutes]);
    }

    public function consumeValid(string $plainToken): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasTable($pdo)) {
            return null;
        }

        $hash = hash('sha256', $plainToken);

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT id, admin_user_id, expires_at, used_at
                 FROM admin_password_resets
                 WHERE token_hash = ?
                   AND used_at IS NULL
                   AND expires_at >= NOW()
                 LIMIT 1
                 FOR UPDATE'
            );
            $stmt->execute([$hash]);
            $row = $stmt->fetch();

            if (!$row) {
                $pdo->rollBack();
                return null;
            }

            $update = $pdo->prepare('UPDATE admin_password_resets SET used_at = NOW() WHERE id = ?');
            $update->execute([(int)$row['id']]);

            $pdo->commit();
            return $row;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}