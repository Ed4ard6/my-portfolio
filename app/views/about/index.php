<?php
$isAdmin = !empty($isAdmin);
$completedProjects = $completedProjects ?? [];
$selectedFeaturedIds = $selectedFeaturedIds ?? [];
?>

<?php if (!$isAdmin): ?>
    <div class="card card-pad about-pro page-shell" style="max-width:980px; margin:0 auto;">
        <span class="badge badge--active"><span class="badge-dot"></span>Perfil profesional</span>
        <h1 style="margin-top:12px;">Sobre mí</h1>

        <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['about_full_body'] ?? $content['about_body'] ?? '')) ?></p>

        <hr class="sep">

        <section>
            <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['skills_title'] ?? 'Tecnologías y habilidades')) ?></h2>
            <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['skills_body'] ?? '')) ?></p>
        </section>

        <hr class="sep">

        <section>
            <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['goal_title'] ?? 'Mi objetivo')) ?></h2>
            <p style="white-space:pre-wrap; margin-bottom:0;"><?= htmlspecialchars((string)($content['goal_body'] ?? '')) ?></p>
        </section>
    </div>
<?php endif; ?>

<?php if ($isAdmin): ?>
    <div class="card card-pad page-shell" id="admin-content-panel" style="max-width:980px; margin:0 auto;">
        <div class="row admin-panel-header" style="align-items:center;">
            <h1 style="margin:0;">Editar contenido público</h1>
            <span class="badge badge--active"><span class="badge-dot"></span>Modo administrador</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="card card-pad" style="margin-top:12px; border-color: rgba(255,0,90,.25); background: rgba(255,0,90,.08);">
                <?= htmlspecialchars((string)$error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="card card-pad" style="margin-top:12px; border-color: rgba(16,185,129,.35); background: rgba(16,185,129,.10);">
                <?= htmlspecialchars((string)$success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/about/save" style="margin-top:14px; display:grid; gap:12px;">
            <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">

            <h2 style="margin:4px 0 0;">Sección principal (Hero)</h2>

            <div>
                <label class="muted">Texto superior</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="hero_kicker" required value="<?= htmlspecialchars((string)($content['hero_kicker'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Título principal</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="hero_greeting" required value="<?= htmlspecialchars((string)($content['hero_greeting'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Rol principal</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="hero_role" required value="<?= htmlspecialchars((string)($content['hero_role'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Resumen principal</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:120px; padding:10px 12px; border-radius:12px;" name="hero_summary" required><?= htmlspecialchars((string)($content['hero_summary'] ?? '')) ?></textarea>
            </div>
            <div class="row" style="gap:12px;">
                <div style="flex:1;">
                    <label class="muted">Texto botón primario</label><br>
                    <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="hero_primary_cta_label" required value="<?= htmlspecialchars((string)($content['hero_primary_cta_label'] ?? '')) ?>">
                </div>
                <div style="flex:1;">
                    <label class="muted">Texto botón secundario</label><br>
                    <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="hero_secondary_cta_label" required value="<?= htmlspecialchars((string)($content['hero_secondary_cta_label'] ?? '')) ?>">
                </div>
            </div>

            <h2 style="margin:6px 0 0;">Tarjeta de perfil</h2>
            <div>
                <label class="muted">Nombre del perfil</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="profile_name" required value="<?= htmlspecialchars((string)($content['profile_name'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Rol del perfil</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="profile_role" required value="<?= htmlspecialchars((string)($content['profile_role'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Texto badge del perfil</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="profile_badge" required value="<?= htmlspecialchars((string)($content['profile_badge'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Ubicación del perfil</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="profile_location" required value="<?= htmlspecialchars((string)($content['profile_location'] ?? '')) ?>">
            </div>

            <h2 style="margin:6px 0 0;">Contenido de secciones</h2>

            <div>
                <label class="muted">Título sobre mí</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="about_title" required value="<?= htmlspecialchars((string)($content['about_title'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Texto sobre mí (resumen para inicio)</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:120px; padding:10px 12px; border-radius:12px;" name="about_body" required><?= htmlspecialchars((string)($content['about_body'] ?? '')) ?></textarea>
            </div>
            <div>
                <label class="muted">Texto sobre mí (versión completa para /about)</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:180px; padding:10px 12px; border-radius:12px;" name="about_full_body" required><?= htmlspecialchars((string)($content['about_full_body'] ?? $content['about_body'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="muted">Título tecnologías y habilidades</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="skills_title" required value="<?= htmlspecialchars((string)($content['skills_title'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Texto tecnologías y habilidades</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:140px; padding:10px 12px; border-radius:12px;" name="skills_body" required><?= htmlspecialchars((string)($content['skills_body'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="muted">Título actualmente aprendiendo</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="learning_title" required value="<?= htmlspecialchars((string)($content['learning_title'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Texto actualmente aprendiendo</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:120px; padding:10px 12px; border-radius:12px;" name="learning_body" required><?= htmlspecialchars((string)($content['learning_body'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="muted">Título objetivo</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="goal_title" required value="<?= htmlspecialchars((string)($content['goal_title'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Texto objetivo</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:90px; padding:10px 12px; border-radius:12px;" name="goal_body" required><?= htmlspecialchars((string)($content['goal_body'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="muted">Título proyectos</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="projects_title" required value="<?= htmlspecialchars((string)($content['projects_title'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Texto proyectos</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:90px; padding:10px 12px; border-radius:12px;" name="projects_body" required><?= htmlspecialchars((string)($content['projects_body'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="muted">Título contacto</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="contact_title" required value="<?= htmlspecialchars((string)($content['contact_title'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Texto introductorio de contacto</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="contact_intro" required value="<?= htmlspecialchars((string)($content['contact_intro'] ?? '')) ?>">
            </div>
            <div>
                <label class="muted">Links de contacto (una línea por link)</label><br>
                <small class="muted">Usa este formato exacto: <code>tipo|etiqueta|valor</code>.</small><br>
                <small class="muted">Ejemplos:</small>
                <pre class="card card-pad" style="margin:8px 0 6px; white-space:pre-wrap;">correo|Correo|ej.machacon@gmail.com
linkedin|LinkedIn|https://www.linkedin.com/in/eduardo-machacon/
github|GitHub|https://github.com/Ed4ard6
whatsapp|WhatsApp|https://wa.me/573001112233</pre>
                <textarea class="card card-pad" style="width:100%; min-height:130px; padding:10px 12px; border-radius:12px; margin-top:6px;" name="contact_body" required><?= htmlspecialchars((string)($content['contact_body'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="muted">URL de foto de perfil (opcional)</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="profile_photo_url" placeholder="/img/profile-photo.jpg o https://..." value="<?= htmlspecialchars((string)($content['profile_photo_url'] ?? '')) ?>">
                <small class="muted">Puedes usar una ruta local dentro de <code>public/img</code> o una URL externa.</small>
            </div>

            <div>
                <label class="muted">Proyectos destacados del inicio (máximo 2, solo completados)</label>
                <?php if (empty($completedProjects)): ?>
                    <p class="muted" style="margin:8px 0 0;">No hay proyectos en estado completado para seleccionar.</p>
                <?php else: ?>
                    <div style="display:grid; gap:8px; margin-top:8px;">
                        <?php foreach ($completedProjects as $project): ?>
                            <?php $projectId = (int)($project['id'] ?? 0); ?>
                            <label class="card card-pad" style="display:flex; gap:10px; align-items:flex-start; padding:10px 12px;">
                                <input type="checkbox" name="featured_project_ids[]" value="<?= htmlspecialchars((string)$projectId) ?>" <?= in_array($projectId, $selectedFeaturedIds, true) ? 'checked' : '' ?>>
                                <span>
                                    <strong><?= htmlspecialchars((string)($project['name'] ?? 'Proyecto sin nombre')) ?></strong><br>
                                    <span class="muted">ID <?= htmlspecialchars((string)$projectId) ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn btn-primary" type="submit">Guardar contenido</button>
                <a class="btn btn-secondary" href="/">Ver inicio público</a>
            </div>
        </form>
    </div>
<?php endif; ?>
