<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Estado;
use App\Negocio;
use App\Producto;
use App\Carrito;
use App\Pedido;
use App\PedidoDetalle;
use App\Pago;
use App\Cliente;
use App\Ubicacione;
use App\Mensajero;
use App\Comentario;
use App\Poblacione;
use App\Banipay;
use App\Banipaydo;
use App\Categoria;
use App\Tipo;
use App\Extraproducto;
use App\Extracarrito;
use App\Extrapedido;
use TCG\Voyager\Models\User;
use TCG\Voyager\Traits\Resizable;
use App\Precio;
use App\RelProductoPrecio;
use App\Laboratorio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// negocios
Route::get('negocios/{poblacion_id}', function($poblacion_id){
    return Negocio::where('estado', true)->where('poblacion_id', $poblacion_id)->with('productos')->get();
});
Route::get('negocio/{id}', function($id){
    return Negocio::where('id', $id)->with('productos')->first();
});
Route::get('minegocio/{phone}', function($phone){
    return Negocio::where('chatbot_id', $phone)->with('productos', 'poblacion')->first();
});

Route::post('negocio/modo/update', function (Request $request) {
    $cliente = Cliente::where('chatbot_id', $request->phone)->first();
    $cliente->modo = $request->modo;
    $cliente->save();
    $newcliente = Cliente::find($cliente->id);
    return $newcliente;
});

Route::get('bussiness', function(){
    return Negocio::all();
});

Route::get('productos', function(){
    return Producto::with('categoria', 'negocio')->get();
    // return Producto::all();
});

Route::get('producto/{id}', function($id){
    return Producto::where('id', $id)->where('ecommerce', 1)->with('categoria', 'negocio', 'precios', 'laboratorio')->first();
});

Route::get('precio/{id}', function($id){
    return Precio::find($id);
});

Route::post('producto/update/admin', function(Request $request){
    $producto = Producto::where('id', $request->producto_id)->with('categoria', 'negocio')->first();
    if ($producto->negocio->chatbot_id === $request->phone) {
        $producto->ecommerce = $producto->ecommerce ? 0 : 1;
        $producto->save();
        return $producto;
    }
    return $producto;
});

// SEARCH PRODUCTO
Route::post('chatbot/search', function (Request $request) {
    $result = Producto::where('nombre', 'like', '%'.$request->misearch.'%')->orWhere('detalle', 'like', '%'.$request->misearch.'%')->orderBy('nombre', 'desc')->with('categoria','negocio')->get();
    return $result;
});

Route::post('chatbot/cart/get', function (Request $request) {
    return Carrito::where('chatbot_id', $request->chatbot_id)->with('producto', 'extras')->get();
});

Route::get('cart/producto/get/{chatbot_id}', function ($chatbot_id) {
    return Carrito::orderBy('created_at', 'desc')->where('chatbot_id', $chatbot_id)->first();
});

//cart
Route::post('chatbot/cart/add', function (Request $request) {
    $item = Carrito::where('producto_id', $request->product_id)->where('chatbot_id', $request->chatbot_id)->first();
    $cart = Carrito::create([
        'producto_id' => $request->product_id,
        'producto_name' => $request->product_name,
        'chatbot_id' => $request->chatbot_id,
        'precio' => $request->precio,
        'cantidad' => $request->cantidad,
        'negocio_id' => $request->negocio_id,
        'negocio_name' =>$request->negocio_name
    ]);
    return $cart;
});

//add extras carrito
Route::post('carrito/add/extras', function(Request $request){
    $extras= Extracarrito::create([
        'extra_id'=>$request->extra_id,
        'precio'=>$request->precio,
        'cantidad'=>$request->cantidad,
        'total'=>$request->total,
        'carrito_id'=>$request->carrito_id,
        'producto_id'=>$request->producto_id,
    ]);
});

Route::get('carrito/negocios/{chatbot_id}', function($chatbot_id){
    return Carrito::where('chatbot_id', $chatbot_id)->count('negocio_id');
});

Route::get('pedido/negocios/{id}', function($id){
    return PedidoDetalle::where('pedido_id', $id)->with('negocio', 'extras')->get();
});

