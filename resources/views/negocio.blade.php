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
		<nav class="navbar sticky-top navbar-light" style="background-color: #F0F0F4;">
			<a class="navbar-brand" href="/">
			  <img src="{{ $negocio->logo ? Voyager::image($negocio->Thumbnail('perfil' ,'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}" width="30" height="30" class="d-inline-block align-top rounded" alt="">
			  {{ $negocio->nombre }}
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
				<i class="fa fa-home"></i>
			  </button>
			  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
				<p class="text-center">{{ $negocio->descripcion }}</p>
				<form class="form-inline my-2 my-lg-0">
				  <input class="form-control mr-sm-1 mt-2" name="criterio" type="search" placeholder="Buscar producto" aria-label="Buscar" value="{{ $micriterio }}" required>
				  <button class="btn miboton" type="submit" hidden><i class="fa fa-search"></i></button>				  
				</form>
				<table class="table">
					<tr class="text-center">
						<td>
							<h2>{{ $negocio->estado ? "A B I E R T O" : "C E R R A D O"; }}</h2>
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
						</td>
					</tr>
					<tr class="text-center">
						<td><span>Atencion:</span> <h4>{{$negocio->horario}}</h4></td>
					</tr>
					<tr class="text-center">
						<td>
							<span>Direccion:</span>
							<h4>{{$negocio->direccion}}</h4>
							<a class="btn miboton" href="http://maps.google.com/maps?&z=15&mrt=yp&t=k&q={{ $negocio->latitud }}+{{ $negocio->longitud }}" rel="noopener noreferrer">Mapa</a>
						</td>
					</tr>
					<tr class="text-center">
						<td>
							<span>Compatir:</span>
							<div class="ss-box ss-circle text-center" data-ss-content="false"></div>
						</td>
					</tr>
					<tr id="panel_control" hidden>
						<td>
							<a href="#" onclick="resetear_pw()" class="btn btn-success">Panel del Negocio <i class="fa fa-sign-in"></i></a>
						</td>
					</tr>
				</table>
			  </div>
		</nav>	
		<aside class="mb-2">
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
				{{-- {{ count($negocio->banner) }} --}}
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

		@if (isset($_GET['criterio']))
			@if (count($productos) == 0)
				<h2 class="text-center mitext">sin resultados</h2>
			@else					
				@foreach ($productos as $item)				
					<div class="card mb-2">
						<a href="{{ route('producto', [$negocio->slug, $item->slug]) }}">
						<figure class="itemside">
							<div class="aside">
								<div class="img-wrap img-sm">
									<img src="{{ $item->image ? Voyager::image($item->thumbnail('cropped', 'image')) : 'storage/'.setting('productos.img_default_producto') }}">
								</div>
							</div>
							<figcaption class="p-1 align-self-center mititle">
								<h5 class="title text-truncate">{{ $item->nombre }}</h5>
								<p>{{ $item->detalle }}</p>
							</figcaption>
						</figure> 
						</a>
					</div>
				@endforeach
			@endif
		@else		
			@foreach ($categorias as $item)
				@php
					$miproductos = App\Producto::where('negocio_id', $negocio->id)->where('categoria_id', $item->id)->where('ecommerce', 1)->orderBy('updated_at', 'desc')->get();
				@endphp
				@if (count($miproductos) != 0)
				<h4 class="text-center mitext"><i class="fa-solid fa-angles-left"></i> {{ $item->nombre }} <i class="fa-solid fa-angles-right"></i></h4>
					<div class="slick-slider" data-slick='{"slidesToShow": 3, "slidesToScroll": 1}'>
						@foreach ($miproductos as $value)
							<div class="item-slide p-1">
								<figure class="card card-product">
								<a href="{{ route('producto', [$negocio->slug, $value->slug]) }}">
									<div class="img-wrap"> 
										@if ($value->nuevo)
											<span class="badge-new"> Nuevo </span>
										@endif
										@if ($value->endescuento)
											<span class="badge-offer"><b> - {{ $item->endescuento }}%</b></span>
										@endif	
										<img src="{{ $value->image ? Voyager::image($value->thumbnail('cropped', 'image')) : 'storage/'.setting('productos.img_default_producto') }}" alt="{{ $value->nombre }}">  
									</div>
									<h6 class="text-center mitext mt-2  text-truncate">{{ $value->nombre }}</h6>
								</a>
								<div class="rating-wrap">
									<ul class="rating-stars">
									<li style="width:{{ $value->rating }}%" class="stars-active"> 
										<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
									</li>
									<li>
										<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> 
									</li>
									</ul>
									{{-- {{ $value->ordenes }} <i class="fa-solid fa-filter" title="Categoria"></i> --}}
									<div class="label-rating">{{ $value->ordenes }}<i class="fa-solid fa-cart-arrow-down"></i></div>

								</div>
								
								<a href="#" data-toggle="collapse" data-target="#collapse{{ $value->id }}" aria-expanded="true" class="mititle mt-2">
									<i class="icon-action fa fa-chevron-down"></i>
									<h6 class="title">Leer mas </h6>
								</a>
								<div class="collapse text-center" id="collapse{{ $value->id }}" style="">
									{{ $value->detalle }}
								</div>
								</figure>
							</div>   
						@endforeach
					</div>   
				@endif
			@endforeach
		@endif
		@if (count($extras) != 0)
			<h4 class="text-center mitext"><i class="fa-solid fa-angles-left"></i> Extras <i class="fa-solid fa-angles-right"></i></h4>
			<div class="slick-slider" data-slick='{"slidesToShow": 3, "slidesToScroll": 1}'>
				@foreach ($extras as $item)
				<div class="item-slide p-1">
					<figure class="card card-product">
					<div class="img-wrap"> 
						<img src="{{ Voyager::image(setting('productos.img_default_producto')) }}" alt="{{ $item->nombre }}">  
					</div>
					<h6 class="text-center mitext mt-2 text-truncate">{{ $item->nombre }}</h6>
					<h6 class="text-center">Precio: {{ $item->precio }} Bs</h6>
					</figure>
				</div>   
				@endforeach
			</div>
		@endif
		<div class="fb-comments" data-href="{{ route('negocio', $negocio->slug) }}" data-width="100%" data-numposts="5"></div>			
	</div>

	{{-- -------------- UI DESSKTOP -------------- --}}
	{{-- {{ $categorias }} --}}
	<div class="container-fluid d-none d-sm-block mt-2">
		<nav class="navbar sticky-top navbar-light" style="background-color: #F0F0F4;">
			<a class="navbar-brand" href="/">
			  <img src="{{ $negocio->logo ? Voyager::image($negocio->Thumbnail('perfil' ,'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}" width="30" height="30" class="d-inline-block align-top rounded" alt="">
			  {{ $negocio->nombre }}
			</a>
			<form class="form-inline">
			  <select name="" id="" class="form-control mr-sm-1 mifiltros">
				<option value="0" @if($micategoria==0) selected @endif> Todos las categorias</option>
				@foreach ($categorias as $item)
					@if (count($item->productos) != 0)
						<option value="{{ $item->id }}"@if($micategoria==$item->id) selected @endif>{{ $item->nombre }} - {{ count($item->productos) }}</option>						
					@endif
				@endforeach
			  </select>
			  <input class="form-control" name="criterio" type="search" placeholder="Buscar" aria-label="Buscar" value="{{ $micriterio }}" required>
			  <button class="btn miboton" type="submit"><i class="fa fa-search"></i></button>
			</form>
		</nav>
		<aside class="mb-3">
			<div id="carousel1_indicator2" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators">
				<li data-target="#carousel1_indicator2" data-slide-to="0" class="active"></li>
				<li data-target="#carousel1_indicator2" data-slide-to="1"></li>
				<li data-target="#carousel1_indicator2" data-slide-to="2"></li>
			</ol>
			<div class="carousel-inner">			
		
				
				@if ($negocio->banner != null)
					@php
						$images = json_decode($negocio->banner);
					@endphp
					@foreach($images as $image)
						@if ($loop->first)
						<div class="carousel-item active">
							<img class="d-block w-100 rounded" src="{{  Voyager::image($negocio->getThumbnail($image ,'banner')) }}" alt="{{ $negocio->nombre }}"> 
						</div>
						@else
						<div class="carousel-item">
							<img class="d-block w-100 rounded" src="{{  Voyager::image($negocio->getThumbnail($image ,'banner')) }}" alt="{{ $negocio->nombre }}"> 
						</div>
						@endif															
					@endforeach	
				@endif
				
			</div>
			<a class="carousel-control-prev" href="#carousel1_indicator2" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next" href="#carousel1_indicator2" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
			</div> 	
		</aside>
		
		<div class="row no-gutters">
			<div class="col-sm-3">
				<div class="panel mr-sm-2">
					<img class="rounded" src="{{ $negocio->logo ? Voyager::image($negocio->Thumbnail('perfil' ,'logo')) : 'storage/'.setting('negocios.img_default_negocio') }}" width="100%">
					<iframe class="mt-2" width="100%" src="https://maps.google.com/maps?q={{$latitud}},{{$longitud}}&hl=es&z=14&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" height="200"></iframe>
					<table class="table">
						<tr class="text-center">
							<td>
								<b>{{ $negocio->estado ? "A B I E R T O" : "C E R R A D O"; }}</b><br>
								<ul class="rating-stars">
								<li style="width:{{ $negocio->rating }}%" class="stars-active"> 
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
							</td>
						</tr>
						<tr class="text-center">
							<td><span>Atencion:</span> <br> <b>{{$negocio->horario}}</b></td>
						</tr>
						<tr class="text-center">
							<td>
								<span>Compartir:</span> <br>
								<div class="ss-box ss-circle text-center" data-ss-content="false"></div>
							</td>
						</tr>
						<tr id="panel_control" hidden>
							<td>
								<span>Panel:</span> <br>
								<a href="#" onclick="resetear_pw()" class="btn btn-success">Panel del Negocio <i class="fa fa-sign-in"></i></a>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="col-sm-9">
				@if (count($productos) == 0)
					<h2 class="text-center mitext">sin resultados</h2>
				@else					
					@foreach ($productos as $item)
						<div class="card mb-2"> 
							<article class="itemlist">
								<a href="{{ route('producto', [$negocio->slug, $item->slug]) }}">
									<div class="row row-sm">
										<aside class="col-sm-3">
											@if ($item->nuevo)
												<span class="badge-new"> Nuevo </span>
											@endif
											@if ($item->endescuento)
												<span class="badge-offer"><b> - {{ $item->endescuento }}%</b></span>
											@endif										
											<div class="img-wrap">
												<img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : 'storage/'.setting('productos.img_default_producto') }}" class="img-md" alt="{{$item->nombre}}">
											</div>
										</aside>
										<div class="col-sm-6">
											<div class="text-wrap" style="color: #0C2746;">
												<h4 class="title"> {{ $item->nombre }}  </h4>
												<div class="rating-wrap">
													<ul class="rating-stars">
														<li style="width:{{ $item->rating }}%" class="stars-active"> 
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
													<div class="label-rating">{{ $item->ordenes }}<i class="fa-solid fa-cart-arrow-down"></i></div>
													<div class="label-rating">{{ $item->categoria->nombre }}<i class="fa-solid fa-filter"></i></div>
												</div>
												<p style="text-align: justify;"> {{ $item->detalle }} </p>
											</div>
										</div>
										<aside class="col-sm-3">
											<div class="border-left pl-2" style="color: #0C2746;">
												@if ($item->precio > 0)
													<h4 class="title">Precio Bs:</h4>
													<h4 class="text-center mitext">{{ number_format($item->precio, 2, ',', '.'); }}</h4>
												@else
													@php
													$rel=App\RelProductoPrecio::where('producto_id', $item->id)->get();
													@endphp 
													<h5 class="title">Precios Bs:</h5>
														@foreach ($rel as $item2)
														@php
															$precio_prod= App\Precio::find( $item2->precio_id);
														@endphp
														<p class="text-center mitext">{{ number_format($precio_prod->precio, 2, ',', '.').' ('.$precio_prod->nombre.')' }}</p>
														@endforeach
												@endif
											</div>
										</aside> 
									</div>
								</a> 
							</article>
						</div> 
					@endforeach
				@endif
				<div class="fb-comments" data-href="{{ route('negocio', $negocio->slug) }}" data-width="100%" data-numposts="5"></div>
				<footer class="text-center">
					<p class="mt-2">Marketplace by loginweb @2022</p>
				</footer>
			</div>
		</div>
	</div>
	@endsection

@section('javascript')
<script src="{{ asset('js/social-share.js') }}" crossorigin="anonymous"></script>
<script>
	$('.mifiltros').change(function () {
		misession = JSON.parse(localStorage.getItem('misession'))
		if (this.value == 0) {
			location.href = "/negocio/goshop"
		} else {
			location.href = "?categoria="+this.value
    	}
  	})
</script>
@endsection