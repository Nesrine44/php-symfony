var welcome_scroll_function_is_loaded = false;

function init_notification_welcome_page() {
    if (!check_if_is_data_capture_toast_enabled()) {
        return;
    }
    var before_seizure_date_libelle = $('#main').attr('data-before-seazure-date-libelle');
    var during_seizure_date_libelle = $('#main').attr('data-during-seazure-date-libelle');
    var start_date = $('#main').attr('data-open-date');
    if ($('#welcome-first-content').length > 0) {
        var cookieId = '',
            content = '',
            theme = 'notice';
        if (check_if_is_open_date()) {
            cookieId = 'welcome-during-info-' + start_date;
            theme = 'success';
            content = '<span class="bold">Data Capture ' + get_libelle_current_le() + ' open: ' + during_seizure_date_libelle + '</span>';
        } else if (!check_if_is_open_date() && !check_if_user_has_no_role() && !check_if_user_is_management()) {
            cookieId = 'welcome-pre-info-' + start_date;
            theme = 'error';
            content = '<span class="bold">Data Capture ' + get_financial_date_libelle(start_date) + ': ' + before_seizure_date_libelle + ' (Financial & Qualitative)</span>';
        }

        $('#welcome-first-content').cookieNotificator({
            id: cookieId,
            theme: theme,
            type: 'html',
            cookieCheck: false,
            content: content,
            customStyle: {
                'margin': '20px auto 0',
                'max-width': '100%'
            }
        });
    }
}

function init_welcome_page() {
    jQuery('#main').removeClass('loading').addClass('welcome_page');
    init_tooltip();
    launchBlueArrowScroll();
    launchFunctionWhenDOMIsReady(init_notification_welcome_page);
    launchFunctionWhenDOMIsReady(welcome_init_block_newsletter);
}

/**
 * welcome_init_block_newsletter.
 */
function welcome_init_block_newsletter() {
    if ($('#main').attr('data-accept-newsletter') != '0') {
        return;
    }
    $('.action-subscribe-newsletter').click(function() {
        var data = { target: 'accept_newsletter', value: "1" };
        data['token'] = $('#hub-token').val();
        $('.action-subscribe-newsletter').hide();
        $('.block-welcome-newsletter .content-loader').html('<div class="loading-spinner loading-spinner--26 centered"></div>');
        $.ajax({
            url: '/api/user/settings/infos/update',
            type: "POST",
            data: data,
            dataType: 'json',
            success: function(response, statut) {
                if (response.hasOwnProperty('status') && response['status'] == 'error') {
                    add_flash_message('error', response['message']);
                } else {
                    add_flash_message('success', 'Your are now subscribed to our newsletter!');
                    $('.block-welcome-newsletter').remove();
                    $('#main').attr('data-accept-newsletter', '1');
                }
            },
            error: function(resultat, statut, e) {
                ajax_on_error(resultat, statut, e);
                if (resultat.statusText == 'abort' || e == 'abort') {
                    return;
                }
                $('.action-subscribe-newsletter').show();
                $('.block-welcome-newsletter .content-loader').html('');
            }
        });
    });
}

function get_welcome_explore_innovation_html(innovation) {
    var out = [],
        o = -1;
    out[++o] = '';
    var explore_detail_url = "/explore/" + innovation['id'];
    var style_background = 'style="background-image:url(' + innovation['explore']['images'][0] + ');"';
    out[++o] = '<a class="content-project link_explore_detail" href="' + explore_detail_url + '">';
    out[++o] = '<span class="picture" ' + style_background + '></span>';
    out[++o] = '<span class="title">' + innovation['title'] + '</span>';
    var mmYY = gmdate("m/y", innovation['in_market_date']);
    out[++o] = '<span class="under-title">In Market since ' + mmYY + '</span>';
    out[++o] = '</a>';
    return out.join('');
}