Route::get('pedido/carrito/negocios/{chatbot_id}', function($chatbot_id){
    $carrito=Carrito::where('chatbot_id', $chatbot_id)->with('negocio')->get();
    $vec1=[];
    $index=0;
    foreach ($carrito as $item) {
        $vec1[$index]=$item->negocio->id;
        $index+=1;
    }
    $cant=array_count_values($vec1);

    $contador=0;
    foreach ($cant as $item) {
        $contador+=1;
    }
    return $contador;
});

Route::get('chatbot/pasarelas/get',function(){
    return Pago::where('view', 'frontend')->get();

});

Route::get('carrito/{chatbot}', function ($chatbot) {
   return Carrito::where('chatbot_id', $chatbot)->with('producto', 'extras')->get();
});
// VENTAS
Route::post('pedido/save', function (Request $request) {
    $carts = Carrito::where('chatbot_id', $request->chatbot_id)->with('producto', 'extras')->get();
    $newpedido = Pedido::create([
        'cliente_id' => $request->cliente_id,
        'pago_id' => $request->pago_id,
        'mensajero_id'=>1,
        'estado_id'=>1,
        'chatbot_id' => $request->chatbot_id,
        'ubicacion_id' => $request->ubicacion_id,
        'descuento' => 0,
        'total'=>0,
    ]);

    //productos------
    $mitotal = 0;
    foreach ($carts as $item) {
        $detalle= PedidoDetalle::create([
            'producto_id' => $item->producto_id,
            'pedido_id' =>  $newpedido->id,
            'precio'=> $item->precio,
            'cantidad' => $item->cantidad,
            'producto_name' => $item->producto_name,
            'total' =>$item->precio * $item->cantidad,
            'negocio_name'=> $item->negocio_name,
            'negocio_id'=> $item->negocio_id
        ]);
        // $prod_order=Producto::find($item->producto_id);
        // $prod_order->ordenes+= $item->cantidad;
        // $prod_order->save();

        $mitotal += $item->precio * $item->cantidad;
        //extras---------
        foreach ($item->extras as $value) {
            Extrapedido::create([
                'extra_id' => $value->extra_id,
                'precio' =>  $value->precio,
                'cantidad'=> $value->cantidad,
                'total' => $value->total,
                'pedido_id' => $newpedido->id,
                'pedido_detalle_id' =>$detalle->id
            ]);
            $mitotal += $value->precio * $value->cantidad;
        }
    }
    $miupdate = Pedido::find($newpedido->id);
    $miupdate->total = $mitotal-($miupdate->descuento);
    $miupdate->save();

    // vaciando carrito
    $carritodel = Carrito::where('chatbot_id', $request->chatbot_id)->get();
    foreach ($carritodel as $item) {
        Extracarrito::where('carrito_id', $item->id)->delete();
    }
    Carrito::where('chatbot_id', $request->chatbot_id)->delete();

    return Pedido::find($newpedido->id);
});

Route::post('chatbot/cart/clean', function (Request $request) {
    $carrito= Carrito::where('chatbot_id', $request->chatbot_id)->get();
    foreach ($carrito as $item) {
        Extracarrito::where('carrito_id', $item->id)->delete();
    }
    return Carrito::where('chatbot_id', $request->chatbot_id)->delete();
});
Route::get('filtros/{negocio_id}', function ($negocio_id) {
    $result = Producto::where('negocio_id', $negocio_id )->where('ecommerce', 1)->with('categoria','negocio')->get();
    return $result;
});

Route::get('productos/negocio/rank/{negocio_id}', function($negocio_id){
    return Producto::where('negocio_id', $negocio_id)->where('ecommerce', 1)->with('categoria','negocio')->orderby('ordenes', 'desc')->limit(8)->get();
});

Route::get('negocio/get/id/{id}', function ($id) {
    $result = Negocio::where('id', $id)->where('estado', 1)->with('productos', 'tipo', 'poblacion')->first();
    return $result;
});

Route::get('negocio/productos/{id}', function ($id) {
    return Producto::where('negocio_id', $id)->where('ecommerce', 1)->get();
});

