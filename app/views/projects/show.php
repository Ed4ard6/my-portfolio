<div class="card card-pad">
  <div class="row">
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <h1 style="margin:0;"><?= htmlspecialchars($heading) ?></h1>

      <?php $status = $status ?? 'pending'; ?>
      <span class="badge badge--<?= htmlspecialchars($status) ?>">
        <span class="badge-dot"></span>
        <?= htmlspecialchars(ucfirst($status)) ?>
      </span>
    </div>

    <div class="muted">
      <strong>ID:</strong> <?= htmlspecialchars((string)($id ?? '')) ?>
    </div>
  </div>

  <p style="margin-top:12px;">
    <?= nl2br(htmlspecialchars($description)) ?>
  </p>

  <p style="margin-top:10px;">
    <b>Tecnologías:</b>
    <?php if (!isset($techNames) || empty($techNames)): ?>
      Pendiente
    <?php else: ?>
      <?= htmlspecialchars(implode(', ', $techNames)) ?>
    <?php endif; ?>
  </p>

  <div style="margin-top:16px; display:flex; gap:8px; flex-wrap:wrap;">
    <a class="btn" href="/projects/edit/<?= urlencode((string)($id ?? '')) ?>">Editar</a>
    <a class="btn" href="/projects">← Volver a proyectos</a>
  </div>
</div>
