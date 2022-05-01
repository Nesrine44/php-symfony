var global_wording = null;
var user_rights = null;
var metrics_key = null;
var current_user = null;
var user_roles = [];
var user_search_history = [];
var data_ping = null;
var data_filters = null;
var innovation_list_filters = null;
var data_update_date = null;
var data_consolidation_all = null;
var other_datas = null;
var updating_data = false;
var main_classes = "";
var main_skills = [];

var request_export = null;
var request_update_datas = null;
var request_load_activities = null;
var request_get_promote_infos = null;
var request_load_people_rights = null;
var request_check_to_update_datas = null;

var search_data_innovations = null;
var data_all_innovations = null;

var allInnovationsDC = new DataCollection();
var default_innovation_list_limit = 50;
var previous_url = null;
var is_popstate_back = false;


/**
 * Instanciate fastclick
 */
if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        FastClick.attach(document.body);
    }, false);
}

/**
 * Update url (for ajax use, changing the history)
 * @param href
 * @param classe
 */
function opti_update_url(href, classe) {
    if (typeof classe === 'undefined') {
        classe = '';
    }
    if (classe != 'no_change') {
        $('#main').removeClass('list-explore list-manage list-monitor list-connect user-profile compare_page compare_page_detail welcome_page explore_detail search-results').addClass(classe + ' loading');
    }
    previous_url = window.location.pathname;
    // Add the location hash via pushState.
    if (window.history.pushState) {

        window.history.pushState({ href: href }, "", href);
        is_popstate_back = false;
        opti_update_mouseflow(href);
    } else {
        window.location = href;
    }
}

/**
 * Update url (for ajax use, changing the history)
 * @param href
 * @param classe
 */
function opti_update_mouseflow(current_path, tab) {
    // Add the location hash via pushState.
    if (window.history.pushState) {
        window._mfq = window._mfq || [];
        var newPageUrl = get_mouseflow_url(current_path, tab);
        // Send new page to Mousflow
        window._mfq.push(["newPageView", newPageUrl]);
        //console.log('mouseflow', newPageUrl);

        // Send new page to google analytics
        gtag('config', 'UA-127686150-1', { 'page_path': current_path });
    }
}

/**
 * get_mouseflow_url
 * @param current_path
 * @param tab
 * @returns {string}
 */
function get_mouseflow_url(current_path, tab) {
    var user_type = get_mouseflow_type();
    var path_exploder = current_path.split("?");
    current_path = path_exploder[0];
    var post_url = '?type=' + user_type;

    if (current_path.indexOf('/explore/') !== -1) { // si page explore detail
        var exploder = current_path.split("/");
        var innovation_id = parseInt(exploder[2]);
        var innovation = (innovation_id) ? getInnovationById(innovation_id, true) : null;
        if (innovation) {
            var ret_url = "/explore/<detail>";
            if (user_type == 'maker') {
                post_url = (user_can_edit_this_innovation(innovation)) ? "'?type=maker" : "'?type=maker_explorer";
            }
            if (typeof tab != 'undefined') {
                post_url += '&tab=' + tab;
            } else {
                post_url += get_proper_tab_for_url(current_path);
            }
            post_url += get_proper_stage_for_url(innovation);
            post_url += get_proper_classification_type_for_url(innovation);;
            return ret_url + post_url;
        }
    }
    if (typeof tab != 'undefined') {
        post_url += '&tab=' + tab;
    }
    var simple_strpos_urls = [
        '/user',
        '/content/search'
    ];
    for (var i = 0; i < simple_strpos_urls.length; i++) {
        var simple_strpos_url = simple_strpos_urls[i];
        if (current_path.indexOf(simple_strpos_url) !== -1) {
            return simple_strpos_url + post_url;
        }
    }
    return current_path + post_url;
}

/**
 * get_mouseflow_type
 * @returns {string}
 */
function get_mouseflow_type() {
    if (check_if_user_is_dev()) {
        return 'dev';
    } else if (check_if_user_is_hq()) {
        return 'hq';
    } else if (check_if_user_is_management()) {
        return 'management';
    } else if (check_if_user_is_managing_director()) {
        return 'managing_director';
    } else if (check_if_user_has_no_role()) {
        return 'other';
    }
    return 'maker';
}


function init_wording(data) {
    global_wording = data;
}
/**
 * Initialisation des données
 * @param data
 */
