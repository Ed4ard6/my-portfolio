<h1><?= htmlspecialchars($heading) ?></h1>

<p class="muted">Aquí están los proyectos ocultos. Puedes restaurarlos cuando quieras.</p>

<?php $isAdmin = class_exists('Auth') && Auth::check(); ?>

<?php if (empty($projects)): ?>
    <p>No hay proyectos archivados.</p>
<?php else: ?>
    <div class="grid">
        <?php foreach ($projects as $p): ?>
            <?php $status = $p['status'] ?? 'archived'; ?>

            <div class="card card-pad">
                <div class="row">
                    <div>
                        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                            <strong><?= htmlspecialchars($p['name']) ?></strong>

                            <span class="badge badge--<?= htmlspecialchars($status) ?>">
                                <span class="badge-dot"></span>
                                <?= htmlspecialchars(ucfirst($status)) ?>
                            </span>
                        </div>

                        <div class="muted" style="margin-top:8px;">
                            <b>Tecnologías:</b>
                            <?= empty($p['technologies']) ? 'Pendiente' : htmlspecialchars($p['technologies']) ?>
                        </div>
                    </div>

                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a class="btn" href="/projects/show/<?= urlencode((string)$p['id']) ?>">Ver detalle</a>

                        <?php if ($isAdmin): ?>
                            <form method="POST" action="/projects/restore/<?= urlencode((string)$p['id']) ?>" style="display:inline;">
                                <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">
                                <button class="btn" type="submit" onclick="return confirm('¿Restaurar este proyecto?');">
                                    Restaurar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<p style="margin-top:16px;">
    <a class="btn btn-secondary" href="/projects">← Volver a proyectos</a>
</p>
