<?php

namespace Core\Routing;

use Core\Helper\MainHelper;

class Request
{
    /**
     * Todas las peticiones de ambos tipos
     */
    public $get;
    public $post;


    /**
     * Urls entrante
     */
    public $url;


    /**
     * Método de entrada
     */
    public $method;


    /**
     * Menciona si la petición es ajax
     */
    public $ajax;


    /**
     * Respuestas de estado
     * En código, y texto
     */
    public $status = 200;
    public $statusText = 'OK';


    /**
     * Objeto habilitado para mandar respuestas
     *
     * @var array
     */
    public $response = [];


    /**
     * Version de PHP donde se ejecuta el servidor
     *
     * @var string
     */
    public $phpVersion;


    /**
     * Path hacia la carpeta publica
     *
     * @var string
     */
//    public $publicPath;


    /**
     * Advertencias internas de la aplicación
     *
     * @var array
     */
    public $warningApp = [];


    /**
     * Entorno de la aplicación
     *
     * @var string
     */
    public $environment = 'unknown';


    /**
     * Request constructor.
     *
     */
    public function __construct()
    {
        $helper = new MainHelper();

        // Variables de GET y POST
        $this->get  = (object)$_GET;
        $this->post = (object)$_POST;

        // Path original
        $path    = trim($_SERVER['REQUEST_URI'], '/');

        if ( strpos($path, '?') !== false )
        {
            $path = trim(
                $path,
                substr(
                    $path,
                    strpos($path,
                        '?'
                    ),
                    strlen($path)
                )
            );
        }

        // Url entrante
        $this->url = [
            'full'    => $helper->getUrl( trim($_SERVER['REQUEST_URI'], '/') ),
            'path'    => $path,
            'explode' => explode('/', $path )
        ];

        // Método entrante a la app
        $this->method =  $_SERVER['REQUEST_METHOD'];

        // Verifica si es Ajax
        $this->ajax = ValidationRequest::isAjax();

        // Version actual de PHP del servidor
        $this->phpVersion = phpversion();

        // Path hacia la carpeta publica
//        $this->publicPath = PUBLIC_PATH; // No mandar el path publico
    }
}
