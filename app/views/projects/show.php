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

  <?php if (($status ?? 'pending') !== 'archived'): ?>
    <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
      <form method="POST" action="/projects/updateStatus/<?= urlencode((string)($id ?? '')) ?>" style="display:inline;">
        <input type="hidden" name="status" value="pending">
        <button class="btn btn-status-pending" <?= ($status === 'pending') ? 'disabled' : '' ?>>
          Pending
        </button>
      </form>

      <form method="POST" action="/projects/updateStatus/<?= urlencode((string)($id ?? '')) ?>" style="display:inline;">
        <input type="hidden" name="status" value="active">
        <button class="btn btn-status-active" <?= ($status === 'active') ? 'disabled' : '' ?>>
          Active
        </button>
      </form>

      <form method="POST" action="/projects/updateStatus/<?= urlencode((string)($id ?? '')) ?>" style="display:inline;">
        <input type="hidden" name="status" value="completed">
        <button class="btn btn-status-completed" <?= ($status === 'completed') ? 'disabled' : '' ?>>
          Completed
        </button>
      </form>
    </div>
  <?php endif; ?>


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