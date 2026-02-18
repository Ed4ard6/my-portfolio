<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class TechnologyModel
{
    private function hasTechnologiesTable(PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS cnt
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?'
        );
        $stmt->execute(['technologies']);

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    private function hasIsActiveColumn(PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS cnt
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?'
        );
        $stmt->execute(['technologies', 'is_active']);

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    private function ensureIsActiveColumn(PDO $pdo): bool
    {
        if ($this->hasIsActiveColumn($pdo)) {
            return true;
        }

        try {
            $pdo->exec('ALTER TABLE technologies ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER name');
        } catch (Throwable $e) {
            // Puede fallar por permisos en hosting compartido.
        }

        return $this->hasIsActiveColumn($pdo);
    }

    public function supportsActiveFlag(): bool
    {
        $pdo = Database::connect();

        if (!$this->hasTechnologiesTable($pdo)) {
            return false;
        }

        return $this->ensureIsActiveColumn($pdo);
    }

    public function all(bool $onlyActive = false): array
    {
        $status = $onlyActive ? 'active' : 'all';
        return $this->paginate(1, PHP_INT_MAX, $status)['items'];
    }

    public function paginate(int $page = 1, int $perPage = 10, string $status = 'all'): array
    {
        $pdo = Database::connect();

        if (!$this->hasTechnologiesTable($pdo)) {
            return ['items' => [], 'page' => 1, 'perPage' => $perPage, 'total' => 0, 'totalPages' => 1];
        }

        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $hasActive = $this->ensureIsActiveColumn($pdo);
        $activeColumn = $hasActive ? 'is_active' : '1 AS is_active';

        $where = '';
        if ($hasActive && $status === 'active') {
            $where = ' WHERE is_active = 1';
        } elseif ($hasActive && $status === 'inactive') {
            $where = ' WHERE is_active = 0';
        }

        $countStmt = $pdo->query("SELECT COUNT(*) AS cnt FROM technologies{$where}");
        $total = (int)($countStmt->fetch()['cnt'] ?? 0);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $stmt = $pdo->prepare(
            "SELECT id, name, {$activeColumn}, created_at
             FROM technologies{$where}
             ORDER BY name ASC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        return [
            'items' => array_map(static function (array $row): array {
                $row['is_active'] = (int)($row['is_active'] ?? 1) === 1 ? 1 : 0;
                return $row;
            }, $rows),
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
        ];
    }

    public function find(int $id): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasTechnologiesTable($pdo)) {
            return null;
        }

        $hasActive = $this->ensureIsActiveColumn($pdo);
        $activeColumn = $hasActive ? 'is_active' : '1 AS is_active';
        $stmt = $pdo->prepare("SELECT id, name, {$activeColumn}, created_at FROM technologies WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        if (!is_array($row)) {
            return null;
        }

        $row['is_active'] = (int)($row['is_active'] ?? 1) === 1 ? 1 : 0;

        return $row;
    }

    public function nameExists(string $name, ?int $ignoreId = null): bool
    {
        $pdo = Database::connect();

        if (!$this->hasTechnologiesTable($pdo)) {
            return false;
        }

        if ($ignoreId !== null) {
            $stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM technologies WHERE name = ? AND id <> ?');
            $stmt->execute([$name, $ignoreId]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM technologies WHERE name = ?');
            $stmt->execute([$name]);
        }

        return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function create(string $name, bool $isActive): int
    {
        $pdo = Database::connect();

        if ($this->ensureIsActiveColumn($pdo)) {
            $stmt = $pdo->prepare('INSERT INTO technologies (name, is_active) VALUES (?, ?)');
            $stmt->execute([$name, $isActive ? 1 : 0]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO technologies (name) VALUES (?)');
            $stmt->execute([$name]);
        }

        return (int)$pdo->lastInsertId();
    }

    public function update(int $id, string $name, bool $isActive): void
    {
        $pdo = Database::connect();

        if ($this->ensureIsActiveColumn($pdo)) {
            $stmt = $pdo->prepare('UPDATE technologies SET name = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$name, $isActive ? 1 : 0, $id]);
            return;
        }

        $stmt = $pdo->prepare('UPDATE technologies SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
    }

    public function setActive(int $id, bool $isActive): void
    {
        $pdo = Database::connect();

        if (!$this->ensureIsActiveColumn($pdo)) {
            return;
        }

        $stmt = $pdo->prepare('UPDATE technologies SET is_active = ? WHERE id = ?');
        $stmt->execute([$isActive ? 1 : 0, $id]);
    }
}
