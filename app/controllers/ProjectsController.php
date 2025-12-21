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
            http_response_code(400);
            echo "Falta el ID del proyecto.";
            return;
        }

        // Simulamos una base de datos con un array
        $projects = [
            1 => [
                'name' => 'Portafolio MVC',
                'description' => 'Un portafolio hecho con PHP MVC puro.',
                'tech' => 'PHP, MVC, HTML, CSS'
            ],
            2 => [
                'name' => 'App de Finanzas',
                'description' => 'Aplicación para controlar ingresos y gastos en COP.',
                'tech' => 'PHP, MySQL, MVC'
            ],
            3 => [
                'name' => 'Landing Personal',
                'description' => 'Página simple para presentarme y mostrar contacto.',
                'tech' => 'HTML, CSS, JS'
            ],
        ];

        // Normalizamos a entero
        $id = (int) $id;

        // Validamos existencia
        if (!isset($projects[$id])) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Proyecto no encontrado',
                'message' => "No existe un proyecto con id $id."
            ]);
            return;
        }

        $project = $projects[$id];

        View::render('projects/show', [
            'title' => $project['name'],
            'heading' => $project['name'],
            'description' => $project['description'],
            'tech' => $project['tech'],
            'id' => $id
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
