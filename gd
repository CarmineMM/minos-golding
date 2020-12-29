<?php 

/**
 * Minos Golding
 *
 * Version Minima de PHP: 7.2
 *
 * (c) Carmine Maggio <carminemaggiom@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once './vendor/autoload.php';

// Instancia original
$console = new \Core\Cli\Console;

// Indica la URL donde se ejecutara el servidor
$console->serve = 'localhost:8081';

// Ejecutar el comando
$console->executor();

unset($console);