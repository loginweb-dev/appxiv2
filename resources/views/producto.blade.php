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
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <ul class="list-group list-group-flush mt-2 text-center mititle">
        <li class="list-group-item">
         <a class="mititle" href="/"> <i class="fa-solid fa-house-user"></i> Inicio</a>
        </li>
        <li class="list-group-item">
          <a class="mititle" href="/taxi/nuevo"> <i class="fa-solid fa-taxi"></i> Nuevo Viaje </a></li>
        <li class="list-group-item">
          <a class="mititle" href="/maps"><i class="fa-solid fa-location-crosshairs"></i> Cerca de Ti</a> 
        </li>
        <li class="list-group-item">
          <a class="mititle" href="/perfil"> <i class="fa-solid fa-address-card"></i> Mi Perfil </a>
        </li>
      </ul>
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
  </nav>
</div>

    @php
      $negocio= App\Negocio::find($producto->negocio_id);
    @endphp

    @if ($negocio->estado)        
      <div class="card">
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
              <h2 class="title mb-1 text-center mititle">{{$producto->nombre}}</h2>
              <p class="mt-2 text-center">{{ $producto->detalle }}</p>
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
                    <div class="label-rating">{{ $producto->ordenes }}<i class="fa-solid fa-cart-arrow-down"></i></div>
                    <div class="label-rating mititle">{{ $producto->categoria->nombre }}<i class="fa-solid fa-filter"></i></div>
                  </div> 
              <table class="table">
                
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
                  <tr>
                    <td width="50%">
                      <div class="">
                        @if ($producto->precio > 0)
                          Precio Bs:
                          <h2 class="mitext text-center">{{ number_format($producto->precio, 2, ',', '.') }}</h2>
                          <input type="number" id="miprecio" value="{{ $producto->precio }}" hidden>
                        @else
                          @php
                            $rel=App\RelProductoPrecio::where('producto_id', $producto->id)->get();                          
                          @endphp 
                          Precios Bs:
                          <select id="miprecios" class="form-control">
                            <option value="0">Elige un Precio</option>
                            @foreach ($rel as $item)
                                @php
                                    $precio = App\Precio::find($item->precio_id);
                                @endphp
                                <option value="{{ $precio->precio }}">{{ $precio->nombre.' '.$precio->precio }}</option>
                            @endforeach
                          </select>
                          <input type="number" id="miprecio" value="0" hidden>
                        @endif
                      </div> 
                    </td>
                    <td>
                      Cantidad:
                      <select id="micant" class="form-control">
                        @for ($i = 1; $i < 9; $i++)
                          <option value="{{ $i }}"> {{ $i }} </option>   
                        @endfor
                      </select>
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
                          @php
                              $extras = App\Extraproducto::where('negocio_id', $negocio->id)->get();
                          @endphp
                          <select id="miextras" class="form-control" multiple>
                            @foreach ($extras as $item)
                                <option value="{{ $item->precio }}">{{ $item->nombre.' '.$item->precio }} Bs</option>
                            @endforeach
                          </select>
                          <p style="text-align: justify; font-size: 11px;">Si desea agregar extras distintos a cada producto, agréguelos al carrito individualmente porfavor, esto es para distinguir que extras van en cada producto.</p>
                        </td>
                      </tr>
                    @endif
                  <tr>        
                    <td>
                      Total Bs:
                      <div id="total_producto"></div>
                    </td>   
                    <td>
                      <br>
                      <a onclick="agregar_carrito()" class="btn  miboton"> <i class="fas fa-shopping-cart"></i> Agregar a Carrito </a>
                    </td>        
                  </tr>
              </table>              
            </article> 
          </aside>
        </div> 
      </div> 

      {{-- section interesar  --}}
      <h4 class="mitext text-center">Te pueden interesar</h4>
      <div class="container-fluid">       
          <div class="col-sm-12">            
            <div class="slick-slider" data-slick='{"slidesToShow": 3, "slidesToScroll": 3}'>
              @foreach ($productos as $item)
                @if ($item->id != $producto->id)                                         
                  <a href="{{ route('producto', [$negocio->slug, $item->slug]) }}">
                    <div class="item-slide p-1">
                      <figure class="card card-product">
                        <div class="img-wrap"> <img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : Voyager::image(setting('productos.img_default_producto')) }}"> </div>
                          <h6 class="mititle text-truncate">{{ $item->nombre }}</h6>
                      </figure>
                    </div>
                  </a>
                  @endif
                @endforeach           
            </div>
          </div>
      </div>

      {{-- section comentarios  --}}
      <div class="text-center">
        <h4 class="text-center mitext"><i class="fa-solid fa-share-from-square"></i> Compartir</h4>
        <div class="ss-box ss-circle text-center" data-ss-content="false"></div>
        <div class="fb-comments" data-href="{{ route('producto',[$negocio->slug, $producto->slug]) }}" data-width="100%" data-numposts="5"></div>
      </div>
    @else
        <h4 class="text-center mitext m-3">Negocio Cerrado</h4>
    @endif
