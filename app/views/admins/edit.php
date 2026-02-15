<div class="card card-pad" style="max-width:680px; margin:0 auto;">
    <div class="row" style="align-items:center;">
        <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Editar administrador') ?></h1>
        <a class="btn" href="/admins">← Volver</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(255,0,90,.25); background: rgba(255,0,90,.08);">
            <ul style="margin:0; padding-left:18px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admins/update" style="margin-top:16px; display:grid; gap:12px;" onsubmit="return confirm('¿Guardar cambios de este administrador?');">
        <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$admin['id'] ?>">

        <div>
            <label class="muted">Usuario</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="username" value="<?= htmlspecialchars((string)$admin['username']) ?>" required>
        </div>

        <div>
            <label class="muted">Correo (para recuperación de contraseña)</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="email" name="email" value="<?= htmlspecialchars((string)($admin['email'] ?? '')) ?>" required>
        </div>

        <div>
            <label class="muted">Nueva contraseña (opcional)</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="password" name="password">
            <div class="muted" style="margin-top:4px;">Déjalo vacío si no quieres cambiarla.</div>
        </div>

        <div>
            <label class="muted">Confirmar nueva contraseña</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="password" name="password_confirm">
        </div>

        <div>
            <label class="muted">Estado</label><br>
            <select class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" name="is_active">
                <option value="1" <?= ((int)$admin['is_active'] === 1) ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= ((int)$admin['is_active'] === 0) ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
            <a class="btn btn-secondary" href="/admins">Cancelar</a>
        </div>
    </form>
</div>
