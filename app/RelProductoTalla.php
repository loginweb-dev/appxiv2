<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class RelProductoTalla extends Model
{
    
	public function tallas()
    {
        return $this->belongsTo(Talla::class, 'talla_id');
    }
}
