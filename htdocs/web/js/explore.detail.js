var feedback_tab_info = {
    'total_feedbackers': 0,
    'total_feedbacks': 0,
    'offset_feedbackers': 0,
    'offset_feedbacks': 0
};
var explore_financial_tab_table_request = null;
var explore_financial_tab_innovationFinancialLineChart = null;
var explore_detail_dataset_innovation = null;



function get_collection_for_name(name) {
    switch (name) {
        case 'consumer_opportunity':
            return get_consumer_opportunities();
    }
    return [];
}



/**
 * get_innovation_entity_brand_libelle
 * @param innovation
 * @returns {string}
 */
function get_innovation_entity_brand_libelle(innovation){
    var innovation_entity_title = (innovation['entity']) ? innovation['entity']['title'] : '';
    var innovation_brand_title = 'Missing data';
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if (innovation['brand']['title']) {
        innovation_brand_title = innovation['brand']['title'];
    }
    if (innovation['new_to_the_world']) {
        innovation_brand_title = 'New to the world';
    }
    if(innovation_is_a_service && innovation['is_multi_brand'] == '1'){
        innovation_brand_title = 'Multi brand';
    }
    return innovation_entity_title + ' • ' + innovation_brand_title;
}



/**
 * goToExploreDetailPage
 * @param id
 * @param tab
 * @param isFeedbackInv
 */
function goToExploreDetailPage(id, tab, isFeedbackInv, link) {
    try {
        before_opening_page();
        tab = tab || 'overview';
        isFeedbackInv = isFeedbackInv || false;
        if(typeof link == 'undefined'){
            link = '/explore/' + id;
            if(tab != 'overview'){
                link += '/tab/'+tab;
            }
        }
        var innovation = getInnovationById(id, true);
        if (!innovation) {
            add_flash_message('error', '[404] - Innovation does not exist.');
            goToExploreListPage();
            return;
        }
        var has_link_to_manage = (user_can_edit_this_innovation(innovation));
        var innovation_is_nba  = check_if_innovation_is_nba(innovation);
        $('#main').addClass('loading');
        opti_update_url(link, 'explore_detail');
        update_menu_active('menu-li-explore');
        changePageTitle(innovation['title'] + ' | ' + global_wording['title']);
        $('#pri-main-container-without-header').html(explore_detail_get_html_template(innovation, tab, isFeedbackInv));
        init_explore_detail(innovation);
        if(has_link_to_manage) {
            explore_detail_init_edit(innovation);
            explore_init_tab_activities(innovation['id']);
            explore_generate_feedback_tab(innovation['id']);
            explore_init_tab_team(innovation['id']);
            explore_init_tab_financial(innovation['id']);
            if(tab == 'markets'){
                explore_detail_init_markets_tab();
            }
            if(innovation_is_nba){
                explore_init_tab_canvas(innovation['id']);
            }
        }
        window.scrollTo(0, 0);
        launchBlueArrowScroll();
    } catch (error) {
        init_unknow_error_view(error);
    }
}

/**
 * explore_detail_get_html_template
 * @param innovation
 * @param tab
 * @param isFeedbackInv
 * @returns {string}
 */
