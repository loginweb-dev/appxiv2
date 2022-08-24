<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        Voyager::addAction(\App\Actions\PedidoDetalle::class);
        // Voyager::addAction(\App\Actions\Comentario::class);
        Voyager::addAction(\App\Actions\ExtraPedido::class);
        Voyager::addAction(\App\Actions\ProductosNegocio::class);
        Voyager::addAction(\App\Actions\HorarioClone::class);



    }
}
