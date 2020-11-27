<?php


namespace App\Config;


trait Database
{
    /*
    |--------------------------------------------------------------------------
    | Connexion por defecto
    |--------------------------------------------------------------------------
    |
    | Establece una connexion a una base de datos
    | Este parámetro es usado después para establecer una correcta connexion
    | Parameters admitidos, son mysql, sqlite
    |
    */
    protected $drive = 'mysql';


    /*
    |--------------------------------------------------------------------------
    | Configuración de las conexiones
    |--------------------------------------------------------------------------
    |
    | Configuraciones iniciales para la connexion con la base de datos
    | Se pueden dejar en blanco los valores que no se necesiten
    | Si no es necesario, no se requiere modificar el puerto
    |
    */
    protected $host     = 'localhost';     # URL a la Base de datos
    protected $database = 'minos_golding'; # Nombre de la base de datos
    protected $username = 'root';          # Usuario de acceso
    protected $password = '';              # Contraseña de acceso
    protected $port_db  = 3306;            # Puerto de acceso (Por defecto 3306 para mysql)
}