<div class="card card-pad">
  <div class="row">
    <h1 style="margin:0;"><?= htmlspecialchars($heading) ?></h1>

    <?php
      $status = $project['status'] ?? 'pending';
      $statusLabelMap = ['pending' => 'Pendiente', 'active' => 'Activo', 'completed' => 'Completado'];
      $statusLabel = $statusLabelMap[$status] ?? ucfirst((string)$status);
    ?>
    <span class="badge badge--<?= htmlspecialchars($status) ?>">
      <span class="badge-dot"></span>
      <?= htmlspecialchars($statusLabel) ?>
    </span>
  </div>



  <?php if (empty($supportsProjectUrl)): ?>
    <div class="card card-pad" style="margin-top:12px; border-color: rgba(245,158,11,.35); background: rgba(245,158,11,.10);">
      Tu tabla <code>projects</code> todavía no tiene columna de enlace (<code>project_url</code>, <code>project_link</code> o <code>url</code>).
      Puedes editar el resto de campos, pero el link no se persistirá hasta aplicar la migración de BD.
    </div>
  <?php endif; ?>

  <form method="POST" action="/projects/update" style="margin-top:14px;">
    <input type="hidden" name="<?= htmlspecialchars(Csrf::fieldName()) ?>" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$project['id'] ?>">

    <div style="margin-top:12px;">
      <label class="muted">Nombre</label><br>
      <input
        class="card card-pad"
        style="width:100%; padding:10px 12px; border-radius:12px;"
        type="text"
        name="name"
        required
        value="<?= htmlspecialchars($project['name']) ?>">
    </div>

    <div style="margin-top:12px;">
      <label class="muted">Descripción</label><br>
      <textarea
        class="card card-pad"
        style="width:100%; padding:10px 12px; border-radius:12px; min-height:110px;"
        name="description"
        rows="4"
        required><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
    </div>

    <div style="margin-top:12px;">
      <label class="muted">Estado del proyecto</label><br>
      <select
        class="card card-pad"
        style="width:100%; padding:10px 12px; border-radius:12px;"
        name="status"
        required>
        <?php $currentStatus = (string)($project['status'] ?? 'pending'); ?>
        <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pendiente</option>
        <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Activo</option>
        <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>Completado</option>
      </select>
    </div>

    <div style="margin-top:12px;">
      <label class="muted">URL del proyecto (opcional)</label><br>
      <input
        class="card card-pad"
        style="width:100%; padding:10px 12px; border-radius:12px;"
        type="text"
        name="project_url"
        inputmode="url"
        spellcheck="false"
        placeholder="https://... o /hangman"
        value="<?= htmlspecialchars($project['project_url'] ?? '') ?>">
      <small class="muted">Acepta URL completa (https://...) o ruta interna (/hangman).</small>
    </div>

    <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
      <a class="btn" href="/technologies">Gestionar tecnologías</a>
      <a class="btn btn-secondary" href="/technologies/create">Crear tecnología</a>
    </div>

    <div style="margin-top:12px;">
      <div class="muted" style="margin-bottom:8px;"><strong>Tecnologías</strong></div>

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
      <button class="btn btn-primary" type="submit">Guardar cambios</button>
      <a class="btn btn-secondary" href="/projects/show/<?= (int)$project['id'] ?>">Cancelar</a>
      <a class="btn" href="/projects">Volver al listado</a>
    </div>

  </form>
</div>