//nuevo cliente 
Route::get('cliente/{phone}', function ($phone) {
    $micliente =  Cliente::where('chatbot_id', $phone)->with('pedidos', 'ubicaciones', 'localidad')->first();
    if ($micliente) {
        return $micliente;
    } else {
        Cliente::create([
            'chatbot_id' => $phone
        ]);
        User::create([
            'email' => $phone.'@appxi.net',
            'name' => $phone,
            'password' => Hash::make('123456')
        ]);
        $newcliente =  Cliente::where('chatbot_id', $phone)->with('pedidos', 'ubicaciones', 'localidad', 'user')->first();
        return $newcliente;
    }
});

Route::post('cliente/update/nombre', function (Request $request) {
    $cliente = Cliente::find($request->id);
    $cliente->nombre = $request->nombre;
    $cliente->modo = 'cliente';
    $cliente->poblacion_id = 1;
    $cliente->save();
    $newcliente = Cliente::find($request->id);
    return $newcliente;
});

Route::post('cliente/update/localidad', function (Request $request) {
    $cliente = Cliente::find($request->id);
    $cliente->poblacion_id = $request->poblacion_id;
    $cliente->save();
    $newcliente = Cliente::find($request->id);
    return $newcliente;
});

Route::post('cliente/update/pin', function (Request $request){
    $cliente= Cliente::find($request->id);
    $cliente->pin=$request->newpassword;
    $cliente->save();
    return $cliente;
});

Route::post('cliente/modo/update', function (Request $request) {
    $cliente = Cliente::where('chatbot_id', $request->phone)->first();
    $cliente->modo = $request->modo;
    $cliente->save();
    $newcliente = Cliente::find($cliente->id);
    return $newcliente;
});

Route::post('cliente/modo/cliente', function (Request $request) {
    $cliente = Cliente::where('chatbot_id', $request->phone)->first();
    $cliente->modo = 'cliente';
    $cliente->save();
    $newcliente = Cliente::find($cliente->id);
    return $newcliente;
});

//ubicacion ---------
Route::post('ubicacion/save', function (Request $request) {
    $ubicacion = Ubicacione::create([
        'latitud' => $request->latitud, //falta
        'longitud' =>  $request->longitud,
        'cliente_id'=> $request->cliente_id
    ]);
    return $ubicacion;
});

Route::post('ubicacion/update', function (Request $request) {
    $ubicacion = Ubicacione::find($request->id);
    $ubicacion->detalles = $request->detalle;
    $ubicacion->save();
    return $ubicacion;
});

Route::get('ubicacion/{id}', function ($id) {
    return Ubicacione::find($id);
});


//pedidos
Route::get('pedidos/{phone}', function ($phone) {
    return Pedido::where('chatbot_id', $phone)->orderBy('created_at', 'desc')->with('estado', 'mensajero', 'productos')->get();
});

Route::get('pedidos/get/encola', function () {
    return Pedido::where('estado_id', 1)->orderBy('created_at', 'desc')->with('estado', 'cliente', 'productos', 'ubicacion')->get();
});


//Mensajeros libres de la Poblacion
Route::get('mensajeros/libre/{poblacion_id}', function($poblacion_id){
    return Mensajero::where('estado', 1)->where('poblacion_id', $poblacion_id)->get();
});

Route::get('mensajeros', function(){
    return Mensajero::all();
});

//Negocios del Pedido
Route::get('negocios/pedido/{midata}', function($midata){
    return Pedido::where('id', $midata)->with('productos')->first();
});
Route::post('negocios/by/tipo', function(Request $request){
    // return 'mierda';
    return Negocio::where('tipo_id', $request->tipo)->where('estado', true)->where('poblacion_id', $request->localidad)->orderBy('order', 'asc')->with('productos')->get();
});
Route::get('negocio/categorias/{tipo_id}', function($tipo_id){
    return Categoria::where('tipo_id', $tipo_id)->get();
});

// //Listar las categorias de los productos del Negocio
// Route::get('categorias/negocio/lista/{negocio_id}', function($negocio_id){
//     $productos=Producto::where('negocio_id', $negocio_id)->get();
//     $lista=[];
//     foreach ($productos as $item) {
        
//     }
// });
//TIPO
Route::get('tipo/negocios', function(){
    return Tipo::all();
});

