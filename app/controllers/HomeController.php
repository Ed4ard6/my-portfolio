<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../models/SiteContentModel.php';
require_once __DIR__ . '/../models/ProjectModel.php';
require_once __DIR__ . '/../models/TechnologyModel.php';

class HomeController
{
    private function parseFeaturedProjectIds(string $raw): array
    {
        $parts = array_filter(array_map('trim', explode(',', $raw)));
        $ids = [];

        foreach ($parts as $part) {
            if (ctype_digit($part)) {
                $ids[] = (int)$part;
            }
        }

        return array_values(array_unique($ids));
    }

    public function index(): void
    {
        $contentModel = new SiteContentModel();
        $projectModel = new ProjectModel();
        $technologyModel = new TechnologyModel();

        $content = $contentModel->get();
        $projects = [];
        $technologies = [];

        try {
            $allProjects = $projectModel->all();
            $completedProjects = array_values(array_filter(
                $allProjects,
                static fn(array $project): bool => (($project['status'] ?? '') === 'completed')
            ));

            $featuredIds = $this->parseFeaturedProjectIds((string)($content['featured_project_ids'] ?? ''));

            if (!empty($featuredIds)) {
                $byId = [];
                foreach ($completedProjects as $project) {
                    $id = (int)($project['id'] ?? 0);
                    if ($id > 0) {
                        $byId[$id] = $project;
                    }
                }

                foreach ($featuredIds as $id) {
                    if (isset($byId[$id])) {
                        $projects[] = $byId[$id];
                    }
                }
            }

            if (empty($projects)) {
                $projects = $completedProjects;
            }

            $projects = array_slice($projects, 0, 2);
        } catch (Throwable $e) {
            $projects = [];
        }

        try {
            $technologies = $technologyModel->all(true);
        } catch (Throwable $e) {
            $technologies = [];
        }

        View::render('home/index', [
            'title' => 'Eduardo Machacón | Desarrollador backend en PHP',
            'heading' => 'Portafolio profesional',
            'content' => $content,
            'projects' => $projects,
            'technologies' => $technologies,
        ]);
    }
}
