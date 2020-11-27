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
 * @return array|bool
 */
function flasher($type, $action = 'get', $message = '')
{
    $f = new \Core\Foundation\Flasher();

    if ( $action === 'get' )
    {
        switch ($type){
            case 'n': return $f->getNotification();
            case 'w': return $f->getWarning();
            case 'e': return $f->getError();
        }
    }
    elseif ( $action === 'set' )
    {
        switch ($type){
            case 'n': return $f->setNotification($message);
            case 'w': return $f->setWarning($message);
            case 'e': return $f->setError($message);
        }
    }
    return false;
}

//--------------------------------------------------------------------

function viewHelper($type, ...$optional_param)
{
    $viewHelper = new \Core\Helper\ViewHelper();

    switch ($type){
        case 'title': return $viewHelper->title(...$optional_param);

        case 'csrf': return $viewHelper->csrf();

        case 'csrf_input': return $viewHelper->csrf_input();
    }

    return false;
}

//--------------------------------------------------------------------

/**
 * Devuelve las notificaciones flash
 *
 * @return array|bool
 */
function get_notifications()
{
    return flasher('n');
}

//--------------------------------------------------------------------

/**
 * Devuelve las advertencias flash
 *
 * @return array|bool
 */
function get_warnings()
{
    return flasher('w');
}

//--------------------------------------------------------------------

/**
 * Devuelve los errores flash
 *
 * @return array|bool
 */
function get_errors()
{
    return flasher('e');
}

//--------------------------------------------------------------------

/**
 * Establece una notificación en los mensajes flashers
 *
 * @param $message
 * @return array|bool
 */
function set_notifications($message)
{
    return flasher('n', 'set', $message);
}

//--------------------------------------------------------------------

/**
 * Establece una advertencia en los mensajes flashers
 *
 * @param $message
 * @return array|bool
 */
function set_warning($message)
{
    return flasher('w', 'set', $message);
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
 * @return \Core\Helper\RouteHelper
 */
function RouteHelper()
{
    return new \Core\Helper\RouteHelper();
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
    return RouteHelper()->redirect($to, $status);
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