<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class AdminUserModel
{
    public function findByUsername(string $username): ?array
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare('
            SELECT id, username, password_hash, is_active
            FROM admin_users
            WHERE username = ?
            LIMIT 1
        ');
        $stmt->execute([$username]);

        $row = $stmt->fetch();
        return $row ?: null;
    }
}
