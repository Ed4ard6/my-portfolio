<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Ahorcado - Eduardo Machacón</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/game.css">
</head>

<body>
    <div class="card">
        <!-- <header>
            <div>
                <h1>🎮 El juego del Ahorcado de Eduardo </h1>
            </div>
            <div style="text-align:right; color:var(--muted);">
                <div>Intentos: <strong><?= $intentos ?>/<?= MAX_INTENTOS ?></strong></div>
                <div style="font-size:13px; margin-top:6px;">Palabra: <strong><?= mb_strlen($palabra) ?> letras</strong></div>
            </div>
        </header> -->

        <header>
            <div>
                <h1>🎮 El juego del Ahorcado de Eduardo </h1>
            </div>
            <div style="text-align:right; color:var(--muted);">
                <div>Intentos: <strong><?= $intentos ?>/<?= MAX_INTENTOS ?></strong></div>
                <div style="font-size:13px; margin-top:6px;">Palabra: <strong><?= mb_strlen($palabra) ?> letras</strong></div>
            </div>
            <div style="position: absolute; top: 20px; left: 20px;">
                <a href="<?= BASE_PATH ?>index.php" class="back-btn">← Volver</a>
            </div>
        </header>

        <div class="main">
            <div class="word">
                <div class="letters" aria-hidden="true">
                    <?php
                    $desc_chars = preg_split('//u', $descubiertas, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($desc_chars as $ch):
                    ?>
                        <div class="letter">
                            <?php if ($ch === '_'): ?>
                                <span class="underscore">_</span>
                            <?php else: ?>
                                <?= htmlspecialchars($ch, ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($mensaje !== ''):
                    $isPenalty = stripos($mensaje, 'pierdes') !== false || stripos($mensaje, 'pierde') !== false;
                ?>
                    <div class="mensaje <?= $isPenalty ? 'penalty' : 'ok' ?>">
                        <?= nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                <?php endif; ?>

                <?php if ($estado_final !== null): ?>
                    <div class="final"><?= htmlspecialchars($estado_final, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <aside class="panel">
                <div style="width:100%; text-align:center;">
                    <div style="margin-bottom:8px;">Progreso de intentos</div>
                    <div class="dots" role="progressbar" aria-valuemin="0" aria-valuemax="<?= MAX_INTENTOS ?>">
                        <?php for ($i = 0; $i < MAX_INTENTOS; $i++): ?>
                            <span class="dot <?= $i < $intentos ? 'used' : '' ?>"></span>
                        <?php endfor; ?>
                    </div>
                    <div style="margin-top:10px;" class="used-letters">Letras usadas: <?= htmlspecialchars(implode(', ', $letras), ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <div style="width:100%;">
                    <?php if ($estado_final === null): ?>
                        <form method="post" style="display:flex; flex-direction:column; gap:8px; align-items:center;">
                            <input type="text" name="letra" maxlength="2" placeholder="Escribe una letra" autofocus>
                            <div class="controls">
                                <button type="submit">Probar letra</button>
                                <button class="reset" type="submit" name="reset">Reiniciar</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="post" style="display:flex; gap:8px; justify-content:center;">
                            <button class="reset" type="submit" name="reset">🔄 Reiniciar juego</button>
                        </form>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</body>

</html>