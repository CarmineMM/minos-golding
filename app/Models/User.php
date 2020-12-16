<?php


namespace App\Models;

use Core\Foundation\Model;

class User extends Model
{
    /**
     * Únicos campos para el llenado de información
     *
     * @var string[]
     */
    protected $allowed = [
        'email', 'password'
    ];

    // protected $visible = [
    //     'email'
    // ];

    protected $useTimestamps = true;

    // protected $useSoftDeletes = true;
}