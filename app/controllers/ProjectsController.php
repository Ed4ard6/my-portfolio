<?php

require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../models/ProjectModel.php';
require_once __DIR__ . '/../models/TechnologyModel.php';

class ProjectsController
{
    private function normalizeProjectUrl(?string $rawUrl): ?string
    {
        if ($rawUrl === null) {
            return null;
        }

        $url = trim($rawUrl);
        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $url;
    }

    private function isValidProjectUrl(?string $url): bool
    {
        if ($url === null) {
            return true;
        }

        return $this->normalizeProjectUrl($url) !== null;
    }

    private function ensureAuthenticated(): void
    {
        Auth::requireLogin();
    }

    private function renderProjectUnavailable(array $project, string $reason): void
    {
        http_response_code(200);

        $status = (string)($project['status'] ?? 'pending');
        $name = (string)($project['name'] ?? 'Proyecto');

        $message = 'Este proyecto aún no está disponible.';
        if ($reason === 'pending') {
            $message = 'Este proyecto aún no se ha iniciado. Cuando pase a activo tendrás el enlace disponible.';
        } elseif ($reason === 'missing_url') {
            $message = 'Este proyecto está en progreso, pero todavía no tiene un enlace público configurado.';
        } elseif ($reason === 'invalid_url') {
            $message = 'Este proyecto tiene un enlace configurado, pero no es válido. Revisa la URL desde el panel de edición.';
        }

        View::render('projects/unavailable', [
            'title' => 'Proyecto no disponible',
            'heading' => $name,
            'status' => $status,
            'message' => $message,
        ]);
    }

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
            'projectUrl' => $project['project_url'] ?? null,
            'id' => $project['id'],
            'techNames' => $techNames,
            'status' => $project['status'] ?? 'pending',

        ]);
    }


    public function open($id = null)
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
                'message' => "No existe un proyecto con id $id.",
            ]);
            return;
        }

        $status = (string)($project['status'] ?? 'pending');
        $rawProjectUrl = trim((string)($project['project_url'] ?? ''));
        $projectUrl = $this->normalizeProjectUrl($rawProjectUrl);

        if ($status === 'pending') {
            $this->renderProjectUnavailable($project, 'pending');
            return;
        }

        if ($rawProjectUrl === '') {
            $this->renderProjectUnavailable($project, 'missing_url');
            return;
        }

        if ($projectUrl === null) {
            $this->renderProjectUnavailable($project, 'invalid_url');
            return;
        }

        header('Location: ' . $projectUrl, true, 302);
        exit;
    }

    public function create()
    {
        $this->ensureAuthenticated();

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
        $technologies = $techModel->all(true);

        // 3) Renderizamos la vista y le pasamos los datos
        View::render('projects/create', [
            'title' => 'Crear proyecto',
            'heading' => 'Crear proyecto',
            'technologies' => $technologies,
            // Por ahora no hay tecnologías seleccionadas
            'selectedTechIds' => [],
            // Para que no falle si vienes desde errores
            'errors' => [],
            'old' => [],
            'supportsProjectUrl' => (new ProjectModel())->supportsProjectUrl(),
        ]);
    }

    public function edit($id = null)
    {
        $this->ensureAuthenticated();

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

        $technologies = $techModel->all(false);
        $selectedTechIds = $projectModel->technologyIds($id);

        View::render('projects/edit', [
            'title' => 'Editar proyecto',
            'heading' => 'Editar proyecto',
            'project' => $project,
            'technologies' => $technologies,
            'selectedTechIds' => $selectedTechIds,
            'supportsProjectUrl' => $projectModel->supportsProjectUrl(),
        ]);
    }

    public function update()
    {
        $this->ensureAuthenticated();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            echo "Método HTTP no permitido. Usa POST.";
            return;
        }

        if (!Csrf::validate($_POST[Csrf::fieldName()] ?? null)) {
            http_response_code(400);
            echo "Token CSRF inválido.";
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $rawProjectUrl = trim($_POST['project_url'] ?? '');
        $projectUrl = $rawProjectUrl !== '' ? $this->normalizeProjectUrl($rawProjectUrl) : null;
        $status = trim($_POST['status'] ?? 'pending');

        $techIds = $_POST['technologies'] ?? [];
        if (!is_array($techIds)) $techIds = [];

        if ($id <= 0 || $name === '' || $description === '') {
            http_response_code(400);
            echo "Datos inválidos.";
            return;
        }

        $allowedStatus = ['pending', 'active', 'completed'];
        if (!in_array($status, $allowedStatus, true)) {
            http_response_code(400);
            echo "Estado inválido.";
            return;
        }

        if ($rawProjectUrl !== '' && !$this->isValidProjectUrl($rawProjectUrl)) {
            http_response_code(400);
            echo "La URL del proyecto no es válida. Usa https://... o una ruta interna (ejemplo: /hangman).";
            return;
        }

        $projectModel = new ProjectModel();
        $projectModel->update($id, $name, $description, $projectUrl, $status, $techIds);

        header("Location: /projects/show/$id");
        exit;
    }

    public function updateStatus($id = null)
    {
        $this->ensureAuthenticated();

        // A) Asegurar que sea POST (seguridad + semántica)
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo "Método no permitido";
            return;
        }

        if (!Csrf::validate($_POST[Csrf::fieldName()] ?? null)) {
            http_response_code(400);
            echo "Token CSRF inválido.";
            return;
        }

        // B) Validar ID
        if ($id === null) {
            http_response_code(400);
            echo "Falta el ID del proyecto.";
            return;
        }

        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo "ID inválido.";
            return;
        }

        // C) Validar status permitido (NO incluye archived)
        $status = trim($_POST['status'] ?? '');
        $allowed = ['pending', 'active', 'completed'];

        if (!in_array($status, $allowed, true)) {
            http_response_code(400);
            echo "Status inválido.";
            return;
        }

        $projectModel = new ProjectModel();

        // D) Verificar que exista (y bloquear archived desde aquí también)
        $project = $projectModel->find($id);
        if (!$project) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'No encontrado',
                'message' => 'Proyecto no existe'
            ]);
            return;
        }

        if (($project['status'] ?? '') === 'archived') {
            http_response_code(400);
            echo "Este proyecto está archivado. Debes restaurarlo antes de cambiar el status.";
            return;
        }

        // E) Actualizar en BD vía Model
        $projectModel->updateStatus($id, $status);

        // F) Redirigir (PRG: evita reenvío al refrescar)
        header('Location: /projects/show/' . $id);
        exit;
    }

    public function store()
    {
        $this->ensureAuthenticated();

        // 1) Asegurarnos de que sea POST
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            echo "Método HTTP no permitido. Usa POST.";
            return;
        }

        if (!Csrf::validate($_POST[Csrf::fieldName()] ?? null)) {
            http_response_code(400);
            echo "Token CSRF inválido.";
            return;
        }

        // 2) Tomar datos del formulario
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $rawProjectUrl = trim($_POST['project_url'] ?? '');
        $projectUrl = $rawProjectUrl !== '' ? $this->normalizeProjectUrl($rawProjectUrl) : null;

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

        $allowedStatus = ['pending', 'active', 'completed'];
        if (!in_array($status, $allowedStatus, true)) {
            http_response_code(400);
            echo "Estado inválido.";
            return;
        }

        if ($rawProjectUrl !== '' && !$this->isValidProjectUrl($rawProjectUrl)) {
            $errors[] = 'La URL del proyecto debe ser http(s) o una ruta interna válida (ejemplo: /hangman).';
        }

        // 4) Si hay errores, volvemos a mostrar el formulario con:
        //    - errores
        //    - old inputs
        //    - technologies (para pintar checkboxes)
        //    - selectedTechIds (para mantener checks)
        if (!empty($errors)) {
            $techModel = new TechnologyModel();
            $technologies = $techModel->all(true);

            View::render('projects/create', [
                'title' => 'Crear proyecto',
                'heading' => 'Crear proyecto',
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'description' => $description,
                    'project_url' => $projectUrl,
                ],
                'technologies' => $technologies,
                'selectedTechIds' => $techIds,
                'supportsProjectUrl' => (new ProjectModel())->supportsProjectUrl(),
            ]);
            return;
        }

        // 5) Guardar en BD (proyecto + tabla pivote)
        $projectModel = new ProjectModel();
        $newId = $projectModel->create($name, $description, $projectUrl, $techIds);

        // 6) Redirigir al detalle del nuevo proyecto
        header("Location: /projects/show/$newId");
        exit;
    }

    public function archive($id = null)
    {
        $this->ensureAuthenticated();

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
        $this->ensureAuthenticated();

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
        $this->ensureAuthenticated();

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
