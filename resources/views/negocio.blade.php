@extends('master')

@section('meta')
<title>{{ $negocio->nombre }}</title>
<meta name="description" content="{{ $negocio->descripcion }}" />
<meta property="og:site_name" content="{{ $negocio->nombre }}">
<meta property="og:title" content="{{ $negocio->nombre }}" />
<meta property="og:description" content="{{ $negocio->descripcion }}" />
<meta property="og:image" itemprop="image" content="{{ $negocio->logo ? Voyager::image($negocio->Thumbnail('perfil' ,'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}">
<meta property="og:type" content="website" />
<meta property="og:updated_time" content="1440432930" />
@endsection
@section('css')
<link href="{{ $negocio->logo ? Voyager::image($negocio->Thumbnail('perfil' ,'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}" rel="shortcut icon" type="image/x-icon">
<style>
.modal-dialog {
  position: fixed;
  bottom: 0px;
  left: 0%;
  right: 0%;
  /* transform: translate(-150%, -150%); */
} 
#map {
		width: 100%;
		height: 200px;
	}

.modal-content { 
    -webkit-border-radius: 20px;
}
</style>
@endsection
@section('content')
	@php
		$image=$negocio->logo ? $negocio->logo : setting('negocios.img_default_negocio');
		$latitud=$negocio->latitud ? $negocio->latitud : '-15.2411217' ;
		$longitud=$negocio->longitud ? $negocio->longitud : '-63.8812874';
		$productos_varios= App\Producto::where('negocio_id', $negocio->id )->where('ecommerce', 1)->orderBy('id', 'desc')->with('categoria','negocio')->limit(10)->get();
		$categorias = App\Categoria::where('tipo_id', $negocio->tipo_id)->with('productos')->get();
		$extras = App\Extraproducto::where('negocio_id', $negocio->id)->orderBy('updated_at', 'desc')->get();
		if (isset($_GET['categoria'])){
			$micategoria = $_GET['categoria'];
			$micriterio = '';
			$productos = App\Producto::where('negocio_id', $negocio->id)->where('ecommerce', 1)->where('categoria_id',$micategoria)->orderBy('updated_at', 'desc')->with('categoria','negocio')->get();
			
		}else if (isset($_GET['criterio'])){
			$micriterio = $_GET['criterio'];
			$micategoria = 0;
			$productos = App\Producto::where('nombre', 'LIKE', '%'.$_GET['criterio'].'%')->where('negocio_id', $negocio->id)->where('ecommerce', 1)->orderBy('updated_at', 'desc')->with('categoria','negocio')->get();
		}else{
			$productos = App\Producto::where('negocio_id', $negocio->id)->where('ecommerce', 1)->orderBy('updated_at', 'desc')->with('categoria','negocio')->limit(8)->get();
			$micategoria = 0;
			$micriterio = '';
		}
	@endphp

	{{-- -------------- UI MOVIL -------------- --}}
	<div class="d-block d-sm-none">
		<nav class="navbar sticky-top navbar-light justify-content-center" style="background-color: #F0F0F4;">
			<a class="navbar-brand text-truncate" href="#">
			  <img src="{{ $negocio->logo ? Voyager::image($negocio->Thumbnail('perfil' ,'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}" width="30" height="30" class="d-inline-block align-top rounded" alt="{{ $negocio->nombre }}">
			  {{ $negocio->nombre }}
			</a>
			<div id="miback">
				<a href="#" onclick="mivolver()" class="mititle"><i class="fa-solid fa-circle-left fa-2xl"></i></a>
			</div>  
			<div id="miinfo">
				<a href="#" onclick="miinfo()" class="mititle"><i class="fa-solid fa-circle-info fa-2xl"></i></a>
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

		{{-- section banner --}}
		<aside class="">
			<div id="carousel1_indicator" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators">
				<li data-target="#carousel1_indicator" data-slide-to="0" class="active"></li>
				<li data-target="#carousel1_indicator" data-slide-to="1"></li>
				<li data-target="#carousel1_indicator" data-slide-to="2"></li>
			</ol>
			<div class="carousel-inner">			
				@php
					$images = json_decode($negocio->banner);
				@endphp
				@if ($negocio->banner != null)
					@foreach($images as $image)
						@if ($loop->first)
						<div class="carousel-item active">
							<img class="d-block w-100" src="{{  Voyager::image($negocio->getThumbnail($image ,'banner')) }}" alt=""> 
						</div>
						@else
						<div class="carousel-item">
							<img class="d-block w-100" src="{{  Voyager::image($negocio->getThumbnail($image ,'banner')) }}" alt=""> 
						</div>
						@endif															
					@endforeach			
				@endif
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

		{{-- section search  --}}
		<div style="background-color: #F0F0F4; padding: 10px;">
			<h2 class="text-center">{{ $negocio->estado ? "A B I E R T O" : "C E R R A D O"; }}</h2>
			<div class="text-center">
				<ul class="rating-stars">
					<li style="width: {{ $negocio->rating }}%" class="stars-active"> 
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
				<br>
				{{ $negocio->tipo->nombre }}
			</div>

			<form class="form-inline my-2 my-lg-0">
			  <input class="form-control mr-sm-1 miinput" name="criterio" type="search" placeholder="Buscar producto" aria-label="Buscar producto" value="{{ $micriterio }}">
			  <button class="btn miboton" type="submit" hidden><i class="fa fa-search"></i></button>
			</form>
			<div class="text-center">
				<p class="text-center">{{ $negocio->descripcion }}</p>
			</div>      
		</div>

		@if (isset($_GET['criterio']))
			@if (count($productos) == 0)
				<h2 class="text-center mitext">sin resultados</h2>
			@else					
				@foreach ($productos as $item)				
					<div class="card m-1">
						<a href="{{ route('producto', [$negocio->slug, $item->slug]) }}">
						<figure class="itemside">
							<div class="aside">
								<div class="img-wrap img-sm">
									<img src="{{ $item->image ? Voyager::image($item->thumbnail('cropped', 'image')) : Voyager::image($negocio->thumbnail('perfil', 'logo')) }}">
								</div>
							</div>
							<figcaption class="p-1 align-self-center mititle">
								<h6 class="title text-truncate text-center">{{ $item->nombre }}</h6>
								@if ($item->precio > 0)
									<p>{{ number_format($item->precio, 2, ',', '.') }} Bs.</p>
								@else
									@php
										$rel=App\RelProductoPrecio::where('producto_id', $item->id)->get();                          
									@endphp 
									<select id="miprecios" class="form-control">
										@foreach ($rel as $item2)
											@php
												$precio = App\Precio::find($item2->precio_id);
											@endphp
											<option value="{{ $precio->precio }}">{{ $precio->nombre.' '.$precio->precio }}Bs</option>
										@endforeach
									</select>
								@endif
								<p>{{ $item->detalle }}</p>
							</figcaption>
						</figure> 
						</a>
					</div>
				@endforeach
			@endif
			{{-- section volver --}}
			<div class="text-center mt-2">
				<a class="btn miboton" href="{{ route('negocio', $negocio->slug) }}">
					<i class="fa-solid fa-rotate-left"></i> Volver
				</a>
			</div>			
		@else		
			
			{{-- section geoinfo --}}
			<div class="micontent p-2 m-2 text-center">
				<h6><div id="miubicacion"></div></h6>
				<nav class="justify-content-center">
					<div class="nav nav-tabs text-center" id="nav-tab" role="tablist">
					<a class="nav-item nav-link active mititle" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><i class="fa-solid fa-truck"></i> Delivery</a>
					<a class="nav-item nav-link mititle" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><i class="fa-solid fa-person-running"></i> Retiro</a>
					<a class="nav-item nav-link mititle" id="nav-profile-tab" data-toggle="tab" href="#nav-message" role="tab" aria-controls="nav-profile" aria-selected="false"><i class="fa-brands fa-whatsapp"></i> Mensaje</a>
					</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
					<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
						<div class="p-2">				
							<table width="100%">
								<tr>
									<td class="text-center">Tiempo: <br> <div id="tiempo"></div> </td>
									<td class="text-center">Distancia: <br><div id="distancia"></div> </td>
									<td class="text-center">Envio: <br> <div id="envio"></div></td>
								</tr>

							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
						<div class="p-2">				
							<table width="100%">
								<tr>
									<td class="text-center">Tiempo: <br> <div id="rtiempo"></div></td>
									<td class="text-center">Distancia: <br> <div id="rdistancia"></div></td>
									{{-- <td class="text-center">Envio: <br> 4 Bs.</td> --}}
								</tr>

							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="nav-message" role="tabpanel" aria-labelledby="nav-profile-tab">
						<div class="p-1 m-2">
							<a href="#" onclick="michat()" class="btn miboton btn-block"><i class="fa-solid fa-comment-dots"></i> Iniciar Chat</a>
						</div>
					</div>
				</div>			  
			</div>

			{{-- section body principal --}}
			@switch($negocio->tipo_id)
				@case(2)
					@foreach ($productos as $item)				
						<div class="card m-1">
							<a href="{{ route('producto', [$negocio->slug, $item->slug]) }}">
							<figure class="itemside">
								<div class="aside">
									<div class="img-wrap img-sm">
										
										<img src="{{ $item->image ? Voyager::image($item->thumbnail('cropped', 'image')) : Voyager::image($negocio->thumbnail('perfil', 'logo')) }}">
									</div>
								</div>
								<figcaption class="p-1 align-self-center mititle">
									<h6 class="title text-truncate text-center">{{ $item->nombre }}</h6>
									<p>{{ $item->detalle }}</p>
									<p>Precio: {{ $item->precio }} Bs.</p>
								</figcaption>
							</figure> 
							</a>
						</div>
					@endforeach
					@break
				@default						
				@foreach ($categorias as $item)
					@php
						$miproductos = App\Producto::where('negocio_id', $negocio->id)->where('categoria_id', $item->id)->where('ecommerce', 1)->orderBy('updated_at', 'desc')->get();
					@endphp
					@if (count($miproductos) != 0)
						<h4 class="text-center mitext m-2"><i class="fa-solid fa-filter"></i> {{ $item->nombre }}</h4>
						<div class="container-fluid">
							<div class="col-sm-12">							
								<div class="slick-slider" data-slick='{"slidesToShow": 2, "slidesToScroll": 2}'>
									@foreach ($miproductos as $value)
										<div class="item-slide p-1">
											<figure class="card card-product">
												<a href="{{ route('producto', [$negocio->slug, $value->slug]) }}">
													<div class="img-wrap"> 
														@if ($value->nuevo)
															<span class="badge-new"> Nuevo </span>
														@endif
														@if ($value->endescuento)
															<span class="badge-offer"><b> - {{ $value->endescuento }}%</b></span>
														@endif
														<img src="{{ $value->image ? Voyager::image($value->thumbnail('cropped', 'image')) : Voyager::image($negocio->thumbnail('perfil', 'logo')) }}" alt="{{ $value->nombre }}">  
													</div>
													<h5 class="text-center mitext mt-2  text-truncate">{{ $value->nombre }}</h5>
												</a>
												@if ($value->precio > 0)
													<h5 class="mitext text-center">{{ number_format($value->precio, 2, ',', '.') }} Bs.</h5>
												@else
													@php
														$rel=App\RelProductoPrecio::where('producto_id', $value->id)->get();                          
													@endphp 
													<select id="miprecios" class="form-control">
														@foreach ($rel as $item)
															@php
																$precio = App\Precio::find($item->precio_id);
															@endphp
															<option value="{{ $precio->precio }}">{{ $precio->nombre.' - '.$precio->precio }}Bs</option>
														@endforeach
													</select>
												@endif
												<div class="rating-wrap text-center">
													<ul class="rating-stars">
													<li style="width:{{ $value->rating }}%" class="stars-active"> 
														<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
													</li>
													<li>
														<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> 
													</li>
													</ul>
													<div class="label-rating">{{ $value->ordenes }}<i class="fa-solid fa-cart-arrow-down"></i></div>

												</div>											
												<a href="#" data-toggle="collapse" data-target="#collapse{{ $value->id }}" aria-expanded="true" class="mititle mt-2 text-center">
													<i class="icon-action fa fa-chevron-down"></i>
													<h6 class="title">Leer mas </h6>
												</a>
												<div class="collapse text-center" id="collapse{{ $value->id }}" style="">
													<p class="text-center">{{ $value->nombre }}</p>
													{{ $value->detalle }}
												</div>
											</figure>
										</div>   
									@endforeach
								</div>
							</div>
						</div>   
					@endif
				@endforeach
			@endswitch	
		@endif
		
	</div>


  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title mititle" id="exampleModalLabel"><img src="{{ $negocio->logo ? Voyager::image($negocio->Thumbnail('perfil' ,'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}" width="30" height="30" class="d-inline-block align-top rounded" alt="{{ $negocio->nombre }}"> Sobre Nosotros</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<i class="fa-solid fa-circle-xmark"></i>
		  </button>
		</div>
		<div class="m-2">
			<div id="map"></div>
		</div>
		
		<div class="m-1 p-1">
			<strong>Direccion:</strong>
			<p>{{ $negocio->direccion }}</p>
			<hr>
			<strong>Acerca de:</strong>
			<p>{{ $negocio->descripcion }}</p>
			<hr>
			<strong>Horario de Atencion:</strong>
			<p>{{ $negocio->horario }}</p>
		</div>
	  </div>
	</div>
  </div>
  {{-- <div id="map"></div> --}}
	@endsection

@section('javascript')
<script>
	// var map = null
	$(document).ready(function () {
		var myLatLng = { lat: parseFloat("{{ $negocio->latitud }}"), lng: parseFloat("{{ $negocio->longitud }}") }
		map = new google.maps.Map(document.getElementById("map"), {
			center: myLatLng,
			mapTypeId: "terrain",
			zoom: 15,
			// disableDefaultUI: true,
			mapTypeControl: false
		});

		var marker = new google.maps.Marker({
			animation: google.maps.Animation.DROP,
			draggable: false,
			position: myLatLng,
			map: map,
			icon: "{{ Voyager::image($negocio->Thumbnail('icon' ,'logo')) }}"
		});
		// $("#mimap").height($(document).height()-500)
		// var myLatLng = { lat: parseFloat(data.latitud), lng: parseFloat(data.longitud) }
        // console.log(data.latitud)
		var miorigen = JSON.parse(localStorage.getItem("mimapa"))
        var viaje = {
            origin: { lat: parseFloat(miorigen.latitud), lng: parseFloat(miorigen.longitud) },
            destination: { lat: parseFloat("{{ $negocio->latitud }}"), lng: parseFloat("{{ $negocio->longitud }}") },
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };
        var directionsDisplay = new google.maps.DirectionsRenderer();
        var directionsService = new google.maps.DirectionsService();
        directionsService.route(viaje, async function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
					$("#miubicacion").html(miorigen.direcion+'<br>'+miorigen.referencia)
					$("#distancia").html(response.routes[0].legs[0].distance.text)
					$("#tiempo").html(response.routes[0].legs[0].duration.text)
					$("#envio").html(micalculo(response.routes[0].legs[0].distance.value)+' Bs')
					// console.log(response.routes[0].legs[0].distance.value)
            }
        });


		var viaje2 = {
            origin: { lat: parseFloat(miorigen.latitud), lng: parseFloat(miorigen.longitud) },
            destination: { lat: parseFloat("{{ $negocio->latitud }}"), lng: parseFloat("{{ $negocio->longitud }}") },
            travelMode: google.maps.DirectionsTravelMode.WALKING
        };

		directionsService.route(viaje2, async function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
				$("#rtiempo").html(response.routes[0].legs[0].duration.text)
					// console.log(response.routes[0].legs[0].distance.value)
				$("#rdistancia").html(response.routes[0].legs[0].distance.text)
            }
        });

	});


	function micalculo(mivalue) {
		var km = mivalue / 1000
		if (km < 3) {
			return 4
		} else if(km < 5) {
			return 6
		}else if(km < 7){
			return 8
		}else{
			return 12
		}
	}
	function miinfo() {
		$("#exampleModal").modal()
	}

	$('.mifiltros').change(function () {
		misession = JSON.parse(localStorage.getItem('misession'))
		if (this.value == 0) {
			location.href = "/negocio/goshop"
		} else {
			location.href = "?categoria="+this.value
    	}
  	})
	function mivolver() {
		location.href = "/"
	}
	function michat() {
		@if(Auth::user())
			localStorage.setItem("mivolver", "{{ route('negocio', $negocio->slug) }}")
			location.href = "/help?negocio={{ $negocio->slug }}"
		@else
			$("#milogin").modal()
		@endif
	}
</script>
@endsection