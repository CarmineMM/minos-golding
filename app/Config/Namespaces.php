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
    | Controlador inicial
    |--------------------------------------------------------------------------
    |
    | Define el controlador inicial, este debe existir
    | Se usara para la llamada de la ruta raíz
    | No es necesario especificarle el namespace ni el identificador
    |
    */
    protected $home_controller = 'Home';

        
    /*
    |--------------------------------------------------------------------------
    | Método Inicial
    |--------------------------------------------------------------------------
    |
    | Define el método que se manda a llamar de forma predeterminada
    | Este método se usa cuando por defecto en cualquier controlador
    | Es la base de instancia para el render de la app
    |
    */
    protected $init_method = 'index';


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
}