<?php

declare(strict_types=1);

class ContactController
{
    public function index(): void
    {
        header('Location: /about#contacto');
        exit;
    }
}
