<?php
$content = $content ?? [];
$projects = $projects ?? [];

$fullName = 'Eduardo Machacón';
$role = 'Desarrollador Backend PHP · Especialista en MVC';
$summary = 'Construyo aplicaciones web seguras con PHP, MySQL y arquitectura MVC, enfocadas en rendimiento, mantenibilidad y experiencia de usuario.';
$githubUrl = 'https://github.com/';
$linkedinUrl = 'https://www.linkedin.com/';
$email = 'contacto@eduardomachacon.com';

$stack = [
    ['name' => 'PHP', 'icon' => '🐘'],
    ['name' => 'MySQL', 'icon' => '🛢️'],
    ['name' => 'JavaScript', 'icon' => '🟨'],
    ['name' => 'HTML5', 'icon' => '🌐'],
    ['name' => 'CSS3', 'icon' => '🎨'],
    ['name' => 'Git', 'icon' => '🧩'],
    ['name' => 'MVC', 'icon' => '🏗️'],
    ['name' => 'CSRF', 'icon' => '🛡️'],
];
?>

<section class="hero-section card card-pad">
    <div class="hero-grid">
        <div>
            <p class="hero-kicker">Disponible para oportunidades backend</p>
            <h1 class="hero-title"><?= htmlspecialchars($fullName) ?></h1>
            <p class="hero-role"><?= htmlspecialchars($role) ?></p>
            <p class="hero-summary"><?= htmlspecialchars($summary) ?></p>

            <div class="hero-actions">
                <a class="btn btn-primary" href="#proyectos">Ver proyectos</a>
                <a class="btn" href="#contacto">Contáctame</a>
            </div>

            <div class="hero-links">
                <a href="<?= htmlspecialchars($githubUrl) ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
                <span>•</span>
                <a href="<?= htmlspecialchars($linkedinUrl) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                <span>•</span>
                <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>
            </div>
        </div>

        <aside class="profile-card">
            <div class="profile-avatar" aria-hidden="true">EM</div>
            <h2><?= htmlspecialchars($fullName) ?></h2>
            <p><?= htmlspecialchars($role) ?></p>
            <span class="badge badge--active"><span class="badge-dot"></span>Open to work</span>
        </aside>
    </div>
</section>

<section class="card card-pad home-section" id="sobre-mi">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['about_title'] ?? 'Sobre mí')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['about_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section">
    <div class="row" style="align-items:center;">
        <h2 class="section-title"><?= htmlspecialchars((string)($content['skills_title'] ?? 'Tecnologías y habilidades')) ?></h2>
        <a class="btn" href="/about">Ver perfil completo</a>
    </div>

    <div class="stack-grid" style="margin-top:10px;">
        <?php foreach ($stack as $item): ?>
            <span class="stack-pill">
                <span><?= htmlspecialchars($item['icon']) ?></span>
                <?= htmlspecialchars($item['name']) ?>
            </span>
        <?php endforeach; ?>
    </div>

    <p style="white-space:pre-wrap; margin-top:12px;"><?= htmlspecialchars((string)($content['skills_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section" id="proyectos">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['projects_title'] ?? 'Proyectos destacados')) ?></h2>
    <p><?= htmlspecialchars((string)($content['projects_body'] ?? '')) ?></p>

    <?php if (empty($projects)): ?>
        <p class="muted">Próximamente verás aquí los proyectos destacados.</p>
    <?php else: ?>
        <div class="featured-projects" style="margin-top:12px;">
            <?php foreach (array_slice($projects, 0, 3) as $project): ?>
                <article class="card card-pad project-card-highlight">
                    <div class="project-shot" aria-hidden="true">
                        <span><?= htmlspecialchars(strtoupper(substr((string)($project['name'] ?? 'P'), 0, 2))) ?></span>
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
                            <strong><?= htmlspecialchars((string)($project['name'] ?? 'Proyecto')) ?></strong>
                            <span class="badge badge--<?= htmlspecialchars((string)($project['status'] ?? 'pending')) ?>">
                                <span class="badge-dot"></span>
                                <?= htmlspecialchars(ucfirst((string)($project['status'] ?? 'pending'))) ?>
                            </span>
                        </div>

                        <?php if (!empty($project['description'])): ?>
                            <p class="muted" style="margin:10px 0 0;"><?= htmlspecialchars((string)$project['description']) ?></p>
                        <?php else: ?>
                            <p class="muted" style="margin:10px 0 0;">Proyecto orientado a resolver un problema real con enfoque en experiencia de usuario y mantenibilidad.</p>
                        <?php endif; ?>

                        <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
                            <a class="btn" href="/projects/show/<?= urlencode((string)$project['id']) ?>">Ver detalle</a>
                            <a class="btn btn-primary" href="/projects/open/<?= urlencode((string)$project['id']) ?>">Ver demo</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:14px;">
            <a class="btn btn-primary" href="/projects">Ver todos los proyectos</a>
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
    <h2 class="section-title"><?= htmlspecialchars((string)($content['contact_title'] ?? 'Contacto')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['contact_body'] ?? '')) ?></p>
</section>
