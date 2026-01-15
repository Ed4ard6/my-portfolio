<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';

class AuthController
{
    public function login(): void
    {
        View::render('auth/login', [
            'title' => 'Iniciar sesión',
            'heading' => 'Acceso privado',
            'error' => null,
        ]);
    }

    public function authenticate(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            echo 'Método HTTP no permitido. Usa POST.';
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            View::render('auth/login', [
                'title' => 'Iniciar sesión',
                'heading' => 'Acceso privado',
                'error' => 'Debes completar usuario y contraseña.',
            ]);
            return;
        }

        if (!Auth::login($username, $password)) {
            View::render('auth/login', [
                'title' => 'Iniciar sesión',
                'heading' => 'Acceso privado',
                'error' => 'Credenciales inválidas.',
            ]);
            return;
        }

        header('Location: /projects');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /');
        exit;
    }
}
