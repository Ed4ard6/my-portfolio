<h1><?= htmlspecialchars($heading) ?></h1>

<form method="post" action="/projects/store">
    <label>
        Nombre del proyecto:
        <input type="text" name="name" required>
    </label>

    <br><br>

    <label>
        Descripción:
        <textarea name="description" rows="4" required></textarea>
    </label>

    <br><br>

    <button type="submit">Guardar</button>
</form>

<p style="margin-top:16px;">
  <a href="/projects">← Volver</a>
</p>
