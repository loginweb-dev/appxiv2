<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class PedidoDetalle extends Model
{
    protected $fillable = [
        'producto_id',
        'pedido_id',
        'precio',
        'cantidad',
        'producto_name',
        'total',
        'negocio_id',
        'negocio_name'
    ];

    protected $appends=['published'];
	public function getPublishedAttribute(){
		return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
	}
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
    public function extras()
    {
        return $this->hasMany(Extrapedido::class);
    }

}
