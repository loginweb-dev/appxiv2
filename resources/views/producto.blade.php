@extends('master')

@section('meta')
<title>{{ $producto->nombre }}</title>
<meta name="description" content="{{ $producto->detalle }}" />
<meta property="og:site_name" content="{{ $producto->nombre }}">
<meta property="og:title" content="{{ $producto->nombre }}" />
<meta property="og:description" content="{{ $producto->detalle }}" />
<meta property="og:image" itemprop="image" content="{{ $producto->image ? Voyager::image($producto->thumbnail('cropped', 'image')) : setting('productos.img_default_producto') }}">
<meta property="og:type" content="website" />
<meta property="og:updated_time" content="1440432930" />
@endsection
@section('css')
<link href="{{ $producto->image ? asset('storage/'.$producto->image) : setting('productos.img_default_producto') }}" rel="shortcut icon" type="image/x-icon">
@endsection

@section('content')
{{-- -------------- UI MOVIL -------------- --}}
<div class="d-block d-sm-none">
  <nav class="navbar navbar-expand-sm sticky-top navbar-light justify-content-between" style="background-color: #F0F0F4;">
    <a class="navbar-brand" href="{{ route('negocio', $negocio->slug) }}">
      <img src="{{ Voyager::image($negocio->thumbnail('perfil', 'logo')) }}" width="30" height="30" class="d-inline-block align-top rounded" alt="">
      {{ $negocio->nombre }}
      <br>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fa-solid fa-share-nodes"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <p class="text-center">{{ $negocio->descripcion }}</p>
      <p class="text-center">Compartir:</p>
      <div class="ss-box ss-circle text-center" data-ss-content="false"></div>
    </div>
  </nav>
</div>

{{-- -------------- UI DESSKTOP -------------- --}}
<div class="container-fluid d-none d-sm-block mt-2">
  <nav class="navbar sticky-top navbar-light" style="background-color: #F0F0F4;">
    <a class="navbar-brand" href="{{ Voyager::image($negocio->thumbnail('perfil', 'logo')) }}  Voyager::image($negocio->thumbnail('perfil', 'logo')) }}">
      <img src="{{ asset('storage/'.$negocio->logo) }}" width="30" height="30" class="d-inline-block align-top" alt="">
      {{ $negocio->nombre }}
    </a>
    <div class="ss-box ss-circle text-center d-none d-sm-block mt-2" data-ss-content="false"></div>
  </nav>
