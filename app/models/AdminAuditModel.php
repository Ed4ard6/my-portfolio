<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class AdminAuditModel
{
    private ?bool $tableExists = null;
    private ?bool $hasPerformerIdColumn = null;

    private function hasAuditTable(PDO $pdo): bool
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
        $stmt->execute(['admin_audit_logs']);

        $this->tableExists = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
        return $this->tableExists;
    }

    private function hasPerformerIdColumn(PDO $pdo): bool
    {
        if ($this->hasPerformerIdColumn !== null) {
            return $this->hasPerformerIdColumn;
        }

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS cnt
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?'
        );
        $stmt->execute(['admin_audit_logs', 'performed_by_admin_id']);

        $this->hasPerformerIdColumn = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
        return $this->hasPerformerIdColumn;
    }

    public function log(string $action, ?int $performedByAdminId, ?int $targetAdminId, string $details = ''): void
    {
        $pdo = Database::connect();

        if (!$this->hasAuditTable($pdo)) {
            return;
        }

        $performedByName = 'system';
        if ($performedByAdminId !== null) {
            $stmtName = $pdo->prepare('SELECT username FROM admin_users WHERE id = ? LIMIT 1');
            $stmtName->execute([$performedByAdminId]);
            $performedByName = (string)($stmtName->fetch()['username'] ?? 'system');
        }

        if ($this->hasPerformerIdColumn($pdo)) {
            $stmt = $pdo->prepare(
                'INSERT INTO admin_audit_logs (action, performed_by, performed_by_admin_id, target_admin_id, details)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$action, $performedByName, $performedByAdminId, $targetAdminId, $details]);
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO admin_audit_logs (action, performed_by, target_admin_id, details)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$action, $performedByName, $targetAdminId, $details]);
    }


    public function paginate(int $page = 1, int $perPage = 10, ?int $targetAdminId = null): array
    {
        $pdo = Database::connect();

        if (!$this->hasAuditTable($pdo)) {
            return [
                'items' => [],
                'total' => 0,
                'page' => 1,
                'perPage' => $perPage,
                'totalPages' => 1,
            ];
        }

        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];

        if ($targetAdminId !== null && $targetAdminId > 0) {
            $where = 'WHERE a.target_admin_id = :target_admin_id';
            $params[':target_admin_id'] = $targetAdminId;
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM admin_audit_logs a {$where}");
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $countStmt->execute();

        $total = (int)($countStmt->fetch()['total'] ?? 0);
        $totalPages = max(1, (int)ceil($total / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }

        if ($this->hasPerformerIdColumn($pdo)) {
            $sql = "
                SELECT
                    a.id,
                    a.action,
                    a.performed_by,
                    a.performed_by_admin_id,
                    COALESCE(actor.username, a.performed_by) AS performed_by_name,
                    a.target_admin_id,
                    target.username AS target_admin_name,
                    a.details,
                    a.created_at
                FROM admin_audit_logs a
                LEFT JOIN admin_users actor ON actor.id = a.performed_by_admin_id
                LEFT JOIN admin_users target ON target.id = a.target_admin_id
                {$where}
                ORDER BY a.id DESC
                LIMIT :limit OFFSET :offset
            ";
        } else {
            $sql = "
                SELECT
                    a.id,
                    a.action,
                    a.performed_by,
                    NULL AS performed_by_admin_id,
                    a.performed_by AS performed_by_name,
                    a.target_admin_id,
                    target.username AS target_admin_name,
                    a.details,
                    a.created_at
                FROM admin_audit_logs a
                LEFT JOIN admin_users target ON target.id = a.target_admin_id
                {$where}
                ORDER BY a.id DESC
                LIMIT :limit OFFSET :offset
            ";
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
        ];
    }
    public function latest(int $limit = 20, ?int $targetAdminId = null): array
    {
        $pdo = Database::connect();

        if (!$this->hasAuditTable($pdo)) {
            return [];
        }

        $where = '';
        $params = [];

        if ($targetAdminId !== null && $targetAdminId > 0) {
            $where = 'WHERE a.target_admin_id = ?';
            $params[] = $targetAdminId;
        }

        if ($this->hasPerformerIdColumn($pdo)) {
            $sql = "
                SELECT
                    a.id,
                    a.action,
                    a.performed_by,
                    a.performed_by_admin_id,
                    COALESCE(actor.username, a.performed_by) AS performed_by_name,
                    a.target_admin_id,
                    target.username AS target_admin_name,
                    a.details,
                    a.created_at
                FROM admin_audit_logs a
                LEFT JOIN admin_users actor ON actor.id = a.performed_by_admin_id
                LEFT JOIN admin_users target ON target.id = a.target_admin_id
                $where
                ORDER BY a.id DESC
                LIMIT ?
            ";
        } else {
            $sql = "
                SELECT
                    a.id,
                    a.action,
                    a.performed_by,
                    NULL AS performed_by_admin_id,
                    a.performed_by AS performed_by_name,
                    a.target_admin_id,
                    target.username AS target_admin_name,
                    a.details,
                    a.created_at
                FROM admin_audit_logs a
                LEFT JOIN admin_users target ON target.id = a.target_admin_id
                $where
                ORDER BY a.id DESC
                LIMIT ?
            ";
        }

        $stmt = $pdo->prepare($sql);

        $position = 1;
        foreach ($params as $value) {
            $stmt->bindValue($position++, $value, PDO::PARAM_INT);
        }
        $stmt->bindValue($position, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
