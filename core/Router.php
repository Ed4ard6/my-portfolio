<?php

require_once __DIR__ . '/View.php';

class Router
{
    public static function run()
    {
        $url = self::resolveUrl();
        $parts = ($url === '') ? [] : explode('/', $url);

        if ($url === '') {
            $controller = 'HomeController';
            $method = 'index';
        } else {
            $controller = ucfirst($parts[0]) . 'Controller';
            $method = $parts[1] ?? 'index';
        }

        $controllerFile = __DIR__ . '/../app/controllers/' . $controller . '.php';

        if (!file_exists($controllerFile)) {
            self::renderNotFound('Controlador no encontrado', 'La ruta solicitada no existe.');
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controller)) {
            self::renderNotFound('Clase controladora no encontrada', 'No se pudo inicializar el controlador solicitado.');
            return;
        }

        if (!method_exists($controller, $method)) {
            self::renderNotFound('Método no encontrado', 'La acción solicitada no existe para esta ruta.');
            return;
        }

        $controllerInstance = new $controller();
        $params = array_slice($parts, 2);
        call_user_func_array([$controllerInstance, $method], $params);
    }

    private static function resolveUrl(): string
    {
        if (isset($_GET['url']) && is_string($_GET['url'])) {
            return trim($_GET['url'], '/');
        }

        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (!is_string($requestPath) || $requestPath === '') {
            return '';
        }

        $baseDir = trim((string) dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        if ($baseDir !== '' && str_starts_with($requestPath, '/' . $baseDir . '/')) {
            $requestPath = substr($requestPath, strlen($baseDir) + 2);
        } elseif ($baseDir !== '' && $requestPath === '/' . $baseDir) {
            $requestPath = '/';
        }

        return trim($requestPath, '/');
    }

    private static function renderNotFound(string $heading, string $message): void
    {
        http_response_code(404);
        View::render('errors/404', [
            'title' => '404 - No encontrado',
            'heading' => $heading,
            'message' => $message,
        ]);
    }
}