function get_welcome_manage_innovation_html(innovation) {
    var out = [],
        o = -1;
    out[++o] = '';
    var count_empty_fields = innovation['count_empty_fields'];
    var explore_detail_url = "/explore/" + innovation['id'];
    var picture = innovation['explore']['images'][0];
    var style_background = 'style="background-image:url(' + picture + ');"';
    out[++o] = '<a class="content-project link_explore_detail" href="' + explore_detail_url + '">';
    if (count_empty_fields > 0) {
        out[++o] = '<span class="pitchou-nb-fields font-montserrat-500 tooltip-right-tr force-tooltip" title="' + count_empty_fields + ' empty fields">' + count_empty_fields + '</span>';
    }
    out[++o] = '<span class="picture background-color-fff" ' + style_background + '></span>';
    out[++o] = '<span class="title">' + innovation['title'] + '</span>';
    if (innovation['in_market_date'] && !in_array(innovation['current_stage'], ['discover', 'ideate'])) {
        var mmYY = gmdate("m/y", innovation['in_market_date']);
        out[++o] = '<span class="under-title">In Market since ' + mmYY + '</span>';
    }
    out[++o] = '</a>';
    return out.join('');
}


/**
 * get_innovations_collection_for_block_manage_your_projets.
 * @returns {*}
 */
function get_innovations_collection_for_block_manage_your_projets() {
    var user_uid = parseInt(jQuery('#main').attr('data-uid'));
    var innovations_ids = [];
    for (var i = 0; i < user_rights.length; i++) {
        if (user_rights[i]['right'] == 'write' && !in_array(user_rights[i]['innovation_id'], innovations_ids)) {
            innovations_ids.push(user_rights[i]['innovation_id']);
        }
    }
    allInnovationsDC.query().filter({ contact__uid__is: user_uid }).each(function(row, index) {
        if (!in_array(row['id'], innovations_ids)) {
            innovations_ids.push(row['id']);
        }
    });
    return new DataCollection(allInnovationsDC.query().filter({
        id__in: innovations_ids,
        is_frozen: false,
        current_stage_id__in: [1, 2, 3, 4, 5]
    }).values());
}

/**
 * goToWelcomePage()
 */
function goToWelcomePage() {
    try {
        before_opening_page();
        jQuery('#main').addClass('loading');
        opti_update_url("/", 'welcome_page');
        update_menu_active();
        changePageTitle('Welcome | ' + global_wording['title']);
        jQuery('#pri-main-container-without-header').html(welcome_get_html_template());
        init_welcome_page();
        window.scrollTo(0, 0);
    } catch (error) {
        init_unknow_error_view(error);
    }
}

/**
 * welcome_get_html_template
 * @returns {string}
 */
