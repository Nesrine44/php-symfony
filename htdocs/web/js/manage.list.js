var manage_loading_more = false;
var manage_all_are_loaded = false;
var manage_data_innovations = null;
var manage_initial_innovation_collection = new DataCollection();
var manage_filtered_innovation_collection = new DataCollection();
var manage_innovation_offset = 0;
var manage_innovation_limit = 20;
var manage_data_stage = null;
var manage_statistics = null;
var manage_stats_loaded = false;
var manage_stageChart = null;
var manage_filters = manage_get_default_filters();
var manage_is_filtered = false;
var manage_color_empty = "rgba(255,255,255,0.1)"; //"#79879E";
var manage_border_color = "#161F4F";
var manage_chart_border_color_array = [manage_border_color, manage_border_color, manage_border_color, manage_border_color, manage_border_color, manage_border_color, manage_border_color];
var manage_chart_stage_background_array = ['#787878', "#4f6e99", "#fab700", "#45ab34", "#e8485c", "#80a7d0", manage_color_empty];
var manage_chart_stage_labels_array = ["out_of_funnel", "discover", "ideate", "experiment", "incubate", "scale_up", "empty"];

var manage_md_data = {
    'recommandation': null,
    'competitors': null
};

/**
 * manage_get_default_filters
 * @returns {{coop: Array, portfolio: Array, growth_model: Array, sort: string, tag: Array, current_stage: Array, classification: Array}}
 */
function manage_get_default_filters() {
    return {
        'sort': 'relevance',
        'tag': [],
        'classification': [],
        'portfolio': [],
        'coop': [],
        'moc': [],
        'current_stage': [],
        'growth_model': []
    };
}

/**
 * Manage set filters by url
 */
function manage_set_filters_by_url() {
    var getVars = getUrlVars();
    manage_filters = manage_get_default_filters();
    manage_is_filtered = false;
    if (Object.keys(getVars).length > 0) {
        if (getVars.hasOwnProperty('filter[tag]')) {
            manage_filters['tag'] = rawSplit(getVars['filter[tag]'], ',');
            manage_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[classification]')) {
            manage_filters['classification'] = rawSplit(getVars['filter[classification]'], ',');
            manage_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[portfolio]')) {
            manage_filters['portfolio'] = rawSplit(getVars['filter[portfolio]'], ',');
            manage_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[coop]')) {
            manage_filters['coop'] = splitToInt(getVars['filter[coop]'], ',');
            manage_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[moc]')) {
            manage_filters['moc'] = rawSplit(getVars['filter[moc]'], ',');
            manage_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[growth_model]')) {
            manage_filters['growth_model'] = rawSplit(getVars['filter[growth_model]'], ',');
            manage_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[current_stage]')) {
            manage_filters['current_stage'] = splitToInt(getVars['filter[current_stage]'], ',');
            manage_is_filtered = true;
        }
    }
    var sort = findGetParameter('sort');
    if (!sort) {
        sort = 'relevance';
    }
    manage_filters['sort'] = sort;
}

/**
 * goToManageList
 * @param link
 * @return {boolean}
 */
function goToManageList(link, tab) {
    try {
        before_opening_page();

        link = link || "/content/manage";
        tab = tab || "overview";

        $('#main').addClass('loading');
        opti_update_url(link, 'list-manage');
        $('html, body').addClass('background-color-fff');
        update_menu_active('menu-li-manage');
        changePageTitle('Manage | ' + global_wording['title']);
        update_main_classes(main_classes);
        $('#pri-main-container-without-header').html(manage_get_html_template(link, tab));
        manage_init();
    } catch (error) {
        init_unknow_error_view(error);
    }
}

