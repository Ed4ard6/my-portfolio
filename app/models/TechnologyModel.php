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

    public function supportsActiveFlag(): bool
    {
        $pdo = Database::connect();

        if (!$this->hasTechnologiesTable($pdo)) {
            return false;
        }

        return $this->hasIsActiveColumn($pdo);
    }

    public function all(bool $onlyActive = false): array
    {
        $pdo = Database::connect();

        if (!$this->hasTechnologiesTable($pdo)) {
            return [];
        }

        $activeColumn = $this->hasIsActiveColumn($pdo) ? 'is_active' : '1 AS is_active';

        if ($onlyActive && $this->hasIsActiveColumn($pdo)) {
            $stmt = $pdo->query("SELECT id, name, {$activeColumn}, created_at FROM technologies WHERE is_active = 1 ORDER BY name ASC");
        } else {
            $stmt = $pdo->query("SELECT id, name, {$activeColumn}, created_at FROM technologies ORDER BY name ASC");
        }

        $rows = $stmt->fetchAll();

        return array_map(static function (array $row): array {
            $row['is_active'] = (int)($row['is_active'] ?? 1) === 1 ? 1 : 0;
            return $row;
        }, $rows);
    }

    public function find(int $id): ?array
    {
        $pdo = Database::connect();

        if (!$this->hasTechnologiesTable($pdo)) {
            return null;
        }

        $activeColumn = $this->hasIsActiveColumn($pdo) ? 'is_active' : '1 AS is_active';
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

        if ($this->hasIsActiveColumn($pdo)) {
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

        if ($this->hasIsActiveColumn($pdo)) {
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

        if (!$this->hasIsActiveColumn($pdo)) {
            return;
        }

        $stmt = $pdo->prepare('UPDATE technologies SET is_active = ? WHERE id = ?');
        $stmt->execute([$isActive ? 1 : 0, $id]);
    }
}
