<?php

namespace App\Imports;

use App\Categoria;
use Maatwebsite\Excel\Concerns\ToModel;

class CategoriasImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Categoria([
            'nombre'     => $row[0],
            'tipo_id'    => $row[1]
        ]);
    }
}
