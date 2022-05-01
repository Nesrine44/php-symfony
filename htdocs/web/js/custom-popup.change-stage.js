var change_stage_ajax = null;


function change_stage_project() {
    $('#popup-select-change-stage').removeClass('error');
    $('.element-change-stage-popup .error-input-text').remove();
    var data = {};
    data['id'] = $('#detail-id-innovation').val();
    var the_innovation = getInnovationById(data['id']);
    var title = the_innovation['title'];
    var hasError = false;
    var action = $('#popup-select-change-stage').val();
    data['action'] = action;
    data['token'] = $('#hub-token').val();
    if (!action || action == '') {
        $('#popup-select-change-stage').addClass('error');
        $('#popup-select-change-stage').next().after('<div class="error-input-text">This field is required</div>');
        hasError = true;
    }
    if (!hasError) {
        enable_load_popup();
        if (change_stage_ajax != null)
            change_stage_ajax.abort();
        change_stage_ajax = $.ajax({
            url: '/api/innovation/change-stage',
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response, statut) {
                disable_load_popup();
                if (response.status == 'error') {
                    $('#' + response['error_id']).addClass('error').after('<div class="error-input-text">' + response.message + '</div>');
                } else {
                    close_popup();
                    if (action == 'delete') {
                        removeFromCollection($('#detail-id-innovation').val());
                        goToManageList();
                        var message = title + ' has been correctly deleted';
                        add_flash_message('success', message);
                    } else {
                        if (response.hasOwnProperty('full_data')) {
                            updateFromCollection(response['full_data']['id'], response['full_data']);
                        }
                        var message = 'Stage updated';
                        add_flash_message('success', message);
                        goToExploreDetailPage($('#detail-id-innovation').val(), 'edit');
                    }
                }
            },
            error: function (resultat, statut, e) {
                ajax_on_error(resultat, statut, e);
                disable_load_popup();
                if (e != 'abort') {
                    //add_flash_message('error', e);
                }
            }
        });
    }
}

function templateOptionSelectChangeStage(state){
    if (!state.id) {
        return state.text;
    }
    var $state = $('<span class="custom-option-change-stage"><span class="icon light '+string_to_classe(state.text)+'"></span><span class="text">' + state.text + '</span></span>');
    return $state;
}
$(document).ready(function () {
    if (!$('#popup-select-change-stage').data('select2')) {
        $('#popup-select-change-stage').select2({
            minimumResultsForSearch: -1,
            width: '100%',
            placeholder: "Select an action",
            allowClear: false,
            templateResult: templateOptionSelectChangeStage,
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    }
    
    $(document).on('click', '.button-change-stage', function (e) {
        e.stopImmediatePropagation();
        change_stage_project();
    });

    $(document).on('click', '.button-action-frozen', function (e) {
        e.stopImmediatePropagation();
        if (!$('.custom-content-popup.opened').hasClass('loading')) {
            enable_load_popup();
            var the_id = $(this).attr('data-id');
            var the_innovation = getInnovationById(the_id);
            var value = (the_innovation['is_frozen']) ? '0' : '1';
            var data = {};
            data['id'] = $(this).attr('data-id');
            data['name'] = 'is_frozen';
            data['value'] = value;
            explore_update_inline_quarterly_data(data, 'is_frozen');
        }
    });

    $('#popup-select-change-stage').change(function(){
        var value = returnStageById($(this).val());
        if(value) {
            var value_class = value.toCssClass();
            $(this).parent().find('.icon').removeClass().addClass('icon light ' + value_class);
        }
    });
});