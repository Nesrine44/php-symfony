var current_user = null;
var user_settings_request_qet_infos = null;

/**
 * goToUserSettings
 * @param link
 * @return {boolean}
 */
function goToUserSettings(user) {
    try {
        before_opening_page();
        var link = "/user/settings";
        $('#main').addClass('loading');
        opti_update_url(link, 'user-settings');
        changePageTitle(  'Settings | ' + global_wording['title']);
        update_main_classes(main_classes);
        update_menu_active();
        $('#pri-main-container-without-header').html(user_settings_get_html_template(user));
        user_settings_init(user);
    } catch(error) {
        init_unknow_error_view(error);
    }
}

/**
 * user_settings_get_html_template
 * @param user
 * @returns {string}
 */
function user_settings_get_html_template(user) {
    var out = [], o = -1;
    out[++o] = '<div class="full-content-page-user-settings padding-top-40" data-user-id="'+user['id']+'">'; // Start full-content-page-user-settings
    out[++o] = '<div class="central-content-994">'; // Start central-content-994
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 margin-bottom-60">Emails settings</div>';
    out[++o] = '<div class="content-user-settings">'; // Start content-user-settings
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>'; // End content-user-settings
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End full-content-page-user-settings
    return out.join('');
}

/**
 * user_settings_init
 * @param user
 */
function user_settings_init(user) {
    load_background_images();
    user_settings_get_infos(user);
}



/**
 * user_settings_get_infos
 * @param user
 */
function user_settings_get_infos(user) {
    if (user_settings_request_qet_infos != null)
        user_settings_request_qet_infos.abort();

    user_settings_request_qet_infos = $.ajax({
        url: '/api/user/settings/infos',
        type: "POST",
        data: {user_id: user['id']},
        dataType: 'json',
        success: function (response, statut) {
            $('#main').removeClass('loading');
            $('.full-content-page-user-settings .content-user-settings').html(user_settings_get_html_inner_template(response));
            user_settings_init_radios();
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            $('#main').removeClass('loading');
            $('.full-content-page-user-settings .content-user-settings').html('');
            $('#main').removeClass('inner-loading');
        }
    });

}


/**
 * user_settings_get_html_inner_template
 * @param data
 * @returns {string}
 */
