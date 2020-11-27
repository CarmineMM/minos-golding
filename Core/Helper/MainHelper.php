<?php

namespace Core\Helper;

use Core\Foundation\Config;

class MainHelper extends Config
{

    /**
     * URL de la aplicación
     *
     * @param string $uri
     * @return string
     */
    public function getUrl( $uri = '' )
    {
        return $this->port
            ? $this->url .':' . $this->port . '/' . trim($uri, '/')
            : $this->url . '/' . trim($uri, '/');
    }


    /*
     * Nombre de la aplicación
     *
     * @return string
     */
    public function getAppName(): string
    {
        return $this->app_name;
    }


    /**
     * Devuelve el entorno de la aplicación
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }


    /**
     * Transforma el directorio pasado en uno compatible con sistemas UNIX
     * También compatible con sistemas Windows
     *
     * @param $directory
     * @return string
     */
    public function parse_directory( string $directory ): string
    {
        return str_replace('/', DG, trim($directory, '/'));
    }


    /**
     * Devuelve el nombre oficial del framework
     *
     * @return string
     */
    public function getFrameworkName(): string
    {
        return defined('FRAMEWORK_NAME') ? FRAMEWORK_NAME : 'Minos Golding';
    }


    /**
     * Devuelve la version oficial del framework
     *
     * @return string
     */
    public function getFrameworkVersion(): string
    {
        return defined('FRAMEWORK_VERSION') ? FRAMEWORK_VERSION : '1.0';
    }


    /**
     * Devuelve el nombre del desarrollador oficial
     *
     * @return string
     */
    public function getFrameworkDeveloper(): string
    {
        return defined('FRAMEWORK_DEVELOPER') ? FRAMEWORK_DEVELOPER : 'Carmine Maggio';
    }
}