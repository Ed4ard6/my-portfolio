<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= isset($title) ? htmlspecialchars($title) : 'Mi Portafolio' ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; }
        header { padding: 16px; background: #111; color: #fff; }
        main { padding: 24px; }
        a { color: inherit; text-decoration: none; margin-right: 12px; }
        nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <strong>Mi Portafolio</strong>
        <nav style="margin-top:8px;">
            <a href="/">Inicio</a>
            <a href="/about">Sobre mí</a>
            <a href="/projects">Proyectos</a>
            <a href="/contact">Contacto</a>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>
</body>
</html>
