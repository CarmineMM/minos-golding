<?php


namespace Core\Foundation;


use Core\Errors\HttpException;
use Core\Helper\MainHelper;
use Core\Helper\SupportHelper;
use Core\Routing\ValidationRequest;

class View extends MainHelper
{
    use \App\Config\FileSystem;

    /**
     * Layout
     *
     * @var string
     */
    private $layout = false;
    private $current_section;
    private $contents;
    private $sections = [];


    /**
     * Parámetros pasados a la vista
     *
     * @var array
     */
    private $params = [];


    /**
     * Rendering una vista
     *
     * @param $view
     * @param array $data
     * @return string
     */
    public function render($view, $data = [])
    {
        return $this->actionRendering($view, $data);
    }


    /**
     * Rendering vistas internas
     *
     * @param $view
     * @param array $data
     * @return string
     */
    public function internalRender($view, $data = [])
    {
        return $this->actionRendering($view, $data, 'internal');
    }


    /**
     * Método padre para el rendering
     *
     * @param $view
     * @param array $data
     * @param string $type
     * @return string
     */
    private function actionRendering(string $view, $data = [], $type = 'user')
    {
        $file = '';


        // Convierte el directorio
        $view = $this->parse_directory($view);

        // Generar variables a partir de los valores pasados
        foreach ( $data as $key => $value ) {
            if ( is_array($value) ) $$key = SupportHelper::to_object($value);
            else $$key = $value;
        }
        $this->params = $data; // Asigna los parameters pasados, para re-usarlos en los includes

        if ( $type === 'user' )
            $file = G_PATH . $this->parse_directory($this->views) . DG . $view . $this->view_file_type;
        elseif ( $type === 'internal' )
            $file = $this->parse_directory($view);

        // Error si no existe el archivo
        if ( !is_file($file) ) {
            global $gb_request;
            $gb_request->warningApp[] = "Falla al cargar la vista: <b>{$view}</b>";
            $gb_request->warningApp[] = "Path de la vista: <b>{$file}</b>";
            $gb_request->warningApp[] = 'Tipo de dato pasado por parámetro: <b>'.gettype($view).'</b>';

            return HttpException::internal_server_error_500();
        }

        ob_start();
        require_once $file;
        return ob_get_clean();
    }



    /**
     * Iniciar un sección
     *
     * @param string $section
     */
    public function start( string $section )
    {
        $this->current_section = $section;
        ob_start();
    }


    /**
     * Finaliza la section
     *
     * @return string
     */
    public function end()
    {
        $contents = ob_get_clean();
        $this->sections[$this->current_section] = $contents;
        $this->current_section = null;

        // Si sale bien, incluye la sección
        if ( count($this->sections) && $this->layout && is_file($this->layout) )
        {
            foreach ( $this->params as $key => $value ) {
                if ( is_array($value) ) $$key = SupportHelper::to_object($value);
                else $$key = $value;
            }
            include_once $this->layout;
        }
        // Devuelve un error 500 al fallar al cargar el layout
        else {
            global $gb_request;
            HttpException::internal_server_error_500();
            $gb_request->warningApp[] = "Falla al cargar el Layout: <b>{$this->layout}</b>";
        }
    }


    /**
     * Extiende de un layout
     *
     * @param string $layout
     */
    public function extend( string $layout )
    {
        $this->layout = G_PATH . $this->parse_directory($this->views) . DG . $this->parse_directory($layout) . $this->view_file_type;
    }


    /**
     * Establece una sección en un layout
     *
     * @param $name
     */
    public function section( $name )
    {
        foreach ( $this->sections as $key => $content ){
            if ( $name === $key ) echo $content;
        }
    }

    /**
     * Procesa los includes
     *
     * @param $file
     * @return false
     */
    public function include($file)
    {
        $include = G_PATH . $this->parse_directory($this->views) . DG . $this->parse_directory($file) . $this->view_file_type;

        if ( !is_file($include) )
        {
            global $gb_request;
            $gb_request->warningApp[] = "No se pudo incluir el archivo: {$file}";
            $gb_request->warningApp[] = "En la ruta: {$include}";
            return false;
        }

        foreach ( $this->params as $key => $value ) {
            if ( is_array($value) ) $$key = SupportHelper::to_object($value);
            else $$key = $value;
        }
        include $include;
    }
}