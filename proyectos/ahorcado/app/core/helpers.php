<?php
// Redirecciona al mismo script (PRG) para evitar re-envíos de formulario
function redirect_to_self() {
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}