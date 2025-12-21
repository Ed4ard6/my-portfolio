<?php

require_once __DIR__ . '/../../core/View.php';

class ProjectsController
{
    public function index()
    {
        $projects = [];

        if (isset($_SESSION['projects']) && is_array($_SESSION['projects'])) {
            $projects = $_SESSION['projects'];
        }

        View::render('projects/index', [
            'title' => 'Proyectos',
            'heading' => 'Mis Proyectos',
            'description' => 'Listado de proyectos (guardados en sesión, sin BD).',
            'projects' => $projects
        ]);
    }


    public function show($id = null)
    {
        if ($id === null) {
            http_response_code(400);
            echo "Falta el ID del proyecto.";
            return;
        }

        $id = (int) $id;

        $projects = $_SESSION['projects'] ?? [];

        $found = null;
        foreach ($projects as $p) {
            if ((int)($p['id'] ?? 0) === $id) {
                $found = $p;
                break;
            }
        }

        if ($found === null) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Proyecto no encontrado',
                'message' => "No existe un proyecto con id $id."
            ]);
            return;
        }

        View::render('projects/show', [
            'title' => $found['name'],
            'heading' => $found['name'],
            'description' => $found['description'],
            'tech' => $found['tech'] ?? 'Pendiente',
            'id' => $found['id']
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
        // Inicializar "tabla" en sesión si no existe
        if (!isset($_SESSION['projects']) || !is_array($_SESSION['projects'])) {
            $_SESSION['projects'] = [];
        }

        if (!isset($_SESSION['projects_next_id'])) {
            $_SESSION['projects_next_id'] = 1;
        }

        // Crear un nuevo proyecto
        $newProject = [
            'id' => $_SESSION['projects_next_id'],
            'name' => $name,
            'description' => $description,
            'tech' => 'Pendiente'
        ];

        // Guardarlo en sesión
        $_SESSION['projects'][] = $newProject;

        // Incrementar el ID para el siguiente
        $_SESSION['projects_next_id']++;

        $_SESSION['flash_success'] = '✅ Proyecto guardado con éxito (sin BD por ahora).';

        header('Location: /projects');
        exit;
    }
    public function reset()
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        echo "Método HTTP no permitido. Usa POST.";
        return;
    }

    unset($_SESSION['projects'], $_SESSION['projects_next_id']);

    $_SESSION['flash_success'] = '🧹 Proyectos borrados (sesión reiniciada).';
    header('Location: /projects');
    exit;
}

}
