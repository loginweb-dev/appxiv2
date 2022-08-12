@extends('voyager::master')

@section('css')
@stop
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-helm"></i>Pedidos de {{$negocio->nombre}}
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
                        {{-- <form method="get" class="form-search"> --}}
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

                            {{-- @if (Request::has('sort_order') && Request::has('order_by'))
                                <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                            @endif --}}
                        {{-- </form> --}}
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

                                        {{-- @if($showCheckboxColumn)
                                            <th class="dt-not-orderable">
                                                <input type="checkbox" class="select_all">
                                            </th>
                                        @endif
                                        @foreach($dataType->browseRows as $row)
                                        <th>
                                            @if ($isServerSide && in_array($row->field, $sortableColumns))
                                                <a href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">
                                            @endif
                                            {{ $row->getTranslatedAttribute('display_name') }}
                                            @if ($isServerSide)
                                                @if ($row->isCurrentSortField($orderBy))
                                                    @if ($sortOrder == 'asc')
                                                        <i class="voyager-angle-up pull-right"></i>
                                                    @else
                                                        <i class="voyager-angle-down pull-right"></i>
                                                    @endif
                                                @endif
                                                </a>
                                            @endif
                                        </th>
                                        @endforeach
                                        <th class="actions text-right dt-not-orderable">{{ __('voyager::generic.actions') }}</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($dataTypeContent as $data)
                                    <tr>
                                        @if($showCheckboxColumn)
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                                            </td>
                                        @endif
                                        @foreach($dataType->browseRows as $row)
                                            @php
                                            if ($data->{$row->field.'_browse'}) {
                                                $data->{$row->field} = $data->{$row->field.'_browse'};
                                            }
                                            @endphp
                                            <td>
                                                @if (isset($row->details->view))
                                                    @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $data->{$row->field}, 'action' => 'browse', 'view' => 'browse', 'options' => $row->details])
                                                @elseif($row->type == 'image')
                                                    <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                                                @elseif($row->type == 'relationship')
                                                    @include('voyager::formfields.relationship', ['view' => 'browse','options' => $row->details])
                                                @elseif($row->type == 'select_multiple')
                                                    @if(property_exists($row->details, 'relationship'))

                                                        @foreach($data->{$row->field} as $item)
                                                            {{ $item->{$row->field} }}
                                                        @endforeach

                                                    @elseif(property_exists($row->details, 'options'))
                                                        @if (!empty(json_decode($data->{$row->field})))
                                                            @foreach(json_decode($data->{$row->field}) as $item)
                                                                @if (@$row->details->options->{$item})
                                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ __('voyager::generic.none') }}
                                                        @endif
                                                    @endif

                                                    @elseif($row->type == 'multiple_checkbox' && property_exists($row->details, 'options'))
                                                        @if (@count(json_decode($data->{$row->field})) > 0)
                                                            @foreach(json_decode($data->{$row->field}) as $item)
                                                                @if (@$row->details->options->{$item})
                                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ __('voyager::generic.none') }}
                                                        @endif

                                                @elseif(($row->type == 'select_dropdown' || $row->type == 'radio_btn') && property_exists($row->details, 'options'))

                                                    {!! $row->details->options->{$data->{$row->field}} ?? '' !!}

                                                @elseif($row->type == 'date' || $row->type == 'timestamp')
                                                    @if ( property_exists($row->details, 'format') && !is_null($data->{$row->field}) )
                                                        {{ \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($row->details->format) }}
                                                    @else
                                                        {{ $data->{$row->field} }}
                                                    @endif
                                                @elseif($row->type == 'checkbox')
                                                    @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                                        @if($data->{$row->field})
                                                            <span class="label label-info">{{ $row->details->on }}</span>
                                                        @else
                                                            <span class="label label-primary">{{ $row->details->off }}</span>
                                                        @endif
                                                    @else
                                                    {{ $data->{$row->field} }}
                                                    @endif
                                                @elseif($row->type == 'color')
                                                    <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
                                                @elseif($row->type == 'text')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                @elseif($row->type == 'text_area')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    @if(json_decode($data->{$row->field}) !== null)
                                                        @foreach(json_decode($data->{$row->field}) as $file)
                                                            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                                                                {{ $file->original_name ?: '' }}
                                                            </a>
                                                            <br/>
                                                        @endforeach
                                                    @else
                                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}" target="_blank">
                                                            Download
                                                        </a>
                                                    @endif
                                                @elseif($row->type == 'rich_text_box')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
                                                @elseif($row->type == 'coordinates')
                                                    @include('voyager::partials.coordinates-static-image')
                                                @elseif($row->type == 'multiple_images')
                                                    @php $images = json_decode($data->{$row->field}); @endphp
                                                    @if($images)
                                                        @php $images = array_slice($images, 0, 3); @endphp
                                                        @foreach($images as $image)
                                                            <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
                                                        @endforeach
                                                    @endif
                                                @elseif($row->type == 'media_picker')
                                                    @php
                                                        if (is_array($data->{$row->field})) {
                                                            $files = $data->{$row->field};
                                                        } else {
                                                            $files = json_decode($data->{$row->field});
                                                        }
                                                    @endphp
                                                    @if ($files)
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                            <img src="@if( !filter_var($file, FILTER_VALIDATE_URL)){{ Voyager::image( $file ) }}@else{{ $file }}@endif" style="width:50px">
                                                            @endforeach
                                                        @else
                                                            <ul>
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                                <li>{{ $file }}</li>
                                                            @endforeach
                                                            </ul>
                                                        @endif
                                                        @if (count($files) > 3)
                                                            {{ __('voyager::media.files_more', ['count' => (count($files) - 3)]) }}
                                                        @endif
                                                    @elseif (is_array($files) && count($files) == 0)
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @elseif ($data->{$row->field} != '')
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:50px">
                                                        @else
                                                            {{ $data->{$row->field} }}
                                                        @endif
                                                    @else
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @endif
                                                @else
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <span>{{ $data->{$row->field} }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="no-sort no-click bread-actions">
                                            @foreach($actions as $action)
                                                @if (!method_exists($action, 'massAction'))
                                                    @include('voyager::bread.partials.actions', ['action' => $action])
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                        {{-- @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    's' => $search->value,
                                    'filter' => $search->filter,
                                    'key' => $search->key,
                                    'order_by' => $orderBy,
                                    'sort_order' => $sortOrder,
                                    'showSoftDeleted' => $showSoftDeleted,
                                ])->links() }}
                            </div>
                        @endif --}}
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
                            <button onclick="ReporteDiario('{{$negocio->id}}')" class="btn btn-primary">Generar</button>
                        </div>
                        {{-- <div class="col-sm-6">
                            <label for="negocio_id">Negocios</label>
                            <select name="negocio_id" id="negocio_id" class="form-control js-example-basic-single"></select>
                        </div> --}}
                        
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home22">Resumen</a></li>
                        {{-- <li><a data-toggle="tab" href="#menu11">Listado</a></li> --}}
                    </ul>
                    
                    <div class="tab-content">
                        <div id="home22" class="tab-pane fade in active">
                            <table class="table table-responsive" id="report_table">
                                <tbody></tbody>
                            </table>
                        </div>
                        {{-- <div id="menu11" class="tab-pane fade">
                            <table class="table" id="report_list">
                                <thead>
                                    <th>id</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Pasarela</th>
                                    <th>Cliente</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div> --}}
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
            // pedidos()
            ListarDefecto()
        });
        async function recargar(){
            var user_id= '{{Auth::user()->id}}'
            var user= await axios("{{setting('admin.url')}}api/user/"+user_id)
            if (user.data.role_id==3) {
                var negocio= await axios("{{setting('admin.url')}}api/user/negocio/"+user_id)
                
                // var url_destino="{{setting('admin.url')}}admin/mispedidos/"+negocio.data.id
                var url_destino="{{route('mispedidos', 'mivariable')}}"
                url_destino= url_destino.replace('mivariable', negocio.data.id)
                //console.log(url_destino)

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
            var id='{{$negocio->id}}'
            var pedido_id=[]
            $("#dataTable tbody tr").remove();
            var mitable=""
            var fecha_ini= fecha_inicial
            var fecha_fin= fecha_final
            //Pedir solo Pedidos del día
            var midata = JSON.stringify({
                date1: fecha_ini,
                date2: fecha_fin,
                negocio_id: id
            })

            var pedido_detalles= await axios("{{setting('admin.url')}}api/pedido/detalle/negocio/"+midata)
            for (let index = 0; index < pedido_detalles.data.length; index++) {
               
                if (pedido_id.indexOf(pedido_detalles.data[index].pedido.id) === -1) {
                    pedido_id.push(pedido_detalles.data[index].pedido.id)
                    var pedido= await axios("{{setting('admin.url')}}api/find/pedido/"+pedido_detalles.data[index].pedido.id)
                    // mitable+="<tr><td>"+pedido.data[0].id+"</td><td>"+pedido.data[0].fecha+"</td><td>"+pedido.data[0].cliente.nombre+"</td><td>"+pedido.data[0].mensajero.nombre+"</td><td>"+pedido.data[0].total+"</td><td>"+pedido.data[0].total_delivery+"</td><td>"+pedido.data[0].negocios+"</td><td>"+pedido.data[0].pasarela.title+"</td><td>"+pedido.data[0].estado.nombre+"</td><td>"+pedido.data[0].ubicacion.detalles+"</td><td><a class='btn btn-success' href='{{setting('admin.url')}}admin/pedidos/midetalle/"+pedido.data[0].id+"'>Detalles</a><a class='btn btn-warning' href='{{setting('admin.url')}}admin/comentarios?key=pedido_id&filter=equals&s="+pedido.data[0].id+"'>Comentarios</a></td></tr>"
                    mitable+="<tr><td>"+pedido.data[0].id+"</td><td>"+pedido.data[0].fecha+"</td><td>"+pedido.data[0].cliente.nombre+"</td><td>"+pedido.data[0].mensajero.nombre+"</td><td>"+pedido.data[0].total+"</td><td>"+pedido.data[0].total_delivery+"</td><td>"+pedido.data[0].negocios+"</td><td>"+pedido.data[0].pasarela.title+"</td><td>"+pedido.data[0].estado.nombre+"</td><td>"+pedido.data[0].ubicacion.detalles+"</td><td><a class='btn btn-success' href='{{setting('admin.url')}}admin/pedidos/midetalle/"+pedido.data[0].id+"'>Detalles</a></td></tr>"

                }            
            }
            
            $('#dataTable').append(mitable);
        }
        function uArray(array) {
            var out = [];
            for (var i=0, len=array.length; i<len; i++)
                if (out.indexOf(array[i]) === -1)
                    out.push(array[i]);
            return out;
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
                var negocio= await axios("{{setting('admin.url')}}api/user/negocio/"+user_id)

                if (pedido.data.length!=0) {
                    var validador=false
                    for (let index = 0; index < pedido.data.productos.length; index++) {
                        if (pedido.data.productos[index].negocio_id==negocio.data.id) {
                            validador=true
                        }
                    }
                    if (validador) {
                        $("#dataTable tbody tr").remove();
                        var mitable=""
                        mitable+="<tr><td>"+pedido.data[0].id+"</td><td>"+pedido.data[0].fecha+"</td><td>"+pedido.data[0].cliente.nombre+"</td><td>"+pedido.data[0].mensajero.nombre+"</td><td>"+pedido.data[0].total+"</td><td>"+pedido.data[0].total_delivery+"</td><td>"+pedido.data[0].negocios+"</td><td>"+pedido.data[0].pasarela.title+"</td><td>"+pedido.data[0].estado.nombre+"</td><td>"+pedido.data[0].ubicacion.detalles+"</td><td><a class='btn btn-success' href='{{setting('admin.url')}}admin/pedidos/midetalle/"+pedido.data[0].id+"'>Detalles</a><a class='btn btn-warning' href='{{setting('admin.url')}}admin/comentarios?key=pedido_id&filter=equals&s="+pedido.data[0].id+"'>Comentarios</a></td></tr>"
                        $('#dataTable').append(mitable);
                    }
                    else{
                        // pb.info(
                        //     'En el Pedido solicitado tu negocio no estuvo presente.'
                        // );
                        toastr.error("En el Pedido solicitado tu negocio no estuvo presente.")
                    }
                }
                else{
                    // pb.info(
                    //         'El Pedido Solicitado no se encuentra'
                    //     );
                    toastr.error("El Pedido Solicitado no se encuentra.")
                }
               
            }
        });
        async function ReporteDiario(id){
            // report_table
            var midata1 = $("#date1").val()
            var midata2 = fechaFinal($("#date2").val())
            var negocio_id = id
            var midata = JSON.stringify({
                date1: midata1,
                date2: midata2,
                negocio_id: negocio_id
            })

            var table= await axios("{{setting('admin.url')}}api/reporte/fechas/negocio/"+midata)
            console.log(table.data)
            var total_negocio= (parseFloat(table.data.total_efectivo)-(parseFloat(table.data.total_efectivo)*0.02))+(parseFloat(table.data.total_banipay)-(parseFloat(table.data.total_banipay)*0.04))
            var total_negocio=Math.round(total_negocio)
            var total_godelivery=parseFloat(table.data.total)-total_negocio
            $('#report_table tbody tr').remove();
            $('#report_table').append("<tr><td>Total Ventas Bs: </td><td> "+table.data.total+"</td></tr>");
            $('#report_table').append("<tr><td>Total Cantidad de Ventas: </td><td> "+table.data.cantidad_total+"</td></tr>");
            $('#report_table').append("<tr><td>Ventas en Efectivo Bs: </td><td> "+table.data.total_efectivo+"</td></tr>");
            $('#report_table').append("<tr><td>Cantidad de Ventas en Efectivo: </td><td> "+table.data.cantidad_efectivo+"</td></tr>");
            $('#report_table').append("<tr><td>Ventas con Banipay Bs: </td><td> "+table.data.total_banipay+"</td></tr>");
            $('#report_table').append("<tr><td>Cantidad de Ventas en Banipay: </td><td> "+table.data.cantidad_banipay+"</td></tr>");
            $('#report_table').append("<tr><td>Total a Pagar al Negocio: </td><td> "+total_negocio+"</td></tr>");
            $('#report_table').append("<tr><td>Total para GoDelivery: </td><td> "+total_godelivery+"</td></tr>");

               
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