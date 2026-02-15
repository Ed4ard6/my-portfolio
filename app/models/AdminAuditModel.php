<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class AdminAuditModel
{
    private ?bool $tableExists = null;

    private function hasAuditTable(PDO $pdo): bool
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
        $stmt->execute(['admin_audit_logs']);

        $this->tableExists = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
        return $this->tableExists;
    }

    public function log(string $action, string $performedBy, ?int $targetAdminId, string $details = ''): void
    {
        $pdo = Database::connect();

        if (!$this->hasAuditTable($pdo)) {
            return;
        }

        $stmt = $pdo->prepare('
            INSERT INTO admin_audit_logs (action, performed_by, target_admin_id, details)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$action, $performedBy, $targetAdminId, $details]);
    }

    public function latest(int $limit = 20): array
    {
        $pdo = Database::connect();

        if (!$this->hasAuditTable($pdo)) {
            return [];
        }

        $stmt = $pdo->prepare('
            SELECT id, action, performed_by, target_admin_id, details, created_at
            FROM admin_audit_logs
            ORDER BY id DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
