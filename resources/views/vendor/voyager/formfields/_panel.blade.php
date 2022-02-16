


@if(isset($panel->rows))
<div class="panel {{$panel->classes ?? ''}}">
    <div class="panel-heading" style="@if(isset($panel->bgcolor)){{'background-color:'.$panel->bgcolor}}@endif">
        <h3 class="panel-title">@if(!Lang::has('voyager::generic.'.$panel->name)) {{$panel->name}} @else {{__('voyager::generic.'.$panel->name)}} @endif</h3>
        <div class="panel-actions">
            <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @foreach($panel->rows as $row)
            @php
            /*
                $index = $data['index'];
                $field = $data['field'];
                $row = $data['row'];
                $field_type = $row->type;
                $read = $row->read;
                $form_group_classes = '';
                if(isset($row->details->classes)){
                    $form_group_classes .=  $row->details->classes;
                }
                if($field_type === 'acf'){
                    $form_group_classes .= ' acf';
                }
            */
            @endphp

                {{-- ANCEDIT ID3 --}}
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
    </div>
</div>
@endif