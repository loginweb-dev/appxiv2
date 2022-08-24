<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ProductosNegocio extends AbstractAction
{
    public function getTitle()
    {
        return 'Productos';
    }

    public function getIcon()
    {
        return 'voyager-helm';
    }

    public function getPolicy()
    {
        return 'browse';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.productos.index', ['key' => 'negocio_id', 'filter' => 'equals', 's' => $this->data->{$this->data->getKeyName()} ]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'negocios';
    }
}