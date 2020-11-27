<?php


namespace App\Config;


trait App
{
    /*
    |--------------------------------------------------------------------------
    | Entorno de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Establece el entorno actual de la aplicación, solo puede tomar local/production
    | Dicho entorno puede ser usado para la configuración del sistema y dependencias
    | Loca: marca entorno para debug. production: Ofrece una aplicación mas segura
    |
    */
    protected $environment = 'local';


    /*
    |--------------------------------------------------------------------------
    | Nombre de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Este nombre es accessible en la aplicación
    | Por defecto, toma un valor 'Minos Golding'
    |
    */
    protected $app_name = 'Minos Golding';


    /*
    |--------------------------------------------------------------------------
    | URL del Sitio Web
    |--------------------------------------------------------------------------
    |
    | Establece la URL de la aplicación
    | Esta se usa para cargar las rutas absolutas y el manejo de rutas en general
    | En caso de ser necesario puede establecerse un puerto
    |
    */
    protected $url = 'http://localhost';
    protected $port = '8080';


    /*
    |--------------------------------------------------------------------------
    | Zona Horaria
    |--------------------------------------------------------------------------
    |
    | Define el uso Horario, para mas información consulta: https://www.php.net/manual/es/timezones.php
    | También se debe definir el lenguaje de la aplicación
    | Esto, para mostrar ciertas configuraciones de PHP en el idioma especificado
    |
    */
    protected $timezone = 'America/Caracas';
    protected $locate   = 'es_VE';
}
