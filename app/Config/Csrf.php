<?php


namespace App\Config;


trait Csrf
{
    /*
    |--------------------------------------------------------------------------
    | Habilita el uso de CSRF
    |--------------------------------------------------------------------------
    |
    | Habilita el uso y comprobación de CSRFs dentro de la aplicación
    | Si es falso, se eliminaran los tokens creados, y no pasaran por validación
    |
    */
    protected $use_csrf = true;


    /*
    |--------------------------------------------------------------------------
    | Longitud del Token
    |--------------------------------------------------------------------------
    |
    | La longitud define la cantidad de caracteres que tendrá
    | Mientras mayor sera la longitud mas lento puede funcionar la App
    | Por defecto y recomendables es: 32, (min:4, max:100)
    |
    */
    protected $length = 32;


    /*
    |--------------------------------------------------------------------------
    | Tiempo de vida del Token
    |--------------------------------------------------------------------------
    |
    | Establece cuando tiempo dura el Token hasta su próxima regeneración
    | El tiempo es definido en minutos, siendo 60 minutos el por defecto
    | Colocar tiempos bajos hara que la experiencia de usuario mengue
    |
    */
    protected $expiration_time = 60;


    /*
    |--------------------------------------------------------------------------
    | Métodos HTTP
    |--------------------------------------------------------------------------
    |
    | Estos son los métodos http que requieren validaciones
    | Los que no están mencionados no serán validados con un CSRF
    | Si no se define ninguno, no se validara ningún método
    |
    */
    protected $methods_to_verify = [
        'POST', 'PATCH', 'PUT', 'DELETE'
    ];


    /*
    |--------------------------------------------------------------------------
    | Abrir Control de acceso CORS
    |--------------------------------------------------------------------------
    |
    | Identifica las rutas a las cuales se abren las puertas de acceso
    | Valida las entradas de peticiones a dichas rutas desde accesos desconocidos
    | Recomendable para APIs y peticiones AJAX
    |
    */
    public $open_CORS = false;
}