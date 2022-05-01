var form_new_innovation_data = null;

function reset_form_new_innovation_data() {
    form_new_innovation_data = {
        'step-1': {
            'classification': 'product'
        },
        'step-2': {
            'question_1': null,
            'question_2': null,
            'question_3': null
        }
    };
}

function update_when_check_replace_existing_product(value) {
    if (value == "1") {
        $('#form-new-innovation .content-existing_product').removeClass('display-none');
    } else {
        $('#form-new-innovation .content-existing_product').addClass('display-none');
    }
}

function onChangeIsAService(value) {
    if (value == "1") {
        $('#form-new-innovation .content-only-service').removeClass('display-none');
        $('#form-new-innovation .content-only-product').addClass('display-none');
        // CHECK SPECIAL CASE FOR SERVICE
        update_when_check_have_a_multi_brand($("#form-new-innovation input[name=multi_brand_innovation]:checked").val());
    } else {
        $('#form-new-innovation .content-only-service').addClass('display-none');
        $('#form-new-innovation .content-only-product').removeClass('display-none');
        $('#form-new-innovation .content-brand-name, #form-new-innovation  .content-have_a_mother_brand').removeClass('display-none');

        // CHECK SPECIAL CASE FOR PRODUCT
        update_when_check_replace_existing_product($("#form-new-innovation input[name=replace_existing_product]:checked").val());
    }
    update_when_check_have_a_mother_brand($("#form-new-innovation input[name=have_a_mother_brand]:checked").val());
}

function update_when_check_have_a_multi_brand(value) {
    if (value == "1") {
        $('#form-new-innovation .content-brand-name, #form-new-innovation  .content-have_a_mother_brand').addClass('display-none');
    } else {
        $('#form-new-innovation .content-brand-name, #form-new-innovation  .content-have_a_mother_brand').removeClass('display-none');
    }
}

function update_when_check_have_a_mother_brand(value) {
    if ($("#form-new-innovation input[name=is_a_service]:checked").val() == "1" && $("#form-new-innovation input[name=multi_brand_innovation]:checked").val() == '1') {
        value = "0";
    }
    if (value == "1") {
        $('#form-new-innovation .content-brand-name').removeClass('display-none');
        $('#form-new-innovation select[name=growth_model]').val('fast_growth').trigger('change');
        $('#form-new-innovation .new-content-infos-strat_intent').html("- Typically line extensions from strong brands<br>- Endorsed by a mother brand with strong equity where it is launched (less education and awareness to do)<br>- Success in 1 market first<br>- Window of 2-4 years to rollout globally<br>- ROI: short/mid-term payback after inflection point");
    } else {
        $('#form-new-innovation .content-brand-name').addClass('display-none');
        $('#form-new-innovation select[name=growth_model]').val('slow_build').trigger('change');
        $('#form-new-innovation .new-content-infos-strat_intent').html("- Typically New-To-World or stretch from the mother brand + M&A and sleeping beauties (needs more time and investment to build awareness and ROI) <br>- Taps into clear consumer insight / opportunity<br>- Brand building over a sustained period > 5 years<br>- Acceleration once inflection point is reached due to strong advocacy<br>- ROI: mid/long-term payback after inflection point");
    }
}

