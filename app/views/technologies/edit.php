<div class="card card-pad form-compact" style="max-width:520px;">
    <h1 class="section-title-center" style="margin:0;"><?= htmlspecialchars($heading ?? 'Editar tecnología') ?></h1>

    <?php if (!empty($error)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(255,0,90,.25); background: rgba(255,0,90,.08);">
            <?= htmlspecialchars((string)$error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/technologies/update" style="margin-top:16px; display:grid; gap:12px;">
        <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$technology['id'] ?>">

        <div>
            <label class="muted">Nombre de la tecnología</label><br>
            <input class="input-compact" type="text" name="name" required value="<?= htmlspecialchars((string)$technology['name']) ?>">
        </div>

        <?php if (!empty($supportsActiveFlag)): ?>
            <div>
                <label class="muted">Estado</label><br>
                <select class="input-compact" name="is_active">
                    <option value="1" <?= ((int)($technology['is_active'] ?? 1) === 1) ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= ((int)($technology['is_active'] ?? 1) === 0) ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>
        <?php endif; ?>

        <div class="form-actions form-actions-center">
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
            <a class="btn btn-secondary" href="/technologies">Cancelar</a>
        </div>
    </form>
</div>
