<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//Declaramos la tabla "Company", que este contendra los datos de los "supermercados" con los que podemos hacer busquedas.
class Company extends Model
{
    protected $fillable = ['name'];
}
