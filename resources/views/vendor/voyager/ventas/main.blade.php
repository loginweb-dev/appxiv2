<h2 class="" title="Cliente selecionado">
	<i class="voyager-person"></i> 
	Cliente Generico / 00000000 
	<a href="#" onclick="clientes()" class="btn btn-xs btn-dark" title="Buscar Cliente">
		<i class="voyager-search"></i>
	</a>
	<a href="#" class="btn btn-xs btn-dark" title="Panel Touch">
		<i class="voyager-tv"></i>
	</a>
</h2>
<div class="" style="padding: 5px; background-color: #536069;">
	<input type="search" class="form-control" placeholder="Buscar o escanear producto" style="width: 40%" id="producto_search">
</div>  
<table class="table" id="productos_table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Nombre</th>
			<th>Cantidad</th>
			<th>Unidad</th>
			<th>S.Total</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>                            
<table border="0" width="100%>
	<tr style="border-top:1pt solid #536069;">
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td align="right">
			<h4>Importe: $0.00</h4>                                        
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td style="padding-bottom: 15px">
			<select name="" id="" class="form-control">
				<option value="">Descuentos</option>
			</select>
		</td>
		<td style="padding-bottom: 15px">
			<input type="text" class="form-control" placeholder="0.00">
		</td>
	</tr>
	<tr style="background-color: #536069; color: white;">
		<td></td>
		<td></td>
		<td></td>
		<td>
			<h4>
				Cantidad: 0
			</h4>			
		</td>                                    
		<td align="right" style="padding: 6px;">
			<h3>Total: Bs 0.00</h3>
		</td>
	</tr>
	<tr>
		<td>
			<a href="#" class="btn btn-xs btn-default" title="Panel Touch">
				<i class="voyager-plus"> Nuevo</i>
			</a>
		</td>
		<td>
			<a href="#" class="btn btn-xs btn-default" title="Panel Touch">
				<i class="voyager-data"> Guardar</i>
			</a>
		</td>
		<td>
			<a href="#" class="btn btn-xs btn-default" title="Panel Touch">
				<i class="voyager-news"> Imprimir</i>
			</a>
		</td>
		<td></td>
		<td  align="right">
			<a href="#" onclick="pogoview()" class="btn btn-xs btn-primary" title="Panel Touch">
				<i class="voyager-credit-cards"> Cobrar</i>
			</a>
		</td>
	</tr>
</table>                            

<script>
	$("#producto_search").keyup(async function (e) { 	
		if (e.key === 'Enter' || e.keyCode === 13) {
			var result = await axios('https://appxi.net/api/pos/productos/search/'+$("#producto_search").val())
			// console.log($("#cliente_search").val())
			$("#productos_table tbody tr").remove()
			for (let index = 0; index < result.data.length; index++) {
				// const element = array[index];
				$("#productos_table tbody").append("<tr><td>"+result.data[index].id+"</td><td>"+result.data[index].nombre+"</td></tr>");
			}
		}
	});
</script>