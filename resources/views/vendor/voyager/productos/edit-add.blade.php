@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    @php
        $negocio=0;
        $vec=[];
        if (Auth::user()->role_id==3) {
            $negocio= App\Negocio::where('user_id', Auth::user()->id)->with('productos')->first();
            foreach ($negocio->productos as $item) {
                array_push($vec, $item->id);
            }
        }
    @endphp
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}
                        {{-- @if(Auth::user()->id==1 || in_array($dataTypeContent->getKey(), $vec)) --}}
                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            <div class="form-group col-sm-6" id="negocio_lista" hidden>
                                <label class="control-label" for="negocio_select">Negocios</label><br>
                                <select class="form-control select2" name="negocio_select" id="negocio_select" required></select>                               
                            </div>
                            
                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                            {{-- <hr>
                            <div class="form-group col-sm-6" id="laboratorio_lista" hidden >
                                <label class="control-label" for="laboratorio_select">Laboratorio</label><br>
                                <select class="form-control select2" name="laboratorio_select" id="laboratorio_select" ></select>
                            </div> --}}
                            {{-- <div class="form-group col-sm-6" id="farmacia_title" hidden>
                                <label class="control-label" for="titulo_input">Title</label>
                                <input class="form-control" type="text" name="titulo_input" id="titulo_input">
                            </div> --}}
                            {{-- <div class="form-group col-sm-6" id="farmacia_etiqueta" hidden>
                                <label class="control-label" for="etiqueta_input">Etiqueta</label>
                                <input class="form-control" type="text" name="etiqueta_input" id="etiqueta_input">
                            </div> --}}
                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                        {{-- @endif --}}
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

        $('document').ready(function () {
            
            $('.js-example-basic-single').select2();

            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
            name_negocio()
            recargar()
            farmacias()
            cargar_edit()
        });

        async function cargar_edit(){
            var location_actual= window.location.href
            var validacion=location_actual.substr(-4)
            if (validacion=="edit") {
                var producto_id='{{$dataTypeContent->getKey()}}'
                var producto= await axios("{{setting('admin.url')}}api/producto/"+producto_id)
                $('#farmacia_title').val(producto.data.titulo)
                $('#farmacia_etiqueta').val(producto.data.etiqueta)

                recargar_selects_edit(producto.data)
            }
            else{
                cargar_negocios()
                cargar_laboratorios()
            }
         
        }
        async function recargar_selects_edit(producto){
            //Primera parte Negocios
            if (producto.negocio!=null) {
                $('#negocio_select').find('option').remove().end();
                var negocios = await axios.get("{{ setting('admin.url') }}api/all/negocios");
                $('#negocio_select').append($('<option>', {
                    value: producto.negocio.id,
                    text: producto.negocio.nombre
                }));
                for (let index = 0; index < negocios.data.length; index++) {
                    if (producto.negocio.id!=negocios.data[index].id) {
                        $('#negocio_select').append($('<option>', {
                            value: negocios.data[index].id,
                            text: negocios.data[index].nombre
                        }));
                    }
                }
            }
            else{
                cargar_negocios()
            }
          

            //Segunda Parte Laboratorios
            if (producto.laboratorio!=null) {
                $('#laboratorio_select').find('option').remove().end();
                var laboratorios= await axios.get("{{setting('admin.url')}}api/all/laboratorios")
                $('#laboratorio_select').append($('<option>', {
                    value: producto.laboratorio.id,
                    text: producto.laboratorio.name
                }));
                for (let index = 0; index < laboratorios.data.length; index++) {
                    if (producto.laboratorio.id!=laboratorios.data[index].id) {
                        $('#laboratorio_select').append($('<option>', {
                            value: laboratorios.data[index].id,
                            text: laboratorios.data[index].name
                        }));
                    }
                }
            }
            else{
                cargar_laboratorios()
            }
            
        }
        async function name_negocio(){
            if ('{{Auth::user()->role_id}}'==3) {
                var negocio= await axios("{{setting('admin.url')}}api/user/negocio/"+'{{Auth::user()->id}}')
                $('input[name="negocio_id"]').attr("readonly", true)
                $('input[name="negocio_id"]').val(negocio.data.id)

            }
            else{
                $('#negocio_lista').attr("hidden", false)
                $('input[name="negocio_id"]').attr("readonly", true)

            }
          
        }
        async function farmacias(){
            if ('{{Auth::user()->role_id}}'==3) {
                var negocio= await axios("{{setting('admin.url')}}api/user/negocio/"+'{{Auth::user()->id}}')
                if (negocio.data.tipo_id==2) {
                    $('#laboratorio_lista').attr("hidden", false)
                    $('#farmacia_title').attr("hidden", false)
                    $('#farmacia_etiqueta').attr("hidden", false)
                }
            }
            if ('{{Auth::user()->role_id}}'==1 || '{{Auth::user()->role_id}}'==5) {
                $('#laboratorio_lista').attr("hidden", false)
                $('#farmacia_title').attr("hidden", false)
                $('#farmacia_etiqueta').attr("hidden", false)
            }
        }

        async function cargar_laboratorios(){
            $('#laboratorio_select').find('option').remove().end();
            var laboratorios= await axios.get("{{setting('admin.url')}}api/all/laboratorios")
            $('#laboratorio_select').append($('<option>', {
                value: 0,
                text: 'Elige un Laboratorio'
            }));
            for (let index = 0; index < laboratorios.data.length; index++) {
                $('#laboratorio_select').append($('<option>', {
                    value: laboratorios.data[index].id,
                    text: laboratorios.data[index].name
                }));
            }

        }

        async function cargar_negocios(){
          
            $('#negocio_select').find('option').remove().end();

            var negocios = await axios.get("{{ setting('admin.url') }}api/all/negocios");
            $('#negocio_select').append($('<option>', {
                value: 0,
                text: 'Elige un Negocio'
            }));
            for (let index = 0; index < negocios.data.length; index++) {
                $('#negocio_select').append($('<option>', {
                    value: negocios.data[index].id,
                    text: negocios.data[index].nombre
                }));
            }
        }
        $('#negocio_select').on('change', function() {
            $('input[name="negocio_id"]').val(this.value);
        });

        $('#laboratorio_select').on('change', function() {
            $('input[name="laboratorio_id"]').val(this.value);
        });

        $('#titulo_input').on('change', function() {
            $('input[name="titulo"]').val(this.value);
        });
        $("#titulo_input").keyup(function(){
            $('input[name="titulo"]').val(this.value);
        });
        $('#etiqueta_input').on('change', function() {
            $('input[name="etiqueta"]').val(this.value);
        });
        $("#etiqueta_input").keyup(function(){
            $('input[name="etiqueta"]').val(this.value);
        });

        async function recargar(){
            var user_id= '{{Auth::user()->id}}'
            var user= await axios("{{setting('admin.url')}}api/user/"+user_id)
            if (user.data.role_id==3) {
                var negocio= await axios("{{setting('admin.url')}}api/user/negocio/"+user_id)
                var url_actual=window.location.href
                var prueba= url_actual.substring(43, 49)
                var aux=prueba.split('/')
                var id_producto= parseInt(aux[0])
                var url_create="{{setting('admin.url')}}admin/productos/create"
                var productos=[]
                for (let index = 0; index < negocio.data.productos.length; index++) {
                    productos.push(negocio.data.productos[index].id)
                }
                if (productos.indexOf(id_producto)==-1 && url_actual!=url_create) {
                    location.href="{{setting('admin.url')}}admin/productos/"+negocio.data.productos[0].id+"/edit"
                }
                
            }
        }
    </script>
@stop
