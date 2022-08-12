<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ExtraPedido extends AbstractAction
{
    public function getTitle()
    {
        return 'Extras';
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
            'class' => 'btn btn-sm btn-dark pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.extrapedidos.index', ['key' => 'pedido_detalle_id', 'filter' => 'equals', 's' => $this->data->{$this->data->getKeyName()} ]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'pedido-detalles';
    }
}