function manage_generate_overview_tab() {
    var out = [],
        o = -1;
    manage_set_filters_by_url();
    var sort = manage_filters['sort'];
    var sort_name = mosaique_get_sort_name(manage_filters['sort']);

    var filter_on_class = ($(window).width() > 700) ? 'filter-on' : '';
    out[++o] = '<div class="full-content-list-manage mosaique-page-container ' + filter_on_class + '">'; // Start full-content-list-manage


    out[++o] = '<div class="content-inner">'; // Start Content inner

    out[++o] = '<div class="content-manage-consolidation content-mosaique-consolidation background-color-gradient-161541">'; // Start content-manage-consolidation
    out[++o] = '<div class="central-content padding-top-20 padding-bottom-20">'; // Start central-content
    out[++o] = manage_get_consolidation_html_template();
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End content-manage-consolidation

    out[++o] = '<div class="background-color-fff content-list-manage content-mosaique-list  ' + ((manage_is_filtered) ? 'filtered' : '') + '">'; // Start content-list-manage

    out[++o] = '<div class="sticky-entete z-index-5 position-relative" id="mosaique-sticky-menu" data-sticky-to="90">'; // Start #mosaique-sticky-menu
    out[++o] = '<div class="mosaique-header">'; // Start mosaique-header
    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<div class="content-left content-menu-filter transition">'; // Start content-left
    out[++o] = '<div class="link-filter"><span class="icon icon-rectangle"><span class="barre"></span>';
    out[++o] = '<span class="barre"></span><span class="barre"></span></span><span class="text">Filter</span>';
    out[++o] = '</div>';
    out[++o] = '<div class="divider"></div>';
    out[++o] = '<div class="reset-filter transition">Reset</div>';
    out[++o] = '</div>'; // End content-left
    out[++o] = '<div class="content-right">'; // Start content-right
    out[++o] = '<div class="content-sort-by" style="margin-top:16px;">'; // Start content-sort-by
    out[++o] = 'Sort by: <span class="active-sort">' + sort_name + '</span>';
    out[++o] = '<div class="popup-sort-by">'; // Start popup-sort-by
    out[++o] = '<div class="content">'; // Start content
    out[++o] = '<div class="link-sort ' + ((sort == 'relevance') ? 'active' : '') + '" data-target="relevance"><span>Relevance</span></div>';
    out[++o] = '<div class="link-sort ' + ((sort == 'date') ? 'active' : '') + '" data-target="date"><span>Date</span></div>';
    out[++o] = '<div class="link-sort ' + ((sort == 'a-z') ? 'active' : '') + '" data-target="a-z"><span>A to Z</span></div>';
    out[++o] = '<div class="link-sort ' + ((sort == 'volumes') ? 'active' : '') + '" data-target="volumes"><span>Volumes</span></div>';
    out[++o] = '</div>'; // End content
    out[++o] = '</div>'; // End popup-sort-by
    out[++o] = '</div>'; // End content-sort-by
    //out[++o] = '<button class="hub-button button-benchmark">Compare</button>';
    out[++o] = '</div>'; // End content-right
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End mosaique-header

    out[++o] = '<div class="special-absolute-position">'; // Start special-absolute-position
    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<div class="mosaique-content-all-filters transition-none-container">'; // Start mosaique-content-all-filters
    out[++o] = '<div class="scroller">'; // Start scroller
    out[++o] = mosaique_get_trending_tags_filter_html_template();
    out[++o] = '<div class="content-active-filters"></div>';
    out[++o] = '<div class="content-other-filters position-relative">'; // Start content-other-filters

    // mosaique-content-filter classification
    out[++o] = '<div class="mosaique-content-filter classification-style" data-family="classification">';
    out[++o] = '<div class="content" style="display:block;">';

    var all_filter_classifications = [
        { id: 'product', target: 'Products' },
        { id: 'Service', target: 'Services ' }
    ];
    for (var i = 0; i < all_filter_classifications.length; i++) {
        var the_classification = all_filter_classifications[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_classification['id'], manage_filters['classification'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="classification" data-target="' + the_classification['id'] + '">';
        out[++o] = '<div class="helper-on-hover" data-target="' + the_classification['id'] + '">';
        out[++o] = '<span class="text">' + the_classification['target'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '</div>';
    // end mosaique-content-filter classification


    // mosaique-content-filter current_stage
    out[++o] = '<div class="mosaique-content-filter" data-family="current_stage">';
    out[++o] = '<div class="title">';
    out[++o] = 'Stages';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';

    var all_filter_stages = [
        { id: 1, target: 'discover' },
        { id: 2, target: 'ideate' },
        { id: 3, target: 'experiment' },
        { id: 4, target: 'incubate' },
        { id: 5, target: 'scale_up' },
        { id: 8, target: 'permanent_range' },
        { id: 88, target: 'frozen' },
        { id: 7, target: 'discontinued' }
    ];
    for (var i = 0; i < all_filter_stages.length; i++) {
        var the_stage = all_filter_stages[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_stage['id'], manage_filters['current_stage'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="current_stage" data-target="' + the_stage['id'] + '">';
        out[++o] = '<div class="helper-on-hover" data-target="' + the_stage['target'] + '">';
        out[++o] = '<span class="background-color-transparent icon big ' + the_stage['target'] + '"></span>';
        out[++o] = '<span class="text">' + returnGoodStage(the_stage['target']) + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter current_stage

    // mosaique-content-filter growth_model
    out[++o] = '<div class="mosaique-content-filter" data-family="growth_model">';
    out[++o] = '<div class="title">';
    out[++o] = 'Growth Model';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';
    var all_filter_growth_model = [
        { id: 'fast_growth', title: 'Fast Growth' },
        { id: 'slow_build', title: 'Slow Build' },
    ];
    for (var i = 0; i < all_filter_growth_model.length; i++) {
        var the_filter = all_filter_growth_model[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_filter['id'], manage_filters['growth_model'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="growth_model" data-target="' + the_filter['id'] + '">';
        out[++o] = '<div class="helper-on-hover" data-target="' + the_filter['id'] + '">';
        out[++o] = '<span class="background-color-transparent icon big ' + the_filter['id'] + '"></span>';
        out[++o] = '<span class="text">' + the_filter['title'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter growth_model

    // mosaique-content-filter portfolio
    out[++o] = '<div class="mosaique-content-filter" data-family="portfolio">';
    out[++o] = '<div class="title">';
    out[++o] = 'Portfolios';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';

    var all_filter_portfolio = [
        { id: 'big_bet', target: 'Big Bet' },
        { id: 'rtd_rts', target: 'RTD / RTS' },
        { id: 'no_low_alcohol', target: 'No & Low Alcohol' },
        { id: 'specialty', target: 'Specialty' }
    ];
    for (var i = 0; i < all_filter_portfolio.length; i++) {
        var the_bp = all_filter_portfolio[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_bp['id'], manage_filters['portfolio'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="portfolio" data-target="' + the_bp['id'] + '">';
        out[++o] = '<div class="helper-on-hover on-hover" data-target="' + the_bp['id'] + '">';
        out[++o] = '<span class="text">' + the_bp['target'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter portfolio


    // mosaique-content-filter COOP
    out[++o] = '<div class="mosaique-content-filter" data-family="coop">';
    out[++o] = '<div class="title">';
    out[++o] = 'Consumer Opportunities';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';
    var all_consumer_opportunities = get_all_consumers_opportunities();
    for (var g = 0; g < all_consumer_opportunities.length; g++) {
        out[++o] = '<div class="filter empty ' + ((in_array(all_consumer_opportunities[g]['id'] + '', manage_filters['coop'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="coop" data-target="' + all_consumer_opportunities[g]['id'] + '">';
        out[++o] = '<div class="helper-on-hover on-hover" data-target="' + all_consumer_opportunities[g]['key'] + '">';
        out[++o] = '<span class="icon big ' + all_consumer_opportunities[g]['key'] + '"></span>';
        out[++o] = '<span class="text">' + all_consumer_opportunities[g]['libelle'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter COOP

    // mosaique-content-filter MOC
    out[++o] = '<div class="mosaique-content-filter" data-family="moc">';
    out[++o] = '<div class="title">';
    out[++o] = 'Moment of Convivialité';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';
    var all_mocs = get_moments_of_convivialite();
    for (var g = 0; g < all_mocs.length; g++) {
        out[++o] = '<div class="filter empty ' + ((in_array(all_mocs[g]['text'] + '', explore_filters['moc'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="moc" data-target="' + all_mocs[g]['text'] + '">';
        out[++o] = '<span class="text">' + all_mocs[g]['text'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter MOC



    out[++o] = '</div>'; // End content-other-filters
    out[++o] = '</div>'; // End scroller
    out[++o] = '</div>'; // End mosaique-content-all-filters
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End special-absolute-position
    out[++o] = '</div>'; // End #mosaique-sticky-menu


    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<div class="mosaique-list-innovations">'; // Start mosaique-list-innovations

    out[++o] = '</div>'; // End mosaique-list-innovations
    out[++o] = '</div>'; // End central-content

    out[++o] = '</div>'; // End content-list-manage
    out[++o] = '</div>'; // End Content inner
    out[++o] = '</div>'; // End full-content-list-manage
    return out.join('');
}

/**
 * Returns recommandations/feedback section empty state 👌
 * @returns {string}
 */
function manage_md_get_recommandations_empty_state() {
    var out = [],
        o = -1;
    out[++o] = '<div class="background-color-f2f2f2 margin-top-40 margin-auto padding-40 text-align-center" style="width:660px;">';
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-b5b5b5 line-height-1-5">No recommandations nor feedback yet…</div>';
    out[++o] = '<div class="font-work-sans-300 font-size-16 color-9b9b9b line-height-1-5">Add your strategic recommandations for COMEX!</div>';
    out[++o] = '<button class="hub-button action-open-popup margin-top-20" data-popup-target=".element-manage-new-feedback-popup">Add recommandations</button>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * Returns recommandations/feedback section content 👌
 * @returns {string}
 */
function manage_md_get_recommandations_content(reco, feedback) {
    var out = [],
        o = -1;
    out[++o] = '<div class="position-relative">';
    out[++o] = '<div class="background-color-84d8e5" style="position: relative; top:40px; left:-40px; width: 689px; max-width: 689px; padding: 40px; box-sizing:border-box; z-index: 1;">';
    out[++o] = '<h2 class="font-montserrat-700 font-size-26 color-fff margin-bottom-20">Strategic recommandations for COMEX</h2>';
    out[++o] = '<div id="recommandation-message" class="work-sans-400 font-size-19 color-000 nl2br" style="line-height: 1.58;">';
    out[++o] = reco;
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="background-color-f2f2f2" style="position: absolute; top:0; right:-40px; width: 425px; max-width: 425px; padding: 40px 40px 40px 80px; box-sizing:border-box;">';
    out[++o] = '<h2 class="font-montserrat-700 font-size-20 color-000 margin-bottom-10">Feedback & needs for New Business Models</h2>';
    out[++o] = '<div id="feedback-message" class="work-sans-400 font-size-16 color-000 nl2br" style="line-height: 1.5;">'
    out[++o] = feedback;
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * manage_md_get_competitors_empty_state
 * Returns competitors section empty state 👌
 * @returns {string}
 */
function manage_md_get_competitors_empty_state() {
    var out = [],
        o = -1;
    out[++o] = '<div class="background-color-f2f2f2 margin-top-40 margin-auto padding-40 text-align-center" style="width:660px;">';
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-b5b5b5 line-height-1-5">No competitor identified yet…</div>';
    out[++o] = '<div class="font-work-sans-300 font-size-16 color-9b9b9b line-height-1-5">Add the competitor products on your market!</div>';
    out[++o] = '<button class="hub-button action-open-popup margin-top-20" data-popup-target=".element-manage-new-competitor-popup">Add a competitor</button>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * manage_md_get_competitors_content
 * Returns competitors section content 👌
 * @returns {string}
 */
function manage_md_get_competitors_content(competitors) {
    var out = [],
        o = -1;
    out[++o] = '<div class="overflow-hidden">';
    for (var i = 0; i < competitors.length; i++) {
        var competitor = competitors[i];
        out[++o] = '<div class="mosaique-innovation">';
        out[++o] = '<div class="content">';
        out[++o] = '<div class="button-edit-md-competitor hub-button" data-popup-target=".element-manage-new-competitor-popup"  data-competitor_id="' + competitor.id + '">Edit</div>';
        var background_picture = (competitor['picture'] && competitor['picture']['thumbnail']) ? competitor['picture']['thumbnail'] : '/images/default/product/default-1.png';
        out[++o] = '<div class="picture" style="background-image:url(' + background_picture + ');"></div>';
        out[++o] = '<div class="title">' + competitor.product_name + '</div>';
        out[++o] = '<div class="infos">' + competitor.category + '</div>';
        out[++o] = '<div class="infos color-787878">' + competitor.brand + '</div>';
        out[++o] = '<ul class="tags content-tranding-tags">';
        for (var k = 0; k < competitor.tags.length; k++) {
            out[++o] = '<li class="trending-tag no-hover">';
            out[++o] = competitor.tags[k].text;
            out[++o] = '</li>';
        }
        out[++o] = '</ul>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    return out.join('');
}

/**
 * Inserts feedback section content given the webservice result 🤪
 */
function insert_manage_feedback_tab_content() {
    manage_md_generate_recommandation();
    manage_md_generate_competitors();
}

/**
 * manage_md_generate_recommandation
 */
function manage_md_generate_recommandation() {
    var $feedbackSection = $('#feedback-section');
    $('.action-edit-popup-manage-new-feedback').addClass('display-none');
    $feedbackSection.html('<div class="display-inline-block loading-spinner loading-spinner--26 margin-top-40"></div>');
    $.ajax({
        url: '/api/manage/md/recommandation',
        success: function(response, status) {
            var feedbackSectionContent = manage_md_get_recommandations_empty_state();
            if (response['status'] == 'error') {
                add_flash_message('error', response['message']);
            } else if (response.recommandation) {
                manage_md_data['recommandation'] = response.recommandation;
                feedbackSectionContent = manage_md_get_recommandations_content(response.recommandation.recommandation, response.recommandation.feedback);
                $('.action-edit-popup-manage-new-feedback').removeClass('display-none');
            }
            $feedbackSection.html(feedbackSectionContent);
        },
        error: function(response, status, e) {
            ajax_on_error(response, status, e);
            if (response.statusText == 'abort' || e == 'abort') {
                return;
            }
            //add_flash_message('error', "An unknown error occurred while loading your recommandations");
            $feedbackSection.html(manage_md_get_recommandations_empty_state());
            console.log('insert_manage_feedback_tab_content', e);
        }
    });
}

/**
 * manage_md_generate_competitors
 */
function manage_md_generate_competitors() {
    var $competitorsSection = $('#competitors-section');
    $competitorsSection.html('<div class="display-inline-block loading-spinner loading-spinner--26 margin-top-40"></div>');
    $('.open-popup-add-competitor').addClass('display-none');
    $.ajax({
        url: '/api/manage/md/competitors',
        success: function(response, status) {
            var competitorsSectionContent = manage_md_get_competitors_empty_state();
            if (response['status'] == 'error') {
                add_flash_message('error', response['message']);
            } else if (response.competitors && response.competitors.length > 0) {
                manage_md_data['competitors'] = response.competitors;
                competitorsSectionContent = manage_md_get_competitors_content(response.competitors);
                if (response.competitors.length < 3) {
                    $('.open-popup-add-competitor').removeClass('display-none');
                }
            }
            $competitorsSection.html(competitorsSectionContent);
        },
        error: function(response, status, e) {
            ajax_on_error(response, status, e);
            if (response.statusText == 'abort' || e == 'abort') {
                return;
            }
            //add_flash_message('error', "An unknown error occurred while loading your competitors");
            console.log('insert_manage_feedback_tab_content', e);
        }
    });
}

function manage_md_get_competitor_by_id(id) {
    var a_collection = new DataCollection(manage_md_data['competitors']);
    id = parseInt(id);
    var target = a_collection.query().filter({ id__is: id }).values();
    if (!target || target && target.length <= 0) {
        return null;
    }
    return target[0];
}

function manage_generate_feedback_tab() {
    var out = [],
        o = -1;
    out[++o] = '<div id="content-tab-feedback-md" class="content-inner padding-bottom-40">'; // Start Content inner
    out[++o] = '<div class="background-color-gradient-161541">'; // Start background-color-gradient-161541
    out[++o] = '<div class="central-content-994 padding-top-40 padding-bottom-40">'; // Start central-content-994
    out[++o] = '<h2 class="font-montserrat-700 font-size-30 color-fff">Welcome to PR France management page</h2>';
    out[++o] = '<p class="font-work-sans-400 font-size-15 margin-top-10 color-fff-0-9">';
    out[++o] = 'Duis tincidunt facilisis tellus. Nam maximus eget metus et cursus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis tincidunt quam. Cras hendrerit eleifend est mollis semper. Nullam accumsan imperdiet massa ac porta.';
    out[++o] = '</p>';
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End background-color-gradient-161541
    out[++o] = '<div class="central-content-994 padding-top-40 padding-bottom-40">'; // Start central-content-994
    out[++o] = '<h2 class="font-montserrat-700 font-size-26 color-000">';
    out[++o] = 'Recommandations & feedback';
    out[++o] = '<button class="hub-button action-edit-popup-manage-new-feedback display-none" data-popup-target=".element-manage-new-feedback-popup" style="float:right;">Edit</button>';
    out[++o] = '</h2>';
    out[++o] = '<div id="feedback-section" class="padding-top-20 padding-bottom-40"></div>';
    out[++o] = '<h2 class="font-montserrat-700 font-size-26 color-000 margin-top-60">';
    out[++o] = 'Latest competitor products on fire';
    out[++o] = '<button class="hub-button action-open-popup open-popup-add-competitor display-none" data-popup-target=".element-manage-new-competitor-popup" style="float:right;">Add a competitor</button>';
    out[++o] = '</h2>';
    out[++o] = '<div id="competitors-section" class="margin-top-30 overflow-hidden"></div>';
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End Content inner
    return out.join('');
}

/**
 * manage_get_html_template
 * @param link
 * @param tab Tab to display.
 * @returns {string}
 */
function manage_get_html_template(link, tab) {
    var out = [],
        o = -1;
    var has_tab = (check_if_user_has_manage_list_access() && check_if_user_is_managing_director());
    out[++o] = '<div class="full-content-manage-list-page ' + ((has_tab) ? 'pri-tab' : '') + '">'; // Start full-content-manage-list-page

    out[++o] = '<div class="element-sub-header-menu-tab">'; // Start element-sub-header-menu-tab
    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<div class="text-align-right">'; // Start text-align-right
    if (has_tab) {
        out[++o] = '<ul class="pri-tab-menu color-white display-inline-block">';
        if (check_if_user_has_manage_list_access()) {
            out[++o] = '<li ' + ((tab == 'overview') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#overview">Overview</a></li>';
        }
        if (check_if_user_is_managing_director()) {
            out[++o] = '<li ' + ((tab == 'feedback') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#feedback">Feedback</a></li>';
        }
        out[++o] = '</ul>';
    }
    out[++o] = '<div class="content-buttons display-inline-block">'; // Start content-buttons
    /*
    if (check_if_user_has_manage_list_access()) {
        out[++o] = '<button class="hub-button light-blue height-26 async-export margin-left-10" data-title="Exporting Entity performance review in PPT…" data-action="/api/export/entity-performance-review"><span class="icon-ddl"></span> <span>Performance Review</span></button>';
    }*/
    if ($('#main').hasClass('project-creation-enabled')) {
        out[++o] = '<button class="hub-button height-26 action-open-popup margin-left-10" data-popup-target=".element-new-innovation-popup">Create new project</button>';
    }
    out[++o] = '</div>'; // End content-buttons
    out[++o] = '</div>'; // End text-align-right
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End element-sub-header-menu-tab
    out[++o] = '<div class="fake-element-sub-header-menu-tab"></div>';

    if (check_if_user_has_manage_list_access()) {
        out[++o] = '<div id="overview">' + manage_generate_overview_tab() + '</div>';
    }
    if (check_if_user_is_managing_director()) {
        out[++o] = '<div id="feedback">' + manage_generate_feedback_tab() + '</div>';
    }

    out[++o] = '</div>'; // End full-content-manage-list-page
    return out.join('');
}

/**
 * Should fit any jquery plugin or post DOM-insertion javascript code 😍
 */
function manage_init() {
    if (check_if_user_has_manage_list_access() && check_if_user_is_managing_director()) {
        $('.pri-tab').tabs({
            activate: function(event, ui) {
                if ($(event.currentTarget).length > 0) {
                    window.scrollTo(0, 0);
                    var target = $(event.currentTarget).attr('href').replace('#', '');
                    if (target == 'overview') {
                        opti_update_url("/content/manage", 'no_change');
                        setTimeout(function() {
                            $('.sticky-ghost').remove();
                            $('.ghosted').removeClass('ghosted').removeClass('sticky-fixed').removeAttr('style');
                            mosaique_optimize_content_all_filters_height();
                        }, 50);
                    } else if (target == 'feedback') {
                        opti_update_url("/content/manage/tab/feedback", 'no_change');
                    }
                }
            }
        });
    }
    window.scrollTo(0, 0);
    $('#main').removeClass('loading');
    $('html, body').addClass('background-color-fff');
    if (check_if_user_has_manage_list_access()) {
        mosaique_init('manage');
    }
    if (check_if_user_is_managing_director()) {
        insert_manage_feedback_tab_content();

        $('.action-edit-popup-manage-new-feedback').click(function() {
            var target = $(this).data('popup-target');
            open_popup(target, function() {
                var message = $('#recommandation-message').text();
                var feedback = $('#feedback-message').text();
                $('#form-manage-new-feedback-popup textarea[name="recommandation"]').val(message);
                $('#form-manage-new-feedback-popup textarea[name="feedback"]').val(feedback);
            });
        });
    }
}

/**
 * manage_get_consolidation_html_template
 * @returns {string}
 */
function manage_get_consolidation_html_template() {
    var out = [],
        o = -1;
    out[++o] = '<div id="manage-content-notification"></div>';
    out[++o] = '<div class="overflow-hidden overflow-graphs">'; // Start overflow-hidden

    out[++o] = '<div class="column-percent-30 text-align-left">'; // Start column-percent-30
    out[++o] = '<div class="font-montserrat-500 font-size-20 line-height-1 color-fff-0-4">Your innovation funnel</div>';
    out[++o] = '</div>'; // End column-percent-30

    out[++o] = '<div class="column-percent-40">'; // Start column-percent-40
    out[++o] = '<div class="content-donut content-donut-stages" style="width: 130px;height: 130px;">'; // Start content-donut
    out[++o] = '<canvas id="manage-chartStages" width="130" height="130"></canvas>';
    out[++o] = '<div class="donut-inner-stage"><span>0</span><div>Projects</div></div>';
    out[++o] = '</div>'; // End content-donut
    out[++o] = '</div>'; // End column-percent-40

    out[++o] = '<div class="column-percent-30  text-align-center">'; // Start column-percent-30
    out[++o] = '</div>'; // End column-percent-30
    out[++o] = '</div>'; // End overflow-hidden

    out[++o] = '<div class="overflow-hidden overflow-stages">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-65">'; // Start column-percent-65
    out[++o] = '<div class="content-manage-consolidation-stage text-align-center">'; // Start content-manage-consolidation-stage
    var all_consolidation_stages = [
        { id: 1, target: 'discover', separator: true },
        { id: 2, target: 'ideate', separator: true },
        { id: 3, target: 'experiment', separator: true },
        { id: 4, target: 'incubate', separator: true },
        { id: 5, target: 'scale_up', separator: false }
    ];
    for (var i = 0; i < all_consolidation_stages.length; i++) {
        var the_stage = all_consolidation_stages[i];
        out[++o] = '<div class="manage-consolidation-stage transition-opacity ' + the_stage['target'] + '">'; // Start manage-consolidation-stage
        out[++o] = '<div class="icon ' + the_stage['target'] + ' dark"></div>';
        out[++o] = '<div class="title font-montserrat-700">' + returnGoodStage(the_stage['target']) + '</div>';
        out[++o] = '<div class="nb-projects"><span class="nb">0</span> <span class="wording">project</span></div>';
        out[++o] = '</div>'; // End manage-consolidation-stage
        if (the_stage['separator']) {
            out[++o] = '<div class="separator"></div>';
        }
    }
    out[++o] = '</div>'; // End content-manage-consolidation-stage
    out[++o] = '</div>'; // End column-percent-65
    out[++o] = '<div class="column-percent-35">'; // Start column-percent-35
    out[++o] = '<div class="content-manage-consolidation-stage text-align-right">'; // Start content-manage-consolidation-stage
    var all_consolidation_stages = [
        { id: 88, target: 'frozen' },
        { id: 8, target: 'permanent_range' },
        { id: 7, target: 'discontinued' }
    ];
    for (var i = 0; i < all_consolidation_stages.length; i++) {
        var the_stage = all_consolidation_stages[i];
        out[++o] = '<div class="manage-consolidation-stage other transition-opacity ' + the_stage['target'] + '">'; // Start manage-consolidation-stage
        out[++o] = '<div class="icon ' + the_stage['target'] + ' dark neutral"></div>';
        out[++o] = '<div class="title font-montserrat-700">' + returnGoodStage(the_stage['target']) + '</div>';
        out[++o] = '<div class="nb-projects"><span class="nb">0</span> <span class="wording">project</span></div>';
        out[++o] = '</div>'; // End manage-consolidation-stage
    }
    out[++o] = '</div>'; // End content-manage-consolidation-stage

    out[++o] = '</div>'; // End column-percent-35
    out[++o] = '</div>'; // End overflow-hidden
    return out.join('');
}


/**
 * manage_load_more_list
 */
function manage_load_more_list() {
    var out = [],
        o = -1;
    manage_filtered_innovation_collection.query().limit(manage_innovation_offset, manage_innovation_limit).each(function(row, index) {
        out[++o] = manage_get_innovation_html(row);
    });
    $('.mosaique-list-innovations').append(out.join(''));
    manage_innovation_offset += manage_innovation_limit;
    if (manage_filtered_innovation_collection.query().count() <= manage_innovation_offset) {
        manage_all_are_loaded = true;
    }
}

/**
 * manage_get_innovation_html
 *
 * @param innovation
 * @returns {string}
 */
function manage_get_innovation_html(innovation) {
    var out = [],
        o = -1;
    out[++o] = '';
    var explore_detail_url = "/explore/" + innovation['id'];
    var added_classe = (innovation_is_out_of_funnel(innovation)) ? ' out-of-funnel' : '';
    out[++o] = '<div class="mosaique-innovation ' + added_classe + '">';
    out[++o] = '<div class="content">';
    out[++o] = '<div class="inner-content"></div>';
    out[++o] = '<a href="' + explore_detail_url + '" class="link_explore_detail global-link">';
    var style_background = 'style="background-image:url(' + innovation['explore']['images'][0] + ');"';
    out[++o] = '<div class="picture" ' + style_background + '></div>';
    var count_empty_fields = innovation['count_empty_fields'];
    var round_nb = (count_empty_fields > 0) ? '<span class="round-nb-emtpy-field tooltip-right-tr force-tooltip" title="' + count_empty_fields + ' empty fields">' + count_empty_fields + '</span>' : '';
    out[++o] = '<div class="title">' + innovation['title'] + round_nb + '</div>';
    var infos = (!innovation_is_out_of_funnel(innovation)) ? '<span class="micro-icon-stage ' + innovation['current_stage'] + '"></span>' : '';
    infos += '<span>' + ((innovation['is_frozen']) ? 'Frozen' : returnGoodStage(innovation['current_stage'])) + '</span>';
    if (!innovation_is_out_of_funnel(innovation) && innovation['proper']['classification_type'] === 'product') {
        var value_growth_model = (innovation['growth_model'] === "fast_growth") ? "Fast Growth" : "Slow Build";
        infos += '<span class="padding-left-5"> • ' + value_growth_model + '</span>';
    }
    out[++o] = '<div class="infos">' + infos + '</div>';
    out[++o] = '</a>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * 
 * manage_use_saved_filters.
 */
function manage_use_saved_filters() {
    var saved_manage_filters = (typeof localStorage !== 'undefined') ? localStorage.getItem("manage_filters") : null;
    if (saved_manage_filters != null) {
        manage_set_filters_by_url();
        $('.mosaique-content-filter .content .filter').each(function() {
            var $celui = $(this);
            var family = $celui.attr('data-family');
            var target = $celui.attr('data-target');
            if (in_array(target, manage_filters[family])) {
                $celui.addClass('active');
                $celui.parent().parent().find(".filter[data-target='" + $celui.attr('data-target') + "']").addClass('active');
            } else {
                $celui.removeClass('active');
                $celui.parent().parent().find(".filter[data-target='" + $celui.attr('data-target') + "']").removeClass('active');
                $celui.parent().parent().find(".filter[data-target='" + $celui.attr('data-target') + "']").removeClass('special-display-none');
            }
        });
        var nb_filters = $('.mosaique-content-filter .content .filter.active').length + +$('#mosaique-sticky-menu .content-active-tag-filters .filter.active').length;
        if (nb_filters > 0) {
            $('.content-list-manage').addClass('filtered');
        } else {
            $('.content-list-manage').removeClass('filtered');
        }
        var text = mosaique_get_sort_name(manage_filters['sort']);
        jQuery('.mosaique-header .content-right .content-sort-by').removeClass('opened');
        jQuery('.mosaique-header .content-right .content-sort-by .active-sort').text(text);
        jQuery('.mosaique-header .content-right .content-sort-by .link-sort').removeClass('active');
        jQuery('.mosaique-header .content-right .content-sort-by .link-sort[data-target="' + manage_filters['sort'] + '"]').addClass('active');
    }
}

/**
 * manage_sort_list_by_target
 * @param target
 * @param update_url
 */
function manage_sort_list_by_target(target, update_url) {
    if (typeof update_url === 'undefined') {
        update_url = true;
    }
    manage_all_are_loaded = false;
    manage_innovation_offset = 0;
    manage_reorder_list(target);
    jQuery('.mosaique-list-innovations').html('');
    hide_and_scroll();
    manage_load_more_list();
    if (update_url) {
        var url = manage_generate_url();
        opti_update_url(url, 'no_change');
    }
    manage_save_filters();
    init_tooltip();
}

/**
 * manage_reorder_list.
 *
 * @param target
 */
function manage_reorder_list(target) {
    switch (target) {
        case 'relevance':
            manage_filtered_innovation_collection = new DataCollection(manage_filtered_innovation_collection.query().order('sort_manage_relevance', true).values());
            break;
        case 'date':
            manage_filtered_innovation_collection = new DataCollection(manage_filtered_innovation_collection.query().order('sort_explore_date', true).values());
            break;
        case 'a-z':
            manage_filtered_innovation_collection = new DataCollection(manage_filtered_innovation_collection.query().order('proper__title', false).values());
            break;
        case 'volumes':
            manage_filtered_innovation_collection = new DataCollection(manage_filtered_innovation_collection.query().order('proper__total_volume', true).values());
            break;
        default:
            break;
    }
}

/**
 * manage_filter_list.
 * @param update_url
 */
function manage_filter_list(update_url) {
    if (typeof update_url === 'undefined') {
        update_url = true;
    }
    manage_all_are_loaded = false;
    manage_innovation_offset = 0;
    manage_filtered_innovation_collection = new DataCollection(manage_initial_innovation_collection.query().values());
    var sort_target = jQuery('.mosaique-header .content-right .content-sort-by .link-sort.active').attr('data-target');
    manage_reorder_list(sort_target);
    manage_filters = manage_get_default_filters();
    if ($('#mosaique-sticky-menu .content-other-filters .filter.active').length > 0 || $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').length > 0) {
        $('#mosaique-sticky-menu .content-other-filters .filter.active').each(function() {
            var $celui = $(this);
            var family = $celui.attr('data-family');
            var target = $celui.attr('data-target');
            if (family == 'category') {
                var cate = mosaique_get_category_for_key(target);
                manage_filters[family].push(cate);
                manage_filters['category_key'].push(target);
            } else if (in_array(family, ['current_stage', 'coop', 'title', 'brand'])) {
                manage_filters[family].push(parseInt(target));
            } else {
                manage_filters[family].push(target);
            }
        });

        $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').each(function() {
            var $celui = $(this);
            var family = $celui.attr('data-family');
            var target = $celui.attr('data-target');
            manage_filters[family].push(target);
        });

        if (manage_filters['coop'].length > 0) {
            manage_filtered_innovation_collection.query().filter({ consumer_opportunity__not_in: manage_filters['coop'] }).remove();
        }
        if (manage_filters['moc'].length > 0) {
            manage_filtered_innovation_collection.query().filter({ moc__not_in: manage_filters['moc'] }).remove();
        }
        if (manage_filters['tag'].length > 0) {
            manage_filtered_innovation_collection.query().filter({ tags_array__array_not_contains_any: manage_filters['tag'] }).remove();
        }
        if (manage_filters['portfolio'].length > 0) {
            var remove_big_bets = (!in_array('big_bet', manage_filters['portfolio']));
            if (!in_array('craft', manage_filters['portfolio'])) { // craft
                if (remove_big_bets) {
                    manage_filtered_innovation_collection.query().filter({ business_drivers: "Craft" }).remove();
                } else {
                    manage_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "Craft"
                    }).remove();
                }
            }
            if (!in_array('rtd_rts', manage_filters['portfolio'])) { // rtd_rts
                if (remove_big_bets) {
                    manage_filtered_innovation_collection.query().filter({ business_drivers: "RTD / RTS" }).remove();
                } else {
                    manage_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "RTD / RTS"
                    }).remove();
                }
            }
            if (!in_array('specialty', manage_filters['portfolio'])) { // specialty
                if (remove_big_bets) {
                    manage_filtered_innovation_collection.query().filter({ business_drivers: "Specialty" }).remove();
                } else {
                    manage_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "Specialty"
                    }).remove();
                }
            }
            if (!in_array('no_low_alcohol', manage_filters['portfolio'])) { // no_low_alcohol
                if (remove_big_bets) {
                    manage_filtered_innovation_collection.query().filter({ business_drivers: "No & Low Alcohol" }).remove();
                } else {
                    manage_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "No & Low Alcohol"
                    }).remove();
                }
            }
            if (!in_array('none', manage_filters['portfolio'])) { // None
                if (remove_big_bets) {
                    manage_filtered_innovation_collection.query().filter({ business_drivers: "None" }).remove();
                    manage_filtered_innovation_collection.query().filter({ business_drivers: null }).remove();
                } else {
                    manage_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "None"
                    }).remove();
                    manage_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: null
                    }).remove();
                }
            }
            if (!in_array('big_bet', manage_filters['portfolio'])) { // Big Bet
                manage_filtered_innovation_collection.query().filter({
                    big_bet: 'BB',
                    business_drivers__not_in: ["Craft", "RTD / RTS", "No & Low Alcohol", "Specialty"]
                }).remove();
            }
        }

        if (manage_filters['current_stage'].length > 0) {
            if (!in_array(1, manage_filters['current_stage'])) { // No Discover
                manage_filtered_innovation_collection.query().filter({ current_stage_id: 1, is_frozen: false }).remove();
            }
            if (!in_array(2, manage_filters['current_stage'])) { // No Ideate
                manage_filtered_innovation_collection.query().filter({ current_stage_id: 2, is_frozen: false }).remove();
            }
            if (!in_array(3, manage_filters['current_stage'])) { // No Experiment
                manage_filtered_innovation_collection.query().filter({ current_stage_id: 3, is_frozen: false }).remove();
            }
            if (!in_array(4, manage_filters['current_stage'])) { // No Incubate
                manage_filtered_innovation_collection.query().filter({ current_stage_id: 4, is_frozen: false }).remove();
            }
            if (!in_array(5, manage_filters['current_stage'])) { // No Scale up
                manage_filtered_innovation_collection.query().filter({ current_stage_id: 5, is_frozen: false }).remove();
            }
            if (!in_array(8, manage_filters['current_stage'])) { // No Permanent range
                manage_filtered_innovation_collection.query().filter({ current_stage_id: 8, is_frozen: false }).remove();
            }
            if (!in_array(88, manage_filters['current_stage'])) { // No Frozen
                manage_filtered_innovation_collection.query().filter({ is_frozen: true }).remove();
            }
            if (!in_array(7, manage_filters['current_stage'])) { // No Y2
                manage_filtered_innovation_collection.query().filter({ current_stage_id: 7, is_frozen: false }).remove();
            }
        }
        if (manage_filters['growth_model'].length > 0) {
            manage_filtered_innovation_collection.query().filter({ growth_model__not_in: manage_filters['growth_model'] }).remove();
        }
        if (manage_filters['classification'].length > 0) {
            manage_filtered_innovation_collection.query().filter({ proper__classification_type__not_in: manage_filters['classification'] }).remove();
        }
    }
    $('.mosaique-list-innovations').html('');
    hide_and_scroll();
    manage_init_filters();
    manage_load_more_list();
    if (update_url) {
        var url = manage_generate_url();
        opti_update_url(url, 'no_change');
    }

    init_tooltip();
    mosaique_filters_to_top();
    manage_update_consolidation();
}

/**
 * manage_init_filters
 */
function manage_init_filters() {
    $('.mosaique-content-filter').each(function() {
        var $celui = $(this);
        var family = $celui.attr('data-family');
        var an_innovation_collection = new DataCollection(manage_initial_innovation_collection.query().values());

        if (manage_filters['tag'].length > 0) {
            an_innovation_collection.query().filter({ tags_array__array_not_contains_any: manage_filters['tag'] }).remove();
        }
        if (family != 'coop' && manage_filters['coop'].length > 0) {
            an_innovation_collection.query().filter({ consumer_opportunity__not_in: manage_filters['coop'] }).remove();
        }
        if (family != 'moc' && manage_filters['moc'].length > 0) {
            an_innovation_collection.query().filter({ moc__not_in: manage_filters['moc'] }).remove();
        }
        if (family != 'portfolio' && manage_filters['portfolio'].length > 0) {
            var remove_big_bets = (!in_array('big_bet', manage_filters['portfolio']));
            if (!in_array('craft', manage_filters['portfolio'])) { // craft
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "Craft" }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "Craft" }).remove();
                }
            }
            if (!in_array('rtd_rts', manage_filters['portfolio'])) { // rtd_rts
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "RTD / RTS" }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "RTD / RTS" }).remove();
                }
            }
            if (!in_array('specialty', manage_filters['portfolio'])) { // specialty
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "Specialty" }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "Specialty" }).remove();
                }
            }
            if (!in_array('no_low_alcohol', manage_filters['portfolio'])) { // no_low_alcohol
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "No & Low Alcohol" }).remove();
                } else {
                    an_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "No & Low Alcohol"
                    }).remove();
                }
            }
            if (!in_array('none', manage_filters['portfolio'])) { // None
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "None" }).remove();
                    an_innovation_collection.query().filter({ business_drivers: null }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "None" }).remove();
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: null }).remove();
                }
            }
            if (!in_array('big_bet', manage_filters['portfolio'])) { // Big Bet
                an_innovation_collection.query().filter({
                    big_bet: 'BB',
                    business_drivers__not_in: ["Craft", "RTD / RTS", "No & Low Alcohol", "Specialty"]
                }).remove();
            }
        }

        if (family != 'current_stage' && manage_filters['current_stage'].length > 0) {
            if (!in_array(1, manage_filters['current_stage'])) { // No Discover
                an_innovation_collection.query().filter({ current_stage_id: 1, is_frozen: false }).remove();
            }
            if (!in_array(2, manage_filters['current_stage'])) { // No Ideate
                an_innovation_collection.query().filter({ current_stage_id: 2, is_frozen: false }).remove();
            }
            if (!in_array(3, manage_filters['current_stage'])) { // No Experiment
                an_innovation_collection.query().filter({ current_stage_id: 3, is_frozen: false }).remove();
            }
            if (!in_array(4, manage_filters['current_stage'])) { // No Incubate
                an_innovation_collection.query().filter({ current_stage_id: 4, is_frozen: false }).remove();
            }
            if (!in_array(5, manage_filters['current_stage'])) { // No Scale up
                an_innovation_collection.query().filter({ current_stage_id: 5, is_frozen: false }).remove();
            }
            if (!in_array(8, manage_filters['current_stage'])) { // No Permanent range
                an_innovation_collection.query().filter({ current_stage_id: 8, is_frozen: false }).remove();
            }
            if (!in_array(88, manage_filters['current_stage'])) { // No Frozen
                an_innovation_collection.query().filter({ is_frozen: true }).remove();
            }
            if (!in_array(7, manage_filters['current_stage'])) { // No Y2
                an_innovation_collection.query().filter({ current_stage_id: 7, is_frozen: false }).remove();
            }
            if (!in_array(7, manage_filters['current_stage'])) { // No Y2
                an_innovation_collection.query().filter({ current_stage_id: 7, is_frozen: false }).remove();
            }
        }
        if (family != 'growth_model' && manage_filters['growth_model'].length > 0) {
            an_innovation_collection.query().filter({ growth_model__not_in: manage_filters['growth_model'] }).remove();
        }
        if (family != 'classification' && manage_filters['classification'].length > 0) {
            an_innovation_collection.query().filter({ proper__classification_type__not_in: manage_filters['classification'] }).remove();
        }
        $celui.find('.filter:not(.group-special)').each(function() {
            var $celui_filter = jQuery(this);
            var target = $celui_filter.attr('data-target');
            var nb = mosaique_get_nb_filtered_list_for_collection_family_and_target(an_innovation_collection, family, target);
            if (nb > 0) {
                $celui_filter.removeClass('empty');
            } else {
                $celui_filter.addClass('empty');
            }
            $celui_filter.find('.nb').text(nb);
        });
        if ($celui.find('.filter.active').length > 0) {
            $celui.find('.title').addClass('active');
        } else {
            $celui.find('.title').removeClass('active');
        }
    });
}

