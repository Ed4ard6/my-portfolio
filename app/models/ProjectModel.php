<?php

require_once __DIR__ . '/../../core/Database.php';

class ProjectModel
{
    private function listProjectColumns(PDO $pdo): array
    {
        $columns = [];

        try {
            $stmt = $pdo->query('SHOW COLUMNS FROM projects');
            foreach ($stmt->fetchAll() as $row) {
                $field = (string)($row['Field'] ?? '');
                if ($field !== '') {
                    $columns[] = $field;
                }
            }
        } catch (Throwable $e) {
            return [];
        }

        return $columns;
    }

    private function projectUrlColumn(): ?string
    {
        $pdo = Database::connect();
        $fields = $this->listProjectColumns($pdo);
        $priority = ['project_url', 'project_link', 'url'];

        foreach ($priority as $candidate) {
            if (in_array($candidate, $fields, true)) {
                return $candidate;
            }
        }

        return null;
    }

    private function ensureProjectUrlColumn(): ?string
    {
        $urlColumn = $this->projectUrlColumn();
        if ($urlColumn !== null) {
            return $urlColumn;
        }

        $pdo = Database::connect();

        try {
            $pdo->exec("ALTER TABLE projects ADD COLUMN project_url VARCHAR(255) NULL AFTER description");
        } catch (Throwable $e) {
            // Puede fallar por permisos o porque la columna ya existe en otra forma.
        }

        // Revalidación directa sin depender de caché de projectUrlColumn().
        $fields = $this->listProjectColumns($pdo);
        if (in_array('project_url', $fields, true)) {
            return 'project_url';
        }

        $legacyPriority = ['project_link', 'url'];
        foreach ($legacyPriority as $candidate) {
            if (in_array($candidate, $fields, true)) {
                return $candidate;
            }
        }

        return null;
    }

    public function supportsProjectUrl(): bool
    {
        return $this->projectUrlColumn() !== null;
    }

    private function normalizeProjectRow(array $row): array
    {
        if (!array_key_exists('project_url', $row)) {
            $legacyColumn = $this->projectUrlColumn();
            if ($legacyColumn !== null && array_key_exists($legacyColumn, $row)) {
                $row['project_url'] = $row[$legacyColumn];
            }
        }

        if (!array_key_exists('project_url', $row)) {
            $row['project_url'] = null;
        }

        return $row;
    }

    public function all(bool $includeArchived = false): array
    {
        $pdo = Database::connect();
        $where = $includeArchived ? "" : "WHERE p.status <> 'archived'";

        $columns = [
            'p.id',
            'p.name',
            'p.description',
            'p.status',
            'p.created_at',
        ];

        $urlColumn = $this->projectUrlColumn();
        $columns[] = $urlColumn !== null ? "p.{$urlColumn} AS project_url" : 'NULL AS project_url';

        $selectColumns = implode(",\n            ", $columns);

        $sql = "
        SELECT
            $selectColumns,
            GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
        FROM projects p
        LEFT JOIN project_technology pt ON pt.project_id = p.id
        LEFT JOIN technologies t ON t.id = pt.technology_id
        $where
        GROUP BY p.id
        ORDER BY p.id DESC
        ";

        return $pdo->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        return is_array($row) ? $this->normalizeProjectRow($row) : null;
    }

    public function technologyIds(int $projectId): array
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT technology_id FROM project_technology WHERE project_id = ?");
        $stmt->execute([$projectId]);

