/**
 * feedbackRequestFormValidation.
 *
 * @returns {boolean}
 */
function feedbackRequestFormValidation() {
    $('#form-feedback-request .form-element').removeClass('error');
    $('#form-feedback-request .form-element-error').remove();
    $('#form-feedback-request .error-message-feedback-request').html('');
    var isValid = true;
    var $firstError = null;
    var $form = $('form#form-feedback-request');
    var $innovationField = $form.find('select[name=innovation]');
    var $peopleField = $form.find('select[name=people]');
    var $messageField = $form.find('textarea[name=message]');

    if ($peopleField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $peopleField : $firstError;
        errorInput($peopleField);
    }

    if ($messageField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $messageField : $firstError;
        errorInput($messageField);
    }

    if ($innovationField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $innovationField : $firstError;
        errorInput($innovationField);
    }

    if(!isValid){
        scroll_to_popup_element($firstError);
    }

    return isValid;
}

function feedback_request_clear_form_error(){
    $('#form-feedback-request .form-element').removeClass('error');
    $('#form-feedback-request .form-element-error').remove();
    $('#form-feedback-request .error-message-feedback-request').html('');
    $('#form-feedback-request .people-select').val('').trigger("change");
    $('#form-feedback-request .innovation-select').val('').trigger("change");
    $('#form-feedback-request-message').val('');
    if($('.full-content-page-user').length > 0){ // Ask feedback in user profile
        $('#form-feedback-request .form-element-selector-innovation').removeClass('display-none');
        // set user
        var user_id = $('.full-content-page-user').attr('data-user-id');
        $('#form-feedback-request .people-select').val(user_id).trigger("change");
        // Update innovations
        var user_innovations_ids = get_access_manage_innovations_ids_for_user_id(current_user['id']);
        var an_innovation_collection = new DataCollection(allInnovationsDC.query().filter({id__in: user_innovations_ids}).values());
        var out = [], o = -1;
        an_innovation_collection.query().order('proper__title', false).each(function (row, index) {
            out[++o] = get_select_option_html_template(row['id'], row['title']);
        });
        $("#form-feedback-request .innovation-select").html(out.join(''));
        feedback_init_innovation_select_2();
    }else{
        $('#form-feedback-request .form-element-selector-innovation').addClass('display-none');
        // set innovation_id
        var innovation_id = $('#detail-id-innovation').val();
        $("#form-feedback-request .innovation-select").html(get_select_option_html_template(innovation_id, '', true));
        feedback_init_innovation_select_2();
    }
}

function feedback_answer_clear_form_error(){
    $('#form-feedback-answer').find('.content-step-2').addClass("display-none");
    $('#form-feedback-answer').find('.content-step-1').removeClass("display-none");
    $('#form-feedback-answer .form-element').removeClass('error');
    $('#form-feedback-answer .form-element-error').remove();
    $('#form-feedback-answer .error-message-feedback-request').html('');
    $('#form-feedback-answer .role-select').val('').trigger("change");

    $('#form-feedback-answer .rateit').rateit('reset');
    $('#form-feedback-answer input[name=rating]').val('');
    $('#form-feedback-answer textarea[name=message]').val('');

    if(current_user && current_user['proper_role']){
        $('#form-feedback-answer .role-select').val(current_user['proper_role']).trigger("change");
    }
}

function feedbackAnswerStepOneValidation() {
    $('#form-feedback-answer .form-element').removeClass('error');
    $('#form-feedback-answer .form-element-error').remove();
    $('#form-feedback-answer .error-message-feedback-request').html('');
    var isValid = true;
    var $firstError = null;

    $form = $('form#form-feedback-answer');
    $ratingField = $form.find('input[name=rating]');
    $messageField = $form.find('textarea[name=message]');

    if ($ratingField.val() == '') {
        isValid = false;
        $firstError = (!$firstError) ? $ratingField : $firstError;
        errorInput($ratingField);
    }

    if ($messageField.val() == '') {
        isValid = false;
        $firstError = (!$firstError) ? $messageField : $firstError;
        errorInput($messageField);
    }

    if(!isValid){
        scroll_to_popup_element($firstError);
    }

    return isValid;
}

/**
 * feedback_answer_showStepTwo
 * @param $form
 */
function feedback_answer_showStepTwo() {
    $('#form-feedback-answer').find('.content-step-2').removeClass("display-none");
    $('#form-feedback-answer').find('.content-step-1').addClass("display-none");
    $('#feedback-invitation-cta-toast').remove();
}

function feedback_init_innovation_select_2(){
    var $innovation_select  = $("#form-feedback-request .innovation-select");
    if($innovation_select.hasClass('select2-hidden-accessible')) {
        $innovation_select.select2('destroy');
    }
    $innovation_select.select2({
        width: '100%',
        placeholder: "Select innovation",
        allowClear: false
    });
}

