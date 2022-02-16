@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

{{-- ANCEDIT --}} {{-- CSS from post/edit-add.blade --}}
@section('css')
    <style>
        .panel .mce-panel {
            border-left-color: #fff;
            border-right-color: #fff;
        }

        .panel .mce-toolbar,
        .panel .mce-statusbar {
            padding-left: 20px;
        }

        .panel .mce-edit-area,
        .panel .mce-edit-area iframe,
        .panel .mce-edit-area iframe html {
            padding: 0 10px;
            min-height: 350px;
        }

        .mce-content-body {
            color: #555;
            font-size: 14px;
        }

        .panel.is-fullscreen .mce-statusbar {
            position: absolute;
            bottom: 0;
            width: 100%;
            z-index: 200000;
        }

        .panel.is-fullscreen .mce-tinymce {
            height:100%;
        }

        .panel.is-fullscreen .mce-edit-area,
        .panel.is-fullscreen .mce-edit-area iframe,
        .panel.is-fullscreen .mce-edit-area iframe html {
            height: 100%;
            position: absolute;
            width: 99%;
            overflow-y: scroll;
            overflow-x: hidden;
            min-height: 100%;
        }
    </style>
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
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                {{--<div class="panel panel-bordered">--}}
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

                        {{-- ANCEDIT ID2 --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Adding / Editing --> {{-- ANCEDIT ID1 --}}
                        @php
                            $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                        @endphp


                        {{-- ANDEDIT --}}
                        {{-- Split panels --}}
                        @php
                            // Get panel options from first data row in bread (it is id almost always)
                            $panels_options = \TCG\Voyager\Models\DataRow::query()->where('data_type_id', $dataType->id)->first()->details;

                            if(isset($panels_options->panels)){
                                // Prepare panels
                                $col_right_empty = true;
                                $panels = [];
                                $panels_left = [];
                                $panels_right = [];
                                $panel_index = 0;
                                foreach ($panels_options->panels as $panel_option){
                                    $panels[$panel_option->id] = $panel_option;
                                    $panel_index++;
                                }
                                $panel_unknown = new stdClass();
                                $panel_unknown->id = "panel_unknown";
                                $panel_unknown->name = "Additional Details";
                                $panel_unknown->order = 99;
                                $panel_unknown->position = "left";
                                $panel_unknown_index = $panel_unknown->id;
                                $panels[$panel_unknown_index] = $panel_unknown;

                                // Split rows to panels
                                foreach($dataTypeRows as $row){
                                    $row_options = $row->details;
                                    if(isset($row_options->panel_id)){
                                        if(isset($panels[$row_options->panel_id])){
                                            $panels[$row_options->panel_id]->rows[] = $row;
                                            if($panels[$row_options->panel_id]->position === "right"){
                                                $col_right_empty = false;
                                            }
                                        }else{
                                            $panels[$panel_unknown_index]->rows[] = $row;
                                        }
                                    }else{
                                        $panels[$panel_unknown_index]->rows[] = $row;
                                    }
                                }

                                // Order panels
                                /*
                                $order_by = 'order';
                                usort($panels, function ($a, $b) use ($order_by)
                                {
                                    return strcmp($a->{$order_by}, $b->{$order_by});
                                });
                                */

                            }else{
                                // Panels not exists -> put in Additional Details
                                $col_right_empty = true;
                                $panel_unknown = new stdClass();
                                $panel_unknown->id = "panel_unknown";
                                $panel_unknown->name = "Additional Details";
                                $panel_unknown->order = 99;
                                $panel_unknown->position = "left";
                                $panel_unknown_index = $panel_unknown->id;
                                $panels[$panel_unknown_index] = $panel_unknown;
                                foreach($dataTypeRows as $row){
                                    $panels[$panel_unknown_index]->rows[] = $row;
                                }
                            }

                        //dd($panels);
                        @endphp

                        {{-- Output rows by panels --}}
                        <div class="row">
                            {{-- LEFT --}}
                            <div class="@if($col_right_empty) col-md-12 @else col-md-8 @endif">
                                @foreach($panels as $panel)
                                    @if($panel->position === 'left')
                                        @include('vendor.voyager.formfields._panel', ['panel' => $panel])
                                    @endif
                                @endforeach
                            </div>
                            {{-- RIGHT --}}
                            @if(!$col_right_empty)
                            <div class="col-md-4">
                                @foreach($panels as $panel)
                                    @if($panel->position === 'right')
                                        @include('vendor.voyager.formfields._panel', ['panel' => $panel])
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="panel-body">

                            {{-- ANCEDIT MOVED TO ID2 --}}

                            {{-- ANCEDIT MOVED TO ID1 --}}

                            {{-- ANCEDIT MOVED TO ID3 (panel) --}}

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                {{--</div>--}}
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

            // Media picker - remove li on X click
            $('[id*="media_picker"]').on('click', '.voyager-x', function () {
                $(this).closest('.dd-item').fadeOut(100);
            });

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
        });
    </script>
@stop