function optimization_init(data) {
    if (!data) {
        return;
    }
    data_ping = data['ping'];
    if (data['user_rights']) {
        user_rights = data['user_rights'];
    }
    if (data['user_roles']) {
        user_roles = data['user_roles'];
    }
    if (!data['all_innovations']) {
        return;
    }
    data_all_innovations = update_sort_explore_date(data['all_innovations']);
    allInnovationsDC = new DataCollection(data_all_innovations);
    monitor_data_filters = data['monitor_filters'];
    data_update_date = data['update'];
    data_consolidation_all = data['all_consolidation'];
    other_datas = data['other_datas'];
    main_classes = data['main_classes'];
    current_user = data['current_user'];
    user_search_history = data['search_history'];
    main_skills = data['popup-new']['main_skills'];
    update_main_classes(main_classes);
    setExplore_innovation_collection();
    setManage_innovation_collection();
    setSearch_innovation_data();
    setAccessManageInnovations(current_user['id']);
}

function setExplore_innovation_collection() {
    var innovations = allInnovationsDC.query().filter({
        proper__classification_type__is: 'product',
        current_stage_id__in: [4, 5],
        is_frozen: false
    }).values();
    explore_initial_innovation_collection = new DataCollection(innovations);

    var innovations = allInnovationsDC.query().filter({
        proper__classification_type__is: 'service'
    }).values();
    explore_new_business_models_initial_innovation_collection = new DataCollection(innovations);
}

/**
 * check_if_innovation_is_on_explore
 * @param id
 * @returns {boolean}
 */
function check_if_innovation_is_on_explore(id) {
    var target = explore_initial_innovation_collection.query().filter({ id__is: id }).values()
    if (!target || target && target.length <= 0) {
        return false;
    }
    return true;
}

function setManage_innovation_collection() {
    var innovations = [];
    allInnovationsDC.query().each(function(row, index) {
        if (user_has_access_to_manage_innovation(row)) {
            innovations.push(row);
        }
    });
    manage_data_innovations = innovations;
    manage_initial_innovation_collection = new DataCollection(manage_data_innovations);
}

function setSearch_innovation_data() {
    search_data_innovations = explore_initial_innovation_collection.query().values();
    /*
    if (check_if_user_is_hq()) {
        search_data_innovations = data_all_innovations;
    } else {
        var explore_innovations = explore_initial_innovation_collection.query().values();
        search_data_innovations = [];
        for (var i in explore_innovations) {
            var shared = false;
            for (var j in manage_data_innovations)
                if (manage_data_innovations[j]['id'] == explore_innovations[i]['id']) {
                    shared = true;
                    break;
                }
            if (!shared) search_data_innovations.push(explore_innovations[i])
        }
        search_data_innovations = search_data_innovations.concat(manage_data_innovations);
    }*/
}


/**
 * manage_get_innovation_html
 *
 * @param innovation
 * @returns {string}
 */
function access_manage_get_innovation_html(innovation) {
    var out = [],
        o = -1;
    out[++o] = '';
    var explore_detail_url = "/explore/" + innovation['id'];
    out[++o] = '<div class="content-access-innovation transition-background-color">'; // Start content-access-innovation
    out[++o] = '<a href="' + explore_detail_url + '" class="link_explore_detail global-link">'; // Start global-link
    var picture = (innovation['explore']['images_detail'].length > 0) ? innovation['explore']['images_detail'][0] : innovation['explore']['images'][0];
    var style_background = 'style="background-image:url(' + picture + ');"';
    out[++o] = '<div class="picture" ' + style_background + '></div>';
    out[++o] = '<div class="content-infos">'; // Start content-infos
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-14 text-ellipsis">' + innovation['title'] + '</div>';
    out[++o] = '<div class="font-montserrat-500 color-b5b5b5 font-size-13 margin-bottom-5">'; // Start entity-brand
    out[++o] = (innovation['entity']) ? innovation['entity']['title'] : '';
    out[++o] = ' • ';
    out[++o] = (innovation['brand']) ? innovation['brand']['title'] : '';
    out[++o] = '</div>'; // End entity-brand
    out[++o] = '<div class="font-work-sans-400 color-787878 font-size-12">' + returnGoodStage(innovation['current_stage']) + '</div>';
    out[++o] = '</div>'; // End content-infos
    var count_empty_fields = innovation['count_empty_fields'];
    var round_nb = (count_empty_fields > 0) ? '<span class="round-nb-emtpy-field tooltip-right-tr force-tooltip" title="' + count_empty_fields + ' empty fields">' + count_empty_fields + '</span>' : '';
    out[++o] = round_nb;
    out[++o] = '</a>'; // Start global-link
    out[++o] = '</div>'; // End content-access-innovation
    return out.join('');
}

