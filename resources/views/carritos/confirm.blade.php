@php
$cliente = App\Cliente::where('user_id', Auth::user()->id)->first();
$micarrito = App\Carrito::where('chatbot_id', $cliente->chatbot_id)->with('extras')->get();         
@endphp 
<div id="map"></div>

<h5 class="mititle text-center">Distancias y Rutas </h5>
<div class="m-2 p-2 micontent">	
	<table width="100%">
		<tr>
			<td class="text-center">
				<strong>Negocios:</strong><br>
				<div id="minegocios"></div>				
			</td>
			<td class="text-center">
				<strong>Distancia:</strong><br>
				<div id="midistancia"></div>
				
			</td>
			<td class="text-center">
				<strong>Tiempo:</strong><br>
				<div id="mitiempo"></div>
			</td>
		</tr>
	</table>
</div>
<h5 class="mititle text-center">Totales en Bs </h5>
<div class="m-2 p-2 micontent">
	<table width="100%">
		<tr>
			<td class="text-center">
				<strong>Productos:</strong>
				<div id="total_productos_a"></div>
			</td>
			<td class="text-center">
				<strong>Envio:</strong><br>
				<div id="mienvio"></div>
			</td>
			<td class="text-center">
				<strong>Monto:</strong><br>
				<div id="mimonto"></div>
			</td>
		</tr>
	</table>
</div>

<div class="m-2">
	<h5 class="mititle text-center">Como quieres pagar ?</h5>	
	<select name="" id="mipago" class="form-control miselect">
		@foreach ($pasarela as $item)
			<option value="{{ $item->id }}">{{ $item->title }}</option>
		@endforeach
	</select>
</div>

<div class="m-1 p-1">
	<input type="text" class="form-control miinput" id="mensaje_delivery" placeholder="Mensaje al Delivery">
</div>

<div class="m-2">
	<a href="#" onclick="savepedido()" class="btn miboton btn-block"><i class="fa-brands fa-whatsapp fa-xl"></i> Enviar Pedido </a>   
