<?php

namespace Core\Routing;

use Core\Errors\HttpException;

class Route
{
    use \App\Config\Namespaces;
    use \App\Config\Route;

    /**
     * URI del path de URLs
     *
     * @var array
     */
    private $uri = [];

    /**
     * Path original de la URL
     *
     * @var mixed
     */
    private $original_uri;

    /**
     * Contiene el Request Inicial
     */
    private $Request;


    /**
     * Route constructor.
     */
    public function __construct()
    {
        global $gb_request;

        // Original URI
        $this->original_uri = $gb_request->url['path'];

        // Partición de la URL
        $this->uri = $gb_request->url['explode'];

        // Asegura el Request
        $this->Request = new ValidationRequest( $this->original_uri );
    }


    /**
     * Valida el tipo de petición entrante
     *
     * @return ValidationRequest
     */
    public function getRequest()
    {
        return $this->Request;
    }


    /**
     * @return array
     */
    public function getUri()
    {
        return $this->uri;
    }


    /**
     * Devuelve el path de la url
     * 
     * @return String
     */
    public function getOriginalUri()
    {
        return $this->original_uri;
    }


    /**
     * Valida la ruta si hay alguno de los métodos crud en el controlador
     *
     * @param $server_request
     * @return array|int
     */
    public function using_CRUD_methods( $server_request )
    {
        // Verificar si son 3 parámetros
        $controller = $this->controller . $this->uri[0] . $this->using_identify_controller;

        // Verifica si el controlador no existe
        if ( !class_exists( $controller ) ) return 404;

        $class = new $controller;

        $verifyUrlCrud = $this->verifyIsUrlCrud( $controller );

        if ( method_exists( $controller, $this->uri[1] ) && $verifyUrlCrud === 'another' ){
            $method = $this->uri[1];
            return $class->$method( $server_request, $this->uri[2] ?? false );
        }
        elseif ( method_exists($controller, $verifyUrlCrud) ) {
            $method = $verifyUrlCrud;
            return $class->$method( $server_request, $this->uri[1] ?? false );
        }

        return 404;
    }

    /**
     * Hace un conjunto de evaluaciones para comprobar que el URI es un Método CRUD
     *
     * @param $controllerRefer
     * @return string
     */
    private function verifyIsUrlCrud( $controllerRefer )
    {
        $show   = $this->CRUD_methods['show'];
        $store  = $this->CRUD_methods['store'];
        $edit   = $this->CRUD_methods['edit'];
        $update = $this->CRUD_methods['update'];
        $delete = $this->CRUD_methods['delete'];


        // Comprobar método show
        if (
            $this->Request->getRequestMethod() === $edit['http_verb'] &&
            isset($this->uri[1]) && isset($this->uri[2]) &&
            $this->uri[2] === $edit['identify'] &&
            method_exists( $controllerRefer, $edit['class_method'] )
        )
            return $edit['class_method'];

        // Método Show
        elseif (
            $this->Request->getRequestMethod() === $show['http_verb'] &&
            isset($this->uri[1]) &&
            method_exists( $controllerRefer, $show['class_method'] )
        )
            return $show['class_method'];

        // Método Store
        elseif (
            $this->Request->getRequestMethod() === $store['http_verb'] &&
            isset($this->uri[1]) &&
            $this->uri[1] === $store['identify'] &&
            method_exists( $controllerRefer, $store['class_method'] )
        )
            return $store['class_method'];

        // Método Update
        elseif (
            $this->Request->getRequestMethod() === $update['http_verb'] &&
            isset($this->uri[1]) &&
            method_exists( $controllerRefer, $update['class_method'] )
        )
            return $update['class_method'];

        // Método Delete
        elseif (
            $this->Request->getRequestMethod() === $delete['http_verb'] &&
            isset($this->uri[1]) &&
            method_exists( $controllerRefer, $delete['class_method'] )
        )
            return $delete['class_method'];

        return 'another';
    }
}