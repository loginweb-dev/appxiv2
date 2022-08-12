<h2 class="" title="Cliente selecionado">
	<i class="voyager-person"></i> 
	Cliente Generico / 00000000 
	<a href="#" onclick="main()" class="btn btn-xs btn-dark" title="Volver">
		<i class="voyager-double-left"></i>
	</a>
</h2>
<div class="form-group">
	<label for="">Buscar</label>
	<input type="search" class="form-control" placeholder="buscar cliente" id="cliente_search">
</div>
<table class="table" id="clientes_table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Cliente</th>
			<th>NIT/CI</th>
			<th>Whastapp</th>
			<th>Accion</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<script>
	$("#cliente_search").keyup(async function (e) { 	
		if (e.key === 'Enter' || e.keyCode === 13) {
			var result = await axios('https://appxi.net/api/pos/clientes/search/'+$("#cliente_search").val())
			// console.log($("#cliente_search").val())
			$("#clientes_table tr").remove()
			for (let index = 0; index < result.data.length; index++) {
				// const element = array[index];
				$("#clientes_table tbody").append("<tr><td>"+result.data[index].id+"</td><td>"+result.data[index].nombre+"</td></tr>");
			}
		}
	});
</script>