/**
 * get_access_manage_innovations_ids_for_user_id
 * @param user_id
 * @returns {Array}
 */
function get_access_manage_innovations_ids_for_user_id(user_id) {
    var innovations_ids = [];
    var user_right_innovation_ids = [];
    for (var i = 0; i < user_rights.length; i++) {
        if (user_rights[i]['right'] == 'write' && !in_array(user_rights[i]['innovation_id'], user_right_innovation_ids)) {
            user_right_innovation_ids.push(user_rights[i]['innovation_id']);
        }
    }

    // User rights
    allInnovationsDC.query().filter({
        id__in: user_right_innovation_ids,
        current_stage_id__in: [1, 2, 3, 4, 5],
        is_frozen: false
    }).each(function(row, index) {
        if (!in_array(row['id'], innovations_ids)) {
            innovations_ids.push(row['id']);
        }
    });

    // CONTACT
    allInnovationsDC.query().filter({
        contact__uid__is: user_id,
        current_stage_id__in: [1, 2, 3, 4, 5],
        is_frozen: false
    }).each(function(row, index) {
        if (!in_array(row['id'], innovations_ids)) {
            innovations_ids.push(row['id']);
        }
    });

    return innovations_ids;
}


function setAccessManageInnovations(user_id) {
    setTimeout(function() {
        var innovations_ids = get_access_manage_innovations_ids_for_user_id(user_id);
        $('.button-access-manage .list').html('');
        if (innovations_ids.length == 0) {
            $('.button-access-manage').removeClass('display-inline-block');
        } else {
            $('.button-access-manage').addClass('display-inline-block');
            var an_innovation_collection = new DataCollection(allInnovationsDC.query().filter({ id__in: innovations_ids }).values());
            var out = [],
                o = -1;
            an_innovation_collection.query().order('proper__title', false).each(function(row, index) {
                out[++o] = access_manage_get_innovation_html(row);
            });
            $('.button-access-manage .list').html(out.join(''));
            init_tooltip();
        }
    }, 20);
}

function update_sort_explore_date(all_innovations) {
    for (var i = 0; i < all_innovations.length; i++) {
        if (all_innovations[i]['sort_explore_date'] && all_innovations[i]['in_market_date'] <= get_current_timestamp()) {
            all_innovations[i]['sort_explore_date'] = all_innovations[i]['sort_explore_date'] + 1000000000;
        }
    }
    return all_innovations;
}
/**
 * A lancer après chaque update
 */
function optimization_update() {
    init_tooltip();
}


/* LIST
 ----------------------------------------------------------------------------------------*/


/**
 * Transform in market string to timestamp
 * @param date_string
 * @returns {number}
 */
function get_in_market_date_string_to_timestamp(date_string) {
    var date_array = date_string.split('/');
    var new_date_string = date_array[2] + "/" + date_array[0] + "/" + date_array[1] + " 00:00:00";
    return Math.round(new Date(new_date_string).getTime() / 1000);
}


/**
 * update_export_selection_params_array
 * @param ids
 */
function update_export_selection_params_array(ids) {
    var params = {
        'innovations_ids': ids,
        'filters': innovation_list_filters
    };
    $('.export-selection.async-export').attr('data-params_array', JSON.stringify(params));
}


/**
 * get_user_by_id
 * @param id
 * @return {*}
 */
function get_user_by_id(id) {
    id = parseInt(id);
    var listContacts = new DataCollection(other_datas['contacts_json']);
    var target = listContacts.query().filter({ id__is: id }).values();
    if (!target || target && target.length <= 0) {
        return null;
    }
    return target[0];
}

/**
 * create_metrics
 *
 * @param innovation_id
 * @param action_id
 */
function create_metrics(innovation_id, action_id) {
    jQuery.ajax({
        url: '/api/metrics/generate',
        data: { innovation_id: innovation_id, key: metrics_key, action_id: action_id },
        type: "POST",
        success: function(the_data) {

        },
        error: function(resultat, statut, e) {

        }
    });
}

/* Async Export
 ----------------------------------------------------------------------------------------*/

