<h1><?= htmlspecialchars($heading ?? 'Proyectos') ?></h1>
<p><?= htmlspecialchars($description ?? '') ?></p>

<hr>

<p>
  <a href="/projects/create">➕ Crear proyecto</a>
</p>

<?php if (empty($projects)): ?>
  <p>No hay proyectos aún. Crea el primero 👆</p>
<?php else: ?>
  <ul>
    <?php foreach ($projects as $p): ?>
      <li>
        <strong><?= htmlspecialchars($p['name']) ?></strong>
        — <a href="/projects/show/<?= urlencode((string)$p['id']) ?>">Ver detalle</a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
