<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../models/AdminUserModel.php';
require_once __DIR__ . '/../models/AdminAuditModel.php';

class AdminsController
{
    private AdminUserModel $model;
    private AdminAuditModel $auditModel;

    public function __construct()
    {
        $this->model = new AdminUserModel();
        $this->auditModel = new AdminAuditModel();
    }

    private function ensureAuthenticated(): void
    {
        Auth::requireLogin();
    }

    private function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    private function consumeFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return is_array($flash) ? $flash : null;
    }

    public function index(): void
    {
        $this->ensureAuthenticated();

        $status = trim((string)($_GET['status'] ?? 'all'));
        if (!in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $targetAdminId = (int)($_GET['target_admin_id'] ?? 0);
        if ($targetAdminId <= 0) {
            $targetAdminId = null;
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $adminsPagination = $this->model->paginate($page, 8, $status);

        $auditPage = max(1, (int)($_GET['audit_page'] ?? 1));
        $auditPagination = $this->auditModel->paginate($auditPage, 10, $targetAdminId);

        View::render('admins/index', [
            'title' => 'Administradores',
            'heading' => 'Administradores',
            'admins' => $adminsPagination['items'],
            'adminsPagination' => $adminsPagination,
            'currentUser' => Auth::user(),
            'currentStatus' => $status,
            'flash' => $this->consumeFlash(),
            'auditLogs' => $auditPagination['items'],
            'auditPagination' => $auditPagination,
            'targetAdminId' => $targetAdminId,
            'emailSupported' => $this->model->supportsEmail(),
        ]);
    }

    public function create(): void
    {
        $this->ensureAuthenticated();

        View::render('admins/create', [
            'title' => 'Crear administrador',
            'heading' => 'Crear administrador',
            'errors' => [],
            'old' => ['username' => '', 'email' => '', 'is_active' => '1'],
            'emailSupported' => $this->model->supportsEmail(),
        ]);
    }

    public function store(): void
    {
        $this->ensureAuthenticated();

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
        $email = mb_strtolower(trim($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['password_confirm'] ?? '');
        $isActive = ($_POST['is_active'] ?? '1') === '1';

        $errors = $this->validateInput($username, $email, $password, $confirmPassword, null);

        if (!empty($errors)) {
            View::render('admins/create', [
                'title' => 'Crear administrador',
                'heading' => 'Crear administrador',
                'errors' => $errors,
                'old' => [
                    'username' => $username,
                    'email' => $email,
                    'is_active' => $isActive ? '1' : '0',
                ],
                'emailSupported' => $this->model->supportsEmail(),
            ]);
            return;
        }

        try {
            $id = $this->model->create($username, $email, $password, $isActive);
            $this->auditModel->log('admin_created', Auth::userId(), $id, 'Se creó un nuevo administrador.');
            $this->setFlash('success', 'Administrador creado correctamente.');

            header('Location: /admins');
            exit;
        } catch (Throwable $e) {
            error_log('[AdminsController] Error creando admin: ' . $e->getMessage());

            View::render('admins/create', [
                'title' => 'Crear administrador',
                'heading' => 'Crear administrador',
                'errors' => ['No se pudo crear el administrador. Revisa datos y estructura de BD.'],
                'old' => [
                    'username' => $username,
                    'email' => $email,
                    'is_active' => $isActive ? '1' : '0',
                ],
                'emailSupported' => $this->model->supportsEmail(),
            ]);
            return;
        }
    }

    public function edit($id = null): void
    {
        $this->ensureAuthenticated();

        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo 'ID inválido.';
            return;
        }

        $admin = $this->model->findById($id);
        if (!$admin) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Administrador no encontrado',
                'message' => "No existe administrador con id $id.",
            ]);
            return;
        }

        View::render('admins/edit', [
            'title' => 'Editar administrador',
            'heading' => 'Editar administrador',
            'admin' => $admin,
            'errors' => [],
            'emailSupported' => $this->model->supportsEmail(),
        ]);
    }

    public function update(): void
    {
        $this->ensureAuthenticated();

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

        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = mb_strtolower(trim($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['password_confirm'] ?? '');
        $isActive = ($_POST['is_active'] ?? '1') === '1';

        $admin = $this->model->findById($id);
        if (!$admin) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Administrador no encontrado',
                'message' => "No existe administrador con id $id.",
            ]);
            return;
        }

        $errors = $this->validateInput($username, $email, $password, $confirmPassword, $id, true);
        if (!empty($errors)) {
            $admin['username'] = $username;
            $admin['email'] = $email;
            $admin['is_active'] = $isActive ? 1 : 0;

            View::render('admins/edit', [
                'title' => 'Editar administrador',
                'heading' => 'Editar administrador',
                'admin' => $admin,
                'errors' => $errors,
                'emailSupported' => $this->model->supportsEmail(),
            ]);
            return;
        }

        try {
            $this->model->update($id, $username, $email, $isActive);
            $this->auditModel->log('admin_updated', Auth::userId(), $id, 'Se actualizó username/email/estado.');

            if ($password !== '') {
                $this->model->updatePassword($id, $password);
                $this->auditModel->log('admin_password_updated', Auth::userId(), $id, 'Se cambió la contraseña del administrador.');
            }

            $this->setFlash('success', 'Cambios guardados correctamente.');
            header('Location: /admins');
            exit;
        } catch (Throwable $e) {
            error_log('[AdminsController] Error actualizando admin: ' . $e->getMessage());

            View::render('admins/edit', [
                'title' => 'Editar administrador',
                'heading' => 'Editar administrador',
                'admin' => [
                    'id' => $id,
                    'username' => $username,
                    'email' => $email,
                    'is_active' => $isActive ? 1 : 0,
                ],
                'errors' => ['No se pudieron guardar los cambios. Revisa datos y estructura de BD.'],
                'emailSupported' => $this->model->supportsEmail(),
            ]);
            return;
        }
    }

    public function delete($id = null): void
    {
        $this->ensureAuthenticated();

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

        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo 'ID inválido.';
            return;
        }

        $admin = $this->model->findById($id);
        if (!$admin) {
            $this->setFlash('error', 'No se encontró el administrador que intentabas desactivar.');
            header('Location: /admins');
            exit;
        }

        if ((string)$admin['username'] === (string)Auth::user()) {
            $this->setFlash('error', 'No puedes desactivar tu propio usuario en sesión.');
            header('Location: /admins');
            exit;
        }

        $this->model->setActive($id, false);
        $this->auditModel->log('admin_deactivated', Auth::userId(), $id, 'Se desactivó un administrador.');
        $this->setFlash('success', 'Administrador desactivado correctamente.');
        header('Location: /admins');
        exit;
    }

    private function validateInput(
        string $username,
        string $email,
        string $password,
        string $confirmPassword,
        ?int $ignoreId,
        bool $isUpdate = false
    ): array {
        $errors = [];

        if ($username === '') {
            $errors[] = 'El usuario es obligatorio.';
        } elseif (mb_strlen($username) < 3) {
            $errors[] = 'El usuario debe tener al menos 3 caracteres.';
        }

        if ($this->model->usernameExists($username, $ignoreId)) {
            $errors[] = 'Ese nombre de usuario ya existe.';
        }

        if ($this->model->supportsEmail()) {
            if ($email === '') {
                $errors[] = 'El correo es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El formato del correo no es válido.';
            } elseif ($this->model->emailExists($email, $ignoreId)) {
                $errors[] = 'Ese correo ya está registrado.';
            }
        }

        if (!$isUpdate || $password !== '') {
            if (mb_strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'La confirmación de contraseña no coincide.';
            }
        }

        return $errors;
    }
}
