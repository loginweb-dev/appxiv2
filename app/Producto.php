<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Resizable;
use Carbon\Carbon;
// use Cviebrock\EloquentSluggable\Sluggable;
class Producto extends Model
{
   
	use SoftDeletes;
    use Resizable;

    protected $fillable = [
        'nombre',
        'slug',
        'categoria_id',
        'negocio_id',
        'precio',
        'laboratorio_id',
        'titulo',
        'etiqueta',
        'extra',
        'ecommerce',
        'created_at',
        'ordenes'
    ];
    

    protected $appends=['published', 'fecha'];
    public function getPublishedAttribute(){
      return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
    }
    public function getFechaAttribute(){
      return date('Y-m-d', strtotime($this->attributes['created_at']));
    }

	public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'laboratorio_id');
    }
    public function precios()
    {
        return $this->hasMany(RelProductoPrecio::class);
    }
    public function tallas()
    {
        return $this->hasMany(RelProductoTalla::class);
    }
    // public function sluggable(): array
    // {
    //     return [
    //         'slug' => [
    //             'source' => 'nombre'
    //         ]
    //     ];
    // }
    // public function getSlugOptions() : SlugOptions
    // {
    //     return SlugOptions::create()
    //         ->generateSlugsFrom('nombre')
    //         ->saveSlugsTo('slug');
    // }

}
