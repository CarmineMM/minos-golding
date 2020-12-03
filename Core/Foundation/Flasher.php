<?php


namespace Core\Foundation;


/**
 * Establece y obtiene mensajes rápidos en la aplicación
 *
 * Class Flasher
 *
 * @author Carmine Maggio
 * @package Core\Foundation
 * @version 1.1
 */
class Flasher
{
    /**
     * Tipos de notificaciones validas.
     * --Recomendado no cambiar--
     *
     * @var string[]
     */
    private $valid_types = [
        'n' => '_notification', # Notificaciones regulares para el usuario
        'w' => '_warning',      # Advertencias importantes
        'e' => '_errors'        # Errores directos generados
    ];


    /**
     * Llave de acceso a las notificaciones
     *
     * @var string[]
     */
    private $key_access = '_flasher';


    /**
     * Establece un mensaje flash
     *
     * @param $message - Mensaje a guardar
     * @param string $type - Tipo de mensaje
     * @param $key - Llave de acceso, Opcional
     * @return bool           - 'true' Si se crea satisfactoriamente el mensaje
     */
    private function setMessage($message, string $type, $key = false)
    {
        if ( $message === '' || $type === '' ) return false;

        if ( !$key ) $_SESSION[$this->key_access][$type][] = $message;
        // Agregar el mensaje al sessions
        else {

            if ( isset($_SESSION[$this->key_access][$type][$key]) ) {
                if ( is_string($_SESSION[$this->key_access][$type][$key]) ) {
                    $last_message = $_SESSION[$this->key_access][$type][$key];
                    unset($_SESSION[$this->key_access][$type][$key]);
                    $_SESSION[$this->key_access][$type][$key][] = $last_message;
                }
                $_SESSION[$this->key_access][$type][$key][] = $message;
            }
            else $_SESSION[$this->key_access][$type][$key] = $message;
        }

        return true;
    }


    /**
     * Establece un mensaje de tipo notificación
     *
     * @param $message  - Mensaje a guardar, se pueden guardar multiples si se manda como arreglo
     * @param bool $key - Llave de acceso
     * @return bool      'true' Si se crea satisfactoriamente el mensaje
     */
    public function setNotification($message, $key = false)
    {
        $complete = [];

        if ( is_string($message) )
            return $this->setMessage($message, $this->valid_types['n'], $key);

        elseif ( is_array($message) ){
            foreach ($message as $key => $m){
                $complete[] = $this->setMessage($m, $this->valid_types['n'], $key);
            }
        }

        return !array_search(false, $complete);
    }

    /**
     * Establece un mensaje de tipo advertencia
     *
     * @param $message  - Mensaje a guardar, se pueden guardar multiples si se manda como arreglo
     * @param bool $key - Llave de acceso
     * @return bool      'true' Si se crea satisfactoriamente el mensaje
     */
    public function setWarning($message, $key = false)
    {
        $complete = [];

        if ( is_string($message) )
            return $this->setMessage($message, $this->valid_types['w'], $key);

        elseif ( is_array($message) ){
            foreach ($message as $m){
                $complete[] = $this->setMessage($m, $this->valid_types['w'], $key);
            }
        }

        return !array_search(false, $complete);
    }

    /**
     * Establece un mensaje de tipo error.
     *
     * @param $message  - Mensaje a guardar, se pueden guardar multiples si se manda como arreglo
     * @param bool $key - Llave de acceso
     * @return bool      'true' Si se crea satisfactoriamente el mensaje
     */
    public function setError($message, $key = false)
    {
        $complete = [];

        if ( is_string($message) )
            return $this->setMessage($message, $this->valid_types['e'], $key);

        elseif ( is_array($message) ){
            foreach ($message as $key => $m){
               $complete[] = $this->setMessage($m, $this->valid_types['e'], $key);
            }
        }

        return !array_search(false, $complete);
    }


    /**
     * Obtiene un mensaje según su tipo
     *
     * @param string $type  - Tipo de mensaje.
     * @param bool $key     - Llave de acceso.
     * @return string|array - Devuelve un arreglo con lo encontrado, si no encuentra nada devuelve el arreglo vació.
     */
    private function getMessage(string $type, $key = false)
    {
        if (
            is_string($key) &&
            !isset( $_SESSION[$this->key_access][ $this->valid_types[$type] ][$key] )
        ) return '';

        if (
            !isset($this->valid_types[$type])
            || !isset($_SESSION[$this->key_access][$this->valid_types[$type]])
        ) return [];


        if ( !$key )
            return $_SESSION[$this->key_access][$this->valid_types[$type]];

        return $_SESSION[$this->key_access][ $this->valid_types[$type] ][$key];
    }

    /**
     * Obtiene las notificaciones
     *
     * @param bool $key
     * @return array|string
     */
    public function getNotification($key = false)
    {
        return $this->getMessage('n', $key );
    }

    /**
     * Obtiene las advertencias
     *
     * @param bool $key
     * @return array|string
     */
    public function getWarning($key = false)
    {
        return $this->getMessage( 'w', $key );
    }

    /**
     * Obtiene los errores
     *
     * @param bool $key
     * @return array|string
     */
    public function getError($key = false)
    {
        return $this->getMessage( 'e', $key );
    }

    /**
     * Destruye todos los mensajes flashers
     */
    public static function destroyerFlasherMessages()
    {
        global $gb_request;
        // Eliminara a excepción que hayan rutas de redirect
        if ( $gb_request->status !== 302 &&  $gb_request->status !== 308 ) {
            $self = new self();
            unset($_SESSION[$self->key_access]);
        }
    }
}