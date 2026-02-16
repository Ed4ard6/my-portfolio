<?php

declare(strict_types=1);

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../models/SiteContentModel.php';
require_once __DIR__ . '/../models/ProjectModel.php';

class HomeController
{
    public function index(): void
    {
        $contentModel = new SiteContentModel();
        $projectModel = new ProjectModel();

        $projects = array_slice($projectModel->all(), 0, 4);

        View::render('home/index', [
            'title' => 'Inicio',
            'heading' => 'Bienvenido a mi Portafolio 👍🏽',
            'content' => $contentModel->get(),
            'projects' => $projects,
        ]);
    }
}
