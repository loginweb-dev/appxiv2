<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Resizable;
class Tipo extends Model
{
    use SoftDeletes;
    use Resizable;
	public function negocios()
    {
        return $this->hasMany(Negocio::class);
    }
    
}
