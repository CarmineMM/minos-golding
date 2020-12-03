<?php

/**
 * Obtiene la información de algún campo configurado
 *
 * @param string $type
 * @param string $optional_param
 * @return string
 */
function get_info( $type = 'app_name', $optional_param = '' ) {
    $mainHelper = new \Core\Helper\MainHelper();

    switch ($type) {
        case 'app_name': return $mainHelper->getAppName();

        case 'app_url': return $mainHelper->getUrl($optional_param);

        case 'app_environment': return $mainHelper->getEnvironment();

        case 'framework_name': return $mainHelper->getFrameworkName();

        case 'framework_version': return $mainHelper->getFrameworkVersion();

        case 'framework_developer': return $mainHelper->getFrameworkDeveloper();
    }

    return false;
}

//--------------------------------------------------------------------

/**
 * Acciones posibles en torno a la clase Flasher
 *
 * @param $type
 * @param string $action
 * @param string $message
 * @param bool $key
 * @return array|bool
 */
function flasher($type, $action = 'get', $message = '', $key = false)
{
    $f = new \Core\Foundation\Flasher();

    if ( $action === 'get' )
    {
        switch ($type){
            case 'n': return $f->getNotification($key);
            case 'w': return $f->getWarning($key);
            case 'e': return $f->getError($key);
        }
    }
    elseif ( $action === 'set' )
    {
        switch ($type){
            case 'n': return $f->setNotification($message, $key);
            case 'w': return $f->setWarning($message, $key);
            case 'e': return $f->setError($message, $key);
        }
    }
    return false;
}

//--------------------------------------------------------------------

/**
 * Instancia de ViewHelper
 * Ayudantes que solo deberían ser ejecutados en las vitas
 *
 * @param $type
 * @param mixed ...$optional_param
 * @return false|mixed|string
 */
function viewHelper($type, ...$optional_param)
{
    $viewHelper = new \Core\Helper\ViewHelper();

    switch ($type){
        case 'title': return $viewHelper->title(...$optional_param);

        case 'csrf': return $viewHelper->csrf();

        case 'csrf_input': return $viewHelper->csrf_input();

        case 'method': return $viewHelper->method($optional_param);
    }

    return false;
}

//--------------------------------------------------------------------

/**
 * Devuelve las notificaciones flash
 *
 * @param bool $key
 * @return array|bool
 */
function get_notifications($key = false)
{
    return flasher('n', 'get', '', $key);
}

//--------------------------------------------------------------------

/**
 * Devuelve las advertencias flash
 *
 * @param bool $key
 * @return array|bool
 */
function get_warnings($key = false)
{
    return flasher('w', 'get', '', $key);
}

//--------------------------------------------------------------------

/**
 * Devuelve los errores flash
 *
 * @param bool $key
 * @return array|bool
 */
function get_errors($key = false)
{
    return flasher('e', 'get', '', $key);
}

//--------------------------------------------------------------------

/**
 * Establece una notificación en los mensajes flashers
 *
 * @param $message
 * @param bool $key
 * @return array|bool
 */
function set_notifications($message, $key = false)
{
    return flasher('n', 'set', $message, $key);
}

//--------------------------------------------------------------------

/**
 * Establece una advertencia en los mensajes flashers
 *
 * @param $message
 * @param bool $key
 * @return array|bool
 */
function set_warning($message, $key = false)
{
    return flasher('w', 'set', $message, $key);
}

//--------------------------------------------------------------------

/**
 * Establece un error en los mensajes flashers
 *
 * @param $message
 * @param bool $key
 * @return array|bool
 */
function set_error($message, $key = false)
{
    return flasher('e', 'set', $message, $key);
}

//--------------------------------------------------------------------

/**
 * Incluye un archivo partiendo de las vistas
 *
 * @param $file
 * @return false|void
 */
function include_f($file)
{
    return \Core\Helper\ViewHelper::include_f($file);
}

//--------------------------------------------------------------------

/**
 * Devuelve el nombre de aplicación
 *
 * @return false|string
 */
function app_name()
{
    return get_info();
}

//--------------------------------------------------------------------

/**
 * URL de la aplicación
 *
 * @param string $uri
 * @return false|string
 */
function app_url( $uri = '' )
{
    return get_info('app_url', $uri);
}

//--------------------------------------------------------------------

/**
 * Función para depurar
 *
 * @param $print
 * @param bool $vardump
 * @return void
 */
function showDev($print, $vardump = true)
{
    \Core\Helper\SupportHelper::showDev($print, $vardump);
}

//--------------------------------------------------------------------

/**
 * Devuelve el entorno de la aplicación
 *
 * @return false|string
 */
function app_environment()
{
    return get_info('app_environment');
}

