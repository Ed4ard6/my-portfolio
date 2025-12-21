<h1><?= htmlspecialchars($heading) ?></h1>

<?php if (!empty($errors)): ?>
  <div style="padding:12px; border:1px solid #ccc; margin-bottom:16px;">
    <strong>Hay errores:</strong>
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="/projects/store">
    <label>
        Nombre del proyecto:
        <input
          type="text"
          name="name"
          required
          value="<?= htmlspecialchars($old['name'] ?? '') ?>"
        >
    </label>

    <br><br>

    <label>
        Descripción:
        <textarea name="description" 
        rows="4" 
        required
        ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
    </label>

    <br><br>

    <button type="submit">Guardar</button>
</form>

<p style="margin-top:16px;">
  <a href="/projects">← Volver</a>
</p>