/**
 * manage_save_filters
 */
function manage_save_filters() {
    if (typeof localStorage !== 'undefined') {
        manage_filters['sort'] = jQuery('.mosaique-header .content-right .content-sort-by .link-sort.active').attr('data-target');
        localStorage.setItem("manage_filters", JSON.stringify(manage_filters));
    }
}

/**
 * manage_reset_filters
 */
function manage_reset_filters() {
    manage_filters = manage_get_default_filters();
    if (typeof localStorage !== 'undefined') {
        localStorage.removeItem('manage_filters');
    }
}


/**
 * manage_generate_url.
 * @returns {string}
 */
function manage_generate_url() {
    var url = "/content/manage";
    var is_first = true;
    var $targets = $('#mosaique-sticky-menu .content-other-filters .filter.active');
    if ($targets.length > 0 || $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').length > 0) {
        if (manage_filters['coop'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[coop]=" + manage_filters['coop'].join(',');
            is_first = false;
        }
        if (manage_filters['moc'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[moc]=" + manage_filters['moc'].join(',');
            is_first = false;
        }
        if (manage_filters['portfolio'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[portfolio]=" + manage_filters['portfolio'].join(',');
            is_first = false;
        }
        if (manage_filters['current_stage'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[current_stage]=" + manage_filters['current_stage'].join(',');
            is_first = false;
        }
        if (manage_filters['growth_model'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[growth_model]=" + manage_filters['growth_model'].join(',');
            is_first = false;
        }
        if (manage_filters['classification'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[classification]=" + manage_filters['classification'].join(',');
            is_first = false;
        }
        if (manage_filters['tag'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[tag]=" + manage_filters['tag'].join(',');
            is_first = false;
        }
    }
    var sort_target = jQuery('.mosaique-header .content-right .content-sort-by .link-sort.active').attr('data-target');
    if (sort_target != 'relevance') {
        url += (is_first) ? "?" : "&";
        url += "sort=" + sort_target;
    }
    return url;
}

/**
 * manage_resetStatistics
 */
function manage_resetStatistics() {
    manage_statistics = null;
    manage_data_stage = {
        "labels": manage_chart_stage_labels_array,
        "datasets": [{
            "data": [0, 0, 0, 0, 0, 0, 100],
            "borderColor": manage_chart_border_color_array,
            "hoverBorderColor": manage_chart_border_color_array,
            "backgroundColor": manage_chart_stage_background_array,
            "hoverBackgroundColor": manage_chart_stage_background_array
        }]
    };
}
manage_resetStatistics();

/**
 * manage_update_statistics
 */
function manage_update_statistics() {
    manage_resetStatistics();
    manage_statistics = {
        'nb_stage': manage_filtered_innovation_collection.query().count(),
        'stages': {
            'discover': manage_filtered_innovation_collection.query().filter({
                is_frozen: false,
                current_stage_id: 1
            }).count(),
            'ideate': manage_filtered_innovation_collection.query().filter({
                is_frozen: false,
                current_stage_id: 2
            }).count(),
            'experiment': manage_filtered_innovation_collection.query().filter({
                is_frozen: false,
                current_stage_id: 3
            }).count(),
            'incubate': manage_filtered_innovation_collection.query().filter({
                is_frozen: false,
                current_stage_id: 4
            }).count(),
            'scale_up': manage_filtered_innovation_collection.query().filter({
                is_frozen: false,
                current_stage_id: 5
            }).count(),
            'discontinued': manage_filtered_innovation_collection.query().filter({
                is_frozen: false,
                current_stage_id: 7
            }).count(),
            'permanent_range': manage_filtered_innovation_collection.query().filter({
                is_frozen: false,
                current_stage_id: 8
            }).count(),
            'frozen': manage_filtered_innovation_collection.query().filter({ is_frozen: true }).count(),
            'out_of_funnel': 0
        }
    };
    manage_statistics['stages']['out_of_funnel'] = manage_statistics['stages']['discontinued'] + manage_statistics['stages']['permanent_range'] + manage_statistics['stages']['frozen'];

    // Set donuts datas
    var nb_state_empty_value = (manage_statistics['nb_stage'] == 0) ? 100 : 0;
    var dataset_stage = [
        manage_statistics['stages']['out_of_funnel'],
        manage_statistics['stages']['discover'],
        manage_statistics['stages']['ideate'],
        manage_statistics['stages']['experiment'],
        manage_statistics['stages']['incubate'],
        manage_statistics['stages']['scale_up'],
        nb_state_empty_value
    ];
    manage_statistics['dataset_stage'] = dataset_stage;
}


/**
 * manage_update_consolidation
 */
function manage_update_consolidation() {
    manage_update_statistics();
    var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
    if (manage_statistics) {
        //console.log('manage_statistics', manage_statistics);
        // Update consolidation
        // chart Stage
        var $span_total = $('.content-donut-stages .donut-inner-stage span');
        $span_total.text(manage_statistics.nb_stage);
        $span_total.attr('data-value', manage_statistics.nb_stage);
        if (!$('html').hasClass('ie')) {
            $span_total.animateNumber({
                number: manage_statistics.nb_stage,
                numberStep: comma_separator_number_step
            }, 800);
        } else {
            $span_total.html(manage_statistics.nb_stage);
        }

        if (!manage_stageChart) {
            manage_init_consolidation();
        }
        manage_stageChart.data.datasets[0].data = manage_statistics.dataset_stage;
        manage_stageChart.update();

        var all_stages = ["discover", "ideate", "experiment", "incubate", "scale_up", "discontinued", "permanent_range", "frozen"];
        for (var i = 0; i < all_stages.length; i++) {
            var nb = manage_statistics['stages'][all_stages[i]];
            $('.manage-consolidation-stage.' + all_stages[i]).removeClass('disabled');
            if (nb == 0) {
                $('.manage-consolidation-stage.' + all_stages[i]).addClass('disabled');
            }
            var $span = $('.manage-consolidation-stage.' + all_stages[i] + ' .nb-projects .nb');
            var wording = (nb > 1) ? 'projects' : 'project';
            $('.manage-consolidation-stage.' + all_stages[i] + ' .nb-projects .wording').html(wording);
            if (!$('html').hasClass('ie')) {
                $span.animateNumber({
                    number: nb,
                    numberStep: comma_separator_number_step
                }, 800);
            } else {
                $span.html(nb);
            }
        }

        manage_stats_loaded = true;
    } else {
        manage_stats_loaded = false;
        console.log('stats not loaded');
    }
}

/**
 * manage_init_consolidation
 */
function manage_init_consolidation() {
    // Chart options
    Chart.defaults.global.legend.display = false;
    //Chart.defaults.global.tooltips.enabled = false;
    Chart.defaults.global.hover.enabled = false;

    if (manage_stageChart !== null) {
        manage_stageChart.destroy();
    }
    var ctx_stage = document.getElementById("manage-chartStages").getContext("2d");
    manage_stageChart = new Chart(ctx_stage, {
        type: 'doughnut',
        data: manage_data_stage,
        animation: {
            animateScale: true
        },
        options: {
            cutoutPercentage: 72,
            tooltips: {
                enabled: false,
                custom: customTooltipsStage
            }
        }
    });
}

/**
 * manage_get_all_markets_for_list
 * @returns {{3: Array, 1: Array, 2: Array, 4: Array, 5: Array, 7: Array, 6: Array}}
 */
function manage_get_all_markets_for_list() {
    var markets_codes = [];
    var ret = [];
    manage_initial_innovation_collection.query().each(function(row, index) {
        var innovation_markets = get_country_array_by_code_array(row['markets_in_array']);
        for (var i = 0; i < innovation_markets.length; i++) {
            var innovation_market = innovation_markets[i];
            if (!in_array(innovation_market['country_code'], markets_codes)) {
                markets_codes.push(innovation_market['country_code']);
                ret.push(innovation_market);
            }
        }
    });
    var innovation_collection = new DataCollection(ret);
    var list = new DataCollection(innovation_collection.query().order('country_name', false).values());

    var true_ret = {
        1: [],
        2: [],
        3: [],
        4: [],
        5: [],
        6: [],
        7: [],
        8: []
    };
    list.query().each(function(row, index) {
        true_ret[parseInt(row['group_id'])].push(row);
    });
    return true_ret;
}

/**
 * manage_get_all_markets_keys_for_region_key
 * @param region_key
 * @returns {Array}
 */
function manage_get_all_markets_keys_for_region_key(region_key) {
    var markets_codes = [];
    if (region_key.charAt(0) === 'R')
        region_key = region_key.slice(1);
    region_key = parseInt(region_key);
    var all_markets = manage_get_all_markets_for_list();
    if (all_markets.hasOwnProperty(region_key)) {
        for (var i = 0; i < all_markets[region_key].length; i++) {
            markets_codes.push(all_markets[region_key][i]['country_code']);
        }
    }
    return markets_codes;
}

/**
 * manage_get_true_market_keys_for_filter
 * @param markets_keys
 * @returns {Array}
 */
function manage_get_true_market_keys_for_filter(markets_keys) {
    // gestion des régions
    var filter_array = [];
    for (var i = 0; i < markets_keys.length; i++) {
        var target = markets_keys[i];
        if (in_array(target, market_region_keys)) {
            var keys_for_region = manage_get_all_markets_keys_for_region_key(target);
            filter_array = filter_array.concat(keys_for_region);
        } else {
            filter_array.push(target);
        }
    }
    return filter_array;
}

/**
 * manage_generate_market_keys_without_region_doublons
 * @param markets_keys
 * @returns {Array.<*>}
 */
function manage_generate_market_keys_without_region_doublons(markets_keys) {
    var filter_array = [];
    var to_remove = [];
    for (var i = 0; i < markets_keys.length; i++) {
        var target = markets_keys[i];
        if (in_array(target, market_region_keys)) {
            var keys_for_region = manage_get_all_markets_keys_for_region_key(target);
            to_remove = to_remove.concat(keys_for_region);
        }
        filter_array.push(target);
    }

    function removeKeys(key) {
        return !in_array(key, to_remove);
    }

    return filter_array.filter(removeKeys);
}

/**
 * manage_get_all_brands_for_list
 * @returns {{0: Array, 1: Array, 2: Array, 3: Array}}
 */
function manage_get_all_brands_for_list() {
    var brand_ids = [];
    var ret = [];

    manage_initial_innovation_collection.query().each(function(row, index) {
        if (row['brand'] && row['brand']['id'] && !in_array(row['brand']['id'], brand_ids)) {
            brand_ids.push(row['brand']['id']);
            ret.push(row['brand']);
        }
    });
    var innovation_collection = new DataCollection(ret);
    var list = new DataCollection(innovation_collection.query().order('title', false).values());

    var true_ret = {
        0: [],
        1: [],
        2: [],
        3: [],
    };
    list.query().each(function(row, index) {
        true_ret[row['group_id']].push(row);
    });
    return true_ret;
}


/* Mosaique
 ----------------------------------------------------------------------------------------*/

/**
 * mosaique_get_category_for_key
 * @param key
 * @returns {*}
 */
function mosaique_get_category_for_key(key) {
    var all_categories = get_innovation_categories();
    for (var i = 0; i < all_categories.length; i++) {
        var a_category = all_categories[i];
        if (a_category['key'] == key) {
            return a_category['text'];
        }
    }
    return key;
}

var market_region_keys = ['R1', 'R2', 'R3', 'R4', 'R5', 'R6', 'R7'];

/**
 * mosaique_init
 * @param type
 */
function mosaique_init(type) {
    type = type || 'manage';
    var list_classe = 'list-explore';
    if (type == 'manage') {
        list_classe = 'list-manage';
    }
    window.scrollTo(0, 0);
    $('#main').addClass(list_classe).removeClass('loading');
    $('html, body').addClass('background-color-fff');
    init_tooltip();
    if (type == 'explore') {
        explore_init();
        explore_use_saved_filters();
        explore_resetStatistics();
        explore_init_consolidation();

        explore_filter_list();
        explore_load_more_list();
        $('.full-content-explore-list .libelle_current_le').html(get_libelle_current_le());
    }

    if (type == 'manage') {
        manage_use_saved_filters();
        manage_resetStatistics();
        manage_init_consolidation();

        manage_filter_list(false);
        manage_load_more_list();
    }

    $('.mosaique-header .content-right .content-sort-by .active-sort').text(mosaique_get_sort_name(jQuery('.mosaique-header .content-right .content-sort-by .link-sort.active').attr('data-target')));

    $('.link-filter').click(function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            $('.mosaique-page-container').toggleClass('filter-on');
        }
    });


    $('#mosaique-sticky-menu .mosaique-header .content-right .content-sort-by .active-sort').click(function(e) {
        e.stopImmediatePropagation();
        if (($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore'))) {
            if ($(this).parent().hasClass('opened')) {
                $(this).parent().removeClass('opened')
            } else {
                $(this).parent().addClass('opened')
            }
        }
    });

    $('#mosaique-sticky-menu .mosaique-header .content-right .content-sort-by .link-sort').click(function(e) {
        e.stopImmediatePropagation();
        if (($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) && !$(this).hasClass('active')) {
            var target = $(this).attr('data-target');
            var text = $(this).find('span').text();
            $('.mosaique-header .content-right .content-sort-by').removeClass('opened');
            $('.mosaique-header .content-right .content-sort-by .active-sort').text(text);
            $('.mosaique-header .content-right .content-sort-by .link-sort').removeClass('active');
            $(this).addClass('active');
            if (type == 'explore') {
                explore_sort_list_by_target(target);
            } else if ($('#main').hasClass('list-manage')) {
                manage_sort_list_by_target(target);
            }
        }
    });

    $(document).mouseup(function(e) {
        var container = $('.content-sort-by');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('.mosaique-header .content-right .content-sort-by').removeClass('opened');
        }
    });

    $('#mosaique-sticky-menu .mosaique-content-filter > .title').click(function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            var $celui = $(this).parent();
            if ($celui.hasClass('opened')) {
                $celui.removeClass('opened');
                $celui.find('.content').slideUp();
            } else {
                $celui.addClass('opened');
                $celui.find('.content').slideDown();
            }
        }
    });

    $('#mosaique-sticky-menu .mosaique-content-filter .content-subfilter:not(.special-case) > .title').click(function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            var $celui = $(this).parent();
            if ($celui.hasClass('opened')) {
                $celui.removeClass('opened');
                $celui.find('.sub-content').slideUp();
            } else {
                $celui.addClass('opened');
                $celui.find('.sub-content').slideDown();
            }
        }
    });

    $('#mosaique-sticky-menu .mosaique-content-filter .content-subfilter.special-case > .title .icon').click(function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            var $celui = $(this).parent().parent();
            if ($celui.hasClass('opened')) {
                $celui.removeClass('opened');
                $celui.find('.sub-content').slideUp();
            } else {
                $celui.addClass('opened');
                $celui.find('.sub-content').slideDown();
            }
        }
    });

    $(document).on("click", '#mosaique-sticky-menu .mosaique-content-all-filters .filter', function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            var $celui = $(this);
            if (!$celui.hasClass('empty')) {
                if ($celui.hasClass('active')) {
                    $celui.removeClass('active');
                    if ($celui.parent().hasClass('content-active-tag-filters')) {
                        $celui.remove();
                    } else {
                        if ($celui.parent().hasClass('content-active-filters') || $celui.parent().hasClass('content-active-tag-filters')) {
                            $celui.remove();
                        }
                        $('.mosaique-content-all-filters').find(".filter[data-target='" + $celui.attr('data-target') + "']").removeClass('active special-display-none');
                        if ($celui.hasClass('group-special')) {
                            $(".content-subfilter .title .filter[data-target='" + $celui.attr('data-target') + "']").parent().parent().find('.filter:not(.group-special)').removeClass('active');
                        } else if ($celui.parent().parent().hasClass('content-subfilter')) {
                            $celui.parent().parent().find('.title .filter').removeClass('active');
                        }
                    }
                } else {
                    $celui.addClass('active');
                    if ($celui.hasClass('group-special')) {
                        $celui.parent().parent().find('.filter:not(.group-special)').addClass('active');
                    }
                }
                mosaique_update_state_filtered();
                if ($('#main').hasClass('list-explore')) {
                    explore_filter_list();
                    explore_save_filters();
                    mosaique_optimize_content_all_filters_height();
                }
                if ($('#main').hasClass('list-manage')) {
                    manage_filter_list();
                    manage_save_filters();
                }
            }
        }
    });

    $('#mosaique-sticky-menu .reset-filter').click(function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            $('.content-mosaique-list').removeClass('filtered');
            $('.mosaique-content-filter .filter').removeClass('active');
            $('.mosaique-content-filter .filter').removeClass('special-display-none');
            $('.content-active-tag-filters').html('');
            if ($('#main').hasClass('list-explore')) {
                explore_reset_filters();
                explore_filter_list();
                mosaique_optimize_content_all_filters_height();
            }
            if ($('#main').hasClass('list-manage')) {
                manage_reset_filters();
                manage_filter_list();
            }
        }
    });

    var stickies = [];
    $('.sticky-entete').each(function() {
        var data_sticky_to = parseInt($(this).attr('data-sticky-to'));
        if ($('#main').hasClass('beta-header-activated')) {
            data_sticky_to += 30;
        }
        var a_sticky = {
            'id': $(this).attr('id'),
            'target_top': data_sticky_to,
            'offsetTop': $(this).offset().top
        };
        stickies.push(a_sticky);
        $('.sticky-entete').addClass('active-stick');
    });

    $(window).scroll(function() {
        var windowTop = $(window).scrollTop();
        if (($(window).scrollTop() + $(window).height() + 300) >= $('html')[0].scrollHeight) {
            if ($('#main').hasClass('list-explore') && (!explore_all_are_loaded && !explore_loading_more)) {
                loadMore();
            }
            if ($('#main').hasClass('list-manage') && !$('#content-tab-feedback-md').is(':visible') && !manage_all_are_loaded && !manage_loading_more) {
                loadMore();
            }
        }
        for (var i = 0; i < stickies.length; i++) {
            var $to_stick = $('#' + stickies[i]['id']);
            if ($to_stick.length > 0) {
                var offset_top = ($('.sticky-ghost-' + stickies[i]['id']).length > 0) ? $('.sticky-ghost-' + stickies[i]['id']).offset().top : $to_stick.offset().top;
                if (offset_top < (windowTop + stickies[i]['target_top'])) {
                    if (!$to_stick.hasClass('ghosted')) {
                        var $clone = $to_stick.clone().removeClass('active-stick').addClass('sticky-ghost sticky-ghost-' + stickies[i]['id']).removeAttr('id');
                        $clone.find('.tooltipstered').removeClass('tooltipstered');
                        $clone.insertBefore('#' + stickies[i]['id']);
                        $to_stick.addClass('ghosted').addClass('sticky-fixed').css({ 'top': stickies[i]['target_top'] + 'px' });
                        $('.sticky-ghost-' + stickies[i]['id']).find('.content-filter').remove();
                        mosaique_optimize_content_all_filters_height();
                    }
                } else {
                    if ($to_stick.hasClass('ghosted')) {
                        $('.sticky-ghost-' + stickies[i]['id']).remove();
                        $to_stick.removeClass('ghosted').removeClass('sticky-fixed').removeAttr('style');
                        mosaique_optimize_content_all_filters_height();
                    }
                }
            }
        }
    });


    $('#mosaique-search-tag').keyup(function() {
        mosaique_search_tag_by_type($(this).val());
    });

    $('.button-benchmark').click(function() {
        if ($('.full-content-list-manage').length > 0) {
            compare_innovation_collection = new DataCollection(manage_filtered_innovation_collection.query().values());
        } else {
            compare_innovation_collection = new DataCollection(explore_filtered_innovation_collection.query().values());
        }
        init_explore_benchmark();
    });

    $(document).on("click", '.content-result-search-tag .search-tag', function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            mosaique_add_tag_to_filters($(this).attr('data-target'));
            $('.content-trending-tags-filter .form-element').removeClass('searching');
            $('.content-trending-tags-filter .input-search-tag').val('');
            $('.content-trending-tags-filter .content-result-search-tag').html('');
        }
    });

    $(document).on("click", '.content-tranding-tags .trending-tag', function(e) {
        e.stopImmediatePropagation();
        if ($('#main').hasClass('list-manage') || $('#main').hasClass('list-explore')) {
            mosaique_add_tag_to_filters($(this).attr('data-target'));
        }
    });


    function loadMore() {
        if ($('#main').hasClass('list-explore')) {
            explore_loading_more = true;
            explore_load_more_list();
            explore_loading_more = false;
        }
        if ($('#main').hasClass('list-manage')) {
            manage_loading_more = true;
            manage_load_more_list();
            manage_loading_more = false;
        }
        init_tooltip();
    }

    if (type == 'explore') {
        explore_save_filters();
    }
    if (type == 'manage') {
        manage_save_filters();
    }
    init_tooltip();
    mosaique_optimize_content_all_filters_height();
}

