<?php


namespace Core\Routing;


class Csrf
{
    use \App\Config\Csrf;


    /**
     * Almacena el momento de la expiración del token
     *
     * @var
     */
    private $token_expiration;

    /**
     * Almacena el Token
     *
     * @var
     */
    private $token;



    /**
     * Csrf constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if ( !$this->getUseCsrf() ) {
            unset($_SESSION['csrf_token']);
            return $this;
        }

        global $gb_request;

        // Si no existe un token, se va generar uno
        if ( !isset($_SESSION['csrf_token']) ){
            try{
                $this->generate();
                $_SESSION['csrf_token'] = [
                    '_token'      => $this->token,
                    '_expiration' => $this->token_expiration
                ];
            }catch (\Exception $e) {
                $gb_request->warningApp[] = 'Error al generar Token CSRF de seguridad';
                $gb_request->warningApp[] = $e->getMessage();
            }
            return $this;
        }

        $this->token            = $_SESSION['csrf_token']['_token'];
        $this->token_expiration = $_SESSION['csrf_token']['_expiration'];

        return $this;
    }


    /**
     * Genera un Token nuevo
     * @throws \Exception
     */
    private function generate()
    {
        $this->length = $this->length < 4 || $this->length > 100 ? 32 : $this->length;

        if ( function_exists('random_bytes') ) {
            $this->token = bin2hex( random_bytes($this->length) );
        }
        elseif ( function_exists('mcrypt_create_iv') ){
            $this->token = bin2hex( mcrypt_create_iv($this->length, MCRYPT_DEV_URANDOM) );
        }
        else {
            $this->token = bin2hex( openssl_random_pseudo_bytes($this->length) );
        }

        $this->token_expiration = time() + ($this->expiration_time * 60);
        return $this;
    }


    /**
     * Valida el Token
     * Devuelve 'true' en caso de ser valido o si el usuario no quiere validación.
     * 'false' en caso contrario
     *
     * @param $csrf - Token a validar
     * @return bool
     */
    public static function validate($csrf): bool
    {
        $self = new self();

        return $self->validateCsrfToken($csrf);
    }


    /**
     * Valida el Token
     * Devuelve 'true' en caso de ser valido o si el usuario no quiere validación.
     * 'false' en caso contrario
     *
     * @param $csrf - Token a validar
     * @return bool
     */
    public function validateCsrfToken($csrf): bool
    {
        // Si el usuario no quiere usar CSRF la validación siempre devolverá true
        if ( !$this->getUseCsrf() ) {
            return true;
        }

        // Tiempo de vida del token
        if ( $this->getTokenExpiration() > time() ) {
            return false;
        }

        // Validar el propio token con el pasado
        if ( (string)$this->getToken() !== (string)$csrf ) {
            return false;
        }

        return true;
    }


    /**
     * Obtiene el token actual
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * Obtiene el momento cuando se expira el token
     *
     * @return mixed
     */
    public function getTokenExpiration()
    {
        return $this->token_expiration;
    }

    /**
     * Indica al sistema si se usara el token
     *
     * @return bool
     */
    public function getUseCsrf(): bool
    {
        return $this->use_csrf;
    }

    /**
     * Métodos que requieren la validación CSRF
     *
     * @return array
     */
    public function getMethodsToVerify(): array
    {
        return $this->methods_to_verify;
    }


    /**
     * Método estático para obtener el Token
     *
     * @return mixed
     */
    public static function get_token()
    {
        return $_SESSION['csrf_token']['_token'];
    }
}