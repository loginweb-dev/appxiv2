@extends('master')

@section('meta')
<title>{{ $producto->nombre }}</title>
<meta name="description" content="{{ $producto->detalle }}" />
<meta property="og:site_name" content="{{ $producto->nombre }}">
<meta property="og:title" content="{{ $producto->nombre }}" />
<meta property="og:description" content="{{ $producto->detalle }}" />
<meta property="og:image" itemprop="image" content="{{ $producto->image ? Voyager::image($producto->thumbnail('cropped', 'image')) : Voyager::image($negocio->thumbnail('perfil', 'logo')) }}">
<meta property="og:type" content="website" />
<meta property="og:updated_time" content="1440432930" />
@endsection
@section('css')
<link href="{{ $producto->image ? Voyager::image($producto->thumbnail('cropped', 'image')) : Voyager::image($negocio->thumbnail('perfil', 'logo')) }}" rel="shortcut icon" type="image/x-icon">
<style>
</style>
@endsection

@section('content')
{{-- -------------- UI MOVIL -------------- --}}
<div class="d-block d-sm-none">
  <nav class="navbar sticky-top navbar-light justify-content-center" style="background-color: #F0F0F4;">
    <a class="navbar-brand mititle" href="{{ route('negocio', $negocio->slug) }}">
      <img src="{{ Voyager::image($negocio->thumbnail('perfil', 'logo')) }}" width="30" height="30" class="d-inline-block align-top rounded" alt="">
      {{ $negocio->nombre }}
      <br>
    </a>

    <div id="miback">
      <a href="{{ route('negocio', $negocio->slug) }}"class="mititle"><i class="fa-solid fa-circle-left fa-2xl"></i></a>
    </div>  
    <div id="micart2">
      <a href="#" onclick="micart()">
        <div class="icon-wrap icon-xs round text-light" style="background-color: #0E2944">
        <i class="fa-solid fa-cart-arrow-down"></i>
        <span class="notify" style="background-color: #E12D47"><div id="micart_count"></div></span>	
        </div>
      </a>
    </div>  
  </nav>
  <div id="mireload">
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
                <img class="img-thumbnail rounded" src="{{ ($producto->image!=null) ? Voyager::image($producto->thumbnail('cropped', 'image')) : Voyager::image($negocio->thumbnail('perfil', 'logo')) }}" alt="{{$producto->nombre}}" width="100%">
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
                        @for ($i = 1; $i < 7; $i++)
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
                                <option value="{{ $item->id }}">{{ $item->nombre.' '.$item->precio }}Bs</option>
                            @endforeach
                          </select>
                          <p style="text-align: center; font-size: 12px;">Si desea agregar extras distintos a cada producto, agréguelos al carrito individualmente porfavor, esto es para distinguir que extras van en cada producto.</p>
                        </td>
                      </tr>
                    @endif
                    @if ($producto->cocciones)  
                    <tr>
                      <td colspan="2">
                        Elije un coccion:
                        @php
                            $cocciones = App\Coccione::where('negocio_id', $negocio->id)->get();
                        @endphp
                        <select id="micoccion" class="form-control">
                          @foreach ($cocciones as $item)
                              <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                          @endforeach
                        </select>
                      </td>
                    </tr>
                  @endif
                  <tr>
                    <td colspan="2">
                      Mensaje al negocio:
                      <input type="text" class="form-control" id="mensajenegocio">
                    </td>
                  </tr>
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
                  <a onclick="mireload()" href="{{ route('producto', [$negocio->slug, $item->slug]) }}">
                    <div class="item-slide p-1">
                      <figure class="card card-product">
                        <div class="img-wrap"> <img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : Voyager::image($negocio->thumbnail('perfil', 'logo'))}}"> </div>
                          <h6 class="mititle text-truncate">{{ $item->nombre }}</h6>
                      </figure>
                    </div>
                  </a>
                  @endif
                @endforeach           
            </div>
          </div>
      </div>
    @else
        <h4 class="text-center mitext m-3">Negocio Cerrado</h4>
    @endif
  </div>