@endsection
@section('javascript')
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="{{ asset('js/social-share.js') }}" crossorigin="anonymous"></script>
  <script>
  var toastr = new Toastr({});
	$(document).ready( function(){
    $('#precio_producto').val('{{$producto->precio}}')  		
		localStorage.setItem('extras', JSON.stringify([]));
    mitotal()
    
	});

  function mitotal() {
    var total = 0
    var textras = 0
    $("#miextras :selected").map(function(i, el) {
      textras += parseFloat($(el).val())
    }).get();
    total = parseFloat($("#miprecio").val()) * parseInt($("#micant").val())
    total += textras
    // console.log($("#miprecio").val())
    $('#total_producto').html("<h2 class='mitext text-center'>"+formatMoney(total, ".", ",")+"</h2>")
    return total
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

  $('#miprecios').on('change', async function(){
    $("#miprecio").val(this.value)
    // console.log(this.value)
      mitotal()
  });

  $('#micant').on('change', async function(){
    if (this.value > 1) {
      console.log(this.value)
      $("#miextras").attr("hidden", true) 
    }else{
      $("#miextras").attr("hidden", false) 
    }
      mitotal()
  });

  $('#miextras').on('change', async function(){
    var miselect = []
    $("#miextras :selected").map(function(i, el) {
      miselect.push($(el).val())
    }).get()
    mitotal()
  });

  async function agregar_carrito()
  {
    if (mitotal() == 0) {
      toastr.show("Agrega una cantidad o precio.")
    } else {       
      @if(Auth::user())
      toastr.show("{{ $producto->nombre }}, agregado a tu carrito, revisa tu whatsapp.")
      var miuser = await axios('https://appxi.net/api/app/cliente/by/user/{{ Auth::user()->id }}')
      // console.log(miuser.data)
      var data={
          product_id: "{{ $producto->id }}",
          product_name: "{{ $producto->nombre }}",
          chatbot_id: miuser.data.chatbot_id,
          precio: $("#miprecio").val(),
          cantidad: parseInt($('#micant').val()),
          negocio_id: "{{ $negocio->id }}",
          negocio_name: "{{ $negocio->nombre }}"
        }
        var carrito = await axios.post("{{setting('admin.url')}}api/chatbot/cart/add", data)
        // console.log(carrito.data)
        @if($producto->extra)
          var miselect = []
          $("#miextras :selected").map(function(i, el) {
            miselect.push($(el).val())
          }).get()
          for (let index = 0; index < miselect.length; index++) {
            var midata={
              extra_id: miselect[index],
              precio: 10,
              cantidad: 1,
              total: 10,
              carrito_id: carrito.data.id,
              producto_id: "{{ $producto->id }}"
            }
            await axios.post("{{setting('admin.url')}}api/carrito/add/extras", midata)	
          }	
        @endif
        var list = "{{ $producto->nombre }}, agregado a tu carrito\n"
        list += '*A* .- Enviar pedido\n'  
        list += '*B* .- Seguir comprando\n'
        list += '*C* .- Vaciar Carrito\n'
        list += '----------------------------------\n'
        list += 'Envía una opción (ejemplo: *A*)'
        await axios.post("https://delivery-chatbot.appxi.net/newproduct", {
            phone: miuser.data.chatbot_id,
            message: list
          })
          // toastr.show("{{ $producto->nombre }}, agregado a tu carrito, revisa tu whatsapp.")
          location.href="/{{ $negocio->slug }}"
      @else
        location.href = '/perfil'
      @endif
    }
  }
  </script>
@endsection