<div class="card card-pad error-page-card" style="max-width:760px; margin:0 auto; text-align:center;">
    <p class="error-code">404</p>
    <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Página no encontrada') ?></h1>
    <p class="muted" style="margin-top:10px;">
        <?= htmlspecialchars($message ?? 'La ruta que estás buscando no existe o fue movida.') ?>
    </p>

    <div style="margin-top:18px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
        <a class="btn btn-primary" href="/">Ir al inicio</a>
        <a class="btn btn-secondary" href="/projects">Ver proyectos</a>
        <button class="btn" type="button" onclick="window.history.back()">Volver atrás</button>
    </div>
</div>
