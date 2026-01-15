<?php

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../models/ProjectModel.php';
require_once __DIR__ . '/../models/TechnologyModel.php';

class ProjectsController
{
    public function index()
    {
        $status = trim($_GET['status'] ?? '');

        $projectModel = new ProjectModel();

        if ($status !== '') {
            $projects = $projectModel->filterByStatus($status);
        } else {
            $projects = $projectModel->all();
        }

        View::render('projects/index', [
            'title' => 'Proyectos',
            'heading' => 'Mis Proyectos',
            'description' => 'Listado de proyectos (ahora desde la BD).',
            'projects' => $projects,
            'currentStatus' => $status
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

        $techNames = $projectModel->technologyNames($id);

        View::render('projects/show', [
            'title' => $project['name'],
            'heading' => $project['name'],
            'description' => $project['description'],
            'id' => $project['id'],
            'techNames' => $techNames,
            'status' => $project['status'] ?? 'pending',

        ]);
    }

    public function create()
    {
        // 1) El controller NO habla directo con la BD
        //    Usa el modelo correspondiente
        $techModel = new TechnologyModel();

        // 2) Pedimos TODAS las tecnologías existentes
        //    Esto devuelve algo como:
        //    [
        //      ['id'=>1,'name'=>'PHP'],
        //      ['id'=>2,'name'=>'MySQL'],
        //      ...
        //    ]
        $technologies = $techModel->all();

        // 3) Renderizamos la vista y le pasamos los datos
        View::render('projects/create', [
            'title' => 'Crear proyecto',
            'heading' => 'Crear proyecto',
            'technologies' => $technologies,
            // Por ahora no hay tecnologías seleccionadas
            'selectedTechIds' => [],
            // Para que no falle si vienes desde errores
            'errors' => [],
            'old' => []
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

        // technologies[] llega como array (o no llega si no marcaron nada)
        $techIds = $_POST['technologies'] ?? [];
        if (!is_array($techIds)) $techIds = [];

        // Normalizamos: int + sin duplicados
        $techIds = array_values(array_unique(array_map('intval', $techIds)));

        // 3) Validar datos (mantenemos tu lógica)
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

        // 4) Si hay errores, volvemos a mostrar el formulario con:
        //    - errores
        //    - old inputs
        //    - technologies (para pintar checkboxes)
        //    - selectedTechIds (para mantener checks)
        if (!empty($errors)) {
            $techModel = new TechnologyModel();
            $technologies = $techModel->all();

            View::render('projects/create', [
                'title' => 'Crear proyecto',
                'heading' => 'Crear proyecto',
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'description' => $description,
                ],
                'technologies' => $technologies,
                'selectedTechIds' => $techIds
            ]);
            return;
        }

        // 5) Guardar en BD (proyecto + tabla pivote)
        $projectModel = new ProjectModel();
        $newId = $projectModel->create($name, $description, $techIds);

        // 6) Redirigir al detalle del nuevo proyecto
        header("Location: /projects/show/$newId");
        exit;
    }

    public function archive($id = null)
    {
        if ($id === null) {
            http_response_code(400);
            echo "Falta el ID del proyecto.";
            return;
        }

        $id = (int)$id;

        $projectModel = new ProjectModel();
        $projectModel->archive($id);

        header("Location: /projects");
        exit;
    }

    public function archived()
    {
        $projectModel = new ProjectModel();
        $projects = $projectModel->archived();

        View::render('projects/archived', [
            'title' => 'Archivados',
            'heading' => 'Proyectos archivados',
            'projects' => $projects
        ]);
    }

    public function restore($id = null)
    {
        if ($id === null) {
            http_response_code(400);
            echo "Falta el ID del proyecto.";
            return;
        }

        $id = (int)$id;

        $projectModel = new ProjectModel();
        $projectModel->restore($id);

        header("Location: /projects/archived");
        exit;
    }
}