</div>

  @php
    // $image=$producto->image ? $producto->image : setting('productos.img_default_producto');
    $negocio= App\Negocio::find($producto->negocio_id);
  @endphp
      <div class="card mt-1">
        <div class="row no-gutters">
          <aside class="col-sm-5">
            <article class="gallery-wrap"> 
              <div class="img-wrap">
                @if ($producto->nuevo)
                  <span class="badge-new"> Nuevo </span>
                @endif
                @if ($producto->endescuento)
                  <span class="badge-offer"><b> - {{ $producto->endescuento }}%</b></span>
                @endif		
                <img src="{{ ($producto->image!=null) ? Voyager::image($producto->thumbnail('cropped', 'image')) : asset('storage/'.setting('productos.img_default_producto')) }}" alt="{{$producto->nombre}}" width="100%">
              </div>
            </article>
          </aside>
          <aside class="col-sm-7">
            <article class="p-1">
              <h2 class="title mb-1 text-center mitext">{{$producto->nombre}}</h2>
              <p class="mt-2 text-center">{{ $producto->detalle }}</p>
                  {{-- Calificacion: --}}
                  <div class="rating-wrap text-center">
                    <ul class="rating-stars">
                      <li style="width:{{ $producto->rating }}%" class="stars-active"> 
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> 
                      </li>
                      <li>
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> 
                      </li>
                    </ul>
                    {{-- <div class="label-rating">132 reviews</div> --}}
                    <div class="label-rating">{{ $producto->ordenes }}<i class="fa-solid fa-cart-arrow-down"></i></div>
                    <div class="label-rating mititle">{{ $producto->categoria->nombre }}<i class="fa-solid fa-filter"></i></div>
                  </div> 
              <table class="table">
                  <tr>
                    @if ($negocio->tipo_id == 2)
                        <tr>
                          <td>
                            Laboratorio:
                            <p>{{ $producto->laboratorio->name }}</p>
                          </td>
                          <td>
                            Etiqueta:
                            <p>{{ $producto->etiqueta }}</p>
                          </td>
                        </tr>
                    @endif

                    <td width="50%">
                      <div class="mb-1">
                        @if ($producto->precio > 0)
                          Precio Bs:
                          <h2 class="mitext text-center">{{ number_format($producto->precio, 2, ',', '.') }}</h2>
                        @else
                          @php
                            $rel=App\RelProductoPrecio::where('producto_id', $producto->id)->get();
                          @endphp 
                          Precios Bs:
                          <select name="opciones_producto" id="opciones_producto" class="form-control">
                              @foreach ($rel as $item2)
                                @php
                                  $precio_prod= App\Precio::find( $item2->precio_id);
                                @endphp
                                <option value="">{{ $precio_prod->nombre.' '.$precio_prod->precio }}</option>                    
                              @endforeach
                            </select>
                        @endif
                      </div> 
                    </td>
                    <td>
                      Cantidad:
                      <input class="form-control" type="number" id="cantidad_producto" min="1" value="1">
                    </td>
                  </tr>
                    @if(count($producto->tallas) != 0)
                        <tr>
                          <td colspan="2">
                            Tallas Disponibles:
                            <br>
                            @php
                                $tallas = App\RelProductoTalla::where('producto_id', $producto->id)->with('tallas')->get();
                            @endphp
                            <select name="" id="" class="form-control">
                              @foreach ($tallas as $value)
                                <option value="{{ $value->id }}">{{ $value->tallas->nombre }}</option>   
                              @endforeach
                          </select>
                          </td>
                        </tr>
                    @endif
                    @if ($producto->extra)  
                      <tr>
                        <td colspan="2">
                          <strong>Extras: </strong> Armala como te gusta..!:
                          <br>
                          @php
                              $extras = App\Extraproducto::where('negocio_id', $negocio->id)->get();
                          @endphp
                          <div class="input-group-append">
                            <input class="form-control" id="texto_extras" type="text" readonly>
                            <button   class="btn btn-sm miboton" id="button_extras"  data-toggle="modal" data-target="#modal-lista_extras" onclick="addextra('{{$producto->negocio_id}}','{{$producto->id}}')"> <i class="fa fa-plus-square-o"></i></button>
                          </div>
                          <input id="subtotal_extras" readonly type="number"  value="0" hidden>
                          <p style="text-align: justify; font-size: 11px;">Si desea agregar extras distintos a cada producto, agréguelos al carrito individualmente porfavor, esto es para distinguir que extras van en cada producto.</p>
                        </td>
                      </tr>
                    @else
                      <input id="subtotal_extras" readonly type="number"  value="0" hidden>
                    @endif
                  <tr>        
                    <td>
                      Total Bs:
                      <div id="total_producto"></div>
                      <input readonly id="subtotal_producto" type="number" hidden>
                      <input id="precio_producto" type="number" hidden>
                    </td>   
                    <td>
                      <br>
                      <a onclick="agregar_carrito('{{$producto->id}}')" class="btn  miboton"> <i class="fas fa-shopping-cart"></i> Agregar a Carrito </a>
                    </td>        
                  </tr>
              </table>
              
            </article> 
          </aside>
        </div> 
      </div> 

  <h4 class="p-2">Te pueden interesar</h4>
  <div class="slick-slider" data-slick='{"slidesToShow": 3, "slidesToScroll": 1}'>
    @foreach ($productos as $item)
    <a href="{{ route('producto', [$negocio->slug, $item->slug]) }}">
      <div class="item-slide p-1">
        <figure class="card card-product">
          <div class="img-wrap"> <img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : Voyager::image(setting('productos.img_default_producto')) }}"> </div>
          <figcaption class="info-wrap text-center">
            <h6 class="title text-truncate mititle">{{ $item->nombre }}</h6>
          </figcaption>
        </figure>
      </div>
    </a>
    @endforeach
  </div>


  <div class="fb-comments" data-href="{{ route('producto',[$negocio->slug, $producto->slug]) }}" data-width="100%" data-numposts="5"></div>


  <div class="modal modal-primary fade" tabindex="-1" id="modal-lista_extras" role="dialog">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="voyager-list-add"></i> Lista de extras</h4>
      </div>
      <div class="modal-body">
        <input type="text" name="producto_extra_id" id="producto_extra_id" hidden>
        <input type="text" name="tr_producto" id="tr_producto" hidden>
  
        <table class="table" id="table-extras">
          <thead>
            <tr>
              {{-- <th>Imagen</th> --}}
              <th hidden>ID</th>
              <th>Extra</th>
              <th>Precio</th>
              <th>Cantidad</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">          
        <button type="button" class="btn btn-secundary pull-right" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn miboton pull-right" onclick="calcular_total_extra()" data-dismiss="modal">Añadir</button>
      </div>
    </div>
    </div>
  </div>
