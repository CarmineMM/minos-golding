<?php


namespace Core\Helper;



class RouteHelper
{
    /**
     * Request global
     *
     */
    private $global_request;

    /**
     * Instancia de el helper Main
     *
     * @var MainHelper
     */
    private $main_helper;


    /**
     * Obtendrá las rutas definidas por el nombre
     *
     * @var
     */
    private $routes;


    /**
     * RouteHelper constructor.
     */
    public function __construct()
    {
        global $gb_request;
        $this->global_request = $gb_request;

        $this->main_helper = new MainHelper();
    }


    /**
     * Redirect hacia un ruta especifica
     *
     * @param $to
     * @param int $status
     * @return mixed
     */
    public function redirect($to, $status = 302)
    {
        $this->global_request->status = $status;
        $this->global_request->statusText = 'Found';

        if ( stripos($to, 'http') === false ){
            $to = $this->main_helper->getUrl($to);
        }

        $this->global_request->response = [
            'redirect' => $to,

            // Scripts de ayuda en caso que la re-dirección falle
            'helpScript' => "
                <script type='text/javascript'>
                    window.location = '{$to}';
                </script>
            ",

            // Scripts HTML en caso que la re-dirección falle
            'helpScript_2' => "
                <noscript>
                    <meta http-equiv='refresh' content='0;url={$to}'>
                </noscript>
            "
        ];
        return $this->global_request;
    }


    /**
     * Redirige al usuario hacia atrás.
     * Según el historial de su navegador
     *
     * @param int $status
     * @param false $optional
     * @return mixed
     */
    public function back($status = 308, $optional = false)
    {
        $this->global_request->status     = $status;
        $this->global_request->statusText = 'Permanent Redirect';

        // Redirections go backs
        $back = $_SERVER['HTTP_REFERER'] ?? false;

        // Re-dirección opcional
        if ( $optional && !$back ) return $this->redirect($optional);


        $this->global_request->response = [
            'redirect' => $back,

            // Scripts de ayuda en caso que la re-dirección falle
            'helpScript' => "
                <script type='text/javascript'>
                    history.back();
                </script>
            ",

            // Scripts HTML en caso que la re-dirección falle
            'helpScript_2' => "
                <noscript>
                    <meta http-equiv='refresh' content='0;url={}'>
                </noscript>
            "
        ];

        return $this->global_request;
    }


    /**
     * URL actual
     *
     * @return mixed
     */
    public function current_uri(): string
    {
        return $this->global_request->url['full'];
    }


    /**
     * Path de la URL actual
     *
     * @return mixed
     */
    public function current_path(): string
    {
        return $this->global_request->url['path'];
    }


    /**
     * Establece las rutas
     *
     * @param mixed $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }


    /**
     * Obtiene las rutas registradas
     *
     * @return mixed Colección de todas las rutas
     */
    public function getRoutes()
    {
        return $this->routes;
    }


    /**
     * Obtiene una ruta especifica,
     * y agrega parámetros en caso de necesitas.
     * 
     * @param $route
     * @param mixed ...$params
     * @return mixed
     */
    public function getRoute($route, ...$params)
    {
        $route = $this->routes[$route];
        if ( !isset($route) ) return false;

        // Devolver la ruta si solo se pide
        if ( count($params[0]) === 0 ) return $this->main_helper->getUrl($route);

        $construct = '';
        $count = 0;
        foreach ( explode('/', $route) as $path ) {
            if ( strpos($path, ':') !== false ) {
                $construct .= isset($params[0][$count]) ? $params[0][$count] . '/' : $path;
                $count++;
            }else {
                $construct .= $path.'/';
            }
        }

        return $this->main_helper->getUrl($construct);
    }
}