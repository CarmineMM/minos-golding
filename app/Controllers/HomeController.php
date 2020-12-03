<?php

namespace App\Controllers;

use App\Models\User;
use Core\Helper\Validator;
use Core\Routing\Request;

class HomeController
{
    public function index(Request $request)
    {
        $title = 'Bienvenido';
        return view('welcome', compact('title'));
    }
}