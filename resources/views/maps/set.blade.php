@extends('master')

@section('css')
<style>
	#map {
		width: 100%;
		height: 100%;
	}

	.modal-dialog {
	  position: fixed;
	  bottom: 0;
	  left: 0%;
	  right: 0%;
	  /* transform: translate(-150%, -150%); */
	} 
	</style>
@endsection


@section('content')
		{{-- -------------- UI MOVIL -------------- --}}
		<div class="d-block d-sm-none">
			<nav class="navbar sticky-top navbar-light justify-content-center" style="background-color: #F0F0F4;">
				<a class="navbar-brand mititle" href="#">
				<img src="{{ Voyager::image(setting('site.logo')) }}" width="30" height="30" class="d-inline-block align-top" alt="">
				{{ setting('site.title') }}
				</a>
				<div id="miback">
					<a href="#" onclick="mivolver()" class="" style=" color: #0C2746;"><i class="fa-solid fa-circle-left fa-2xl"></i></a>
				</div>
			</nav>
			<div id="mimap">
						
				<div class="micontent p-2 ml-1 mr-1">
					<h4 class="text-center">Tu Ubicacion</h4>
					<div class="form-group">
						<label for="">Direccion</label>
						<input type="text" class="form-control miinput" id="direcion" placeholder="Direccion">
					</div>
					<div class="form-group">
						<label for="">Referencia</label>
						<input type="text" class="form-control miinput" id="referencia" placeholder="Referencia">
					</div>
					<a href="#" class="btn miboton btn-block" onclick="miset()"><i class="fa-solid fa-check-double"></i> Confirmar</a>
					<input type="text" id="latitud" hidden>
					<input type="text" id="longitud" hidden>
				</div>

				<div id="map"></div>	
			</div>
		</div>
	</div>

@endsection

@section('javascript')
<script>
	var toastr = new Toastr({});
	function miset(){
		if ($("#referencia").val() == '' || $("#direcion").val() == '') {
			toastr.show("Ingresa una Direccion o Rerencia")
		} else {
				
			localStorage.setItem('mimapa', JSON.stringify({
				latitud: $("#latitud").val(),
				longitud: $("#longitud").val(),
				direcion: $("#direcion").val(),
				referencia: $("#referencia").val()
			}))
			location.href = '/'
		}
	}
	// $("#exampleModal").modal()

    var map;
    var maker;
    var options = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };
    var set_radius = null
    var marker_negocio = null
    var negocios_array = []
    var directionsDisplay = null
    var directionsService = null
    var geocoder = null

    navigator.geolocation.getCurrentPosition(set_origen, error, options)

	async function set_origen(pos) {
		var crd = pos.coords
		var radio = pos.accuracy
		var myLatLng = { lat: pos.coords.latitude, lng: pos.coords.longitude }
		map = new google.maps.Map(document.getElementById("map"), {
			center: myLatLng,
			mapTypeId: "terrain",
			zoom: 15,
			// disableDefaultUI: true,
			mapTypeControl: false
		});

		marker = new google.maps.Marker({
			animation: google.maps.Animation.DROP,
			draggable: false,
			position: myLatLng,
			map: map,
			icon: "https://appxi.net//storage/landinpage/icons8-i-skin-type-1-48.png"
		});


		geocoder =  new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
		geocoder.geocode({
		'latLng': latlng
		}, function (results, status) {
			if (status === google.maps.GeocoderStatus.OK) {
			if (results[1]) {
				console.log(results[1]);
				$("#direcion").val(results[1].formatted_address)
			} else {
				// alert('No results found');
				$("#direcion").val('No results found')

			}
			} else {
			alert('Geocoder failed due to: ' + status);
			}
		});

		//set input lst lng
		$("#latitud").val(pos.coords.latitude)
		$("#longitud").val(pos.coords.longitude)

		google.maps.event.addListener(map, 'bounds_changed', function() {		
			marker.setPosition({lat: map.center.lat(), lng: map.center.lng()})

		});

		google.maps.event.addListener(map, "dragend", function() {
			console.log('fin')
			geocoder =  new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(map.center.lat(), map.center.lng());
			geocoder.geocode({
			'latLng': latlng
			}, function (results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
				if (results[1]) {
					console.log(results[1]);
					$("#direcion").val(results[1].formatted_address)
				} else {
					$("#direcion").val('No results found')

				}
				} else {
				alert('Geocoder failed due to: ' + status);
				}
			});
		});
	}

	$("#mimap").height($(document).height()-330)





  function error(err) {
      // alert("Habilita tu Sensor GPS")
      // location.reload()
      toastr.show("Habilita tu Sensor GPS")
  };
  
	function mivolver() {
		var mivolver = localStorage.getItem('mivolver')
		location.href = mivolver
	}
</script>
@endsection