<div class="card card-pad">
    <div class="row">
        <h1 style="margin:0;"><?= htmlspecialchars($heading) ?></h1>
        <a class="btn" href="/projects">← Volver</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="card card-pad" style="margin-top:12px; border-color: rgba(255,0,90,.25); background: rgba(255,0,90,.08);">
            <strong>Hay errores:</strong>
            <ul style="margin:8px 0 0 18px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/projects/store" style="margin-top:14px;">
        <div style="margin-top:12px;">
            <label class="muted">Nombre del proyecto</label><br>
            <input
                class="card card-pad"
                style="width:100%; padding:10px 12px; border-radius:12px;"
                type="text"
                name="name"
                required
                value="<?= htmlspecialchars($old['name'] ?? '') ?>">
        </div>

        <div style="margin-top:12px;">
            <label class="muted">Descripción</label><br>
            <textarea
                class="card card-pad"
                style="width:100%; padding:10px 12px; border-radius:12px; min-height:110px;"
                name="description"
                rows="4"
                required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div style="margin-top:12px;">
            <div class="muted" style="margin-bottom:8px;"><strong>Tecnologías</strong></div>

            <?php $selectedTechIds = $selectedTechIds ?? []; ?>

            <?php if (empty($technologies)): ?>
                <p>No hay tecnologías registradas en la base de datos.</p>
            <?php else: ?>
                <div class="grid" style="grid-template-columns: 1fr; gap:8px;">
                    <?php foreach ($technologies as $t): ?>
                        <?php $checked = in_array((int)$t['id'], $selectedTechIds, true); ?>
                        <label class="card card-pad" style="display:flex; gap:10px; align-items:center; padding:10px 12px;">
                            <input
                                type="checkbox"
                                name="technologies[]"
                                value="<?= (int)$t['id'] ?>"
                                <?= $checked ? 'checked' : '' ?>>
                            <?= htmlspecialchars($t['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <a class="btn btn-secondary" href="/projects">Cancelar</a>
        </div>
    </form>
</div>