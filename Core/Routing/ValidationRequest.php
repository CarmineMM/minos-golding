<?php


namespace Core\Routing;


use Core\Foundation\Render;
use Core\Errors\HttpException;

class ValidationRequest
{
    use \App\Config\Route;

    /**
     * Método de entrada
     *
     * @var mixed
     */
    private $request_method;

    /**
     *
     * @var array
     */
    private $validate_method;

    /**
     * Server request
     * @var Request
     */
    private $server_request;


    /**
     * ValidationRequest constructor.
     *
     * @param $current_uri
     */
    public function __construct( $current_uri )
    {
        global $gb_request;

        $this->server_request = $gb_request;

        $this->request_method = $gb_request->method;

        // Pasear método de forma arcaica
        if ( isset($_POST['_method']) ) {
            $this->request_method = strtoupper( $_POST['_method'] );
        }

        // Apertura del Ajax
        if ( ValidationRequest::isAjax() ) {
            $_POST = json_decode( file_get_contents("php://input"), true );
            header("Access-Control-Allow-Methods: PUT, PATCH, DELETE, POST, GET");
            header("Access-Control-Allow-Headers: Content-Type");

            if ( $this->open_CORS )
                header("Access-Control-Allow-Origin: *");

            $gb_request->post = (object)$_POST; // Evita el reset por el método POST
        }


        // Validar el método del request
        $this->validate_method = $this->validate_request_method( $current_uri );

        // Instancia el CSRF
        $csrf = new Csrf();

        // Verifica si se desea usar CSRF
        if ($csrf->getUseCsrf() && array_search($this->request_method, $csrf->getMethodsToVerify()) !== false && $gb_request->status === 200)
        {
            // Saber si viene el Token en algún encabezado
            if ( !isset($gb_request->post->_csrf) || !$csrf->validateCsrfToken($gb_request->post->_csrf) ) {
                return HttpException::not_acceptable_406();
            }
        }
    }


    /**
     * Valida los métodos de entrada a la aplicación
     *
     * @param $current_uri
     * @return Request
     */
    private function validate_request_method( $current_uri )
    {
        // Rutas solo GET
        foreach ($this->only_get as $uri) {
            $uri = trim($uri, '/');


            if ( $current_uri === $uri && $this->request_method !== 'GET' ){
                return HttpException::method_not_allowed_405();
            }

            // Revisa si el usuario quiere que sea cualquier ruta a partir de una determinada
            $uri = explode('/', $uri);
            $current_uri_explode = explode('/', $current_uri);

            if ( count($uri) === count($current_uri_explode) && $uri[count($uri)-1] === '*' ) {
                return HttpException::method_not_allowed_405();
            }
        }

        // Ruta solo post
        foreach ($this->only_post as $uri) {
            $uri = trim($uri, '/');

            if ( $current_uri === $uri && $this->request_method !== 'POST' ){
                return HttpException::method_not_allowed_405();
            }

            // Revisa si el usuario quiere que sea cualquier ruta a partir de una determinada
            $uri = explode('/', $uri);
            $current_uri = explode('/', $current_uri);

            if ( count($uri) === count($current_uri) && $uri[count($uri)-1] === '*' ) {
                return HttpException::method_not_allowed_405();
            }

        }

        return $this->server_request;
    }


    /**
     * Saber si una petición es por ajax
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        // TODO: Mejorar esta función, hacerla mas exacta
        return isset($_SERVER['HTTP_REFERER']);
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->request_method;
    }

    /**
     * @return Request
     */
    public function getValidateMethod()
    {
        return $this->validate_method;
    }

    /**
     * @return Request
     */
    public function getServerRequest()
    {
        return $this->server_request;
    }
}