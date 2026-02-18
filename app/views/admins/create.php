<div class="card card-pad form-compact" style="max-width:760px;">
    <h1 class="section-title-center" style="margin:0;"><?= htmlspecialchars($heading ?? 'Crear administrador') ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(255,0,90,.25); background: rgba(255,0,90,.08);">
            <ul style="margin:0; padding-left:18px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admins/store" style="margin-top:16px; display:grid; gap:12px;">
        <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">

        <div class="form-grid-two">
            <div>
                <label class="muted">Usuario</label><br>
                <input class="input-compact" type="text" name="username" value="<?= htmlspecialchars((string)($old['username'] ?? '')) ?>" required>
            </div>

            <?php if (!empty($emailSupported)): ?>
                <div>
                    <label class="muted">Correo</label><br>
                    <input class="input-compact" type="email" name="email" value="<?= htmlspecialchars((string)($old['email'] ?? '')) ?>" required>
                </div>
            <?php else: ?>
                <div class="card card-pad form-grid-span" style="border-color: rgba(245,158,11,.35); background: rgba(245,158,11,.10);">
                    La columna <code>email</code> aún no existe en <code>admin_users</code>. Puedes crear admins, pero para recuperación por token debes ejecutar el ALTER TABLE del README.
                </div>
            <?php endif; ?>

            <div>
                <label class="muted">Contraseña <span aria-hidden="true">*</span></label><br>
                <input class="input-compact" type="password" name="password" required>
                <small class="muted">Mínimo 8 caracteres.</small>
            </div>

            <div>
                <label class="muted">Confirmar contraseña <span aria-hidden="true">*</span></label><br>
                <input class="input-compact" type="password" name="password_confirm" required>
            </div>

            <div class="form-grid-span">
                <label class="muted">Estado</label><br>
                <select class="input-compact" name="is_active">
                    <option value="1" <?= (($old['is_active'] ?? '1') === '1') ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= (($old['is_active'] ?? '1') === '0') ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="form-actions form-actions-center">
            <button class="btn btn-primary" type="submit">Crear administrador</button>
            <a class="btn btn-secondary" href="/admins">Cancelar</a>
        </div>
    </form>
</div>
