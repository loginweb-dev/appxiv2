<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class Comentario extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pedido_id',
        'description'
       
    ];
    
}
