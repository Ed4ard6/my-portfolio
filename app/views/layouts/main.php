<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= isset($title) ? htmlspecialchars($title) : 'Mi Portafolio' ?></title>

  <link rel="icon" href="/img/favicon.png" type="image/png">
  <link rel="stylesheet" href="/css/app.css">
</head>

<body>
  <div class="container">
    <header class="nav">
      <a href="/" class="brand-link" aria-label="Inicio">
        <img id="siteLogo" class="logo" src="/img/logo-dark.png" alt="Logo">
      </a>

      <?php $isAdmin = class_exists('Auth') && Auth::check(); ?>

      <nav class="links">
        <a class="btn" href="/projects">Proyectos</a>
        <?php if ($isAdmin): ?>
          <a class="btn" href="/about#admin-content-panel">Editar contenido</a>
        <?php endif; ?>
      </nav>

      <div style="display:flex; gap:10px; align-items:center;">
        <?php if ($isAdmin): ?>
          <a class="btn" href="/admins">Administradores</a>
          <a class="btn" href="/technologies">Tecnologías</a>
          <a class="btn btn-secondary" href="/auth/logout">Cerrar sesión</a>
        <?php else: ?>
          <a class="btn btn-secondary" href="/auth/login">Admin</a>
        <?php endif; ?>

        <button id="themeToggle" class="icon-toggle" type="button" aria-label="Cambiar tema">
          <span class="icon" aria-hidden="true">🌙</span>
        </button>
      </div>
    </header>

    <main>
      <?= $content ?>
    </main>

    <footer class="card card-pad" style="margin-top:16px; text-align:center;">
      <small class="muted">© <?= date('Y') ?> Mi Portafolio · Hecho con PHP MVC</small>
    </footer>
  </div>

  <script src="/js/theme.js"></script>
</body>
</html>
