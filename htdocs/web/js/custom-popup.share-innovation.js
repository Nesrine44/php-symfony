function share_innovation_form_validation(){
    $('#form-share-innovation .form-element').removeClass('error');
    $('#form-share-innovation .form-element-error').remove();
    $('#form-share-innovation .error-message-share-innovation').html('');
    var isValid = true;
    var $firstError = null;
    var $form = $('#form-share-innovation');
    var $peopleField = $form.find('select[name=people]');

    if ($peopleField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $peopleField : $firstError;
        errorInput($peopleField);
    }
    if(!isValid){
        scroll_to_popup_element($firstError);
    }

    return isValid;
}

function share_innovation_clear_form(){
    $('#form-share-innovation .form-element').removeClass('error');
    $('#form-share-innovation .form-element-error').remove();
    $('#form-share-innovation .error-message-share-innovation').html('');
    $('#form-share-innovation select[name=people]').val('').trigger("change");
    $('#form-share-innovation textarea[name=message]').val('');
    $('#form-share-innovation-id').val('');
}


$(document).ready(function () {
    $('#form-share-innovation-people').select2({
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
        }
    });

    var share_innovation_request = null;
    $(document).on('submit', '#form-share-innovation', function (e) {
        e.preventDefault();

        if (!share_innovation_form_validation()) {
            return false;
        }

        enable_load_popup();

        if (share_innovation_request !== null) {
            share_innovation_request.abort();
        }
        var fields = {};
        $(this).serializeArray().forEach(function(o) {
            fields[o.name] = o.value;
        });
        fields['token'] = $('#hub-token').val();
        var innovation_id = fields['innovation_id'];
        var target_username = $('#form-share-innovation .people-select option[value='+fields['people']+']').text();
        share_innovation_request = $.ajax({
            url: '/api/innovation/share',
            type: 'POST',
            data: fields,
            dataType: 'json',
            success: function(response, status) {
                disable_load_popup();
                if(response.hasOwnProperty('status') && response['status'] == 'error'){
                    $('#form-share-innovation .error-message-share-innovation').html(response['message']);
                }else{
                    explore_initVisualExplorePitchous(innovation_id);
                    close_popup();
                    var message = 'Innovation successfully shared.<br>A mail has been sent to '+target_username+'.';
                    add_flash_message('success', message);
                }
            },
            error: function(response, status, e) {
                ajax_on_error(response, status, e);
                console.log('form-share-innovation submit', e);
                disable_load_popup();
                $(this).find('.error-message-share-innovation').html(response.status+' '+e);
            }
        });
        return false;
    });
});