<?php

class HangmanController
{
    public function index(): void
    {
        require __DIR__ . '/../../proyectos/ahorcado/public/index.php';
    }

    public function assets($file = null): void
    {
        $allowed = [
            'game.css' => [
                'path' => __DIR__ . '/../../proyectos/ahorcado/public/css/game.css',
                'type' => 'text/css; charset=UTF-8',
            ],
        ];

        $name = is_string($file) ? basename($file) : '';

        if (!isset($allowed[$name]) || !is_file($allowed[$name]['path'])) {
            http_response_code(404);
            echo 'Asset no encontrado.';
            return;
        }

        header('Content-Type: ' . $allowed[$name]['type']);
        readfile($allowed[$name]['path']);
    }
}
