<?php
$content = $content ?? [];
$projects = $projects ?? [];
?>

<section class="card card-pad home-section">
    <h1 style="margin-top:0;"><?= htmlspecialchars($heading ?? 'Bienvenido') ?></h1>
    <p class="muted">Junior Web Developer en transición profesional al desarrollo de software.</p>
</section>

<section class="card card-pad home-section" id="sobre-mi">
    <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['about_title'] ?? 'Sobre mí')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['about_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section">
    <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['skills_title'] ?? 'Tecnologías y habilidades')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['skills_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section">
    <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['learning_title'] ?? 'Actualmente aprendiendo')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['learning_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section">
    <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['goal_title'] ?? 'Mi objetivo')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['goal_body'] ?? '')) ?></p>
</section>

<section class="card card-pad home-section" id="proyectos">
    <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['projects_title'] ?? 'Proyectos')) ?></h2>
    <p><?= htmlspecialchars((string)($content['projects_body'] ?? '')) ?></p>

    <?php if (empty($projects)): ?>
        <p class="muted">Próximamente verás aquí los proyectos destacados.</p>
    <?php else: ?>
        <div class="grid" style="margin-top:10px;">
            <?php foreach ($projects as $project): ?>
                <article class="card card-pad">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
                        <strong><?= htmlspecialchars((string)($project['name'] ?? 'Proyecto')) ?></strong>
                        <span class="badge badge--<?= htmlspecialchars((string)($project['status'] ?? 'pending')) ?>">
                            <span class="badge-dot"></span>
                            <?= htmlspecialchars(ucfirst((string)($project['status'] ?? 'pending'))) ?>
                        </span>
                    </div>
                    <?php if (!empty($project['description'])): ?>
                        <p class="muted" style="margin-bottom:0;"><?= htmlspecialchars((string)$project['description']) ?></p>
                    <?php endif; ?>
                    <div style="margin-top:10px;">
                        <a class="btn" href="/projects/show/<?= urlencode((string)$project['id']) ?>">Ver detalle</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:12px;">
            <a class="btn btn-primary" href="/projects">Ver todos los proyectos</a>
        </div>
    <?php endif; ?>
</section>

<section class="card card-pad home-section" id="contacto">
    <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['contact_title'] ?? 'Contacto')) ?></h2>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['contact_body'] ?? '')) ?></p>
</section>
