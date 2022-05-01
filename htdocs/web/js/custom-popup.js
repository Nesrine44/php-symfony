/**
 * Open popup.
 *
 * @param target
 * @param functionBefore
 * @param functionAfter
 */
function open_popup(target, functionBefore, functionAfter) {
    if($(target).length <= 0){
        return;
    }
    var timeout_delay = 0;
    if($('html').hasClass('popup-opened')){
        var close_target = $('html').attr('data-popup-target');
        close_popup(close_target);
        timeout_delay = 320;
    }
    setTimeout(function(){
        $('html').addClass('popup-opened').attr('data-popup-target', target);
        before_open_popup(target);
        if (typeof functionBefore != "undefined") {
            functionBefore();
        }
        $(target).addClass('opened opening').removeClass('loading');
        $(".custom-content-popup.opened .content-popup-for-scroll").animate({scrollTop: 0}, 300);
        setTimeout(function(){
            if (typeof functionAfter != "undefined") {
                functionAfter();
            }
            after_open_popup(target);
            $(target).removeClass('opening');
        }, 300);
    }, timeout_delay);
}

/**
 * Close popup.
 *
 * @param target
 * @param functionBefore
 * @param functionAfter
 */
function close_popup(target, functionBefore, functionAfter) {
    target = (typeof target != "undefined") ? target :  $('html').attr('data-popup-target');
    if(!$('html').hasClass('popup-opened') || $(target).length <= 0){
        return;
    }
    before_close_popup(target);
    if (typeof functionBefore != "undefined") {
        functionBefore();
    }
    $(target).addClass('closed');
    setTimeout(function(){
        $(target).removeClass('closed opened');
        $('html').removeClass('popup-opened').removeAttr('data-popup-target');
        after_close_popup(target);
        if (typeof functionAfter != "undefined") {
            functionAfter();
        }
    }, 300);
}

/**
 * Close popup or back.
 *
 */
function close_or_back_popup(target) {
    target = (typeof target != "undefined") ? target :  $('html').attr('data-popup-target');
    if(!$('html').hasClass('popup-opened') || $('.custom-content-popup.opened').hasClass('loading')){
        return false;
    }
    if($('.custom-content-popup.opened .back-on-popup').length > 0 && $('.custom-content-popup.opened .back-on-popup').is(":visible")){
        back_on_popup(target);
    }else{
        if(target == '.element-walkthrough-start-popup'){
            hide_walkthrough_blur();
        }
        close_popup(target);
    }
}

/**
 * Back on popup
 * @param target
 */
function back_on_popup(target) {
    target = (typeof target != "undefined") ? target :  $('html').attr('data-popup-target');
    if($('.custom-content-popup.opened .back-on-popup').length > 0 && $('.custom-content-popup.opened .back-on-popup').is(":visible")){
        if(target == '.element-consumer-opportunity-popup') {
            back_on_consumer_opportunity_popup();
        }else if(target == '.element-new-innovation-popup') {
            var current_step = $('.element-new-innovation-popup').attr('data-current-step');
            if(current_step == 'step-out-of-scope'){
                var previous_step = 'step-2';
            } else if(current_step == 'step-success'){
                var previous_step = 'step-4';
            }else{
                var previous_step = 'step-'+(parseInt(current_step.split('-')[1]) -1);
            }
            go_to_new_innovation_step(previous_step);
        }
    }
}

/**
 * before_open_popup.
 * @param target
 */
function before_open_popup(target){
    if(target == '.element-change-stage-popup'){
        before_open_change_stage_popup();
    }else if(target == '.element-consumer-opportunity-popup'){
        before_open_consumer_opportunity_popup();
    }else if(target == '.element-popup-new-user-innovation-right'){
        before_open_add_user_innovation_right_popup();
    }else if(target == '.element-new-innovation-popup'){
        before_open_new_innovation_popup();
    }else if(target == '.element-feedback-request-popup'){
        before_open_feedback_request_popup();
    }else if(target == '.element-feedback-answer-popup'){
        before_open_feedback_answer_popup();
    }else if(target == '.element-share-innovation-popup'){
        before_open_share_innovation_popup();
    }else if(target == '.element-manage-new-feedback-popup'){
        before_open_manage_new_feedback_popup();
    }else if(target == '.element-manage-new-competitor-popup'){
        manage_new_competitor_clear_form_error();
    }else if(target == '.element-tab-activities-view-all-popup'){
        before_open_tab_activities_view_all_popup();
    }else if(target == '.element-update-innovation-popup'){
        before_open_update_innovation_popup();
    }else if(target == '.element-update-innovation-contact-popup'){
        before_open_update_innovation_contact_popup();
    }else if(target == '.element-search-popup'){
        before_open_search_popup();
    }else if(target == '.element-popup-new-key-city'){
        before_open_add_key_city_popup();
    }else if(target == '.element-popup-open-question'){
        before_open_open_question_popup();
    }
}

