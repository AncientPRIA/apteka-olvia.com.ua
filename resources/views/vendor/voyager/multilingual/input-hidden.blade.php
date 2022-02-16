@if($_field_name === 'field_display_name_eee')
{{dd($_field_trans)}}
@endif
@if ($isModelTranslatable)
    <input type="hidden"
           data-i18n="true"
           name="{{ $_field_name }}_i18n"
           id="{{ $_field_name }}_i18n"
           value="{{ $_field_trans }}">
@endif