/**
 * mosaique_optimize_content_all_filters_height();
 */
function mosaique_optimize_content_all_filters_height() {
    if ($('#mosaique-sticky-menu').hasClass('sticky-fixed')) {
        var height = $(window).height() - $('.mosaique-header').height() - $('#element-main-header').height();
        if (!$('#mosaique-sticky-menu').hasClass('sticky-fixed') && $('.content-mosaique-consolidation').is(':visible')) {
            height -= $('.content-mosaique-consolidation').height();
        }
        if ($('.element-sub-header-menu-tab').length > 0) {
            height -= $('.element-sub-header-menu-tab').height();
        }
        height -= 5;
        if (height <= 0) {
            var height = $('.mosaique-page-container .mosaique-list-innovations').height();
            $('.mosaique-content-all-filters').css('height', height + 'px');
        } else {
            $('.mosaique-content-all-filters').css('height', height + 'px');
        }
    } else {
        var height = $('.mosaique-page-container .mosaique-list-innovations').height();
        $('.mosaique-content-all-filters').css('height', height + 'px');
    }
}


/**
 * mosaique_filters_to_top
 */
function mosaique_filters_to_top() {
    $('.content-active-filters *').remove();
    $('#mosaique-sticky-menu .content-other-filters .filter.active').each(function() {
        var $parentActiveContent = $('.content-active-filters');
        var a_clone = '';
        if ($(this).hasClass('group-special')) {
            a_clone = $(this).clone();
            a_clone.addClass('display-block');
            var content_clone = '<span class="text">' + $(this).text() + '</span>';
            content_clone += '<span class="close">+</span>';
            a_clone.html(content_clone);
            $parentActiveContent.append(a_clone);
        } else if (!$(this).parent().parent().hasClass('content-subfilter') ||
            (
                $(this).parent().parent().hasClass('content-subfilter') && !$(this).parent().parent().find('.title .filter').hasClass('active')
            )
        ) {
            a_clone = $(this).clone();
            a_clone.find('.tooltipstered').removeClass('tooltipstered');
            $parentActiveContent.append(a_clone);
        }
    });
    init_tooltip();
    mosaique_update_trending_tags_list_list();
}

