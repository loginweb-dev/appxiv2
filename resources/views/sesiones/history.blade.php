<style>
	#map {
		width: 100%;
		height: 200px;
	}
</style>
@if (Auth::user())
	@php
		$cliente = App\Cliente::where('user_id', Auth::user()->id)->with('localidad')->first();
		$micarrito = App\Carrito::where('chatbot_id', $cliente->chatbot_id)->with('extras')->get();      
		$pedidos = App\Pedido::where('chatbot_id', $cliente->chatbot_id)->with('extras', 'estado', 'mensajero')->orderBy('created_at', 'desc')->limit(6)->get();             
	@endphp 
@endif
<div class="list-group">
	<article class="list-group-item">
		<header class="filter-header">
			<a href="#" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" class="">
				<i class="icon-action fa fa-chevron-down"></i>
				<h5 class="title mititle"><i class="fa-solid fa-user"></i> Datos Personales </h5>
			</a>
		</header>
		<div class="filter-content collapse show" id="collapse1" style="">			
			<table width="100%" border="0">
				<tr>
					<td class="text-left">
						<strong>Nombre: </strong>								
					</td>
					<td class="text-left">
						{{ $cliente->nombre }}
					</td>
				</tr>
				<tr>
					<td class="text-left">
						<strong>Whatsapp: </strong>							
					</td>
					<td class="text-left">
						{{ $cliente->chatbot_id }}
					</td>
				</tr>
				<tr>
					<td class="text-left">
						<strong>Localidad: </strong>							
					</td>
					<td class="text-left">
						{{ $cliente->localidad->nombre }}
					</td>
				</tr>
				<tr>
					<td class="text-left">
						<strong>Direccion: </strong>							
					</td>
					<td class="text-left">
						<div id="direccion"></div>
					</td>
				</tr>
				<tr>
					<td class="text-left">
						<strong>Referencia: </strong>							
					</td>
					<td class="text-left">
						<div id="referencia"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="map"></div>	
					</td>
				</tr>
			</table>
			<div class="text-center">
				<small class="text-center">Cierra sesion para editar los datos</small>
			</div>
			
		</div>
	</article>
	<article class="list-group-item">
		<header class="filter-header">
			<a href="#" data-toggle="collapse" data-target="#collapse2">
				<i class="icon-action fa fa-chevron-down"></i>
				<h5 class="title mititle"><i class="fa-solid fa-taxi"></i> Viajes </h5>
			</a>
		</header>
		<div class="filter-content collapse" id="collapse2">
			<h4 class="mitext"> en desarrollo </h4>
		</div>
	</article>
	<article class="list-group-item">
		<header class="filter-header">
			<a href="#" data-toggle="collapse" data-target="#collapse3">
				<i class="icon-action fa fa-chevron-down"></i>
				<h5 class="title mititle"><i class="fa-solid fa-truck"></i> Pedidos </h5>
			</a>
		</header>
		<div class="filter-content collapse" id="collapse3">
			{{-- {{$micliente->pedidos }} --}}
			<div class="table-responsive">
				<table class="table">
					<thead>
						{{-- <th>#</th> --}}
						<th>Fecha</th>
						{{-- <th>Mensaje</th> --}}
						{{-- <th>Estado</th> --}}
						<th>Delivery</th>
						<th>Total</th>
						{{-- <th>Mensaje</th> --}}
						<th>Productos</th>
					</thead>
					<tbody>
						@foreach ($pedidos as $item)		
						<tr>
							{{-- <td>
								{{ $item->id }}
							</td> --}}
							<td>
								{{ $item->id }}
								<br>
								{{ $item->published }}
							</td>
							{{-- <td>
								{{ $item->mensaje }}
							</td> --}}
							{{-- <td>
								{{ $item->estado->nombre }}
					
							</td> --}}
							<td>								
								{{ $item->mensajero->nombre }}
								<br>
								{{ $item->mensaje }}
							</td>
							<td>
								{{ $item->total }} Bs.
								<br>
								{{ $item->estado->nombre }}
							</td>
						
							<td>
								{{-- <a href="#" class="btn miboton">Productos</a> --}}
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
							</td>
						</tr>
						@endforeach
					</tbody>

				</table>	
			</div>	
				{{-- <div class="card m-1">
					<a href="#" class="mititle">
						<figure class="itemside">
						<figcaption class="m-1 p-1">
							<h6>DETALLE</h6>
							Pedido {{ '#'.$item->id.' - '.$item->published }}
							Estado: {{ $item->estado->nombre }}
							Pago: {{ $item->pasarela->title }}</
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
				</div> --}}
			
		</div>
	</article>
</div> 

<script>
		var miorigen = JSON.parse(localStorage.getItem("mimapa"))
		var myLatLng = { lat: parseFloat(miorigen.latitud), lng: parseFloat(miorigen.longitud) }
		var map = new google.maps.Map(document.getElementById("map"), {
			center: myLatLng,
			mapTypeId: "terrain",
			zoom: 15,
			disableDefaultUI: true,
			mapTypeControl: false
		});

		var marker = new google.maps.Marker({
			animation: google.maps.Animation.DROP,
			draggable: false,
			position: myLatLng,
			map: map,
			icon: "https://appxi.net//storage/landinpage/icons8-i-skin-type-1-48.png"
		});
		console.log(miorigen)
		$("#direccion").html(miorigen.direcion)
		$("#referencia").html(miorigen.referencia)

</script>