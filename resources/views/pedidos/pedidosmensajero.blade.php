@extends('voyager::master')

@section('css')
@stop
@section('page_header')
@php
    $user=TCG\Voyager\Models\User::find($id);
@endphp
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-helm"></i>Pedidos llevados por  {{$user->name}}
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
                            <div id="search-input">
                                <div class="col-4">
                                    <select id="search_key" name="key" style="width: 300px" class="form-control js-example-basic-single">
                                            <option value="id">Buscar por ID</option>
                                            <option value="reporte_diario">Ventas por Fecha</option>
                                            <option value="listar_ventas">Listado</option>            
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select id="filter" name="filter" class="form-control js-example-basic-single">
                                            <option value="equals"> = </option>
                                            {{-- <option value="contains">LIKE</option> --}}
                                    </select>
                                </div>
                                <div class="col-4">
                                    <input class="form-control" type="search"  id="s" name="s" >
                                </div>
                            </div>
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Creado</th>
                                        <th>Cliente</th>
                                        <th>Mensajero</th>
                                        <th>Total(Bs) Productos</th>
                                        <th>Total(Bs) Delivery</th>
                                        <th>Cantidad Negocios</th>
                                        <th>Pasarela</th>
                                        <th>Estado</th>
                                        <th>Ubicacion</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-primary fade" tabindex="-1" id="modal_reportes" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                   <h4 class="modal-title">Ventas por Fechas</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="">Fecha Inicial</label>
                            <input class="form-control" type="date" name="date1" id="date1">
                        </div>
                        <div class="col-sm-6">
                            <label for="">Fecha Final</label>
                            <input class="form-control" type="date" name="date2" id="date2">
                        </div>
                        <div class="col-sm-6">
                            <button onclick="ReporteDiario('{{$id}}')" class="btn btn-primary">Generar</button>
                        </div>
                       
                        
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home22">Resumen</a></li>
                    </ul>
                    
                    <div class="tab-content">
                        <div id="home22" class="tab-pane fade in active">
                            <table class="table table-responsive" id="report_table">
                                <tbody></tbody>
                            </table>
                        </div>
                
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-primary fade" tabindex="-1" id="modal_lista" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                   <h4 class="modal-title">Listar Ventas por Fechas</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="">Fecha Inicial</label>
                            <input class="form-control" type="date" name="date1Lista" id="date1Lista">
                        </div>
                        <div class="col-sm-6">
                            <label for="">Fecha Final</label>
                            <input class="form-control" type="date" name="date2Lista" id="date2Lista">
                        </div>
                        <div class="col-sm-6">
                            <button onclick="Listar()" class="btn btn-primary">Listar</button>
                        </div>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home22">Resumen</a></li>
                        {{-- <li><a data-toggle="tab" href="#menu11">Listado</a></li> --}}
                    </ul>
                    
                    
                </div>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $('document').ready(function () {
            recargar()
            $('.js-example-basic-single').select2();
            ListarDefecto()
        });
        async function recargar(){
            var user_id= '{{Auth::user()->id}}'
            var user= await axios("{{setting('admin.url')}}api/user/"+user_id)
            if (user.data.role_id==4) {
                var mensajero= await axios("{{setting('admin.url')}}api/find/mensajero/"+user.data.id)
               
                var url_destino="{{route('pedidosmensajero', 'mivariable')}}"
                url_destino= url_destino.replace('mivariable', mensajero.data.id)
                var url_actual=window.location.href  
                if (url_destino!=url_actual) {
                    location.href=url_destino
                }
                else{
                    $('.mireload').attr("hidden", true)
                }
            }
        }

        async function pedidos(fecha_inicial, fecha_final){
            var id='{{$id}}'
            var pedido_id=[]
            $("#dataTable tbody tr").remove();
            var mitable=""
            var fecha_ini= fecha_inicial
            var fecha_fin= fecha_final
            //Pedir solo Pedidos del día
            var midata = JSON.stringify({
                date1: fecha_ini,
                date2: fecha_fin,
                mensajero_id: id
            })
            console.log(id)

            var pedido= await axios("{{setting('admin.url')}}api/pedido/mensajero/"+midata)
            for (let index = 0; index < pedido.data.length; index++) {
                mitable+="<tr><td>"+pedido.data[index].id+"</td><td>"+pedido.data[index].fecha+"</td><td>"+pedido.data[index].cliente.nombre+"</td><td>"+pedido.data[index].mensajero.nombre+"</td><td>"+pedido.data[index].total+"</td><td>"+pedido.data[index].total_delivery+"</td><td>"+pedido.data[index].negocios+"</td><td>"+pedido.data[index].pasarela.title+"</td><td>"+pedido.data[index].estado.nombre+"</td><td>"+pedido.data[index].ubicacion.detalles+"</td><td><a class='btn btn-success' href='{{setting('admin.url')}}admin/pedidos/midetalle/"+pedido.data[index].id+"'>Detalles</a></td></tr>"           
            }
            console.log(pedido.data)
            console.log(fecha_ini)
            console.log(fecha_fin)
            
            $('#dataTable').append(mitable);
        }
        $('#search_key').on('change', async function() {
            $('.js-example-basic-single').select2();
            switch (this.value) {
                case 'id':
                    break;
                case 'reporte_diario':
                    $('#modal_reportes').modal();
                    
                    break;
                case 'listar_ventas':
                    $('#modal_lista').modal();

                    break;
                default:
                    //Declaraciones ejecutadas cuando ninguno de los valores coincide con el valor de la expresión
                    break
            }
        });
        $('#s').keypress(async function(event) {
            if ( event.which == 13 ) {
                var id= $('#s').val()
                var pedido= await axios("{{setting('admin.url')}}api/find/pedido/"+id)
                var user_id= '{{Auth::user()->id}}'
                var user= await axios("{{setting('admin.url')}}api/user/"+user_id)
                var mensajero= await axios("{{setting('admin.url')}}api/search/mensajero/"+user.data.id)
                if (pedido.data.length!=0) {
                    if (pedido.data.mensajero_id==mensajero.data.id) {
                        $("#dataTable tbody tr").remove();
                        var mitable=""
                        mitable+="<tr><td>"+pedido.data[0].id+"</td><td>"+pedido.data[0].fecha+"</td><td>"+pedido.data[0].cliente.nombre+"</td><td>"+pedido.data[0].mensajero.nombre+"</td><td>"+pedido.data[0].total+"</td><td>"+pedido.data[0].total_delivery+"</td><td>"+pedido.data[0].negocios+"</td><td>"+pedido.data[0].pasarela.title+"</td><td>"+pedido.data[0].estado.nombre+"</td><td>"+pedido.data[0].ubicacion.detalles+"</td><td><a class='btn btn-success' href='{{setting('admin.url')}}admin/pedidos/midetalle/"+pedido.data[0].id+"'>Detalles</a><a class='btn btn-warning' href='{{setting('admin.url')}}admin/comentarios?key=pedido_id&filter=equals&s="+pedido.data[0].id+"'>Comentarios</a></td></tr>"
                        $('#dataTable').append(mitable);

                    }
                    else{
                        // pb.info(
                        //     'El Pedido solicitado no fue llevado por tu persona.'
                        // );
                        toastr.error("El Pedido solicitado no fue llevado por tu persona.")
                    }
                   
                }
                else{
                    // pb.info(
                    //         'El Pedido Solicitado no se encuentra'
                    //     );
                    toastr.error("El Pedido solicitado no se encuentra.")

                }
               
            }
        });
        async function ReporteDiario(id){
            // report_table
            var midata1 = $("#date1").val()
            var midata2 = fechaFinal($("#date2").val())
            
            var midata = JSON.stringify({
                date1: midata1,
                date2: midata2,
                mensajero_id: id
            })

            var table= await axios("{{setting('admin.url')}}api/ventas/fechas/mensajero/"+midata)
            // var total_negocio= (parseFloat(table.data.total_efectivo)-(parseFloat(table.data.total_efectivo)*0.02))+(parseFloat(table.data.total_banipay)-(parseFloat(table.data.total_banipay)*0.04))
            // var total_negocio=Math.round(total_negocio)
            // var total_godelivery=parseFloat(table.data.total)-total_negocio
          
            $('#report_table tbody tr').remove();
            $('#report_table').append("<tr><td>Total Ventas Bs: </td><td> "+table.data.total+"</td></tr>");
            $('#report_table').append("<tr><td>Total Cantidad de Ventas: </td><td> "+table.data.cantidad_total+"</td></tr>");
            $('#report_table').append("<tr><td>Ventas en Efectivo Bs: </td><td> "+table.data.total_efectivo+"</td></tr>");
            $('#report_table').append("<tr><td>Cantidad de Ventas en Efectivo: </td><td> "+table.data.cantidad_efectivo+"</td></tr>");
            $('#report_table').append("<tr><td>Ventas con Banipay Bs: </td><td> "+table.data.total_banipay+"</td></tr>");
            $('#report_table').append("<tr><td>Cantidad de Ventas en Banipay: </td><td> "+table.data.cantidad_banipay+"</td></tr>");
            $('#report_table').append("<tr><td>Total a Pagar al Mensajero: </td><td>"+table.data.total_delivery+"</td></tr>");
            $('#report_table').append("<tr><td>Total para GoDelivery: </td><td>"+table.data.total_negocio+"</td></tr>");

               
        }

        function sumarDias(fecha, dias){
            fecha.setDate(fecha.getDate() + dias);
            return fecha;
        }

        function fechaFinal(midata2) {
            var dia=""
            var mes=""
            var year=""

            var fecha = new Date(midata2);
            var fecha_funcion=sumarDias(fecha, 2)
            var dia=((fecha_funcion).getDate()).toString()
            dia= dia.padStart(2,'0')
            
            var mes=((fecha_funcion).getMonth()+1).toString()
            mes= mes.padStart(2,'0') 

            var year=((fecha_funcion).getFullYear()).toString()
            
            var fecha_salida=""
            fecha_salida= year+"-"+mes+"-"+dia
            return fecha_salida;
        }

        function fechaDefectoPedidos(validador) {
            if (validador) {
                var dia=""
                var mes=""
                var year=""

                var fecha = new Date();
                var dia=((fecha).getDate()).toString()
                dia= dia.padStart(2,'0')
                
                var mes=((fecha).getMonth()+1).toString()
                mes= mes.padStart(2,'0') 

                var year=((fecha).getFullYear()).toString()
                
                var fecha_salida=""
                fecha_salida= year+"-"+mes+"-"+dia
                return fecha_salida;
            }
            else{
                var dia=""
                var mes=""
                var year=""

                var fecha = new Date();
                var fecha_funcion=sumarDias(fecha, 2)
                var dia=((fecha_funcion).getDate()).toString()
                dia= dia.padStart(2,'0')
                
                var mes=((fecha_funcion).getMonth()+1).toString()
                mes= mes.padStart(2,'0') 

                var year=((fecha_funcion).getFullYear()).toString()
                
                var fecha_salida=""
                fecha_salida= year+"-"+mes+"-"+dia
                return fecha_salida;
            }
        }
        async function Listar(){
            var fecha_inicial = $("#date1Lista").val()
            var fecha_final = fechaFinal($("#date2Lista").val())
            pedidos(fecha_inicial, fecha_final)

        }
        function ListarDefecto(){
            var fecha_inicial = fechaDefectoPedidos(1)
            var fecha_final = fechaDefectoPedidos(0)
            pedidos(fecha_inicial, fecha_final)

        }

    </script>
@stop