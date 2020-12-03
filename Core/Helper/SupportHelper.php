<?php


namespace Core\Helper;


class SupportHelper
{
    /**
     * Convierte un Array en un Objeto
     *
     * @param $array
     * @return Object
     */
    public static function to_object($array)
    {
        return json_decode( json_encode($array) );
    }

    /**
     * Función de depuración
     *
     * @param $print
     * @param bool $vardump
     * @return void
     */
    public static function showDev($print, $vardump = true) {
        echo '<pre style="background-color: #eee; padding: 8px; color: #51172d; overflow-x: scroll; text-align: left">';
        ($vardump) ? var_dump($print) : print_r($print);
        echo '</pre>';
    }

    /**
     * Hora actual del sistema en formato universal
     *
     * @return false|string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }
}