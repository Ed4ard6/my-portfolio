<h1><?= htmlspecialchars($heading) ?></h1>

<p><?= htmlspecialchars($description) ?></p>

<?php if (isset($tech)): ?>
    <p><strong>Tecnologías:</strong> <?= htmlspecialchars($tech) ?></p>
<?php endif; ?>

<p><strong>ID:</strong> <?= htmlspecialchars((string)($id ?? '')) ?></p>

<p><a href="/projects">← Volver a proyectos</a></p>
