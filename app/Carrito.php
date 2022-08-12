<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Carrito extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'producto_id' ,
        'producto_name',
        'chatbot_id',
        'precio',
        'cantidad',
        'negocio_id',
        'negocio_name',
    ];
    protected $appends=['published', 'fecha'];
	public function getPublishedAttribute(){
		return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
	}
	public function getFechaAttribute(){
		return date('Y-m-d', strtotime($this->attributes['created_at']));
	}
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }
    public function extras()
    {
        return $this->hasMany(Extracarrito::class);
    }
    
}
