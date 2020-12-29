<?php 

namespace Core\Cli;

use Core\Helper\MainHelper;
use Exception;

/**
 * Consola de comandos ejecución rápida
 */
class Console extends ShowPrint
{
    /**
     * Comando a ejecutar
     *
     * @var string
     */
    private $command;


    /**
     * Puerto del servidor
     *
     * @var string
     */
    public $serve = 'localhost:8080';


    /**
     * Constructor Console
     */
    public function __construct() 
    {
        $this->command = $_SERVER['argv'][1] ?? false;
    }

    
    /**
     * Ejecutar el Console
     *
     * @return $this
     */
    public function executor()
    {
        if ( !$this->command || $this->command === 'list' ) return $this->list(); 

        switch ($this->command) {
            case 'help': return $this->help();

            case 'serve': return $this->serve();
            
            default:
                # code...
                break;
        }
    }


    /**
     * Ejecuta el servidor local
     *
     * @return void
     */
    public function serve($usHelp = false)
    {
        if ( !$usHelp ) {
            $this->color_light_cyan("Servidor iniciado en: http://{$this->serve}")->toPrint();
            exec("php -S {$this->serve} -t public");
            return;
        }
        
        $this->color_light_cyan("Descripción\n");
        $this->color_unset("   Inicia un servidor.\n\n");

        $this->color_light_cyan("Forma de uso:\n");
        $this->color_light_green('  php gd serve');

        $this->color_light_cyan("\nArgumentos:\n");
        $this->color_red('  Sin Argumentos');
    }


    /**
     * lista de comandos disponibles.
     *
     * @return void
     */
    private function list()
    {       
        $this->color_light_cyan(self::frameworkName . "\n");
        
        # List
        $this->color_light_green('  list');
        $this->color_unset("\t\tMuestra la lista de comandos.\n");

        # Help
        $this->color_light_green('  help');
        $this->color_unset("\t\tMuestra información básica.\n");

        # Serve
        $this->color_light_green('  serve');
        $this->color_unset("\t\tHabilita el servidor de PHP.\n");


        return $this->toPrint();
    }


    /**
     * Información Básica
     *
     * @return void
     */
    private function help()
    {
        $argument = $_SERVER['argv'][2] ?? false;

        // Información de un comando especifico
        if ( $argument ) {
            // Comprobar que el comando esta disponible
            if ( method_exists($this, $argument) ) {
                $this->$argument(true);
            }       

            // El comando no existe
            else  {
                $this->bg_red()->color_white("No se encontró un resultado para: '{$argument}'");
                $this->color_unset("\n  Intenta con otro comando...");
            }
            return $this->toPrint();
        }

        // Descripción general de los comandos
        $this->color_light_cyan("Descripción\n");
        $this->color_unset("   Información básica referente al uso de la consola.\n\n");

        $this->color_light_cyan("Forma de uso:\n");
        $this->color_light_green('  php gd help');
        $this->color_unset(" [command_to_use]\n\n");

        $this->color_light_cyan("Argumentos:\n");
        $this->color_light_green('  command_to_use');
        $this->color_unset("   Nombre del comando, por defecto [command_to_use = 'help']\n\n");


        // Imprimir la lista de comandos
        $this->color_unset("A continuación la lista de comandos disponibles desde consola...\n\n");
        $this->list();
    }
}