<div class="card card-pad" style="max-width:420px; margin:0 auto;">
    <div class="row" style="align-items:center;">
        <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Iniciar sesión') ?></h1>
        <a class="btn" href="/">← Volver</a>
    </div>

    <p class="muted" style="margin-top:8px;">
        Esta sección es solo para administrar tus proyectos.
    </p>

    <?php if (!empty($error)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(255,0,90,.25); background: rgba(255,0,90,.08);">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/auth/authenticate" style="margin-top:16px;">
        <div style="margin-top:12px;">
            <label class="muted">Usuario</label><br>
            <input
                class="card card-pad"
                style="width:100%; padding:10px 12px; border-radius:12px;"
                type="text"
                name="username"
                autocomplete="username"
                required>
        </div>

        <div style="margin-top:12px;">
            <label class="muted">Contraseña</label><br>
            <input
                class="card card-pad"
                style="width:100%; padding:10px 12px; border-radius:12px;"
                type="password"
                name="password"
                autocomplete="current-password"
                required>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Entrar</button>
            <a class="btn btn-secondary" href="/">Cancelar</a>
        </div>
    </form>
</div>
