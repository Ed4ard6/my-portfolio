<?php

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../models/ProjectModel.php';
require_once __DIR__ . '/../models/TechnologyModel.php';

class ProjectsController
{
    public function index()
    {
        $projectModel = new ProjectModel();
        $projects = $projectModel->all();

        View::render('projects/index', [
            'title' => 'Proyectos',
            'heading' => 'Mis Proyectos',
            'description' => 'Listado de proyectos (ahora desde la BD).',
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

        $id = (int)$id;

        $projectModel = new ProjectModel();
        $project = $projectModel->find($id);

        if (!$project) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Proyecto no encontrado',
                'message' => "No existe un proyecto con id $id."
            ]);
            return;
        }

        // Para mostrar tecnologías bonitas en el detalle
        $techIds = $projectModel->technologyIds($id);

        View::render('projects/show', [
            'title' => $project['name'],
            'heading' => $project['name'],
            'description' => $project['description'],
            'tech' => (empty($techIds) ? 'Pendiente' : 'Asignadas'), // luego lo mejoramos a nombres
            'id' => $project['id']
        ]);
    }


    public function create()
    {
        View::render('projects/create', [
            'title' => 'Crear proyecto',
            'heading' => 'Crear nuevo proyecto'
        ]);
    }

    public function edit($id = null)
    {
        if ($id === null) {
            http_response_code(400);
            echo "Falta el ID del proyecto.";
            return;
        }

        $id = (int)$id;

        $projectModel = new ProjectModel();
        $techModel = new TechnologyModel();

        $project = $projectModel->find($id);
        if (!$project) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'heading' => 'Proyecto no encontrado',
                'message' => "No existe un proyecto con id $id."
            ]);
            return;
        }

        $technologies = $techModel->all();
        $selectedTechIds = $projectModel->technologyIds($id);

        View::render('projects/edit', [
            'title' => 'Editar proyecto',
            'heading' => 'Editar proyecto',
            'project' => $project,
            'technologies' => $technologies,
            'selectedTechIds' => $selectedTechIds
        ]);
    }

    public function update()
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        echo "Método HTTP no permitido. Usa POST.";
        return;
    }

    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $techIds = $_POST['technologies'] ?? [];
    if (!is_array($techIds)) $techIds = [];

    if ($id <= 0 || $name === '' || $description === '') {
        http_response_code(400);
        echo "Datos inválidos.";
        return;
    }

    $projectModel = new ProjectModel();
    $projectModel->update($id, $name, $description, $techIds);

    header("Location: /projects/show/$id");
    exit;
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
        $tech = trim($_POST['tech'] ?? '');

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
        if ($tech !== '' && mb_strlen($tech) < 2) {
            $errors[] = 'Si agregas tecnologías, escribe al menos 2 caracteres.';
        }

        // 4) Si hay errores, volvemos a mostrar el formulario con errores + datos previos
        if (!empty($errors)) {
            View::render('projects/create', [
                'title' => 'Crear proyecto',
                'heading' => 'Crear nuevo proyecto',
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'description' => $description,
                    'tech' => $tech
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
            'tech' => ($tech === '') ? 'Pendiente' : $tech
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
