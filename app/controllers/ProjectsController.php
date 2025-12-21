<?php

require_once __DIR__ . '/../../core/View.php';

class ProjectsController
{
    public function index()
    {
        View::render('projects/index', [
            'title' => 'Proyectos',
            'heading' => 'Mis Proyectos',
            'description' => 'Aquí podrás ver algunos de los proyectos que he desarrollado.'
        ]);
    }

    public function show()
    {
        View::render('projects/show', [
            'title' => 'Detalle del proyecto',
            'heading' => 'Proyecto destacado',
            'description' => 'Este es un ejemplo de “detalle” usando un método del controlador.'
        ]);
    }

    public function create()
    {
        View::render('projects/create', [
            'title' => 'Crear proyecto',
            'heading' => 'Crear nuevo proyecto'
        ]);
    }
}
