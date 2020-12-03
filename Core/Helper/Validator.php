<?php


namespace Core\Helper;


class Validator
{
    /**
     * Validaciones para los emails
     *
     * @param $email
     * @return mixed false en caso de ser un email invalido, true es valido
     */
    public static function email($email)
    {
        $email = trim($email, ' '); # Eliminar espacios en blanco
        $email = filter_var($email, FILTER_SANITIZE_EMAIL); # Sanea el Email
        return filter_var($email, FILTER_VALIDATE_EMAIL); # Validación
    }


    /**
     * Validaciones para las url
     * Devuelve 'false' en caso de ser invalido la url
     * Devuelve la url en caso de pasar la validación
     *
     * @param $url
     * @return mixed
     */
    public static function url($url)
    {
        $url = trim($url, ' ');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL);
    }


    /**
     * Valida las contraseñas y la intensidad de las mismas.
     * Seguridad nivel:
     *  - 0 = Validara que haya algo escrito y espacios internos.
     *  - 1 = Valida la longitud sea de min:8 y max:16.
     *  - 2 = La contraseña debe tener minúsculas y mayúsculas.
     *  - 3 = La contraseña debe tener Números.
     *  - 4 = La contraseña debe tener caracteres especiales: !@#$%^&*()\-_=+{};:,<.>
     *
     * Si se coloca '4' las validaciones 3, 2, 1 y 0 serán necesarias,
     * lo mismo si se coloca '3' requerirá las validaciones 2, 1 y 0,
     * y asi sucesivamente.
     *
     * Devolverá el numero donde falla la validación, y un 'true' si las pasa todas.
     *
     * @param string $password - String a validar
     * @param int $level       - Nivel de validación
     * @return int|bool        - Numero donde falla la validación. 'true' en caso de pasar.
     */
    public static function password(string $password, int $level = 0)
    {
        $password = trim($password, ' ');

        // Leven 0 de validación, esto sera automático
        // Sin importar el parámetro '$level'
        // También valida que no hayan espacios internos
        if ( $password === '' && preg_match('/\s/', $password) ) return 0;

        // Valida la longitud
        if ( $level >= 1 && (strlen($password) < 8 || strlen($password) > 16) ) return 1;

        // Valida la existencia de mayúsculas y minúsculas
        if ( $level >= 2 && (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) ) return 2;

        // Valida la existencia de números
        if ( $level >= 3 && !preg_match('/[0-9]/', $password) ) return 3;

        // Valida los caracteres especiales
        if ( $level >= 4 && !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password) ) return 4;

        return true;
    }


    /**
     * Valida la igualdad de dos campos
     *
     * @param $field1
     * @param $field2
     * @return bool - 'true' en caso de ser iguales. 'false' en caso contrario
     */
    public static function twoFields($field1, $field2)
    {
        if ( $field1 === $field2 ) return true;
        return false;
    }
}













