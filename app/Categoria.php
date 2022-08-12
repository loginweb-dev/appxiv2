<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use TCG\Voyager\Traits\Resizable;
class Categoria extends Model
{
	use SoftDeletes;
	use Resizable;

  protected $fillable = [
    'nombre',
    'tipo_id'
  ];

	protected $appends=['published', 'fecha'];
    public function getPublishedAttribute(){
      return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
    }
    public function getFechaAttribute(){
      return date('Y-m-d', strtotime($this->attributes['created_at']));
    }
    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

}
