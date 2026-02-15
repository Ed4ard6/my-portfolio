<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../models/AdminUserModel.php';

class AdminsController
{
    private AdminUserModel $model;

    public function __construct()
    {
        $this->model = new AdminUserModel();
    }

    private function ensureAuthenticated(): void
    {
        Auth::requireLogin();
    }

    public function index(): void
    {
        $this->ensureAuthenticated();

        View::render('admins/index', [
            'title' => 'Administradores',
            'heading' => 'Administradores',
            'admins' => $this->model->all(),
            'currentUser' => Auth::user(),
        ]);
    }

    public function create(): void
    {
        $this->ensureAuthenticated();

        View::render('admins/create', [
            'title' => 'Crear administrador',
            'heading' => 'Crear administrador',
            'errors' => [],
            'old' => ['username' => '', 'is_active' => '1'],
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
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['password_confirm'] ?? '');
        $isActive = ($_POST['is_active'] ?? '1') === '1';

        $errors = $this->validateInput($username, $password, $confirmPassword, null);

        if (!empty($errors)) {
            View::render('admins/create', [
                'title' => 'Crear administrador',
                'heading' => 'Crear administrador',
                'errors' => $errors,
                'old' => [
                    'username' => $username,
                    'is_active' => $isActive ? '1' : '0',
                ],
            ]);
            return;
        }

        $this->model->create($username, $password, $isActive);
        header('Location: /admins');
        exit;
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

        $errors = $this->validateInput($username, $password, $confirmPassword, $id, true);
        if (!empty($errors)) {
            $admin['username'] = $username;
            $admin['is_active'] = $isActive ? 1 : 0;

            View::render('admins/edit', [
                'title' => 'Editar administrador',
                'heading' => 'Editar administrador',
                'admin' => $admin,
                'errors' => $errors,
            ]);
            return;
        }

        $this->model->update($id, $username, $isActive);

        if ($password !== '') {
            $this->model->updatePassword($id, $password);
        }

        header('Location: /admins');
        exit;
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
            header('Location: /admins');
            exit;
        }

        if ((string)$admin['username'] === (string)Auth::user()) {
            http_response_code(400);
            echo 'No puedes eliminar tu propio usuario en sesión.';
            return;
        }

        $this->model->delete($id);
        header('Location: /admins');
        exit;
    }

    private function validateInput(
        string $username,
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
