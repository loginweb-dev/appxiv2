@extends('master')

@section('content')
<div class="d-block d-sm-none">
	<nav class="navbar navbar-expand-sm sticky-top navbar-light justify-content-between" style="background-color: #F0F0F4;">
		<a class="navbar-brand" href="/">
		<img src="{{ Voyager::image(setting('site.logo')) }}" width="30" height="30" class="d-inline-block align-top" alt="">
		{{ setting('site.title') }}
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
			  {{-- <li class="list-group-item">
				<a class="mititle" href="/perfil"> <i class="fa-solid fa-address-card"></i> Mi Perfil </a>
			  </li> --}}
				<li class="list-group-item">
					<a href="{{ route('milogout') }}" class="btn miboton">Salir</a>
				</li>
			  

			</ul>
		  </div>  

	</nav>
	{{-- <h1>Perfil</h1> --}}
	{{-- <p>{{ $micliente->nombre }}</p> --}}
	<div class="text-center mititle m-2 p-2">

					<h4>Nombre: {{ $micliente->nombre }}</h4>
		
		
	</div>
	{{-- {{ $micliente }} --}}
	<div class="">
		@foreach ($micliente->pedidos as $item)				
			<div class="card m-1">
				{{-- <a href="#"> --}}
				<figure class="itemside">
					{{-- <div class="aside">
						<div class="img-wrap img-sm">
							<img src="https://appxi.net//storage/landinpage/cart-empty.png">
						</div>
					</div> --}}
					<figcaption class="m-1 border-left p-1">
						<h6>DETALLE</h6>
						<p>Pedido {{ '#'.$item->id.' - '.$item->published }}</p>
						<p>Estado: {{ $item->estado->nombre }}</p>
						<p>Pago: {{ $item->pasarela->title }}</p>
						<p>Productos: {{ $item->total }}Bs.</p>
						<p>Delivery: {{ $item->total_delivery }}Bs.</p>
		
					</figcaption>
					<div class="m-1 border-left p-1">
						<h6>PRODUCTOS</h6>
							@php
								$detalles = App\PedidoDetalle::where('pedido_id', $item->id)->get();
							@endphp
							@foreach ($detalles as $item2)
								@php
									$extras = App\Extrapedido::where('pedido_id', $item2->id)->get();
								@endphp
								<p>{{ $item2->producto_name }}</p>
								<ul>
									@foreach ($extras as $item3)
										@php
											$extra = App\Extraproducto::find($item3->extra_id);
										@endphp
											<li>{{ $extra->nombre }}</li>
									@endforeach
								</ul>
							@endforeach
						
					</div>
				</figure> 
				{{-- </a> --}}
			</div>
		@endforeach
	</div>
	{{-- <div class="text-center m-2">
		<a href="{{ route('milogout') }}" class="btn miboton">Salir</a>
	</div> --}}
	
</div>
@endsection