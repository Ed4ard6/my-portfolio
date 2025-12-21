<?php

require_once __DIR__ . '/../../core/Database.php';

class ProjectModel
{
    public function all(): array
    {
        $pdo = Database::connect();

        // Lista proyectos + tecnologías en una sola fila (para tu vista index)
        $sql = "
            SELECT 
                p.id, p.name, p.description, p.status, p.created_at,
                GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS technologies
            FROM projects p
            LEFT JOIN project_technology pt ON pt.project_id = p.id
            LEFT JOIN technologies t ON t.id = pt.technology_id
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

    public function update(int $id, string $name, ?string $description, array $techIds): void
    {
        $pdo = Database::connect();

        // Transacción = “o se guarda todo, o no se guarda nada”
        $pdo->beginTransaction();

        try {
            $status = count($techIds) > 0 ? 'active' : 'pending';

            // 1) Actualizar datos del proyecto
            $stmt = $pdo->prepare("
                UPDATE projects
                SET name = ?, description = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $status, $id]);

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
}