function check_if_step_3_is_valid() {
    var step_3_is_valid = true;
    var $first_error = null;

    var is_service = ($("#form-new-innovation input[name=is_a_service]:checked").val() == "1");
    var is_multi_brand = ($("#form-new-innovation input[name=multi_brand_innovation]:checked").val() == "1");

    var $field_innovation_name = $("#form-new-innovation input[name=innovation-name]");
    if ($field_innovation_name.val() == '') {
        step_3_is_valid = false;
        $first_error = (!$first_error) ? $field_innovation_name : $first_error;
        errorInput($field_innovation_name);
    }

    var $field_brand_name = $('#form-new-innovation select[name=brand-name]');
    var have_a_mother_brand = $("#form-new-innovation input[name=have_a_mother_brand]:checked").val();
    if ((!is_service || (is_service && !is_multi_brand)) && have_a_mother_brand == '1' && ($field_brand_name.val() == '' || !$field_brand_name.val())) {
        step_3_is_valid = false;
        $first_error = (!$first_error) ? $field_brand_name : $first_error;
        errorInput($field_brand_name);
    }

    var $field_existing_product = $('#form-new-innovation input[name="existing_product"]');
    var replace_existing_product = $("#form-new-innovation input[name=replace_existing_product]:checked").val();
    if (!is_service && replace_existing_product == '1' && ($field_existing_product.val() == '' || !$field_existing_product.val())) {
        step_3_is_valid = false;
        $first_error = (!$first_error) ? $field_existing_product : $first_error;
        errorInput($field_existing_product);
    }

    var $field_entry_date = $('#form-new-innovation input[name="entry-date"]');
    if ($field_entry_date.val() == '') {
        step_3_is_valid = false;
        $first_error = (!$first_error) ? $field_entry_date : $first_error;
        errorInput($field_entry_date);
    }
    if (!step_3_is_valid) {
        scroll_to_popup_element($first_error);
    } else {
        $('#form-new-innovation .content-step-1').addClass("display-none");
        $('#form-new-innovation .content-step-2').addClass("display-none");
        $('#form-new-innovation .content-step-3').addClass("display-none");
        $('#form-new-innovation .content-step-4').removeClass("display-none");
        $('.element-new-innovation-popup .cross-close-popup').addClass("display-none");
        $('.element-new-innovation-popup .back-on-popup').removeClass("display-none");
    }
    return step_3_is_valid;
}

/**
 * form_new_innovation_init_step
 * @param step_nb
 */
function form_new_innovation_init_step(step_nb) {
    $('#form-new-innovation .content-' + step_nb + ' .container-choices .choice').removeClass('active');
    var classification_value = form_new_innovation_data['step-1']['classification'];
    if($('#form-new-innovation .content-' + step_nb+' .only-'+classification_value).length > 0){
        $('#form-new-innovation .content-' + step_nb+' .only-product').hide();
        $('#form-new-innovation .content-' + step_nb+' .only-service').hide();
        $('#form-new-innovation .content-' + step_nb+' .only-'+classification_value).show();
    }
    if (step_nb === 'step-out-of-scope') {
        $('#form-new-innovation .main-title').text('Your project is out of scope…');
    } else if (step_nb === 'step-success') {
        $('#form-new-innovation .main-title').text('Congratulations!');
    }
    if (!form_new_innovation_data.hasOwnProperty(step_nb)) {
        return false;
    }
    var classification_value = form_new_innovation_data['step-1']['classification'];
    if(classification_value == 'product'){
        $('#form-new-innovation .new_is_a_service').prop('checked', false);
        $('#form-new-innovation #new_is_a_service_no').prop('checked', true);
        onChangeIsAService("0");
    }else{
        $('#form-new-innovation .new_is_a_service').prop('checked', false);
        $('#form-new-innovation #new_is_a_service_yes').prop('checked', true);
        onChangeIsAService("1");
    }

    $('#form-new-innovation .main-title').text('Project setup');
    var step = form_new_innovation_data[step_nb];
    var is_valid = true;
    for (var key in step) {
        if (step.hasOwnProperty(key)) {
            if (step[key]) {
                var classification_value = form_new_innovation_data['step-1']['classification'];
                var $container_choices = ($('#form-new-innovation .content-' + step_nb+' .only-'+classification_value).length > 0) ? $('#form-new-innovation .content-' + step_nb + ' .only-'+classification_value+' .container-choices[data-target='+key+']') : $('#form-new-innovation .content-' + step_nb + ' .container-choices[data-target='+key+']');
                if ($container_choices.length > 0) {
                    $container_choices.find('.choice[data-value=' + step[key] + ']').addClass('active');
                }
            } else {
                is_valid = false;
            }
        }
    }
    if (step_nb !== 'step-1') {
        $('#form-new-innovation .content-' + step_nb + ' .content-buttons .hub-button:not(.grey)').prop('disabled', (!is_valid));
    }


}

