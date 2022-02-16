// To add new rules go to RULES section

export default class Validator {

    constructor(jq_form){
        this.form = jq_form;

        // settings
        this.input_group_class          = 'form-group';         // Class name of input group
        this.input_container_class      = 'input-container';    // Class name of input container
        this.input_group_error_class    = 'error_active';       // Class name added to input group, when validation fails
        this.error_element_class        = 'input_error';        // Class name of error element in each input group
        this.input_error_class          = 'shake';              // Class name added to each input in input group with error (OPTIONAL, leave empty)
        this.general_error_class        = 'general-error';      // Class name of general error element

        if(typeof js_strings === 'undefined'){
            console.log("VALIDATOR: Error! js_strings undefined")
        }
    }

    // Launch validation. Return if error - return true, if correct - return formdata
    validate(){
        var errored = false;
        var this_class = this;
        var input_groups = this.form.find('.' + this.input_group_class);

        input_groups.each(function () {
            var this_input_group = $(this);
            if(!this_class.validate_form_group(this_input_group)){
                errored = true;
            }
        });

        if(errored){
            return false;
        }else{
            return this.get_form_data();
        }
    }

    watch(){
        console.log('Validator watch launched');
        var this_class = this;
        var input_groups = this.form.find('.' + this.input_group_class);

        //console.log(this.form);

        input_groups.each(function () {
            var this_input_group = $(this);

            this_input_group.find('input').on('change', function () {
                let input_group = $(this).closest(`.${this_class.input_group_class}`);
                this_class.validate_form_group(input_group);
            })
        })
    }

    validate_form_group(this_input_group){
        var this_class = this;
        this_input_group.removeClass(this_class.input_group_error_class); // Remove errors from input groups
        //this_class.general_error_hide(); // Hide general error
        if(this_class.input_error_class !== ''){
            this_input_group.find('.' + this_class.input_error_class).removeClass(this_class.input_error_class); // Remove errors from inputs
        }

        // validations
        var is_required = false;
        var validation_types = this_input_group.data('validation');
        if(typeof validation_types !== "undefined" && validation_types !== ''){
            validation_types = validation_types.split('|');
            for (let validation of validation_types){
                var validation_params = [];
                if(validation === 'required'){
                    is_required = true;
                }
                validation_params['is_required'] = is_required;
                if(validation.indexOf(':') > -1){
                    var tmp = validation.split(':');
                    validation = tmp[0];
                    validation_params['validation_param'] = tmp[1];
                }

                console.log(validation);

                if(!this_class['rule_' +validation](this_input_group, validation_params)){
                    return false; // Errored
                }
            }
        }

        return true;
    }

    // Get FormData of this.form
    get_form_data(){
        var form_data = new FormData();
        var this_class = this;
        var input_groups = this_class.form.find('.' + this_class.input_group_class);

        input_groups.each(function () {
            var this_input_group = $(this);

            // switch for type, like input, checkbox, etc.
            switch (true){
                case this_input_group.hasClass('type-checkbox'): // custom checkboxes
                    var checkbox_data = [];
                    var checkboxes = this_input_group.find('.checkbox');
                    if(checkboxes.length > 1){
                        var checkbox_group_key = this_input_group.data('key');
                        //checkbox_data[checkbox_group_key] = [];
                        checkboxes.each(function () {
                            var this_checkbox = $(this);
                            if(this_class.checkbox_selected(this_checkbox)){
                                var name = this_checkbox.data('name');
                                checkbox_data[checkbox_group_key][name] = true;
                            }

                        });
                        form_data.append(checkbox_group_key, checkbox_data);

                    }else{
                        if(this_class.checkbox_selected(checkboxes)){
                            var name = checkboxes.data('name');
                            form_data.append(name, true);
                        }
                    }



                    break;

                default: // default input
                    var inputs = this_input_group.find('.input');
                    inputs.each(function () {
                        var input = $(this);
                        var name = input.attr('name');
                        var value = input.val();
                        form_data.append(name, value);
                    });
                    break;
            }
        });

        return form_data;
    }

    trigger_error(input_group, error_key, params){
        console.log('Error key:', error_key);
        var error = input_group.find('.' + this.error_element_class);
        if(typeof js_strings['error_'+error_key] !== 'undefined'){
            var error_string = this.replace(js_strings['error_'+error_key], params);
        }else{
            console.log('VALIDATOR: Error! error string for key error_'+error_key+' undefined');
            var error_string = 'Error string undefined'
        }
        error.html(error_string);

        input_group.addClass(this.input_group_error_class);
        if(this.input_error_class !== ''){
            var input_element = input_group.find('.input');
            input_element.addClass(this.input_error_class);
        }

    }

