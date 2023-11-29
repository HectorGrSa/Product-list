<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//Declaramos la tabla "Company", este se utilizara para poder registrar todas las cetgorias con las que podemos trabajar
class Category extends Model
{
    protected $fillable = ['category'];
}