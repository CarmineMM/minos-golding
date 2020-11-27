<?php


namespace Core\Errors;

use App\Config\FileSystem;
use Core\Helper\MainHelper;
use Core\Routing\Request;
use Core\Routing\ValidationRequest;

class HttpException extends MainHelper
{
    use FileSystem;

    /**
     * Vista interna para los errores HTTP
     *
     * @var string
     */
    private $views_internal_http_errors = 'Core\InternalViews\http_exceptions.php';


    /**
     * Devuelve la vista definida por el usuario, o una vista interna
     *
     * @param $type_error
     * @return string
     */
    public function view_to_render( $type_error )
    {
        $view = '';
        if ( isset($this->error_views[$type_error]) )
            $view = G_PATH . $this->parse_directory($this->views) . DG . $this->parse_directory($this->error_views[$type_error]);
        return is_file($view) ? $view : G_PATH.$this->views_internal_http_errors;
    }


    /**
     * Rendering un error 404
     *
     * @return array|Request
     */
    public static function not_found_404()
    {
        $self = new self();
        return $self->constructorError(
            "HTTP/1.5 404 Not Found",
            404,
            'Not Found'
        );
    }

    /**
     * Método no aceptado
     *
     * @return array|Request
     */
    public static function method_not_allowed_405()
    {
        $self = new self();
        return $self->constructorError(
            "HTTP/1.5 405 Method Not Allowed",
            405,
            'Method Not Allowed'
        );
    }


    /**
     * Errores del lado del servidor
     *
     * @return Request
     */
    public static function internal_server_error_500()
    {
        $self = new self();
        return $self->constructorError(
            "HTTP/1.5 500 Internal Server Error",
            500,
            'Internal Server Error'
        );
    }


    // TODO: Hacer un error para los CSRF
    public static function not_acceptable_406()
    {
        $self = new self();
        return $self->constructorError(
            "HTTP/1.5 406 Not Acceptable",
            406,
            'Not Acceptable'
        );
    }

    /**
     * Constructor de errores
     *
     * @param $header
     * @param $type
     * @param $statusText
     * @return mixed
     */
    public function constructorError($header, $type, $statusText)
    {
        global $gb_request;

        $gb_request->status = $type;
        $gb_request->statusText = $statusText;

        // Construir respuesta Ajax
        if (ValidationRequest::isAjax()) return $gb_request;

        $self = new self();

        header($header);
        http_response_code($type);
        $gb_request->response = $self->view_to_render( $type );

        // Vista de los errores http
        return $gb_request;
    }
}