    general_error_show(text){
        var gereral_error = this.form.find('.'+this.general_error_class);
        console.log('general_error_show, dom', gereral_error);
        gereral_error.html(text);
        gereral_error.addClass('active');
    }

    general_error_hide(){
        var gereral_error = this.form.find('.'+this.general_error_class);
        gereral_error.removeClass('active');
    }



    // =================== RULES =================== //

    // Template for rule. Copy and rename to rule_{name},
    // where name equal to validation type, i.e., data-validation="required" uses rule_required function
    /*
    rule_tmp(input_group){                              // <- change 'tmp'
        var error_key = 'tmp';                          // <- change 'tmp'. Best to use same 'tmp' as in function (rule_email -> error_key = 'email')
        var input_element = input_group.find('.input');

        var value = input_element.val();
        var regexp = /^$/;                              // <- change regexp or change if statement
        if(regexp.test(value)){
            return true
        }else{
            this.trigger_error(input_group, error_key);
            return false
        }
    }
    */

    rule_required(input_group){
        var error_key = 'required';
        var input_element = input_group.find('.input');

        var value = input_element.val();
        if(value.length > 0){
            return true
        }else{
            this.trigger_error(input_group, error_key);
            return false
        }
    }

    rule_email(input_group, params){
        var error_key = 'email';
        var input_element = input_group.find('.input');
        let is_required = params['is_required'] || false;

        var value = input_element.val();
        if(!is_required && value.length === 0){
            return true
        }
        var regexp = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if(regexp.test(value)){
            return true
        }else{
            this.trigger_error(input_group, error_key);
            return false
        }
    }

    rule_password(input_group, params){
        var error_key = 'password';
        var input_element = input_group.find('.input');
        let is_required = params['is_required'] || false;

        var value = input_element.val();
        if(!is_required && value.length === 0){
            return true
        }
        var regexp = /^[A-Za-z@$!%*#?&\d]+$/; // Any latin letter, numbers, special characters
        if(regexp.test(value)){
            return true
        }else{
            this.trigger_error(input_group, error_key);
            return false
        }
    }


    rule_minlength(input_group, params ){
        var error_key = 'minlength';
        var input_element = input_group.find('.input');
        let is_required = params['is_required'] || false;

        var value = input_element.val();
        if(!is_required && value.length === 0){
            return true
        }
        if(value.length >= params['validation_param']){
            return true
        }else{
            this.trigger_error(input_group, error_key, [params['validation_param']]);
            return false
        }
    }

    rule_maxlength(input_group, params){
        var error_key = 'maxlength';
        var input_element = input_group.find('.input');
        let is_required = params['is_required'] || false;

        var value = input_element.val();
        if(!is_required && value.length === 0){
            return true
        }
        if(value.length <= params['validation_param']){
            return true
        }else{
            this.trigger_error(input_group, error_key, [params['validation_param']]);
            return false
        }
    }

    rule_password_confirmation(input_group, params){
        var error_key = 'password_confirmation';
        var input_element = input_group.find('.input');
        var orig_element = this.form.find('input[name="password"]');
        let is_required = params['is_required'] || false;

        var value = input_element.val();
        if(!is_required && value.length === 0){
            return true
        }
        var orig_value = orig_element.val();
        if(value === orig_value){
            return true
        }else{
            this.trigger_error(input_group, error_key);
            return false
        }
    }

    /*
    rule_phone(input_group, params){
        var error_key = 'password';
        var input_element = input_group.find('.input');
        let is_required = params['is_required'] || false;

        var value = input_element.val();
        if(!is_required && value.length === 0){
            return true
        }
        var regexp = /^[A-Za-z@$!%*#?&\d]+$/; // Any latin letter, numbers, special characters
        if(regexp.test(value)){
            return true
        }else{
            this.trigger_error(input_group, error_key);
            return false
        }
    }
    */

    // =================== RULES END =================== //

    // ==================== HELPERS ==================== //
    // Replace %s to values (array)
    replace(string, values){
        //console.log(values[0]);
        var index = 0;
        return string.replace(/%s/g, function(match, number) {
            index += 1;
            // replace with array[number] or leave %s
            return typeof values[index-1] !== 'undefined'
                ? values[index-1]
                : match
                ;
        });
    }

    checkbox_selected(element){
        if(element.hasClass('active')){
            return true;
        }else{
            return false;
        }
    }
    // ================== HELPERS END ================== //

}