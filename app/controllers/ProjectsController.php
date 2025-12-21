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

    public function show($id = null)
    {
        if ($id === null) {
            echo "Falta el ID del proyecto.";
            return;
        }

        View::render('projects/show', [
            'title' => 'Proyecto ' . $id,
            'heading' => 'Proyecto #' . $id,
            'description' => 'Mostrando el detalle del proyecto con id: ' . $id
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
