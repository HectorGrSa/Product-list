<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//Declaramos la tabla "SearchURL", este se utilizara para registrar todas las busquedas que hace un usuario
class Searchurl extends Model
{
    protected $fillable = ['searchURL'];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';
}
