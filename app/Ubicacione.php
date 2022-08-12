<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubicacione extends Model
{
    use SoftDeletes;
	protected $fillable = [
        'latitud',
        'longitud',
        'cliente_id',
        'detalles'
    ];
}
