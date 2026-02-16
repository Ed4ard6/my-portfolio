<?php

declare(strict_types=1);

class SiteContentModel
{
    private const DEFAULTS = [
        'about_title' => 'Sobre mí',
        'about_body' => 'Soy desarrollador y este portafolio reúne proyectos en los que he trabajado. Aquí puedes ver avances, tecnologías usadas y estado actual de cada proyecto.',
        'contact_title' => 'Contacto',
        'contact_body' => 'Puedes escribirme por correo a tu-email@dominio.com o por LinkedIn. También puedes dejar una descripción breve de tu proyecto y te responderé.',
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
        $data = [
            'about_title' => trim((string)($content['about_title'] ?? self::DEFAULTS['about_title'])),
            'about_body' => trim((string)($content['about_body'] ?? self::DEFAULTS['about_body'])),
            'contact_title' => trim((string)($content['contact_title'] ?? self::DEFAULTS['contact_title'])),
            'contact_body' => trim((string)($content['contact_body'] ?? self::DEFAULTS['contact_body'])),
        ];

        if ($data['about_title'] === '' || $data['about_body'] === '' || $data['contact_title'] === '' || $data['contact_body'] === '') {
            return false;
        }

        $path = $this->filePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL) !== false;
    }
}
