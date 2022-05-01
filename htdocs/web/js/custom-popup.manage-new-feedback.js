/**
 * Validates the recommandation/feedback form in manage.
 *
 * @returns {boolean}
 */
function manageNewFeedbackValidation() {

    var isValid = true;
    var $firstError = null;
    var $form = $('form#form-manage-new-feedback-popup');
    var $recommandationField = $form.find('textarea[name=recommandation]');
    var $feedbackField = $form.find('textarea[name=feedback]');

    $form.find('.form-element').removeClass('error');
    $form.find('.form-element-error').remove();
    $form.find('.error-message').html('');

    if ($recommandationField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $recommandationField : $firstError;
        errorInput($recommandationField);
    }

    if ($feedbackField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $feedbackField : $firstError;
        errorInput($feedbackField);
    }

    if(!isValid){
        scroll_to_popup_element($firstError);
    }

    return isValid;
}

function manage_new_feedback_clear_form_error(){
    var $form = $('form#form-manage-new-feedback-popup');
    $form.find('.content-step-1').removeClass("display-none");
    $form.find('.form-element').removeClass('error');
    $form.find('.form-element-error').remove();
    $form.find('.error-message').html('');

    $form.find('textarea[name=recommandation]').val('');
    $form.find('textarea[name=feedback]').val('');
}

/**
 * after_open_manage_new_feedback_popup
 */
function after_open_manage_new_feedback_popup(){
    $('#form-manage-new-feedback-popup textarea[name="recommandation"]').height(0);
    if($('#form-manage-new-feedback-popup textarea[name="recommandation"]').val() != ''){
        $('#form-manage-new-feedback-popup textarea[name="recommandation"]').height($('#form-manage-new-feedback-popup textarea[name="recommandation"]')[0].scrollHeight);
    }
    $('#form-manage-new-feedback-popup textarea[name="feedback"]').height(0);
    if($('#form-manage-new-feedback-popup textarea[name="feedback"]').val() != ''){
        $('#form-manage-new-feedback-popup textarea[name="feedback"]').height($('#form-manage-new-feedback-popup textarea[name="feedback"]')[0].scrollHeight);
    }

}


(function ($) {
    $(document).ready(function () {

        var requestXHR = null;
        $(document).on('submit', '#form-manage-new-feedback-popup', function (e) {
            e.preventDefault();

            $form = $(this);
            
            if (!manageNewFeedbackValidation()) {
                return false;
            }

            enable_load_popup();

            if (requestXHR !== null) {
                requestXHR.abort();
            }


            var fields = {};
            $form.serializeArray().forEach(function(o) {
                fields[o.name] = o.value;
            });
            fields['token'] = $('#hub-token').val();
            requestXHR = $.ajax({
                url: ''.concat('/api/manage/md/recommandation'),
                type: 'POST',
                data: fields,
                dataType: 'json',
                success: function(response, status) {
                    disable_load_popup();
                    if(response.hasOwnProperty('status') && response['status'] == 'error'){
                        $form.find('.error-message').html(response['message']);
                    }else{
                        close_popup();
                        var message = 'Thank you for your feedback.';
                        add_flash_message('success', message);
                        manage_md_generate_recommandation();
                    }
                },
                error: function(response, status, e) {
                    ajax_on_error(response, status, e);
                    console.log('form-manage-new-feedback-popup submit', e);
                    disable_load_popup();
                    $form.find('.error-message').html(response.status+' '+e);
                }
            });
            return false;
        });
    });
})(jQuery);