<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Extrapedido extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'extra_id',
        'precio',
        'cantidad',
        'total',
        'pedido_id',
        'pedido_detalle_id',
    ];

    public function extra()
    {
        return $this->belongsTo(Extraproducto::class, 'extra_id');
    }
}
