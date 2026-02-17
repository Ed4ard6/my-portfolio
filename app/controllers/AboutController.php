<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../models/SiteContentModel.php';
require_once __DIR__ . '/../models/ProjectModel.php';

class AboutController
{
    private function selectedProjectIds(array $source): array
    {
        $ids = [];

        foreach ($source as $item) {
            if (is_string($item) && ctype_digit($item)) {
                $ids[] = (int)$item;
                continue;
            }

            if (is_int($item) && $item > 0) {
                $ids[] = $item;
            }
        }

        return array_values(array_unique($ids));
    }

    private function completedProjects(): array
    {
        $projectModel = new ProjectModel();
        $projects = $projectModel->all();

        return array_values(array_filter(
            $projects,
            static fn(array $project): bool => (($project['status'] ?? '') === 'completed')
        ));
    }

    public function index(): void
    {
        $model = new SiteContentModel();
        $content = $model->get();

        View::render('about/index', [
            'title' => 'Sobre mí',
            'heading' => 'Sobre mí',
            'error' => null,
            'success' => null,
            'content' => $content,
            'isAdmin' => Auth::check(),
            'completedProjects' => $this->completedProjects(),
            'selectedFeaturedIds' => $model->featuredProjectIds($content),
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
        $selectedFeaturedIds = $this->selectedProjectIds($_POST['featured_project_ids'] ?? []);

        if (count($selectedFeaturedIds) > 2) {
            View::render('about/index', [
                'title' => 'Sobre mí',
                'heading' => 'Sobre mí',
                'error' => 'Solo puedes seleccionar hasta 2 proyectos destacados para el inicio.',
                'success' => null,
                'content' => $model->get(),
                'isAdmin' => true,
                'completedProjects' => $this->completedProjects(),
                'selectedFeaturedIds' => $selectedFeaturedIds,
            ]);
            return;
        }

        $payload = [
            'hero_kicker' => $_POST['hero_kicker'] ?? '',
            'hero_greeting' => $_POST['hero_greeting'] ?? '',
            'hero_role' => $_POST['hero_role'] ?? '',
            'hero_summary' => $_POST['hero_summary'] ?? '',
            'hero_primary_cta_label' => $_POST['hero_primary_cta_label'] ?? '',
            'hero_secondary_cta_label' => $_POST['hero_secondary_cta_label'] ?? '',
            'profile_name' => $_POST['profile_name'] ?? '',
            'profile_role' => $_POST['profile_role'] ?? '',
            'profile_badge' => $_POST['profile_badge'] ?? '',
            'profile_location' => $_POST['profile_location'] ?? '',
            'about_title' => $_POST['about_title'] ?? '',
            'about_body' => $_POST['about_body'] ?? '',
            'about_full_body' => $_POST['about_full_body'] ?? '',
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
            'profile_photo_url' => $_POST['profile_photo_url'] ?? '',
            'featured_project_ids' => implode(',', $selectedFeaturedIds),
        ];

        $ok = $model->save($payload);

        View::render('about/index', [
            'title' => 'Sobre mí',
            'heading' => 'Sobre mí',
            'error' => $ok ? null : 'No se pudo guardar. Verifica que todos los campos estén completos y que la carpeta storage tenga permisos de escritura.',
            'success' => $ok ? 'Contenido actualizado correctamente.' : null,
            'content' => $ok ? $model->get() : array_merge($model->get(), $payload),
            'isAdmin' => true,
            'completedProjects' => $this->completedProjects(),
            'selectedFeaturedIds' => $selectedFeaturedIds,
        ]);
    }
}