function asyncExport(url, title, type, params_string, innovation_id, export_type) {
    var progress_delay = 1000;
    var progress = 0;
    var target_url = null;
    var export_id = null;
    var activity_id = null;
    open_popup('.element-export-popup', function() {
        $('.element-export-popup').attr('data-page_title', $(document).find("title").text());
        changePageTitle('Exporting (' + progress + '%) | ' + global_wording['title']);
        $('.element-export-popup .title').html(title);
        $('.content-eventsource-progress .percent span').text(progress);
        $('.content-eventsource-progress .content-bar .bar').css('width', progress + "%");
    });
    //Start the long running process
    if (request_export != null)
        request_export.abort();
    request_export = $.ajax({
        url: url,
        data: {
            type: type,
            params_string: params_string,
            innovation_id: innovation_id,
            export_type: export_type,
            token: $('#hub-token').val()
        },
        type: "POST",
        success: function(the_data) {
            if (the_data.hasOwnProperty('status') && the_data['status'] == 'error') {
                disable_load_popup();
                add_flash_message('error', the_data['message'], false);
                if (request_export != null)
                    request_export.abort();
                close_popup('.element-export-popup');
            } else {
                var progress = 3;
                $('.content-eventsource-progress .percent span').text(progress);
                changePageTitle('Exporting (' + progress + '%) | ' + global_wording['title']);
                $('.content-eventsource-progress .content-bar .bar').css('width', progress + "%");
                target_url = the_data['url'];
                export_id = the_data['export_id'];
                $('.element-export-popup').attr('data-export_id', export_id);
                activity_id = the_data['activity_id'];
                $('.element-export-popup').attr('data-activity_id', activity_id);
                getAsyncProgress();
            }
        },
        error: function(resultat, statut, e) {
            if (ajax_on_error(resultat, statut, e)) {
                return;
            }
            if (resultat.status == 200) {
                var progress = 100;
                $('.content-eventsource-progress .percent span').text(progress);
                $('.content-eventsource-progress .content-bar .bar').css('width', progress + "%");
                location.reload();
            } else if (resultat.statusText == 'abort') {
                return;
            } else {
                add_flash_message('error', 'Error : failed to export data, please reload this page', false);
                if (request_export != null)
                    request_export.abort();
                close_popup('.element-export-popup');
            }
        }
    });
    //Start receiving progress
    function getAsyncProgress() {
        if ($('.element-export-popup').hasClass('opened')) {
            setTimeout(function() {
                $.ajax({
                    url: '/api/export/get-progress',
                    data: { export_id: export_id, activity_id: activity_id },
                    type: "POST",
                    success: function(data) {
                        if ($('.element-export-popup').hasClass('opened')) {
                            var progress = (data['progress'] < 0) ? 100 : data['progress'];
                            changePageTitle('Exporting (' + progress + '%) | ' + global_wording['title']);
                            $('.content-eventsource-progress .percent span').text(progress);
                            $('.content-eventsource-progress .content-bar .bar').css('width', progress + "%");
                            if (data['progress'] == -1) { // ERROR
                                add_flash_message('error', 'Error : failed to export data, please reload this page or try later.', false);
                                close_popup('.element-export-popup');
                            } else if (data['progress'] == 100) {
                                window.location.href = target_url + "?time=" + Date.now();
                                setTimeout(function() {
                                    var innovation_id = $('.explore_detail  .link-export-ppt').attr('data-id');
                                    if (innovation_id) {
                                        explore_initVisualExplorePitchous(parseInt(innovation_id));
                                    }
                                    close_popup('.element-export-popup');
                                }, 1000);
                            } else {
                                getAsyncProgress();
                            }
                        }
                    },
                    error: function(resultat, statut, e) {
                        if (ajax_on_error(resultat, statut, e)) {
                            return;
                        }
                        getAsyncProgress();
                    }
                });
            }, progress_delay);
        }
    }
}

/**
 * Reset current export key
 */
function reset_async_progress_by_export_id() {
    changePageTitle($('.element-export-popup').attr('data-page_title'));
    $('.element-export-popup').attr('data-page_title', "");
    var key = $('.element-export-popup').attr('data-export_id');
    $.ajax({
        url: '/api/export/reset-progress',
        data: { export_key: key },
        type: "POST",
        success: function(data) {
            $('.element-export-popup').attr('data-export_id', "");
            $('.element-export-popup').attr('data-activity_id', "");
            $('.element-export-popup').attr('data-key', "");
        },
        error: function(resultat, statut, e) {
            $('.element-export-popup').attr('data-export_id', "");
            $('.element-export-popup').attr('data-activity_id', "");
            $('.element-export-popup').attr('data-key', "");
        }
    });
}


