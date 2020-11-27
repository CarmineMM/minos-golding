<?php


namespace Core\Foundation;

use App\Config\App;
use App\Config\FileSystem;
use Core\Helper\SupportHelper;

class Config
{
    use App, FileSystem;

    /**
     * Testea y configura cualquier parámetro presente el archivo 'App'
     */
    protected function loadFileApp()
    {
        global $gb_request;
        $error_reporting = 1;

        // Valida el entorno de la aplicación
        if ( $this->environment !== 'local' && $this->environment !== 'production' )
        {
            $gb_request->warningApp[] = 'El entorno de la aplicación solo puede ser "local" o "production". Verificar archivo: '.G_PATH.'app\Config\App.php';
        }
        // Informa al Request el entorno de desarrollo
        else $gb_request->environment = $this->environment;

        if ( $this->environment === 'production' )
        {
            error_reporting(E_ALL);
            $error_reporting = 0;
        }

        // Establece la zona horaria
        if ( $this->timezone !== '' ) {
            date_default_timezone_set($this->timezone);
        }

        // Establece el idioma de la aplicación
        if ( $this->locate !== '' ) {
            setlocale(LC_ALL, $this->locate);
        }

        // Visualización de errores
        ini_set('display_errors', $error_reporting);
        ini_set('display_startup_error', $error_reporting);
    }
}