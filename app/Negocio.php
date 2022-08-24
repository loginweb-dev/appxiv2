<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use TCG\Voyager\Traits\Resizable;
class Negocio extends Model
{
    
	use SoftDeletes;
  use Resizable;

    protected $appends=['published', 'fecha'];
    public function getPublishedAttribute(){
      return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
    }
    public function getFechaAttribute(){
      return date('Y-m-d', strtotime($this->attributes['created_at']));
    }    
	  public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    public function poblacion()
    {
        return $this->belongsTo(Poblacione::class, 'poblacion_id');
    }
    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }
    public function extras()
    {
        return $this->hasMany(Extraproducto::class);
    }

    public function horarios()
    {
      return $this->hasMany(Horario::class);
    }
    public function user()
    {
        // return $this->hasOne(\TCG\Voyager\Models\User::class, 'id');
        return $this->belongsTo(\TCG\Voyager\Models\User::class, 'user_id');
    }
}