/**
 * go_to_new_innovation_step
 * @param step_nb
 */
function go_to_new_innovation_step(step_nb) {
    if (!$('.custom-content-popup.opened').hasClass('loading')) {
        $('#form-new-innovation .content-step-1').addClass("display-none");
        $('#form-new-innovation .content-step-2').addClass("display-none");
        $('#form-new-innovation .content-step-3').addClass("display-none");
        $('#form-new-innovation .content-step-4').addClass("display-none");
        $('#form-new-innovation .content-step-success').addClass('display-none');
        $('#form-new-innovation .content-step-out-of-scope').addClass('display-none');
        $('#form-new-innovation .content-' + step_nb).removeClass("display-none");
        if (step_nb == 'step-1') {
            $('.element-new-innovation-popup .cross-close-popup').removeClass("display-none");
            $('.element-new-innovation-popup .back-on-popup').addClass("display-none");
            $('#form-new-innovation .content-' + step_nb + ' .content-buttons .hub-button:not(.grey)').prop('disabled', false);
        } else {
            $('.element-new-innovation-popup .cross-close-popup').addClass("display-none");
            $('.element-new-innovation-popup .back-on-popup').removeClass("display-none");
        }
        $('.element-new-innovation-popup').attr('data-current-step', step_nb);

        form_new_innovation_init_step(step_nb);
    }
}

function go_to_new_innovation_step_3() {
    if (!$('.custom-content-popup.opened').hasClass('loading')) {
        $('#form-new-innovation .content-step-1').addClass("display-none");
        $('#form-new-innovation .content-step-2').addClass("display-none");
        $('#form-new-innovation .content-step-3').removeClass("display-none");
        $('#form-new-innovation .content-step-4').addClass("display-none");
        $('#form-new-innovation .content-step-success').addClass('display-none');
        $('#form-new-innovation .content-step-out-of-scope').addClass('display-none');
        $('.element-new-innovation-popup .cross-close-popup').removeClass("display-none");
        $('.element-new-innovation-popup .back-on-popup').addClass("display-none");
    }
}

/* Usefull
 ----------------------------------------------------------------------------------------*/

var request_create_innovation = null;

function initLimitText(limitField, limitCount, limitNum, hasBreakline) {
    limitText(limitField, limitCount, limitNum);
    var acceptBreakLine = (hasBreakline !== undefined) ? hasBreakline : true;
    limitField.keyup(function () {
        if (!acceptBreakLine) {
            $(this).val($(this).val().replace(/[\r\n\v]+/g, ''));
        }
        limitText(limitField, limitCount, limitNum);
    });
    limitField.keydown(function () {
        if (!acceptBreakLine) {
            $(this).val($(this).val().replace(/[\r\n\v]+/g, ''));
        }
        limitText(limitField, limitCount, limitNum);
    });

}


function limitText(limitField, limitCount, limitNum) {
    if (limitField.length > 0) {
        if (limitField.val().length > limitNum) {
            var newVal = limitField.val().substring(0, limitNum);
            limitField.val(newVal);
        }
        var newCount = limitNum - limitField.val().length;
        limitCount.text(newCount);
    }
}


function loadPageSmoothly(url, classe) {
    window.location.href = url;
}


