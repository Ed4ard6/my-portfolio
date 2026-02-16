<?php

function hangman_state(): array
{
    $state = $_SESSION[HANGMAN_SESSION_KEY] ?? null;
    return is_array($state) ? $state : [];
}

function set_hangman_state(array $state): void
{
    $_SESSION[HANGMAN_SESSION_KEY] = $state;
}

function init_game(): void
{
    $word = HANGMAN_WORDS[array_rand(HANGMAN_WORDS)];

    set_hangman_state([
        'palabra' => $word,
        'descubiertas' => str_repeat('_', mb_strlen($word)),
        'intentos' => 0,
        'letras' => [],
        'flash' => '',
    ]);
}

function reset_game(): void
{
    unset($_SESSION[HANGMAN_SESSION_KEY]);
    init_game();
    redirect_to_self();
}

function handle_user_input(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    if (isset($_POST['reset'])) {
        reset_game();
    }

    if (isset($_POST['letra'])) {
        process_guess((string)$_POST['letra']);
    }
}

function process_guess(string $letraRaw): void
{
    $state = hangman_state();
    if (empty($state)) {
        init_game();
        $state = hangman_state();
    }

    $mensaje = '';
    $letraTrimmed = trim($letraRaw);

    if ($letraTrimmed === '') {
        $state['intentos'] += 2;
        $restantes = max(0, MAX_INTENTOS - $state['intentos']);
        $mensaje = "Ingresa una letra válida.\nPierdes 2 intentos. Te quedan $restantes.";
    } elseif (mb_strlen($letraTrimmed) !== 1) {
        $state['intentos'] += 2;
        $restantes = max(0, MAX_INTENTOS - $state['intentos']);
        $mensaje = "Solo puedes ingresar UNA letra.\nPierdes 2 intentos. Te quedan $restantes.";
    } elseif (!preg_match('/^[\p{L}]$/u', $letraTrimmed)) {
        $state['intentos'] += 2;
        $restantes = max(0, MAX_INTENTOS - $state['intentos']);
        $mensaje = "'$letraTrimmed' no es una letra válida.\nPierdes 2 intentos. Te quedan $restantes.";
    } else {
        $letra = mb_strtolower($letraTrimmed, 'UTF-8');

        if (in_array($letra, $state['letras'], true)) {
            $mensaje = "Ya probaste la letra '$letra'.";
        } else {
            $state['letras'][] = $letra;
            $palChars = preg_split('//u', (string)$state['palabra'], -1, PREG_SPLIT_NO_EMPTY);
            $descChars = preg_split('//u', (string)$state['descubiertas'], -1, PREG_SPLIT_NO_EMPTY);

            if (in_array($letra, $palChars, true)) {
                foreach ($palChars as $i => $ch) {
                    if ($ch === $letra) {
                        $descChars[$i] = $letra;
                    }
                }
                $state['descubiertas'] = implode('', $descChars);
                $mensaje = "¡Bien! Has revelado la letra '$letra'.";
            } else {
                $state['intentos']++;
                $restantes = max(0, MAX_INTENTOS - $state['intentos']);
                $mensaje = "La letra '$letra' no está. Te quedan $restantes intentos.";
            }
        }
    }

    if ($state['intentos'] > MAX_INTENTOS) {
        $state['intentos'] = MAX_INTENTOS;
    }

    $state['flash'] = $mensaje;
    set_hangman_state($state);
    redirect_to_self();
}

function is_game_active(): bool
{
    $state = hangman_state();
    return isset($state['palabra']);
}

function get_flash_message(): string
{
    $state = hangman_state();
    $mensaje = (string)($state['flash'] ?? '');
    $state['flash'] = '';
    set_hangman_state($state);

    return $mensaje;
}

function get_word(): string
{
    $state = hangman_state();
    return (string)($state['palabra'] ?? '');
}

function get_discovered_word(): string
{
    $state = hangman_state();
    return (string)($state['descubiertas'] ?? '');
}

function get_attempts(): int
{
    $state = hangman_state();
    return (int)($state['intentos'] ?? 0);
}

function get_used_letters(): array
{
    $state = hangman_state();
    $letters = $state['letras'] ?? [];
    return is_array($letters) ? $letters : [];
}

function get_game_status(): ?string
{
    $palabra = get_word();
    $descubiertas = get_discovered_word();
    $intentos = get_attempts();

    if ($palabra === '') {
        return null;
    }

    if ($intentos >= MAX_INTENTOS && $descubiertas === str_repeat('_', mb_strlen($palabra))) {
        return 'Perdiste: no lograste adivinar ninguna letra.';
    }

    if ($intentos >= MAX_INTENTOS) {
        return "¡Has perdido! La palabra era '$palabra'.";
    }

    if ($descubiertas === $palabra) {
        return "¡Felicidades! Adivinaste la palabra '$palabra'.";
    }

    return null;
}
