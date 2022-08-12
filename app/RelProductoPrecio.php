<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Relations\Pivot;

class RelProductoPrecio extends Model
{
    
    public function precios()
    {
        return $this->belongsTo(Precio::class, 'precio_id');
    }
}
