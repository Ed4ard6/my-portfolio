<div class="card card-pad">
    <div class="row" style="align-items:center;">
        <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Administradores') ?></h1>
        <div style="display:flex; gap:10px;">
            <a class="btn" href="/projects">← Volver a proyectos</a>
            <a class="btn btn-primary" href="/admins/create">+ Nuevo admin</a>
        </div>
    </div>

    <p class="muted" style="margin-top:8px;">Solo los administradores autenticados pueden gestionar estos usuarios.</p>

    <?php if (empty($admins)): ?>
        <div class="card card-pad" style="margin-top:12px;">
            No hay administradores registrados.
        </div>
    <?php else: ?>
        <div class="grid" style="grid-template-columns:1fr; margin-top:12px;">
            <?php foreach ($admins as $admin): ?>
                <div class="card card-pad">
                    <div class="row" style="align-items:center;">
                        <div>
                            <strong><?= htmlspecialchars($admin['username']) ?></strong>
                            <div class="muted" style="margin-top:4px;">
                                Estado: <?= ((int)$admin['is_active'] === 1) ? 'Activo' : 'Inactivo' ?>
                                · Creado: <?= htmlspecialchars((string)($admin['created_at'] ?? '-')) ?>
                            </div>
                            <?php if (($currentUser ?? '') === $admin['username']): ?>
                                <div class="muted" style="margin-top:4px;">(Tu sesión actual)</div>
                            <?php endif; ?>
                        </div>

                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            <a class="btn" href="/admins/edit/<?= (int)$admin['id'] ?>">Editar</a>

                            <?php if (($currentUser ?? '') !== $admin['username']): ?>
                                <form method="POST" action="/admins/delete/<?= (int)$admin['id'] ?>" onsubmit="return confirm('¿Eliminar este administrador? Esta acción no se puede deshacer.');" style="margin:0;">
                                    <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">
                                    <button class="btn btn-danger" type="submit">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
