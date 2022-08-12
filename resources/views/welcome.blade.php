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
{{-- <style>
  .column {
		float: left;
		width: 50%;
	}
	/* Clear floats after the columns */
	.row:after {
		content: "";
		display: table;
		clear: both;
	}
</style> --}}
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
    <nav class="navbar navbar-expand-sm sticky-top navbar-light justify-content-between" style="background-color: #F0F0F4;">
      <a class="navbar-brand" href="/">
        <img src="storage/{{ setting('site.logo') }}" width="30" height="30" class="d-inline-block align-top" alt="">
        {{ setting('site.title') }}
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-home"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <p class="text-center">{{ setting('site.description') }}</p>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-1" name="criterio" type="search" placeholder="Buscar negocio" aria-label="Buscar negocio">
          <button class="btn miboton" type="submit" hidden><i class="fa fa-search"></i></button>
        </form>
        <p class="text-center">Compartir:</p>
        <div class="ss-box ss-circle text-center" data-ss-content="false"></div>
        <div class="text-center mt-2">
          <a href="login" class="mititle text-center">
            <div class="icontext text-center mt-2">                
                <div class="icon-wrap icon-xs bg-secondary round text-light">
                  <i class="fa fa-user"></i>
                </div>
                <div class="text-wrap">
                  <div>Confimar tus credenciales</div>
                </div>
            </div>
          </a>
        </div>
      </div>      
    </nav>


        <aside class="mb-3">
          <div id="carousel1_indicator" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
              <li data-target="#carousel1_indicator" data-slide-to="0" class="active"></li>
              <li data-target="#carousel1_indicator" data-slide-to="1"></li>
              <li data-target="#carousel1_indicator" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/banner1.png" alt="First slide"> 
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/banner2.png" alt="Second slide">
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/banner3.png" alt="Third slide">
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
    @foreach ($tipos as $item)
      
        @php
          $minegocios = App\Negocio::where('estado', 1)->where('tipo_id', $item->id)->orderBy('order', 'asc')->with('poblacion', 'tipo', 'productos')->get();
        @endphp
        @if (count($minegocios) != 0)
        
        <h4 class="text-center mititle">{{ $item->nombre }}</h4>
        <div class="slick-slider" data-slick='{"slidesToShow": 3, "slidesToScroll": 1}'>
        @foreach ($minegocios as $value)
            <div class="item-slide p-1">
              <figure class="card card-product">
                <a href="{{ route('negocio', $value->slug) }}">
                  <div class="img-wrap"> <img src="{{ $value->logo ? 'storage/'.$value->logo : 'storage/'.setting('negocios.img_default_negocio') }}"> </div>
                  <h6 class="title text-center mitext mt-2 text-truncate">{{ $value->nombre }}</h6>
                </a>
                <a href="#" data-toggle="collapse" data-target="#collapse{{ $value->id }}" aria-expanded="true" class="mititle mt-2">
                  <i class="icon-action fa fa-chevron-down"></i>
                  <h6 class="title">Leer mas </h6>
                </a>
              <div class="collapse text-center" id="collapse{{ $value->id }}" style="">
                  <small>{{ $value->descripcion }}</small>
              </div>
                <div class="rating-wrap">
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
      @endif
    @endforeach
    {{-- <div class="fb-page" data-href="https://www.facebook.com/Appxinet-106265762190084" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/Appxinet-106265762190084" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/Appxinet-106265762190084">Appxi.net</a></blockquote></div> --}}

  </div>

  {{-- -------------- UI DESSKTOP -------------- --}}
  <div class="container-fluid d-none d-sm-block mt-2">
    <nav class="navbar navbar-expand-sm sticky-top navbar-light justify-content-between" style="background-color: #F0F0F4;">
      <a class="navbar-brand" href="/">
        <img src="storage/{{ setting('site.logo') }}" width="30" height="30" class="d-inline-block align-top" alt="">
        {{ setting('site.title') }}
      </a>
      <form class="form-inline">
        {{-- <input name="localidad" id="localidad2" type="text" hidden> --}}
        
        <select name="" id="milocalidades" class="form-control mr-sm-1">
          <option value="0" @if($milocalidad==0) selected @endif> Filtro por localidad </option>
          @foreach ($localidades as $item)
            <option value="{{ $item->id }}" @if($milocalidad==$item->id) selected @endif>{{ $item->nombre }}</option>
          @endforeach
        </select>

        <select class="form-control mr-sm-1 mifiltros">
          <option value="0" @if($mitipo==0) selected @endif> Filtro por categorias </option>
          @foreach ($tipos as $item)
            <option value="{{ $item->id }}" @if($mitipo==$item->id) selected @endif>{{ $item->nombre }}</option>
          @endforeach
        </select>
        <input class="form-control mr-sm-1" name="criterio" type="search" placeholder="Buscar negocio" aria-label="Buscar" value="{{ $micriterio }}">
        {{-- <button class="btn miboton" type="submit"><i class="fa fa-search"></i></button> --}}
      </form>
    </nav>

    <div class="row no-gutters">      
      <div class="col-sm-3 mt-2">
        <div class="panel mr-sm-2">  
        <aside class="mb-3">
          <div id="carousel1_indicator" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
              <li data-target="#carousel1_indicator" data-slide-to="0" class="active"></li>
              <li data-target="#carousel1_indicator" data-slide-to="1"></li>
              <li data-target="#carousel1_indicator" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/banner1.png" alt="First slide"> 
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/banner2.png" alt="Second slide">
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="https://appxi.net//storage/landinpage/banner3.png" alt="Third slide">
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
        </aside> <!-- col.// -->
       


          {{-- <article class="box">
            <a href="login" class="mititle">
            <div class="icontext">
                
                <div class="icon-wrap icon-xs bg-secondary round text-light">
                  <i class="fa fa-user"></i>
                </div>
                <div class="text-wrap">
                  <div>Usuario Invitado</div>
                </div>              
            </div>
          </a>
          </article> --}}
          



          @if (setting('site.live'))
            <h4 class="title" style="color: #38A54A">Live</h4>
            <video id="videoElement" class="embed-responsive embed-responsive-21by9"></video>
          @else          
            <video class="mr-sm-2" id="myVideo" controls width="100%">
              <source id="mp4Source" src="https://www.appxi.net//storage/videos/video1.mp4" type="video/mp4">
            </video>                            
          @endif
          {{-- <div class="fb-page" data-href="https://www.facebook.com/Appxinet-106265762190084" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/Appxinet-106265762190084" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/Appxinet-106265762190084">Appxi.net</a></blockquote></div> --}}
   
          <div class="ss-box ss-circle text-center" data-ss-content="false"></div>
        </div>
      </div>

      <div class="col-sm-9">
        @if (count($negocios) == 0)
            <h1 class="text-center mitext">sin resultados</h1>
        @else            
          @foreach ($negocios as $item)
            @php
                $miestado = $item->estado ? 'Abierto' : 'Cerrado';
            @endphp         
            {{-- <a href="{{ route('negocio', $item->slug) }}">                     --}}
              <article class="card p-1 mt-1">
                {{-- <div class="card-body"> --}}
                  <div class="row">
                    <aside class="col-sm-3">
                      <div class="img-wrap"><img src="{{ $item->logo ? 'storage/'.$item->logo : 'storage/'.setting('negocios.img_default_negocio') }}"></div>
                    </aside>
                    <div class="col-sm-6" style="color: #0C2746">
                      <a href="{{ route('negocio', $item->slug) }}" class="mititle">  
                        <h4 class="title">{{ $item->nombre }}</h4>
                        <div class="rating-wrap mb-2">
                          <ul class="rating-stars">
                            <li style="width: {{ $item->rating }}" class="stars-active"> 
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
                          <div class="label-rating"><i class="fa-solid fa-door-open"></i> {{ $miestado }}</div>
                          <div class="label-rating"><i class="fa-solid fa-boxes-stacked"></i> {{ count($item->productos) }} Productos</div>
                          <div class="label-rating"><i class="fa-brands fa-whatsapp"></i> {{ $item->telefono }}</div>
                          {{-- <div class="label-rating"><i class="fa-solid fa-business-time"></i> {{ $item->horario }}</div> --}}
                        </div>
                      </a>
                        {{-- <p style="font-size: 18px;"> {{ $item->direccion }} </p> --}}
                      <a href="#" data-toggle="collapse" data-target="#collapse2{{ $item->id }}" aria-expanded="true" class="mititle mt-2">
                          <i class="icon-action fa fa-chevron-down"></i>
                          <h5 class="title">Leer mas </h5>
                      </a>
                      <div class="collapse text-center" id="collapse2{{ $item->id }}" style="">
                          <p><i class="fa-solid fa-circle-info"></i> {{ $item->descripcion }}</p>
                          <p><i class="fa-solid fa-business-time"></i> {{ $item->horario }}</p>
                      </div>
                    </div>
                    
                    <aside class="col-sm-3 border-left" style="color: #0C2746">
                      <div class="action-wrap">
                        <p class="">
                          <i class="fa-solid fa-filter"></i> <span> {{ $item->tipo->nombre }} </span><br>
                          <i class="fa-solid fa-location-dot"></i> <span>{{ $item->poblacion->nombre }}</span> <br>
                          
                          {{-- <i class="fa-solid fa-clock"></i> <span> {{ $item->horario }} </span><br> --}}
                          {{-- <a href="/negocio/{{$item->slug }}" class="btn miboton"> Ver Tienda </a> --}}
                        </p>                      
                      </div>
                    </aside>
                  </div>
                {{-- </div> --}}
              </article>
            {{-- </a> --}}
          @endforeach
        @endif
      </div>
    </div>
    <footer class="panel text-center">
      <p class="mt-2">Marketplace by loginweb @2022</p>
    </footer>
  </div>