function explore_detail_get_html_template(innovation, tab, isFeedbackInv){
    var out = [], o = -1;
    var current_stage = (innovation['current_stage'] && innovation['current_stage'] != 'Empty' && innovation['current_stage'] != 'empty') ? innovation['current_stage'] : 'empty';
    var previous_stage = (innovation['previous_stage'] && innovation['previous_stage'] != 'Empty' && innovation['previous_stage'] != 'empty') ? innovation['previous_stage'] : 'empty';
    var value = "";
    var consumer_opportunity = innovation['consumer_opportunity'];
    var has_link_to_manage = (user_can_edit_this_innovation(innovation));
    var is_only_reader = check_if_user_is_only_reader_of_innovation(innovation);
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    var innovation_is_early_stage = (in_array(innovation['current_stage'], ['discover','ideate','experiment']));
    var innovation_is_on_explore = (!innovation_is_a_service && !innovation_is_early_stage);
    var innovation_is_nba  = check_if_innovation_is_nba(innovation);

    var classe = (has_link_to_manage) ? "full-content-explore-detail pri-tab" : "full-content-explore-detail";
    out[++o] = '<div class="' + classe + '">';
    out[++o] = '<input type="hidden" id="detail-id-innovation" value="' + innovation['id'] + '"/>';
    if (has_link_to_manage) {
        out[++o] = '<div class="element-sub-header-menu-tab">';
        out[++o] = '<div class="central-content">';
        out[++o] = '<div class="text-align-right">'; // Start text-align-right
        out[++o] = '<ul class="pri-tab-menu color-white  display-inline-block">';
        out[++o] = '<li ' + ((tab == 'overview') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#overview">Overview</a></li>';
        if (!is_only_reader) {
            out[++o] = '<li ' + ((tab == 'edit') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#edit">Edit <span class="round-missing-fields display-none edit">0</span></a></li>';
            if(innovation_is_nba) {
                out[++o] = '<li ' + ((tab == 'canvas') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#canvas">Canvas</a></li>';
            }
            out[++o] = '<li ' + ((tab == 'activities') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#activities">Activities</a></li>';
            out[++o] = '<li id="tab-feedback" ' + ((tab == 'feedback') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#feedback">Feedback</a></li>';
            out[++o] = '<li ' + ((tab == 'financial') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#financial">Financial <span class="round-missing-fields display-none financial">0</span></a></li>';
            if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate', 'experiment'])) {
                out[++o] = '<li ' + ((tab == 'markets') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#markets">Markets <span class="round-missing-fields display-none market">0</span></a></li>';
            }
            if(innovation_is_nba) {
                out[++o] = '<li ' + ((tab == 'key-cities') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#key-cities">Key cities</a></li>';
            }
            out[++o] = '<li ' + ((tab == 'team') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#team">Team</a></li>';
        }
        out[++o] = '</ul>';
        out[++o] = '<div class="content-buttons display-inline-block">'; // Start content-buttons
        out[++o] = '<button class="hub-button light-blue height-26 async-export margin-left-10" data-action="/api/export/innovation-to-ppt" data-id="' + innovation['id'] + '" data-export-type="quali_full" data-title="Exporting to PPT…"><span class="icon-ddl"></span> <span>Export .ppt</span></button>';
        if (innovation_is_on_explore) {
            out[++o] = '<button id="button-share-innovation" class="hub-button pink height-26 action-open-popup margin-left-10" data-popup-target=".element-share-innovation-popup">Share</button>';
        }
        out[++o] = '</div>'; // End content-buttons
        out[++o] = '</div>'; // End text-align-right
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '<div class="fake-element-sub-header-menu-tab"></div>';
    }
    out[++o] = '<div id="overview">'; // Start overview
    out[++o] = explore_generate_overview_tab(innovation['id'], isFeedbackInv);
    out[++o] = '</div>'; // End overview
    if (has_link_to_manage) {
        out[++o] = '<div id="edit">' + explore_generate_edit_tab(innovation['id']) + '</div>';
        if(innovation_is_nba) {
            out[++o] = '<div id="canvas">' + explore_get_canvas_tab_html_template(innovation['id']) + '</div>';
        }
        out[++o] = '<div id="activities">' + explore_get_activities_tab_html_template(innovation['id']) + '</div>';
        out[++o] = '<div id="feedback"><div class="background-color-fff padding-vertical-40 text-align-center"><div class="display-inline-block loading-spinner loading-spinner--26"></div></div></div>';
        out[++o] = '<div id="financial">'+ explore_get_financial_tab_html_template(innovation['id']) +'</div>';
        if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate', 'experiment'])) {
            out[++o] = '<div id="markets">'+explore_get_markets_tab_html_template(innovation['id'])+'</div>';
        }
        if(innovation_is_nba) {
            out[++o] = '<div id="key-cities">' + explore_get_key_cities_tab_html_template(innovation['id']) + '</div>';
        }
        out[++o] = '<div id="team">' + explore_get_team_tab_html_template(innovation['id']) + '</div>';
    }
    out[++o] = '</div>';

    return out.join('');
}


/**
 * explore_detail_update_nb_missing_fields
 */
function explore_detail_update_nb_missing_fields(){
    var id = parseInt($('#detail-id-innovation').val());
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }
    $('.full-content-explore-detail .round-missing-fields').addClass('display-none');
    if(innovation['empty_fields']){
        if(innovation['empty_fields']['financial'] > 0){
            $('.full-content-explore-detail .round-missing-fields.financial').removeClass('display-none').html(innovation['empty_fields']['financial']);
        }
        if(innovation['empty_fields']['edit'] > 0){
            $('.full-content-explore-detail .round-missing-fields.edit').removeClass('display-none').html(innovation['empty_fields']['edit']);
        }
        if(innovation['empty_fields']['market'] > 0){
            $('.full-content-explore-detail .round-missing-fields.market').removeClass('display-none').html(innovation['empty_fields']['market']);
        }
    }
}