(function ($) {
    $(document).ready(function () {
        /* POPUP FEEDBACK REQUEST
        ----------------------------------------------------------------------------------------*/

        $('#form-feedback-request .people-select').select2({
            width: '100%',
            placeholder: "Select people",
            allowClear: false,
            "language": {
                "noResults": function () {
                    return "<a href='" + mail_contact + "' class='btn-no-result-found'>Oops, no result found, please click here to <span>contact us</span></a>";
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            data: get_contactable_users()
        });

        feedback_init_innovation_select_2();


        var feedbackRequestXHR = null;
        $(document).on('submit', '#form-feedback-request', function (e) {
            e.preventDefault();

            if (!feedbackRequestFormValidation()) {
                return false;
            }

            enable_load_popup();

            if (feedbackRequestXHR !== null) {
                feedbackRequestXHR.abort();
            }


            var fields = {};
            $(this).serializeArray().forEach(function(o) {
                fields[o.name] = o.value;
            });
            fields['token'] = $('#hub-token').val();
            var innovation_id = fields['innovation_id'];
            var target_username = $('#form-feedback-request .people-select option[value='+fields['people']+']').text();
            feedbackRequestXHR = $.ajax({
                url: '/api/feedback/invite',
                type: 'POST',
                data: fields,
                dataType: 'json',
                success: function(response, status) {
                    disable_load_popup();
                    if(response.hasOwnProperty('status') && response['status'] == 'error'){
                        $('#form-feedback-request .error-message-feedback-request').html(response['message']);
                    }else{
                        close_popup();
                        var message = 'Your feedback request has been sent to '+target_username+'.';
                        add_flash_message('success', message);
                        if($('.full-content-explore-detail').length > 0) { // Ask feedback in explore detail
                            explore_generate_feedback_tab(innovation_id);
                        }
                    }
                },
                error: function(response, status, e) {
                    ajax_on_error(response, status, e);
                    console.log('form-feedback-request submit', e);
                    disable_load_popup();
                    $(this).find('.error-message-feedback-request').html(response.status+' '+e);
                }
            });
            return false;
        });

        /* POPUP FEEDBACK ANSWER
        ----------------------------------------------------------------------------------------*/

        $feedbackAnswerForm = $('#form-feedback-answer');
        $feedbackAnswerForm.find('.rateit').bind('rated', function() {
            var rating = $(this).rateit('value');
            $feedbackAnswerForm.find('input[name=rating]').val(rating);
        });
        $feedbackAnswerForm.find('.role-select').select2({
            width: '100%',
            placeholder: "Select a role",
            minimumResultsForSearch: Infinity,
            allowClear: true,
        });

        $feedbackAnswerForm.find("input[name=rating]").change(function () {
            if ($(this).val() != "") {
                $(this).parent().parent().removeClass('error');
                $(this).parent().parent().find('.form-element-error').remove();
            }
        });
        $feedbackAnswerForm.find("input[name=message]").change(function () {
            if ($(this).val() != "") {
                $(this).parent().parent().removeClass('error');
                $(this).parent().parent().find('.form-element-error').remove();
            }
        });


        var feedbackAnswerXHR = null;
        $(document).on('submit', '#form-feedback-answer', function (e) {
            $form = $(this);
            e.preventDefault();
            var isValid = feedbackAnswerStepOneValidation();
            if (!isValid) {
                return false;
            }

            $form.find('.error-message-feedback-answer').html('');
            enable_load_popup();
            
            var innovation_id = $('#detail-id-innovation').val();

            if (feedbackAnswerXHR !== null) {
                feedbackAnswerXHR.abort();
            }

            var fields = {};
            $form.serializeArray().forEach(function(o) {
                fields[o.name] = o.value;
            });
            fields['token'] = $('#hub-token').val();
            feedbackAnswerXHR = jQuery.ajax({
                url: '/api/feedback/answer',
                type: 'POST',
                data: fields,
                dataType: 'json',
                success: function(response, status) {
                    disable_load_popup();
                    if(response.hasOwnProperty('status') && response['status'] == 'error'){
                        $('#form-feedback-answer .error-message-feedback-answer').html(response['message']);
                    }else {
                        feedback_answer_showStepTwo();
                    }
                },
                error: function(response, status, e) {
                    if(ajax_on_error(response, status, e)){
                        return;
                    }
                    console.log('error dans  la queue ajax : si le formulaire a été submit, elle a juste été aborted');
                    if (response && response.statusText && response.statusText == 'abort') {
                        return;
                    }else if (status && status == 'abort') {
                        return;
                    }
                    console.log('form-feedback-answer submit', e);
                    disable_load_popup();
                    $form.find('.error-message-feedback-answer').html(response.status+' '+e);
                },
            })
        });
    });
})(jQuery);