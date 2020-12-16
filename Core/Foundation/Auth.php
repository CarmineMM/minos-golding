<?php

namespace Core\Foundation;

use Core\Errors\HttpException;
use Core\Helper\RouteHelper;
use Core\Helper\Validator;
use Core\Routing\Request;

class Auth
{
    /**
     * Modelo para el llenado de información
     *
     * @var string
     */
    protected $model = '\App\Models\User';

    /**
     * Campos en la bse de datos para almacenar el Email
     *
     * @var string
     */
    protected $column_email = 'email';

    /**
     * Campos en la bse de datos para almacenar el Password
     *
     * @var string
     */
    protected $column_password = 'password';

    /**
     * Indica el nivel de seguridad que debe tener la contraseña
     *  - 0 = Validara que haya algo escrito y espacios internos.
     *  - 1 = Valida la longitud sea de min:8 y max:16.
     *  - 2 = La contraseña debe tener minúsculas y mayúsculas.
     *  - 3 = La contraseña debe tener Números.
     *  - 4 = La contraseña debe tener caracteres especiales: !@#$%^&*()\-_=+{};:,<.>
     *
     * @var int
     */
    protected $level_security_password = 3;

    /**
     * Vista del login
     *
     * @var string
     */
    protected $view_login = 'auth/login';

    /**
     * Vista para el registro
     *
     * @var string
     */
    protected $view_register = 'auth/register';

    /**
     * Ruta para hacer login
     *
     * @var string
     */
    protected $route_login = 'login';

    /**
     * Ruta para hacer registro
     *
     * @var string
     */
    protected $route_register = 'register';


    /**
     * Ruta después de hacer un login In
     *
     * @var string
     */
    protected $route_after_login = '/';


    /**
     * Ruta después del registro
     *
     * @var string
     */
    protected $route_after_register = 'login';


    /**
     * Instancia del helper de rutas
     *
     * @var
     */
    private $routeHelper;


    /**
     * Instancia de mensajes rápidos
     *
     * @var
     */
    private $flashers;

    /**
     * Auth constructor.
     */
    public function __construct()
    {
        $this->routeHelper = new RouteHelper();
        $this->flashers = new Flasher();
    }

    /**
     * Vista del Login
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        // Si el usuario ya ha iniciado sesión, mandar al inicio
        if ( Auth::validateAuth() ) return $this->routeHelper->redirect('/');

        global $gb_view;

        // Rendering la vista de login
        return $gb_view->render($this->view_login, compact($request));
    }


    /**
     * Iniciar session en la aplicación
     *
     * @param Request $request
     * @return array|Request
     */
    public function loginIn(Request $request)
    {
        if ( $request->method !== 'POST' ) return HttpException::method_not_allowed_405();

        $email = $this->column_email;
        $pass  = $this->column_password;

        if ( !isset($request->post->$email) || !isset($request->post->$pass) ) {
            $this->flashers->setWarning('Email o Contraseña no están definidas');
            return $this->routeHelper->redirect($this->route_login);
        }

        // Trae al usuario registrado
        $userModel = new $this->model;
        $user = $userModel->where('email', $request->post->$email)->limit(1)->exec();

        // Validación de email
        if ( count($user) === 0 ) {
            $this->flashers->setWarning('No existe con este email: '.$request->post->$email);
            return $this->routeHelper->redirect($this->route_login);
        }

        $user = $user[0]; # Ajusta al primer y único resultado

        // Validación de la contraseña
        if ( !password_verify($request->post->$pass, $user->$pass) ){
            $this->flashers->setWarning('Contraseña invalida');
            return $this->routeHelper->redirect($this->route_login);
        }

        unset($user->$pass); # Oculta el pass de la consulta
        // INICIA SESIÓN!!!
        $_SESSION['auth'] = $user;
        return $this->routeHelper->redirect($this->route_after_login);
    }


    /**
     * Vista para el registro
     *
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        if ( Auth::validateAuth() ) return $this->routeHelper->redirect('/');

        global $gb_view;

        // Rendering la vista de login
        return $gb_view->render($this->view_register, compact($request));
    }


    /**
     * Acción de registrar al usuario
     *
     * @param Request $request
     * @return array|Request
     */
    public function registerIn(Request $request)
    {
        // Valida si el usuario ya ha iniciado sesión
        if ( Auth::validateAuth() ) return $this->routeHelper->redirect('/');

        $email = $this->column_email;
        $pass  = $this->column_password;
        $error = false;

        // Validación de campos
        if ( !isset($request->post->$email) || !isset($request->post->$pass) ) {
            $this->flashers->setError("Los campos {$email} y {$pass} son requeridos", 'email_pass_required');
            $error = true;
        }

        // Valida la confirmación de contraseñas
        if (
            isset($request->post->password_confirm)
            && !Validator::twoFields($request->post->$pass, $request->post->password_confirm)
        ){
            $this->flashers->setWarning('Las contraseñas deben ser iguales', 'password_confirm');
            $error = true;
        }

        //Validación de la seguridad de la contraseña
        $security_pass = Validator::password($request->post->$pass, $this->level_security_password);
        if ( $security_pass !== true ) {
            if ( $this->level_security_password === 0 )
                $this->flashers->setWarning('Indique una contraseña', 'level_password');

            if ( $this->level_security_password >= 1 )
                $this->flashers->setWarning('La contraseña debe tener entre 8 y 16 caracteres', 'level_password');

            if ( $this->level_security_password >= 2 )
                $this->flashers->setWarning('La contraseña debe tener al menos una minúscula y una mayúscula', 'level_password');

            if ( $this->level_security_password >= 3 )
                $this->flashers->setWarning('La contraseña debe tener al menos 1 numero', 'level_password');

            if ( $this->level_security_password >= 4 )
                $this->flashers->setWarning('La contraseña debe tener al menos 1 carácter especial: !@#$%^&*()\-_=+{};:,<.>', 'level_password');

            $error = true;
        }

        // Validar el Email
        if ( !Validator::email($request->post->$email) ) {
            $this->flashers->setWarning('El email no es correcto', 'invalid_email');
            $error = true;
        }

        // Comprueba la existencia de errores
        if ( $error ) {
            // Reset de campos
            foreach ( $request->post as $key => $field ) {
                $this->flashers->setNotification($field, $key);
            }
            return $this->routeHelper->redirect( $this->route_register );
        }

        // Validaciones de email y de contraseñas pasadas
        $user = new $this->model;

        // Validar que ya exista el email registrado
       if( !$user->where($this->column_email, $email)->first() ) {
           $this->flashers->setWarning('Ya existe un usuario con el email: '.$request->post->$email, 'incomplete');
           return $this->routeHelper->redirect( $this->route_register );
       }

        // Hash la contraseña
        $request->post->$pass = password_hash($request->post->$pass, PASSWORD_DEFAULT);

        // Error durante el guardado
        if ( $user->create($request->post)->exec() === false ) {
            $this->flashers->setWarning('No se pudo guardar el registro en la base de datos', 'incomplete');
            return $this->routeHelper->redirect( $this->route_register );
        }

        // Registro completado
        $this->flashers->setNotification('Registro completado', 'register');
        return $this->routeHelper->redirect( $this->route_after_register );
    }


    /**
     * Valida si hay un usuario con una sesión activa
     *
     * @return bool true, si se esta autenticado, false en caso contrario
     */
    public static function validateAuth()
    {
        return isset($_SESSION['auth']);
    }

    /**
     * Devuelve la información del usuario
     *
     * @return false
     */
    public static function auth()
    {
        if ( !self::validateAuth() ) return false;
        return $_SESSION['auth'];
    }
}