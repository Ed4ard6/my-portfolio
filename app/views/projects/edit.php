<h1><?= htmlspecialchars($heading) ?></h1>

<form method="POST" action="/projects/update">
  <input type="hidden" name="id" value="<?= (int)$project['id'] ?>">

  <div>
    <label>Nombre</label><br>
    <input type="text" name="name" required value="<?= htmlspecialchars($project['name']) ?>">
  </div>

  <br>

  <div>
    <label>Descripción</label><br>
    <textarea name="description" rows="4" required><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
  </div>

  <br>

  <div>
    <label>Tecnologías</label><br>

    <?php foreach ($technologies as $t): ?>
      <?php $checked = in_array((int)$t['id'], $selectedTechIds, true); ?>
      <label style="display:block;">
        <input type="checkbox" name="technologies[]" value="<?= (int)$t['id'] ?>" <?= $checked ? 'checked' : '' ?>>
        <?= htmlspecialchars($t['name']) ?>
      </label>
    <?php endforeach; ?>
  </div>

  <br>

  <button type="submit">Guardar cambios</button>
  <a href="/projects/show/<?= (int)$project['id'] ?>">Cancelar</a>
</form>
