<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class HorarioClone extends AbstractAction
{
    public function getTitle()
    {
        return 'Clonar';
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
        return route('replicate.horario', [ 'id' => $this->data->{$this->data->getKeyName()} ]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'horarios';
    }
}