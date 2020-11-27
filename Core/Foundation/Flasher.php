<?php


namespace Core\Foundation;


class Flasher
{
    /**
     * Tipos de notificaciones validas.
     * --Recomendado no cambiar--
     *
     * @var string[]
     */
    private $valid_types = [
        'n' => '_notification', // Notificaciones regulares para el usuario
        'w' => '_warning',      // Advertencias importantes
        'e' => '_errors'        // Errores directos generados
    ];


    /**
     * Llave de acceso a las notificaciones
     *
     * @var string[]
     */
    private $key_access = '_flasher';


    /**
     * Establece un mensaje flash
     * Devuelve un 'true' si se pudo crear el mensaje
     * Caso contrario un 'false'
     *
     * @param string $message
     * @param string $type
     * @return bool
     */
    private function setMessage(string $message, string $type)
    {
        if ( $message === '' || $type === '' ) return false;

        // Agregar el mensaje al sessions
        $_SESSION[$this->key_access][$type][] = $message;
        return true;
    }

    /**
     * Establece un mensaje de tipo notificación
     *
     * @param $message
     * @return bool
     */
    public function setNotification($message)
    {
        $complete = [];

        if ( is_string($message) )
            return $this->setMessage($message, $this->valid_types['n']);

        elseif ( is_array($message) ){
            foreach ($message as $m){
                $complete[] = $this->setMessage($m, $this->valid_types['n']);
            }
        }

        return array_search(false, $complete) === false ? false : true;
    }

    /**
     * Establece un mensaje de tipo advertencia
     *
     * @param $message
     * @return bool
     */
    public function setWarning($message)
    {
        $complete = [];

        if ( is_string($message) )
            return $this->setMessage($message, $this->valid_types['w']);

        elseif ( is_array($message) ){
            foreach ($message as $m){
                $complete[] = $this->setMessage($m, $this->valid_types['w']);
            }
        }

        return array_search(false, $complete) === false ? false : true;
    }

    /**
     * Establece un mensaje de tipo error
     *
     * @param $message
     * @return bool
     */
    public function setError($message)
    {
        $complete = [];

        if ( is_string($message) )
            return $this->setMessage($message, $this->valid_types['e']);

        elseif ( is_array($message) ){
            foreach ($message as $m){
                $complete[] = $this->setMessage($m, $this->valid_types['e']);
            }
        }

        return array_search(false, $complete) === false ? false : true;
    }


    /**
     * Obtiene un mensaje según su tipo
     *
     * @param string $type
     * @return array
     */
    private function getMessage(string $type)
    {
        if ( !isset($this->valid_types[$type]) ) return [];

        return isset($_SESSION[$this->key_access][$this->valid_types[$type]])
            ? $_SESSION[$this->key_access][$this->valid_types[$type]]
            : [];
    }

    /**
     * Obtiene las notificaciones
     *
     * @return array
     */
    public function getNotification()
    {
        return $this->getMessage('n' );
    }

    /**
     * Obtiene las advertencias
     *
     * @return array
     */
    public function getWarning()
    {
        return $this->getMessage( 'w' );
    }

    /**
     * Obtiene los errores
     *
     * @return array
     */
    public function getError()
    {
        return $this->getMessage( 'e' );
    }

    /**
     * Destruye todos los mensajes flashers
     */
    public static function destroyerFlasherMessages()
    {
        $self = new self();
        unset($_SESSION[$self->key_access]);
    }
}