/**
 * after_open_popup.
 * @param target
 */
function after_open_popup(target){
    if(target == '.element-video-popup'){
        after_open_video_popup();
    } else if(target == '.element-manage-new-feedback-popup'){
        after_open_manage_new_feedback_popup();
    }
}

/**
 * before_close_popup.
 * @param target
 */
function before_close_popup(target){
    if(target == '.element-export-popup'){
        on_cancel_export();
    }else if(target == '.element-video-popup'){
        before_close_video_popup();
    }else if(target == '.element-search-popup'){
        before_close_search_popup();
    }
}

/**
 * after_close_popup.
 * @param target
 */
function after_close_popup(target){
    if(target == '.element-change-stage-popup'){
        after_close_change_stage_popup();
    }else if(target == '.element-consumer-opportunity-popup'){
        after_close_consumer_opportunity_popup();
    }else if(target == '.element-popup-new-user-innovation-right'){
        after_close_add_user_innovation_right_popup();
    }else if (target == '.element-popup-update-user-innovation-right'){
        after_close_update_user_innovation_right_popup();
    }else if(target == '.element-walkthrough-end-popup'){
        hide_walkthrough_blur();
    }else if(target == '.element-popup-delete-skill'){
        $('.element-popup-delete-skill .button-delete-skill').removeAttr('data-skill-id');
        $('.element-popup-delete-skill .button-delete-skill').removeAttr('data-user-id');
    }else if(target == '.element-popup-new-key-city'){
        after_close_add_key_city_popup();
    }else if(target == '.element-canvas-popup'){
        opened_canvas = null;
        $('.element-canvas-popup').removeAttr('data-innovation_id');
        $('.element-canvas-popup').removeAttr('data-canvas_id');
    }
}

/**
 * enable_load_popup
 */
function enable_load_popup(){
    $('.custom-content-popup.opened').addClass('loading');
    $('.element-canvas-popup.opened textarea').prop('disabled', true);
    $('.custom-content-popup.opened .hub-button').prop('disabled', true);
}

/**
 * disable_load_popup
 */
function disable_load_popup(){
    $('.custom-content-popup.opened').removeClass('loading');
    $('.element-canvas-popup.opened textarea').prop('disabled', false);
    $('.custom-content-popup.opened .hub-button').prop('disabled', false);
}


/**
 * Scroll vers l'element
 * @param $element
 */
function scroll_to_popup_element($element) {
    var elementTop = $element.offset().top - 30;
    var scrollTop = $(".custom-content-popup.opened .element-popup").offset().top;
    var scroll_to = (elementTop - scrollTop);
    $(".custom-content-popup.opened .content-popup-for-scroll").animate({
        scrollTop: scroll_to
    }, 300);
}


/* Popup Export
 ----------------------------------------------------------------------------------------*/

/**
 * Cancel export.
 */
function on_cancel_export(){
    if (request_export != null)
        request_export.abort();
    reset_async_progress_by_export_id();
}

/* Popup Change Stage
 ----------------------------------------------------------------------------------------*/

/**
 * before_open_change_stage_popup.
 */
function before_open_change_stage_popup(){
    var the_id = $('#detail-id-innovation').val();
    var the_innovation = getInnovationById(the_id);
    var current_stage_id = the_innovation['current_stage_id'];
    $('#popup-select-change-stage').val(current_stage_id).trigger('change');
    $('.button-action-frozen').attr('data-id', the_id);
    if (the_innovation['is_frozen']) {
        $('.popup-terminate-project-not-frozen').addClass('display-none');
        $('.popup-terminate-project-frozen').removeClass('display-none');
    } else {
        $('.popup-terminate-project-not-frozen').removeClass('display-none');
        $('.popup-terminate-project-frozen').addClass('display-none');
    }
}

