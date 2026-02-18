<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php $pageTitle = isset($title) ? htmlspecialchars((string)$title) : 'Mi Portafolio'; ?>
  <title><?= $pageTitle ?></title>
  <meta name="description" content="Portafolio de Eduardo Machacón, desarrollador backend especializado en PHP, MVC y MySQL.">
  <meta property="og:title" content="<?= $pageTitle ?>">
  <meta property="og:description" content="Conoce proyectos backend en PHP con enfoque en arquitectura MVC, seguridad y experiencia de usuario.">
  <meta property="og:type" content="website">

  <link rel="icon" href="/img/favicon.png" type="image/png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
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
        <a class="btn" href="/">Inicio</a>
        <a class="btn" href="/projects">Proyectos</a>
        <a class="btn" href="/#contacto">Contacto</a>
        <?php if ($isAdmin): ?>
          <a class="btn" href="/about#admin-content-panel">Editar contenido</a>
        <?php endif; ?>
      </nav>

      <button id="themeToggle" class="icon-toggle nav-theme-toggle" type="button" aria-label="Cambiar tema" style="position:absolute; top:10px; right:10px; z-index:6;">
        <span class="icon" aria-hidden="true">🌙</span>
      </button>

      <div class="nav-admin-actions">
        <?php if ($isAdmin): ?>
          <a class="btn" href="/admins">Administradores</a>
          <a class="btn" href="/technologies">Tecnologías</a>
          <a class="btn btn-danger nav-logout-btn" href="/auth/logout">Cerrar sesión</a>
        <?php else: ?>
          <a class="btn btn-secondary" href="/auth/login">Admin</a>
        <?php endif; ?>
      </div>
    </header>

    <main>
      <?= $content ?>
    </main>

    <footer class="card card-pad" style="margin-top:16px; text-align:center;">
      <small class="muted">© <?= date('Y') ?> Eduardo Machacón · Portafolio Backend en PHP MVC</small>
    </footer>
  </div>

  <script src="/js/theme.js"></script>
</body>
</html>
