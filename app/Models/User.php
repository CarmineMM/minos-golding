<?php


namespace App\Models;

use Core\Foundation\Model;

class User extends Model
{
    /**
     * Unicos campos para el llenado de información
     *
     * @var string[]
     */
    protected $allowed = [
        'email', 'password'
    ];

    protected $useTimestamps = true;
}