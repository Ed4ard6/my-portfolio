<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../core/Router.php';

Router::run();
