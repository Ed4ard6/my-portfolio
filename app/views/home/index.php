<?php
$content = $content ?? [];
$projects = $projects ?? [];

$fullName = 'Eduardo Machacón';
$role = 'Desarrollador Backend · PHP & MVC';
$summary = 'Construyo sistemas desde cero con enfoque en seguridad, bases de datos relacionales y código limpio. Actualmente busco una oportunidad como desarrollador junior backend/fullstack.';
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

$aboutBody = trim((string)($content['about_body'] ?? ''));
$aboutSummary = $aboutBody;
if ($aboutBody !== '') {
    $parts = preg_split('/\R\R+/', $aboutBody);
    if (is_array($parts) && isset($parts[0])) {
        $aboutSummary = trim((string)$parts[0]);
    }
}
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
            <span class="badge badge--active"><span class="badge-dot"></span>Disponible para contratación</span>
            <p class="muted" style="margin-top:10px;">📍 Bogotá, Colombia</p>
        </aside>
    </div>
</section>

<section class="card card-pad home-section" id="sobre-mi">
    <div class="row" style="align-items:center;">
        <h2 class="section-title"><?= htmlspecialchars((string)($content['about_title'] ?? 'Sobre mí')) ?></h2>
        <a class="btn" href="/about">Ver historia completa</a>
    </div>

    <?php if ($aboutSummary !== ''): ?>
        <p style="white-space:pre-wrap;"><?= htmlspecialchars($aboutSummary) ?></p>
    <?php else: ?>
        <p class="muted">Actualiza tu historia profesional desde el panel de administración.</p>
    <?php endif; ?>
</section>

<section class="card card-pad home-section">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['skills_title'] ?? 'Stack técnico')) ?></h2>

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
    <p><?= htmlspecialchars((string)($content['projects_body'] ?? '')) ?></p>

    <?php if (empty($projects)): ?>
        <p class="muted" style="margin-top:12px;">Aún no hay proyectos destacados en la base de datos.</p>
    <?php else: ?>
        <div class="featured-projects-list" style="margin-top:18px;">
            <?php foreach (array_slice($projects, 0, 3) as $project): ?>
                <?php
                    $projectName = (string)($project['name'] ?? 'Proyecto');
                    $projectDescription = trim((string)($project['description'] ?? ''));
                    $projectStatus = (string)($project['status'] ?? 'pending');
                    $projectTech = trim((string)($project['technologies'] ?? ''));
                    $projectUrl = trim((string)($project['project_url'] ?? ''));
                ?>
                <article class="card story-project-card card-pad">
                    <div style="display:flex; justify-content:space-between; gap:8px; align-items:center; flex-wrap:wrap;">
                        <h3 style="margin:0;"><?= htmlspecialchars($projectName) ?></h3>
                        <span class="badge badge--<?= htmlspecialchars($projectStatus) ?>">
                            <span class="badge-dot"></span><?= htmlspecialchars(ucfirst($projectStatus)) ?>
                        </span>
                    </div>

                    <?php if ($projectDescription !== ''): ?>
                        <p class="muted" style="margin-top:10px;"><?= htmlspecialchars($projectDescription) ?></p>
                    <?php else: ?>
                        <p class="muted" style="margin-top:10px;">Proyecto registrado en portafolio. Agrega una descripción de impacto desde el panel admin.</p>
                    <?php endif; ?>

                    <?php if ($projectTech !== ''): ?>
                        <p style="margin-top:10px;"><strong>Tecnologías:</strong> <?= htmlspecialchars($projectTech) ?></p>
                    <?php endif; ?>

                    <div class="project-links-inline">
                        <a class="btn" href="/projects/show/<?= urlencode((string)($project['id'] ?? '')) ?>">Ver detalle</a>
                        <?php if ($projectUrl !== ''): ?>
                            <a class="btn btn-primary" href="<?= htmlspecialchars($projectUrl) ?>" target="_blank" rel="noopener noreferrer">Ver demo</a>
                        <?php else: ?>
                            <a class="btn btn-secondary" href="/projects/open/<?= urlencode((string)($project['id'] ?? '')) ?>">Abrir proyecto</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:14px;">
            <a class="btn" href="/projects">Ver todos los proyectos</a>
        </div>
    <?php endif; ?>
</section>

<section class="card card-pad home-section" id="contacto">
    <h2 class="section-title"><?= htmlspecialchars((string)($content['contact_title'] ?? 'Contacto')) ?></h2>
    <p style="margin-bottom:6px;">¿Hablamos? Estoy abierto a oportunidades junior backend/fullstack.</p>
    <p class="muted" style="margin-bottom:0; white-space:pre-wrap;"><?= htmlspecialchars((string)($content['contact_body'] ?? '')) ?></p>
</section>
