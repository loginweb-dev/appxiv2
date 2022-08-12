<?php

namespace App\Imports;

use App\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
class ProductosImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Producto([
            'nombre'     => $row[0],
            'slug'     => Str::slug($row[0]),
            'categoria_id'    => $row[1],
            'negocio_id' => $row[2],
            'precio' => $row[3],
            'laboratorio_id' => $row[4],
            'titulo'=> $row[5],
            'etiqueta'=> $row[6],
            'extra'=> $row[7],
            'ecommerce' => 1
        ]);
    }
}
