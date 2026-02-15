<div class="card card-pad">
    <div class="row" style="align-items:center;">
        <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Administradores') ?></h1>
        <div style="display:flex; gap:10px;">
            <a class="btn" href="/projects">← Volver a proyectos</a>
            <a class="btn btn-primary" href="/admins/create">+ Nuevo admin</a>
        </div>
    </div>

    <p class="muted" style="margin-top:8px;">Solo los administradores autenticados pueden gestionar estos usuarios.</p>

    <?php if (!empty($flash)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: <?= ($flash['type'] ?? '') === 'success' ? 'rgba(16,185,129,.30)' : 'rgba(255,0,90,.25)' ?>; background: <?= ($flash['type'] ?? '') === 'success' ? 'rgba(16,185,129,.10)' : 'rgba(255,0,90,.08)' ?>;">
            <?= htmlspecialchars((string)($flash['message'] ?? '')) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($emailSupported)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(245,158,11,.35); background: rgba(245,158,11,.10);">
            Aviso: tu tabla <code>admin_users</code> aún no tiene columna <code>email</code>. El acceso admin funciona, pero la recuperación por token no estará disponible hasta aplicar la migración SQL del README.
        </div>
    <?php endif; ?>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:12px;">
        <a class="btn <?= ($currentStatus ?? 'all') === 'all' ? 'btn-primary' : '' ?>" href="/admins?status=all">Todos</a>
        <a class="btn <?= ($currentStatus ?? '') === 'active' ? 'btn-primary' : '' ?>" href="/admins?status=active">Activos</a>
        <a class="btn <?= ($currentStatus ?? '') === 'inactive' ? 'btn-primary' : '' ?>" href="/admins?status=inactive">Inactivos</a>
    </div>

    <?php if (empty($admins)): ?>
        <div class="card card-pad" style="margin-top:12px;">
            No hay administradores registrados para este filtro.
        </div>
    <?php else: ?>
        <div class="grid" style="grid-template-columns:1fr; margin-top:12px;">
            <?php foreach ($admins as $admin): ?>
                <div class="card card-pad">
                    <div class="row" style="align-items:center;">
                        <div>
                            <strong><?= htmlspecialchars($admin['username']) ?></strong>
                            <span class="status-pill <?= ((int)$admin['is_active'] === 1) ? 'status-pill-active' : 'status-pill-inactive' ?>">
                                <?= ((int)$admin['is_active'] === 1) ? 'Activo' : 'Inactivo' ?>
                            </span>
                            <div class="muted" style="margin-top:4px;">
                                Correo: <?= htmlspecialchars((string)($admin['email'] ?? '-')) ?>
                            </div>
                            <div class="muted" style="margin-top:4px;">
                                Creado: <?= htmlspecialchars((string)($admin['created_at'] ?? '-')) ?>
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

    <div class="card card-pad" style="margin-top:14px;">
        <h3 style="margin:0 0 10px 0;">Historial reciente de cambios de administradores</h3>
        <?php if (empty($auditLogs)): ?>
            <div class="muted">No hay historial disponible (crea la tabla <code>admin_audit_logs</code> para activarlo).</div>
        <?php else: ?>
            <ul style="margin:0; padding-left:18px;">
                <?php foreach ($auditLogs as $log): ?>
                    <li style="margin-bottom:8px;">
                        <strong><?= htmlspecialchars((string)$log['action']) ?></strong>
                        · por <?= htmlspecialchars((string)$log['performed_by']) ?>
                        · admin objetivo #<?= htmlspecialchars((string)($log['target_admin_id'] ?? '-')) ?>
                        · <?= htmlspecialchars((string)$log['created_at']) ?>
                        <?php if (!empty($log['details'])): ?>
                            <div class="muted"><?= htmlspecialchars((string)$log['details']) ?></div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
