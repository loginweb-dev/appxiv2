@extends('master')
@section('content')
		{{-- -------------- UI MOVIL -------------- --}}
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
					{{-- <li class="list-group-item">
					  <a class="mititle" href="/taxi/nuevo"> <i class="fa-solid fa-taxi"></i> Nuevo Viaje </a>
					</li> --}}
					<li class="list-group-item">
					  <a class="mititle" href="/maps"><i class="fa-solid fa-location-crosshairs"></i> Cerca de Ti</a> 
					</li>
					<li class="list-group-item">
					  <a class="mititle" href="/perfil"> <i class="fa-solid fa-address-card"></i> Mi Perfil </a>
					</li>
				  </ul>
				</div>      
		</div>
@endsection