//--------------------------------------------------------------------

/**
 *  Devuelve el nombre oficial del framework
 *
 * @return false|string
 */
function framework_name()
{
    return get_info('framework_name');
}

//--------------------------------------------------------------------

/**
 *  Devuelve la version oficial del framework
 *
 * @return false|string
 */
function framework_version()
{
    return get_info('framework_version');
}

//--------------------------------------------------------------------

/**
 *  Devuelve el nombre del desarrollador oficial
 *
 * @return false|string
 */
function framework_developer()
{
    return get_info('framework_developer');
}

//--------------------------------------------------------------------

/**
 * Convierte un Array en un Objeto
 *
 * @param $array
 * @return Object
 */
function to_object($array)
{
    return \Core\Helper\SupportHelper::to_object($array);
}

//--------------------------------------------------------------------

/**
 * Devuelve una vista
 *
 * @param $render
 * @param array $data
 * @return string
 */
function view($render, $data = [])
{
    global $gb_view;
    return $gb_view->render($render, $data);
}


//--------------------------------------------------------------------

/**
 * Instancia de RouteHelper
 *
 * @return array Colección de rutas
 */
function get_routes()
{
    global $route_helper;
    return $route_helper->getRoutes();
}

//--------------------------------------------------------------------

/**
 * Trae una ruta según sea su nombre
 *
 * @param $route
 * @param mixed ...$params
 * @return string
 */
function route(string $route, ...$params)
{
    global $route_helper;
    return $route_helper->getRoute($route, $params);
}

//--------------------------------------------------------------------

/**
 * Devuelve la URL actual
 *
 * @return string
 */
function current_uri()
{
    global $route_helper;
    return $route_helper->current_uri();
}

//--------------------------------------------------------------------

/**
 * Path de la URL actual
 *
 * @return string
 */
function current_path()
{
    global $route_helper;
    return $route_helper->current_path();
}

//--------------------------------------------------------------------

/**
 * Trae un input hidden para poner un método
 *
 * @param string $method
 * @return string
 */
function method($method = 'POST')
{
    return viewHelper('method', $method);
}

//--------------------------------------------------------------------

/**
 * Redirecting a una URL
 *
 * @param $to
 * @param int $status
 * @return mixed
 */
function redirect($to, $status = 302)
{
    global $route_helper;
    return $route_helper->redirect($to, $status);
}

//--------------------------------------------------------------------

/**
 * Go back
 *
 * @param int $status
 * @param bool $optional
 * @return mixed
 */
function back($status = 308, $optional = false)
{
    global $route_helper;
    return $route_helper->back($status, $optional);
}

//--------------------------------------------------------------------

/**
 * Crea un titulo dinámico
 *
 * @param $title - Titulo a mostrar
 * @param string $separator - Separador que se usara
 * @param bool $showTitle - Mostrar el titulo principal de la aplicación
 * @return string
 */
function title($title, $separator = ' | ', $showTitle = true)
{
    return viewHelper('title', $title, $separator, $showTitle);
}

//--------------------------------------------------------------------

/**
 * Devuelve la hora actual, en formato universal
 *
 * @return false|string
 */
function now()
{
    return \Core\Helper\SupportHelper::now();
}

//--------------------------------------------------------------------

/**
 * Crea un modelo y lo devuelve con la tabla para la acción
 * Por defecto, se activa el uso de los Timestamps y de los SoftDeletes
 *
 * @param $table
 * @return \Core\Foundation\Model
 */
function fast_model($table)
{
    $fast_model = new \Core\Foundation\Model();
    $fast_model->setTable($table);
    $fast_model->setUseSoftDeletes(true);
    $fast_model->setUseTimestamps(true);
    return $fast_model;
}

//--------------------------------------------------------------------

/**
 * Ejecuta una consulta rápida sobre una base de dato especifica
 *
 * @param $table
 * @param false $softDeletes
 * @return array|bool|string
 */
function fast_all($table, $softDeletes = false)
{
    return fast_model($table)->all($softDeletes)->exec();
}

//--------------------------------------------------------------------

/**
 * Busca en una tabla solo por el ID.
 * La tabla debe tener un Primary Key de 'ID'
 *
 * @param $table
 * @param $id
 * @return array|mixed
 * @throws Exception
 */
function fast_find($table, $id)
{
    return fast_model($table)->find($id);
}

//--------------------------------------------------------------------

/**
 * Devuelve el csrf actual de la aplicación
 *
 * @return false|mixed|string
 */
function csrf()
{
    return viewHelper('csrf');
}

//--------------------------------------------------------------------

/**
 * Devuelve el csrf en un input hidden
 *
 * @return false|mixed|string
 */
function csrf_input()
{
    return viewHelper('csrf_input');
}