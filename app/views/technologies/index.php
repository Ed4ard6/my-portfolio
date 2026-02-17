<div class="card card-pad page-shell">
    <div class="page-header">
        <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Tecnologías') ?></h1>
        <a class="btn btn-primary" href="/technologies/create">➕ Nueva tecnología</a>
    </div>

    <?php if (empty($supportsActiveFlag)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(245,158,11,.35); background: rgba(245,158,11,.10);">
            La tabla <code>technologies</code> aún no tiene columna <code>is_active</code> o no hay permisos para crearla automáticamente.
            Puedes crear y editar nombres, pero no activar/inactivar hasta aplicar la migración.
        </div>
    <?php endif; ?>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin:12px 0;">
        <a class="btn <?= ($currentStatus ?? 'all') === 'all' ? 'btn-primary' : '' ?>" href="/technologies?status=all">Todas</a>
        <a class="btn <?= ($currentStatus ?? 'all') === 'active' ? 'btn-primary' : '' ?>" href="/technologies?status=active">Activas</a>
        <a class="btn <?= ($currentStatus ?? 'all') === 'inactive' ? 'btn-primary' : '' ?>" href="/technologies?status=inactive">Inactivas</a>
    </div>

    <?php if (empty($technologies)): ?>
        <p class="muted">No hay tecnologías registradas.</p>
    <?php else: ?>
        <div class="grid" style="grid-template-columns:1fr; gap:10px; margin-top:8px;">
            <?php foreach ($technologies as $tech): ?>
                <?php $isActive = (int)($tech['is_active'] ?? 1) === 1; ?>
                <div class="card card-pad" style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                    <div>
                        <strong><?= htmlspecialchars((string)$tech['name']) ?></strong>
                        <div class="muted" style="margin-top:6px;">Estado: <?= $isActive ? 'Activa' : 'Inactiva' ?></div>
                    </div>

                    <div class="card-actions">
                        <a class="btn" href="/technologies/edit/<?= urlencode((string)$tech['id']) ?>">Editar</a>

                        <?php if (!empty($supportsActiveFlag)): ?>
                            <form method="POST" action="/technologies/toggle/<?= urlencode((string)$tech['id']) ?>" style="display:inline;">
                                <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">
                                <input type="hidden" name="active" value="<?= $isActive ? '0' : '1' ?>">
                                <button class="btn <?= $isActive ? 'btn-danger' : 'btn-secondary' ?>" type="submit">
                                    <?= $isActive ? 'Desactivar' : 'Activar' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
