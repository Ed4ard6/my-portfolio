<div class="card card-pad" style="max-width:680px; margin:0 auto;">
    <div class="row" style="align-items:center;">
        <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Crear administrador') ?></h1>
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

    <form method="POST" action="/admins/store" style="margin-top:16px; display:grid; gap:12px;">
        <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">

        <div>
            <label class="muted">Usuario</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="username" value="<?= htmlspecialchars((string)($old['username'] ?? '')) ?>" required>
        </div>

        <div>
            <label class="muted">Correo (para recuperación de contraseña)</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="email" name="email" value="<?= htmlspecialchars((string)($old['email'] ?? '')) ?>" required>
        </div>

        <div>
            <label class="muted">Contraseña (mínimo 8 caracteres)</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="password" name="password" required>
        </div>

        <div>
            <label class="muted">Confirmar contraseña</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="password" name="password_confirm" required>
        </div>

        <div>
            <label class="muted">Estado</label><br>
            <select class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" name="is_active">
                <option value="1" <?= (($old['is_active'] ?? '1') === '1') ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= (($old['is_active'] ?? '1') === '0') ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Guardar administrador</button>
            <a class="btn btn-secondary" href="/admins">Cancelar</a>
        </div>
    </form>
</div>
