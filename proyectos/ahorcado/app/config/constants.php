<?php

// Define la ruta base del sitio de forma dinámica
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    // Entorno local
    define('BASE_PATH', '/local/PHP/Ruta%20PHP/hosting/');
} else {
    // Entorno de producción (Hostinger)
    define('BASE_PATH', '/');
}

define('MAX_INTENTOS', 6);
