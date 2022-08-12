@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if(!empty($dataType->order_column) && !empty($dataType->order_display_column))
                <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan


        @switch($dataType->getTranslatedAttribute('slug'))
            @case('cocinas')

            @break
            @default
                @foreach($actions as $action)
                    @if (method_exists($action, 'massAction'))
                        @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
                    @endif
                @endforeach
                @include('voyager::multilingual.language-selector')
        @endswitch
       
    </div>
@stop

<!-- ---------------------BOBY-------------------  -->
<!-- ---------------------BODY-------------------  -->
@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            @switch($dataType->getTranslatedAttribute('slug'))
                                @case('pedidos')
                                    <form method="get" class="form-search">
                                        <div id="search-input">
                                            <div class="col-4">
                                                <select id="search_key" name="key" style="width: 250px" class="js-example-basic-single">
                                                        <option value=""> ---- Elige un Filtro ----</option>
                                                        <option value="id"> Pedido </option>
                                                        <option value="cliente_id"> Cliente </option>
                                                        <option value="mensajero_id"> Mensajero </option>
                                                        <option value="reporte_diario"> Reporte Diario </option>
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select id="filter" name="filter">
                                                        <option value="equals"> = </option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="s" id="s" class="form-control" onchange="this.form.submit()">
                                            </div>
                                        </div>
                                    </form>
                                @break
                                @default
                                    <form method="get" class="form-search">
                                        <div id="search-input">
                                            <div class="col-2">
                                                <select id="search_key" name="key">
                                                    @foreach($searchNames as $key => $name)
                                                        <option value="{{ $key }}" @if($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)) selected @endif>{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select id="filter" name="filter">
                                                    <option value="contains" @if($search->filter == "contains") selected @endif>contains</option>
                                                    <option value="equals" @if($search->filter == "equals") selected @endif>=</option>
                                                </select>
                                            </div>
                                            <div class="input-group col-md-12">
                                                <input type="text" class="form-control" placeholder="{{ __('voyager::generic.search') }}" name="s" value="{{ $search->value }}">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-info btn-lg" type="submit">
                                                        <i class="voyager-search"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        @if (Request::has('sort_order') && Request::has('order_by'))
                                            <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                            <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                                        @endif
                                    </form>
                            @endswitch
                        @endif
                        
                        @switch($dataType->getTranslatedAttribute('slug'))
                            @case('pedidos')
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                @if($showCheckboxColumn)
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
                                                <th class="actions text-right dt-not-orderable">{{ __('voyager::generic.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dataTypeContent as $data)
                                            @php
                                                $detalle= App\PedidoDetalle::where('pedido_id', $data->id)->with('negocio')->get();
                                                $users=[];
                                                foreach ($detalle as $item) {
                                                    array_push($users, $item->negocio->user_id);
                                                }
                                                $user=0;
                                                if ($data->mensajero_id!=null) {
                                                    $mensajero=App\Mensajero::find($data->mensajero_id);
                                                    $user=$mensajero->user_id;
                                                }
                                                
                                            @endphp
                                            @if(in_array(Auth::user()->id, $users)||($user==Auth::user()->id))
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
                                            @elseif(Auth::user()->id==1)
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
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @break
                            @case('negocios')
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                @if($showCheckboxColumn)
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
                                                <th class="actions text-right dt-not-orderable">{{ __('voyager::generic.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dataTypeContent as $data)
                                                @if (Auth::user()->id==1)
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
                                                @else
                                                    @php
                                                        $negocio= App\Negocio::where('user_id', Auth::user()->id)->first();
                                                    @endphp
                                                    @if ($negocio->id==$data->id) 
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
                                                    @endif  
                                                @endif    
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @break
                      
                            @default
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                @if($showCheckboxColumn)
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
                                                <th class="actions text-right dt-not-orderable">{{ __('voyager::generic.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dataTypeContent as $data)
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
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                        @endswitch

                        
                        @if ($isServerSide)
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------------------MODAL-------------------  -->
    <!-- ---------------------MODAL-------------------  -->

    {{-- Ejemplo de Modal --}}
    <div class="modal modal-primary fade" tabindex="-1" id="reporte_diario" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                   <h4>Reporte Diario</h4>
                </div>
                <div class="modal-body">
                    <div id="tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">General</a></li>
                            {{-- <li role="presentation" ><a href="#historial" aria-controls="historial" role="tab" data-toggle="tab">Historial</a></li>
                            <li role="presentation" ><a href="#cobro" aria-controls="cobro" role="tab" data-toggle="tab">Cobrar</a></li> --}}
                        </ul>
                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane active" id="home">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="">Fecha Inicial</label>
                                        <input class="form-control" type="date" name="date1" id="date1">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="">Fecha Final</label>
                                        <input class="form-control" type="date" name="date2" id="date2">
                                    </div>
                                    {{-- <div class="col-sm-6">
                                        <strong>Elija una Sucursal</strong>
                                        <select name="" id="sucursal_consulta" class="form-control" data-width="100%"></select>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Elija un Cliente</strong>
                                        <select name="" id="cliente_consulta" class="form-control"></select>
                                    </div> --}}
                                    <div class="col-sm-12 text-center">
                                        <button type="button" class="btn btn-dark" onclick="Consultar()"> <i class="voyager-search"></i> Consultar</button>
                                        <table class="table" id="table_reporte_diario">
                                            <thead>
                                                <tr>
                                                    <th>Negocio</th>
                                                    <th>Pedidos</th>
                                                    <th>Productos</th>
                                                    <th>Total</th>
                                                    <th>Delivery</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- <div role="tabpanel" class="tab-pane" id="historial">

                                <div class="col-sm-12">
                                    <table class="table" id="table_historial">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Venta</th>
                                                <th>Cliente</th>
                                                <th>Deuda</th>
                                                <th>Cuota</th>
                                                <th>Restante a Pagar</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="cobro">
                                <div class="col-sm-12">
                                    <table class="table" id="table_cobros">
                                        <thead>
                                            <tr>
                                                <th>Venta</th>
                                                <th>Cliente</th>
                                                <th>Deuda Inicial</th>
                                                <th>Restante a Pagar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>

                                    </table>
                                    <div class="form-group col-md-4 text-center">
                                        <form class="form-horizontal" role="form">
                                            <label class="radio-inline"> <input type="radio" name="season" id="" value="1" checked> Pago En Efectivo </label>
                                            <label class="radio-inline"> <input type="radio" name="season" id="" value="0"> Pago en Línea </label>
                                        </form>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="">Cuota</label>
                                        <input class="form-control" type="number" value="0" min="0" placeholder="Ingrese Monto" id="cuota_cobro">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <input class="form-control" type="hidden" placeholder="Ingrese Venta" id="venta_id">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <input class="form-control" type="hidden" placeholder="Ingrese Deuda" id="deuda">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <input class="form-control" type="hidden" placeholder="Ingrese Cliente" id="cliente_id">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <input class="form-control" type="hidden" placeholder="Ingrese texto Cliente" id="cliente_text">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <input class="form-control" type="hidden" placeholder="Ingrese Restante" id="restante">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-dark" onclick="ActualizarCredito()">Guardar</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('css')
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif
@stop

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    @php
        $mislug =  $dataType->getTranslatedAttribute('slug');
    @endphp
    <script>
        $(document).ready(function () {
            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => $orderColumn,
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [
                            ['targets' => 'dt-not-orderable', 'searchable' =>  false, 'orderable' => false],
                        ],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1]), true)) }}"></a>');
                    }else{
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0]), true)) }}"></a>');
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
        $('input[name="row_id"]').on('change', function () {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });

        @switch($mislug)
            @case('pedidos')
                $('#search_key').on('change', async function() {
                    $('.js-example-basic-single').select2();


                    switch (this.value) {

                        case 'reporte_diario':
                            $('#table_reporte_diario tbody tr').remove()
                            $('#reporte_diario').modal()
                            // $('#s').find('option').remove().end();

                            // var tabla= await axios("{{setting('admin.url')}}api/pos/proveedores");

                            // $('#s').append($('<option>', {
                            //     value: null,
                            //     text: 'Elige un Proveedor'
                            // }));
                            // for (let index = 0; index < tabla.data.length; index++) {
                            //     $('#s').append($('<option>', {
                            //         value: tabla.data[index].id,
                            //         text: tabla.data[index].name
                            //     }));
                            // }

                        break;

                        

                    }
                });
                async function Consultar(){
                    $('#table_reporte_diario tbody tr').remove()
                    var midata1 = $("#date1").val()
                    var midata2 = $("#date2").val()
                    // if(midata1==''){
                    //     var midata = JSON.stringify({
                    //         date1: midata2
                    //     })
                    //     var pedidos=await axios("{{setting('admin.url')}}api/fecha/unica/pedidos/"+midata)
                    //     Consulta2(pedidos)
                    // }
                    // else if(midata2==''){
                    //     var midata = JSON.stringify({
                    //         date1: midata1
                    //     })
                    //     var pedidos=await axios("{{setting('admin.url')}}api/fecha/unica/pedidos/"+midata)
                    //     Consulta2(pedidos)
                    // }
                     if ((midata1!='')&&(midata2!='')) {
                        var midata = JSON.stringify({
                            date1: midata1,
                            date2: midata2,
                        })
                        var pedidos= await axios("{{setting('admin.url')}}api/fecha/doble/pedidos/"+midata)
                        Consulta2(pedidos)
                    }
                    else if ((midata1=='')&&(midata2=='')) {
                        toastr.error("Coloque una Fecha o un Rango de Fecha para su Reporte")
                    }
                }
                async function Consulta2(pedidos){
                    //console.log(pedidos.data)
                    var negocios= await axios.get("{{setting('admin.url')}}api/all/negocios")
                    for (let index = 0; index < negocios.data.length; index++) {
                        var pedido_id=0;
                        var cantidad_pedidos=0;
                        var aux_cantidad=0;
                        var productos=0;
                        var total=0;
                        var delivery=0;
                        var aux_delivery=0

                        for (let j = 0; j < pedidos.data.length; j++) {
                            for (let k = 0; k < pedidos.data[j].productos.length; k++) {
                                if(negocios.data[index].id==pedidos.data[j].productos[k].negocio_id){
                                    pedido_id=pedidos.data[j].id
                                    productos+=pedidos.data[j].productos[k].cantidad
                                    total+=pedidos.data[j].productos[k].cantidad*pedidos.data[j].productos[k].precio
                                    aux_cantidad=1
                                    aux_delivery=1
                                }
                            }
                            cantidad_pedidos+=aux_cantidad
                            delivery+= parseFloat(aux_delivery) *parseFloat("{{setting('pedidos.comision_delivery_san_pablo')}}")                            
                            pedido_id=0
                            aux_cantidad=0
                            aux_delivery=0
                        }
                        if(cantidad_pedidos>0){
                            // console.log("Negocio: "+negocios.data[index].nombre)
                            // console.log("Pedidos: "+cantidad_pedidos)
                            // console.log("Productos: "+productos)
                            // console.log("Total: "+total)
                            // console.log("Delivery: "+delivery)
                            $('#table_reporte_diario').append("<tr><td>"+negocios.data[index].nombre+"</td><td>"+cantidad_pedidos+"</td><td>"+productos+"</td><td>"+total+"</td><td>"+delivery+"</td></tr>") 
                        }
                                               
                    }
                }
            @break
            @default
        @endswitch
    </script>
@stop
