<?php


namespace App\Config;


trait Route
{
    /*
    |--------------------------------------------------------------------------
    | Peticiones GET
    |--------------------------------------------------------------------------
    |
    | Establece cuales serán las rutas que solo aceptaran GET
    | Cualquier ruta no especificada, se cargara con cualquier método
    | La ruta se define con 'myroute/second', también 'ruta/*'
    |
    */
    public $only_get = [

    ];


    /*
    |--------------------------------------------------------------------------
    | Peticiones POST
    |--------------------------------------------------------------------------
    |
    | Establece cuales serán las rutas que solo aceptaran POT
    | Cualquier ruta no especificada, se cargara con cualquier método
    | La ruta se define con 'myroute/second', también 'ruta/*'
    |
    */
    public $only_post = [

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


    /*
    |--------------------------------------------------------------------------
    | Métodos habilitados para CRUD
    |--------------------------------------------------------------------------
    |
    | Habilita métodos dentro de los controladores para el CRUD
    | No modifique los valores del primer nivel del array
    | Los valores que puede modificar al gusto y asociar a los controladores son:
    | 1. 'class_method' Nombre que el método de la clase
    | 2. 'http_verb'    Verbo de entrada
    | 3. 'identify'     Identificador al final de la ruta
    |
    */
    public $CRUD_methods = [
        // Leer un registro especifico (ruta/id)
        'show' => [
            'class_method' => 'show',
            'http_verb'    => 'GET',
        ],

        // Creación de un registro nuevo (ruta/store)
        'store' => [
            'class_method' => 'store',
            'http_verb'    => 'POST',
            'identify'     => 'store'
        ],

        // Vista preparada para la edición de un producto (ruta/id/edit)
        'edit' => [
            'class_method' => 'edit',
            'http_verb'    => 'GET',
            'identify'     => 'edit'
        ],

        // Actualización del registro en la base de datos (ruta/id)
        'update' => [
            'class_method' => 'update',
            'http_verb'    => 'PUT',
        ],

        // Eliminar el registro (ruta/id)
        'delete' => [
            'class_method' => 'delete',
            'http_verb'    => 'DELETE',
        ]
    ];
}