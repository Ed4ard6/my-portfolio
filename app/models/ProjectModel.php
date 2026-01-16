<?php

require_once __DIR__ . '/../../core/Database.php';

class ProjectModel
{
    private ?bool $hasImageUrlColumn = null;

    private function supportsImageUrl(PDO $pdo): bool
    {
        if ($this->hasImageUrlColumn !== null) {
            return $this->hasImageUrlColumn;
        }

        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM projects LIKE 'image_url'");
            $this->hasImageUrlColumn = $stmt && $stmt->fetch() ? true : false;
        } catch (Throwable $e) {
            $this->hasImageUrlColumn = false;
        }

        return $this->hasImageUrlColumn;
    }

    public function all(bool $includeArchived = false): array
    {
        $pdo = Database::connect();
        $imageSelect = $this->supportsImageUrl($pdo) ? "p.image_url" : "'' AS image_url";

        $where = $includeArchived ? "" : "WHERE p.status <> 'archived'";

        $sql = "
        SELECT 
            p.id,
            p.name,
            p.description,
            p.image_url,
            p.status,
            p.created_at,
            GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
        FROM projects p
        LEFT JOIN project_technology pt ON pt.project_id = p.id
        LEFT JOIN technologies t ON t.id = pt.technology_id
        $where
        GROUP BY p.id
        ORDER BY p.id DESC
        ";

        try {
            return $pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            if ($imageSelect !== "'' AS image_url") {
                $fallbackSql = "
                SELECT 
                    p.id,
                    p.name,
                    p.description,
                    '' AS image_url,
                    p.status,
                    p.created_at,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
                FROM projects p
                LEFT JOIN project_technology pt ON pt.project_id = p.id
                LEFT JOIN technologies t ON t.id = pt.technology_id
                $where
                GROUP BY p.id
                ORDER BY p.id DESC
                ";
                return $pdo->query($fallbackSql)->fetchAll();
            }
            throw $e;
        }
    }

    public function find(int $id): ?array
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function technologyIds(int $projectId): array
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
            SELECT technology_id
            FROM project_technology
            WHERE project_id = ?
        ");
        $stmt->execute([$projectId]);

        $rows = $stmt->fetchAll();
        return array_column($rows, 'technology_id'); // [1,3,5]
    }

    public function update(int $id, string $name, ?string $description, string $imageUrl, array $techIds): void
    {
        $pdo = Database::connect();
        $supportsImageUrl = $this->supportsImageUrl($pdo);

        // Transacción = “o se guarda todo, o no se guarda nada”
        $pdo->beginTransaction();

        try {
            $status = count($techIds) > 0 ? 'active' : 'pending';

            // 1) Actualizar datos del proyecto
            $stmt = $pdo->prepare("
                UPDATE projects
                SET name = ?, description = ?, image_url = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $imageUrl, $status, $id]);

            // 2) Borrar relaciones viejas
            $stmt = $pdo->prepare("DELETE FROM project_technology WHERE project_id = ?");
            $stmt->execute([$id]);

            // 3) Insertar relaciones nuevas
            if (count($techIds) > 0) {
                $stmt = $pdo->prepare("
                    INSERT INTO project_technology (project_id, technology_id)
                    VALUES (?, ?)
                ");

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

        $stmt = $pdo->prepare("
        SELECT t.name
        FROM technologies t
        INNER JOIN project_technology pt ON pt.technology_id = t.id
        WHERE pt.project_id = ?
        ORDER BY t.name ASC");
        $stmt->execute([$projectId]);

        return array_column($stmt->fetchAll(), 'name');
    }

    public function create(string $name, ?string $description, string $imageUrl, array $techIds): int
    {
        $pdo = Database::connect();
        $supportsImageUrl = $this->supportsImageUrl($pdo);
        $pdo->beginTransaction();

        try {
            $status = count($techIds) > 0 ? 'active' : 'pending';

            $stmt = $pdo->prepare("
            INSERT INTO projects (name, description, image_url, status)
            VALUES (?, ?, ?, ?)
        ");
            $stmt->execute([$name, $description, $imageUrl, $status]);

            $projectId = (int)$pdo->lastInsertId();

            if (count($techIds) > 0) {
                $stmt = $pdo->prepare("
                INSERT INTO project_technology (project_id, technology_id)
                VALUES (?, ?)
            ");

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

    public function archive(int $id): void //Es diferente a archived
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("UPDATE projects SET status = 'archived' WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function archived(): array //es diferente a Archive
    {
        $pdo = Database::connect();
        $imageSelect = $this->supportsImageUrl($pdo) ? "p.image_url" : "'' AS image_url";

        $sql = "
        SELECT 
            p.id,
            p.name,
            p.description,
            p.image_url,
            p.status,
            p.created_at,
            GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
        FROM projects p
        LEFT JOIN project_technology pt ON pt.project_id = p.id
        LEFT JOIN technologies t ON t.id = pt.technology_id
        WHERE p.status = 'archived'
        GROUP BY p.id
        ORDER BY p.id DESC
        ";
        try {
            return $pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            if ($imageSelect !== "'' AS image_url") {
                $fallbackSql = "
                SELECT 
                    p.id,
                    p.name,
                    p.description,
                    '' AS image_url,
                    p.status,
                    p.created_at,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
                FROM projects p
                LEFT JOIN project_technology pt ON pt.project_id = p.id
                LEFT JOIN technologies t ON t.id = pt.technology_id
                WHERE p.status = 'archived'
                GROUP BY p.id
                ORDER BY p.id DESC
                ";
                return $pdo->query($fallbackSql)->fetchAll();
            }
            throw $e;
        }
    }

    public function restore(int $id): void
    {
        $pdo = Database::connect();

        // Si tiene tecnologías, lo restauramos como active; si no, pending
        $stmt = $pdo->prepare("
        SELECT COUNT(*) AS cnt
        FROM project_technology
        WHERE project_id = ?
        ");
        $stmt->execute([$id]);
        $cnt = (int)($stmt->fetch()['cnt'] ?? 0);

        $newStatus = $cnt > 0 ? 'active' : 'pending';

        $stmt = $pdo->prepare("UPDATE projects SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
    }

    public function filterByStatus(string $status, bool $includeArchived = false): array
    {
        $pdo = Database::connect();
        $imageSelect = $this->supportsImageUrl($pdo) ? "p.image_url" : "'' AS image_url";

        $allowed = ['pending', 'active', 'completed', 'archived'];

        if (!in_array($status, $allowed, true)) {
            return $this->all($includeArchived); // fallback seguro
        }

        // Si no queremos archived dentro de /projects, lo filtramos aquí también
        // (aunque en tu caso solo lo llamaremos con pending/active/completed)
        $extra = '';
        if (!$includeArchived) {
            $extra = "AND p.status <> 'archived'";
        }

        $sql = "
        SELECT 
            p.id,
            p.name,
            p.description,
            p.image_url,
            p.status,
            p.created_at,
            GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
        FROM projects p
        LEFT JOIN project_technology pt ON pt.project_id = p.id
        LEFT JOIN technologies t ON t.id = pt.technology_id
        WHERE p.status = :status
        $extra
        GROUP BY p.id
        ORDER BY p.id DESC
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':status' => $status]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            if ($imageSelect !== "'' AS image_url") {
                $fallbackSql = "
                SELECT 
                    p.id,
                    p.name,
                    p.description,
                    '' AS image_url,
                    p.status,
                    p.created_at,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
                FROM projects p
                LEFT JOIN project_technology pt ON pt.project_id = p.id
                LEFT JOIN technologies t ON t.id = pt.technology_id
                WHERE p.status = :status
                $extra
                GROUP BY p.id
                ORDER BY p.id DESC
                ";
                $stmt = $pdo->prepare($fallbackSql);
                $stmt->execute([':status' => $status]);
                return $stmt->fetchAll();
            }
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
        UPDATE projects
        SET status = :status
        WHERE id = :id
          AND status <> 'archived'
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }
}
