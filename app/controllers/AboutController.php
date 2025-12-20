<?php

require_once __DIR__ . '/../../core/View.php';

class AboutController
{
    public function index()
    {
        View::render('about/index', [
            'title' => 'Sobre mí',
            'heading' => 'Sobre mí',
            'description' => 'Aquí puedes contar quién eres, qué haces y qué te apasiona.'
        ]);
    }
}
