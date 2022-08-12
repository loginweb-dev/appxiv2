@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        
    </style>
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
{{-- <div class="container-fluid">
    <h1 class="page-title" title="Cliente selecionado">
        <i class="voyager-person"></i> 
        Cliente Generico / 00000000 
    </h1>
    <a href="#" class="btn btn-xs btn-dark" title="Buscar Cliente">
        <i class="voyager-search"></i>
    </a>
    <a href="#" class="btn btn-xs btn-dark" title="Panel Touch">
        <i class="voyager-tv"></i>
    </a>
</div> --}}
    {{-- @include('voyager::multilingual.language-selector') --}}
@stop

@section('content')

    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
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
                        @php
                            $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                        @endphp
                        <div id="voyager-loader" class="mireload">
                            <?php $admin_loader_img = Voyager::setting('admin.loader', ''); ?>
                            @if($admin_loader_img == '')
                                <img src="{{ voyager_asset('images/logo-icon.png') }}" alt="Voyager Loader">
                            @else
                                <img src="{{ Voyager::image($admin_loader_img) }}" alt="Voyager Loader">
                            @endif
                        </div>
                        <div id="mibody"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            main()
            $(".mireload").attr("hidden",true);
        });

        function clientes(){
            $(".mireload").attr("hidden", false);
            $.ajax({
                url: "{{ route('ajax.ventas.clientes') }}",
                dataType: "html",
                success: function (response) {
                    $("#mibody").html(response)
                    $(".mireload").attr("hidden",true);
                }
            });
        }
        function main(){
            $(".mireload").attr("hidden", false);
            $.ajax({
                url: "{{ route('ajax.ventas.main') }}",
                dataType: "html",
                success: function (response) {
                    $("#mibody").html(response)
                    $(".mireload").attr("hidden",true);
                }
            });
        }
        function pogoview(){
            $(".mireload").attr("hidden", false);
            $.ajax({
                url: "{{ route('ajax.ventas.pago') }}",
                dataType: "html",
                success: function (response) {
                    $("#mibody").html(response)
                    $(".mireload").attr("hidden",true);
                }
            });
        }
    </script>
@stop
