@extends('voyager::master')

@section('css')
@stop
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-helm"></i>Detalle Del Pedido
        </h1>
    </div>
@stop
@section('content')
    <div id="voyager-loader" class="mireload">
        <?php $admin_loader_img = Voyager::setting('admin.loader', ''); ?>
        @if($admin_loader_img == '')
            <img src="{{ voyager_asset('images/logo-icon.png') }}" alt="Voyager Loader">
        @else
            <img src="{{ Voyager::image($admin_loader_img) }}" alt="Voyager Loader">
        @endif
    </div>
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Nombre</th>
                                        <th>Negocio</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
                                        <td><h5>Producto</h5></td>
                                        <td>Hamburguesa</td>
                                        <td>4</td>
                                        <td>2</td>
                                        <td>8</td>
                                        <td>1</td>
                                        <td>Comentario</td>
                                    </tr>
                                    <tr>
                                        <td>Extra</td>
                                        <td>Tocino</td>
                                        <td>4</td>
                                        <td>2</td>
                                        <td>8</td>
                                        <td>1</td>
                                        <td>Comentario</td>
                                    </tr><hr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $('document').ready(function () {
            Detalles()
        });

        async function Detalles(){
            $("#dataTable tbody tr").remove();
            var detalle= await axios("{{setting('admin.url')}}api/pedido/negocios/"+'{{$id}}')
            var pedido= await axios("{{setting('admin.url')}}api/find/pedido/"+'{{$id}}')
            var comentario = await axios("{{setting('admin.url')}}api/find/comentario/"+'{{$id}}')
            //console.log(detalle.data)
            var list=""
            
            var validador=false;

            var user_id= '{{Auth::user()->id}}'
            var user= await axios("{{setting('admin.url')}}api/user/"+user_id)
            if (user.data.role_id==3) {
                var negocio= await axios("{{setting('admin.url')}}api/user/negocio/"+user_id)
                
            }
            else if(user.data.role_id==4){
                var mensajero= await axios("{{setting('admin.url')}}api/find/mensajero/"+user.data.id)
            }

            for (let index = 0; index < detalle.data.length; index++) {
                list+="<tr><td><h5>Producto</h5></td><td>"+detalle.data[index].producto_name+"</td><td>"+detalle.data[index].negocio_name+"</td><td>"+detalle.data[index].precio+"</td><td>"+detalle.data[index].cantidad+"</td><td>"+detalle.data[index].total+"</td></tr>"
                if (detalle.data[index].extras) {
                    for (let j = 0; j < detalle.data[index].extras.length; j++) {
                        var extra= await axios("{{setting('admin.url')}}api/producto/extra/get/"+detalle.data[index].extras[j].extra_id)
                        list+="<tr><td>--> Extra</td><td>"+extra.data.nombre+"</td><td></td><td>"+detalle.data[index].extras[j].precio+"</td><td>"+detalle.data[index].extras[j].cantidad+"</td><td>"+detalle.data[index].extras[j].total+"</td></tr>"
                    }
                }
                if (user.data.role_id==3 && negocio.data.id==detalle.data[index].negocio_id ) {
                    validador=true;
                }
                if (user.data.role_id==4 && mensajero.data.id==pedido.data.mensajero_id) {
                    validador=true;
                }
            }
            if (comentario.data) {
                list+="<tr><td>--* Comentario:</td><td>"+comentario.data.description+"</td><td></td><td></td><td></td><td></td></tr>"
            }

            if (validador) {
                $('.mireload').attr("hidden", true)
                $('#dataTable').append(list);
            }
            else{
                if (user.data.role_id==3) {
                    var url_destino="{{route('mispedidos', 'mivariable')}}"
                    url_destino= url_destino.replace('mivariable', negocio.data.id)
                    location.href=url_destino
                }
            }
            
           
        }
        

     
    </script>
@stop