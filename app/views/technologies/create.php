<div class="card card-pad form-compact" style="max-width:560px;">
    <h1 class="section-title-center" style="margin:0;"><?= htmlspecialchars($heading ?? 'Crear tecnología') ?></h1>

    <?php if (!empty($error)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(255,0,90,.25); background: rgba(255,0,90,.08);">
            <?= htmlspecialchars((string)$error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/technologies/store" style="margin-top:16px; display:grid; gap:12px;">
        <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">

        <div>
            <label class="muted">Nombre de la tecnología</label><br>
            <input class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" type="text" name="name" required value="<?= htmlspecialchars((string)($old['name'] ?? '')) ?>">
        </div>

        <?php if (!empty($supportsActiveFlag)): ?>
            <div>
                <label class="muted">Estado</label><br>
                <select class="card card-pad" style="width:100%; padding:10px 12px; border-radius:12px;" name="is_active">
                    <option value="1" <?= (($old['is_active'] ?? '1') === '1') ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= (($old['is_active'] ?? '1') === '0') ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>
        <?php endif; ?>

        <div style="display:flex; gap:10px; flex-wrap:wrap; justify-content:center;">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <a class="btn btn-secondary" href="/technologies">Cancelar</a>
        </div>
    </form>
</div>