function welcome_get_html_template() {
    var out = [],
        o = -1;
    var user_innovation_collection = get_innovations_collection_for_block_manage_your_projets();
    var max_explore_innovations = 6;
    if (check_if_user_has_monitor_access() && user_innovation_collection.query().count() == 0) {
        max_explore_innovations = 9;
    }
    out[++o] = '<div class="full-content-page-welcome">'; // Start full-content-page-welcome
    out[++o] = '<div class="central-content overflow-hidden" id="welcome-first-content"></div>'; // Welcome-first-content
    out[++o] = '<div class="padding-top-40 text-align-center">'; // Start Entete
    out[++o] = '<div class="central-content">'; // central-content
    out[++o] = '<div class="font-montserrat-700 font-size-40 color-000 line-height-1">Welcome to the ' + global_wording['title'] + '!</div>';
    out[++o] = '<div class="font-work-sans-400 font-size-21 color-000 padding-top-10 line-height-1 z-index-2 position-relative">Boost your innovations\' success</div>';
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End Entete

    out[++o] = '<div class="background-color-fff padding-top-60 padding-bottom-40">'; // Start content white
    out[++o] = '<div class="central-content-1280 no-responsive-margin">'; // Start central-content-1280


    out[++o] = '<div class="overflow-hidden content-welcome-column">'; // Start overflow-hidden
    out[++o] = '<div class="central-content">'; // Central content
    out[++o] = '<div class="column-percent-70">'; // Start column-percent-70

    if (user_innovation_collection.query().count() > 0) {
        user_innovation_collection = new DataCollection(user_innovation_collection.query().order('count_empty_fields', true).values());
        // BLOCK MANAGE YOUR PROJECTS
        out[++o] = '<div class="block-manage-your-projets background-color-f2f2f2">';
        out[++o] = '<div class="font-montserrat-700 font-size-26 color-000 margin-bottom-20">';
        out[++o] = 'Manage your projects';
        out[++o] = '</div>';
        out[++o] = '<div class="content">';
        user_innovation_collection.query().limit(0, 3).each(function(row, index) {
            out[++o] = get_welcome_manage_innovation_html(row);
        });
        out[++o] = '</div>';
        out[++o] = '<div class="margin-top-20 margin-bottom-20 text-align-center max-width-786">';
        out[++o] = '<a href="/content/manage" class="hub-button link-list-innovations">Access all your projects</a>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        // END BLOCK MANAGE YOUR PROJECTS
    }

    // BLOCK LAST INNOVATIONS
    var an_innovation_collection = new DataCollection(explore_initial_innovation_collection.query().filter().values());
    an_innovation_collection = new DataCollection(an_innovation_collection.query().order('sort_explore_date', true).values());

    out[++o] = '<div class="block-discover-last-innovations">';
    out[++o] = '<div class="font-montserrat-700 font-size-26 color-000 margin-bottom-20">';
    out[++o] = 'Discover the latest innovations in markets';
    out[++o] = '</div>';
    out[++o] = '<div class="content">';
    an_innovation_collection.query().limit(0, max_explore_innovations).each(function(row, index) {
        out[++o] = get_welcome_explore_innovation_html(row);
    });
    out[++o] = '</div>';
    out[++o] = '<div class="margin-top-20 margin-bottom-20 text-align-center max-width-786">';
    out[++o] = '<a href="/content/explore" class="hub-button link-list-explore">Explore all innovations</a>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    // END BLOCK LAST INNOVATIONS

    if ($('#main').attr('data-accept-newsletter') == '0') {
        // BLOCK Subscribe to our newsletter
        out[++o] = '<div class="block-welcome-left-grey background-color-f2f2f2 margin-bottom-20 margin-top-40  block-welcome-newsletter">';
        out[++o] = '<div class="font-montserrat-700 font-size-26 color-000 margin-bottom-20">';
        out[++o] = 'Subscribe to our newsletter';
        out[++o] = '</div>';
        out[++o] = '<div class="content margin-bottom-20">';
        out[++o] = '<div class="text-align-center">';
        out[++o] = '<div class="picture picture-welcome-newsletter"></div>';
        out[++o] = '<button class="hub-button pink action-subscribe-newsletter">';
        out[++o] = 'Subscribe';
        out[++o] = '</button>';
        out[++o] = '<div class="content-loader text-align-center"></div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        // END BLOCK MANAGE YOUR PROJECTS
    }

    out[++o] = '</div>'; // End column-percent-70

    out[++o] = '<div class="column-percent-30 padding-top-40">'; // Start column-percent-30

    /*
    if (check_if_user_has_monitor_access()) {
        // BLOCK MONITOR
        out[++o] = '<div class="welcome-block-right block-monitor hide-no-role background-color-90dce8 padding-top-30 padding-bottom-40 padding-left-40 padding-right-40">';
        out[++o] = '<div class="font-montserrat-700 font-size-22 color-000 margin-bottom-20">';
        out[++o] = 'Monitor innovation';
        out[++o] = '</div>';
        out[++o] = '<a class="picture link-monitor-innovations" href="/content/monitor"></a>';
        out[++o] = '<div class="text-align-center">';
        out[++o] = '<a class="hub-button pink link-monitor-innovations" href="/content/monitor">';
        out[++o] = 'Access your dashboard';
        out[++o] = '</a>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        // END BLOCK MONITOR
    }*/

    var class_bg_bleu_clair = "background-color-90dce8";
    var class_bg_bleu_fonce = "background-color-gradient-161541";

    if (check_if_user_has_new_business_model_access()) {
        // BLOCK  Business
        out[++o] = '<div class="welcome-block-right  ' + class_bg_bleu_clair + '  padding-top-30 padding-bottom-40 padding-left-40 padding-right-40">';
        out[++o] = '<div class="font-montserrat-700 font-size-22 color-000 line-height-1-5 margin-bottom-30">';
        out[++o] = 'You are managing New Business Models?';
        out[++o] = '</div>';
        out[++o] = '<a class="picture link-list-explore-new-business-model position-relative display-block text-align-center margin-bottom-30" href="/content/explore/tab/new_business_models">';
        out[++o] = '<img class="welcome-user height-128 width-301 margin-bottom-0" src="/images/welcome/illus-new-business-models.png"/>';
        out[++o] = '</a>';
        out[++o] = '<div class="text-align-center">';
        out[++o] = '<a class="hub-button pink link-list-explore-new-business-model" href="/content/explore/tab/new_business_models">';
        out[++o] = 'Manage New Business Models';
        out[++o] = '</a>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        // END BLOCK Business model

    }

    var class_complete_your_profile = (check_if_user_has_new_business_model_access()) ? class_bg_bleu_fonce : class_bg_bleu_clair;
    var class_title_complete_your_profile = (check_if_user_has_new_business_model_access()) ? 'color-fff' : 'color-000';
    // BLOCK Complete your profile
    out[++o] = '<div class="welcome-block-right block-complete-your-profile hide-no-role ' + class_complete_your_profile + ' padding-top-30 padding-bottom-40 padding-left-40 padding-right-40">';
    out[++o] = '<div class="font-montserrat-700 font-size-22 ' + class_title_complete_your_profile + ' margin-bottom-20">';
    out[++o] = 'Complete your profile';
    out[++o] = '</div>';
    out[++o] = '<a class="picture link-profile" href="/user"></a>';
    out[++o] = '<div class="text-align-center">';
    out[++o] = '<a class="hub-button pink link-profile" href="/user">';
    out[++o] = 'Complete your profile';
    out[++o] = '</a>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    // END BLOCK Complete your profile



    // BLOCK NEW PROJECT
    if (check_if_user_is_hq() || $('#main').hasClass('project-creation-enabled')) {
        var classe_background = (check_if_user_has_monitor_access()) ? 'background-color-ebebeb' : 'background-color-90dce8';
        out[++o] = '<div class="welcome-block-right block-new-project ' + classe_background + ' padding-top-30 padding-bottom-40 padding-left-40 padding-right-40 ">';
        out[++o] = '<div class="font-montserrat-700 font-size-22 color-000 margin-bottom-20">';
        out[++o] = 'Start your journey';
        out[++o] = '</div>';
        out[++o] = '<div class="picture action-open-popup" data-popup-target=".element-new-innovation-popup"></div>';
        out[++o] = '<div class="text-align-center">';
        out[++o] = '<a class="hub-button pink action-open-popup" data-popup-target=".element-new-innovation-popup">';
        out[++o] = 'Create new project';
        out[++o] = '</a>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    // END BLOCK NEW PROJECT

    // BLOCK VIDEO
    out[++o] = '<div class="welcome-block-right block-coming-soon background-color-f8f8f8 padding-top-30 padding-bottom-40 padding-left-40 padding-right-40 ">';
    out[++o] = '<div class="font-montserrat-700 font-size-22 color-000 margin-bottom-10">';
    out[++o] = 'What\'s the ' + global_wording['title'] + '?';
    out[++o] = '</div>';
    out[++o] = '<div class="font-work-sans-400 font-size-15 color-000 margin-bottom-20">';
    out[++o] = 'Get an overview of what the ' + global_wording['title'] + ' can do in this video';
    out[++o] = '</div>';
    out[++o] = '<div class="video-inner action-open-popup" data-popup-target=".element-video-popup">';
    out[++o] = '<img alt="" src="/images/masks/video-big.png">';
    out[++o] = '<button class="vjs-big-play-button" type="button" aria-live="polite" title="Play Video" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    // END BLOCK VIDEO


    /*
    // BLOCK COMING SOON
    out[++o] = '<div class="welcome-block-right background-color-gradient-161541 padding-top-30 padding-bottom-40 padding-left-40 padding-right-40">';
        out[++o] = '<div class="font-montserrat-700 font-size-22 color-fff margin-bottom-10 line-height-1-5">';
            out[++o] = 'We help you transform ideas into sustainable business value';
        out[++o] = '</div>';
        out[++o] = '<div class="font-work-sans-400 font-size-15 color-fff margin-bottom-30 line-height-1-53">';
            out[++o] = 'Meet the Global Innovation Team and discover how we can help you through your innovation journey';
        out[++o] = '</div>';
        out[++o] = '<div class="margin-bottom-30">';
            out[++o] = '<div class="c-about-us__person display-inline-block">';
                out[++o] = '<div class="c-about-us__person-photo" style="width:70px;height:70px;">';
                    out[++o] = '<img src="/images/about-us/people-paco.jpg" alt="" />';
                out[++o] = '</div>';
            out[++o] = '</div>';
            out[++o] = '<div class="c-about-us__person display-inline-block" style="margin-left:18px;">';
                out[++o] = '<div class="c-about-us__person-photo" style="width:70px;height:70px;">';
                    out[++o] = '<img src="/images/about-us/people-astrid.jpg" alt="" />';
                out[++o] = '</div>';
            out[++o] = '</div>';
            out[++o] = '<div class="c-about-us__person display-inline-block" style="margin-left:18px;">';
                out[++o] = '<div class="c-about-us__person-photo" style="width:70px;height:70px;">';
                    out[++o] = '<img src="/images/about-us/people-emily.jpg" alt="" />';
                out[++o] = '</div>';
            out[++o] = '</div>';
            out[++o] = '<div class="c-about-us__person display-inline-block" style="margin-left:17px;">';
                out[++o] = '<div class="c-about-us__person-photo" style="width:70px;height:70px;">';
                    out[++o] = '<img src="/images/about-us/people-simon.jpg" alt="" />';
                out[++o] = '</div>';
            out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '<div class="text-align-center">';
            out[++o] = '<a href="/about-us" class="hub-button pink">About us</a>';
        out[++o] = '</div>';
    out[++o] = '</div>';
    // END BLOCK COMING SOON
    */

    out[++o] = '</div>'; // End column-percent-30
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '</div>'; // End central-content-1280
    out[++o] = '</div>'; // End content white


    out[++o] = '<div class="background-color-gradient-161541 padding-top-40 padding-bottom-40">'; // Start Footer
    out[++o] = '<div class="central-content">'; // central-content
    out[++o] = '<div class="font-montserrat-700 font-size-22 color-fff margin-bottom-20">Useful downloads</div>';
    out[++o] = '<div class="overflow-hidden content-welcome-column-footer">'; // Start overflow-hidden

    out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
    out[++o] = '<div class="padding-20 text-align-center">'; // Start padding-20
    out[++o] = '<img src="/images/welcome/illus-innovation-leaflet.png" width="185" height="140" class="margin-bottom-20"/>';
    out[++o] = '<div class="font-montserrat-700 font-size-18 color-fff margin-bottom-5">Innovation leaflet</div>';
    out[++o] = '<div class="font-work-sans-400 font-size-15 line-height-1-2 color-e1e1e1 margin-bottom-30">';
    out[++o] = 'Craving to transform ideas into sustainable business value?<br>';
    out[++o] = 'Download our innovation leaflet and learn more<br>';
    out[++o] = 'about how we can help';
    out[++o] = '</div>';
    out[++o] = '<a class="hub-button pri-generate-activity-download-action" data-action_id="21" target="_blank" href="/documents/Leading for Innovation in your organisation .pdf"><span class="icon-ddl"></span> <span>Innovation leaflet</span></a>';
    out[++o] = '</div>'; // End padding-20
    out[++o] = '</div>'; // End column-percent-50

    out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
    out[++o] = '<div class="padding-20 text-align-center">'; // Start padding-20
    out[++o] = '<img src="/images/welcome/illus-icon-set.png" width="185" height="140" class="margin-bottom-20"/>';
    out[++o] = '<div class="font-montserrat-700 font-size-18 color-fff margin-bottom-5">Pernod Ricard Innovation icon set</div>';
    out[++o] = '<div class="font-work-sans-400 font-size-15 line-height-1-2 color-e1e1e1 margin-bottom-30">';
    out[++o] = 'Download our innovation icon set for your presentations<br>';
    out[++o] = '(the 3 Success Criteria, the Innovation Funnel,<br>';
    out[++o] = 'the Consumer Opportunities)';
    out[++o] = '</div>';
    out[++o] = '<a class="hub-button pri-generate-activity-download-action" data-action_id="20" target="_blank" href="/documents/Pernod Ricard Innovation Icon Set.zip?v=2"><span class="icon-ddl"></span> <span>Icon set</span></a>';
    out[++o] = '</div>'; // End padding-20
    out[++o] = '</div>'; // End column-percent-50

    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End Footer

    out[++o] = '</div>'; // End full-content-page-welcome
    return out.join('');
}