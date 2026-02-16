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
    private const RESET_TOKEN_MINUTES = 30;

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

        $identifier = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($identifier === '' || $password === '') {
            View::render('auth/login', [
                'title' => 'Iniciar sesión',
                'heading' => 'Acceso privado',
                'error' => 'Debes completar usuario o correo y contraseña.',
            ]);
            return;
        }

        try {
            $result = Auth::attempt($identifier, $password);
        } catch (Throwable $e) {
            error_log('[AuthController] Error autenticando: ' . $e->getMessage());
            $result = ['ok' => false, 'reason' => 'server_error'];
        }

        if (!(bool)($result['ok'] ?? false)) {
            $message = 'Credenciales inválidas o configuración de autenticación incompleta.';
            $remainingAttempts = (int)($result['remaining_attempts'] ?? 0);

            if (($result['reason'] ?? '') === 'invalid_credentials' && $remainingAttempts > 0) {
                $message = "Credenciales inválidas. Te quedan {$remainingAttempts} intento(s) antes del bloqueo temporal.";
            }

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

        $identifier = trim($_POST['identifier'] ?? '');
        $userModel = new AdminUserModel();
        $resetModel = new PasswordResetTokenModel();

        if (!$resetModel->supported()) {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'Falta configuración de base de datos para recuperación por token (email o tabla de tokens).',
                'success' => null,
                'tokenSupported' => $resetModel->supported(),
            ]);
            return;
        }

        if ($identifier === '') {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'Ingresa tu usuario o correo.',
                'success' => null,
                'tokenSupported' => true,
            ]);
            return;
        }

        $normalizedEmail = mb_strtolower($identifier);
        $isEmail = filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL) !== false;
        $user = $isEmail
            ? $userModel->findByEmail($normalizedEmail)
            : $userModel->findByUsername($identifier);

        if (!$user) {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'El usuario o correo ingresado no existe en administradores.',
                'success' => null,
                'tokenSupported' => true,
            ]);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $ok = $resetModel->create((int)$user['id'], $token, self::RESET_TOKEN_MINUTES);

        if (!$ok) {
            View::render('auth/forgot', [
                'title' => 'Recuperar contraseña',
                'heading' => 'Recuperar contraseña de administrador',
                'error' => 'No se pudo generar el token de recuperación.',
                'success' => null,
                'tokenSupported' => true,
            ]);
            return;
        }

        $baseUrl = rtrim((string)(getenv('APP_URL') ?: 'http://localhost'), '/');
        $link = $baseUrl . '/auth/reset/' . rawurlencode($token);

        $logLine = date('Y-m-d H:i:s') . ' | ' . ($user['email'] ?? $user['username'] ?? $identifier) . ' | ' . $link . PHP_EOL;
        $logFile = dirname(__DIR__, 2) . '/storage/password_reset_links.log';
        @file_put_contents($logFile, $logLine, FILE_APPEND);
        error_log('[AuthController] Token reset para ' . ($user['email'] ?? $user['username'] ?? $identifier) . ': ' . $link);

        $audit = new AdminAuditModel();
        $audit->log('password_reset_requested', (int)$user['id'], (int)$user['id'], 'Solicitud de recuperación de contraseña.');

        View::render('auth/forgot', [
            'title' => 'Recuperar contraseña',
            'heading' => 'Recuperar contraseña de administrador',
            'error' => null,
            'success' => 'Token generado correctamente. Expira en ' . self::RESET_TOKEN_MINUTES . ' minutos.',
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
            'enabled' => $resetModel->supported(),
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
        $enabled = $resetModel->supported();

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
                'error' => 'El token no es válido, ya fue usado o expiró (vigencia: ' . self::RESET_TOKEN_MINUTES . ' minutos).',
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
