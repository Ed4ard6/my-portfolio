<div class="card card-pad page-shell" style="max-width:560px; margin:0 auto;">
    <h1 class="auth-title-center" style="margin:0;"><?= htmlspecialchars($heading ?? 'Restablecer contraseña') ?></h1>

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

    <?php if (empty($enabled)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(245,158,11,.35); background: rgba(245,158,11,.10);">
            Esta funcionalidad no está habilitada todavía por configuración de base de datos.
        </div>
    <?php endif; ?>

    <form method="POST" action="/auth/updatePasswordByToken" style="margin-top:16px; display:grid; gap:12px;">
        <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">

        <div>
            <label class="muted">Token</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="token" value="<?= htmlspecialchars((string)($token ?? '')) ?>" required>
        </div>

        <div>
            <label class="muted">Nueva contraseña</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="password" name="password" required>
        </div>

        <div>
            <label class="muted">Confirmar nueva contraseña</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="password" name="password_confirm" required>
        </div>

        <div class="form-actions form-actions-center">
            <button class="btn btn-primary" type="submit">Actualizar contraseña</button>
            <a class="btn btn-secondary" href="/auth/login">Cancelar</a>
        </div>
    </form>
</div>
