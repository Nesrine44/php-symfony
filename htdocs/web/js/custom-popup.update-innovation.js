/**
 * before_open_update_innovation_popup
 */
function before_open_update_innovation_popup() {
    var id = parseInt($('#detail-id-innovation').val());
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    $('#form-update-innovation .form-element').removeClass('error');
    $('#form-update-innovation .form-element-error').remove();
    $('.error-message-update-innovation').html('');
    $('#form-update-innovation input[name="innovation-name"]').val(innovation['title']);
    var entity_id = innovation['entity']['id'];
    var brand_id = innovation['brand']['id'];
    $('#form-update-innovation-entity').val(entity_id).trigger('change');
    $('#form-update-innovation-brand').val(brand_id).trigger('change');
    if (innovation_is_a_service) {
        $('#form-update-innovation .content-only-service').removeClass('display-none');
        if (innovation['is_multi_brand'] && innovation['is_multi_brand'] == '1') {
            $('#update_multi_brand_innovation_yes').prop('checked', true);
            $('#form-update-innovation .content-brand-name').addClass('display-none');
            $('#form-update-innovation .content-have_a_mother_brand').addClass('display-none');
        } else {
            $('#update_multi_brand_innovation_no').prop('checked', true);
            if (innovation['new_to_the_world']) {
                $('#update_have_a_mother_brand_no').prop('checked', true);
                $('#form-update-innovation .content-brand-name').addClass('display-none');
            } else {
                $('#update_have_a_mother_brand_yes').prop('checked', true);
                $('#form-update-innovation .content-brand-name').removeClass('display-none');
            }
        }
    } else {
        $('#form-update-innovation .content-only-service').addClass('display-none');
        if (innovation['new_to_the_world']) {
            $('#update_have_a_mother_brand_no').prop('checked', true);
            $('#form-update-innovation .content-brand-name').addClass('display-none');
        } else {
            $('#update_have_a_mother_brand_yes').prop('checked', true);
            $('#form-update-innovation .content-brand-name').removeClass('display-none');
        }
    }
}

/**
 * before_open_update_innovation_contact_popup
 */
function before_open_update_innovation_contact_popup() {
    var id = parseInt($('#detail-id-innovation').val());
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }
    $('#form-update-innovation-contact .form-element').removeClass('error');
    $('#form-update-innovation-contact  .form-element-error').remove();
    $('.error-message-update-innovation-contact').html('');
    var contact_id = innovation['contact']['uid'];
    $('#form-update-contact').val(contact_id).trigger('change');
}

function check_if_update_form_is_valid() {
    var step_1_is_valid = true;
    var $first_error = null;

    var is_service = ($("#form-update-innovation input[name=is_a_service]:checked").val() == "1");
    var is_multi_brand = ($("#form-update-innovation input[name=multi_brand_innovation]:checked").val() == "1");

    var $field_innovation_name = $("#form-update-innovation input[name=innovation-name]");
    if ($field_innovation_name.val() == '') {
        step_1_is_valid = false;
        $first_error = (!$first_error) ? $field_innovation_name : $first_error;
        errorInput($field_innovation_name);
    }

    var $field_brand_name = $('#form-update-innovation select[name=brand-name]');
    var have_a_mother_brand = $("#form-update-innovation input[name=have_a_mother_brand]:checked").val();
    if ((!is_service || (is_service && !is_multi_brand)) && have_a_mother_brand == '1' && ($field_brand_name.val() == '' || !$field_brand_name.val())) {
        step_1_is_valid = false;
        $first_error = (!$first_error) ? $field_brand_name : $first_error;
        errorInput($field_brand_name);
    }
    if (!step_1_is_valid) {
        scroll_to_popup_element($first_error);
    }
    return step_1_is_valid;
}

function check_if_update_contact_form_is_valid() {
    var step_1_is_valid = true;
    var $first_error = null;

    var $field_contact = $("#form-update-innovation select[name=contact]");
    if ($field_contact.val() == '') {
        step_1_is_valid = false;
        $first_error = (!$first_error) ? $field_contact : $first_error;
        errorInput($field_contact);
    }

    if (!step_1_is_valid) {
        scroll_to_popup_element($first_error);
    }
    return step_1_is_valid;
}


