<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';

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

        if (!Csrf::validate($_POST[Csrf::fieldName()] ?? null)) {
            http_response_code(400);
            echo 'Token CSRF inválido.';
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

        try {
            $result = Auth::attempt($username, $password);
        } catch (Throwable $e) {
            error_log('[AuthController] Error autenticando: ' . $e->getMessage());
            $result = ['ok' => false, 'reason' => 'server_error'];
        }

        if (!(bool)($result['ok'] ?? false)) {
            $message = 'Credenciales inválidas o configuración de autenticación incompleta.';

            if (($result['reason'] ?? '') === 'inactive_user') {
                $message = 'Tu usuario administrador está inactivo. Contacta a otro administrador para reactivarlo.';
            }

            View::render('auth/login', [
                'title' => 'Iniciar sesión',
                'heading' => 'Acceso privado',
                'error' => $message,
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