function errorInput($element) {
    var value = $element.val();
    var $form_element = $element.closest('.form-element');
    $form_element.removeClass('error');
    $form_element.find('.form-element-error').remove();
    if (
        value == '' ||
        value == null ||
        ($element.hasClass('datepicker') && !isDate(value)) ||
        ($element.attr('data-type') == 'number' && !isInt(value))
    ) {
        $form_element.addClass('error');
        if ($element.is("select")) {
            $form_element.append('<div class="form-element-error">This field is required</div>');
        } else {
            var message = ($element.hasClass('datepicker') && !isDate(value)) ? 'This field is incorrect, please enter a date like mm/dd/yyyy' : 'This field is required';
            if ($element.attr('data-type') == 'number' && !isInt(value)) {
                message = 'This field is incorrect, please enter a number';
            }
            $form_element.append('<div class="form-element-error">' + message + '</div>');
        }
    }
}


function resetNewInnovationForm() {
    // Global
    reset_form_new_innovation_data();
    $('#form-new-innovation .action-new-innovation-lets-go').attr('data-id', '');
    $('.element-new-innovation-popup').attr('data-current-step', 'step-1');
    $('#form-new-innovation .form-element').removeClass('error');
    $('#form-new-innovation').find('input[type=text], input[type=email], input[type=search], textarea').val('');
    $('#form-new-innovation .main-title').text('Project setup');
    $('#form-new-innovation .content-step-1').removeClass("display-none");
    $('#form-new-innovation .content-step-2').addClass("display-none");
    $('#form-new-innovation .content-step-3').addClass("display-none");
    $('#form-new-innovation .content-step-4').addClass("display-none");
    $('#form-new-innovation .content-step-success').addClass('display-none');
    $('#form-new-innovation .content-step-out-of-scope').addClass('display-none');

    $('#form-new-innovation .container-choices .choice').removeClass('active');
    for (var k in form_new_innovation_data) {
        if (form_new_innovation_data.hasOwnProperty(k)) {
            var step = form_new_innovation_data[k];
            for (var key in step) {
                if (step.hasOwnProperty(key)) {
                    var $container_choices = $('.container-choices[data-target=' + key + ']');
                    if ($container_choices.length > 0) {
                        $container_choices.find('.choice[data-value=' + step[key] + ']').addClass('active');
                    }
                }
            }
        }
    }


    $('#form-new-innovation .form-element-error').remove();

    // Entity
    $('#form-new-innovation .team-entity-select').each(function () {
        var value = $(this).find('option[selected=selected]').attr('value');
        $(this).val(value).trigger("change");
    });
    // Is a service ?
    $('#form-new-innovation .new_is_a_service').prop('checked', false);
    $('#form-new-innovation #new_is_a_service_no').prop('checked', true);
    onChangeIsAService('0');

    // Have a mother Brand ?
    $('#form-new-innovation .new_have_a_mother_brand').prop('checked', false);
    $('#form-new-innovation #new_have_a_mother_brand_yes').prop('checked', true);
    update_when_check_have_a_mother_brand('1');

    // Brand
    $('#form-new-innovation select[name=brand-name]').val('').trigger("change");

    // Growth model
    $('#form-new-innovation select[name=growth_model]').val("fast_growth").trigger("change");
    $('#form-new-innovation .new-content-infos-strat_intent').html("- Typically line extensions from strong brands<br>- Endorsed by a mother brand with strong equity where it is launched (less education and awareness to do)<br>- Success in 1 market first<br>- Window of 2-4 years to rollout globally<br>- ROI: short/mid-term payback after inflection point");

    // Pernod Ricard SKU
    $('#form-new-innovation .new_replace_existing_product').prop('checked', false);
    $('#form-new-innovation #new_replace_existing_product_no').prop('checked', true);
    $('#form-new-innovation input[name="existing_product"]').val('');
    update_when_check_replace_existing_product('0');

    // In market date
    $('#form-new-innovation input[name="entry-date"]').val('');

    // Stage
    $('#form-new-innovation .carre, #form-new-innovation .container-carre, #form-new-innovation  .text-current-stage').removeClass('active');
    $('#form-new-innovation .text-current-stage.discover').addClass('active');
    $('#form-new-innovation .container-carre.discover').addClass('active');
    $('#form-new-innovation .container-carre.discover .carre').addClass('active');
    $("#form-new-innovation input[name=innovation-stage]").val(1);

    // Buttons
    $('.element-new-innovation-popup .cross-close-popup').removeClass("display-none");
    $('.element-new-innovation-popup .back-on-popup').addClass("display-none");
}