$(document).ready(function () {
    if ($('#form-update-innovation-entity').length > 0 && $('#form-update-innovation-entity').hasClass('team-entity-select')) {
        $('#form-update-innovation-entity').select2({
            width: '100%',
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
    }

    $('#form-update-contact').select2({
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

    $('#form-update-innovation .brand-select').select2({
        width: '100%',
        placeholder: "Enter the name of the brand",
        allowClear: true,
        "language": {
            "noResults": function () {
                return "<a href='" + mail_contact + "' class='btn-no-result-found'>Oops, no result found, please click here to <span>contact us</span></a>";
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    $('.update_have_a_mother_brand').change(function () {
        if ($(this).val() == '1') {
            $('#form-update-innovation .content-brand-name').removeClass('display-none');
        } else {
            $('#form-update-innovation .content-brand-name').addClass('display-none');
        }
    });

    $('.update_multi_brand_innovation').change(function () {
        if ($(this).val() == '1') {
            $('#form-update-innovation .content-have_a_mother_brand').addClass('display-none');
            $('#form-update-innovation .content-brand-name').addClass('display-none');
        } else {
            $('#form-update-innovation .content-have_a_mother_brand').removeClass('display-none');
            $('#form-update-innovation .content-brand-name').removeClass('display-none');
        }
    });

    var updateInnovationXHR = null;
    $(document).on('submit', '#form-update-innovation', function (e) {
        e.preventDefault();

        var id = parseInt($('#detail-id-innovation').val());
        var innovation = getInnovationById(id, true);
        if (!innovation) {
            return false;
        }
        $('#form-update-innovation .form-element').removeClass('error');
        $('#form-update-innovation .form-element-error').remove();
        $('.error-message-update-innovation').html('');

        if (!check_if_update_form_is_valid()) {
            return false;
        }
        var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
        var data = {};
        var changes = false;
        data['id'] = id;
        data['token'] = $('#hub-token').val();
        var new_title = $('#form-update-innovation input[name="innovation-name"]').val();
        if (new_title != innovation['title']) {
            data['title'] = new_title;
            changes = true;
        }
        var new_entity = $('#form-update-innovation-entity').val();
        if (new_entity != innovation['entity']['id']) {
            data['entity'] = new_entity;
            changes = true;
        }
        var new_have_a_mother_brand = ($("#form-update-innovation input[name=have_a_mother_brand]:checked").val() == '0');
        if (new_have_a_mother_brand != innovation['new_to_the_world']) {
            var value = (new_have_a_mother_brand) ? '1' : '0';
            data['new_to_the_world'] = value;
            changes = true;
        }
        var new_brand = $('#form-update-innovation-brand').val();
        if (new_brand != innovation['brand']['id']) {
            data['brand'] = new_brand;
            changes = true;
        }
        if (innovation_is_a_service) {
            var new_is_multi_brand = ($("#form-update-innovation input[name=multi_brand_innovation]:checked").val() == "1");
            if (new_is_multi_brand != innovation['is_multi_brand']) {
                var value = (new_is_multi_brand) ? '1' : '0';
                data['is_multi_brand'] = value;
                changes = true;
            }
        }
        if (changes) {
            enable_load_popup();
            if (updateInnovationXHR !== null) {
                updateInnovationXHR.abort();
            }
            updateInnovationXHR = $.ajax({
                url: '/api/innovation/update-form',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response, status) {
                    disable_load_popup();
                    if(response.hasOwnProperty('status') && response['status'] == 'error'){
                        $('#form-update-innovation .error-message-update-innovation').html(response['message']);
                    }else{
                        close_popup();
                        if (response.hasOwnProperty('full_data')) {
                            updateFromCollection(response['full_data']['id'], response['full_data']);
                            explore_detail_update_nb_missing_fields();
                            var id = $('#detail-id-innovation').val();
                            goToExploreDetailPage(id, 'edit');
                        }
                    }
                },
                error: function(response, status, e) {
                    ajax_on_error(response, status, e);
                    disable_load_popup();
                    $('#form-update-innovation .error-message-update-innovation').html(response.status+' '+e);
                }
            });
        }else{
            close_popup();
        }
        return false;
    });


    $(document).on('submit', '#form-update-innovation-contact', function (e) {
        e.preventDefault();

        var id = parseInt($('#detail-id-innovation').val());
        var innovation = getInnovationById(id, true);
        if (!innovation) {
            return false;
        }
        $('#form-update-innovation-contact .form-element').removeClass('error');
        $('#form-update-innovation-contact .form-element-error').remove();
        $('.error-message-update-innovation-contact').html('');

        if (!check_if_update_contact_form_is_valid()) {
            return false;
        }
        var data = {};
        var changes = false;
        data['token'] = $('#hub-token').val();
        data['id'] = id;
        var new_contact = $('#form-update-innovation-contact select[name="contact"]').val();
        if (new_contact != innovation['contact']['uid']) {
            data['contact'] = new_contact;
            changes = true;
        }
        if (changes) {
            enable_load_popup();
            if (updateInnovationXHR !== null) {
                updateInnovationXHR.abort();
            }
            updateInnovationXHR = $.ajax({
                url: '/api/innovation/update-form',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response, status) {
                    disable_load_popup();
                    if(response.hasOwnProperty('status') && response['status'] == 'error'){
                        $('#form-update-innovation-contact .error-message-update-innovation-contact').html(response['message']);
                    }else{
                        close_popup();
                        if (response.hasOwnProperty('full_data')) {
                            updateFromCollection(response['full_data']['id'], response['full_data']);
                            explore_detail_update_nb_missing_fields();
                            var id = $('#detail-id-innovation').val();
                            goToExploreDetailPage(id, 'edit');
                        }
                    }
                },
                error: function(response, status, e) {
                    ajax_on_error(response, status, e);
                    disable_load_popup();
                    $('#form-update-innovation .error-message-update-innovation-contact').html(response.status+' '+e);
                }
            });
        }else{
            close_popup();
        }
        return false;
    });

});