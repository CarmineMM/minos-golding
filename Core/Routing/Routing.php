<?php


namespace Core\Routing;


class Routing
{
    /**
     * Almacena las rutas registradas explícitamente
     *
     * @var array
     */
    private $routes = [];


    /**
     * Define el controlador HOME
     *
     * @var string
     */
    public $home_controller = 'Home';


    /**
     * Define el método iniciar de cualquier controlador
     *
     * @var string
     */
    public $method_index = 'index';


    /**
     * Grupo de rutas
     *
     * @var string
     */
    private $group = '';


    /**
     * Registra rutas por el verbo GET
     *
     * @param string $route 
     * @param string $action
     * @param bool $name
     * @return Routing
     */
    protected function get(string $route, string $action, $name = false)
    {
        return $this->registerRoute('GET', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo POST
     *
     * @param string $route
     * @param string $action
     * @param bool $name
     * @return Routing
     */
    protected function post(string $route, string $action, $name = false)
    {
        return $this->registerRoute('POST', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo PUT
     *
     * @param string $route
     * @param string $action
     * @param bool $name
     * @return Routing
     */
    protected function put(string $route, string $action, $name = false)
    {
        return $this->registerRoute('PUT', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo PATCH
     *
     * @param string $route
     * @param string $action
     * @param bool $name
     * @return Routing
     */
    protected function patch(string $route, string $action, $name = false)
    {
        return $this->registerRoute('PATCH', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo DELETE
     *
     * @param string $route
     * @param string $action
     * @param bool $name
     * @return Routing
     */
    protected function delete(string $route, string $action, $name = false)
    {
        return $this->registerRoute('DELETE', $route, $action, $name);
    }


    /**
     * Ruta recurso
     *
     * @param string $route
     * @param string $action Solo se necesita especificar el Controlador.
     * @param bool $name
     * @return Routing
     */
    protected function resource(string $route, string $action, $name = false)
    {
        $route = trim($route, '/');

        // Nombre no especificado
        if ( !$name ){
            $name = strtolower(str_replace('/', '\\', $action));

            if ( strpos($name, '\\') ) {
                $name = explode('\\', $name);
                $name = $name[count($name)-1];
            }
        }

        // Index
        $this->get($route, $action.'::index', $name.'.index');

        // Create
        $this->get($route.'/create', $action.'::create', $name.'.create');

        // Store
        $this->post($route, $action.'::store', $name.'.store');

        // Show
        $this->get($route.'/:id', $action.'::show', $name.'.show');

        // Edit
        $this->get($route.'/:id/edit', $action.'::edit', $name.'.edit');

        // Update
        $this->patch($route.'/:id', $action.'::update', $name.'.update');
        $this->put($route.'/:id', $action.'::update', $name.'.update');

        //Delete
        $this->delete($route.'/:id', $action.'::destroy', $name.'.destroy');

        return $this;
    }


    /**
     * Crea un grupo de rutas
     *
     * @param string $route
     * @param function $callback
     * @return Route
     */
    public function group(string $route, $callback)
    {
        if ( !is_callable($callback) ) return $this;

        if ( $this->group !== '' ) {
            $this->group = $this->group . $this->clearRouteToUser($route).'/';
        }

        // Limpiar un único grupo
        else $this->group = $this->clearRouteToUser($route) . '/';
        
        // Executer
        $callback($this);

        // Re-establecer el grupo
        $this->group = '';
    }

    /**
     * Registra rutas
     *
     * @param $type
     * @param $route
     * @param $action
     * @param bool $name
     * @return Routing
     */
    private function registerRoute($type, $route, $action, $name = false)
    {
        $register = [
            'route'  => $this->group . $this->clearRouteToUser($route),
            'action' => str_replace('/', '\\', trim($action, ' '))
        ];

        if ( $name ) $register['name'] = $name;

        $this->routes[$type][] = $register;
        return $this;
    }


    /**
     * Limpia la ruta que pase el usuario
     *
     * @param string $route
     * @return void
     */
    public function clearRouteToUser(string $route): string
    {
        return trim(trim($route, ' '), '/');
    }

    /**
     * Devuelve todas las rutas registradas
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}