</div>
@endsection
@section('javascript')
  <script>

  var toastr = new Toastr({});
	$(document).ready( function(){
    // $("#mireload").html("<div class='text-center'><img src='/reload.gif' alt='mireload' class='img-fluid m-2 p-2' width='200'></div>")    
     
    console.log( "ready!" );
    $('#precio_producto').val('{{$producto->precio}}')  	
    mitotal()
    // $("#mireload").empty();
	});

  function mitotal() {
    var total = 0
    var textras = 0
    var miextra = 0
    @if($producto->extras)
      $("#miextras :selected").map(async function(i, el) {
        var miextra = await axios('https://appxi.net/api/app/extra/by/'+$(el).val()) 
        textras += parseFloat(miextra.data.precio)
        total = (parseFloat($("#miprecio").val()) * parseInt($("#micant").val())) + textras
        $('#total_producto').html("<h2 class='mitext text-center'>"+formatMoney(total, ".", ",")+"</h2>")
      }).get();
    @else
      total = (parseFloat($("#miprecio").val()) * parseInt($("#micant").val()))  
      $('#total_producto').html("<h2 class='mitext text-center'>"+formatMoney(total, ".", ",")+"</h2>")
    @endif
    // return total
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
      mitotal()
  });

  $('#micant').on('change', async function(){
    if (this.value > 1) {
      $("#miextras").attr("hidden", true) 
    }else{
      $("#miextras").attr("hidden", false) 
    }
    mitotal()
  });

  $('#miextras').on('change', function(){
    // var miselect = []
    // $("#miextras :selected").map(function(i, el) {
    //   miselect.push($(el).val())
    // }).get()
    console.log('miselect')
    mitotal()
  });

  async function agregar_carrito()
  {
    // $("#mireload").html("<div class='text-center'><img src='/reload.gif' alt='mireload' class='img-fluid m-2 p-2' width='200'></div>")
    if ($("#miprecio").val() == 0) {
      toastr.show("Agrega un precio, por favor")

    } else {       
      @if(Auth::user())
        toastr.show("{{ $producto->nombre }}, agregado a tu carrito.")
        var miuser = await axios('https://appxi.net/api/app/cliente/by/user/{{ Auth::user()->id }}')
        var data={
            product_id: "{{ $producto->id }}",
            product_name: "{{ $producto->nombre }}",
            chatbot_id: miuser.data.chatbot_id,
            precio: $("#miprecio").val(),
            cantidad: parseInt($('#micant').val()),
            negocio_id: "{{ $negocio->id }}",
            negocio_name: "{{ $negocio->nombre }}",
            @if ($producto->cocciones)  
            coccion_id: $('#micoccion').val(),
            @endif
            mensaje: $('#mensajenegocio').val()
        }
        var carrito = await axios.post("{{setting('admin.url')}}api/chatbot/cart/add", data)
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
        // var list = "{{ $producto->nombre }}, agregado a tu carrito\n"
        // list += '*A* .- Enviar pedido\n'  
        // list += '*B* .- Seguir comprando\n'
        // list += '*C* .- Vaciar Carrito\n'
        // list += '----------------------------------\n'
        // list += 'Envía una opción (ejemplo: *A*)'
        // await axios.post("https://delivery-chatbot.appxi.net/newproduct", {
        //     phone: miuser.data.chatbot_id,
        //     message: list
        // })
        // var micant_count = parseInt(localStorage.getItem("micart")) + parseInt($('#micant').val())
        // localStorage.setItem('micart', micant_count)
        location.href="/{{ $negocio->slug }}"
      @else
        $("#milogin").modal()
        // localStorage.setItem('mivolver', "{{ route('producto', [$negocio->slug, $producto->slug]) }}")
        // location.href = '/perfil'
      @endif
    }
  }


  function mireload() {
    $("#mireload").html("<div class='text-center'><img src='/reload.gif' alt='mireload' class='img-fluid m-2 p-2' width='200'></div>")    
  }

  // function miperfil(){
  //   localStorage.setItem('mivolver', "{{ route('producto', [$negocio->slug, $producto->slug]) }}")
  //   location.href = '/perfil'
  // }
  </script>
@endsection