/**
 * mosaique_get_sort_name
 * @param $sort_target
 * @return string
 */
function mosaique_get_sort_name(sort_target) {
    switch (sort_target) {
        case 'relevance':
            return "Relevance";
        case 'date':
            return "Date";
        case 'a-z':
            return "A to Z";
        case 'volumes':
            return "Volumes";
        default:
            return "Date";
    }
}

/**
 * mosaique_get_nb_filtered_list_for_collection_family_and_target.
 * @param an_innovation_collection
 * @param family
 * @param target
 * @returns {*}
 */
function mosaique_get_nb_filtered_list_for_collection_family_and_target(an_innovation_collection, family, target) {
    switch (family) {
        case "coop":
            return an_innovation_collection.query().filter({ consumer_opportunity: parseInt(target) }).count();
            break;
        case "market":
            return an_innovation_collection.query().filter({ markets_in_array__array_contains: target }).count();
            break;
        case "category":
            return an_innovation_collection.query().filter({ category: target }).count();
            break;
        case "business_priorities":
        case "portfolio":
            switch (target) {
                case 'big_bet':
                    return an_innovation_collection.query().filter({ big_bet: 'BB' }).count();
                case 'craft':
                    return an_innovation_collection.query().filter({ business_drivers: 'Craft' }).count();
                case 'rtd_rts':
                    return an_innovation_collection.query().filter({ business_drivers: 'RTD / RTS' }).count();
                case 'specialty':
                    return an_innovation_collection.query().filter({ business_drivers: 'Specialty' }).count();
                case 'no_low_alcohol':
                    return an_innovation_collection.query().filter({ business_drivers: 'No & Low Alcohol' }).count();
                default:
                    return 0;
            }

            //investment model
        case "investment_model":
            switch (target) {
                case 'Internal Development':
                    return an_innovation_collection.query().filter({ investment_model: 'Internal Development' }).count();
                case 'External Acquisition':
                    return an_innovation_collection.query().filter({ investment_model: 'External Acquisition' }).count();
                case 'Co-partnership':
                    return an_innovation_collection.query().filter({ investment_model: 'Co-partnership' }).count();
                default:
                    return 0;
            }
            //New business opportunity
        case "new_business_opportunity":
            switch (target) {
                case 'Products beyond liquids':
                    return an_innovation_collection.query().filter({ new_business_opportunity: 'Products beyond liquids' }).count();
                case 'Valuing assets and expertise':
                    return an_innovation_collection.query().filter({ new_business_opportunity: 'Valuing assets and expertise' }).count();
                case 'B to C services':
                    return an_innovation_collection.query().filter({ new_business_opportunity: 'B to C services' }).count();
                case 'B to B services':

                    return an_innovation_collection.query().filter({ new_business_opportunity: 'B to B services' }).count();
                default:
                    return 0;
            }
            //nbm_status
        case "nbm_status":
            switch (target) {
                case 'New Business Acceleration':
                    return an_innovation_collection.query().filter({ growth_strategy: 'New Business Acceleration' }).count();
                case 'Early Stage':
                    return an_innovation_collection.query().filter({ current_stage_id__in: [1, 2, 3] }).count();
                case 'In Market':
                    return an_innovation_collection.query().filter({ current_stage_id__in: [4, 5] }).count();
                default:
                    return 0;
            }

        case "brand":
            return an_innovation_collection.query().filter({ brand__id__is: parseInt(target) }).count();
        case "current_stage":
            if (parseInt(target) === 1) { // Discover
                return an_innovation_collection.query().filter({ current_stage_id: 1, is_frozen: false }).count();
            }
            if (parseInt(target) === 2) { // Ideate
                return an_innovation_collection.query().filter({ current_stage_id: 2, is_frozen: false }).count();
            }
            if (parseInt(target) === 3) { // Experiment
                return an_innovation_collection.query().filter({ current_stage_id: 3, is_frozen: false }).count();
            }
            if (parseInt(target) === 4) { // Incubate
                return an_innovation_collection.query().filter({ current_stage_id: 4, is_frozen: false }).count();
            }
            if (parseInt(target) === 5) { // Scale up
                return an_innovation_collection.query().filter({ current_stage_id: 5, is_frozen: false }).count();
            }
            if (parseInt(target) === 8) { // Permanent range
                return an_innovation_collection.query().filter({ current_stage_id: 8, is_frozen: false }).count();
            }
            if (parseInt(target) === 88) { // Frozen
                return an_innovation_collection.query().filter({ is_frozen: true }).count();
            }
            if (parseInt(target) === 7) { // Discontinued
                return an_innovation_collection.query().filter({ current_stage_id: 7, is_frozen: false }).count();
            }
            return an_innovation_collection.query().filter({ current_stage_id: parseInt(target) }).count();
        case "moc":
            return an_innovation_collection.query().filter({ moc: target }).count();
        case "business_drivers":
            return an_innovation_collection.query().filter({ business_drivers: target }).count();
        case "growth_model":
            return an_innovation_collection.query().filter({ growth_model: target }).count();
        case "classification":
            return an_innovation_collection.query().filter({ proper__classification_type__is: target }).count();

        default:
            return 0;
    }
    return 0;
}

