<?php
session_start();

// Incluir archivos de lógica del juego
require_once __DIR__ . '/../vendor/autoload.php';


// Inicializar el juego si no está activo
if (!is_game_active()) {
    init_game();
}

handle_user_input();

// Recuperar el mensaje flash para mostrar en la vista
$mensaje = get_flash_message();

// Obtener el estado actual del juego
$palabra = get_word();
$descubiertas = get_discovered_word();
$intentos = get_attempts();
$letras = get_used_letters();
$estado_final = get_game_status();

// Incluir la vista (la parte HTML/CSS)
require_once __DIR__ . '/../app/templates/game_view.php';