<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Extracarrito extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'extra_id',
        'precio',
        'cantidad',
        'total',
        'carrito_id',
        'producto_id',
    ];
}
