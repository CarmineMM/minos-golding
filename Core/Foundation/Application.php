<?php

namespace Core\Foundation;

use Core\Errors\HttpException;
use Core\Errors\WarningApp;
use Core\Routing\Route;
use Core\Routing\Request;
use Core\Routing\ValidationRequest;

class Application extends Kernel
{
    use \App\Config\Namespaces;

    /**
     * Lo que va a rendering
     * por defecto sera un 404
     */
    private $dispatcher = 404;

    /**
     * Request del servidor
     *
     * @var Request
     */
    private $server_request;

    /**
     * Inicia la aplicación
     */
    public function run()
    {
        // Carga el controlador estimado
        $current_controller = $this->loadController();

        // Realiza Dispatcher
        $this->dispatcher( $current_controller );
    }

    /**
     * Carga el controlador según sea el caso
     */
    private function loadController()
    {
        global $gb_request;

        $route = new Route();

        // Valida el método de entrada de la petición
        if( $gb_request->status === 405 ) {
            return $route->getRequest()->getValidateMethod();
        }

        // Controlador inicial, este seria el HomeController
        $controller_home = $this->controller . $this->home_controller . $this->using_identify_controller;
        $class_home      = new $controller_home;

        // Comprobar que sea la ruta raíz
        if ($route->getOriginalUri() === ''
            && class_exists($controller_home)
            && method_exists($controller_home, $this->init_method)
        ) {
            $method = $this->init_method;
            $this->dispatcher = $class_home->$method($gb_request);
        }
        // Comprueba un único parámetro en la ruta
        else {
            // Controlador que concuerde con la petición
            $controller = $this->controller . $route->getUri()[0] . $this->using_identify_controller;

            // Verifica si existe una clase con el primer parámetro del uri
            if ( class_exists($controller) ) {
                $class = new $controller;

                // Si solo existe un parámetro en la uri, llama al método index de dicho controlador
                if (count($route->getUri()) === 1) {
                    $method = $this->init_method;
                    $this->dispatcher = $class->$method($this->server_request);
                }
                // Verificar si el usuario esta intentando hacer un CRUD
                else {
                    $this->dispatcher = $route->using_CRUD_methods( $gb_request );
                }
            }
            // Comprueba si se esta llamando un método del controlador raíz
            elseif (
                class_exists($controller_home) &&
                method_exists( $controller_home, $route->getUri()[0] ) &&
                count($route->getUri()) <= 2
            ){
                $method = $route->getUri()[0];
                $this->dispatcher = $class_home->$method($gb_request, $route->getUri()[1] ?? true);
            }
        }

        // Detener la ejecución al llamar al error 404
        if ($this->dispatcher === 404){
            return $this->dispatcher = HttpException::not_found_404();
        }

        // Errores internos
        if( $gb_request->status === 500 ) {
            $this->dispatcher = HttpException::internal_server_error_500();
        }

        if ( isset($gb_request->status) && $gb_request->status === 200  ){
            header("HTTP/1.5 200 OK");
            http_response_code(200);
        }

        return $this->dispatcher;
    }

    /**
     * Realiza el Dispatcher de la aplicación
     *
     * @param $render
     * @return bool
     */
    private function dispatcher( $render )
    {
        global $gb_view;
        global $gb_request;

        // Rendering para ajax los objetos y los arrays
        if( ValidationRequest::isAjax() ) {
            if ( $gb_request->status === 200 && !isset($render->status) ) $gb_request->response = $render;
            echo json_encode( $gb_request );
        }

        // Dispatcher para re-direccionamiento
        elseif ( isset($render->status) && ($render->status === 302 || $render->status === 308) ) {
            http_response_code($render->status);
            header("Location: ".$render->response, true, $render->status);

            $force_redirect = "<script type='text/javascript'>window.location.href = '". $render->response ."'</script>";
            $force_redirect .= "<noscript><meta http-equiv='refresh' content='0;url=\"". $render->response ."\"' /></noscript>";
            echo $force_redirect;
        }

        // Rendering a un error HTTP
        elseif ( $gb_request->status !== 200 ) {
            $status     = $gb_request->status;
            $statusText = $gb_request->statusText;
            echo $gb_view->internalRender($gb_request->response, compact('status', 'statusText'));
        }

        // Rendering Strings, Booleanos, Números
        elseif ( is_string($render) || is_bool($render) || is_numeric($render) ) echo $render;

        // Rendering Arrays
        elseif ( is_array($render) || is_object($render) ) showDev($render);

        // Liberar memoria
        unset($render);
        return true;
    }

    /**
     * Method Destruct
     */
    public function __destruct()
    {
        global $gb_request;
        $warning_app = new WarningApp();

        // Destruye cualquier mensaje Flasher al final de la ejecución de la aplicación
        Flasher::destroyerFlasherMessages();

        // Muestra las advertencias dentro de la aplicación
        $warnings = $warning_app->showWarningsApp();

        // Solo muestra los warnings si existen
        if ( $warnings ) echo $warnings;

        // Muestra el tiempo de ejecución de la aplicación
        $warning_app->showTimingApp( !$warnings, $this->start_time, self::frameworkName, self::frameworkVersion );

        // Liberar memoria
        unset($this->dispatcher);
        unset($gb_view);
        unset($gb_request);
    }
}