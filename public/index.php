<?php

/**
 * Minos Golding
 * Version: 0.3
 *
 * Version Minima de PHP: 7.2
 *
 * (c) Carmine Maggio <carminemaggiom@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Check PHP Version
$minPHPVersion = 7.2;
if ( phpversion() < $minPHPVersion )
{
    die("Your PHP version must be {$minPHPVersion} or higher to run <b>Minos Golding</b>. Current version: " . phpversion());
}
unset($minPHPVersion);

// Separador de carpetas
define('DG', DIRECTORY_SEPARATOR);

// Path hacia la capeta publica
define('PUBLIC_PATH', dirname(__FILE__));

$array = explode(DG, PUBLIC_PATH);
array_pop($array);

// Define la ruta de carpetas de la raíz
define('G_PATH', implode(DG, $array).DG); unset($array);

// Incluir Composer
require_once G_PATH . 'vendor/autoload.php';

// Instancia inicial
$application = new \Core\Foundation\Application();

// Carga los archivos de configuración
$application->loadFilesConfigs();

// Ejecuta la aplicación
$application->run();

// Libera memoria
unset($application);