function user_settings_get_html_inner_template(data) {
    var out = [], o = -1;
    out[++o] = '<div class="content-multi-radio margin-bottom-40">'; // Start content-multi-radio
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 line-height-1-5 margin-bottom-20">Weekly activity reports</div>';
    out[++o] = '<div class="display-inline-block position-relative vertical-align-middle">'; // Start display-inline-block
    out[++o] = '<div class="content-radio margin-bottom-15">'; // Start content-radio
    out[++o] = '<input type="radio" id="weekly-report-yes" name="accept_scheduled_emails" value="1" class="hidden-round-radio" '+((data['accept_scheduled_emails']) ? 'checked' : '')+'/>';
    out[++o] = '<label class="fake-round-radio" for="weekly-report-yes"><div class="round transition-opacity"></div></label>';
    out[++o] = '<label class="true-label" for="weekly-report-yes">Yes, send me weekly activity reports about my projects</label>';
    out[++o] = '</div>'; // End content-radio
    out[++o] = '<div class="content-radio">'; // Start content-radio
    out[++o] = '<input type="radio" id="weekly-report-no" name="accept_scheduled_emails" value="0" class="hidden-round-radio" '+((!data['accept_scheduled_emails']) ? 'checked' : '')+'/>';
    out[++o] = '<label class="fake-round-radio" for="weekly-report-no"><div class="round transition-opacity"></div></label>';
    out[++o] = '<label class="true-label" for="weekly-report-no">No, thanks</label>';
    out[++o] = '</div>'; // End content-radio
    out[++o] = '</div>'; // End display-inline-block
    out[++o] = '</div>'; // End content-multi-radio

    out[++o] = '<div class="content-multi-radio margin-bottom-80">'; // Start content-multi-radio
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 line-height-1-5 margin-bottom-20">Newsletter</div>';
    out[++o] = '<div class="display-inline-block position-relative vertical-align-middle">'; // Start display-inline-block
    out[++o] = '<div class="content-radio margin-bottom-15">'; // Start content-radio
    out[++o] = '<input type="radio" id="newsletter-yes" name="accept_newsletter" value="1" class="hidden-round-radio" '+((data['accept_newsletter']) ? 'checked' : '')+'/>';
    out[++o] = '<label class="fake-round-radio" for="newsletter-yes"><div class="round transition-opacity"></div></label>';
    out[++o] = '<label class="true-label" for="newsletter-yes">Yes, send me the Innovation Hub Newsletter</label>';
    out[++o] = '</div>'; // End content-radio
    out[++o] = '<div class="content-radio">'; // Start content-radio
    out[++o] = '<input type="radio" id="newsletter-no" name="accept_newsletter" value="0" class="hidden-round-radio" '+((!data['accept_newsletter']) ? 'checked' : '')+'/>';
    out[++o] = '<label class="fake-round-radio" for="newsletter-no"><div class="round transition-opacity"></div></label>';
    out[++o] = '<label class="true-label" for="newsletter-no">No, thanks</label>';
    out[++o] = '</div>'; // End content-radio
    out[++o] = '</div>'; // End display-inline-block
    out[++o] = '</div>'; // End content-multi-radio

    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 margin-bottom-60">Contact settings</div>';

    out[++o] = '<div class="content-multi-radio margin-bottom-40">'; // Start content-multi-radio
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 line-height-1-5 margin-bottom-20">Allow people to ask you for feedback</div>';
    out[++o] = '<div class="display-inline-block position-relative vertical-align-middle">'; // Start display-inline-block
    out[++o] = '<div class="content-radio margin-bottom-15">'; // Start content-radio
    out[++o] = '<input type="radio" id="contact-yes" name="accept_contact" value="1" class="hidden-round-radio" '+((data['accept_contact']) ? 'checked' : '')+'/>';
    out[++o] = '<label class="fake-round-radio" for="contact-yes"><div class="round transition-opacity"></div></label>';
    out[++o] = '<label class="true-label" for="contact-yes">Yes, I allow people to ask me for feedback</label>';
    out[++o] = '</div>'; // End content-radio
    out[++o] = '<div class="content-radio">'; // Start content-radio
    out[++o] = '<input type="radio" id="contact-no" name="accept_contact" value="0" class="hidden-round-radio" '+((!data['accept_contact']) ? 'checked' : '')+'/>';
    out[++o] = '<label class="fake-round-radio" for="contact-no"><div class="round transition-opacity"></div></label>';
    out[++o] = '<label class="true-label" for="contact-no">No, I don\'t want people to ask me for feedback</label>';
    out[++o] = '</div>'; // End content-radio
    out[++o] = '</div>'; // End display-inline-block
    out[++o] = '</div>'; // End content-multi-radio



    return out.join('');
}

/**
 * user_settings_init_radios
 */
function user_settings_init_radios(){
    $('.content-multi-radio input[type="radio"]').change(function(){
        var $parent = $(this).parent().parent().parent();
        if(!$parent.hasClass('loading')){
            $parent.addClass('loading');
            $parent.append('<div class="loading-spinner"></div>');
            var data = {
                target: $(this).attr('name'),
                value: $(this).val()
            };
            data['token'] = $('#hub-token').val();
            $parent.find('input[type="radio"]').prop('disabled', true);
            $.ajax({
                url: '/api/user/settings/infos/update',
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response, statut) {
                    $parent.removeClass('loading');
                    $parent.find('.loading-spinner').remove();
                    $parent.find('input[type="radio"]').prop('disabled', false);
                    if(response['status'] === 'error'){
                        add_flash_message('error', response['message']);
                    } else {
                        add_saved_message();
                        switch (data.target) {
                            case 'accept_newsletter':
                                $('#main').attr('data-accept-newsletter', data.value);
                                break;
                            case 'accept_contact':
                                $('#main').attr('data-accept-contact', data.value);
                                break;
                        }
                    }
                },
                error: function (resultat, statut, e) {
                    ajax_on_error(resultat, statut, e);
                    if (resultat.statusText == 'abort' || e == 'abort') {
                        return;
                    }
                    $parent.removeClass('loading');
                    $parent.find('.loading-spinner').remove();
                    $parent.find('input[type="radio"]').prop('disabled', false);
                }
            });
        }
    });
}