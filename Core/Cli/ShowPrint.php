<?php 

namespace Core\Cli;

use Core\Foundation\Kernel;

/**
 * Consola de comandos ejecución rápida
 */
class ShowPrint extends Kernel
{
    /**
     * Version del CLI
     *
     * @var string
     */
    private $versionCli = '1.0';


    /**
     * Colores de letra
     *
     * @var array
     */
    private $colors = [
        'black'         =>	"0;30",
        'gray'          =>	"1;30",
        'light gray'    =>	"0;37",
        'red'           =>	"0;31",
        'light red'     =>	"1;31",
        'green'         =>	"0;32",
        'light green'   =>	"1;32",
        'coffee'        =>	"0;33",
        'yellow'        =>	"1;33",
        'blue'          =>	"0;34",
        'light blue'    =>	"1;34",
        'magenta'       =>	"0;35",
        'light magenta' =>	"1;35",
        'cyan'          =>	"0;36",
        'light cyan'    =>	"1;36",
        'white'         =>	"1;37"
    ];


    /**
     * Colores de fondo
     *
     * @var array
     */
    private $background = [
        'black'      => "40",
        'red'        => "41",
        'green'      => "42",
        'yellow'     => "43",
        'blue'       => "44",
        'Magenta'    => "45",
        'Cyan'       => "46",
        'light gray' =>	"47"
    ];


    /**
     * Sentencia a imprimir
     *
     * @var string
     */
    private $sentence_to_print = '';


    /**
     * Preparación de los colores
     *
     * @var string
     */
    private $colorized = "\e[";
    private $color = "";
    private $bg    = "";
    private $resetAllColors = "\e[0m";


    /**
     * Devuelve el formato a imprimir con su respectivo color.
     * Si se le pasa una sentencia construirá el texto completo.
     *
     * @param string $color
     * @param string $sentence
     * @return void
     */
    private function print_color( $color = 'white', $sentence = '' )
    {
        $this->color = $this->colors[$color];
        return $sentence ? $this->text($sentence) : $this;
    }


    /**
     * Devuelve la sentencia a imprimir con el color de fondo.
     * Si se le pasa una sentencia construirá el texto completo.
     *
     * @param [type] $bg
     * @param string $sentence
     * @return void
     */
    private function print_bg( $bg = 'red', $sentence = '' )
    {
        $this->bg = $this->background[$bg];
        return $sentence ? $this->text($sentence) : $this;
    }


    /**
     * Color de texto rojo.
     *
     * @param string $sentence
     * @return $this
     */
    public function color_red( $sentence = '' )
    {
        return $this->print_color('red', $sentence);
    }


    /**
     * Color verde.
     *
     * @param string $sentence
     * @return $thiss\
     */
    public function color_green( $sentence = '' )
    {
        return $this->print_color('green', $sentence);        
    }

    /**
     * Color Magenta
     *
     * @param string $sentence
     * @return $this
     */
    public function color_magenta( $sentence = '' )
    {
        return $this->print_color('magenta', $sentence);        
    }

    /**
     * Color Verde claro
     *
     * @param string $sentence
     * @return $this
     */
    public function color_light_green( $sentence = '' )
    {
        return $this->print_color('light green', $sentence);        
    }

    /**
     * Color Cyan
     *
     * @param string $sentence
     * @return $this
     */
    public function color_cyan( $sentence = '' )
    {
        return $this->print_color('cyan', $sentence);        
    }

    /**
     * Color Cyan Claro
     *
     * @param string $sentence
     * @return $this
     */
    public function color_light_cyan( $sentence = '' )
    {
        return $this->print_color('light cyan', $sentence);        
    }


    /**
     * Color blanco
     *
     * @param string $sentence
     * @return void
     */
    public function color_white( $sentence = '' )
    {
        return $this->print_color( 'white', $sentence );
    }

    /**
     * Color blanco
     *
     * @param string $sentence
     * @return void
     */
    public function color_black( $sentence = '' )
    {
        return $this->print_color( 'black', $sentence );
    }


    /**
     * Color por defecto de la consola
     *
     * @param [type] $sentence
     * @return void
     */
    public function color_unset( $sentence )
    {
        $this->sentence_to_print .= "{$sentence}";
        return $this;
    }


    /**
     * Color de fondo verde
     *
     * @param string $sentence
     * @return $this
     */
    public function bg_green( $sentence = '' )
    {
        return $this->print_bg( 'green', $sentence );
    }


    /**
     *  Color de fondo Rojo
     *
     * @param string $sentence
     * @return void
     */
    public function bg_red( $sentence = '' )
    {
        return $this->print_bg( 'red', $sentence );
    }


    /**
     * Establece un texto
     *
     * @param string $text
     * @return $this
     */
    public function text( $text ) 
    {
        $print = $this->colorized;
        
        if ( $this->color && $this->bg ) $print .= $this->color . ';' . $this->bg;
        else if ( $this->color ) $print .= $this->color;
        else if ( $this->bg )    $print .= $this->bg;

        if ( $this->color || $this->bg  ) $print .= "m";

        // Resets
        $this->color = ""; 
        $this->bg = "";
        
        $this->sentence_to_print .= $print . $text . $this->resetAllColors;
        return $this;
    }


    /**
     * Echo de la aplicación
     *
     * @param string $toPrint
     * @return string
     */
    public function toPrint( string $toPrint = '' )
    {
        print("\n\e[0;32m". self::frameworkName . ' v'. self::frameworkVersion);
        echo "\e[0m - CLI version " . $this->versionCli;
        // echo "\nPor: \e[0;36m". self::frameworkDeveloper . "\e[0m\n\n";
        echo "\n\n";

        print( $toPrint ? $toPrint : $this->sentence_to_print );
        echo "\n\n";
        return '';
    }
}