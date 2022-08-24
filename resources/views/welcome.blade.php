@extends('master')

@section('meta')
<title>{{ setting('site.title') }}</title>
<meta name="description" content="{{ setting('site.description') }}" />
<meta property="og:site_name" content="{{ setting('site.title')  }}">
<meta property="og:title" content="{{ setting('site.title')  }}" />
<meta property="og:description" content="{{ setting('site.description') }}" />
<meta property="og:image" itemprop="image" content="{{ Voyager::image(setting('site.logo')) }}">
<meta property="og:type" content="website" />
<meta property="og:updated_time" content="1440432930" />
@endsection

@section('css')
<link href="{{ Voyager::image(setting('site.logo')) }}" rel="shortcut icon" type="image/x-icon">
<style>

.miaction img {
  width: 100%;
  height: auto;
}

.miaction .btn {
  position: absolute;
  top: 55%;
  left: 25%;
  transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  background-color: #0D2845;
  color: white;
  font-size: 16px;
  /* padding: 12px 24px; */
  border: none;
  cursor: pointer;
  border-radius: 5px;
}
.miaction .btn:hover {
  background-color: black;
}
#map {
        width: 100%;
        height: 200px;
    }
</style>
@endsection

@section('content')

  @php
    $tipos = App\Tipo::with('negocios')->orderBy('order', 'asc')->get();
    $localidades = App\Poblacione::orderBy('created_at', 'desc')->get();
    // $milocalidad = isset($_GET['localidad']) ? $_GET['localidad'] : 0;
    if (isset($_GET['tipo'])){
      $negocios = App\Negocio::where('estado', 1)->where('tipo_id', $_GET['tipo'])->orderBy('order', 'asc')->with('poblacion', 'tipo', 'productos')->get();
      $mitipo = $_GET['tipo'];
			$micriterio = '';
      $milocalidad = '';
    }else if (isset($_GET['criterio'])){
      $negocios = App\Negocio::where('nombre', 'like', '%'.$_GET['criterio'].'%')->where('estado', 1)->orderBy('order', 'asc')->with('poblacion', 'tipo', 'productos')->get();
      $productos = App\Producto::where('ecommerce', true)->where('nombre', 'LIKE', '%'.$_GET['criterio'].'%')->orderBy('updated_at', 'desc')->with('negocio')->get();
      $micriterio = $_GET['criterio'];
			$mitipo = 0;
      $milocalidad = 0;
    }else if (isset($_GET['localidad'])){
      $negocios = App\Negocio::where('estado', 1)->where('poblacion_id', $_GET['localidad'])->orderBy('order', 'asc')->with('poblacion', 'tipo', 'productos')->get();
      $micriterio = '';
			$mitipo = 0;
      $milocalidad = $_GET['localidad'];
    }else{
      $negocios = App\Negocio::where('estado', 1)->orderBy('order', 'asc')->with('poblacion', 'tipo', 'productos')->get();
      $micriterio ='';
			$mitipo = 0;
      $milocalidad = 0;
    }
  @endphp
  {{-- -------------- UI MOVIL -------------- --}}
  <div class="d-block d-sm-none">
    <nav class="navbar sticky-top navbar-light" style="background-color: #F0F0F4;">
      <a class="navbar-brand" href="/">
        <img src="storage/{{ setting('site.logo') }}" width="30" height="30" class="d-inline-block align-top" alt="">
        {{ setting('site.title') }}
      </a>
  
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation" style="background-color: #0E2944; color: white;">
        <i class="fa-solid fa-sliders"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="list-group list-group-flush mt-2 text-center mititle">
          {{-- <li class="list-group-item">
           <a class="mititle" href="/"> <i class="fa-solid fa-house-user"></i> Inicio</a>
          </li> --}}
          <li class="list-group-item">
            <a class="mititle" href="#" onclick="nuevoviaje()"> <i class="fa-solid fa-taxi fa-xl"></i> Solicitar Viaje (pedir taxi)</a>
          </li>
          <li class="list-group-item">
            <a class="mititle" href="#" onclick="mihome()"><i class="fa-solid fa-user"></i> Perfil, Viajes y Compras</a>
          </li>
          {{-- <li class="list-group-item">
            <a class="mititle" href="#" onclick="mimaps()"><i class="fa-solid fa-location-crosshairs fa-xl"></i> Negocios Cerca de Ti</a> 
          </li> --}}
          <li class="list-group-item">
            <a class="mititle" href="#" onclick="mihelp()"> <i class="fa-solid fa-headset fa-xl"></i> Soporte Tecnico </a>
          </li>
          
          @if (Auth::user())
              @if (Auth::user()->role_id == 4)
              <li class="list-group-item">
                <a class="mititle" href="#" onclick="mihelp()"> <i class="fa-solid fa-truck"></i> Soy Delivery </a>
              </li>
              @endif
          @endif
        </ul>
      </div>      

      <div id="micart">
          <a href="#" onclick="micart('/')">
            <div class="icon-wrap icon-xs round text-light" style="background-color: #0E2944">
              <i class="fa-solid fa-cart-arrow-down"></i>
              <span class="notify" style="background-color: #E12D47"><div id="micart_count"></div></span>
            </div>
          </a>
      </div>   
    </nav>

    <div id="mireload"> 
      {{-- Section Banner appxi  --}}
      <aside class="">
        <div id="carousel1_indicator" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carousel1_indicator" data-slide-to="0" class="active"></li>
            <li data-target="#carousel1_indicator" data-slide-to="1"></li>
            <li data-target="#carousel1_indicator" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="miaction">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/mibanner5.png" alt="First slide"> 
                <a href="#" onclick="nuevoviaje()" class="btn">SOLICITAR TAXI</a>              
              </div>
            </div>
            <div class="carousel-item">
              <div class="miaction">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/mibanner6.png" alt="Third slide">
                <a href="#" onclick="nuevoviaje()" class="btn">SOLICITAR TAXI</a>
              </div>
          </div>
            <div class="carousel-item">
              <div class="miaction">
              <img class="d-block w-100" src="https://appxi.net//storage/landinpage/mibanner4.png" alt="Second slide">
              <a href="#" onclick="nuevoviaje()" class="btn">SOLICITAR TAXI</a>
            </div>
            </div>
          </div>
          <a class="carousel-control-prev" href="#carousel1_indicator" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carousel1_indicator" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div> 	
      </aside>

      {{-- section buscador  --}}
      <div style="background-color: #F0F0F4; padding: 10px;">
        <h2 class="text-center">MARKETPLACE</h2>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-1 miinput" name="criterio" type="search" placeholder="Que vas a pedir hoy ?" aria-label="Buscar negocio o producto" value="{{ $micriterio }}">
          <button class="btn miboton" type="submit" hidden><i class="fa fa-search"></i></button>
        </form>
        <div class="text-center">
          <small class="" style="display: block; align: center;">{{ setting('site.description') }}</small>
        </div>      
      </div>

      {{-- section promociones --}}
      {{-- @foreach ($promociones as $item)
          
      @endforeach --}}

      {{-- section negocios por tipos --}}
      @if (isset($_GET['criterio']))
        @if (count($negocios) == 0)
          {{-- <h2 class="text-center mitext">sin negocios</h2> --}}
          {{-- <small><i class="fa-solid fa-shop"></i> sin negocios </small>     --}}
        @else					
          <h4 class="mitext text-center p-1 m-2">Negocios</h4>
          @foreach ($negocios as $item)				
          {{-- negocios --}}
            <div class="card mt-1">
              <a href="{{ route('negocio', $item->slug) }}">
              <figure class="itemside">
                <div class="aside">
                  <div class="img-wrap img-sm">
                  
                    <img src="{{ $item->logo ? Voyager::image($item->thumbnail('perfil', 'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}">
                  </div>
                </div>
                <figcaption class="p-1 align-self-center mititle">
                  <h5 class="mititle text-truncate">{{ $item->nombre }}</h5>
                  <p>{{ $item->descripcion }}</p>
                </figcaption>
              </figure> 
              </a>
            </div>
          @endforeach
        @endif
        @if(count($productos) == 0)
          {{-- <h2 class="text-center mitext">sin productos</h2> --}}
        @else				
          <h4 class="mitext mitext text-center p-1 m-2">Productos</h4>
          @foreach ($productos as $item)		
            @if ($item->negocio->estado)
                
            
            {{-- productos --}}
              <div class="card mt-1">
                <a href="{{ route('producto', [$item->negocio->slug, $item->slug]) }}">
                <figure class="itemside">
                  <div class="aside">
                    <div class="img-wrap img-sm">          
                      <img src="{{ $item->logo ? Voyager::image($item->thumbnail('cropped', 'image')) : Voyager::image($item->negocio->thumbnail('perfil', 'logo')) }}">
                    </div>
                  </div>
                  <figcaption class="p-1 align-self-center mititle">
                    <h5 class="mititle text-truncate">{{ $item->nombre }}</h5>
                        
                    <p>{{ $item->detalle }}</p>
                    <small><i class="fa-solid fa-shop"></i> {{ $item->negocio->nombre }}</small>    
                  </figcaption>
                </figure> 
                </a>
              </div>
            @endif	
          @endforeach
          <div class="text-center m-2">
            <a class="btn miboton btn-block" href="/">
              <i class="fa-solid fa-rotate-left"></i> Volver
            </a>
          </div>        
        @endif
      @else
        @foreach ($tipos as $item)        
            @php
              $minegocios = App\Negocio::where('estado', 1)->where('tipo_id', $item->id)->orderBy('updated_at', 'desc')->with('poblacion', 'tipo', 'productos')->get();
            @endphp
            @if (count($minegocios) != 0)          
              <h4 class="text-center mitext m-1"><i class="{{ $item->icon }}"></i> {{ $item->nombre }} </h4>
              <div class="container-fluid">
                <div class="col-sm-12">	
                  <div class="slick-slider" data-slick='{"slidesToShow": 2, "slidesToScroll": 2}'>
                    @foreach ($minegocios as $value)
                        <div class="item-slide p-1">
                          <figure class="card card-product">
                            <a href="{{ route('negocio', $value->slug) }}">
                              <div class="img-wrap"> <img class="rounded" src="{{ $value->logo ? Voyager::image($value->thumbnail('perfil', 'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}"> </div>
                              <h6 class="mitext text-center mititle mt-2 text-truncate">{{ $value->nombre }}</h6>
                            </a>
                            <a href="#" data-toggle="collapse" data-target="#collapse{{ $value->id }}" aria-expanded="true" class="mititle mt-2">
                              <i class="icon-action fa fa-chevron-down"></i>
                              <h6 class="title text-center">Leer mas</h6>
                            </a>
                          <div class="collapse text-center" id="collapse{{ $value->id }}" style="">
                              <h6>{{ $value->nombre }}</h6>
                              <small>{{ $value->descripcion }}</small>
                          </div>
                            <div class="rating-wrap text-center">
                              <ul class="rating-stars">
                                <li style="width:{{ $value->rating }}%" class="stars-active"> 
                                  <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </li>
                                <li>
                                  <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> 
                                </li>
                              </ul>
                              {{ $value->ordenes }}<i class="fa-solid fa-cart-arrow-down"></i>
                            </div>
                          </figure>
                        </div>   
                    @endforeach
                  </div>
                </div>
              </div>
          @endif
        @endforeach
      @endif
    </div>
  </div>

@endsection

@section('javascript')

<script type='text/javascript'>
$(document).ready(function () {
  var mimapa = localStorage.getItem('mimapa')
  localStorage.setItem('mivolver', '/')
  if (mimapa) {
    // console.log('ok')
  } else {
    console.log('not')    
    location.href = '/mapaset'
  }
  // var d=new Date();
  // console.log(d.getDay());
});

  $('.mifiltros').change(function () {
    if (this.value == 0) {
      location.href = "/"
    } else {
      location.href = "?tipo="+this.value
    }
  })
  $('milocalidades').change(function () {
    // misession = JSON.parse(localStorage.getItem('misession'))
    if (this.value == 0) {
      location.href = "/"
    } else {
      location.href = "?localidad="+this.value
    }
  })

  function mihome() {    
    localStorage.setItem('mivolver', "/")
    location.href = "/perfil"
  }
  function mimaps() {    
    localStorage.setItem('mivolver', "/")
    location.href = "/maps"
  }
  
  function nuevoviaje() {    
    localStorage.setItem('mivolver', "/")
    location.href = "/taxi/nuevo"
  }

  function mihelp() {    
    localStorage.setItem('mivolver', "/")
    location.href = "/help"
  }
</script>
@endsection