        return array_column($stmt->fetchAll(), 'technology_id');
    }

    public function update(int $id, string $name, ?string $description, ?string $projectUrl, string $status, array $techIds): void
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $allowedStatus = ['pending', 'active', 'completed'];
            if (!in_array($status, $allowedStatus, true)) {
                $status = count($techIds) > 0 ? 'active' : 'pending';
            }

            $urlColumn = $projectUrl !== null ? $this->ensureProjectUrlColumn() : $this->projectUrlColumn();

            if ($urlColumn !== null) {
                $stmt = $pdo->prepare("UPDATE projects SET name = ?, description = ?, {$urlColumn} = ?, status = ? WHERE id = ?");
                $stmt->execute([$name, $description, $projectUrl, $status, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE projects SET name = ?, description = ?, status = ? WHERE id = ?");
                $stmt->execute([$name, $description, $status, $id]);
            }

            $stmt = $pdo->prepare("DELETE FROM project_technology WHERE project_id = ?");
            $stmt->execute([$id]);

            if (count($techIds) > 0) {
                $stmt = $pdo->prepare("INSERT INTO project_technology (project_id, technology_id) VALUES (?, ?)");
                foreach ($techIds as $techId) {
                    $stmt->execute([$id, (int)$techId]);
                }
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function technologyNames(int $projectId): array
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
            "SELECT t.name
             FROM technologies t
             INNER JOIN project_technology pt ON pt.technology_id = t.id
             WHERE pt.project_id = ?
             ORDER BY t.name ASC"
        );
        $stmt->execute([$projectId]);

        return array_column($stmt->fetchAll(), 'name');
    }

    public function create(string $name, ?string $description, ?string $projectUrl, array $techIds): int
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $status = count($techIds) > 0 ? 'active' : 'pending';

            $urlColumn = $projectUrl !== null ? $this->ensureProjectUrlColumn() : $this->projectUrlColumn();

            if ($urlColumn !== null) {
                $stmt = $pdo->prepare("INSERT INTO projects (name, description, {$urlColumn}, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $projectUrl, $status]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO projects (name, description, status) VALUES (?, ?, ?)");
                $stmt->execute([$name, $description, $status]);
            }

            $projectId = (int)$pdo->lastInsertId();

            if (count($techIds) > 0) {
                $stmt = $pdo->prepare("INSERT INTO project_technology (project_id, technology_id) VALUES (?, ?)");
                foreach ($techIds as $techId) {
                    $stmt->execute([$projectId, (int)$techId]);
                }
            }

            $pdo->commit();
            return $projectId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function archive(int $id): void
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE projects SET status = 'archived' WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function archived(): array
    {
        $pdo = Database::connect();

        $columns = [
            'p.id',
            'p.name',
            'p.description',
            'p.status',
            'p.created_at',
        ];

        $urlColumn = $this->projectUrlColumn();
        $columns[] = $urlColumn !== null ? "p.{$urlColumn} AS project_url" : 'NULL AS project_url';

        $selectColumns = implode(",\n            ", $columns);

        $sql = "
        SELECT
            $selectColumns,
            GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
        FROM projects p
        LEFT JOIN project_technology pt ON pt.project_id = p.id
        LEFT JOIN technologies t ON t.id = pt.technology_id
        WHERE p.status = 'archived'
        GROUP BY p.id
        ORDER BY p.id DESC
        ";

        return $pdo->query($sql)->fetchAll();
    }

    public function restore(int $id): void
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM project_technology WHERE project_id = ?");
        $stmt->execute([$id]);
        $cnt = (int)($stmt->fetch()['cnt'] ?? 0);

        $newStatus = $cnt > 0 ? 'active' : 'pending';

        $stmt = $pdo->prepare("UPDATE projects SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
    }

    public function filterByStatus(string $status, bool $includeArchived = false): array
    {
        $pdo = Database::connect();

        $allowed = ['pending', 'active', 'completed', 'archived'];
        if (!in_array($status, $allowed, true)) {
            return $this->all($includeArchived);
        }

        $extra = !$includeArchived ? "AND p.status <> 'archived'" : '';

        $columns = [
            'p.id',
            'p.name',
            'p.description',
            'p.status',
            'p.created_at',
        ];

        $urlColumn = $this->projectUrlColumn();
        $columns[] = $urlColumn !== null ? "p.{$urlColumn} AS project_url" : 'NULL AS project_url';

        $selectColumns = implode(",\n            ", $columns);

        $sql = "
        SELECT
            $selectColumns,
            GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
        FROM projects p
        LEFT JOIN project_technology pt ON pt.project_id = p.id
        LEFT JOIN technologies t ON t.id = pt.technology_id
        WHERE p.status = :status
        $extra
        GROUP BY p.id
        ORDER BY p.id DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':status' => $status]);

        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
            "UPDATE projects
             SET status = :status
             WHERE id = :id
               AND status <> 'archived'"
        );

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }
}
