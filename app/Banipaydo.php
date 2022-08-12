<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Banipaydo extends Model
{
    use SoftDeletes;
	protected $fillable = [
		'pedido_id',
        'externalId',
        'identifier',
        'image',
        'id_banipay'
	];
}
