<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../models/SiteContentModel.php';

class AboutController
{
    public function index(): void
    {
        $model = new SiteContentModel();

        View::render('about/index', [
            'title' => 'Sobre mí',
            'heading' => 'Sobre mí',
            'error' => null,
            'success' => null,
            'content' => $model->get(),
            'isAdmin' => Auth::check(),
        ]);
    }

    public function save(): void
    {
        Auth::requireLogin();

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

        $model = new SiteContentModel();

        $payload = [
            'about_title' => $_POST['about_title'] ?? '',
            'about_body' => $_POST['about_body'] ?? '',
            'skills_title' => $_POST['skills_title'] ?? '',
            'skills_body' => $_POST['skills_body'] ?? '',
            'learning_title' => $_POST['learning_title'] ?? '',
            'learning_body' => $_POST['learning_body'] ?? '',
            'goal_title' => $_POST['goal_title'] ?? '',
            'goal_body' => $_POST['goal_body'] ?? '',
            'projects_title' => $_POST['projects_title'] ?? '',
            'projects_body' => $_POST['projects_body'] ?? '',
            'contact_title' => $_POST['contact_title'] ?? '',
            'contact_body' => $_POST['contact_body'] ?? '',
        ];

        $ok = $model->save($payload);

        View::render('about/index', [
            'title' => 'Sobre mí',
            'heading' => 'Sobre mí',
            'error' => $ok ? null : 'No se pudo guardar. Verifica que todos los campos estén completos y que la carpeta storage tenga permisos de escritura.',
            'success' => $ok ? 'Contenido actualizado correctamente.' : null,
            'content' => $ok ? $model->get() : array_merge($model->get(), $payload),
            'isAdmin' => true,
        ]);
    }
}