/**
 * mosaique_get_trending_tags_filter_html_template
 * @returns {string}
 */
function mosaique_get_trending_tags_filter_html_template() {
    var out = [],
        o = -1;
    var innovation_collection = ($('#main').hasClass('list-explore')) ? explore_initial_innovation_collection : manage_initial_innovation_collection;
    if ($('#main').hasClass('list-explore')) {
        innovation_collection = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_initial_innovation_collection : explore_initial_innovation_collection;
    }
    var filters = ($('#main').hasClass('list-explore')) ? explore_filters : manage_filters;
    var trending_tags = mosaique_get_all_tags_by_innovation_collection(innovation_collection);
    if (trending_tags.length > 0) {
        out[++o] = '<div class="content-trending-tags-filter margin-top-20">'; // Start content-trending-tags-filter
        out[++o] = '<div class="title font-work-sans-500">Trending tags</div>';
        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<div class="content-input-text position-relative">';
        out[++o] = '<input type="text" name="search-tag" class="input-search input-search-tag" value="" id="mosaique-search-tag" placeholder="Search tags" />';
        out[++o] = '<div class="clear-input-search-tag"></div>';
        out[++o] = '</div>';
        out[++o] = '<div class="content-result-search-tag"></div>';
        out[++o] = '</div>'; // End form-element

        out[++o] = '<div class="content-tranding-tags"></div>';
        out[++o] = '</div>'; // End content-trending-tags-filter
        out[++o] = '<div class="content-active-tag-filters">';
        if (filters['tag'].length > 0) {
            for (var i = 0; i < filters['tag'].length; i++) {
                out[++o] = '<div class="filter active" data-family="tag" data-target="' + filters['tag'][i] + '"><span class="text">#' + filters['tag'][i] + '</span><span class="close">+</span></div>';
            }
        }
        out[++o] = '</div>';
    }
    return out.join('');
}

