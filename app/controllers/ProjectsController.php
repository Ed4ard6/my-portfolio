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
    public function store()
    {
        // 1) Asegurarnos de que sea POST
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            echo "Método HTTP no permitido. Usa POST.";
            return;
        }

        // 2) Tomar datos del formulario
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // 3) Validar datos
        $errors = [];

        if ($name === '') {
            $errors[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($name) < 3) {
            $errors[] = 'El nombre debe tener al menos 3 caracteres.';
        }

        if ($description === '') {
            $errors[] = 'La descripción es obligatoria.';
        } elseif (mb_strlen($description) < 10) {
            $errors[] = 'La descripción debe tener al menos 10 caracteres.';
        }

        // 4) Si hay errores, volvemos a mostrar el formulario con errores + datos previos
        if (!empty($errors)) {
            View::render('projects/create', [
                'title' => 'Crear proyecto',
                'heading' => 'Crear nuevo proyecto',
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'description' => $description
                ]
            ]);
            return;
        }

        // 5) Si todo está bien, guardamos un mensaje flash
        $_SESSION['flash_success'] = '✅ Proyecto guardado con éxito (sin BD por ahora).';

        // 6) Redirigimos a /projects
        header('Location: /projects');
        exit;
    }
}
