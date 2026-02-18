<div class="card card-pad page-shell" style="max-width:460px; margin:0 auto;">
    <h1 class="auth-title-center" style="margin:0;"><?= htmlspecialchars($heading ?? 'Recuperar contraseña') ?></h1>

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
            <label class="muted">Usuario o correo</label><br>
            <input class="input-compact" type="text" name="identifier" autocomplete="username" required>
        </div>

        <div class="form-actions form-actions-center">
            <button class="btn btn-primary" type="submit">Enviar enlace</button>
            <a class="btn btn-secondary" href="/auth/login">Volver al login</a>
        </div>
    </form>
</div>
