<?php

use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;
use App\Imports\CategoriasImport;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/app', function () {
//     return view('app');
// });

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/nosotros', function () {
    return view('welcome');
});

// Route::get('/marketplace', function () {
//     return view('markplace');
// });

Route::get('/privacidad', function () {
    return view('privacidad');
});

Route::get('mensajero/{chatbot_id}', function ($chatbot_id) {
    $mipedidos = App\Pedido::where('mensajero_id', $chatbot_id)->orderBy('created_at', 'desc')->get();
    return view('misviajes', compact('mipedidos'));
});

Route::get('cliente/{phone}', function ($phone) {
    $pedidos = App\Negocio::where('chatbot_id', $phone)->first();
    return view('mispedidos', compact('pedidos')); 
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('/mispedidos/{id}', function ($id) {
        $negocio= App\Negocio::find($id);
        return view('pedidos.mipedidos', compact('negocio'));
    })->name('mispedidos');
    Route::get('pedidos/midetalle/{id}', function ($id) {
        return view('detalles.midetalle', compact('id'));
    })->name('midetalle');
    Route::get('/pedidosmensajero/{id}', function ($id) {
        // $negocio= App\Negocio::find($id);
        return view('pedidos.pedidosmensajero', compact('id'));
    })->name('pedidosmensajero');
    Route::get('/imports/productos', function () {
        Excel::import(new ProductosImport, 'imports/products.xlsx');
        return 'TODO OK.';
    });
    Route::get('/imports/categorias', function () {
        Excel::import(new CategoriasImport, 'imports/categorys.xlsx');
        return 'TODO OK.';
    });
    Route::get('/miproducto/slugify', function () {
        foreach (App\Producto::all() as $value) {
             $value->slug = Str::slug($value->nombre);
             $value->save();
        }
        return true;
    });

    Route::get('/ajax/ventas/main', function () {
        return view('vendor.voyager.ventas.main');
    })->name('ajax.ventas.main');
    Route::get('/ajax/ventas/clientes', function () {
        return view('vendor.voyager.ventas.clientes');
    })->name('ajax.ventas.clientes');
    Route::get('/ajax/ventas/pago', function () {
        return view('vendor.voyager.ventas.pago');
    })->name('ajax.ventas.pago');
    //ajax
});

//NEGOCIOS PRODUCTOS
Route::get('{slug}', function ($slug) {
    $negocio = App\Negocio::where('slug', $slug)->with('productos', 'tipo')->first();
    // return $negocio;
    return view('negocio', compact('negocio')); 
})->name('negocio');

Route::get('{negocio}/{producto}', function ($negocio, $producto) {
    $negocio = App\Negocio::where('slug', $negocio)->first();
    $producto = App\Producto::where('negocio_id', $negocio->id)->where('slug', $producto)->with('tallas')->first();
    // return $producto;
    $productos = App\Producto::where('categoria_id', $producto->categoria_id)->where('ecommerce', 1)->where('negocio_id', $negocio->id)->limit(6)->get();
    return view('producto', compact('negocio', 'producto', 'productos')); 
})->name('producto');

