<?php


namespace Core\Routing;


use Core\Foundation\Render;
use Core\Errors\HttpException;

class ValidationRequest
{
    use \App\Config\Csrf;


    /**
     * ValidationRequest constructor.
     *
     * @param $request
     */
    public function __construct( $request )
    {
        // Apertura del Ajax
        if ( ValidationRequest::isAjax() ) {
            $_POST = json_decode( file_get_contents("php://input"), true );
            header("Access-Control-Allow-Methods: PUT, PATCH, DELETE, POST, GET");
            
            if ( isset($_SERVER['HTTP_CONTENT_TYPE']) ) {
                header("Access-Control-Allow-Headers: ".$_SERVER['CONTENT_TYPE']);   
            }
            else header("Access-Control-Allow-Headers: Content-Type");

            if ( $this->open_CORS )
                header("Access-Control-Allow-Origin: *");

            $request->post = (object)$_POST; // Evita el reset por el método POST
        }
    }


    /**
     * Valida si el usuario quiere forzar un verbo HTTP
     *
     * @param $request
     */
    public function forceMethod($request)
    {
        // Pasear método de forma arcaica
        if ( isset($_POST['_method']) && strtoupper($_POST['_method']) !== 'GET' ) {
            $request->method = strtoupper( $_POST['_method'] );
        }
    }


    /**
     * Valida la llegada y uso de los CSRF
     *
     * @param $request
     * @return mixed
     */
    public function csrf($request)
    {
        $csrf = new Csrf();

        // Verifica si se desea usar CSRF
        if ($csrf->getUseCsrf() && array_search($request->method, $csrf->getMethodsToVerify()) !== false)
        {
            // Saber si viene el Token en algún encabezado
            if ( !isset($request->post->_csrf) || !$csrf->validateCsrfToken($request->post->_csrf) ) {
                return HttpException::not_acceptable_406();
            }
        }
        return true;
    }



    /**
     * Saber si una petición es por ajax
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return false;
        return ( isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['REQUEST_TIME']) ) 
            || ( isset($_SERVER['HTTP_CONTENT_TYPE']) && isset($_SERVER['HTTP_CONTENT_LENGTH']) );
    }
}