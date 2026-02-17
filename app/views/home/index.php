<?php
$content = $content ?? [];
$projects = $projects ?? [];

$fullName = 'Eduardo Machacón';
$role = 'Desarrollador backend · PHP y MVC';
$summary = 'Construyo sistemas web con enfoque en seguridad, bases de datos relacionales y código mantenible. Busco aportar valor en un equipo que construya productos útiles para personas reales.';
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
?>

<section class="hero-section card card-pad">
    <div class="hero-grid">
        <div>
            <p class="hero-kicker">Disponible para oportunidades en desarrollo backend</p>
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
            <span class="badge badge--active"><span class="badge-dot"></span>Disponible para trabajar</span>
            <p class="muted" style="margin-top:10px;">📍 Bogotá, Colombia</p>
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
    <h2 class="section-title"><?= htmlspecialchars((string)($content['projects_title'] ?? 'Proyectos destacados')) ?></h2>
    <p><?= htmlspecialchars((string)($content['projects_body'] ?? 'Proyectos obtenidos desde la base de datos y enriquecidos con su stack tecnológico.')) ?></p>

    <?php if (empty($projects)): ?>
        <p class="muted" style="margin-top:14px;">Aún no hay proyectos publicados.</p>
    <?php else: ?>
        <div class="featured-projects-list" style="margin-top:18px;">
            <?php foreach ($projects as $project): ?>
                <?php
                $projectId = (int)($project['id'] ?? 0);
                $projectName = (string)($project['name'] ?? 'Proyecto sin nombre');
                $projectDescription = trim((string)($project['description'] ?? ''));
                $projectStatus = (string)($project['status'] ?? 'pending');
                $projectUrl = trim((string)($project['project_url'] ?? ''));
                $tags = array_filter(array_map('trim', explode(',', (string)($project['technologies'] ?? ''))));
                ?>
                <article class="card story-project-card">
                    <div class="project-image-placeholder" aria-hidden="true">
                        <span>🚀</span>
                        <small><?= htmlspecialchars($projectStatus === 'active' ? 'Proyecto activo' : 'Proyecto en evolución') ?></small>
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
    <h2 class="section-title"><?= htmlspecialchars((string)($content['contact_title'] ?? 'Contacto')) ?></h2>
    <p style="margin-bottom:6px;">¿Hablamos? Estoy abierto a oportunidades como desarrollador backend o fullstack junior.</p>
    <p class="muted" style="margin-bottom:0;">
        📧 <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>
        · 💼 <a href="<?= htmlspecialchars($linkedinUrl) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
        · 🐙 <a href="<?= htmlspecialchars($githubUrl) ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
    </p>
</section>
