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
        #map {
            width: 100%;
            height: 100%;
        }
	</style>
@endsection
@php
    
    $negocios = App\Negocio::where('estado', 1)->orderBy('order', 'asc')->with('poblacion', 'tipo', 'productos')->get();

@endphp
@section('content')
	{{-- <h1>Certa de Ti</h1> --}}
	  {{-- -------------- UI MOVIL -------------- --}}
	  <div class="d-block d-sm-none">
		{{-- <nav class="navbar navbar-expand-sm sticky-top navbar-light justify-content-between" style="background-color: #F0F0F4;"> --}}
            <nav class="navbar sticky-top navbar-light justify-content-center" style="background-color: #F0F0F4;">
                <a class="navbar-brand" href="/">
                  <img src="storage/{{ setting('site.logo') }}" width="30" height="30" class="d-inline-block align-top" alt="">
                  {{ setting('site.title') }}
                </a>
                <div id="miback">
                    <a href="#" onclick="mivolver()" class="" style=" color: #0C2746;"><i class="fa-solid fa-circle-left fa-2xl"></i></a>
                </div>   
              </nav>
              <div class="text-center">
                
                    <table>
                        <tr>
                            <td width=50%>
                                <h6 class="text-center mitext p-2 m-2">Negocios y Comercios <br> Cerca de Ti</h6>                                    
                            </td>
                            <td>
                                <select id="minegocios" class="form-control">
                                    <option value="0">Elige un Negocio</option>
                                    @foreach ($negocios as $item)
                                        @if (isset($_GET['negocio']))
                                            <option value="{{ $item->id }}" @if($_GET['negocio'] == $item->id) selected @endif>{{ $item->nombre }}</option>
                                        @else
                                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button class="btn miboton"  hidden><i class="fa fa-search"></i></button>
                                <select class="form-control" id="miradio">
                                    @if (isset($_GET['radio']))                                        
                                        <option value="0.5" @if($_GET['radio'] == 0.5) selected @endif>Radio 1/2 KM</option>
                                        <option value="2" @if($_GET['radio'] == 2) selected @endif>Radio 2 KM</option>
                                        <option value="4" @if($_GET['radio'] == 4) selected @endif>Radio 4 KM</option>
                                    @else
                                    <option value="0.5">Radio 1/2 KM</option>
                                        <option value="2">Radio 2 KM</option>
                                        <option value="4">Radio 4 KM</option>
                                    @endif
                                </select>
                            </td>
                        </tr>
                    </table>
                    <input type="number" id="milatitud" hidden>
                    <input type="number" id="milongitud" hidden>
   
              </div>
              <div id="mimap">
                <div id="map"></div>
              </div>
	  </div>	
@endsection

@section('javascript')

