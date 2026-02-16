<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../models/AdminUserModel.php';
require_once __DIR__ . '/../models/PasswordResetTokenModel.php';
require_once __DIR__ . '/../models/AdminAuditModel.php';

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

            if (($result['reason'] ?? '') === 'too_many_attempts') {
                $seconds = (int)($result['retry_after'] ?? 0);
                $minutes = max(1, (int)ceil($seconds / 60));
                $message = "Demasiados intentos fallidos. Intenta de nuevo en {$minutes} minuto(s).";
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

    public function forgot(): void
    {
        $userModel = new AdminUserModel();
        $resetModel = new PasswordResetTokenModel();

        View::render('auth/forgot', [
            'title' => 'Recuperar contraseña',
            'heading' => 'Recuperar contraseña de administrador',
            'error' => null,
            'success' => null,
            'emailSupported' => $userModel->supportsEmail(),
            'tokenSupported' => $resetModel->supported(),
        ]);
    }

    public function sendReset(): void
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

        $email = mb_strtolower(trim($_POST['email'] ?? ''));
        $userModel = new AdminUserModel();
        $resetModel = new PasswordResetTokenModel();

        if (!$userModel->supportsEmail() || !$resetModel->supported()) {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'Falta configuración de base de datos para recuperación por token (email o tabla de tokens).',
                'success' => null,
                'emailSupported' => $userModel->supportsEmail(),
                'tokenSupported' => $resetModel->supported(),
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'Ingresa un correo válido.',
                'success' => null,
                'emailSupported' => true,
                'tokenSupported' => true,
            ]);
            return;
        }

        $user = $userModel->findByEmail($email);
        if (!$user) {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'El correo ingresado no existe en administradores.',
                'success' => null,
                'emailSupported' => true,
                'tokenSupported' => true,
            ]);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $ok = $resetModel->create((int)$user['id'], $token, 30);

        if (!$ok) {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'No se pudo generar el token de recuperación.',
                'success' => null,
                'emailSupported' => true,
                'tokenSupported' => true,
            ]);
            return;
        }

        $baseUrl = rtrim((string)(getenv('APP_URL') ?: 'http://localhost'), '/');
        $link = $baseUrl . '/auth/reset/' . rawurlencode($token);

        $logLine = date('Y-m-d H:i:s') . ' | ' . $email . ' | ' . $link . PHP_EOL;
        $logFile = dirname(__DIR__, 2) . '/storage/password_reset_links.log';
        @file_put_contents($logFile, $logLine, FILE_APPEND);
        error_log('[AuthController] Token reset para ' . $email . ': ' . $link);

        $audit = new AdminAuditModel();
        $audit->log('password_reset_requested', (int)$user['id'], (int)$user['id'], 'Solicitud de recuperación de contraseña.');

        View::render('auth/forgot', [
            'title' => 'Recuperar contraseña',
            'heading' => 'Recuperar contraseña de administrador',
            'error' => null,
            'success' => 'Enlace generado. Revisa storage/password_reset_links.log en tu proyecto local.',
            'emailSupported' => true,
            'tokenSupported' => true,
        ]);
    }

    public function reset($token = null): void
    {
        $userModel = new AdminUserModel();
        $resetModel = new PasswordResetTokenModel();

        View::render('auth/reset', [
            'title' => 'Restablecer contraseña',
            'heading' => 'Restablecer contraseña',
            'token' => (string)($token ?? ''),
            'error' => null,
            'success' => null,
            'enabled' => $userModel->supportsEmail() && $resetModel->supported(),
        ]);
    }

    public function updatePasswordByToken(): void
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

        $token = trim($_POST['token'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirm'] ?? '');

        $userModel = new AdminUserModel();
        $resetModel = new PasswordResetTokenModel();
        $enabled = $userModel->supportsEmail() && $resetModel->supported();

        if (!$enabled) {
            View::render('auth/reset', [
                'title' => 'Restablecer contraseña',
                'heading' => 'Restablecer contraseña',
                'token' => $token,
                'error' => 'La recuperación por token no está habilitada en la base de datos.',
                'success' => null,
                'enabled' => false,
            ]);
            return;
        }

        if ($token === '' || mb_strlen($password) < 8 || $password !== $confirm) {
            View::render('auth/reset', [
                'title' => 'Restablecer contraseña',
                'heading' => 'Restablecer contraseña',
                'token' => $token,
                'error' => 'Datos inválidos. Verifica token y contraseña (mínimo 8 caracteres, confirmación igual).',
                'success' => null,
                'enabled' => true,
            ]);
            return;
        }

        $tokenRow = $resetModel->consumeValid($token);
        if (!$tokenRow) {
            View::render('auth/reset', [
                'title' => 'Restablecer contraseña',
                'heading' => 'Restablecer contraseña',
                'token' => $token,
                'error' => 'El token no es válido o ya expiró.',
                'success' => null,
                'enabled' => true,
            ]);
            return;
        }

        $adminId = (int)($tokenRow['admin_user_id'] ?? 0);
        $userModel->updatePassword($adminId, $password);

        $audit = new AdminAuditModel();
        $audit->log('password_reset_completed', $adminId, $adminId, 'Contraseña actualizada con token.');

        View::render('auth/reset', [
            'title' => 'Restablecer contraseña',
            'heading' => 'Restablecer contraseña',
            'token' => '',
            'error' => null,
            'success' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.',
            'enabled' => true,
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /');
        exit;
    }
}
