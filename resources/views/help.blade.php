@extends('master')
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
			<div id="mibody"></div>		
			<h4 class="mitext text-center">en desarrollo</h4>
		</div>
@endsection

@section('javascript')
<script>
	function mivolver() {
		var mivolver = localStorage.getItem('mivolver')
		location.href = mivolver
	}
</script>
@endsection