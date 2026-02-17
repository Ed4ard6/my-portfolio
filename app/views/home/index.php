<?php
$content = $content ?? [];
$projects = $projects ?? [];
$technologies = $technologies ?? [];

$fullName = (string)($content['profile_name'] ?? 'Eduardo Machacón');
$role = (string)($content['hero_role'] ?? 'Desarrollador backend · PHP y MVC');
$summary = (string)($content['hero_summary'] ?? '');
$heroKicker = (string)($content['hero_kicker'] ?? 'Disponible para oportunidades en desarrollo backend');
$heroTitle = (string)($content['hero_greeting'] ?? 'Hola, soy Eduardo Machacón');
$heroPrimaryCta = (string)($content['hero_primary_cta_label'] ?? 'Ver proyectos destacados');
$heroSecondaryCta = (string)($content['hero_secondary_cta_label'] ?? 'Contáctame');
$profileRole = (string)($content['profile_role'] ?? $role);
$profileBadge = (string)($content['profile_badge'] ?? 'Disponible para trabajar');
$profileLocation = (string)($content['profile_location'] ?? 'Bogotá, Colombia');

$contactTitleRaw = trim((string)($content['contact_title'] ?? ''));
$contactTitle = in_array(mb_strtolower($contactTitleRaw), ["let's connect", 'lets connect'], true)
    ? 'Contacto'
    : ($contactTitleRaw !== '' ? $contactTitleRaw : 'Contacto');

$profilePhotoUrl = trim((string)($content['profile_photo_url'] ?? ''));
$defaultProfilePhoto = '/img/profile-photo.jpg';
$defaultProfileAbsolutePath = dirname(__DIR__, 3) . '/public' . $defaultProfilePhoto;
if ($profilePhotoUrl === '' && is_file($defaultProfileAbsolutePath)) {
    $profilePhotoUrl = $defaultProfilePhoto;
}

$techIconMap = [
    'php' => 'devicon-php-plain',
    'mysql' => 'devicon-mysql-plain',
    'javascript' => 'devicon-javascript-plain',
    'js' => 'devicon-javascript-plain',
    'html' => 'devicon-html5-plain',
    'html/css' => 'devicon-html5-plain',
    'css' => 'devicon-css3-plain',
    'git' => 'devicon-git-plain',
    'go' => 'devicon-go-plain',
    'python' => 'devicon-python-plain',
    'laravel' => 'devicon-laravel-plain',
    'docker' => 'devicon-docker-plain',
    'react' => 'devicon-react-original',
    'node' => 'devicon-nodejs-plain',
    'nodejs' => 'devicon-nodejs-plain',
    'java' => 'devicon-java-plain',
    'c#' => 'devicon-csharp-plain',
    'c++' => 'devicon-cplusplus-plain',
    'c' => 'devicon-c-plain',
    'typescript' => 'devicon-typescript-plain',
    'postgresql' => 'devicon-postgresql-plain',
];

