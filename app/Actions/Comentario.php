<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class Comentario extends AbstractAction
{
    public function getTitle()
    {
        return 'Comentario';
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
            'class' => 'btn btn-sm btn-warning pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.comentarios.index', ['key' => 'pedido_id', 'filter' => 'equals', 's' => $this->data->{$this->data->getKeyName()} ]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'pedidos';
    }
}