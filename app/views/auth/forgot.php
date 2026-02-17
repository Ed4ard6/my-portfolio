<div class="card card-pad page-shell" style="max-width:520px; margin:0 auto;">
    <div class="row" style="align-items:center;">
        <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Recuperar contraseña') ?></h1>
        <a class="btn" href="/auth/login">← Volver al login</a>
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

    <?php if (empty($tokenSupported)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(245,158,11,.35); background: rgba(245,158,11,.10);">
            Para activar esta sección debes tener la tabla <code>admin_password_resets</code>.
        </div>
    <?php endif; ?>

    <form method="POST" action="/auth/sendReset" style="margin-top:16px; display:grid; gap:12px;">
        <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">

        <div>
            <label class="muted">Usuario o correo del administrador</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="identifier" autocomplete="username" required>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Generar enlace de recuperación</button>
            <a class="btn btn-secondary" href="/auth/login">Cancelar</a>
        </div>
    </form>
</div>
