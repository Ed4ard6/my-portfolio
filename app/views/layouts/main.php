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
      <!-- Logo = Inicio -->
      <a href="/" class="brand-link" aria-label="Inicio">
        <img id="siteLogo" class="logo" src="/img/logo-dark.png" alt="Logo">
      </a>

      <!-- Menú SIN "Inicio" -->
      <nav class="links">
        <a class="btn" href="/about">Sobre mí</a>
        <a class="btn" href="/projects">Proyectos</a>
        <a class="btn" href="/contact">Contacto</a>
      </nav>

      <?php $isAdmin = class_exists('Auth') && Auth::check(); ?>

      <div style="display:flex; gap:10px; align-items:center;">
        <?php if ($isAdmin): ?>
          <a class="btn btn-secondary" href="/auth/logout">Cerrar sesión</a>
        <?php else: ?>
          <a class="btn btn-secondary" href="/auth/login">Admin</a>
        <?php endif; ?>

        <!-- Toggle elegante -->
        <button id="themeToggle" class="icon-toggle" type="button" aria-label="Cambiar tema">
          <span class="icon" aria-hidden="true">🌙</span>
        </button>
      </div>
    </header>

    <main>
      <?= $content ?>
    </main>
  </div>

  <script src="/js/theme.js"></script>
</body>
</html>
