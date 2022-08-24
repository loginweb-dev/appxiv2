@extends('master')


@section('css')
<link href="{{ Voyager::image(setting('site.logo')) }}" rel="shortcut icon" type="image/x-icon">
@endsection
@section('content')
<div class="d-block d-sm-none">
	<nav class="navbar sticky-top navbar-light justify-content-center" style="background-color: #F0F0F4;">
		<a class="navbar-brand mititle" href="#">
		<img src="{{ Voyager::image(setting('site.logo')) }}" width="25"class="d-inline-block align-top" alt="">
		{{ setting('site.title') }}
		</a>
		<div id="miback">
			<a href="#" onclick="mivolver()" class="" style=" color: #0C2746;"><i class="fa-solid fa-circle-left fa-2xl"></i></a>
		</div>
		@if (Auth::user())
		<div id="milogout">
			<a href="{{ route('milogout') }}" onclick="milogout()" class="" style=" color: #0C2746;"><i class="fa-solid fa-arrow-right-from-bracket fa-2xl"></i></a>
		</div>
		@endif

	</nav>
	<div id="mibody"></div>		
</div>

@endsection

@section('javascript')
	<script>
		function milogout() {
			localStorage.removeItem('mimapa');
		}
		var toastr = new Toastr({ theme: 'default' });
		$(document).ready(function () {
			@if(Auth::user())
			$.ajax({
				url: "/history?user_id={{ Auth::user()->id }}",
				data: "html",
				success: function (response) {
					$("#mibody").html(response)
				}
			});
			@else
			$.ajax({
				url: "/login",
				data: "html",
				success: function (response) {
					$("#mibody").html(response)
				}
			});
				
			@endif
		});
		function mivolver() {
			var mivolver = localStorage.getItem('mivolver')
			location.href = mivolver
		}
		
		async function getpin() {
			if ($("#phone").val().length <= 8 && $("#nombre").val().length <= 8) {
				toastr.show("Ingresa tu nombre y whatsapp<br>(8 digitos minimo)")
			} else {			
				await axios.post('https://delivery-chatbot.appxi.net/getpin', {
					phone: $("#phone").val(),
					nombre: $("#nombre").val(),
					localidad: $("#localidad").val(),
				})
				$("#phone").attr('readonly', true)
				$("#nombre").attr('readonly', true)
				$("#localidad").attr('readonly', true)
				$(".getpin").attr('hidden', false)
				$(".setpin").attr('hidden', true)
			}
		}
		async function setpin() { 
			if ($("#pin").val().length < 4) {
				toastr.show("Ingresa un pin valido (4 digitos)")
			} else {	
			
					var result = await axios.post('https://delivery-chatbot.appxi.net/setpin', {
						phone: $("#phone").val(),
						pin: $("#pin").val()
					})
					if (result.data) {				
						// var miruta = "{{ route('setauth', 'user_id') }}"
						// miruta = miruta.replace('user_id', result.data.user.id)
						// location.href = miruta
						// localStorage.setItem('misession', JSON.stringify({
						// 	data: result.data
						// }))

						$.ajax({
							url: "/misession?user_id="+result.data.user.id,
							data: "html",
							success: function (response) {
								// $("#mibody").html(response)
								localStorage.setItem('minombre', $("#nombre").val())
								location.href = localStorage.getItem('mivolver')
							}
						});
					} else {
						// $("#phone").attr('readonly', false)
						// $("#nombre").attr('readonly', false)
						// $("#localidad").attr('readonly', false)
						// $(".getpin").attr('hidden', true)
						// $(".setpin").attr('hidden', false)
						$("#pin").val('')
						toastr.show("Pin incorrecto")

					}				
			}
		 }

		 function mireload() {
			$("#mibody").html("<div class='text-center'><img src='/reload.gif' alt='mireload' class='img-fluid m-2 p-2' width='200'></div>")    
		 	location.reload()
		 }
	</script>
@endsection