/**
 * after_update_collection
 */
function after_update_collection() {
    setExplore_innovation_collection();
    setManage_innovation_collection();
    setSearch_innovation_data();
    setAccessManageInnovations(current_user['id']);
}
/**
 * Remove innovation from collection
 * @param id
 */
function removeFromCollection(id) {
    id = parseInt(id);
    var oDC_all = new DataCollection(data_all_innovations);
    oDC_all.query().filter({ id__is: id }).remove();
    data_all_innovations = oDC_all.query().values();
    allInnovationsDC = new DataCollection(data_all_innovations);
    after_update_collection();
}

/**
 * Add innovation to collection
 * @param id
 */
function addToCollection(id, new_value) {
    id = parseInt(id);
    var oDC_all = new DataCollection(data_all_innovations);
    var nb_all = oDC_all.query().filter({ id__is: id }).count();
    if (nb_all == 0) {
        oDC_all.insert(new_value);
        data_all_innovations = oDC_all.query().values();
        allInnovationsDC = new DataCollection(data_all_innovations);
        after_update_collection();
    }
}

/**
 * Update innovation from collection
 * @param id
 */
function updateFromCollection(id, new_value) {
    id = parseInt(id);
    var oDC_all = new DataCollection(data_all_innovations);
    oDC_all.query().filter({ id__is: id }).remove();
    oDC_all.insert(new_value);
    data_all_innovations = oDC_all.query().values();
    allInnovationsDC = new DataCollection(data_all_innovations);
    after_update_collection();
}


/* OTHERS
 ----------------------------------------------------------------------------------------*/

/**
 * changePageTitle
 * @param title
 */
function changePageTitle(title) {
    document.title = title;
}

/**
 * before_opening_page
 */
function before_opening_page() {
    closeSearchBar();
    if (!$('html').hasClass('no-close-popup-on-change-page')) {
        close_popup();
    }
    $('html').removeClass('background-color-f2f2f2 height-100 no-scroll background-color-gradient-161541 no-close-popup-on-change-page');
    $('html, body').removeClass('background-color-fff');
    $('#mosaique-sticky-menu .mosaique-content-all-filters .filter').off('click');
    $('#pri-main-container').off('scroll');
    $(window).off('scroll');
    $('.content-result-search-tag .search-tag').off('click');
    $('.content-tranding-tags .trending-tag').off('click');
    init_scroll_blue_arrow();
}


/**
 * init_unknow_error_view.
 */
function init_unknow_error_view(error) {
    console.error(error);
    $('#main').removeClass('list-explore list-manage list-monitor list-connect user-profile compare_page compare_page_detail welcome_page explore_detail');
    before_opening_page();
    var out = [],
        o = -1;
    if ($('html').hasClass('ie') && ($('html').hasClass('v-8') || $('html').hasClass('v-7') || $('html').hasClass('v-6'))) {
        out[++o] = '<div class="content-twig-exception">';
        out[++o] = '<img src="/images/exceptions/broken-browser.png" class="img-error-browser" />';
        out[++o] = '<div class="text-align-center font-work-sans-600 color-fff-0-5 margin-bottom-10 font-size-24 line-height-1-33">Unfortunately, you are using Internet Explorer,<br>which is obsolete...</div>';
        out[++o] = '<div class="text-align-center font-montserrat-700 color-fff font-size-34 line-height-1-33">Please use another web browser like<br>Microsoft Edge, Firefox or Chrome</div>';
        out[++o] = '</div>';
    } else {
        out[++o] = '<div class="content-twig-exception">';
        out[++o] = '<img src="/images/exceptions/broken-browser.png" class="img-error-browser" />';
        out[++o] = '<div class="text-align-center font-work-sans-600 color-fff-0-5 margin-bottom-10 font-size-24 line-height-1-33">An unknown error appeared</div>';
        out[++o] = '<div class="text-align-center font-montserrat-700 color-fff margin-bottom-10 font-size-34 line-height-1-33">Please reload this page<br>or try with another web browser</div>';
        out[++o] = '<div class="text-align-center font-montserrat-500 color-fff-0-5 font-size-14 line-height-1-33">If the problem persist, please <a class="color-fff-0-5 cursor-pointer text-decoration-underline" href="' + getContactMail() + '">contact us</a></div>';
        out[++o] = '</div>';
    }
    $('html').addClass('height-100 background-color-gradient-161541');
    $('#pri-main-container-without-header').html(out.join(''));
}