$iconByType = [
    'correo' => '<svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v.35l-10 6.25L2 6.35V6Zm0 2.69V18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8.69l-9.47 5.92a1 1 0 0 1-1.06 0L2 8.69Z"/></svg>',
    'email' => '<svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v.35l-10 6.25L2 6.35V6Zm0 2.69V18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8.69l-9.47 5.92a1 1 0 0 1-1.06 0L2 8.69Z"/></svg>',
    'linkedin' => '<svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.94 8.5A1.56 1.56 0 1 1 6.94 5.4a1.56 1.56 0 0 1 0 3.1ZM5.5 9.8h2.9V19H5.5V9.8Zm4.7 0H13v1.3h.04c.39-.74 1.35-1.52 2.78-1.52 2.98 0 3.53 1.96 3.53 4.5V19h-2.9v-4.35c0-1.04-.02-2.37-1.45-2.37-1.45 0-1.67 1.13-1.67 2.3V19h-2.9V9.8Z"/></svg>',
    'github' => '<svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 .5a12 12 0 0 0-3.79 23.39c.6.11.82-.26.82-.58v-2.05c-3.34.73-4.04-1.42-4.04-1.42-.55-1.38-1.33-1.75-1.33-1.75-1.1-.75.08-.74.08-.74 1.2.08 1.84 1.23 1.84 1.23 1.08 1.84 2.83 1.3 3.51 1 .1-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.9 0-1.3.47-2.36 1.24-3.2-.12-.3-.54-1.52.12-3.16 0 0 1.01-.33 3.3 1.22a11.44 11.44 0 0 1 6 0c2.3-1.55 3.3-1.22 3.3-1.22.66 1.64.24 2.86.12 3.16.77.84 1.24 1.9 1.24 3.2 0 4.58-2.8 5.6-5.48 5.9.43.37.82 1.1.82 2.23v3.31c0 .32.21.7.83.58A12 12 0 0 0 12 .5Z"/></svg>',
    'whatsapp' => '<svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.84 11.84 0 0 0 12.05 0C5.5 0 .15 5.34.15 11.9a11.8 11.8 0 0 0 1.61 5.95L0 24l6.33-1.65a11.9 11.9 0 0 0 5.72 1.46h.01c6.56 0 11.9-5.34 11.9-11.9a11.8 11.8 0 0 0-3.44-8.43ZM12.06 21.8h-.01a9.86 9.86 0 0 1-5.03-1.38l-.36-.21-3.75.98 1-3.66-.24-.38a9.82 9.82 0 0 1-1.51-5.25c0-5.45 4.44-9.89 9.9-9.89 2.64 0 5.12 1.03 6.98 2.9a9.8 9.8 0 0 1 2.9 6.98c0 5.46-4.44 9.9-9.88 9.9Z"/></svg>',
    'web' => '<svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm6.93 9h-3.02a15.9 15.9 0 0 0-1.06-4 8.03 8.03 0 0 1 4.08 4ZM12 4.04c.84 0 2.13 1.63 2.78 4.96H9.22C9.87 5.67 11.16 4.04 12 4.04ZM4.99 13h3.03a16.8 16.8 0 0 0 1.04 4 8.05 8.05 0 0 1-4.07-4Zm3.03-2H4.99a8.05 8.05 0 0 1 4.07-4 16.8 16.8 0 0 0-1.04 4Zm3.98 8.96c-.84 0-2.14-1.64-2.79-4.96h5.58c-.65 3.32-1.95 4.96-2.79 4.96ZM15.18 13H8.82a14.77 14.77 0 0 1 0-2h6.36a14.77 14.77 0 0 1 0 2Zm-.33 4a15.9 15.9 0 0 0 1.06-4h3.02a8.03 8.03 0 0 1-4.08 4Z"/></svg>',
];
$defaultIcon = $iconByType['web'];

$contactItems = [];
$rawContactBody = trim((string)($content['contact_body'] ?? ''));

if ($rawContactBody !== '') {
    $legacyTokens = preg_split('/\s*[·•]\s*/u', $rawContactBody) ?: [];
    $normalizedLines = [];

    foreach ($legacyTokens as $token) {
        $token = trim((string)$token);
        if ($token !== '') {
            $normalizedLines[] = $token;
        }
    }

    if (str_contains($rawContactBody, '\n') || str_contains($rawContactBody, '|')) {
        $normalizedLines = preg_split('/\r\n|\r|\n/', $rawContactBody) ?: [];
    }

    foreach ($normalizedLines as $line) {
        $line = trim((string)$line);
        if ($line === '') continue;

        $type = '';
        $label = '';
        $value = '';

        $parts = array_map('trim', explode('|', $line));
        if (count($parts) >= 3) {
            [$type, $label, $value] = [$parts[0], $parts[1], $parts[2]];
        } elseif (count($parts) === 2) {
            [$label, $value] = [$parts[0], $parts[1]];
        } else {
            $value = $line;
            $label = $line;
        }

        if (str_contains($value, ':')) {
            $chunks = array_map('trim', explode(':', $value, 2));
            if (count($chunks) === 2 && filter_var($chunks[1], FILTER_VALIDATE_URL)) {
                if ($label === $line || $label === $value) {
                    $label = $chunks[0];
                }
                $value = $chunks[1];
            }
        }

        $valueLower = mb_strtolower($value);
        $typeLower = mb_strtolower($type);

        if ($typeLower === '' && filter_var($value, FILTER_VALIDATE_EMAIL)) $typeLower = 'correo';
        if ($typeLower === '' && str_contains($valueLower, 'linkedin.com')) $typeLower = 'linkedin';
        if ($typeLower === '' && str_contains($valueLower, 'github.com')) $typeLower = 'github';
        if ($typeLower === '' && (str_contains($valueLower, 'wa.me') || str_contains($valueLower, 'whatsapp'))) $typeLower = 'whatsapp';
        if ($typeLower === '') $typeLower = 'web';

        $href = null;
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $href = 'mailto:' . $value;
        } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
            $href = $value;
        }

        if ($label === '') $label = $value;

        $contactItems[] = [
            'href' => $href,
            'label' => $label,
            'icon' => $iconByType[$typeLower] ?? $defaultIcon,
        ];
    }
}

