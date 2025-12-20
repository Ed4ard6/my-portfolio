<?php

require_once __DIR__ . '/../../core/View.php';

class ContactController
{
    public function index()
    {
        View::render('contact/index', [
            'title' => 'Contacto',
            'heading' => 'Contacto',
            'description' => 'Puedes contactarme a través de este formulario o mis redes.'
        ]);
    }
}
