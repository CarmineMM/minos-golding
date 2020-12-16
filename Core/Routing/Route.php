<?php

namespace Core\Routing;

use Core\Helper\MainHelper;
use Core\Helper\RouteHelper;

class Route
{
    use \App\Config\Namespaces;

    /**
     * Obtiene las rutas registradas por el usuario
     *
     * @var mixed
     */
    public $routes;


    /**
     * Route constructor.
     */
    public function __construct()
    {
        $this->routes = new $this->register_routes;

        // Registra las rutas y nombres
        $this->globalAccessRoutes();
    }

    /**
     * Comprobación de rutas explicitas.
     * Rutas que el usuario define de forma explicita
     *
     * @param $request
     * @return bool
     */
    public function explicitRoutes($request)
    {
        if ( $this->routeWasFound($request) ) return false; # Verifica si una ruta fue encontrada

        // Trae las rutas disponibles por el verbo de entrada
        $route_in_verbs = $this->routes->getRoutes()[$request->method] ?? false;

        // Verifica si hay una ruta por el verbo de entrada
        if ( !$route_in_verbs ) return false;

        // Rutas que concuerde con la cantidad de paths y que requieran parámetros
        $routeParams = [];

        // Verifica ruta por ruta
        foreach ( $route_in_verbs as $route ) {
            // Ruta con la misma cantidad de explodes
            if ( count($request->url['explode']) === count( explode('/', $route['route']) ) ){
                $routeParams[] = $this->routeWithParameters($route, $request);
            }

            // Es una ruta directa?
            if ( $request->url['path'] === $route['route'] ){
                return $this->routeFind($route['action'], $request);
            }
        }

        foreach ( $routeParams as $routeParam ) {
            if ( $routeParam ) {
                return $this->routeFind($routeParam['action'], $request, $routeParam['parameters']??false);
            }
        }
        unset($routeParams);
        return false;
    }


    /**
     * Verifica si la ruta ya fue encontrada
     *
     * @param $request
     * @return bool
     */
    private function routeWasFound($request)
    {
        return $request->status ? true : false;
    }


    /**
     * Modifica el estado global del gb_request,
     * para indicar que encontró la ruta destino
     *
     * @param $executor
     * @param $request
     * @param bool $params
     * @return bool
     */
    private function routeFind($executor, $request, $params = false)
    {
        $executor = explode('::', $executor);

        $request->status = 200;
        $request->statusText = 'OK';
        $request->response = [
            'controller' => MainHelper::parseDirectory($executor[0]),
            'method' => $executor[1]
        ];

        if ( $params && is_array($params) ) {
            foreach ( $params as $key => $param ) {
                $request->response['parameters'][$key] = $param;
            }
        }

        return true;
    }


    /**
     * Verifica la ruta que pueda tener parámetros
     *
     * @param $theRoute
     * @param $request
     * @return false
     */
    private function routeWithParameters($theRoute, $request)
    {
        $theRouteExplode = explode('/', $theRoute['route']);
        $newRoute = false;


        foreach ($theRouteExplode as $key => $path) {
            if ( $request->url['explode'][$key] === $path ){
                $newRoute .= $path . '/';
            }elseif( strpos($path, ':') !== false ) {
                $newRoute .= $request->url['explode'][$key] . '/';
                $theRoute['parameters'][str_replace(':','',$path )] = $request->url['explode'][$key];
            }
        }

        if ( $newRoute && trim($newRoute, '/') === $request->url['path'] )
            return $theRoute;

        return false;
    }


    /**
     * Verifica las rutas implícitas
     *
     * @param $request
     * @return bool
     */
    public function implicitRoutes($request)
    {
        if ( $this->routeWasFound($request) ) return false; # Verifica si una ruta fue encontrada

        $controller = $this->controller . $this->routes->home_controller .$this->using_identify_controller;

        // -------------------------------------------
        // Verifica la ruta inicial
        // -------------------------------------------
        if( class_exists($controller)
            && $request->url['explode'][0] === ''
            && method_exists($controller, $this->routes->method_index)
            && count($request->url['explode']) === 1
        ) {
            return $this->routeFind("{$this->routes->home_controller}::{$this->routes->method_index}", $request);
        }
        // -------------------------------------------
        // Verifica ruta para el Home controller y un método
        // -------------------------------------------
        elseif ( class_exists($controller)
            && count($request->url['explode']) < 3
            && method_exists($controller, $request->url['explode'][0])
        ) {
            return $this->routeFind("{$this->routes->home_controller}::{$request->url['explode'][0]}", $request, [ $request->url['explode'][1]??false ]);
        }

        // Reemplaza el controlador por el de la ruta
        $controller = $this->controller . $request->url['explode'][0] . $this->using_identify_controller;

        // -------------------------------------------
        // Verifica ruta con un controlador y método index estén definidos
        // -------------------------------------------
        if ( count($request->url['explode']) === 1
            && class_exists($controller)
            && method_exists($controller, $this->routes->method_index)
        ){
            return $this->routeFind("{$request->url['explode'][0]}::{$this->routes->method_index}", $request);
        }
        // -------------------------------------------
        // Verifica ruta con un controlador y método index estén definidos
        // -------------------------------------------
        elseif (
            count($request->url['explode']) < 4
            && isset($request->url['explode'][1])
            && class_exists($controller)
            && method_exists($controller, $request->url['explode'][1])
        ){
            return $this->routeFind("{$request->url['explode'][0]}::{$request->url['explode'][1]}", $request, [ $request->url['explode'][2]??false ]);
        }

        return false;
    }


    /**
     * Registra el acceso global de las rutas
     * Y los nombres de las mismas
     */
    private function globalAccessRoutes()
    {
        $registry = [];
        global $route_helper;
        $route_helper = new RouteHelper();
        $count = 0;

        foreach($this->routes->getRoutes() as $route_per_verb ) {
            foreach ($route_per_verb as $route) {
                // Rutas que si tienen nombre
                if ( isset($route['name']) ) {
                    $registry[$count]['name'] = $route['name'];
                }

                $registry[$count]['action'] = $route['action'];
                $registry[$count]['route']  = $route_helper->current_uri().strtolower($route['route']);
                
                $count++;
            }
        }
        
        unset($count);
        $route_helper->setRoutes($registry);
    }
}