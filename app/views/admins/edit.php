<div class="card card-pad form-compact" style="max-width:760px;">
    <h1 class="section-title-center" style="margin:0;"><?= htmlspecialchars($heading ?? 'Editar administrador') ?></h1>

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

        <div class="form-grid-two">
            <div>
                <label class="muted">Usuario</label><br>
                <input class="input-compact" type="text" name="username" value="<?= htmlspecialchars((string)$admin['username']) ?>" required>
            </div>

            <?php if (!empty($emailSupported)): ?>
                <div>
                    <label class="muted">Correo</label><br>
                    <input class="input-compact" type="email" name="email" value="<?= htmlspecialchars((string)($admin['email'] ?? '')) ?>" required>
                </div>
            <?php else: ?>
                <div class="card card-pad form-grid-span" style="border-color: rgba(245,158,11,.35); background: rgba(245,158,11,.10);">
                    Tu tabla <code>admin_users</code> no tiene la columna <code>email</code> todavía. El login funciona, pero ejecuta la migración del README para habilitar recuperación por token.
                </div>
            <?php endif; ?>

            <div>
                <label class="muted">Nueva contraseña (opcional)</label><br>
                <input class="input-compact" type="password" name="password">
                <div class="muted" style="margin-top:4px;">Mínimo 8 caracteres. Déjalo vacío si no quieres cambiarla.</div>
            </div>

            <div>
                <label class="muted">Confirmar nueva contraseña</label><br>
                <input class="input-compact" type="password" name="password_confirm">
            </div>

            <div class="form-grid-span">
                <label class="muted">Estado</label><br>
                <select class="input-compact" name="is_active">
                    <option value="1" <?= ((int)$admin['is_active'] === 1) ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= ((int)$admin['is_active'] === 0) ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="form-actions form-actions-center">
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
            <a class="btn btn-secondary" href="/admins">Cancelar</a>
        </div>
    </form>
</div>