/**
 * after_close_change_stage_popup.
 */
function after_close_change_stage_popup(){
    $('#popup-select-change-stage').removeClass('error').val('6').trigger('change');
    $('.element-change-stage-popup .error-input-text').remove();
}

/* Popup Consumer Opportunity
 ----------------------------------------------------------------------------------------*/

/**
 * before_open_consumer_opportunity_popup.
 */
function before_open_consumer_opportunity_popup(){
    $('.element-consumer-opportunity-popup .back-on-popup').hide().attr('data-step', '').removeAttr('data-selectable-values');
    $('.element-consumer-opportunity-popup .cross-close-popup').show();
    $('.element-consumer-opportunity-popup .content-modale-main').attr('data-step', 0).attr('data-selection', '[]').html(consumerOpportunityStep0Html());
    opti_update_mouseflow('/helper-consumer-opportunity?step=first-page');
}

/**
 * back_on_consumer_opportunity_popup.
 */
function back_on_consumer_opportunity_popup(){
    var $back_on_popup = $('.element-consumer-opportunity-popup .back-on-popup');
    var $modale_main = $('.element-consumer-opportunity-popup .content-modale-main');
    var $modale_detail = $('.element-consumer-opportunity-popup .content-modale-detail');
    if (!$('.confirm-consumer-opportunity-selection').hasClass('loading')) {
        if ($modale_detail.is(":visible")) {
            $modale_main.slideToggle(120);
            $modale_detail.slideToggle(120);
            $('.element-consumer-opportunity-popup .main-title').html('FIND YOUR CONSUMER OPPORTUNITY');
        } else {
            if ($back_on_popup.attr('data-old-step') !== undefined && $modale_main.attr('data-step') == 777) {
                var next_step = parseInt($back_on_popup.attr('data-old-step'));
            } else {
                var next_step = parseInt($back_on_popup.attr('data-step'));
            }
            if (next_step < 5) {
                var step_back = next_step - 1;
                $back_on_popup.attr('data-step', step_back);
            } else if (next_step == 777) {
                $back_on_popup.attr('data-step', 4);
            }
            $modale_main.attr('data-step', next_step).attr('data-selection', JSON.stringify(getReverseSelectionByStep(next_step)));
            displayConsumerOpportunityStep(next_step);
        }
    }
}

/**
 * after_close_consumer_opportunity_popup.
 */
function after_close_consumer_opportunity_popup(){
    // Will fire when popup is closed
    var current_url = window.location.href;
    opti_update_mouseflow(current_url, get_current_tab_target());
}

/* Popup Add add_user_innovation_right or update_user_innovation_right_popup
 ----------------------------------------------------------------------------------------*/

/**
 * before_open_add_user_innovation_right_popup.
 */
