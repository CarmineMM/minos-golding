<?php

namespace App\Routes;

use App\Config\Namespaces;

/**
 * Registro de rutas en la aplicación
 *
 * Class Web
 * @package App\Routes
 */
class Web extends \Core\Routing\Routing
{
    use Namespaces;

    /**
     * Define el controlador Home
     * A donde caerían los métodos de las rutas implícitas
     *
     * @var string
     */
    public $home_controller = 'Home';

    /**
     * Es el método de entrada iniciar de cualquier controlador
     *
     * @var string
     */
    public $method_index = 'index';

    /**
     * Definición de las rutas
     *
     * Web constructor.
     */
    public function __construct()
    {
        // $this->get('/', 'Home::index', 'index');
        $this->post('/subir', 'Home::archivo');
        $this->auth();
    }


    /**
     * Rutas para el Auth
     */
    public function auth()
    {
        $this->get( 'login', $this->auth_controller.'::login',  'login');
        $this->post('login', $this->auth_controller.'::loginIn','login.in');

        $this->get( 'register', $this->auth_controller.'::register',  'register');
        $this->post('register', $this->auth_controller.'::registerIn','register.in');
    }
}