if (empty($contactItems)) {
    $contactItems[] = [
        'href' => 'mailto:ej.machacon@gmail.com',
        'label' => 'ej.machacon@gmail.com',
        'icon' => $iconByType['correo'],
    ];
}
?>

<section class="hero-section card card-pad">
    <div class="hero-grid">
        <div>
            <p class="hero-kicker"><?= htmlspecialchars($heroKicker) ?></p>
            <h1 class="hero-title"><?= htmlspecialchars($heroTitle) ?></h1>
            <p class="hero-role"><?= htmlspecialchars($role) ?></p>
            <p class="hero-summary"><?= htmlspecialchars($summary) ?></p>

            <div class="hero-actions">
                <a class="btn btn-primary" href="#proyectos"><?= htmlspecialchars($heroPrimaryCta) ?></a>
                <a class="btn" href="#contacto"><?= htmlspecialchars($heroSecondaryCta) ?></a>
            </div>

            <div class="hero-links">
                <?php foreach (array_slice($contactItems, 0, 3) as $index => $item): ?>
                    <?php if (!empty($item['href'])): ?>
                        <a href="<?= htmlspecialchars((string)$item['href']) ?>" <?= str_starts_with((string)$item['href'], 'mailto:') ? '' : 'target="_blank" rel="noopener noreferrer"' ?>>
                            <?= $item['icon'] ?> <?= htmlspecialchars((string)$item['label']) ?>
                        </a>
                    <?php else: ?>
                        <span><?= $item['icon'] ?> <?= htmlspecialchars((string)$item['label']) ?></span>
                    <?php endif; ?>
                    <?php if ($index < min(2, count($contactItems) - 1)): ?><span>•</span><?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="profile-card">
            <div class="profile-avatar" aria-hidden="true">
                <?php if ($profilePhotoUrl !== ''): ?>
                    <img class="profile-avatar-image" src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="Foto de <?= htmlspecialchars($fullName) ?>">
                <?php else: ?>
                    <span>EM</span>
                <?php endif; ?>
            </div>
            <h2><?= htmlspecialchars($fullName) ?></h2>
            <p><?= htmlspecialchars($profileRole) ?></p>
            <span class="badge badge--active"><span class="badge-dot"></span><?= htmlspecialchars($profileBadge) ?></span>
            <p class="muted" style="margin-top:10px;">📍 <?= htmlspecialchars($profileLocation) ?></p>
        </aside>
    </div>
</section>