function getDataForNewInnovation() {
    var data = {};
    data['title'] = $("#form-new-innovation input[name=innovation-name]").val();
    data['have_a_mother_brand'] = $("#form-new-innovation input[name=have_a_mother_brand]:checked").val();
    data['brand'] = $("#form-new-innovation select[name=brand-name]").val();
    $('#new_brand_name').val();
    data['growth_model'] = $('#form-new-innovation select[name=growth_model]').val();
    data['replace_existing_product'] = $("#form-new-innovation input[name=replace_existing_product]:checked").val();
    data['existing_product'] = $('#form-new-innovation input[name=replace_existing_product]').val();
    var start_date = $('#form-new-innovation input[name="entry-date"]').val();
    var true_start_date = start_date.split('/')[2] + '-' + start_date.split('/')[0] + '-' + start_date.split('/')[1];
    data['start_date'] = true_start_date;
    data['current_stage'] = parseInt($("#form-new-innovation input[name=innovation-stage]").val());
    data['entity'] = $('#form-new-innovation-entity').val();
    var is_a_service = ($("#form-new-innovation input[name=is_a_service]:checked").val() == "1");
    if (is_a_service) {
        data['classification'] = 3;
        data['is_multi_brand'] = $("#form-new-innovation input[name=multi_brand_innovation]:checked").val();
    } else {
        data['classification'] = 2;
    }
    data['token'] = $("#form-new-innovation-token").val();
    return data;
}

