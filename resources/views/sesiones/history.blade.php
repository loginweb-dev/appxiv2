
	{{-- <div class="text-center mititle m-2 p-2">

		<h4>Nombre: {{ $micliente->nombre }}</h4>


	</div> --}}
{{-- {{ $micliente }} --}}
{{-- <div class="">
@foreach ($micliente->pedidos as $item)				
<div class="card m-1">
	<a href="#">
	<figure class="itemside">
		<div class="aside">
			<div class="img-wrap img-sm">
				<img src="https://appxi.net//storage/landinpage/cart-empty.png">
			</div>
		</div>
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
	</a>
</div>
@endforeach
</div> --}}


<div class="list-group">
	<article class="list-group-item">
		<header class="filter-header">
			<a href="#" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" class="">
				<i class="icon-action fa fa-chevron-down"></i>
				<h5 class="title mititle">Datos Personales </h5>
			</a>
		</header>
		<div class="filter-content collapse show" id="collapse1" style="">			
			<p class="mititle">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt deserunt mollit anim id est laborum.	</p>
		</div> <!-- collapse -filter-content  .// -->
	</article>
	<article class="list-group-item">
		<header class="filter-header">
			<a href="#" data-toggle="collapse" data-target="#collapse2">
				<i class="icon-action fa fa-chevron-down"></i>
				<h5 class="title mititle"> Pedidos </h5>
			</a>
		</header>
		<div class="filter-content collapse" id="collapse2">
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
				tempor incididunt deserunt mollit anim id est laborum.	</p>
		</div>
	</article>
</div> <!-- list-group.// -->
