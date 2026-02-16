<?php

declare(strict_types=1);

class SiteContentModel
{
    private const DEFAULTS = [
        'about_title' => 'Sobre mí',
        'about_body' => "Soy Eduardo Machacón, Junior Web Developer enfocado en construir aplicaciones web y mejorar mis habilidades de desarrollo cada día.\n\nMi experiencia en soporte al cliente fortaleció mi pensamiento analítico, mis habilidades de comunicación y mi capacidad para entender las necesidades de los usuarios; competencias que hoy aplico al crear soluciones de software.\n\nTrabajo con flujos de Git, arquitectura MVC y fundamentos de desarrollo web mientras construyo proyectos personales para ganar experiencia práctica.\n\nActualmente estoy en transición hacia el desarrollo de software y buscando activamente mi primera oportunidad profesional como desarrollador.",
        'skills_title' => 'Tecnologías y habilidades',
        'skills_body' => "Frontend\n• HTML5\n• CSS3\n• JavaScript\n\nBackend\n• PHP\n• Arquitectura MVC\n• MySQL\n\nHerramientas\n• Git y GitHub\n• VS Code\n• Hosting y despliegue\n\nHabilidades blandas\n• Resolución de problemas\n• Comunicación\n• Aprendizaje continuo",
        'learning_title' => 'Actualmente aprendiendo',
        'learning_body' => "• Conceptos avanzados de JavaScript\n• Buenas prácticas de desarrollo backend\n• Diseño de bases de datos\n• Principios de código limpio\n• Inglés (nivel A2 en mejora continua)",
        'goal_title' => 'Mi objetivo',
        'goal_body' => 'Mi objetivo es unirme a un equipo de desarrollo donde pueda aportar, seguir aprendiendo y crecer profesionalmente mientras construyo soluciones de software con impacto.',
        'projects_title' => 'Proyectos',
        'projects_body' => 'Aquí encontrarás algunos de los proyectos en los que he trabajado y sigo mejorando.',
        'contact_title' => 'Contacto',
        'contact_body' => 'Escríbeme a tu-email@dominio.com o por LinkedIn para colaborar.',
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
