<h1><?= htmlspecialchars($heading ?? 'Proyectos') ?></h1>
<p><?= htmlspecialchars($description ?? '') ?></p>

<hr>

<div style="display:flex; gap:10px; flex-wrap:wrap; margin:12px 0;">
  <a class="btn btn-primary" href="/projects/create">➕ Crear proyecto</a>
  <a class="btn btn-secondary" href="/projects/archived">Ver archivados</a>
</div>

<?php if (empty($projects)): ?>
  <p>No hay proyectos aún. Crea el primero 👆</p>
<?php else: ?>
  <div class="grid">
    <?php foreach ($projects as $p): ?>
      <?php $status = $p['status'] ?? 'pending'; ?>

      <div class="card card-pad">
        <div class="row">
          <div>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
              <strong><?= htmlspecialchars($p['name']) ?></strong>

              <span class="badge badge--<?= htmlspecialchars($status) ?>">
                <span class="badge-dot"></span>
                <?= htmlspecialchars(ucfirst($status)) ?>
              </span>
            </div>

            <div class="muted" style="margin-top:8px;">
              <b>Tecnologías:</b>
              <?= empty($p['technologies']) ? 'Pendiente' : htmlspecialchars($p['technologies']) ?>
            </div>
          </div>

          <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a class="btn" href="/projects/show/<?= urlencode((string)$p['id']) ?>">Ver detalle</a>
            <a class="btn" href="/projects/edit/<?= urlencode((string)$p['id']) ?>">Editar</a>
            <a class="btn btn-danger"
              href="/projects/archive/<?= urlencode((string)$p['id']) ?>"
              onclick="return confirm('¿Seguro que quieres archivar este proyecto?');">
              Archivar
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>