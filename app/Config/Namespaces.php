<?php


namespace App\Config;


trait Namespaces
{
    /*
    |--------------------------------------------------------------------------
    | Controladores
    |--------------------------------------------------------------------------
    |
    | Declara el namespace donde están ubicados los controladores
    | Solo puede haber un namespace raíz
    | Dentro de estos pueden haber anidaciones en carpetas especificadas con un '\'
    |
    */
    protected $controller = '\App\Controllers\\';


    /*
    |--------------------------------------------------------------------------
    | Identificador de Controladores
    |--------------------------------------------------------------------------
    |
    | Se declara si los controladores tendrán un identificador al final del nombre
    | Por defecto es: 'Controller', por lo tanto un controlador queda como: 'HomeController'
    | Si se especifica como String vació: '', el controlador podría quedar como: 'Home'
    |
    */
    protected $using_identify_controller = 'Controller';


    /*
    |--------------------------------------------------------------------------
    | Namespace del registrador de rutas
    |--------------------------------------------------------------------------
    |
    | Contiene la declaración de todas las rutas.
    | Implícitas: Las que se buscan de forma dinámica en los controladores
    | Explicitas: Son las que define el usuario
    |
    */
    protected $register_routes = '\App\Routes\Web';


    /*
    |--------------------------------------------------------------------------
    | Controlador de las authentication's
    |--------------------------------------------------------------------------
    |
    | Controlador que se encantará del registro, login y cosas del usuario
    | El controlador esta enlazado al namespace de los controladores
    | También esta enlazado al identificador de controladores
    |
    */
    protected $auth_controller = 'Admin\Auth';
}