//Buscar Pedido con Cliente
 Route::get('pedido/{id}', function($id){
    return Pedido::where('id', $id)->with('cliente', 'productos', 'ubicacion', 'mensajero', 'banipaydos')->first();
 });

 //Asignar Pedido a Mensajero
 Route::post('asignar/pedido', function(Request $request){
    $mensajero=Mensajero::where('telefono', $request->telefono)->first();
    $pedido= Pedido::find($request->pedido_id);
    if($pedido!=null && (($pedido->estado_id) ==1 || ($pedido->estado_id)==6)){
        $pedido->estado_id=2;
        $pedido->mensajero_id= $mensajero->id;
        $pedido->save();
        $mensajero->estado=0;
        $mensajero->save();
        return true;
    }
    else{
        return false;
    }    
 });
 //Cancelar Pedido de parte del Mensajero
 Route::post('cancelar/pedido', function(Request $request){
    $mensajero=Mensajero::where('telefono', $request->telefono)->first();
    if ($mensajero==null) {
        return false;
    }
    $pedido=Pedido::where('estado_id', 2)->where('mensajero_id', $mensajero->id)->with('cliente', 'productos', 'ubicacion', 'mensajero')->first();
    if($pedido!= null){
        $pedido->estado_id=1;
        $pedido->mensajero_id= 1;
        $pedido->save();
        $mensajero->estado=1;
        $mensajero->save();
        return $pedido;
    }
    else{
        return false;
    }    
 });
 Route::get('mensajero/{phone}', function ($phone) {
    $mimensajero =  Mensajero::where('telefono', $phone)->with('pedidos', 'localidad')->first();
    if ($mimensajero) {
        return $mimensajero;
    } else {
        // $mimensajero = Mensajero::create([
        //     'telefono' => $phone
        // ]);
        return false;
    }
});

//Mensajero por ID
Route::get('search/mensajero/{id}', function($id){
    return Mensajero::find($id);
});

//Mensajero por user_id
Route::get('find/mensajero/{user_id}', function($user_id){
    return Mensajero::where('user_id', $user_id)->first();
});
Route::get('mensajero/update/{phone}', function($phone){
    $mimsg =  Mensajero::where('telefono', $phone)->with('pedidos')->first();
    $mimsg->estado = $mimsg->estado ? false : true;
    $mimsg->save();
    return Mensajero::where('telefono', $phone)->with('pedidos')->first();
});
Route::get('mensajero/pedidos/{phone}', function($phone){
    $mimsg =  Mensajero::where('telefono', $phone)->with('pedidos')->first();
    $pedidos = Pedido::where('mensajero_id', $mimsg->id)->with('productos', 'cliente', 'pasarela', 'estado')->get();
    return $pedidos;
});
Route::get('mensajero/get/{telefono}', function($telefono){
    return Mensajero::where('telefono', $telefono)->first();
});
//Estado del Pedido Llevando
Route::get('llevando/pedido/{id}', function($id){
    $pedido= Pedido::where('id', $id)->with('cliente', 'productos', 'ubicacion', 'mensajero')->first();
    $pedido->estado_id=3;
    $pedido->save();
    return $pedido;
});

//Estado del Pedido Entregado
Route::get('entregando/pedido/{id}', function($id){
    $pedido= Pedido::where('id', $id)->with('cliente', 'productos', 'ubicacion', 'mensajero')->first();
    $pedido->estado_id=4;
    $pedido->save();
    $mensajero=Mensajero::find($pedido->mensajero_id);
    $mensajero->estado=1;
    $mensajero->save();
    return $pedido;
});

//Añadir Queja o Sugerencia del Pedido
Route::post('pedido/comentario', function(Request $request){
    $pedido= Pedido::where('chatbot_id', $request->telefono)->orderBy('created_at', 'desc')->first();
    if ($pedido) {
        $newcliente = Comentario::create([
            'pedido_id' => $pedido->id,
            'description' => $request->description
        ]);
        $pedido_comentado= Pedido::where('id', $pedido->id)->with('cliente', 'productos', 'ubicacion', 'mensajero', 'comentario')->first();
    }
    return $pedido_comentado;
});

//poblaciones
Route::get('poblaciones', function(){
    return Poblacione::all();
});
Route::get('poblacion/{id}', function ($id) {
    return Poblacione::find($id);
});

//APIS PARA BACKEND

