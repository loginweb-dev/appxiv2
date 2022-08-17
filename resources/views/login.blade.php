@extends('master')

@php
	$localidades = App\Poblacione::orderBy('created_at', 'desc')->get();
@endphp
@section('content')
	

{{-- -------------- UI MOVIL -------------- --}}
<div class="d-block d-sm-none">
	<nav class="navbar navbar-expand-sm sticky-top navbar-light justify-content-between" style="background-color: #F0F0F4;">
		<a class="navbar-brand" href="/">
		  <img src="storage/{{ setting('site.logo') }}" width="30" height="30" class="d-inline-block align-top" alt="">
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
			</ul>
		  </div>     
	</nav>

<div class="card">
	<article class="card-body">
		<h4 class="card-title text-center mt-1 mitext">Credenciales</h4>
		<hr>
			<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text"> <i class="fa fa-user"></i> </span>
				</div>
				<input id="nombre" class="form-control" placeholder="Nombre Completo" type="text" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAkCAYAAADo6zjiAAAAAXNSR0IArs4c6QAAAbNJREFUWAntV8FqwkAQnaymUkpChB7tKSfxWCie/Yb+gbdeCqGf0YsQ+hU95QNyDoWCF/HkqdeiIaEUqyZ1ArvodrOHxanQOiCzO28y781skKwFW3scPV1/febP69XqarNeNTB2KGs07U3Ttt/Ozp3bh/u7V7muheQf6ftLUWyYDB5yz1ijuPAub2QRDDunJsdGkAO55KYYjl0OUu1VXOzQZ64Tr+IiPXedGI79bQHdbheCIAD0dUY6gV6vB67rAvo6IxVgWVbFy71KBKkAFaEc2xPQarXA931ot9tyHphiPwpJgSbfe54Hw+EQHMfZ/msVEEURjMfjCjbFeG2dFxPo9/sVOSYzxmAwGIjnTDFRQLMQAjQ5pJAQkCQJ5HlekeERxHEsiE0xUUCzEO9AmqYQhiF0Oh2Yz+ewWCzEY6aYKKBZCAGYs1wuYTabKdNNMWWxnaA4gp3Yry5JBZRlWTXDvaozUgGTyQSyLAP0dbb3DtQlmcan0yngT2ekE9ARc+z4AvC7nauh9iouhpcGamJeX8XF8MaClwaeROWRA7nk+tUnyzGvZrKg0/40gdME/t8EvgG0/NOS6v9NHQAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;" autocomplete="off">
			</div>
			</div> 
			<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text"> <i class="fa fa-whatsapp"></i> </span>
				</div>
				<input id="phone" class="form-control" placeholder="whatsapp" type="number" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKRJREFUWAnNl0tsVGUUxzvTTlslZUaCloZHY6BRFkp9sDBuqgINpaBp02dIDImwKDG6ICQ8jBYlhg0rxUBYEALTpulMgBlqOqHRDSikJkZdGG0CRqAGUuwDovQ1/s7NPTffnTu3zMxGvuT2vP7n8Z3vu+dOi4r+5xUoJH8sFquamZmpTqfTVeIfCARGQ6HQH83NzaP5xsu5gL6+vuVzc3NdJN1Kkhd8Ev1MMYni4uJjra2tt3wwLvUjCxgYGFg8Pj7+MV5dPOUub3/hX0zHIpFId0NDw6Q/jO4tZOzv76+Znp6+AOb5TBw7/YduWC2Hr4J/IhOD/GswGHy7vb39tyw2S+VbAC1/ZXZ29hKoiOE8RrIvaPE5WvyjoS8CX8sRvYPufYpZYtjGS0pKNoD/wdA5bNYCCLaMYMMEWq5IEn8ZDof3P6ql9pF9jp8cma6bFLGeIv5ShdISZUzKzqPIVnISp3l20caTJsaPtwvc3dPTIx06ziZkkyvY0FnoW5l+ng7guAWnpAI5w4MkP6yy0GQy+dTU1JToGm19sqKi4kBjY+PftmwRYn1ErEOq4+i2tLW1DagsNGgKNv+p6tj595nJxUbyOIF38AwipoSfnJyMqZ9SfD8jxlWV5+fnu5VX6iqgt7d3NcFeUiN0n8FbLEOoGkwdgY90dnbu7OjoeE94jG9wd1aZePRp5AOqw+9VMM+qLNRVABXKkLEWzn8S/FtbdAhnuVQE7LdVafBPq04pMYawO0OJ+6XHZkFcBQA0J1xKgyhlB0EChEWGX8RulsgjvOjEBu+5V+icWOSoFawuVwEordluG28oSCmXSs55SGSCHiXhmDzC25ghMHGbdwhJr6sAdpnyQl0FYIyoEX5CeYOuNHg/NhvGiUUxVgfV2VUAxjtqgPecp9oKoE4sNnbX9HcVgMH8nD5nAoWnKM/5ZmKyySRdq3pCmDncR4DxOwVC64eHh0OGLOcur1Vey46xUZ3IcVl5oa4OlJaWXgQwJwZyhUdGRjqE14VtSnk/mokhxnawiwUvsZmsX5u+rgKamprGMDoA5sKhRCLxpDowSpsJ8vpCj2AUPzg4uIiNfKIyNMkH6Z4hF3k+RgTYz6vVAEiKq2bsniZIC0nTtvMVMwBzoBT9tKkTHp8Ak1V8dTrOE+NgJs7VATESTH5WnVAgfHUqlXK6oHpJEI1G9zEZH/Du16leqHyS0UXBNKmeOMf5NvyislJPB8RAFz4g8IuwofLy8k319fUP1EEouw7L7mC3kUTO1nn3sb02MTFxFpsz87FfJuaH4pu5fF+reDz+DEfxkI44Q0ScSbyOpDGe1RqMBN08o+ha0L0JdeKi/6msrGwj98uZMeon1AGaSj+elr9LwK9IkO33n8cN7Hl2vp1N3PcYbUXOBbDz9bwV1/wCmXoS3+B128OPD/l2LLg8l9APXVlZKZfzfDY7ehlQv0PPQDez6zW5JJdYOXdAwHK2dGIv7GH4YtHJIvEOvvunLCHPPzl3QOLKTkl0hPbKaDUvlTU988xtwfMqQBPQ3m/4mf0yBVlDCSr/CRW0CipAMnGzb9XU1NSRvIX7kSgo++Pg9B8wltxxbHKPZgAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;" autocomplete="off">
			</div> 			
			</div> 
			<div class="form-group input-group">
				<div class="input-group-prepend">
					<span class="input-group-text"> <i class="fa fa-map"></i> </span>
				</div>
				<select name="" id="localidad" class="form-control mr-sm-1">
					{{-- <option value="0"> Elige tu localidades </option> --}}
					@foreach ($localidades as $item)
					  <option value="{{ $item->id }}">{{ $item->nombre }}</option>
					@endforeach
				  </select>
			</div>
			<div class="getpin" hidden>				
				<div class="form-group panel confimar">
						<input id="pin" class="form-control" placeholder="Ingresan tu pin" type="number">
						<button onclick="setpin()" type="button" class="btn miboton btn-block mt-2">Confirmar</button>
				</div>
			</div>
			<div class="form-group setpin">
				<p class="text-center">se enviara noficacion a tu whatsapp, para confirmar.</p>
				<button onclick="getpin()" type="button" class="btn miboton btn-block">Enviar a WhatsApp</button>
			</div>

	</article>
	</div>
	
</div>

{{-- -------------- UI DESSKTOP -------------- --}}
<div class="container-fluid d-none d-sm-block mt-2">

</div>
@endsection

@section('javascript')
	<script>
		async function getpin() {
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

		async function setpin() { 
			var result = await axios.post('https://delivery-chatbot.appxi.net/setpin', {
				phone: $("#phone").val(),
				pin: $("#pin").val()
			})
			if (result.data) {				
				var miruta = "{{ route('setauth', 'user_id') }}"
				miruta = miruta.replace('user_id', result.data.user.id)
				location.href = miruta
				// localStorage.setItem('misession', JSON.stringify({
				// 	data: result.data
				// }))
			} else {
				$("#phone").attr('readonly', false)
				$("#nombre").attr('readonly', false)
				$("#localidad").attr('readonly', false)
				$(".getpin").attr('hidden', true)
				$(".setpin").attr('hidden', false)
			}

		 }
	</script>
@endsection