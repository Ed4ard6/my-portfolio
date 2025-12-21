<h1><?= htmlspecialchars($heading ?? 'Proyectos') ?></h1>
<p><?= htmlspecialchars($description ?? '') ?></p>

<hr>

<!-- Enlaces a métodos del mismo controlador -->
<p>
  <a href="/projects/create">➕ Crear proyecto</a>
  |
  <a href="/projects/show/1">👁️ Ver proyecto #1</a>
  <a href="/projects/show/2">👁️ Ver proyecto #2</a>
  <a href="/projects/show/3">👁️ Ver proyecto #3</a>

</p>