function before_open_add_user_innovation_right_popup(){
    if (!$('#new-ui-role-select').data('select2')) {
        $('#new-ui-role-select').select2({
            minimumResultsForSearch: -1,
            width: '100%',
            placeholder: "Select a role",
            allowClear: false,
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        $('#new-ui-role-select').val('Other').trigger('change');
    }
    if (!$('#new-ui-right-uid-select').data('select2')) {
        $("#new-ui-right-uid-select").select2({
            width: '100%',
            placeholder: "Choose a member",
            "language": {
                "noResults": function () {
                    return "<div class='btn-no-result-found btn-create-user-uir cursor-pointer'>Oops, we didn’t found this name. <span class='ui-right-action-create-new-member'>Create a new member</span>.</div>";
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            data: other_datas['contacts_json']
        });
        $('#new-ui-right-uid-select').select2('val', '');
        $('.element-popup-new-user-innovation-right .choose-select-member').removeClass('display-none');
        $('.element-popup-new-user-innovation-right .choose-create-member').addClass('display-none');
        $('#new-ui-name').val('');
        $('#new-ui-email').val('');

        $("#new-ui-right-uid-select").change(function () {
            var user_id = $(this).val();
            var target_user = (user_id) ? get_user_by_id(user_id) : null;
            if(!target_user){
                $('.form-new-team-member-content-skills').html('');
            }else {
                $('.form-new-team-member-content-skills').html('<div class="loading-spinner loading-spinner--26"></div>');
                explore_detail_team_get_infos(target_user);
            }
        });
    }
}

/**
 * after_close_add_user_innovation_right_popup.
 */
function after_close_add_user_innovation_right_popup(){
    $('#new-ui-role-select').val('Other').trigger('change');
    $('#new-ui-right-uid-select').val('').trigger('change');
    $('#new-ui-name').val('');
    $('#new-ui-email').val('');
    $('.element-popup-new-user-innovation-right .form-element').removeClass('error');
    $('#form-new-user-innovation-right .form-element-error').remove();
    $('.element-popup-new-user-innovation-right .choose-select-member').removeClass('display-none');
    $('.element-popup-new-user-innovation-right .choose-create-member').addClass('display-none');
    goToNewUpdate();
}


/**
 * after_close_update_user_innovation_right_popup.
 */
function after_close_update_user_innovation_right_popup() {
    $('#update-ui-role-select').select2('val', '');
    $('#update-ui-uid').val('');
    $('#form-update-user-innovation-right .form-element').removeClass('error');
    $('#form-update-user-innovation-right .form-element-error').remove();
    goToNewUpdate();
}

/* Popup new innovation
 ----------------------------------------------------------------------------------------*/

/**
 * before_open_new_innovation_popup.
 */
function before_open_new_innovation_popup(){
    resetNewInnovationForm();
}

/* Popup Feedback Request
 ----------------------------------------------------------------------------------------*/

function before_open_feedback_request_popup(){
    feedback_request_clear_form_error();
}

/* Popup Feedback Answer
 ----------------------------------------------------------------------------------------*/

function before_open_feedback_answer_popup(){
    feedback_answer_clear_form_error();
}

/* Popup Share Innovation
 ----------------------------------------------------------------------------------------*/

function before_open_share_innovation_popup(){
    share_innovation_clear_form();
    var innovation_id = $('#detail-id-innovation').val();
    $('#form-share-innovation-id').val(innovation_id);
}

/* Popup manage new recommandation/feedback
 ----------------------------------------------------------------------------------------*/

function before_open_manage_new_feedback_popup(){
    manage_new_feedback_clear_form_error();
}

/* Popup Activities view all
 ----------------------------------------------------------------------------------------*/

function before_open_tab_activities_view_all_popup(){
    tab_activities_view_all_popup_load_more();
}

/* Popup video
 ----------------------------------------------------------------------------------------*/

function after_open_video_popup(){
    videojs("video-pipeline").play();
}

function before_close_video_popup(){
    videojs("video-pipeline").pause();
}



/* Popup Add key city
----------------------------------------------------------------------------------------*/

/**
 * before_open_add_key_city_popup.
 */
function before_open_add_key_city_popup(){
    // TODO
    if (!$('#new-key-city-select').data('select2')) {
        $('#new-key-city-select').select2({
            placeholder: "Select a city",
            width: '100%',
            language: {
                errorLoading: function () {
                    return "Searching..."
                }
            },
            ajax: {
                url: "/city/search",
                dataType: 'json',
                type: "POST",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        filters: params
                    };
                },
                processResults: function (data, page) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3
        });
        $('#new-key-city-select').val('').trigger('change');
    }

}

/**
 * after_close_add_key_city_popup.
 */
function after_close_add_key_city_popup(){
    $('#new-key-city-select').val('').trigger('change');
    $('.element-popup-new-key-city .form-element').removeClass('error');
    $('#form-new-key-city .form-element-error').remove();
}

/* Popup open question
----------------------------------------------------------------------------------------*/

/**
 * before_open_open_question_popup/
 */
function before_open_open_question_popup(){
    $('#form-open-question-message').attr('data-old-value', '').val('');
    $('.element-popup-open-question .form-element').removeClass('error');
    $('#form-open-question .form-element-error').remove();
    var innovation_id = $('#detail-id-innovation').val();
    if(!innovation_id){
        return;
    }
    var innovation = getInnovationById(innovation_id, true);
    if (!innovation) {
        return;
    }
    var message = (innovation['open_question'] && innovation['open_question']['message']) ? innovation['open_question']['message'] : '';
    $('#form-open-question-message').attr('data-old-value', message).val(message);

}