<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../models/TechnologyModel.php';

class TechnologiesController
{
    private function ensureAuthenticated(): void
    {
        Auth::requireLogin();
    }

    public function index(): void
    {
        $this->ensureAuthenticated();

        $status = trim($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $model = new TechnologyModel();
        $technologies = $model->all(false);

        if ($status === 'active') {
            $technologies = array_values(array_filter($technologies, static fn(array $t) => (int)$t['is_active'] === 1));
        } elseif ($status === 'inactive') {
            $technologies = array_values(array_filter($technologies, static fn(array $t) => (int)$t['is_active'] === 0));
        }

        View::render('technologies/index', [
            'title' => 'Tecnologías',
            'heading' => 'Gestión de tecnologías',
            'technologies' => $technologies,
            'currentStatus' => $status,
            'supportsActiveFlag' => $model->supportsActiveFlag(),
        ]);
    }

    public function create(): void
    {
        $this->ensureAuthenticated();

        $model = new TechnologyModel();

        View::render('technologies/create', [
            'title' => 'Nueva tecnología',
            'heading' => 'Crear tecnología',
            'error' => null,
            'old' => ['name' => '', 'is_active' => '1'],
            'supportsActiveFlag' => $model->supportsActiveFlag(),
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

        $name = trim($_POST['name'] ?? '');
        $isActive = ($_POST['is_active'] ?? '1') === '1';

        $model = new TechnologyModel();

        if ($name === '') {
            View::render('technologies/create', [
                'title' => 'Nueva tecnología',
                'heading' => 'Crear tecnología',
                'error' => 'El nombre es obligatorio.',
                'old' => ['name' => $name, 'is_active' => $isActive ? '1' : '0'],
                'supportsActiveFlag' => $model->supportsActiveFlag(),
            ]);
            return;
        }

        if ($model->nameExists($name)) {
            View::render('technologies/create', [
                'title' => 'Nueva tecnología',
                'heading' => 'Crear tecnología',
                'error' => 'Ya existe una tecnología con ese nombre.',
                'old' => ['name' => $name, 'is_active' => $isActive ? '1' : '0'],
                'supportsActiveFlag' => $model->supportsActiveFlag(),
            ]);
            return;
        }

        $model->create($name, $isActive);

        header('Location: /technologies');
        exit;
    }

    public function edit($id = null): void
    {
        $this->ensureAuthenticated();

        $id = (int)($id ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo 'ID inválido.';
            return;
        }

        $model = new TechnologyModel();
        $technology = $model->find($id);

        if (!$technology) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Tecnología no encontrada',
                'message' => "No existe una tecnología con id $id.",
            ]);
            return;
        }

        View::render('technologies/edit', [
            'title' => 'Editar tecnología',
            'heading' => 'Editar tecnología',
            'error' => null,
            'technology' => $technology,
            'supportsActiveFlag' => $model->supportsActiveFlag(),
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
        $name = trim($_POST['name'] ?? '');
        $isActive = ($_POST['is_active'] ?? '1') === '1';

        $model = new TechnologyModel();
        $technology = $model->find($id);

        if (!$technology) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Tecnología no encontrada',
                'message' => "No existe una tecnología con id $id.",
            ]);
            return;
        }

        if ($name === '') {
            $technology['name'] = $name;
            $technology['is_active'] = $isActive ? 1 : 0;

            View::render('technologies/edit', [
                'title' => 'Editar tecnología',
                'heading' => 'Editar tecnología',
                'error' => 'El nombre es obligatorio.',
                'technology' => $technology,
                'supportsActiveFlag' => $model->supportsActiveFlag(),
            ]);
            return;
        }

        if ($model->nameExists($name, $id)) {
            $technology['name'] = $name;
            $technology['is_active'] = $isActive ? 1 : 0;

            View::render('technologies/edit', [
                'title' => 'Editar tecnología',
                'heading' => 'Editar tecnología',
                'error' => 'Ya existe una tecnología con ese nombre.',
                'technology' => $technology,
                'supportsActiveFlag' => $model->supportsActiveFlag(),
            ]);
            return;
        }

        $model->update($id, $name, $isActive);

        header('Location: /technologies');
        exit;
    }

    public function toggle($id = null): void
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

        $id = (int)($id ?? 0);
        $active = ($_POST['active'] ?? '1') === '1';

        if ($id <= 0) {
            http_response_code(400);
            echo 'ID inválido.';
            return;
        }

        $model = new TechnologyModel();
        $technology = $model->find($id);

        if (!$technology) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Tecnología no encontrada',
                'message' => "No existe una tecnología con id $id.",
            ]);
            return;
        }

        $model->setActive($id, $active);

        header('Location: /technologies');
        exit;
    }
}