<script>
    var toastr = new Toastr({});
    var map;
    var marker;
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
    
    navigator.geolocation.getCurrentPosition(set_origen, error, options)
    navigator.geolocation.watchPosition(update_point, error, options)

    function update_point(pos) {
      var crd = pos.coords;
    //   set_radius.setCenter({lat: crd.latitude, lng: crd.longitude})
      marker.setPosition({lat: crd.latitude, lng: crd.longitude})
      map.setCenter({lat: crd.latitude, lng: crd.longitude})
        $("#milatitud").val(crd.latitude)
        $("#milongitud").val(crd.longitude)

    }

    async function set_origen(pos) {
        const styledMapType = new google.maps.StyledMapType(
            [
                {
                    "featureType": "administrative",
                    "elementType": "all",
                    "stylers": [
                        {
                            "saturation": "-100"
                        }
                    ]
                },
                {
                    "featureType": "administrative.province",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [
                        {
                            "saturation": -100
                        },
                        {
                            "lightness": 65
                        },
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [
                        {
                            "saturation": -100
                        },
                        {
                            "lightness": "50"
                        },
                        {
                            "visibility": "simplified"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [
                        {
                            "saturation": "-100"
                        }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "simplified"
                        }
                    ]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "all",
                    "stylers": [
                        {
                            "lightness": "30"
                        }
                    ]
                },
                {
                    "featureType": "road.local",
                    "elementType": "all",
                    "stylers": [
                        {
                            "lightness": "40"
                        }
                    ]
                },
                {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [
                        {
                            "saturation": -100
                        },
                        {
                            "visibility": "simplified"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "hue": "#ffff00"
                        },
                        {
                            "lightness": -25
                        },
                        {
                            "saturation": -97
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "labels",
                    "stylers": [
                        {
                            "lightness": -25
                        },
                        {
                            "saturation": -100
                        }
                    ]
                }
            ],
        { name: "Styled Map" }
        );

        var crd = pos.coords
        var radio = pos.accuracy
        var myLatLng = { lat: pos.coords.latitude, lng: pos.coords.longitude }
        map = new google.maps.Map(document.getElementById("map"), {
            center: myLatLng,
            mapTypeId: "terrain",
            zoom: 14,
            // disableDefaultUI: true,
            mapTypeControl: false
        });



    // geo radio 
    set_radius = new google.maps.Circle({
      strokeColor: "#0C2746",
      strokeOpacity: 0.2,
      strokeWeight: 1,
      fillColor: "#0C2746",
      fillOpacity: 0.10,
      map: map,
      center: myLatLng,
      radius: $("#miradio").val() * 1000, // 1km
      animation: google.maps.Animation.DROP,
    });

    // marker user
    marker = new google.maps.Marker({
        animation: google.maps.Animation.DROP,
        draggable: false,
        position: myLatLng,
        map: map,
        icon: "https://appxi.net//storage/landinpage/icons8-i-skin-type-1-48.png"
    });

	// estilos
    // map.mapTypes.set("styled_map", styledMapType);
    // map.setMapTypeId("styled_map");


	// controls 
	// var input = document.getElementById('pac-input');
    // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    // var button = document.getElementById('doSearch');
    // map.controls[google.maps.ControlPosition.TOP_LEFT].push(button);

    // var minegocios = await axios('https://appxi.net/api/app/negocios')
    // for (let index = 0; index < minegocios.data.length; index++) {
      // console.log(minegocios.data)
    //   @foreach($negocios as $item)
    //     marker_negocio = new google.maps.Marker({
    //         position:  { lat: parseFloat({{ $item->latitud }}), lng: parseFloat({{ $item->longitud }}) },
    //         icon: "{{ Voyager::image($item->Thumbnail('icon' ,'logo'))  }}",
    //         // label: minegocios.data[index].nombre,
    //         // label: {
    //         //   text: minegocios.data[index].nombre,
    //         //   color: "#0C2746",
    //         //   fontSize: "20px",
    //         //   fontWeight: "bold"
    //         // },
    //         title: "{{ $item->nombre }}",
    //         map: map,
    //     });
    //     negocios_array.push({id: "{{ $item->id }}", nombre: "{{ $item->nombre }}", direccion: "{{ $item->direccion }}", mimarker: marker_negocio})
    //     $("#browsers").append("<option value='{{ $item->nombre }}'>")
    //   @endforeach

    // }

    //detectar negocios dentro del radio
    var toastr = new Toastr({});
    google.maps.event.addListener(map, 'bounds_changed', function() {
        set_radius.setCenter({lat: map.center.lat(), lng: map.center.lng()})
        marker.setPosition({lat: map.center.lat(), lng: map.center.lng()})
        // var bounds = set_radius.getBounds()
        // for (let index = 0; index < negocios_array.length; index++) {      
        //   var numarker = negocios_array[index].mimarker
        //   if (bounds.contains(numarker.getPosition())) {
        //       toastr.show(negocios_array[index].nombre +' '+negocios_array[index].direccion)
        //       break
        //   }
        // }
    });

  }
  function error(err) {
      // alert("Habilita tu Sensor GPS")
      // location.reload()
      toastr.show("Habilita tu Sensor GPS")
  };

  console.log($(document).height())
  $("#mimap").height($(document).height()-200)

  $("#miradio").change(function (e) { 
    e.preventDefault();
    location.href = '?radio='+this.value
    console.log(this.value)
  });

  $("#minegocios").change(function (e) { 
    e.preventDefault();
    location.href = '?negocio='+this.value
    console.log(this.value)
  });
  @if(isset($_GET['negocio']))
    $.get("https://appxi.net/api/negocio/{{ $_GET['negocio'] }}", function( data ) {
        var myLatLng = { lat: parseFloat(data.latitud), lng: parseFloat(data.longitud) }
        console.log(data.latitud)
        var viaje = {
            origin: { lat: parseFloat($("#milatitud").val()), lng: parseFloat($("#milongitud").val()) },
            destination: { lat: parseFloat(data.latitud), lng: parseFloat(data.longitud) },
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };
        directionsDisplay = new google.maps.DirectionsRenderer();
        directionsService = new google.maps.DirectionsService();
        directionsDisplay.setMap(map);
        directionsService.route(viaje, async function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response)
            }
        });
    });
  @endif
  function mivolver() {
			var mivolver = localStorage.getItem('mivolver')
			location.href = mivolver
		}
</script>
@endsection