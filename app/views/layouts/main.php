<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= isset($title) ? htmlspecialchars($title) : 'Mi Portafolio' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            padding: 16px;
            background: #111;
            color: #fff;
        }

        main {
            padding: 24px;
        }

        a {
            color: inherit;
            text-decoration: none;
            margin-right: 12px;
        }

        nav a:hover {
            text-decoration: underline;
        }
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
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div style="padding:12px; border:1px solid #ccc; margin-bottom:16px;">
                <?= htmlspecialchars($_SESSION['flash_success']) ?>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div style="padding:12px; border:1px solid #f5c2c7; background:#f8d7da; margin-bottom:16px;">
                <?= htmlspecialchars($_SESSION['flash_error']) ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>


        <?= $content ?>
    </main>
</body>

</html>