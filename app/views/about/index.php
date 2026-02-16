<?php $isAdmin = !empty($isAdmin); ?>

<div class="card card-pad" style="max-width:920px; margin:0 auto;">
    <h1 style="margin-top:0;"><?= htmlspecialchars((string)($content['about_title'] ?? 'Sobre mí')) ?></h1>
    <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['about_body'] ?? '')) ?></p>

    <hr class="sep">

    <section>
        <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['skills_title'] ?? 'Tecnologías y habilidades')) ?></h2>
        <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['skills_body'] ?? '')) ?></p>
    </section>

    <hr class="sep">

    <section>
        <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['learning_title'] ?? 'Actualmente aprendiendo')) ?></h2>
        <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['learning_body'] ?? '')) ?></p>
    </section>

    <hr class="sep">

    <section>
        <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['goal_title'] ?? 'Mi objetivo')) ?></h2>
        <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['goal_body'] ?? '')) ?></p>
    </section>

    <hr class="sep">

    <section id="contacto">
        <h2 style="margin-top:0;"><?= htmlspecialchars((string)($content['contact_title'] ?? 'Contacto')) ?></h2>
        <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['contact_body'] ?? '')) ?></p>
    </section>
</div>

<?php if ($isAdmin): ?>
    <div class="card card-pad" id="admin-content-panel" style="max-width:920px; margin:14px auto 0;">
        <div class="row" style="align-items:center;">
            <h2 style="margin:0;">Editar contenido público</h2>
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

            <div>
                <label class="muted">Título sobre mí</label><br>
                <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="about_title" required value="<?= htmlspecialchars((string)($content['about_title'] ?? '')) ?>">
            </div>

            <div>
                <label class="muted">Texto sobre mí</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:120px; padding:10px 12px; border-radius:12px;" name="about_body" required><?= htmlspecialchars((string)($content['about_body'] ?? '')) ?></textarea>
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
                <label class="muted">Texto contacto</label><br>
                <textarea class="card card-pad" style="width:100%; min-height:120px; padding:10px 12px; border-radius:12px;" name="contact_body" required><?= htmlspecialchars((string)($content['contact_body'] ?? '')) ?></textarea>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn btn-primary" type="submit">Guardar contenido</button>
                <a class="btn btn-secondary" href="/">Ver inicio público</a>
            </div>
        </form>
    </div>
<?php endif; ?>
