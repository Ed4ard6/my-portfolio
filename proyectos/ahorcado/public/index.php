<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../app/config/constants.php';
require_once __DIR__ . '/../app/core/helpers.php';
require_once __DIR__ . '/../app/core/game.php';

if (!is_game_active()) {
    init_game();
}

handle_user_input();

$mensaje = get_flash_message();
$palabra = get_word();
$descubiertas = get_discovered_word();
$intentos = get_attempts();
$letras = get_used_letters();
$estado_final = get_game_status();

require_once __DIR__ . '/../app/templates/game_view.php';
