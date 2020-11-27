<?php

namespace App\Controllers;

use App\Models\User;
use Core\Routing\Request;

class HomeController
{
    public function index(Request $request)
    {
//        $title = 'Bienvenida';
//        return view('welcome', compact('title'));

        $user = new User();

        return $user->all()->exec();
    }



    public function user($request, $user)
    {
        return 'entre a home controller';
    }
}