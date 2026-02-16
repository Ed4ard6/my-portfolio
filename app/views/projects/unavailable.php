<div class="card card-pad project-state-card" style="max-width:760px; margin:0 auto;">
  <div class="row" style="align-items:center;">
    <h1 style="margin:0;"><?= htmlspecialchars($heading ?? 'Proyecto no disponible') ?></h1>
    <?php $status = (string)($status ?? 'pending'); ?>
    <span class="badge badge--<?= htmlspecialchars($status) ?>">
      <span class="badge-dot"></span>
      <?= htmlspecialchars(ucfirst($status)) ?>
    </span>
  </div>

  <p class="muted" style="margin-top:10px;">
    <?= htmlspecialchars($message ?? 'Este proyecto aún no está disponible.') ?>
  </p>

  <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
    <a class="btn btn-primary" href="/projects">← Volver a proyectos</a>
    <a class="btn btn-secondary" href="/">Ir al inicio</a>
  </div>
</div>
