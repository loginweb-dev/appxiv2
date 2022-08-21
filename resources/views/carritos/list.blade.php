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
				{{-- <small><i class="fa-solid fa-message"></i> {{ $item->mensaje }}</small>        
				<br> --}}
				<small><i class="fa-solid fa-shop"></i> {{ $item->negocio_name }}</small>           
			  </td>
			  <td class="text-center" width="30%">
				{{ number_format(($item->cantidad * $item->precio) + $extras, 2, ',', '.')  }}Bs.
				<br>
				<small><i class="fa-solid fa-message"></i> {{ $item->mensaje }}</small>

			  </td>
			  <td class="text-center">
				<a href="#" style="color: #D9374D;"><i class="fa-solid fa-trash"></i></a>
			  </td>                            
			</tr>                        
			@php 
			  $stotal +=  ($item->cantidad * $item->precio);
			  $extras = 0;
			@endphp
		@endforeach
		<tr>
		  <td colspan="3" class="text-right">
			{{-- <strong>Productos: </strong>{{ number_format($stotal, 2, ',', '.')  }} Bs. --}}
			{{-- <br>
			<strong>Costo Envio : </strong> 06,00 Bs. --}}
			{{-- <br> --}}
			<strong>Total Productos: </strong>{{ number_format($stotal, 2, ',', '.')  }} Bs.
		  </td>
		</tr>
		<tr>
		  <td colspan="3">
			<a href="#" onclick="miconfirm()" class="btn miboton btn-block"><i class="fa-solid fa-check-double"></i> Confirmar Pedido</a>                        
		  </td>
		</tr>
	  @endif
	@endif
</table>