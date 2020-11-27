<?php


namespace Core\Foundation;

use Core\Routing\Request;

/**
 * Class Kernel
 * Instancia general del Framework
 *
 * @package Core\Foundation
 */
class Kernel extends Config
{
    /**
     * Nombre Oficial del Framework
     */
    const frameworkName = 'Minos Golding';


    /**
     * Version Oficial del Framework
     */
    const frameworkVersion = '0.3';


    /**
     * Nombre/s del desarrollador del framework
     *
     * @var string
     */
    const frameworkDeveloper = 'Carmine Maggio';

    /**
     * Archivo donde se almacenan las funciones globales
     *
     * @var string
     */
    private $file_functions = 'Core\functions\core_functions.php';

    /**
     * Revisa el tiempo de ejecución de la App
     *
     * @var
     */
    protected $start_time;


    /**
     * Kernel constructor.
     */
    public function __construct()
    {
        // Inicia la medición del tiempo
        $this->start_time = microtime(true);

        // Instancia la clase rendering en la aplicación
        global $gb_view;
        $gb_view = new View();

        // Request entrante
        global $gb_request;
        $gb_request = new Request();

        // Instancia la session
        if ( session_status() === PHP_SESSION_NONE ) session_start();

        // Constantes de entornos
        define('FRAMEWORK_NAME',      self::frameworkName);
        define('FRAMEWORK_VERSION',   self::frameworkVersion);
        define('FRAMEWORK_DEVELOPER', self::frameworkDeveloper);
    }


    /**
     * Carga los archivos de configuración
     */
    public function loadFilesConfigs()
    {
        global $gb_request;

        // Funciones globales
        $core_functions = G_PATH . $this->file_functions;
        if ( !is_file($core_functions) )
            $gb_request->warningApp[] = "Falla al cargar archivo de funciones globales: {$core_functions}";

        else require_once $core_functions;

        $this->loadFileApp();
    }
}