(function ($) {
    $(document).ready(function () {

        /* POPUP CREATE NEW INNOVATION STEP 1
         ----------------------------------------------------------------------------------------*/

        $('#form-new-innovation .brand-select').select2({
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


        $('#form-new-innovation .team-entity-select').select2({
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

        $('#form-new-innovation .growth_model-select').select2({
            width: '100%',
            allowClear: false
        });

        $("#form-new-innovation input[name=innovation-name]").keyup(function () {
            if ($(this).val() != "") {
                $(this).parent().parent().removeClass('error');
                $(this).parent().parent().find('.form-element-error').remove();
            }
        });

        $("#form-new-innovation input[name=innovation-name]").change(function () {
            if ($(this).val() != "") {
                $(this).parent().parent().removeClass('error');
                $(this).parent().parent().find('.form-element-error').remove();
            }
        });

        $("#form-new-innovation select[name=brand-name]").change(function () {
            if ($(this).val() != "") {
                $(this).parent().parent().removeClass('error');
                $(this).parent().parent().find('.form-element-error').remove();
            }
        });

        $("#form-new-innovation input[name=existing_product]").change(function () {
            if ($(this).val() != "") {
                $(this).parent().parent().removeClass('error');
                $(this).parent().parent().find('.form-element-error').remove();
            }
        });


        $('.action-step').click(function () {
            if (!$(this).prop('disabled')) {
                var step_nb = $(this).attr('data-step');
                var current_step = $('.element-new-innovation-popup').attr('data-current-step');
                var classification_value = form_new_innovation_data['step-1']['classification'];
                var $container_choices = ($('#form-new-innovation .content-' + current_step+' .only-'+classification_value).length > 0) ? $('#form-new-innovation .content-' + current_step + ' .only-'+classification_value+' .container-choices') : $('#form-new-innovation .content-' + current_step + ' .container-choices');
                if(current_step == 'step-1'){
                    reset_form_new_innovation_data();
                }
                $container_choices.each(function () {
                    var target = $(this).attr('data-target');
                    var value = $(this).find('.choice.active').attr('data-value');
                    form_new_innovation_data[current_step][target] = value;
                });
                if (step_nb == 'step-3') {
                    var is_valid = true;
                    classification_value = form_new_innovation_data['step-1']['classification'];
                    if (classification_value == 'product') {
                        if (
                            (form_new_innovation_data['step-2']['question_1'] === 'yes') ||
                            (
                                form_new_innovation_data['step-2']['question_1'] === 'no' &&
                                form_new_innovation_data['step-2']['question_2'] === 'no' &&
                                form_new_innovation_data['step-2']['question_3'] === 'no'
                            )
                        ) {
                            is_valid = false;
                        }
                    }else if (classification_value == 'service') {
                        if (
                            (form_new_innovation_data['step-2']['question_1'] === 'no') ||
                            (form_new_innovation_data['step-2']['question_2'] === 'no')
                        ) {
                            is_valid = false;
                        }
                    }
                    if (!is_valid) {
                        step_nb = 'step-out-of-scope';
                    }
                }
                if (current_step == 'step-3') {
                    check_if_step_3_is_valid();
                    return false;
                }
                go_to_new_innovation_step(step_nb);
            }
            return false;
        });

        $('.action-new-innovation-lets-go').click(function () {
            var id = $(this).attr('data-id');
            if(id) {
                goToExploreDetailPage(id, 'edit');
            }
            return false;
        });


        $("#form-new-innovation input[name=have_a_mother_brand]").change(function () {
            var value = $(this).val();
            update_when_check_have_a_mother_brand(value);
        });


        $("#form-new-innovation input[name=is_a_service]").change(function () {
            var value = $(this).val();
            onChangeIsAService(value);
        });


        $("#form-new-innovation input[name=multi_brand_innovation]").change(function () {
            var value = $(this).val();
            update_when_check_have_a_multi_brand(value);
            update_when_check_have_a_mother_brand($("#form-new-innovation input[name=have_a_mother_brand]:checked").val());
        });


        $("#form-new-innovation input[name=replace_existing_product]").change(function () {
            if (!$('.custom-content-popup.opened').hasClass('loading')) {
                var value = $(this).val();
                update_when_check_replace_existing_product(value);
            }
        });

        $("#form-new-innovation select[name=growth_model]").change(function () {
            var value = $(this).val();
            if (value) {
                var value_class = value.toCssClass();
                $(this).parent().find('.icon').removeClass().addClass('icon ' + value_class);
            }
        });

        $(document).on('submit', '#form-new-innovation', function (e) {
            e.preventDefault();
            if ($('#form-new-innovation .content-step-1').is(':visible')) {
                check_if_step_3_is_valid();
                return false;
            }
            var data = getDataForNewInnovation();
            $('.error-message-create-innovation').html('');
            enable_load_popup();
            if (request_create_innovation !== null) {
                request_create_innovation.abort();
            }
            request_create_innovation = $.ajax({
                url: '/api/innovation/create',
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response, statut) {
                    if (response.hasOwnProperty('status') && response['status'] == 'error') {
                        disable_load_popup();
                        $('.error-message-create-innovation').html(response['message']);
                    } else {
                        disable_load_popup();
                        if (response.hasOwnProperty('optimization_init')) {
                            optimization_init(response['optimization_init']);
                            $('#main').removeClass('no-role');
                            if (response.hasOwnProperty('full_data')) {
                                $('#form-new-innovation .action-new-innovation-lets-go').attr('data-id', response['full_data']['id']);
                                go_to_new_innovation_step('step-success');
                            } else {
                                goToManageList();
                                var message = 'Your innovation has been created. Please wait a few minutes before accessing it. Your innovation will appear in your list.';
                                add_flash_message('success', message);
                            }
                        } else if (response.hasOwnProperty('full_data')) {
                            close_popup();
                            addToCollection(response['id'], response['full_data']);
                        } else {
                            close_popup();
                            var url = window.location.href;
                            loadPageSmoothly(url);
                        }
                    }
                },
                error: function (resultat, statut, e) {
                    if (ajax_on_error(resultat, statut, e)) {
                        return;
                    }
                    if (resultat && resultat.statusText && resultat.statusText == 'abort') {
                        return;
                    } else if (statut && statut == 'abort') {
                        return;
                    }
                    console.log('form-new-innovation submit', e);
                    disable_load_popup();
                    $('.error-message-create-innovation').html(resultat.status + ' ' + e);
                }
            });
            return false;
        });

        /* POPUP CREATE NEW INNOVATION STEP 2
         ----------------------------------------------------------------------------------------*/


        $('#form-new-innovation .container-carre').click(function () {
            if (!$(this).hasClass('active')) {
                $('#content-stage-charged').show();
                $('#form-new-innovation .content-text-current-stage').removeClass('hovered');
                $('#form-new-innovation .text-current-stage').removeClass('hovered');
                $('#form-new-innovation .container-carre').removeClass('active');
                $('#form-new-innovation .container-carre .carre').removeClass('active');
                $(this).addClass('active');
                $(this).find('.carre').addClass('active');
                var target = $(this).find('.libelle').attr('data-target');
                var libelle_target = $(this).find('.libelle').attr('data-libelle-target');
                $('#form-new-innovation .text-current-stage').removeClass('active');
                $('#form-new-innovation .text-current-stage.' + libelle_target).addClass('active');
                $("#form-new-innovation input[name=innovation-stage]").val(target);
            }
        });
        $('#form-new-innovation .container-carre').hover(function () {
            $(this).addClass('hovered');
            if (!$(this).hasClass('active')) {
                $('#form-new-innovation .content-text-current-stage').addClass('hovered');
                var target = $(this).find('.libelle').attr('data-libelle-target');
                $('#form-new-innovation .text-current-stage').removeClass('hovered');
                $('#form-new-innovation .text-current-stage.' + target).addClass('hovered');
            }
        }, function () {
            $(this).removeClass('hovered');
            if ($('#form-new-innovation .container-carre').hasClass('active')) {
                $('#form-new-innovation .content-text-current-stage').removeClass('hovered');
                $('#form-new-innovation .text-current-stage').removeClass('hovered');
            }
        });


        $("#form-new-innovation .datepicker").datepicker({
            dateFormat: 'mm/dd/yy',
            showOn: "both",
            buttonImageOnly: true,
            buttonImage: "/images/datepicker/pic-calendar.png",
            yearRange: '1900:+5',
            onSelect: function (dateText, inst) {
                oldValue = inst.lastVal;
                errorInput($(this));
                oldValue = null;
            }
        });

        $('#form-new-innovation .classification-select').select2({
            width: '100%',
            placeholder: "Choose classification type",
            allowClear: true
        });


        $(document).on('click', '.element-new-innovation-popup .container-choices .choice', function (e) {
            e.stopImmediatePropagation();
            if (!$(this).hasClass('active')) {
                $(this).parent().find('.choice.active').removeClass('active');
                $(this).addClass('active');
                var current_step = $('.element-new-innovation-popup').attr('data-current-step');
                var classification_value = form_new_innovation_data['step-1']['classification'];
                var $container_choices = ($('#form-new-innovation .content-' + current_step+' .only-'+classification_value).length > 0) ? $('#form-new-innovation .content-' + current_step + ' .only-'+classification_value+' .container-choices') : $('#form-new-innovation .content-' + current_step + ' .container-choices');
                var is_valid = true;
                $container_choices.each(function () {
                    if ($(this).find('.choice.active').length == 0) {
                        is_valid = false;
                    }
                });
                $('#form-new-innovation .content-' + current_step + ' .content-buttons .hub-button:not(.grey)').prop('disabled', (!is_valid));
            }
        });

    });
})(jQuery);