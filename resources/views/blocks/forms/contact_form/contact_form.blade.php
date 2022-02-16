<form id="contact_form" class="contact_form">
    <div class="contact_form_title">
        <div class="contact_form_title_svg">

        </div>
        <div class="contact_form_title_text">
            Написать нам
        </div>
    </div>

    <div class="contact_form_group">
        @php
            $input_param=[
                "name"=>"name",
                "atr" =>"",
                "form_group_class"=>"",
                "input_class"=>"",
                "title"=>"Имя*",
                "validation" => "required|minlength:3",
                "value" => $user->name ?? '',
            ];
        @endphp
        @include("blocks.input.input")

        @php
            $input_param=[
                "name"=>"email",
                "atr" =>"",
                "form_group_class"=>"",
                "input_class"=>"",
                "title"=>"Email*",
                "validation" => "required|email|maxlength:32",
                "value" => $user->email ?? '',
            ];
        @endphp
        @include("blocks.input.input")

        <div class="form-group" data-validation="required|maxlength:1000">
            <div class="input-container">
                <label class="label textarea_label" for="message">Отзыв*</label>
                <textarea class=" input textarea_input autofill-disable review_input" value="" name="message" type="text"></textarea>
            </div>
            <div class="input_error"></div>
        </div>

        @php
            $btn_param=[
                "name"=>"btn",
                "btn_class"=>"tab_btn btn-submit",
                "text"=>"Отправить"
            ];
        @endphp
        @include("blocks.btn.btn")
    </div>
</form>