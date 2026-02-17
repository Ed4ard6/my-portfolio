<?php

declare(strict_types=1);

class SiteContentModel
{
    private const DEFAULTS = [
        'about_title' => 'Sobre mí',
        'about_body' => "Soy Eduardo Machacón, desarrollador backend especializado en PHP, MySQL y arquitectura MVC.\n\nHe construido aplicaciones con autenticación segura, protección CSRF, auditoría de acciones administrativas y paneles de gestión de contenido. Trabajo con enfoque en mantenibilidad, calidad de código y resolución de problemas reales.\n\nActualmente busco mi primera oportunidad profesional como desarrollador junior backend/fullstack, donde pueda aportar en producto mientras sigo fortaleciendo arquitectura de software y APIs.",
        'skills_title' => 'Tecnologías y habilidades',
        'skills_body' => "Frontend\n• HTML5\n• CSS3\n• JavaScript\n\nBackend\n• PHP\n• Arquitectura MVC\n• MySQL\n\nHerramientas\n• Git y GitHub\n• VS Code\n• Hosting y despliegue\n\nHabilidades blandas\n• Resolución de problemas\n• Comunicación\n• Aprendizaje continuo",
        'learning_title' => 'Actualmente aprendiendo',
        'learning_body' => "• APIs RESTful en PHP\n• Buenas prácticas backend y código limpio\n• Diseño y optimización de bases de datos\n• Pruebas y calidad de software\n• Inglés técnico (en mejora continua)",
        'goal_title' => 'Mi objetivo',
        'goal_body' => 'Mi objetivo es unirme a un equipo de desarrollo donde pueda aportar, seguir aprendiendo y crecer profesionalmente mientras construyo soluciones de software con impacto.',
        'projects_title' => 'Proyectos',
        'projects_body' => 'Estos son proyectos orientados a resolver problemas reales con foco en seguridad, mantenibilidad y resultados medibles.',
        'contact_title' => 'Contacto',
        'contact_body' => '📧 ej.machacon@gmail.com\n💼 LinkedIn: https://www.linkedin.com/in/eduardo-machacon/\n🐙 GitHub: https://github.com/Ed4ard6',
        'profile_photo_url' => '',
        'featured_project_ids' => '',
    ];

    private const OPTIONAL_KEYS = [
        'profile_photo_url',
        'featured_project_ids',
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

    public function featuredProjectIds(?array $content = null): array
    {
        $source = $content ?? $this->get();
        $raw = (string)($source['featured_project_ids'] ?? '');
        $parts = array_filter(array_map('trim', explode(',', $raw)));
        $ids = [];

        foreach ($parts as $part) {
            if (ctype_digit($part)) {
                $ids[] = (int)$part;
            }
        }

        return array_values(array_unique($ids));
    }

    public function save(array $content): bool
    {
        $data = [];
        foreach (self::DEFAULTS as $key => $value) {
            $data[$key] = trim((string)($content[$key] ?? $value));
            if ($data[$key] === '' && !in_array($key, self::OPTIONAL_KEYS, true)) {
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
