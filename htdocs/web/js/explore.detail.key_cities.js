/* Tab Key Cities
 ----------------------------------------------------------------------------------------*/


/**
 * explore_get_key_cities_tab_html_template
 * @param id
 * @returns {*}
 */
function explore_get_key_cities_tab_html_template(id){
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    var cities = innovation['key_cities'];
    out[++o] = '<div class="central-content-994 padding-top-30 padding-bottom-30">'; // Start central-content-994
    out[++o] = '<div class="contextual-headline">';
    out[++o] = innovation['title'];
    out[++o] = '</div>';
    out[++o] = '<div class="headline padding-top-5 font-montserrat-700">Key cities</div>';
    out[++o] = '<div class="content-tab-key-cities margin-top-40">'; // Start content-tab-activities
    out[++o] = explore_detail_get_key_cities_content(cities);
    out[++o] = '</div>'; // End content-tab-activities
    out[++o] = '</div>'; // End central-content-994
    return out.join('');
}

/**
 * explore_detail_get_key_city_tab_with_data_html_template
 * @param response
 * @returns {string}
 */
function explore_detail_get_key_cities_content(cities) {
    var out = [], o = -1;
    out[++o] = '<div class="margin-top-20">'; // Start margin-top-20
    if(cities.length === 0){
        out[++o] = '<div class="empty-key-city text-align-center background-color-f2f2f2">';
        out[++o] = '<div class="padding-40">';
        out[++o] = '<div class="font-montserrat-700 font-size-20 color-b5b5b5 line-height-1-5">No key city yet…</div>';
        out[++o] = '<div class="font-work-sans-300 font-size-16 color-b5b5b5 line-height-1-5">Add the key cities for your project</div>';
        out[++o] = '<button class="hub-button action-open-popup margin-top-20" data-popup-target=".element-popup-new-key-city">Add a key city</button>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }else{
        out[++o] = '<div class="content-key-cities">'; // Start content-key-cities
        for(var i = 0; i < cities.length; i++){
            out[++o] =  explore_detail_get_city_line_html_template(cities[i]);
        }
        out[++o] = '</div>'; // End content-key-cities
        out[++o] = '<div class="position-relative margin-top-30"><button class="hub-button action-open-popup" data-popup-target=".element-popup-new-key-city">Add a key city</button></div>';
    }
    out[++o] = '</div>'; // End margin-top-20
    return out.join('');
}

/**
 * explore_detail_get_city_line_html_template
 * @param city
 * @returns {string}
 */
function explore_detail_get_city_line_html_template(city) {
    var out = [], o = -1;
    out[++o] = '<div class="c-city transition-background-color">';
    out[++o] = '<div class="left">';
    out[++o] = '<div class="picture loading-bg" data-bg="'+city['picture_url']+'" data-default-bg="/images/default/city.png"></div>';
    out[++o] = '<div class="infos">';
    out[++o] = '<div class="name text-ellipsis line-height-1-2 font-montserrat-700 color-000">'+city['city_name']+'</div>';
    out[++o] = '<div class="subname text-ellipsis line-height-1-2 color-9b9b9b font-size-14 font-work-sans-400">'+city['country_name']+'</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content-right-button transition-opacity">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '<button class="hub-button height-26 with-tooltip explore-detail-button-remove-key-city" data-kcid="'+city['id']+'" title="Remove \“'+city['city_name']+'\" from your innovation">Remove</button>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * explore_detail_update_key_cities_template
 * @param city
 * @returns {string}
 */
function explore_detail_update_key_cities_template(cities) {
    $('.content-tab-key-cities').html(explore_detail_get_key_cities_content(cities));
    init_tooltip();
    load_background_images();
}

$(document).ready(function () {
    $(document).on('click', '.explore-detail-button-remove-key-city', function (e) {
        e.stopImmediatePropagation();
        var $target = $(this);
        var id = $('#detail-id-innovation').val();
        var kcid = $target.attr('data-kcid');
        var the_data = {'id': id, 'kcid': kcid, 'action': 'delete'};
        the_data['token'] = $('#hub-token').val();
        $target.parent().addClass('loading');
        $.ajax({
            url: '/api/innovation/key-city/update',
            type: "POST",
            data: the_data,
            dataType: 'json',
            success: function (response, statut) {
                if (response['status'] == 'success') {
                    $target.parent().parent().remove();
                } else {
                    add_flash_message('error', response['message']);
                    $target.parent().removeClass('loading');
                }
            },
            error: function (resultat, statut, e) {
                if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                    add_flash_message('error', 'Impossible to update key cities for now : ' + resultat.status + ' ' + e);
                    $target.parent().removeClass('loading');
                }
            }
        });
        return false;
    });

    $('#form-new-key-city').submit(function () {
        addNewKeyCity();
        return false;
    });

    $(document).on('click', '.add-new-key-city', function (e) {
        e.stopImmediatePropagation();
        addNewKeyCity();
    });

    function addNewKeyCity() {
        if ($('.custom-content-popup.opened').hasClass('loading')) {
            return false;
        }
        $('.element-popup-new-key-city .form-element').removeClass('error');
        $('.element-popup-new-key-city .form-element-error').remove();
        var data = {};
        data['token'] = $('#hub-token').val();
        data['action'] = 'add';
        data['id'] = $('#detail-id-innovation').val();
        var hasError = false;
        var kcid = $('#new-key-city-select').val();
        data['kcid'] = kcid;
        if (!kcid || kcid == '') {
            $('#new-key-city-select').parent().parent().addClass('error');
            $('#new-key-city-select').parent().parent().append('<div class="form-element-error">This field is required</div>');
            hasError = true;
        }
        if (!hasError) {
            enable_load_popup();
            $.ajax({
                url: '/api/innovation/key-city/update',
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response, statut) {
                    disable_load_popup();
                    if (response['status'] == 'field_error') {
                        $('#' + response['error_id']).parent().parent().addClass('error').append('<div class="form-element-error">' + response.message + '</div>');
                    } else if (response['status'] == 'error') {
                        add_flash_message('error', response['message']);
                        close_popup();
                    } else {
                        if (response.hasOwnProperty('full_data')) {
                            updateFromCollection(response['full_data']['id'], response['full_data']);
                            explore_detail_update_nb_missing_fields();
                        }
                        explore_detail_update_key_cities_template(response['key_cities']);
                        close_popup();
                        add_saved_message();
                    }
                },
                error: function (resultat, statut, e) {
                    if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                        add_flash_message('error', 'Impossible to add key city for now : ' + resultat.status + ' ' + e);
                    }
                    disable_load_popup();
                    close_popup();
                }
            });
        }
    }
});