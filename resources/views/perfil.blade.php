@extends('master')

@section('content')
<div class="d-block d-sm-none">
	<nav class="navbar navbar-expand-sm sticky-top navbar-light justify-content-between" style="background-color: #F0F0F4;">
		<a class="navbar-brand" href="/">
		<img src="{{ Voyager::image(setting('site.logo')) }}" width="30" height="30" class="d-inline-block align-top" alt="">
		{{ setting('site.title') }}
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
		<i class="fa fa-list"></i>
		</button>
		<div class="collapse navbar-collapse" id="navbarTogglerDemo02">

		</div>

	</nav>
	<h1>Perfil</h1>
	<p>{{ $micliente->nombre }}</p>
	<a href="{{ route('milogout') }}" class="btn miboton">Salir</a>
</div>
@endsection