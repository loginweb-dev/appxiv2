<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pedido extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'cliente_id',
        'pago_id',
        'mensajero_id',
        'chatbot_id',
        'descuento',
        'total',
        'ubicacion_id',
        'estado_id',
        'total_delivery',
        'negocios'
    ];

	protected $appends=['published', 'fecha'];
	public function getPublishedAttribute(){
		return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
	}
	public function getFechaAttribute(){
		return date('Y-m-d', strtotime($this->attributes['created_at']));
	}
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
    public function mensajero()
    {
        return $this->belongsTo(Mensajero::class, 'mensajero_id');
    }
    public function productos()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacione::class, 'ubicacion_id');
    }
    public function pasarela()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }
    public function comentario()
    {
        return $this->hasMany(Comentario::class);
    }
    public function extras()
    {
        return $this->hasMany(Extrapedido::class);
    }
    public function banipay()
    {
        return $this->hasOne(Banipay::class);
    }
    public function banipaydos()
    {
        return $this->hasOne(Banipaydo::class);
    }
}
