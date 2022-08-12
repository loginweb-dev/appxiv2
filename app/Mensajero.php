<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
class Mensajero extends Model
{
    use SoftDeletes;


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
    public function localidad()
    {
        return $this->belongsTo(Poblacione::class, 'poblacion_id');
    }

    public function user()
    {
        // return $this->hasOne(\TCG\Voyager\Models\User::class, 'id');
        return $this->belongsTo(\TCG\Voyager\Models\User::class, 'user_id');
    }
}