@endsection

@section('javascript')
<script src="{{ asset('js/social-share.js') }}" crossorigin="anonymous"></script>
{{-- <script src='https://meet.jit.si/external_api.js'></script> --}}
  {{-- <script src="https://cdn.bootcss.com/flv.js/1.5.0/flv.min.js"></script> --}}
  {{-- <script>
      const domainServer = "meet.jit.si";
      const roomName = "mireunion";
      const options = {
          roomName: roomName,
          height: 500,
          parentNode: document.querySelector('#meet'),
          devices: {
              audioInput: '<deviceLabel>',
              audioOutput: '<deviceLabel>',
              videoInput: '<deviceLabel>'
          },
          interfaceConfigOverwrite: {
              TOOLBAR_BUTTONS: [
                  'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                  'fodeviceselection', 'profile', 'etherpad', 'settings', 'hangup',
                  'videoquality', 'filmstrip', 'feedback', 'stats', 'shortcuts',
                  'tileview', 'download', 'help', 'mute-everyone', 'e2ee', 'security',
                  'chat',
                  'raisehand',
              ],
              SHOW_JITSI_WATERMARK: false
          }
      };
      const api = new JitsiMeetExternalAPI(domainServer, options);
      // Video conferencia clinte/médico inicada
      api.addEventListener('participantJoined', res => {
      })
      // Finalizar la video conferencia
      api.addEventListener('videoConferenceLeft', res => {
      });
  </script> --}}

  @if (setting('site.live'))
    <script>
      if (flvjs.isSupported()) {
          var videoElement = document.getElementById('videoElement');
          var flvPlayer = flvjs.createPlayer({
              type: 'flv',
              url: 'https://live.appxi.net/live/marketplace.flv'
          });
          flvPlayer.attachMediaElement(videoElement);
          flvPlayer.load();
          flvPlayer.play();
      }
  </script>
  @endif


<script type='text/javascript'>
  var index=1;
  var count=7 // replace it with whatever number last video has. 
  var player=document.getElementById('myVideo');
  var mp4Vid = document.getElementById('mp4Source');
  player.addEventListener('ended',myHandler,false);
  // console.log(mp4Vid)
  function myHandler(e)
  {
     // How to Looping video play list -----
     console.log(index)
     index++;
     if (index > count) index = 1;
     $(mp4Vid).attr('src', "https://www.appxi.net//storage/videos/video"+index+".mp4");
     player.load();
     player.play();
  }

  const tabs= document.querySelectorAll(".tab");
  tabs.forEach((clickedTab)=>{
      clickedTab.addEventListener('click',()=>{
          tabs.forEach((tab=>{
              tab.classList.remove("active");
          }))
          clickedTab.classList.add("active");
          const clickedTabBGColor=getComputedStyle
          (clickedTab).getPropertyValue(
              "color"
          );
          // document.body.style.background=clickedTabBGColor;
      });
  });

  $('.mifiltros').change(function () {
    // misession = JSON.parse(localStorage.getItem('misession'))
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
</script>
@endsection