/**
 * mosaique_get_all_tags_by_innovation_collection.
 * @param innovation_collection
 * @param return_collection
 * @returns {Array}
 */
function mosaique_get_all_tags_by_innovation_collection(innovation_collection, return_collection) {
    return_collection = return_collection || false;
    var ret = [];
    var ret_collection = [];
    innovation_collection.query().each(function(row, index) {
        for (var i = 0; i < row['tags_array'].length; i++) {
            var a_tag = row['tags_array'][i];
            if (!in_array(a_tag, ret)) {
                ret_collection.push({ title: a_tag });
                ret.push(a_tag);
            }
        }
    });
    return (return_collection) ? ret_collection : ret;
}

/**
 * mosaique_get_already_filtered_tags
 * @returns {Array}
 */
function mosaique_get_already_filtered_tags() {
    var filtered_tags = [];
    $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').each(function() {
        var a_tag = $(this).attr('data-target');
        if (!in_array(a_tag, filtered_tags)) {
            filtered_tags.push(a_tag);
        }
    });
    return filtered_tags;
}
/**
 * mosaique_get_all_tags_by_innovation_collection.
 * @param innovation_collection
 * @returns {Array}
 */
function mosaique_get_trendings_tags_by_innovation_collection(innovation_collection, check_already_filtered) {
    check_already_filtered = (typeof check_already_filtered != 'undefined') ? check_already_filtered : true;
    var dc = new DataCollection();
    var filtered_tags = mosaique_get_already_filtered_tags();
    innovation_collection.query().each(function(row, index) {
        for (var i = 0; i < row['tags_array'].length; i++) {
            var a_tag = row['tags_array'][i];
            if (!check_already_filtered || !in_array(a_tag, filtered_tags)) {
                if (dc.query().filter({ title: a_tag }).count() > 0) {
                    var element = dc.query().filter({ title: a_tag }).first();
                    dc.query()
                        .filter({ title: a_tag })
                        .update({ nb: (element['nb'] + 1) });
                } else {
                    dc.insert({
                        'title': a_tag,
                        'nb': 1
                    });
                }
            }

        }
    });
    dc = new DataCollection(dc.query().order('nb', true).limit(0, 5).values());
    var ret = [];
    dc.query().each(function(row, index) {
        ret.push(row['title']);
    });
    return ret;
}

