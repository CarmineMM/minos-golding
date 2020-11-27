<?php

namespace Core\Helper;

use App\Config\App;
use Core\Foundation\View;
use Core\Routing\Csrf;

class ViewHelper extends View
{
    use App;
    /**
     * Incluye un archivo de las vistas
     * Con un 'include', función de PHP
     *
     * @param $file - Archivo a incluir
     * @return false
     */
    public static function include_f($file)
    {
        $self = new self();
        return $self->include($file);
    }

    /**
     * Crea un titulo dinámico
     *
     * @param $title - Titulo a mostrar
     * @param string $separator - Separador que se usara
     * @param bool $showTitle - Mostrar el titulo principal de la aplicación
     * @return string
     */
    public function title($title, $separator = ' | ', $showTitle = true)
    {
        $app_title = $showTitle ? $this->app_name : '';

        if ( is_string($title) || is_numeric($title) )
        {
            return $title . $separator . $app_title;
        }
        return $this->app_name;
    }


    /**
     * Devuelve el Token
     *
     * @return mixed
     */
    public function csrf()
    {
        return Csrf::get_token();
    }


    /**
     * Devuelve el token en un input
     *
     * @return string
     */
    public function csrf_input()
    {
        return "<input type='hidden' name='_csrf' value='{$this->csrf()}' aria-hidden='true'>";
    }
}