//Todos los Pedidos entre fechas
Route::get('fecha/doble/pedidos/{midata}', function($midata){
    $midata2= json_decode($midata);
        return Pedido::whereBetween('created_at', [$midata2->date1, $midata2->date2])->with('cliente', 'productos', 'ubicacion', 'mensajero')->get();
});

// //Todos los Pedidos en una fecha
// Route::get('fecha/unica/pedidos/{midata}', function($midata){
//     $midata2= json_decode($midata);
//     return Pedido::where('created_at', $midata2->date1)->with('cliente', 'productos', 'ubicacion', 'mensajero')->get();
// });

//Todos los Negocios
Route::get('all/negocios', function(){
    return Negocio::all();
});
Route::get('negocio/get/{phone}', function($phone){
    return Negocio::where('chatbot_id', $phone)->first();
});

Route::get('negocio/update/{phone}', function($phone){
    $mimsg =  Negocio::where('chatbot_id', $phone)->with('productos', 'poblacion')->first();
    // return $mimsg;
    $mimsg->estado = $mimsg->estado ? false : true;
    $mimsg->save();
    return Negocio::where('chatbot_id', $phone)->with('productos', 'poblacion')->first();
});

//banipay
Route::post('banipay/save', function(Request $request) {
    $banipay = Banipay::create([
        'pedido_id' => $request->externalCode,
        'paymentId' => $request->paymentId,
        'transactionGenerated' => $request->transactionGenerated,
        'urlTransaction' => '?t='.$request->transactionGenerated.'&p='.$request->paymentId
    ]);
    return $banipay;
});

//banipay v.2
Route::post('banipay/dos/save', function(Request $request) {
    $banipay = Banipaydo::create([
        'pedido_id' => $request->identifier,
        'externalId'=> $request->externalId,
        'identifier'=>$request->identifier,
        'image'=>$request->image,
        'id_banipay' =>$request->id_banipay,
    ]);
    return $banipay;
});

//Actualizar Venta con Total Delivery y Negocios
Route::post('update/pedido/delivery', function(Request $request){
    $pedido= Pedido::find($request->pedido_id);
    $pedido->negocios=$request->negocios;
    $pedido->total_delivery=$request->total_delivery;
    $pedido->save();
    return true;
});

//Obtener todos los extra por negocio
Route::get('producto/extra/negocio/{negocio_id}', function($negocio_id){
    return Extraproducto::where('negocio_id', $negocio_id)->get();
});

Route::get('producto/extra/get/{id}', function($id){
    return Extraproducto::find($id);
});


//Restablecer Password Negocios-------
Route::post('reset/pw/negocio', function(Request $request){
    $negocio= Negocio::where('chatbot_id', $request->phone)->first();
    $user=User::find($negocio->user_id);
    $user->password=Hash::make($request->password);
    $user->save();
    return $user;
});

//Restablecer Password Mensajeros-------
Route::post('reset/pw/mensajero', function(Request $request){
    $mensajero= Mensajero::where('telefono', $request->phone)->first();
    $user=User::find($mensajero->user_id);
    $user->password=Hash::make($request->password);
    $user->save();
    return $user;
});

//Get User
Route::get('user/{id}', function($id){
    return User::find($id);
});

//Get Negocio del User
Route::get('user/negocio/{id}', function($id){
    return Negocio::where('user_id', $id)->with('productos', 'extras')->first();
});

//PedidoDetalle por Negocio
Route::get('pedido/detalle/negocio/{midata}', function($midata){
    $midata2=json_decode($midata);
    return PedidoDetalle::where('negocio_id', $midata2->negocio_id)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->orderBy('id', 'desc')->with('pedido')->get();
});

//Cliente por ID
Route::get('find/cliente/{id}', function($id){
    return Cliente::find($id);
});

//Pasarela por ID
Route::get('find/pago/{id}', function($id){
    return Pago::find($id);
});

//Estado por ID
Route::get('find/estado/{id}', function($id){
    return Estado::find($id);
});

//Pedido por ID
Route::get('find/pedido/{id}', function($id){
    return Pedido::where('id', $id)->with('cliente', 'mensajero', 'pasarela', 'estado', 'ubicacion', 'productos')->first();
});

