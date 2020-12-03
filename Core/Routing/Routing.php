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
     * Registra rutas por el verbo GET
     *
     * @param $route
     * @param $action
     * @param bool $name
     * @return Routing
     */
    protected function get($route, $action, $name = false)
    {
        return $this->registerRoute('GET', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo POST
     *
     * @param $route
     * @param $action
     * @param bool $name
     * @return Routing
     */
    protected function post($route, $action, $name = false)
    {
        return $this->registerRoute('POST', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo PUT
     *
     * @param $route
     * @param $action
     * @param bool $name
     * @return Routing
     */
    protected function put($route, $action, $name = false)
    {
        return $this->registerRoute('PUT', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo PATCH
     *
     * @param $route
     * @param $action
     * @param bool $name
     * @return Routing
     */
    protected function patch($route, $action, $name = false)
    {
        return $this->registerRoute('PATCH', $route, $action, $name);
    }

    /**
     * Registra rutas por el verbo DELETE
     *
     * @param $route
     * @param $action
     * @param bool $name
     * @return Routing
     */
    protected function delete($route, $action, $name = false)
    {
        return $this->registerRoute('DELETE', $route, $action, $name);
    }


    /**
     * Ruta recurso
     *
     * @param $route
     * @param $action
     * @param bool $name
     */
    protected function resource($route, $action, $name = false)
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

        //Delete
        $this->delete($route.'/:id', $action.'::destroy', $name.'.destroy');
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
            'route'  => trim(trim($route, ' '), '/'),
            'action' => str_replace('/', '\\', trim($action, ' '))
        ];

        if ( $name ) $register['name'] = $name;

        $this->routes[$type][] = $register;
        return $this;
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