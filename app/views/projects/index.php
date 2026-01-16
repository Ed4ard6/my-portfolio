<h1><?= htmlspecialchars($heading ?? 'Proyectos') ?></h1>
<p><?= htmlspecialchars($description ?? '') ?></p>

<hr>

<?php $isAdmin = class_exists('Auth') && Auth::check(); ?>

<?php if ($isAdmin): ?>
  <div style="display:flex; gap:10px; flex-wrap:wrap; margin:12px 0;">
    <a class="btn btn-primary" href="/projects/create">➕ Crear proyecto</a>
    <a class="btn btn-secondary" href="/projects/archived">Ver archivados</a>
  </div>
<?php endif; ?>
<?php $currentStatus = $currentStatus ?? ''; ?>

<div style="display:flex; gap:10px; flex-wrap:wrap; margin:12px 0;">
  <a class="btn <?= $currentStatus === '' ? 'btn-primary' : '' ?>" href="/projects">
    Todos
  </a>

  <a class="btn <?= $currentStatus === 'pending' ? 'btn-primary' : '' ?>" href="/projects?status=pending">
    Pendiente
  </a>

  <a class="btn <?= $currentStatus === 'active' ? 'btn-primary' : '' ?>" href="/projects?status=active">
    Activo
  </a>

  <a class="btn <?= $currentStatus === 'completed' ? 'btn-primary' : '' ?>" href="/projects?status=completed">
    Completado
  </a>
</div>


<?php if (empty($projects)): ?>
  <p>No hay proyectos aún. Crea el primero 👆</p>
<?php else: ?>
  <div class="grid">
    <?php foreach ($projects as $p): ?>
      <?php $status = $p['status'] ?? 'pending'; ?>

      <?php $imageUrl = trim($p['image_url'] ?? ''); ?>
      <?php $imageSrc = $imageUrl !== '' ? $imageUrl : '/img/project-placeholder.svg'; ?>

      <div class="card card-pad project-card">
        <div class="project-media">
          <img src="<?= htmlspecialchars($imageSrc) ?>" alt="Imagen de <?= htmlspecialchars($p['name']) ?>">
        </div>

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

            <?php if ($isAdmin): ?>
              <a class="btn" href="/projects/edit/<?= urlencode((string)$p['id']) ?>">Editar</a>
              <a class="btn btn-danger"
                href="/projects/archive/<?= urlencode((string)$p['id']) ?>"
                onclick="return confirm('¿Seguro que quieres archivar este proyecto?');">
                Archivar
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