Route::get('extra/{id}', function($id){
    return Extrapedido::where('pedido_detalle_id', $id)->with('extra')->get();
});

Route::get('reporte/fechas/negocio/{midata}', function($midata){
    $midata2=json_decode($midata);

    $detalle= PedidoDetalle::where('negocio_id', $midata2->negocio_id)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->with('extras', 'pedido')->get();
    $pedido_id=[];
    $pedido=[];
    $cantidad=0;
    $efectivo=0;
    $total_efectivo=0;
    $banipay=0;
    $total_banipay=0;
    $total_extras=0;
    $aux_total=0;
    foreach ($detalle as $item) {
        array_push($pedido_id, $item->pedido_id);
        if ($item->extras!=null) {
            foreach ($item->extras as $value) {
                $total_extras+=$value->total;
                $aux_total+=$value->total;
            }
           
        }
        if ($item->pedido->pago_id==1) {
            $total_efectivo+=$aux_total+$item->total;
        }
        else{
            $total_banipay+=$aux_total+$item->total;
        }
        $aux_total=0;
    }
    $arraypedido_id= array_unique($pedido_id);

    

    foreach ($arraypedido_id as $item) {
        $aux= Pedido::where('id', $item)->with('cliente', 'mensajero', 'pasarela', 'estado', 'ubicacion', 'extras')->first();
        array_push($pedido, $aux);
        $cantidad += 1;
        if ($aux->pago_id==1) {
            $efectivo+=1;
            //$total_efectivo+=$aux->total;
        }
        else{
            $banipay+=1;
            //$total_banipay+=$aux->total;
        }
    }

    $total_detalle = PedidoDetalle::where('negocio_id', $midata2->negocio_id)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->sum('total');
    $total=$total_detalle+$total_extras;

    return response()->json([
        'cantidad_total' => $cantidad,
        'total' => $total,
        'cantidad_efectivo' => $efectivo,
        'cantidad_banipay' => $banipay,
        'total_efectivo' => $total_efectivo,
        'total_banipay' => $total_banipay,
        'total_extras' => $total_extras
    ]);
});

Route::get('rel/precios/producto/{id}', function($id){
    return RelProductoPrecio::where('producto_id', $id)->with('precios')->get();
});

Route::get('find/comentario/{pedido_id}', function($pedido_id){
    return Comentario::where('pedido_id', $pedido_id)->first();
});

//Pedido por Mensajero
Route::get('pedido/mensajero/{midata}', function($midata){
    $midata2=json_decode($midata);
    return Pedido::where('mensajero_id', $midata2->mensajero_id)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->orderBy('id', 'desc')->with('cliente', 'mensajero', 'pasarela', 'estado', 'ubicacion')->get();
});

//Prueba
Route::get('prueba/mensajero/{mensajero_id}/{date1}/{date2}', function($mensajero_id, $date1, $date2){
    // $midata2=json_decode($midata);
    return Pedido::where('mensajero_id', $mensajero_id)->whereBetween('created_at', [$date1, $date2])->orderBy('id', 'desc')->with('cliente', 'mensajero', 'pasarela', 'estado', 'ubicacion')->get();
});

Route::get('ventas/fechas/mensajero/{midata}', function($midata){
    $midata2=json_decode($midata);
    $pedidos = Pedido::where('mensajero_id', $midata2->mensajero_id)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->get();
    // $cantidad = Pedido::where('mensajero_id', $midata2->mensajero_id)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->count();
    // $cantidad_efectivo = Pedido::where('mensajero_id', $midata2->mensajero_id)->where('pago_id', 1)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->count();
    // $total_efectivo= Pedido::where('mensajero_id', $midata2->mensajero_id)->where('pago_id', 1)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->sum('total');
    // $cantidad_banipay= Pedido::where('mensajero_id', $midata2->mensajero_id)->where('pago_id', 2)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->count();
    // $total_banipay= Pedido::where('mensajero_id', $midata2->mensajero_id)->where('pago_id', 1)->whereBetween('created_at', [$midata2->date1, $midata2->date2])->sum('total');
    $cantidad=0;
    $total=0;
    $cantidad_efectivo=0;
    $cantidad_banipay=0;
    $total_efectivo=0;
    $total_banipay=0;
    $total_negocio=0;
    $total_delivery=0;

    foreach ($pedidos as $item) {
        if ($item->pago_id==1) {
            $cantidad_efectivo+=1;
            $total_efectivo+=($item->total+$item->total_delivery);
        }
        if ($item->pago_id==2) {
            $cantidad_banipay+=1;
            $total_banipay+=($item->total+$item->total_delivery);
        }
        $cantidad+=1;
        $total+=($item->total+$item->total_delivery);
        $total_negocio+=$item->total;
        $total_delivery+=$item->total_delivery;

    }
    return response()->json([
        'cantidad_total' => $cantidad,
        'total' => $total,
        'cantidad_efectivo' => $cantidad_efectivo,
        'cantidad_banipay' => $cantidad_banipay,
        'total_efectivo' => $total_efectivo,
        'total_banipay' => $total_banipay,
        'total_negocio' => $total_negocio,
        'total_delivery' => $total_delivery

    ]);
});

