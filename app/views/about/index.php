<?php $isAdmin = !empty($isAdmin); ?>

<div class="card card-pad about-pro" style="max-width:980px; margin:0 auto;">
    <span class="badge badge--active"><span class="badge-dot"></span>Perfil profesional</span>
    <h1 style="margin-top:12px;">Sobre mí</h1>

    <p>Soy <strong>Eduardo Machacón</strong>, desarrollador backend radicado en Bogotá, Colombia.</p>
    <p>Mi transición a tecnología empezó desde una formación técnica en electrónica y evolucionó hacia el desarrollo de software. Tras estudiar en SENA y trabajar en proyectos reales, consolidé un enfoque fuerte en backend con PHP, bases de datos relacionales y arquitectura MVC.</p>
    <p>Me caracteriza la constancia para construir productos desde cero, iterar versiones y mejorar hasta lograr una solución sólida, segura y mantenible.</p>

    <hr class="sep">

    <section>
        <h2 style="margin-top:0;">Qué he construido</h2>
        <ul class="about-list">
            <li><strong>Portafolio MVC con panel administrativo:</strong> autenticación, protección CSRF, auditoría de acciones, migraciones y gestión de contenido.</li>
            <li><strong>Sistema de control de gastos (en desarrollo):</strong> ingresos, gastos, deudas, proyecciones de ahorro y reportes visuales.</li>
            <li><strong>Sistema web para lavandería (SENA):</strong> usuarios, órdenes, facturación y reportes; primer despliegue real en hosting propio.</li>
        </ul>
    </section>

    <hr class="sep">

    <section>
        <h2 style="margin-top:0;">Stack técnico</h2>
        <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)($content['skills_body'] ?? '')) ?></p>
    </section>

    <hr class="sep">

    <section>
        <h2 style="margin-top:0;">Qué busco</h2>
        <p>Estoy buscando mi primera oportunidad como <strong>desarrollador junior backend/fullstack</strong>, donde pueda aportar en desarrollo de producto, continuar creciendo en arquitectura de software y contribuir con soluciones reales para usuarios.</p>
        <p style="white-space:pre-wrap; margin-bottom:0;"><?= htmlspecialchars((string)($content['goal_body'] ?? '')) ?></p>
    </section>

    <hr class="sep">

    <section id="contacto" class="about-contact">
        <h2 style="margin-top:0;">Contacto</h2>
        <p style="margin:0 0 6px;">📧 <a href="mailto:ej.machacon@gmail.com">ej.machacon@gmail.com</a></p>
        <p style="margin:0 0 6px;">💼 <a href="https://www.linkedin.com/in/eduardo-machacon/" target="_blank" rel="noopener noreferrer">LinkedIn</a></p>
        <p style="margin:0;">🐙 <a href="https://github.com/Ed4ard6" target="_blank" rel="noopener noreferrer">GitHub</a></p>
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
