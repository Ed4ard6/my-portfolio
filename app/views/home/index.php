<?php
$content = $content ?? [];
$projects = $projects ?? [];

$fullName = 'Eduardo Machacón';
$role = 'Desarrollador Backend · PHP & MVC';
$summary = 'Construyo sistemas desde cero con enfoque en seguridad, bases de datos relacionales y código limpio. Actualmente buscando una oportunidad como desarrollador junior backend/fullstack.';
$githubUrl = 'https://github.com/Ed4ard6';
$linkedinUrl = 'https://www.linkedin.com/in/eduardo-machacon/';
$email = 'ej.machacon@gmail.com';

$stack = [
    ['name' => 'PHP', 'icon' => '🐘'],
    ['name' => 'MySQL', 'icon' => '🗄️'],
    ['name' => 'MVC', 'icon' => '🏗️'],
    ['name' => 'HTML/CSS', 'icon' => '🎨'],
    ['name' => 'JavaScript', 'icon' => '⚡'],
    ['name' => 'Git', 'icon' => '🔀'],
];

$featuredProjects = [
    [
        'emoji' => '🖥️',
        'name' => 'Sistema de Portafolio con Panel Administrativo',
        'description' => 'Plataforma de gestión de proyectos con panel admin, CRUD de tecnologías, autenticación segura con CSRF, recuperación de contraseña por token, auditoría de acciones y migraciones de base de datos. Es la tercera versión completa tras iteraciones anteriores.',
        'tags' => ['PHP', 'MySQL', 'MVC', 'CSRF', 'Migraciones', 'Auditoría'],
        'demo_url' => 'https://eduardomachacon.com',
        'code_url' => 'https://github.com/Ed4ard6/my-portfolio',
        'demo_label' => 'Ver demo',
        'code_label' => 'Ver código',
    ],
    [
        'emoji' => '💰',
        'name' => 'Sistema de Control de Gastos Personales',
        'description' => 'Aplicación para registro de ingresos, gastos y deudas con proyecciones de ahorro, categorización y reportes visuales. Nació desde un flujo en Excel y evolucionó hacia una app web para automatizar decisiones financieras.',
        'tags' => ['PHP', 'MySQL', 'Dashboard', 'Reportes'],
        'demo_url' => null,
        'code_url' => null,
        'demo_label' => 'En desarrollo',
        'code_label' => 'Código privado',
    ],
    [
        'emoji' => '🧺',
        'name' => 'Sistema Web para Lavandería (Proyecto SENA)',
        'description' => 'Proyecto académico en equipo con módulos de usuarios, órdenes de servicio, facturación y reportes. Fue el primer sistema publicado por Eduardo en hosting propio y consolidó experiencia práctica en sesiones y arquitectura backend.',
        'tags' => ['PHP', 'MySQL', 'Sesiones', 'Facturación', 'Trabajo en equipo'],
        'demo_url' => null,
        'code_url' => null,
        'demo_label' => 'Offline',
        'code_label' => 'Proyecto académico',
    ],
];
?>

<section class="hero-section card card-pad">
    <div class="hero-grid">
        <div>
            <p class="hero-kicker">Disponible para oportunidades backend</p>
            <h1 class="hero-title">Hola, soy <?= htmlspecialchars($fullName) ?></h1>
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
            <div class="profile-avatar" aria-hidden="true">📸</div>
            <h2><?= htmlspecialchars($fullName) ?></h2>
            <p><?= htmlspecialchars($role) ?></p>
            <span class="badge badge--active"><span class="badge-dot"></span>Open to work</span>
            <p class="muted" style="margin-top:10px;">📍 Bogotá, Colombia</p>
        </aside>
    </div>
</section>

<section class="card card-pad home-section" id="sobre-mi">
    <h2 class="section-title">Sobre mí</h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['about_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section">
    <div class="row" style="align-items:center;">
        <h2 class="section-title">Stack técnico</h2>
        <a class="btn" href="/about">Ver historia completa</a>
    </div>

    <div class="tech-grid-custom" style="margin-top:14px;">
        <?php foreach ($stack as $item): ?>
            <span class="stack-pill stack-pill--square">
                <span><?= htmlspecialchars($item['icon']) ?></span>
                <?= htmlspecialchars($item['name']) ?>
            </span>
        <?php endforeach; ?>
    </div>
</section>

<section class="card card-pad home-section" id="proyectos">
    <h2 class="section-title">Proyectos destacados</h2>
    <p>Sistemas completos construidos desde cero con enfoque en impacto y mantenibilidad.</p>

    <div class="featured-projects-list" style="margin-top:18px;">
        <?php foreach ($featuredProjects as $project): ?>
            <article class="card story-project-card">
                <div class="project-image-placeholder" aria-hidden="true">
                    <span><?= htmlspecialchars($project['emoji']) ?></span>
                    <small>Screenshot aquí</small>
                </div>

                <div class="project-story-content card-pad">
                    <h3><?= htmlspecialchars($project['name']) ?></h3>
                    <p class="muted"><?= htmlspecialchars($project['description']) ?></p>

                    <div class="project-tags">
                        <?php foreach ($project['tags'] as $tag): ?>
                            <span class="stack-pill"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="project-links-inline">
                        <?php if (!empty($project['demo_url'])): ?>
                            <a class="btn btn-primary" href="<?= htmlspecialchars((string)$project['demo_url']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($project['demo_label']) ?></a>
                        <?php else: ?>
                            <span class="btn btn-secondary" aria-disabled="true"><?= htmlspecialchars($project['demo_label']) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($project['code_url'])): ?>
                            <a class="btn" href="<?= htmlspecialchars((string)$project['code_url']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($project['code_label']) ?></a>
                        <?php else: ?>
                            <span class="btn btn-secondary" aria-disabled="true"><?= htmlspecialchars($project['code_label']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($projects)): ?>
        <div style="margin-top:14px;">
            <a class="btn" href="/projects">Ver también proyectos administrables</a>
        </div>
    <?php endif; ?>
</section>

<section class="card card-pad home-section" id="contacto">
    <h2 class="section-title">Contacto</h2>
    <p style="margin-bottom:6px;">¿Hablamos? Estoy abierto a oportunidades junior backend/fullstack.</p>
    <p class="muted" style="margin-bottom:0;">
        📧 <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>
        · 💼 <a href="<?= htmlspecialchars($linkedinUrl) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
        · 🐙 <a href="<?= htmlspecialchars($githubUrl) ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
    </p>
</section>
