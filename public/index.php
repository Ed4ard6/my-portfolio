<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Env.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';

Env::load(dirname(__DIR__));
session_start();

require_once __DIR__ . '/../core/Router.php';

Router::run();