//Todos los Laboratorios
Route::get('all/laboratorios', function(){
    return Laboratorio::all();
});

Route::post('search/producto/negocio', function (Request $request) {
    $result = Producto::where('negocio_id', $request->negocio_id)->where('nombre', 'like', '%'.$request->criterio.'%')->orWhere('titulo', 'like', '%'.$request->criterio.'%')->orWhere('etiqueta', 'like', '%'.$request->criterio.'%')->orderBy('nombre', 'desc')->with('categoria','negocio')->get();
    return $result;
});

//Search Producto para el Chatbot
Route::post('search/producto/negocio/chatbot', function (Request $request) {
    $result = Producto::where('negocio_id', $request->negocio_id)->where('nombre', 'like', '%'.$request->criterio.'%')->orWhere('titulo', 'like', '%'.$request->criterio.'%')->orWhere('etiqueta', 'like', '%'.$request->criterio.'%')->orderBy('nombre', 'desc')->with('categoria','negocio')->limit(8)->get();
    return $result;
});


//catgorias
Route::get('categorias/get', function(){
    return Categoria::all();
});


//Update Slug
Route::post('update/producto/slug', function(Request $request){
    $producto=Producto::find($request->id);
    $producto->slug=$request->slug;
    $producto->save();
});


// -------- users
Route::get('user/get/phone/{phone}', function($phone){
    $user = Mensajero::where('telefono', $phone)->with('user', 'localidad', 'pedidos')->first();
    if (!$user) {
        # code...
        $user = Negocio::where('chatbot_id', $phone)->with('user', 'poblacion')->first();
        if (!$user) {
            return null;
        }else {
            # code...
            return $user;
        }
    }else{
        return $user;
    }

});

//Change Status Producto
Route::get('change/status/producto/{id}', function($id){
    $producto=Producto::find($id);
    if ($producto->ecommerce==1) {
        $producto->ecommerce=0;
        $producto->save();
        return true;
    }
    else{
        $producto->ecommerce=1;
        $producto->save();
        return true;
    }

});

//productos
Route::post('productos/categoria', function(Request $request){
    return Producto::where('negocio_id', $request->negocio)->where('categoria_id', $request->categoria)->get();
});



Route::group(['prefix' => 'pos'], function () {
    Route::get('clientes/search/{criterio}', function($criterio){
        return Cliente::where('nombre', 'LIKE', '%'.$criterio.'%')->get();
    });

    Route::get('productos/search/{criterio}', function($criterio){
        return Producto::where('nombre', 'LIKE', '%'.$criterio.'%')->get();
    });
    
});

Route::group(['prefix' => 'app'], function () {

    Route::post('setauth', function (Request $request) {
        $miuser = Cliente::where('chatbot_id', $request->phone)->with('user')->first();
        // Auth::login($miuser->user, $remember = true);
        if ($miuser->pin === $request->pin) {
            # code...
            if (Auth::attempt(['email' => $miuser->user->email, 'password' => '123456'], true)) {
                // The user is being remembered...
                return $miuser;
            }else{
                return response()->json([
                    'message' => 'error en user'
                ]);
            }
        } else {
            # code...
            return response()->json([
                'message' => 'error en pin'
            ]);
        }
        

        // return $miuser;
    });
    
});