<section class="card card-pad home-section" id="sobre-mi">
    <div class="row" style="align-items:center;">
        <h2 class="section-title"><?= htmlspecialchars((string)($content['about_title'] ?? 'Sobre mí')) ?></h2>
        <a class="btn" href="/about">Ver perfil completo</a>
    </div>
    <p style="white-space:pre-wrap; margin-top:14px;"><?= htmlspecialchars((string)($content['about_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['skills_title'] ?? 'Tecnologías y habilidades')) ?></h2>
    <p class="muted" style="margin:8px 0 0;">Mostrando <?= count($technologies) ?> tecnologías activas registradas.</p>

    <div class="tech-grid-custom" style="margin-top:14px;">
        <?php if (empty($technologies)): ?>
            <span class="stack-pill stack-pill--square">Sin tecnologías registradas</span>
        <?php else: ?>
            <?php foreach ($technologies as $tech): ?>
                <?php
                $techName = (string)($tech['name'] ?? 'Tecnología');
                $techKey = mb_strtolower(trim($techName));
                $iconClass = $techIconMap[$techKey] ?? null;
                ?>
                <span class="stack-pill stack-pill--square">
                    <?php if ($iconClass !== null): ?>
                        <i class="<?= htmlspecialchars($iconClass) ?>" aria-hidden="true"></i>
                    <?php else: ?>
                        <span>•</span>
                    <?php endif; ?>
                    <?= htmlspecialchars($techName) ?>
                </span>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="card card-pad home-section" id="proyectos">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['projects_title'] ?? 'Proyectos destacados')) ?></h2>
    <p><?= htmlspecialchars((string)($content['projects_body'] ?? 'Aquí se muestran proyectos destacados.')) ?></p>

    <?php if (empty($projects)): ?>
        <p class="muted" style="margin-top:14px;">Aún no hay proyectos completados para mostrar en el inicio.</p>
    <?php else: ?>
        <div class="featured-projects-list" style="margin-top:18px;">
            <?php foreach ($projects as $project): ?>
                <?php
                $projectId = (int)($project['id'] ?? 0);
                $projectName = (string)($project['name'] ?? 'Proyecto sin nombre');
                $projectDescription = trim((string)($project['description'] ?? ''));
                $projectUrl = trim((string)($project['project_url'] ?? ''));
                $tags = array_filter(array_map('trim', explode(',', (string)($project['technologies'] ?? ''))));

                $projectImageUrl = '';
                foreach (["/img/projects/{$projectId}.webp", "/img/projects/{$projectId}.png", "/img/projects/{$projectId}.jpg", "/img/projects/{$projectId}.jpeg"] as $candidate) {
                    if (is_file(dirname(__DIR__, 3) . '/public' . $candidate)) {
                        $projectImageUrl = $candidate;
                        break;
                    }
                }
                ?>
                <article class="card story-project-card">
                    <div class="project-image-placeholder" aria-hidden="true">
                        <?php if ($projectImageUrl !== ''): ?>
                            <img class="project-cover-image" src="<?= htmlspecialchars($projectImageUrl) ?>" alt="Imagen del proyecto <?= htmlspecialchars($projectName) ?>">
                        <?php else: ?>
                            <span>🚀</span>
                            <small>Proyecto completado</small>
                        <?php endif; ?>
                    </div>

                    <div class="project-story-content card-pad">
                        <h3><?= htmlspecialchars($projectName) ?></h3>
                        <p class="muted"><?= htmlspecialchars($projectDescription !== '' ? $projectDescription : 'Este proyecto aún no tiene descripción registrada.') ?></p>

                        <div class="project-tags">
                            <?php if (empty($tags)): ?>
                                <span class="stack-pill">Sin tecnologías asociadas</span>
                            <?php else: ?>
                                <?php foreach ($tags as $tag): ?>
                                    <span class="stack-pill"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="project-links-inline">
                            <a class="btn" href="/projects/show/<?= urlencode((string)$projectId) ?>">Ver detalle</a>
                            <?php if ($projectUrl !== ''): ?>
                                <a class="btn btn-primary" href="<?= htmlspecialchars($projectUrl) ?>" target="_blank" rel="noopener noreferrer">Abrir proyecto</a>
                            <?php else: ?>
                                <span class="btn btn-secondary" aria-disabled="true">Sin URL pública</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:14px;">
            <a class="btn" href="/projects">Ver todos los proyectos</a>
        </div>
    <?php endif; ?>
</section>

<section class="card card-pad home-section">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['learning_title'] ?? 'Actualmente aprendiendo')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['learning_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['goal_title'] ?? 'Mi objetivo')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['goal_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section" id="contacto">
    <h2 class="section-title"><?= htmlspecialchars($contactTitle) ?></h2>
    <p style="margin-bottom:8px;">Canales de contacto:</p>
    <p class="muted contact-links" style="margin-bottom:0;">
        <?php foreach ($contactItems as $index => $item): ?>
            <?php if (!empty($item['href'])): ?>
                <a href="<?= htmlspecialchars((string)$item['href']) ?>" <?= str_starts_with((string)$item['href'], 'mailto:') ? '' : 'target="_blank" rel="noopener noreferrer"' ?>>
                    <?= $item['icon'] ?> <?= htmlspecialchars((string)$item['label']) ?>
                </a>
            <?php else: ?>
                <span><?= $item['icon'] ?> <?= htmlspecialchars((string)$item['label']) ?></span>
            <?php endif; ?>
            <?php if ($index < count($contactItems) - 1): ?> · <?php endif; ?>
        <?php endforeach; ?>
    </p>
</section>
