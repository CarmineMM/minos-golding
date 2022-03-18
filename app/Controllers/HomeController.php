<?php

namespace App\Controllers;

use Core\Routing\Request;

class HomeController
{
    public function index(Request $request)
    {
        $title = 'Hola Mundo!';
        return view('welcome', compact('title'));
    }

    public function archivo($request)
    {
        showDev($request);
        return 'Subir archivo';
    }
}