/**
 * mosaique_update_trending_tags_list_list
 */
function mosaique_update_trending_tags_list_list() {
    var innovation_collection = ($('#main').hasClass('list-explore')) ? explore_filtered_innovation_collection : manage_filtered_innovation_collection;
    var trending_tags = mosaique_get_trendings_tags_by_innovation_collection(innovation_collection);
    $('.content-tranding-tags').html('');
    var out = [],
        o = -1;
    for (var i = 0; i < trending_tags.length; i++) {
        out[++o] = '<div class="trending-tag transition" data-target="' + trending_tags[i] + '">' + trending_tags[i] + '</div>';
    }
    $('.content-tranding-tags').html(out.join(''));
}

/**
 * mosaique_get_all_tags_for_search
 * @param searchWord
 * @returns {*}
 */
function mosaique_get_all_tags_for_search(searchWord) {
    var is_explore = $('#main').hasClass('list-explore');
    var filters = (is_explore) ? explore_filters : manage_filters;
    var innovation_collection = (is_explore) ? explore_initial_innovation_collection : manage_initial_innovation_collection;
    if (is_explore) {
        innovation_collection = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_initial_innovation_collection : explore_initial_innovation_collection;
    }
    var an_innovation_collection = new DataCollection(innovation_collection.query().values());

    if (filters.hasOwnProperty('coop') && filters['coop'].length > 0) {
        an_innovation_collection.query().filter({ consumer_opportunity__not_in: filters['coop'] }).remove();
    }
    if (filters.hasOwnProperty('moc') && filters['moc'].length > 0) {
        an_innovation_collection.query().filter({ moc__not_in: filters['moc'] }).remove();
    }
    if (filters.hasOwnProperty('market') && filters['market'].length > 0) {
        if (is_explore) {
            an_innovation_collection.query().filter({ markets_in_array__array_not_contains_any: explore_get_true_market_keys_for_filter(filters['market']) }).remove();
        } else {
            an_innovation_collection.query().filter({ markets_in_array__array_not_contains_any: manage_get_true_market_keys_for_filter(filters['market']) }).remove();
        }
    }
    if (filters.hasOwnProperty('category') && filters['category'].length > 0) {
        an_innovation_collection.query().filter({ category__not_in: filters['category'] }).remove();
    }
    if (filters.hasOwnProperty('brand') && filters['brand'].length > 0) {
        an_innovation_collection.query().filter({ brand_id__not_in: filters['brand'] }).remove();
    }
    if (filters.hasOwnProperty('portfolio') && filters['portfolio'].length > 0) {
        var remove_big_bets = (!in_array('big_bet', filters['portfolio']));
        if (!in_array('craft', filters['portfolio'])) { // craft
            if (remove_big_bets) {
                an_innovation_collection.query().filter({ business_drivers: "Craft" }).remove();
            } else {
                an_innovation_collection.query().filter({ big_bet: '', business_drivers: "Craft" }).remove();
            }
        }
        if (!in_array('rtd_rts', filters['portfolio'])) { // rtd_rts
            if (remove_big_bets) {
                an_innovation_collection.query().filter({ business_drivers: "RTD / RTS" }).remove();
            } else {
                an_innovation_collection.query().filter({ big_bet: '', business_drivers: "RTD / RTS" }).remove();
            }
        }
        if (!in_array('specialty', filters['portfolio'])) { // specialty
            if (remove_big_bets) {
                an_innovation_collection.query().filter({ business_drivers: "Specialty" }).remove();
            } else {
                an_innovation_collection.query().filter({ big_bet: '', business_drivers: "Specialty" }).remove();
            }
        }
        if (!in_array('no_low_alcohol', filters['portfolio'])) { // no_low_alcohol
            if (remove_big_bets) {
                an_innovation_collection.query().filter({ business_drivers: "No & Low Alcohol" }).remove();
            } else {
                an_innovation_collection.query().filter({
                    big_bet: '',
                    business_drivers: "No & Low Alcohol"
                }).remove();
            }
        }
        if (!in_array('none', filters['portfolio'])) { // None
            if (remove_big_bets) {
                an_innovation_collection.query().filter({ business_drivers: "None" }).remove();
                an_innovation_collection.query().filter({ business_drivers: null }).remove();
            } else {
                an_innovation_collection.query().filter({ big_bet: '', business_drivers: "None" }).remove();
                an_innovation_collection.query().filter({ big_bet: '', business_drivers: null }).remove();
            }
        }
        if (!in_array('big_bet', filters['portfolio'])) { // Big Bet
            an_innovation_collection.query().filter({
                big_bet: 'BB',
                business_drivers__not_in: ["Craft", "RTD / RTS", "No & Low Alcohol", "Specialty"]
            }).remove();
        }
    }

    //investment model 

    if (filters.hasOwnProperty('investment_model') && filters['investment_model'].length > 0) {
        an_innovation_collection.query().filter({ investment_model__not_in: filters['investment_model'] }).remove();
    }
    if (filters.hasOwnProperty('nbm_status') && filters['nbm_status'].length > 0) {
        var remove_nba = (!in_array('New Business Acceleration', filters['nbm_status']));
        if (!in_array('New Business Acceleration', filters['nbm_status'])) {
            an_innovation_collection.query().filter({ growth_strategy: 'New Business Acceleration' }).remove();
        }
        if (!in_array('Early Stage', filters['nbm_status'])) {
            if (remove_nba) {
                an_innovation_collection.query().filter({ current_stage_id__in: [1, 2, 3] }).remove();
            } else {
                an_innovation_collection.query().filter({
                    current_stage_id__in: [1, 2, 3],
                    growth_strategy__not: 'New Business Acceleration'
                }).remove();
            }
        }
        if (!in_array('In Market', filters['nbm_status'])) {
            if (remove_nba) {
                an_innovation_collection.query().filter({ current_stage_id__in: [4, 5] }).remove();
            } else {
                an_innovation_collection.query().filter({
                    current_stage_id__in: [4, 5],
                    growth_strategy__not: 'New Business Acceleration'
                }).remove();
            }
        }
    }
    if (filters.hasOwnProperty('current_stage') && filters['current_stage'].length > 0) {
        if (!in_array(1, manage_filters['current_stage'])) { // No Discover
            an_innovation_collection.query().filter({ current_stage_id: 1, is_frozen: false }).remove();
        }
        if (!in_array(2, manage_filters['current_stage'])) { // No Ideate
            an_innovation_collection.query().filter({ current_stage_id: 2, is_frozen: false }).remove();
        }
        if (!in_array(3, manage_filters['current_stage'])) { // No Experiment
            an_innovation_collection.query().filter({ current_stage_id: 3, is_frozen: false }).remove();
        }
        if (!in_array(4, manage_filters['current_stage'])) { // No Incubate
            an_innovation_collection.query().filter({ current_stage_id: 4, is_frozen: false }).remove();
        }
        if (!in_array(5, manage_filters['current_stage'])) { // No Scale up
            an_innovation_collection.query().filter({ current_stage_id: 5, is_frozen: false }).remove();
        }
        if (!in_array(8, manage_filters['current_stage'])) { // No Permanent range
            an_innovation_collection.query().filter({ current_stage_id: 8, is_frozen: false }).remove();
        }
        if (!in_array(88, manage_filters['current_stage'])) { // No Frozen
            an_innovation_collection.query().filter({ is_frozen: true }).remove();
        }
        if (!in_array(7, manage_filters['current_stage'])) { // No Y2
            an_innovation_collection.query().filter({ current_stage_id: 7, is_frozen: false }).remove();
        }
    }
    if (filters.hasOwnProperty('growth_model') && filters['growth_model'].length > 0) {
        an_innovation_collection.query().filter({ growth_model__not_in: filters['growth_model'] }).remove();
    }
    if (filters.hasOwnProperty('classification') && filters['classification'].length > 0) {
        an_innovation_collection.query().filter({ proper__classification_type__not_in: filters['classification'] }).remove();
    }
    var all_tags = mosaique_get_all_tags_by_innovation_collection(an_innovation_collection, true);
    var filtered_tags = mosaique_get_already_filtered_tags();
    var tags_collection = new DataCollection(all_tags);
    return tags_collection.query().filter({ title__icontains: searchWord, title__not_in: filtered_tags }).values();
}

/**
 * mosaique_search_tag_by_type
 * @param searchWord
 */
function mosaique_search_tag_by_type(searchWord) {
    $('.content-result-search-tag').html('');
    var search_results = mosaique_get_all_tags_for_search(searchWord);
    if (search_results.length == 0) {
        $('.content-result-search-tag').html('<div class="no-result">No tag found…</div>');
    } else {
        var out = [],
            o = -1;
        for (var i = 0; i < search_results.length; i++) {
            out[++o] = '<div class="search-tag text-ellipsis" data-target="' + search_results[i]['title'] + '">' + search_results[i]['title'] + '</div>';
        }
        $('.content-result-search-tag').html(out.join(''));
    }
}

function mosaique_add_tag_to_filters(target) {
    $('.content-mosaique-list').addClass('filtered');
    var element = '<div class="filter active" data-family="tag" data-target="' + target + '"><span class="text">#' + target + '</span><span class="close">+</span></div>';
    $('.content-active-tag-filters').append(element);
    if ($('#main').hasClass('list-explore')) {
        explore_filter_list();
    } else if ($('#main').hasClass('list-manage')) {
        manage_filter_list();
    }
}

function mosaique_update_state_filtered() {
    var nb_filters = $('#mosaique-sticky-menu .content-other-filters .filter.active').length + $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').length;
    if (nb_filters > 0) {
        $('.content-mosaique-list').addClass('filtered');
    } else {
        $('.content-mosaique-list').removeClass('filtered');
    }
}