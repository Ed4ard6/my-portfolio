<?php

class View
{
    public static function render(string $view, array $data = []): void
    {
        // 1) Convertimos el array $data en variables sueltas para la vista
        // Ej: ['title' => 'Hola'] => $title = 'Hola'
        extract($data);

        // 2) Rutas internas
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../app/views/layouts/main.php';

        // 3) Validaciones básicas (si no existe, damos error claro)
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Vista no encontrada: " . htmlspecialchars($view);
            return;
        }

        if (!file_exists($layoutFile)) {
            http_response_code(500);
            echo "Layout no encontrado";
            return;
        }

        // 4) Capturamos el contenido de la vista (output buffering)
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // 5) Renderizamos el layout, y dentro de él usamos $content
        require $layoutFile;
    }
}
