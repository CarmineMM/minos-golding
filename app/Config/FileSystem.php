<?php


namespace App\Config;


trait FileSystem
{
    /*
    |--------------------------------------------------------------------------
    | Vistas
    |--------------------------------------------------------------------------
    |
    | Declara la ubicación donde se almacenan las vistas que necesitan ser renderings
    | Este es un parámetro único, y no require de un '/' al final del string
    | También es posible definir los tipos de archivos de las vistas
    | Y los identificadores de dichas vistas
    |
    */
    protected $views          = 'resources/views';
    protected $view_file_type = '.view.php';


    /*
    |--------------------------------------------------------------------------
    | Path Public
    |--------------------------------------------------------------------------
    |
    | Declara la ubicación de la carpeta publica
    | Este es el path hacia la carpeta mas no una URL
    | También configura los paths de carpetas importantes
    |
    */
    protected $public_path = 'public';


    /*
    |--------------------------------------------------------------------------
    | Path Custom Functions - Functions
    |--------------------------------------------------------------------------
    |
    | Define el path donde estarán las funciones personalizadas
    | Estas funciones son cargadas atreves de un arreglo
    | Solo se debe especificar la ruta y los archivos
    |
    */
    protected $path_custom_functions = 'resource/functions';
    protected $custom_functions = [
        'myfunc.php'
    ];


    /*
    |--------------------------------------------------------------------------
    | Vistas de errores HTTP
    |--------------------------------------------------------------------------
    |
    | Establece los archivos que se llamaran al haber un error
    | Estas vistas parten de la configuración del path de las vistas
    | Si los archivos no se encuentran se usaran vistas internas del framework
    |
    */
    protected $error_views = [
        '404' => 'errors/404.php',
        '405' => 'errors/405.php',
        '406' => 'errors/406.php',
        '500' => 'errors/500.php'
    ];
}