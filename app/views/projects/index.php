<h1><?= htmlspecialchars($heading ?? 'Proyectos') ?></h1>
<p><?= htmlspecialchars($description ?? '') ?></p>

<hr>

<p>
  <a href="/projects/create">➕ Crear proyecto</a>
</p>
<form method="post" action="/projects/reset" style="display:inline;">
  <button type="submit" onclick="return confirm('¿Borrar todos los proyectos de la sesión?');">
    🧹 Reset
  </button>
</form>

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
