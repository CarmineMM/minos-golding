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
     * Inicia la aplicación
     */
    public function run()
    {
        global $gb_request;
        $route      = new Route();
        $validation = new ValidationRequest($gb_request);

        // Fuerza un verbo HTTP, que indique el usuario
        $validation->forceMethod($gb_request);

        // Valida los CSRF
        $validation->csrf($gb_request);

        // Comprobación de rutas definidas
        $route->explicitRoutes($gb_request);

        // Comprueba las rutas implícitas (Aquellas que van directamente a los controladores)
        $route->implicitRoutes($gb_request);

        // Carga el controlador llamado
        try {
            $this->loadController($gb_request);
        }
        catch (\Exception $e) {
            HttpException::internal_server_error_500();
        }

        $this->dispatcher($gb_request);
    }


    /**
     * Carga el controlador según sea el caso
     *
     * @param $request
     * @return array|Request
     */
    private function loadController($request)
    {
        // Ruta no pudo ser encontrada
        if ( $request->status === false ) {
            return HttpException::not_found_404();
        }
        elseif ( $request->status === 200 ){
            $controller = $this->controller.ucfirst($request->response['controller']).$this->using_identify_controller;
            $method = $request->response['method'];

            if ( !method_exists($controller, $method) ) return HttpException::not_found_404();

            $sendParams = false;

            // Comprobación de parámetros
            if ( isset($request->response['parameters']) )
            {
                if ( count($request->response['parameters']) === 1 ){
                    $sendParams = array_pop($request->response['parameters']);
                }
                else {
                    $sendParams = $request->response['parameters'];
                }
            }

            // Instantiations
            $instance = new $controller;
            $executor = $instance->$method($request, $sendParams);

            // Comprobación final de que marche bien
            if ( $request->status === 200 ){
                // Valida que el usuario este devolviendo el Request
                if ( $executor === $request ){
                    $request->response = null;
                    return $request;
                }
                // Cualquier otra cosa devuelta es agregada al response
                return $request->response = $executor;
            }
        }
        return $request;
    }

    /**
     * Realiza el Dispatcher de la aplicación
     *
     * @param $request
     * @return void
     */
    private function dispatcher( $request )
    {
        global $gb_view;
        $status     = $request->status;
        $statusText = $request->statusText;

        // -------------------------------------------
        // Verificación de Ajax
        // -------------------------------------------
        if ( ValidationRequest::isAjax() ) {
            echo json_encode($request);
        }
        // -------------------------------------------
        // Verifica la ruta 200
        // -------------------------------------------
        elseif ( $request->status === 200 )
        {
            if ( is_array($request->response) || is_object($request->response) )
                showDev($request->response);

            elseif( is_string($request->response) || is_bool($request->response) || is_numeric($request->response) )
                echo $request->response;

            else showDev($request);
        }
        // -------------------------------------------
        // Errores en el HTTP - 404, 405, 406, 500...
        // -------------------------------------------
        elseif( $request->status !== 302 && $request->status !== 308 ) {
            $echo = $gb_view->internalRender($request->response, compact('status', 'statusText'));
            if ( $request->status !== 500 )  echo $echo;
        }
        // -------------------------------------------
        // Rutas de redirections registradas
        // -------------------------------------------
        elseif ($request->status === 302 || $request->status === 308) {
            http_response_code($request->status);
            if ( isset($request->response['redirect']) && $request->response['redirect'] !== false ) {
                header("Location: ".$request->response['redirect']);
            }

            echo isset($request->response['helpScript'])   ? $request->response['helpScript']   : '';
            echo isset($request->response['helpScript_2']) ? $request->response['helpScript_2'] : '';
        }


        // -------------------------------------------
        // Si cualquier cosa sale, mal: 500
        // -------------------------------------------
        if( $request->status === 500 )  {
            echo $gb_view->internalRender($request->response, compact('status', 'statusText'));
        }

        return '';
    }

    /**
     * Method Destruct
     */
    public function __destruct()
    {
        global $gb_request;
        global $route_helper;
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
        unset($route_helper);
    }
}