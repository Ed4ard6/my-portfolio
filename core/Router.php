<?php

class Router
{
    public static function run()
    {
        $url = $_GET['url'] ?? '/';
        $url = trim($url, '/');
        $parts = ($url === '') ? [] : explode('/', $url);

        if ($url === '') {
            $controller = 'HomeController';
            $method = 'index';
        } else {
            $parts = explode('/', $url);
            $controller = ucfirst($parts[0]) . 'Controller';
            $method = $parts[1] ?? 'index';
        }

        $controllerFile = __DIR__ . '/../app/controllers/' . $controller . '.php';

        if (!file_exists($controllerFile)) {
            http_response_code(404);
            echo "Controlador no encontrado";
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controller)) {
            http_response_code(404);
            echo "Clase controladora no encontrada: " . htmlspecialchars($controller);
            return;
        }


        if (!method_exists($controller, $method)) {
            http_response_code(404);
            echo "Método no encontrado";
            return;
        }

        $controllerInstance = new $controller();
        $params = array_slice($parts, 2);
        call_user_func_array([$controllerInstance, $method], $params);
    }
}
