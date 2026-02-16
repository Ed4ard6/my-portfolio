<?php

// Palabras posibles

// Inicializa el juego en sesión
function init_game() {
    //global $palabras; // eliminamos por que da error al momento de usar con el global 
    $palabras = ['casa', 'perro', 'gato', 'elefante', 'jirafa'];
    $_SESSION['palabra'] = $palabras[array_rand($palabras)];
    $_SESSION['descubiertas'] = str_repeat('_', mb_strlen($_SESSION['palabra']));
    $_SESSION['intentos'] = 0;
    $_SESSION['letras'] = [];
    $_SESSION['flash'] = '';
}

// Resetea el juego por completo
function reset_game() {
    session_unset();
    session_destroy();
    session_start();
    init_game();
    redirect_to_self();
}

// Maneja la entrada del usuario (letras y reinicio)
function handle_user_input() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (isset($_POST['reset'])) {
        reset_game();
    } elseif (isset($_POST['letra'])) {
        process_guess($_POST['letra']);
    }
}

// Procesa una letra adivinada
function process_guess($letra_raw) {
    $mensaje = '';
    $letra_trimmed = trim($letra_raw);
    if ($letra_trimmed === '') {
        $_SESSION['intentos'] += 2;
        $restantes = max(0, MAX_INTENTOS - $_SESSION['intentos']);
        $mensaje = "¡¡¡Tu es que eres BRUTO o te la das escribe una letra sopla picha!!!";
        $mensaje .= "\nPor esa gracia pierdes 2 intentos por loca y te van quedando $restantes intentos.";
    } elseif (mb_strlen($letra_raw) !== 1) {
        $mensaje = "Te dije UNA letra nada más caremonda.";
        $_SESSION['intentos'] += 2;
        $restantes = max(0, MAX_INTENTOS - $_SESSION['intentos']);
        $mensaje .= "\nPor esa gracia pierdes 2 intentos por loca y te van quedando $restantes intentos.";
    } elseif (!preg_match('/^[a-zA-Z]$/u', $letra_raw)) {
        $mensaje = "¿Acaso '$letra_raw' es una letra careverga?";
        $_SESSION['intentos'] += 2;
        $restantes = max(0, MAX_INTENTOS - $_SESSION['intentos']);
        $mensaje .= "\nPor esa gracia pierdes 2 intentos por loca y te van quedando $restantes intentos.";
    } else {
        $letra = mb_strtolower($letra_raw, 'UTF-8');
        
        if (in_array($letra, $_SESSION['letras'])) {
            $mensaje = "Ya probaste la letra '$letra', no seas bruto.";
        } else {
            $_SESSION['letras'][] = $letra;
            $palabra = $_SESSION['palabra'];
            $pal_chars = preg_split('//u', $palabra, -1, PREG_SPLIT_NO_EMPTY);
            $desc_chars = preg_split('//u', $_SESSION['descubiertas'], -1, PREG_SPLIT_NO_EMPTY);
            
            if (in_array($letra, $pal_chars, true)) {
                foreach ($pal_chars as $i => $ch) {
                    if ($ch === $letra) $desc_chars[$i] = $letra;
                }
                $_SESSION['descubiertas'] = implode('', $desc_chars);
                $mensaje = "Bien! Has revelado la letra '$letra'.";
            } else {
                $_SESSION['intentos']++;
                $restantes = max(0, MAX_INTENTOS - $_SESSION['intentos']);
                $mensaje = "Lo siento, la letra '$letra' no está en la palabra. Te van quedando $restantes intentos.";
            }
        }
    }
    
    if ($_SESSION['intentos'] > MAX_INTENTOS) $_SESSION['intentos'] = MAX_INTENTOS;
    $_SESSION['flash'] = $mensaje;
    redirect_to_self();
}

// Funciones para obtener el estado del juego
function is_game_active() {
    return isset($_SESSION['palabra']);
}

function get_flash_message() {
    $mensaje = !empty($_SESSION['flash']) ? $_SESSION['flash'] : '';
    $_SESSION['flash'] = '';
    return $mensaje;
}

function get_word() {
    return isset($_SESSION['palabra']) ? $_SESSION['palabra'] : '';
}

function get_discovered_word() {
    return isset($_SESSION['descubiertas']) ? $_SESSION['descubiertas'] : '';
}

function get_attempts() {
    return isset($_SESSION['intentos']) ? $_SESSION['intentos'] : 0;
}

function get_used_letters() {
    return isset($_SESSION['letras']) ? $_SESSION['letras'] : [];
}

function get_game_status() {
    $palabra = get_word();
    $descubiertas = get_discovered_word();
    $intentos = get_attempts();

    if ($intentos >= MAX_INTENTOS && $descubiertas === str_repeat('_', mb_strlen($palabra))) {
        return "Debe ser uno muy bruto en la vida para que con " . MAX_INTENTOS . " intentos no pegar ni una HP letra";
    } elseif ($intentos >= MAX_INTENTOS) {
        return "¡Has perdido! La palabra era '" . htmlspecialchars($palabra, ENT_QUOTES, 'UTF-8') . "'.";
    } elseif ($descubiertas === $palabra) {
        return "¡Felicidades! Has adivinado la palabra '" . htmlspecialchars($palabra, ENT_QUOTES, 'UTF-8') . "'.";
    }
    return null;
}