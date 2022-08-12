<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Cliente extends Model
{
  use SoftDeletes;
	protected $fillable = [
        'nombre' ,
        'telefono',
        'chatbot_id',
        'direccion',
        'latitud',
        'longitud',
        'chatbot_id',
		    'chatbot_status',
        'poblacion_id'
    ];

    protected $appends=['published', 'fecha'];
    public function getPublishedAttribute(){
      return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
    }
    public function getFechaAttribute(){
      return date('Y-m-d', strtotime($this->attributes['created_at']));
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
    public function ubicaciones()
    {
        return $this->hasMany(Ubicacione::class);
    }
    public function localidad()
    {
        return $this->belongsTo(Poblacione::class, 'poblacion_id');
    }
}
