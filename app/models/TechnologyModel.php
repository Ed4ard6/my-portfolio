<?php

require_once __DIR__ . '/../../core/Database.php';


class TechnologyModel
{
    public function all(): array
    {
        $pdo = Database::connect();
        return $pdo->query("SELECT id, name FROM technologies ORDER BY name ASC")->fetchAll();
    }
}