@endsection
@section('javascript')
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="{{ asset('js/social-share.js') }}" crossorigin="anonymous"></script>
  <script>
	$(document).ready( function(){
		// addextra('{{$producto->negocio_id}}' , '{{$producto->id}}')
    $('#precio_producto').val('{{$producto->precio}}')  
		totales()
		localStorage.setItem('extras', JSON.stringify([]));
		localidad_validacion()
    extras()
    opciones()
   
	});
  async function extras(){
    var condicion ='{{$producto->extra}}'
    
    if (condicion!=0) {
      $("#extras_opciones").attr("hidden",false); 
    }
  }
	async function localidad_validacion(){
		// var user = JSON.parse(localStorage.getItem('misession'));

		// localidad= user.localidad.id
		// id='{{$producto->id}}'
		// var negocio= await axios("{{setting('admin.url')}}api/producto/"+id)
		// if (negocio.data.negocio.poblacion_id!=localidad) {
		// 	location.href="{{setting('admin.url')}}"
		// }
	}

  function formatMoney(number, decPlaces, decSep, thouSep) {
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
    decSep = typeof decSep === "undefined" ? "," : decSep;
    thouSep = typeof thouSep === "undefined" ? "." : thouSep;
    var sign = number < 0 ? "-" : "";
    var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
    var j = (j = i.length) > 3 ? j % 3 : 0;

    return sign +
        (j ? i.substr(0, j) + thouSep : "") +
        i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
        (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
}

	function totales(){
		var cantidad=$('#cantidad_producto').val()
		var subtotal_producto=parseFloat(cantidad)*parseFloat($('#precio_producto').val())
    // console.log(subtotal_producto)
		$('#subtotal_producto').val(subtotal_producto)
		var total = subtotal_producto+parseFloat($('#subtotal_extras').val())
    // console.log(total)
		$('#total_producto').html("<h2 class='mitext text-center'>"+formatMoney(total, ".", ",")+"</h2>")

	}
	async function addextra(negocio_id , producto_id) {
        $("#table-extras tbody tr").remove();
        $("#producto_extra_id").val(producto_id);
        //$("#tr_producto").val(code);
        //console.log(extras)
        var mitable="";
        var extrasp=  await axios.get("{{ setting('admin.url') }}api/producto/extra/negocio/"+negocio_id);
        for(let index=0; index < extrasp.data.length; index++){
            // var image = extrasp.data[index].image ? extrasp.data[index].image : "{{ setting('productos.imagen_default') }}"
            // mitable = mitable + "<tr><td> <img class='img-thumbnail img-sm img-responsive' height=40 width=40 src='{{setting('admin.url')}}storage/"+image+"'></td><td>"+extrasp.data[index].id+"</td><td><input class='form-control extra-name' readonly value='"+extrasp.data[index].name+"'></td><td><input class='form-control extra-precio' readonly  value='"+extrasp.data[index].precio+" Bs."+"'></td><td><input class='form-control extra-cantidad' style='width:100px' type='number' min='0' value='0'  id='extra_"+extrasp.data[index].id+"'></td></tr>";
            // mitable = mitable + "<tr><td><input class='form-control extraprodid' readonly value='"+extrasp.data[index].id+"'></td><td><input class='form-control extra-name' readonly value='"+extrasp.data[index].nombre+"'></td><td><input class='form-control extra-precio' readonly  value='"+extrasp.data[index].precio+" Bs."+"'></td><td><input class='form-control extra-cantidad' style='width:100px' type='number' min='0' value='0'  id='extra_"+extrasp.data[index].id+"'></td></tr>";
			mitable = mitable + "<tr><td ><input class='form-control extraprodid' hidden value='"+extrasp.data[index].id+"'><input class='form-control col-sm-12 extra-name' readonly value='"+extrasp.data[index].nombre+"'></td><td><input class='form-control extra-precio' readonly  value='"+extrasp.data[index].precio+"Bs."+"'></td><td><input class='form-control extra-cantidad' style='width:100px' type='number' min='0' value='0'  id='extra_"+extrasp.data[index].id+"'></td></tr>";

        }
        $('#table-extras').append(mitable);
  }
  async function calcular_total_extra(){
    var cantidad=[];
    var name=[];
    var precio=[];
    var idprod=[];
    var index_cantidad=0;
    var index_name_aux=0;
    var index_precio_aux=0;
    var index_cantidad_aux=0;
    var index_idprod_aux=0;
    

    $('.extra-cantidad').each(function(){
        if($(this).val()>0){
            cantidad[index_cantidad_aux]=parseFloat($(this).val());
            index_cantidad_aux+=1;
            var index_name=0;
            $('.extra-name').each(function(){
                if(index_name==index_cantidad){
                    name[index_name_aux]=$(this).val();
                    index_name_aux+=1;
                }
                index_name+=1;
            });

            var index_precio=0;
            $('.extra-precio').each(function(){
                if(index_precio==index_cantidad){
                    precio[index_precio_aux]=parseFloat($(this).val());
                    index_precio_aux+=1;
                }
                index_precio+=1;
            });

            var index_idprod=0;
            $('.extraprodid').each(function(){
                if(index_idprod==index_cantidad){
                    idprod[index_idprod_aux]=parseFloat($(this).val());
                    index_idprod_aux+=1;
                }
                index_idprod+=1;
            });


        }
        index_cantidad+=1;
    });
    //   console.log(name)
    //   console.log(cantidad)
    //   console.log(precio)
    //   console.log(idprod)
    var nombre_extras=""
    var precio_extras=0
    for(let index=0;index<precio.length;index++){
    if (index+1<precio.length) {
      nombre_extras+=cantidad[index]+' '+name[index]+", ";
    }
    else{
      nombre_extras+=cantidad[index]+' '+name[index];
    }
        //console.log(nombre_extras)
        precio_extras+=parseFloat(cantidad[index])*parseFloat(precio[index]);
      }
    $('#texto_extras').val(nombre_extras)
    $('#subtotal_extras').val(precio_extras)

    //   for (let index = 0; index < idprod.length; index++) {
      
    //   }
    totales()
    var extras_temp={name:name, cantidad:cantidad, precio:precio, idprod:idprod}
    localStorage.setItem('extras', JSON.stringify(extras_temp))

  }
	$('#cantidad_producto').on('change', function() {
       
        if ($('#cantidad_producto').val()!=1) {
          // $("#extras_opciones").attr("hidden",true);
          $("#button_extras").attr("disabled",true);          
          localStorage.setItem('extras', JSON.stringify([]));
          $('#subtotal_extras').val(0)
          $('#texto_extras').val("")
        }
        else{
          if ('{{$producto->extra}}'==1) {
            // $("#extras_opciones").attr("hidden",false); 
            $("#button_extras").attr("disabled",false);
          }
        }
        totales()
    });
	
	$("#cantidad_producto").keyup(function(){
        if ($('#cantidad_producto').val()!=1) {
          // $("#extras_opciones").attr("hidden",true);
          $("#button_extras").attr("disabled",true);
          localStorage.setItem('extras', JSON.stringify([]));
          $('#subtotal_extras').val(0)
          $('#texto_extras').val("") 
        }
        else{
          if ('{{$producto->extra}}'==1) {
            // $("#extras_opciones").attr("hidden",false);
            $("#button_extras").attr("disabled",false);

          } 
        }
        totales();

    });

	async function agregar_carrito(id) {
    misession = JSON.parse(localStorage.getItem('misession'))
    if (misession) {
      
    } else {
      location.href = '/login'
    }


      var producto= await axios("{{setting('admin.url')}}api/producto/"+id)
      var user = JSON.parse(localStorage.getItem('misession'));
      var telefono ='591'+user.phone+'@c.us'
      var nombre = user.name
      var localidad= user.localidad.id
      var precio =0;
      var product_name=""
      if (producto.data.precio==0) {
        var aux_precio= await axios("{{setting('admin.url')}}api/precio/"+$('#opciones_producto').val())
        product_name=producto.data.nombre+" "+aux_precio.data.nombre
        precio= aux_precio.data.precio
      } else {
        precio=producto.data.precio
        product_name=producto.data.nombre
      }
      var cliente= await axios("{{setting('admin.url')}}api/cliente/"+telefono)
      if (cliente.data.poblacion_id) {
      }
      else{
        var midata={
          id:cliente.data.id,
          nombre:nombre,
        }
        await axios.post("{{setting('admin.url')}}api/cliente/update/nombre", midata)
        var midata={
          id:cliente.data.id,
          poblacion_id:localidad,
        }
        await axios.post("{{setting('admin.url')}}api/cliente/update/localidad", midata)
      }
      var data={
        product_id: id,
        product_name: product_name,
        chatbot_id: telefono,
        precio: precio,
        cantidad: parseInt($('#cantidad_producto').val()),
        negocio_id: producto.data.negocio.id,
        negocio_name: producto.data.negocio.nombre
      }
      var carrito= await axios.post("{{setting('admin.url')}}api/chatbot/cart/add", data)
	  var extras = JSON.parse(localStorage.getItem('extras'));
	  if (extras.idprod) {
		for (let index = 0; index < extras.idprod.length; index++) {
			var midata={
				extra_id:extras.idprod[index],
				precio:extras.precio[index],
				cantidad:extras.cantidad[index],
				total:parseFloat(extras.precio[index])*parseFloat(extras.cantidad[index]),
				carrito_id:carrito.data.id,
				producto_id:id
			}
			await axios.post("{{setting('admin.url')}}api/carrito/add/extras", midata)			
		}
    $('#cantidad_producto').val(1)
		localStorage.setItem('extras', JSON.stringify([]));
    $('#subtotal_extras').val(0)
    $('#texto_extras').val("")
    totales()

	  }
      if (carrito.data) {
        var list = '*🎉 Producto agregado a tu carrito 🎉*\n'
        list += 'Si deseas agregar mas productos a tu carrito visita el mismo u otros negocios (A).\n'
        list += '------------------------------------------\n'
        list += '*A* .- PEDIR AHORA\n'
        list += '*B* .- SEGUIR COMPRANDO\n'
        // list += '*C* .- VER TU CARRITO\n'
        list += '------------------------------------------\n'
        list += 'ENVIA UNA OPCION ejemplo: A o B'
        
        //Mensaje a Cliente
        pb.info(
          'Producto Agregado a Carrito Exitosamente, debes terminar el Pedido en WhatsApp o puedes seguir añadiendo más productos a tu Carrito.'
        );
        // toastr.succes("Producto Agregado a Carrito Exitosamente, debes terminar el Pedido en WhatsApp o puedes seguir añadiendo más productos a tu Carrito.")
        var data={
          message:list,
          phone:telefono,
          status:1.1
        }
        await axios.post("{{setting('admin.chatbot_url_clientes')}}cart", data)
      }
    }

  async function opciones(){
    var id= '{{$producto->id}}'
    var producto= await axios("{{setting('admin.url')}}api/producto/"+id)
    if (producto.data.precio==0) {
      $("#op_producto").attr("hidden",false); 
      $('#opciones_producto').find('option').remove().end();
      var table= await axios("{{setting('admin.url')}}api/rel/precios/producto/"+id)
      //console.log(table.data)
      $('#opciones_producto').append($('<option>', {
          value: null,
          text: 'Elige una Opción'
      }));
      for (let index = 0; index < table.data.length; index++) {
          $('#opciones_producto').append($('<option>', {
              value: table.data[index].precios.id,
              text: table.data[index].precios.nombre+" "+table.data[index].precios.precio+"Bs."
          }));
      }
    }

  }
  $('#opciones_producto').on('change', async function(){
    if ( $('#opciones_producto').val()>0) {
      var aux_precio= await axios("{{setting('admin.url')}}api/precio/"+$('#opciones_producto').val())
      $('#precio_producto').val(aux_precio.data.precio)
      totales()
    }
  });
  </script>
@endsection