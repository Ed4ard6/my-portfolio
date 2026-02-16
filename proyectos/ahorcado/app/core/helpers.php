<?php

// Redirecciona a la misma URL (incluye query string) para evitar re-envíos de formulario.
function redirect_to_self(): void
{
    $target = (string)($_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF'] ?? '/');

    header('Location: ' . $target);
    exit;
}