</div>
<script>
		 
		var total_productos = localStorage.getItem('total_productos')
		$(document).ready(function () {
			calularruta()
			$("#total_productos_a").html(formatMoney(total_productos, ".", ","))
		});


		var miorigen = JSON.parse(localStorage.getItem("mimapa"))
		var myLatLng = { lat: parseFloat(miorigen.latitud), lng: parseFloat(miorigen.longitud) }
		var directionsDisplay = null
    	var directionsService = null
		var map = new google.maps.Map(document.getElementById("map"), {
			center: myLatLng,
			mapTypeId: "terrain",
			zoom: 15,
			disableDefaultUI: false,
			mapTypeControl: false
		});
		
		
		async function calularruta(){
			var minegocios = JSON.parse(localStorage.getItem('minegocios'))
			var milocation = JSON.parse(localStorage.getItem('mimapa'))
			$("#minegocios").html(minegocios.length+' cant')
			switch (minegocios.length) {
				case 1:
					var minegocio = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[0])
					var points = {
						origin: { lat: parseFloat(minegocio.data.latitud), lng: parseFloat(minegocio.data.longitud) },
						destination: { lat: parseFloat(milocation.latitud), lng: parseFloat(milocation.longitud) },
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					};
					directionsDisplay = new google.maps.DirectionsRenderer();
					directionsService = new google.maps.DirectionsService();
					directionsDisplay.setMap(map);		
					directionsService.route(points, async function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay.setDirections(response)
							console.log(response.routes[0].legs[0].duration.text)
							$("#mitiempo").html(response.routes[0].legs[0].duration.text)
							$("#midistancia").html(response.routes[0].legs[0].distance.text)
							var calc_envio = micalculo(response.routes[0].legs[0].distance.value)
							$("#mienvio").html(formatMoney(calc_envio, ".", ","))
							$("#mimonto").html(formatMoney( calc_envio + parseFloat(total_productos), ".", ","))

							localStorage.setItem("total_envio", calc_envio)
							localStorage.setItem("total_a_pagar", parseFloat(total_productos) + parseFloat(calc_envio))
						}
					});
		
					break;
				case 2:
					var minegocio1 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[0])
					var minegocio2 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[1])
					var tiempo_total = 0
					var distancia_total = 0					
					var points1 = {
						origin: { lat: parseFloat(minegocio1.data.latitud), lng: parseFloat(minegocio1.data.longitud) },
						destination: { lat: parseFloat(minegocio2.data.latitud), lng: parseFloat(minegocio2.data.longitud) },
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					};
					var directionsDisplay1 = new google.maps.DirectionsRenderer({map: map});
					var directionsService1 = new google.maps.DirectionsService();
					directionsService1.route(points1, async function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay1.setDirections(response)
							tiempo_total += response.routes[0].legs[0].duration.value
							distancia_total += response.routes[0].legs[0].distance.value
							console.log(response.routes[0].legs[0].distance.value)
						}
					});					
					var points2 = {
						origin: { lat: parseFloat(minegocio1.data.latitud), lng: parseFloat(minegocio1.data.longitud) },
						destination: { lat: parseFloat(milocation.latitud), lng: parseFloat(milocation.longitud) },
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					};	
					var directionsDisplay2 = new google.maps.DirectionsRenderer({map: map});
					var directionsService2 = new google.maps.DirectionsService();	
					directionsService2.route(points2, async function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay2.setDirections(response)
							tiempo_total += response.routes[0].legs[0].duration.value
							distancia_total += response.routes[0].legs[0].distance.value
							console.log(response.routes[0].legs[0].distance.value)
							$("#mitiempo").html(formatMoney(tiempo_total/60, ".", ","))
							$("#midistancia").html(formatMoney(distancia_total/1000, ".", ","))
							var calc_envio = micalculo(distancia_total)
							$("#mienvio").html(formatMoney(calc_envio, ".", ","))
							$("#mimonto").html(formatMoney( calc_envio + parseFloat(total_productos), ".", ","))
						}
					});					
					break;
				case 3:

					var minegocio1 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[0])
					var minegocio2 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[1])
					var minegocio3 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[2])
					var tiempo_total = 0
					var distancia_total = 0				

					//1
					var points1 = {
						origin: { lat: parseFloat(minegocio1.data.latitud), lng: parseFloat(minegocio1.data.longitud) },
						destination: { lat: parseFloat(minegocio2.data.latitud), lng: parseFloat(minegocio2.data.longitud) },
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					};
					var directionsDisplay1 = new google.maps.DirectionsRenderer({map: map});
					var directionsService1 = new google.maps.DirectionsService();
					directionsService1.route(points1, async function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay1.setDirections(response)
							tiempo_total += response.routes[0].legs[0].duration.value
							distancia_total += response.routes[0].legs[0].distance.value
							console.log(response.routes[0].legs[0].distance.value)
						}
					});	

					//2
					var points2 = {
						origin: { lat: parseFloat(minegocio2.data.latitud), lng: parseFloat(minegocio2.data.longitud) },
						destination: { lat: parseFloat(minegocio3.data.latitud), lng: parseFloat(minegocio3.data.longitud) },
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					};
					var directionsDisplay2 = new google.maps.DirectionsRenderer({map: map});
					var directionsService2 = new google.maps.DirectionsService();
					directionsService2.route(points2, async function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay1.setDirections(response)
							tiempo_total += response.routes[0].legs[0].duration.value
							distancia_total += response.routes[0].legs[0].distance.value
							console.log(response.routes[0].legs[0].distance.value)
						}
					});	

					// 3			
					var points3 = {
						origin: { lat: parseFloat(minegocio3.data.latitud), lng: parseFloat(minegocio3.data.longitud) },
						destination: { lat: parseFloat(milocation.latitud), lng: parseFloat(milocation.longitud) },
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					};	
					var directionsDisplay3 = new google.maps.DirectionsRenderer({map: map});
					var directionsService3 = new google.maps.DirectionsService();	
					directionsService3.route(points3, async function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay3.setDirections(response)
							tiempo_total += response.routes[0].legs[0].duration.value
							distancia_total += response.routes[0].legs[0].distance.value
							console.log(response.routes[0].legs[0].distance.value)
							$("#mitiempo").html(formatMoney(tiempo_total/60, ".", ",")+' min')
							$("#midistancia").html(formatMoney(distancia_total/1000, ".", ",")+' km')
							var calc_envio = micalculo(distancia_total)
							$("#mienvio").html(formatMoney(calc_envio, ".", ","))
							$("#mimonto").html(formatMoney( calc_envio + parseFloat(total_productos), ".", ","))
						}
					});	
					
					break;
				default:
					break;
			}
		}

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

		function getdirections(lat, lng) {
			directionsDisplay = new google.maps.DirectionsRenderer();
        	directionsService = new google.maps.DirectionsService();
       		directionsDisplay.setMap(map);
   
			directionsService.route(points, async function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(response)
					console.log(response.routes[0].legs[0].duration.text)
				}
			});
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

</script>
