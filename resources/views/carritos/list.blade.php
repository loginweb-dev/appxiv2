@php
$cliente = App\Cliente::where('user_id', Auth::user()->id)->first();
$micarrito = App\Carrito::where('chatbot_id', $cliente->chatbot_id)->with('extras')->get();                    
@endphp 
<table class="table">
	@if (Auth::user())
	  @php
		  $stotal = 0;
		  $extras = 0;
	  @endphp
	  @if (count($micarrito) == 0 )
		 <h4 class="mitext text-center">Carrito Vacio</h4>
	  @else                      
		@foreach ($micarrito as $item)
			<tr>
			  <td width="60%">             
				{{ $item->cantidad.'-'.$item->producto_name.' ('.$item->precio.'Bs)' }} <br> 
				@if ($item->extras)			   
				  @foreach ($item->extras as $value)				  
					@php
					  $extrapro = App\Extraproducto::find($value->extra_id);
					  $extras += $item->cantidad * $extrapro->precio;
					@endphp
					<small>Extra: {{ $item->cantidad.' '.$extrapro->nombre.' ('.$extrapro->precio.'Bs)' }}</small>                                  
					<br>
				  @endforeach
				@endif
				<small><i class="fa-solid fa-shop"></i> {{ $item->negocio_name }}</small>           
			  </td>
			  <td class="text-center" width="30%">
				{{ number_format(($item->cantidad * $item->precio) + $extras, 2, ',', '.')  }}Bs.
				<br>
				<small><i class="fa-solid fa-message"></i> {{ $item->mensaje }}</small>
			  </td>
			  <td class="text-center">
				<a href="#" onclick="removeitem('{{ $item->id }}')" style="color: #D9374D;"><i class="fa-solid fa-trash"></i></a>
			  </td>                            
			</tr>                        
			@php 
			  $stotal +=  ($item->cantidad * $item->precio) + $extras;
			  $extras = 0;
			@endphp
			{{-- <input type="number" id="total_productos"> --}}
		@endforeach
		<tr>
		  <td colspan="3" class="text-right">
			<strong>Total Productos: </strong>{{ number_format($stotal, 2, ',', '.')  }} Bs.
			<input type="number" id="total_productos" hidden>
			<select name="" id="minegocios" multiple hidden>
				@foreach ($micarrito as $item)
					<option value="{{ $item->negocio_id }}" selected>{{ $item->negocio_name }}</option>
				@endforeach
			</select>
		  </td>
		</tr>
		<tr>
		  <td colspan="3">
			<a href="#" onclick="miconfirm('{{ $item->id }}')" class="btn miboton btn-block"><i class="fa-solid fa-check-double"></i> Confirmar Pedido</a>                        
		  </td>
		</tr>
	  @endif
	@endif
</table>

<script>
	$("#total_productos").val("{{ $stotal }}")
	localStorage.setItem('total_productos', $("#total_productos").val())

	var array_negocios = []
	localStorage.removeItem("micart")
	$("#minegocios :selected").map(async function(i, el) {	
			array_negocios.push($(el).val())
	}).get();
	localStorage.setItem("minegocios", JSON.stringify(removeDuplicates(array_negocios)))
	function removeDuplicates(arr) {
		return arr.filter((item,
			index) => arr.indexOf(item) === index);
	}


</script>