<?php

declare(strict_types=1);

class SiteContentModel
{
    private const DEFAULTS = [
        'about_title' => 'Sobre mí',
        'about_body' => "Soy Eduardo Machacón, desarrollador backend enfocado en PHP y arquitectura MVC, con experiencia creando aplicaciones web seguras y mantenibles.\n\nHe desarrollado soluciones con autenticación, protección CSRF, auditoría de acciones y gestión de contenido, priorizando tanto la calidad del código como la experiencia final del usuario.\n\nMi objetivo es unirme a un equipo de producto donde pueda aportar en desarrollo backend, diseño de bases de datos y construcción de APIs, mientras sigo creciendo en buenas prácticas de arquitectura y escalabilidad.",
        'skills_title' => 'Tecnologías y habilidades',
        'skills_body' => "Frontend\n• HTML5\n• CSS3\n• JavaScript\n\nBackend\n• PHP\n• Arquitectura MVC\n• MySQL\n\nHerramientas\n• Git y GitHub\n• VS Code\n• Hosting y despliegue\n\nHabilidades blandas\n• Resolución de problemas\n• Comunicación\n• Aprendizaje continuo",
        'learning_title' => 'Actualmente aprendiendo',
        'learning_body' => "• Conceptos avanzados de JavaScript\n• Buenas prácticas de desarrollo backend\n• Diseño de bases de datos\n• Principios de código limpio\n• Inglés (nivel A2 en mejora continua)",
        'goal_title' => 'Mi objetivo',
        'goal_body' => 'Mi objetivo es unirme a un equipo de desarrollo donde pueda aportar, seguir aprendiendo y crecer profesionalmente mientras construyo soluciones de software con impacto.',
        'projects_title' => 'Proyectos',
        'projects_body' => 'Estos son proyectos orientados a resolver problemas reales con foco en seguridad, mantenibilidad y resultados medibles.',
        'contact_title' => 'Contacto',
        'contact_body' => '¿Hablamos? Escríbeme a contacto@eduardomachacon.com o conéctate por LinkedIn para oportunidades y colaboraciones.',
    ];

    private function filePath(): string
    {
        return dirname(__DIR__, 2) . '/storage/site_content.json';
    }

    public function get(): array
    {
        $path = $this->filePath();
        if (!is_readable($path)) {
            return self::DEFAULTS;
        }

        $raw = file_get_contents($path);
        if (!is_string($raw) || $raw === '') {
            return self::DEFAULTS;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return self::DEFAULTS;
        }

        return array_merge(self::DEFAULTS, $decoded);
    }

    public function save(array $content): bool
    {
        $data = [];
        foreach (self::DEFAULTS as $key => $value) {
            $data[$key] = trim((string)($content[$key] ?? $value));
            if ($data[$key] === '') {
                return false;
            }
        }

        $path = $this->filePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL) !== false;
    }
}
