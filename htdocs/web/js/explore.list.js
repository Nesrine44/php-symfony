var explore_loading_more = false;
var explore_all_are_loaded = false;
var explore_initial_innovation_collection = new DataCollection();
var explore_new_business_models_initial_innovation_collection = new DataCollection();
var explore_filtered_innovation_collection = new DataCollection();
var explore_stageChart = null;
var explore_data_stage = null;
var explore_innovation_offset = 0;
var explore_innovation_limit = 20;
var explore_filters = explore_products_get_default_filters();
var explore_is_filtered = false;
var explore_statistics = null;
var explore_stats_loaded = false;
var explore_data_filters = null;
var explore_filteredinnovationsDC = new DataCollection();
var explore_filteredOuterInnovationsDC = new DataCollection();
var explore_color_empty = "rgba(255,255,255,0.1)"; //"#79879E";
var explore_border_color = "#18346A";
var explore_map = null;
var explore_map_data = {};
var explore_map_data_empty = {};
var explore_map_max = 0;


/**
 * explore_products_get_default_filters
 * @returns {{sort: string, tag: Array, portfolio: Array, category: Array, category_key: Array, market: Array, coop: Array, brand: Array, current_stage: Array}}
 */
function explore_products_get_default_filters() {
    return {
        'sort': 'date',
        'tag': [],
        'portfolio': [],
        'category': [],
        'category_key': [],
        'market': [],
        'coop': [],
        'brand': [],
        'moc': [],
        'current_stage': []
    };
}

/**
 * explore_new_business_models_get_default_filters
 * @returns {{sort: string, tag: Array, investment_model: Array, new_business_opportunity: Array, current_stage: Array,growth_strategy: Array}}
 */
function explore_new_business_models_get_default_filters() {
    return {
        'sort': 'relevance',
        'tag': [],
        'investment_model': [],
        'new_business_opportunity': [],
        'growth_strategy': [],
        'current_stage': [],
        'nbm_status': []
    };
}

/**
 * explore_set_filters_by_url
 */
function explore_set_filters_by_url() {
    var getVars = getUrlVars();
    var is_products = ($('#explore-products').is(':visible'));
    explore_filters = (is_products) ? explore_products_get_default_filters() : explore_new_business_models_get_default_filters();
    explore_is_filtered = false;
    if (Object.keys(getVars).length > 0) {
        if (getVars.hasOwnProperty('filter[tag]')) {
            explore_filters['tag'] = rawSplit(getVars['filter[tag]'], ',');
            explore_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[portfolio]')) {
            explore_filters['portfolio'] = rawSplit(getVars['filter[portfolio]'], ',');
            explore_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[coop]')) {
            explore_filters['coop'] = splitToInt(getVars['filter[coop]'], ',');
            explore_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[moc]')) {
            explore_filters['moc'] = rawSplit(getVars['filter[moc]'], ',');
            explore_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[market]')) {
            explore_filters['market'] = rawSplit(getVars['filter[market]'], ',');
            explore_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[category]')) {
            explore_filters['category'] = rawSplit(getVars['filter[category]'], ',');
            explore_is_filtered = true;
        }
        if (getVars.hasOwnProperty('filter[brand]')) {
            explore_filters['brand'] = splitToInt(getVars['filter[brand]'], ',');
            explore_is_filtered = true;
        }
        //investment model
        if (getVars.hasOwnProperty('filter[investment_model]')) {
            explore_filters['investment_model'] = rawSplit(getVars['filter[investment_model]'], ',');
            explore_is_filtered = true;
        }

        //nbm status
        if (getVars.hasOwnProperty('filter[nbm_status]')) {
            explore_filters['nbm_status'] = rawSplit(getVars['filter[nbm_status]'], ',');
            explore_is_filtered = true;
        }
        //new business opportunity
        if (getVars.hasOwnProperty('filter[new_business_opportunity]')) {
            explore_filters['new_business_opportunity'] = rawSplit(getVars['filter[new_business_opportunity]'], ',');
            explore_is_filtered = true;
        }
        //growth_strategy
        if (getVars.hasOwnProperty('filter[growth_strategy]')) {
            explore_filters['growth_strategy'] = rawSplit(getVars['filter[growth_strategy]'], ',');
            explore_is_filtered = true;
        }

        if (getVars.hasOwnProperty('filter[current_stage]')) {
            explore_filters['current_stage'] = splitToInt(getVars['filter[current_stage]'], ',');
            explore_is_filtered = true;
        }
    }
    var sort = findGetParameter('sort');
    if (!sort) {
        sort = ($('#explore-new_business_models').is(':visible')) ? 'relevance' : 'date';
    }
    explore_filters['sort'] = sort;
}

/**
 * goToExploreListPage
 * @param link
 */
function goToExploreListPage(link, tab) {
    try {
        before_opening_page();
        link = link || "/content/explore";
        tab = tab || "products";
        $('#main').addClass('loading');
        opti_update_url(link, 'list-explore');
        $('html, body').addClass('background-color-fff');
        update_menu_active('menu-li-explore');
        changePageTitle('Explore | ' + global_wording['title']);
        update_main_classes(main_classes);
        explore_filters = (tab == 'new_business_models') ? explore_new_business_models_get_default_filters() : explore_products_get_default_filters();
        $('#pri-main-container-without-header').html(explore_get_html_template(link, tab));
        explore_set_filters_by_url();
        mosaique_init('explore');
    } catch (error) {
        init_unknow_error_view(error);
    }
}

/**
 * explore_init.
 */
function explore_init() {
    if (check_if_user_has_new_business_model_access()) {
        $('.pri-tab').tabs({
            activate: function(event, ui) {
                if ($(event.currentTarget).length > 0) {
                    window.scrollTo(0, 0);
                    var target = $(event.currentTarget).attr('href').replace('#', '');
                    if (target == 'explore-products') {
                        goToExploreListPage('/content/explore', 'products');
                    } else if (target == 'explore-new_business_models') {
                        goToExploreListPage('/content/explore/tab/new_business_models', 'new_business_models');
                    }
                }
            }
        });
    }
}

/**
 * explore_get_html_template
 * @param link
 * @returns {string}
 */
function explore_get_html_template(link, tab) {
    var out = [],
        o = -1;
    var has_tab = (check_if_user_has_new_business_model_access());
    out[++o] = '<div class="full-content-explore-list-page ' + ((has_tab) ? 'pri-tab' : '') + '">'; // Start full-content-explore-list-page

    if (has_tab) {
        out[++o] = '<div class="element-sub-header-menu-tab">'; // Start element-sub-header-menu-tab
        out[++o] = '<div class="central-content">'; // Start central-content
        out[++o] = '<div class="text-align-right">'; // Start text-align-right
        out[++o] = '<ul class="pri-tab-menu color-white display-inline-block">';
        out[++o] = '<li ' + ((tab == 'products') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#explore-products">Products</a></li>';
        out[++o] = '<li ' + ((tab == 'new_business_models') ? 'class="ui-tabs-active ui-state-active"' : '') + '><a href="#explore-new_business_models">New Business Models</a></li>';
        out[++o] = '</ul>';
        out[++o] = '</div>'; // End text-align-right
        out[++o] = '</div>'; // End central-content
        out[++o] = '</div>'; // End element-sub-header-menu-tab
        out[++o] = '<div class="fake-element-sub-header-menu-tab"></div>';
    }
    out[++o] = '<div id="explore-products">' + ((tab == 'products') ? explore_generate_products_tab() : '') + '</div>';
    if (has_tab) {
        out[++o] = '<div id="explore-new_business_models">' + ((tab == 'new_business_models') ? explore_generate_new_business_models_tab() : '') + '</div>';
    }
    out[++o] = '</div>'; // End full-content-explore-list-page
    return out.join('');
}

/**
 * explore_generate_products_tab.
 *
 * @returns {string}
 */
function explore_generate_products_tab() {
    var out = [],
        o = -1;
    var has_tab = check_if_user_has_new_business_model_access();
    var categories = get_innovation_categories();
    var brands = explore_get_all_brands_for_list();
    var markets = explore_get_all_markets_for_list();

    var sort = explore_filters['sort'];
    var sort_name = mosaique_get_sort_name(explore_filters['sort']);

    var filter_on_class = ($(window).width() > 700) ? 'filter-on' : '';
    out[++o] = '<div class="full-content-explore-list mosaique-page-container ' + filter_on_class + '">'; // Start full-content-explore-list

    out[++o] = '<div class="content-inner">'; // Start Content inner
    out[++o] = '<div class="content-explore-consolidation content-mosaique-consolidation background-color-gradient-161541" style="height:440px;">'; // Start content-explore-consolidation
    out[++o] = explore_products_get_consolidation_html_template();
    out[++o] = '</div>'; // End content-explore-consolidation

    out[++o] = '<div class="background-color-fff content-explore-list content-mosaique-list  ' + ((explore_is_filtered) ? 'filtered' : '') + '">'; // Start content-explore-list

    var sticky_to = (has_tab) ? 90 : 50;
    out[++o] = '<div class="sticky-entete z-index-5 position-relative" id="mosaique-sticky-menu" data-sticky-to="' + sticky_to + '">'; // Start #mosaique-sticky-menu
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
    out[++o] = '<div class="content-sort-by">'; // Start content-sort-by
    out[++o] = 'Sort by: <span class="active-sort">' + sort_name + '</span>';
    out[++o] = '<div class="popup-sort-by">'; // Start popup-sort-by
    out[++o] = '<div class="content">'; // Start content
    out[++o] = '<div class="link-sort ' + ((sort == 'date') ? 'active' : '') + '" data-target="date"><span>Date</span></div>';
    out[++o] = '<div class="link-sort ' + ((sort == 'a-z') ? 'active' : '') + '" data-target="a-z"><span>A to Z</span></div>';
    out[++o] = '<div class="link-sort ' + ((sort == 'volumes') ? 'active' : '') + '" data-target="volumes"><span>Volumes</span></div>';
    out[++o] = '</div>'; // End content
    out[++o] = '</div>'; // End popup-sort-by
    out[++o] = '</div>'; // End content-sort-by
    out[++o] = '<button class="hub-button button-benchmark">Compare</button>';
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
        out[++o] = '<div class="filter empty ' + ((in_array(the_bp['id'], explore_filters['portfolio'])) ? 'active' : '') + '"';
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

    // mosaique-content-filter CATEGORY
    out[++o] = '<div class="mosaique-content-filter"';
    out[++o] = 'data-family="category">';
    out[++o] = '<div class="title">';
    out[++o] = 'Categories';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';

    for (var u = 0; u < categories.length; u++) {
        var a_category = categories[u];
        out[++o] = '<div class="filter empty helper-on-hover on-hover ' + ((in_array(a_category['text'], explore_filters['category'])) ? 'active' : '') + '" ';
        out[++o] = 'data-family="category" data-target="' + a_category['text'] + '">';
        out[++o] = '<span class="text">' + a_category['text'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter CATEGORY

    // mosaique-content-filter MARKET
    out[++o] = '<div class="mosaique-content-filter content-filter-markets" data-family="market">';
    out[++o] = '<div class="title">';
    out[++o] = 'Geographic Scope';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display: none;">';

    for (var k in markets) {
        if (markets.hasOwnProperty(k)) {
            if (parseInt(k) != COUNTRY_GROUP_ID_USA) { // USA state
                var group_name = get_country_group_name(parseInt(k));
                var group_markets = markets[k];
                if (group_markets.length > 0) {
                    out[++o] = '<div class="content-subfilter special-case">';
                    out[++o] = '<div class="title">';
                    out[++o] = '<span class="filter group-special ' + ((in_array('R' + k, explore_filters['market'])) ? 'active' : '') + '"';
                    out[++o] = 'data-family="market" data-target="R' + k + '">';
                    out[++o] = group_name;
                    out[++o] = '</span>';
                    out[++o] = '<div class="icon">+</div>';
                    out[++o] = '</div>';
                    out[++o] = '<div class="sub-content" style="display:none;">';
                    for (var i = 0; i < group_markets.length; i++) {
                        var market = group_markets[i];
                        //if(market['country_code'] != 'US') { // USA state
                        out[++o] = '<div class="filter empty ' + ((in_array(market['country_code'], explore_filters['market']) || in_array('R' + k, explore_filters['market'])) ? 'active' : '') + '"';
                        out[++o] = 'data-family="market" data-target="' + market['country_code'] + '">';
                        out[++o] = '<span class="text">' + market['country_name'] + '</span>';
                        out[++o] = '<span class="nb">0</span>';
                        out[++o] = '<span class="close">+</span>';
                        out[++o] = '</div>';
                        //} // USA state
                    }
                    out[++o] = '</div>';
                    out[++o] = '</div>';
                }
            }
        }
    }

    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter MARKET

    // mosaique-content-filter BRAND
    out[++o] = '<div class="mosaique-content-filter content-filter-brands" data-family="brand">';
    out[++o] = '<div class="title">';
    out[++o] = 'Brands';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display: none;">';

    out[++o] = '<div class="content-subfilter">';
    out[++o] = '<div class="title">';
    out[++o] = '•  Strategic International';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="sub-content" style="display: none;">';
    for (var i = 0; i < brands[1].length; i++) {
        var brand = brands[1][i];
        out[++o] = '<div class="filter empty ' + ((in_array(brand['id'], explore_filters['brand'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="brand" data-target="' + brand['id'] + '">';
        out[++o] = '<span class="text">' + brand['title'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '<div class="content-subfilter">';
    out[++o] = '<div class="title">';
    out[++o] = '•  Strategic Local';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="sub-content" style="display: none;">';
    for (var i = 0; i < brands[2].length; i++) {
        var brand = brands[2][i];
        out[++o] = '<div class="filter empty ' + ((in_array(brand['id'], explore_filters['brand'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="brand" data-target="' + brand['id'] + '">';
        out[++o] = '<span class="text">' + brand['title'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '<div class="content-subfilter">';
    out[++o] = '<div class="title">';
    out[++o] = '•  Wines';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="sub-content" style="display: none;">';
    for (var i = 0; i < brands[3].length; i++) {
        var brand = brands[3][i];
        out[++o] = '<div class="filter empty ' + ((in_array(brand['id'], explore_filters['brand'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="brand" data-target="' + brand['id'] + '">';
        out[++o] = '<span class="text">' + brand['title'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '<div class="content-subfilter">';
    out[++o] = '<div class="title">';
    out[++o] = '•  Others';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="sub-content" style="display: none;">';
    for (var i = 0; i < brands[0].length; i++) {
        var brand = brands[0][i];
        out[++o] = '<div class="filter empty ' + ((in_array(brand['id'], explore_filters['brand'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="brand" data-target="' + brand['id'] + '">';
        out[++o] = '<span class="text">' + brand['title'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '</div>';
    out[++o] = '<div class="separator"></div>';
    out[++o] = '</div>';
    // end mosaique-content-filter BRAND

    // mosaique-content-filter COOP
    out[++o] = '<div class="mosaique-content-filter" data-family="coop">';
    out[++o] = '<div class="title">';
    out[++o] = 'Consumer Opportunities';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';
    var all_consumer_opportunities = get_all_consumers_opportunities();
    for (var g = 0; g < all_consumer_opportunities.length; g++) {
        out[++o] = '<div class="filter empty ' + ((in_array(all_consumer_opportunities[g]['id'] + '', explore_filters['coop'])) ? 'active' : '') + '"';
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



    // mosaique-content-filter current_stage
    out[++o] = '<div class="mosaique-content-filter" data-family="current_stage">';
    out[++o] = '<div class="title">';
    out[++o] = 'Stages';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';

    var all_filter_stages = [
        { id: 4, target: 'incubate' },
        { id: 5, target: 'scale_up' }
    ];
    for (var i = 0; i < all_filter_stages.length; i++) {
        var the_stage = all_filter_stages[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_stage['id'], explore_filters['current_stage'])) ? 'active' : '') + '"';
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

    out[++o] = '</div>'; // End content-explore-list
    out[++o] = '</div>'; // End Content inner
    out[++o] = '</div>'; // End full-content-explore-list
    return out.join('');
}

/**
 * explore_generate_new_business_models_tab.
 *
 * @returns {string}
 */
function explore_generate_new_business_models_tab() {
    var out = [],
        o = -1;
    var has_tab = check_if_user_has_new_business_model_access();
    var categories = get_innovation_categories();
    var brands = explore_get_all_brands_for_list();
    var markets = explore_get_all_markets_for_list();

    var sort = explore_filters['sort'];
    var sort_name = mosaique_get_sort_name(explore_filters['sort']);

    var filter_on_class = ($(window).width() > 700) ? 'filter-on' : '';
    out[++o] = '<div class="full-content-explore-list mosaique-page-container ' + filter_on_class + '">'; // Start full-content-explore-list

    out[++o] = '<div class="content-inner">'; // Start Content inner
    out[++o] = '<div class="content-explore-consolidation content-mosaique-consolidation background-color-gradient-161541" style="height:440px;">'; // Start content-explore-consolidation





    out[++o] = '<div class="column-percent-30 margin-left-40 ">';
    out[++o] = '<div class="column-percent-50 margin-top-20">';
    out[++o] = '<div class="content-donut content-donut-stages" style="width: 130px;height: 130px;">'; // Start content-donut
    out[++o] = '<canvas id="explore-chartStages" width="130" height="130"></canvas>';
    //out[++o] = '<div class="donut-inner-stage"><span>0</span><div>Projects</div></div>';
    out[++o] = '</div>';


    out[++o] = '</div>'; // End content-donut






    out[++o] = '</div>'; //End column-percent-30
    out[++o] = '</div>'; // End content-explore-consolidation

    out[++o] = '<div class="background-color-fff content-explore-list content-mosaique-list  ' + ((explore_is_filtered) ? 'filtered' : '') + '">'; // Start content-explore-list

    var sticky_to = (has_tab) ? 90 : 50;
    out[++o] = '<div class="sticky-entete z-index-5 position-relative" id="mosaique-sticky-menu" data-sticky-to="' + sticky_to + '">'; // Start #mosaique-sticky-menu
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
    out[++o] = '<div class="content-sort-by padding-top-17">'; // Start content-sort-by
    out[++o] = 'Sort by: <span class="active-sort">' + sort_name + '</span>';
    out[++o] = '<div class="popup-sort-by">'; // Start popup-sort-by
    out[++o] = '<div class="content">'; // Start content
    out[++o] = '<div class="link-sort ' + ((sort == 'relevance') ? 'active' : '') + '" data-target="relevance"><span>Relevance</span></div>';
    out[++o] = '<div class="link-sort ' + ((sort == 'a-z') ? 'active' : '') + '" data-target="a-z"><span>A to Z</span></div>';
    out[++o] = '</div>'; // End content
    out[++o] = '</div>'; // End popup-sort-by
    out[++o] = '</div>'; // End content-sort-by
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




    // mosaique-content-filter nbm_status 
    out[++o] = '<div class="mosaique-content-filter growth_strategy-style" data-family="nbm_status">';
    out[++o] = '<div class="content" style="display:block;">';

    var all_filter_growth_nbm_status = [
        { 'id': 'New Business Acceleration', target: 'New Business Acceleration' },
        { 'id': 'Early Stage', target: 'Early Stage' },
        { 'id': 'In Market', target: 'In Market' }
    ];

    for (var i = 0; i < all_filter_growth_nbm_status.length; i++) {
        var nbm_status = all_filter_growth_nbm_status[i];
        out[++o] = '<div class="filter empty ' + ((in_array(nbm_status['id'], explore_filters['nbm_status'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="nbm_status" data-target="' + nbm_status['id'] + '">';
        out[++o] = '<div class="helper-on-hover" data-target="' + nbm_status['id'] + '">';
        out[++o] = '<span class="text">' + nbm_status['target'] + '</span>';
        out[++o] = '<span class="nb">0</span>';
        out[++o] = '<span class="close">+</span>';
        out[++o] = '</div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '</div>';
    // end mosaique-content-filter growth_strategies





    // mosaique-content-filter Investment Model
    out[++o] = '<div class="mosaique-content-filter" data-family="investment_model">';
    out[++o] = '<div class="title">';
    out[++o] = 'Investment Model';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';

    var all_filter_investment_models = [
        { id: 'Internal Development', target: 'Internal Development' },
        { id: 'External Acquisition', target: 'External Acquisition' },
        { id: 'Co-partnership', target: 'Co-partnership' },

    ];
    for (var i = 0; i < all_filter_investment_models.length; i++) {
        var the_bp = all_filter_investment_models[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_bp['id'], explore_filters['investment_model'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="investment_model" data-target="' + the_bp['id'] + '">';
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
    // end mosaique-content-filter Investment Model




    // mosaique-content-filter Business Opportunities 
    out[++o] = '<div class="mosaique-content-filter" data-family="new_business_opportunity">';
    out[++o] = '<div class="title">';
    out[++o] = 'New Business Opportunity';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';

    var all_filter_new_business_opportunities = [
        { id: 'Products beyond liquids', target: 'Products beyond liquids' },
        { id: 'Valuing assets and expertise', target: 'Valuing assets and expertise' },
        { id: 'B to C services', target: 'B to C services' },
        { id: 'B to B services', target: 'B to B services' },

    ];
    for (var i = 0; i < all_filter_new_business_opportunities.length; i++) {
        var the_bp = all_filter_new_business_opportunities[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_bp['id'], explore_filters['new_business_opportunity'])) ? 'active' : '') + '"';
        out[++o] = 'data-family="new_business_opportunity" data-target="' + the_bp['id'] + '">';
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
    // end mosaique-content-filter Business Opportunities 






    // mosaique-content-filter current_stage
    out[++o] = '<div class="mosaique-content-filter" data-family="current_stage">';
    out[++o] = '<div class="title">';
    out[++o] = 'Stages';
    out[++o] = '<div class="icon">+</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content" style="display:none;">';


    var all_filter_stages = [
        { id: 2, target: 'ideate' },
        { id: 3, target: 'experiment' },
        { id: 4, target: 'incubate' },
        { id: 5, target: 'scale_up' }
    ];
    for (var i = 0; i < all_filter_stages.length; i++) {
        var the_stage = all_filter_stages[i];
        out[++o] = '<div class="filter empty ' + ((in_array(the_stage['id'], explore_filters['current_stage'])) ? 'active' : '') + '"';
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

    out[++o] = '</div>'; // End content-explore-list
    out[++o] = '</div>'; // End Content inner

    out[++o] = '</div>'; // End full-content-explore-list
    return out.join('');
}



/**
 * Chargement de la liste
 * @param scroll_to_top
 */
function explore_load_list(scroll_to_top) {
    scroll_to_top = typeof scroll_to_top !== 'undefined' ? scroll_to_top : false;
    $('#main').addClass('loading');
    $('.pri-table-list tbody').html('');
    monitor_update_list_elements();

    var out = [],
        o = -1;
    explore_filteredinnovationsDC.query().limit(explore_innovation_list_offset, explore_innovation_list_limit).each(function(row, index) {
        out[++o] = explore_get_table_line_by_innovation(row);
    });
    explore_initialize_list_pagination();
    $('.pri-table-list').parent().find('.pri-list-not-found').remove();
    $('.pri-table-list tbody').html("");
    if (out.length == 0) {
        $('.pri-table-list').parent().append('<div class="pri-list-not-found">No innovation found…</div>');
    } else {
        $('.pri-table-list tbody').html(out.join(''));
    }
    $('#main').removeClass('loading');
    if (scroll_to_top) {
        window.scrollTo(0, 0);
    }
    explore_set_libelle_h1_for_list();
    var link = window.location.href;
    if (link.indexOf('inno/') === -1) {
        $('a.link-list-innovations').attr('href', link);
    }
    optimization_update();
    explore_update_stats_consolidation();
}










/**
 * explore_load_more_list
 */
function explore_load_more_list() {
    var out = [],
        o = -1;
    explore_filtered_innovation_collection.query().limit(explore_innovation_offset, explore_innovation_limit).each(function(row, index) {
        out[++o] = explore_get_innovation_html(row);
    });
    $('.mosaique-list-innovations').append(out.join(''));
    explore_innovation_offset += explore_innovation_limit;
    if (explore_filtered_innovation_collection.query().count() <= explore_innovation_offset) {
        explore_all_are_loaded = true;
    }
}

/**
 * explore_get_innovation_html
 *
 * @param innovation
 * @param with_growth_model
 * @param action_save_search
 * @returns {string}
 */
function explore_get_innovation_html(innovation, with_growth_model, action_save_search) {
    with_growth_model = (typeof with_growth_model != 'undefined') ? with_growth_model : true;
    action_save_search = (typeof action_save_search != 'undefined') ? action_save_search : false;
    var out = [],
        o = -1;
    out[++o] = '';
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    var explore_detail_url = "/explore/" + innovation['id'];
    out[++o] = '<div class="mosaique-innovation">';
    out[++o] = '<div class="content">';
    if (action_save_search) {
        out[++o] = '<a href="' + explore_detail_url + '" class="link_explore_detail global-link save-search-action" data-title="' + innovation['title'] + '" data-css-class="link_explore_detail">';
    } else {
        out[++o] = '<a href="' + explore_detail_url + '" class="link_explore_detail global-link">';
    }
    var style_background = 'style="background-image:url(' + innovation['explore']['images'][0] + ');"';
    out[++o] = '<div class="picture" ' + style_background + '></div>';
    out[++o] = '<div class="title">' + innovation['title'] + '</div>';
    out[++o] = '<div class="infos">';

    if (innovation_is_a_service) {
        out[++o] = '<span class="micro-icon-stage position-relative ' + innovation['current_stage'] + '" style="top:-1px;"></span>';
        out[++o] = returnGoodStage(innovation['current_stage']);
    }


    if (with_growth_model && !innovation_is_a_service) {
        if (innovation['growth_model'] == 'fast_growth') {
            out[++o] = 'Fast Growth';
        } else if (innovation['growth_model'] == 'slow_build') {
            out[++o] = 'Slow Build';
        }
    }
    if (innovation['in_market_date'] && !in_array(innovation['current_stage'], ['discover', 'ideate'])) {
        var mmYY = gmdate("m/y", innovation['in_market_date']);
        if ((with_growth_model && !innovation_is_a_service) || innovation_is_a_service) {
            out[++o] = ' • ';
        }
        out[++o] = ' Started in ' + mmYY;
    }

    out[++o] = '</div>';
    out[++o] = '</a>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * explore_products_get_consolidation_html_template
 * @returns {string}
 */
function explore_products_get_consolidation_html_template() {
    var out = [],
        o = -1;
    out[++o] = '<div class="central-content padding-top-20 padding-bottom-20 overflow-hidden">'; // Start central-content
    out[++o] = '<div class="column-percent-80 no-zoom-map">';
    out[++o] = '<div id="explore-list-consolidation-map" class="explore-list-map transition"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="column-percent-20">';

    out[++o] = '<div class="color-d2d2d2 font-work-sans-400 font-size-13 line-height-1 margin-bottom-5" style="margin-top: 166px;">Projects in markets</div>';
    out[++o] = '<div class="explore-list-consolidation-nb-projects color-fff font-work-sans-300 font-size-48 margin-bottom-40">0</div>';

    out[++o] = '<div class="color-d2d2d2 font-work-sans-400 font-size-13 line-height-1 margin-bottom-5">Volumes <span class="libelle_current_le">LE1 19</span></div>';
    out[++o] = '<div class="color-fff font-work-sans-300 font-size-48 margin-bottom-40"><span class="explore-list-consolidation-nb-volume">0</span> <span class="font-work-sans-300 line-height-1 font-size-30 color-fff-0-5">' + global_wording['volume_unit'] + '</span></div>';

    out[++o] = '</div>';
    out[++o] = '</div>'; // End central-content
    return out.join('');
}


/**
 * explore_use_saved_filters.
 */
function explore_use_saved_filters() {
    var saved_explore_filters = (typeof localStorage !== 'undefined') ? localStorage.getItem("explore_filters") : null;
    if (saved_explore_filters != null) {
        explore_set_filters_by_url();
        $('.mosaique-content-filter .content .filter').each(function() {
            var $celui = $(this);
            var family = $celui.attr('data-family');
            var target = $celui.attr('data-target');
            if (in_array(target, explore_filters[family])) {
                $celui.addClass('active');
                $celui.parent().parent().find(".filter[data-target='" + $celui.attr('data-target') + "']").addClass('active');
            } else {
                $celui.removeClass('active');
                $celui.parent().parent().find(".filter[data-target='" + $celui.attr('data-target') + "']").removeClass('active');
                $celui.parent().parent().find(".filter[data-target='" + $celui.attr('data-target') + "']").removeClass('special-display-none');
            }
        });
        var nb_filters = $('.mosaique-content-filter .content .filter.active').length + $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').length;

        if (nb_filters > 0) {
            $('.content-explore-list').addClass('filtered');
        } else {
            $('.content-explore-list').removeClass('filtered');
        }
        var text = mosaique_get_sort_name(explore_filters['sort']);
        jQuery('.mosaique-header .content-right .content-sort-by').removeClass('opened');
        jQuery('.mosaique-header .content-right .content-sort-by .active-sort').text(text);
        jQuery('.mosaique-header .content-right .content-sort-by .link-sort').removeClass('active');
        jQuery('.mosaique-header .content-right .content-sort-by .link-sort[data-target="' + explore_filters['sort'] + '"]').addClass('active');
    }
}

/**
 * explore_sort_list_by_target
 * @param target
 * @param update_url
 */
function explore_sort_list_by_target(target, update_url) {
    if (typeof update_url === 'undefined') {
        update_url = true;
    }
    explore_all_are_loaded = false;
    explore_innovation_offset = 0;
    explore_reorder_list(target);
    jQuery('.mosaique-list-innovations').html('');
    hide_and_scroll();
    explore_load_more_list();
    if (update_url) {
        var url = explore_generate_url();
        opti_update_url(url, 'no_change');
    }
    explore_save_filters();
    init_tooltip();
}

/**
 * explore_reorder_list.
 *
 * @param target
 */
function explore_reorder_list(target) {
    switch (target) {
        case 'relevance':
            explore_filtered_innovation_collection = new DataCollection(explore_filtered_innovation_collection.query().order('sort_explore_nbm_relevance', true).values());
            break;
        case 'date':
            explore_filtered_innovation_collection = new DataCollection(explore_filtered_innovation_collection.query().order('sort_explore_date', true).values());
            break;
        case 'a-z':
            explore_filtered_innovation_collection = new DataCollection(explore_filtered_innovation_collection.query().order('proper__title', false).values());
            break;
        case 'volumes':
            explore_filtered_innovation_collection = new DataCollection(explore_filtered_innovation_collection.query().order('proper__total_volume', true).values());
            break;
        default:
            break;
    }
}

/**
 * explore_filter_list.
 * @param update_url
 */
function explore_filter_list(update_url) {
    if (typeof update_url === 'undefined') {
        update_url = true;
    }
    explore_all_are_loaded = false;
    explore_innovation_offset = 0;
    var initial_collection = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_initial_innovation_collection : explore_initial_innovation_collection;
    explore_filtered_innovation_collection = new DataCollection(initial_collection.query().values());
    var sort_target = jQuery('.mosaique-header .content-right .content-sort-by .link-sort.active').attr('data-target');
    var is_products = ($('#explore-products').is(':visible'));
    explore_reorder_list(sort_target);
    explore_filters = (is_products) ? explore_products_get_default_filters() : explore_new_business_models_get_default_filters();
    if ($('#mosaique-sticky-menu .content-other-filters .filter.active').length > 0 || $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').length > 0) {
        $('#mosaique-sticky-menu .content-other-filters .filter.active').each(function() {
            var $celui = $(this);
            var family = $celui.attr('data-family');
            var target = $celui.attr('data-target');
            if (family == 'category') {
                var cate = mosaique_get_category_for_key(target);
                explore_filters[family].push(cate);
                explore_filters['category_key'].push(target);
            } else if (in_array(family, ['current_stage', 'coop', 'title', 'brand'])) {
                explore_filters[family].push(parseInt(target));
            } else {
                explore_filters[family].push(target);
            }
        });
        $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').each(function() {
            var $celui = $(this);
            var family = $celui.attr('data-family');
            var target = $celui.attr('data-target');
            explore_filters[family].push(target);
        });

        if (explore_filters.hasOwnProperty('market')) {
            var filters_market = explore_get_true_market_keys_for_filter(explore_filters['market']);
            explore_filters['market'] = explore_generate_market_keys_without_region_doublons(explore_filters['market']);
        }
        if (explore_filters.hasOwnProperty('moc') && explore_filters['moc'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ moc__not_in: explore_filters['moc'] }).remove();
        }
        if (explore_filters.hasOwnProperty('coop') && explore_filters['coop'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ consumer_opportunity__not_in: explore_filters['coop'] }).remove();
        }
        if (explore_filters.hasOwnProperty('market') && explore_filters['market'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ markets_in_array__array_not_contains_any: filters_market }).remove();
        }
        if (explore_filters.hasOwnProperty('tag') && explore_filters['tag'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ tags_array__array_not_contains_any: explore_filters['tag'] }).remove();
        }
        if (explore_filters.hasOwnProperty('category') && explore_filters['category'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ category__not_in: explore_filters['category'] }).remove();
        }
        if (explore_filters.hasOwnProperty('brand') && explore_filters['brand'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ brand__id__not_in: explore_filters['brand'] }).remove();
        }
        if (explore_filters.hasOwnProperty('portfolio') && explore_filters['portfolio'].length > 0) {
            var remove_big_bets = (!in_array('big_bet', explore_filters['portfolio']));
            if (!in_array('craft', explore_filters['portfolio'])) { // craft
                if (remove_big_bets) {
                    explore_filtered_innovation_collection.query().filter({ business_drivers: "Craft" }).remove();
                } else {
                    explore_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "Craft"
                    }).remove();
                }
            }
            if (!in_array('rtd_rts', explore_filters['portfolio'])) { // rtd_rts
                if (remove_big_bets) {
                    explore_filtered_innovation_collection.query().filter({ business_drivers: "RTD / RTS" }).remove();
                } else {
                    explore_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "RTD / RTS"
                    }).remove();
                }
            }
            if (!in_array('specialty', explore_filters['portfolio'])) { // specialty
                if (remove_big_bets) {
                    explore_filtered_innovation_collection.query().filter({ business_drivers: "Specialty" }).remove();
                } else {
                    explore_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "Specialty"
                    }).remove();
                }
            }
            if (!in_array('no_low_alcohol', explore_filters['portfolio'])) { // no_low_alcohol
                if (remove_big_bets) {
                    explore_filtered_innovation_collection.query().filter({ business_drivers: "No & Low Alcohol" }).remove();
                } else {
                    explore_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "No & Low Alcohol"
                    }).remove();
                }
            }
            if (!in_array('none', explore_filters['portfolio'])) { // None
                if (remove_big_bets) {
                    explore_filtered_innovation_collection.query().filter({ business_drivers: "None" }).remove();
                    explore_filtered_innovation_collection.query().filter({ business_drivers: null }).remove();
                } else {
                    explore_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "None"
                    }).remove();
                    explore_filtered_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: null
                    }).remove();
                }
            }
            if (!in_array('big_bet', explore_filters['portfolio'])) { // Big Bet
                explore_filtered_innovation_collection.query().filter({
                    big_bet: 'BB',
                    business_drivers__not_in: ["Craft", "RTD / RTS", "No & Low Alcohol", "Specialty"]
                }).remove();
            }
        }

        //investment model 
        if (explore_filters.hasOwnProperty('investment_model') && explore_filters['investment_model'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ investment_model__not_in: explore_filters['investment_model'] }).remove();
        }
        //new business opportunity
        if (explore_filters.hasOwnProperty('new_business_opportunity') && explore_filters['new_business_opportunity'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ new_business_opportunity__not_in: explore_filters['new_business_opportunity'] }).remove();
        }
        //growth strategy 


        if (explore_filters.hasOwnProperty('nbm_status') && explore_filters['nbm_status'].length > 0) {
            var remove_nba = (!in_array('New Business Acceleration', explore_filters['nbm_status']));
            if (!in_array('New Business Acceleration', explore_filters['nbm_status'])) {
                explore_filtered_innovation_collection.query().filter({ growth_strategy: 'New Business Acceleration' }).remove();
            }
            if (!in_array('Early Stage', explore_filters['nbm_status'])) {
                if (remove_nba) {
                    explore_filtered_innovation_collection.query().filter({ current_stage_id__in: [1, 2, 3] }).remove();
                } else {
                    explore_filtered_innovation_collection.query().filter({
                        current_stage_id__in: [1, 2, 3],
                        growth_strategy__not: 'New Business Acceleration'
                    }).remove();
                }
            }
            if (!in_array('In Market', explore_filters['nbm_status'])) {
                if (remove_nba) {
                    explore_filtered_innovation_collection.query().filter({ current_stage_id__in: [4, 5] }).remove();
                } else {
                    explore_filtered_innovation_collection.query().filter({
                        current_stage_id__in: [4, 5],
                        growth_strategy__not: 'New Business Acceleration'
                    }).remove();
                }
            }
        }

        if (explore_filters.hasOwnProperty('current_stage') && explore_filters['current_stage'].length > 0) {
            explore_filtered_innovation_collection.query().filter({ current_stage_id__not_in: explore_filters['current_stage'] }).remove();
        }
    }
    $('.mosaique-list-innovations').html('');
    hide_and_scroll();
    explore_init_filters();
    explore_load_more_list();
    if (update_url) {
        var url = explore_generate_url();
        opti_update_url(url, 'no_change');
    }

    init_tooltip();
    mosaique_filters_to_top();
    explore_update_consolidation();
}


/**
 * explore_init_filters
 */
function explore_init_filters() {
    $('.mosaique-content-filter').each(function() {
        var $celui = $(this);
        var family = $celui.attr('data-family');
        var initial_collection = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_initial_innovation_collection : explore_initial_innovation_collection;
        var an_innovation_collection = new DataCollection(initial_collection.query().values());

        if (explore_filters['tag'].length > 0) {
            an_innovation_collection.query().filter({ tags_array__array_not_contains_any: explore_filters['tag'] }).remove();
        }
        if (explore_filters.hasOwnProperty('moc') && family != 'moc' && explore_filters['moc'].length > 0) {
            an_innovation_collection.query().filter({ moc__not_in: explore_filters['moc'] }).remove();
        }
        if (explore_filters.hasOwnProperty('coop') && family != 'coop' && explore_filters['coop'].length > 0) {
            an_innovation_collection.query().filter({ consumer_opportunity__not_in: explore_filters['coop'] }).remove();
        }
        if (explore_filters.hasOwnProperty('market') && family != 'market' && explore_filters['market'].length > 0) {
            an_innovation_collection.query().filter({ markets_in_array__array_not_contains_any: explore_get_true_market_keys_for_filter(explore_filters['market']) }).remove();
        }
        if (explore_filters.hasOwnProperty('category') && family != 'category' && explore_filters['category'].length > 0) {
            an_innovation_collection.query().filter({ category__not_in: explore_filters['category'] }).remove();
        }
        if (explore_filters.hasOwnProperty('brand') && family != 'brand' && explore_filters['brand'].length > 0) {
            an_innovation_collection.query().filter({ brand__id__not_in: explore_filters['brand'] }).remove();
        }
        if (explore_filters.hasOwnProperty('portfolio') && family != 'portfolio' && explore_filters['portfolio'].length > 0) {
            var remove_big_bets = (!in_array('big_bet', explore_filters['portfolio']));
            if (!in_array('craft', explore_filters['portfolio'])) { // craft
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "Craft" }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "Craft" }).remove();
                }
            }
            if (!in_array('rtd_rts', explore_filters['portfolio'])) { // rtd_rts
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "RTD / RTS" }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "RTD / RTS" }).remove();
                }
            }
            if (!in_array('specialty', explore_filters['portfolio'])) { // specialty
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "Specialty" }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "Specialty" }).remove();
                }
            }
            if (!in_array('no_low_alcohol', explore_filters['portfolio'])) { // no_low_alcohol
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "No & Low Alcohol" }).remove();
                } else {
                    an_innovation_collection.query().filter({
                        big_bet: '',
                        business_drivers: "No & Low Alcohol"
                    }).remove();
                }
            }
            if (!in_array('none', explore_filters['portfolio'])) { // None
                if (remove_big_bets) {
                    an_innovation_collection.query().filter({ business_drivers: "None" }).remove();
                    an_innovation_collection.query().filter({ business_drivers: null }).remove();
                } else {
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: "None" }).remove();
                    an_innovation_collection.query().filter({ big_bet: '', business_drivers: null }).remove();
                }
            }
            if (!in_array('big_bet', explore_filters['portfolio'])) { // Big Bet
                an_innovation_collection.query().filter({
                    big_bet: 'BB',
                    business_drivers__not_in: ["Craft", "RTD / RTS", "No & Low Alcohol", "Specialty"]
                }).remove();
            }
        }


        if (family != 'current_stage' && explore_filters['current_stage'].length > 0) {
            an_innovation_collection.query().filter({ current_stage_id__not_in: explore_filters['current_stage'] }).remove();
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
 * explore_save_filters
 */
function explore_save_filters() {
    if (typeof localStorage !== 'undefined') {
        explore_filters['sort'] = jQuery('.mosaique-header .content-right .content-sort-by .link-sort.active').attr('data-target');
        localStorage.setItem("explore_filters", JSON.stringify(explore_filters));
    }
}

/**
 * explore_reset_filters
 */
function explore_reset_filters() {
    explore_filters = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_get_default_filters() : explore_products_get_default_filters();
    if (typeof localStorage !== 'undefined') {
        localStorage.removeItem('explore_filters');
    }
}

/**
 * explore_generate_url.
 * @returns {string}
 */
function explore_generate_url() {
    var url = ($('#explore-products').is(':visible')) ? "/content/explore" : '/content/explore/tab/new_business_models';
    var is_first = true;
    var $targets = $('#mosaique-sticky-menu .content-other-filters .filter.active');
    if ($targets.length > 0 || $('#mosaique-sticky-menu .content-active-tag-filters .filter.active').length > 0) {
        if (explore_filters.hasOwnProperty('coop') && explore_filters['coop'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[coop]=" + explore_filters['coop'].join(',');
            is_first = false;
        }
        if (explore_filters.hasOwnProperty('moc') && explore_filters['moc'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[moc]=" + explore_filters['moc'].join(',');
            is_first = false;
        }
        if (explore_filters.hasOwnProperty('market') && explore_filters['market'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[market]=" + explore_filters['market'].join(',');
            is_first = false;
        }
        if (explore_filters.hasOwnProperty('category') && explore_filters['category'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[category]=" + explore_filters['category'].join(',');
            is_first = false;
        }
        if (explore_filters.hasOwnProperty('brand') && explore_filters['brand'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[brand]=" + explore_filters['brand'].join(',');
            is_first = false;
        }

        //investment model
        if (explore_filters.hasOwnProperty('investment_model') && explore_filters['investment_model'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[investment_model]=" + explore_filters['investment_model'].join(',');
            is_first = false;
        }

        // nbm_status
        if (explore_filters.hasOwnProperty('nbm_status') && explore_filters['nbm_status'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[nbm_status]=" + explore_filters['nbm_status'].join(',');
            is_first = false;
        }

        //new_business_opportunity
        if (explore_filters.hasOwnProperty('new_business_opportunity') && explore_filters['new_business_opportunity'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[new_business_opportunity]=" + explore_filters['new_business_opportunity'].join(',');
            is_first = false;
        }
        if (explore_filters.hasOwnProperty('portfolio') && explore_filters['portfolio'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[portfolio]=" + explore_filters['portfolio'].join(',');
            is_first = false;
        }
        if (explore_filters.hasOwnProperty('current_stage') && explore_filters['current_stage'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[current_stage]=" + explore_filters['current_stage'].join(',');
            is_first = false;
        }
        if (explore_filters.hasOwnProperty('tag') && explore_filters['tag'].length > 0) {
            url += (is_first) ? "?" : "&";
            url += "filter[tag]=" + explore_filters['tag'].join(',');
            is_first = false;
        }
    }
    var is_new_business_models = $('#explore-new_business_models').is(':visible');
    var sort_target = jQuery('.mosaique-header .content-right .content-sort-by .link-sort.active').attr('data-target');
    if ((is_new_business_models && sort_target != 'relevance') || (!is_new_business_models && sort_target != 'date')) {
        url += (is_first) ? "?" : "&";
        url += "sort=" + sort_target;
    }
    return url;
}

/**
 * explore_resetStatistics
 */
function explore_resetStatistics() {
    explore_statistics = null;
    explore_map = null;

    var all_countries = get_all_countries();
    explore_map_data = {};
    explore_map_data_empty = {};
    for (var i = 0; i < all_countries.length; i++) {
        var a_country = all_countries[i];
        explore_map_data[a_country['country_code']] = 0;
        explore_map_data_empty[a_country['country_code']] = 0;
    }
    // unused countries
    explore_map_data['TF'] = 0;
    explore_map_data['GL'] = 0;
    explore_map_data['_1'] = 0;
    explore_map_data['_2'] = 0;

    explore_map_data_empty['TF'] = 0;
    explore_map_data_empty['GL'] = 0;
    explore_map_data_empty['_1'] = 0;
    explore_map_data_empty['_2'] = 0;
    explore_map_max = 0;
}

explore_resetStatistics();

/**
 * explore_get_initial_explore_map_data.
 *
 * @returns {{}}
 */
function explore_get_initial_explore_map_data() {
    var all_countries = get_all_countries();
    var explore_initial_map_data = {};
    for (var i = 0; i < all_countries.length; i++) {
        var a_country = all_countries[i];
        explore_initial_map_data[a_country['country_code']] = 0;
    }
    // unused countries
    explore_initial_map_data['TF'] = 0;
    explore_initial_map_data['GL'] = 0;
    explore_initial_map_data['_1'] = 0;
    explore_initial_map_data['_2'] = 0;

    var initial_collection = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_initial_innovation_collection : explore_initial_innovation_collection;
    initial_collection.query().each(function(product, index) {
        var markets_in_array = product['markets_in_array'];
        for (var i = 0; i < markets_in_array.length; i++) {
            var market = markets_in_array[i];
            if (explore_initial_map_data.hasOwnProperty(market)) {
                explore_initial_map_data[market] += 1;
            } else {
                explore_initial_map_data[market] = 1;
            }
            if (explore_initial_map_data[market] > explore_map_max) {
                explore_map_max = explore_initial_map_data[market];
            }
        }
    });
    return explore_initial_map_data;
}

/**
 * explore_update_statistics
 */
function explore_update_statistics() {
    explore_resetStatistics();
    if ($('#explore-products').is(':visible')) {
        explore_statistics = {
            'nb_innovations': explore_filtered_innovation_collection.query().count(),
            'volume': 0
        };
        explore_filtered_innovation_collection.query().each(function(product, index) {
            explore_statistics['volume'] += Math.round(product['financial']['explore_volume']['latest']['calc']);
            var markets_in_array = product['markets_in_array'];
            var has_usa_added = false;
            for (var i = 0; i < markets_in_array.length; i++) {
                var market = markets_in_array[i];
                if (market.indexOf('US-') !== -1) {
                    market = 'US';
                }
                if (market != 'US' || (market == 'US' && !has_usa_added)) {
                    if (explore_map_data.hasOwnProperty(market)) {
                        explore_map_data[market] += 1;
                    } else {
                        explore_map_data[market] = 1;
                    }
                    if (explore_map_data[market] > explore_map_max) {
                        explore_map_max = explore_map_data[market];
                    }
                    if (market == 'US') {
                        has_usa_added = true;
                    }
                }
            }
        });
    } else if ($('#explore-new_business_models').is(':visible')) {
        // TODO
    }
}

/**
 * explore_update_consolidation
 */
function explore_update_consolidation() {
    explore_update_statistics();
    if ($('#explore-products').is(':visible')) {
        var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
        $('.explore-list-map').addClass('updating');
        // NB Innovations
        var $span_nb_innovations = $('.explore-list-consolidation-nb-projects');
        $span_nb_innovations.text(explore_statistics.nb_innovations);
        $span_nb_innovations.attr('data-value', explore_statistics.nb_innovations);
        if (!$('html').hasClass('ie')) {
            $span_nb_innovations.animateNumber({
                number: explore_statistics.nb_innovations,
                numberStep: comma_separator_number_step
            }, 800);
        } else {
            $span_nb_innovations.html(explore_statistics.nb_innovations);
        }

        // Volume
        var $span_volume = $('.explore-list-consolidation-nb-volume');
        $span_volume.text(explore_statistics.volume);
        $span_volume.attr('data-value', explore_statistics.volume);
        if (!$('html').hasClass('ie')) {
            $span_volume.animateNumber({
                number: explore_statistics.volume,
                numberStep: comma_separator_number_step
            }, 800);
        } else {
            $span_volume.html(explore_statistics.volume);
        }

        explore_map = $('#explore-list-consolidation-map').vectorMap('get', 'mapObject');
        explore_map.series.regions[0].params.min = 0;
        explore_map.series.regions[0].params.max = explore_map_max;
        explore_map.clearSelectedRegions();
        if (explore_filters['market'].length > 0) {
            var selected_regions = explore_get_true_market_keys_for_filter(explore_filters['market']);
            explore_map.setSelectedRegions(selected_regions);
            explore_map.series.regions[0].setValues(explore_map_data_empty);
        } else {
            explore_map.series.regions[0].setValues(explore_map_data);
        }
        setTimeout(function() {
            $('.explore-list-map').removeClass('updating');
        }, 850);
    } else if ($('#explore-new_business_models').is(':visible')) {
        // TODO
    }
}

/**
 * explore_init_consolidation
 */
function explore_init_consolidation() {
    if ($('#explore-products').is(':visible')) {
        // I put fake data to initialize coloring scale
        var explore_initial_map_data = explore_get_initial_explore_map_data();
        $('.explore-list-map').addClass('updating');
        $('#explore-list-consolidation-map').vectorMap({
            map: 'world_mill_en',
            backgroundColor: "transparent",
            regionsSelectable: false,
            zoomOnScroll: false,
            panOnDrag: true,
            focusOn: {
                x: 0.8,
                y: 0.8,
                scale: 1,
                animate: true
            },
            regionStyle: {
                initial: {
                    'fill': '#435A7A',
                    'fill-opacity': 1
                },
                selected: {
                    'fill': '#fff',
                    'fill-opacity': 1,
                    'cursor': 'pointer'
                },
                hover: {
                    'fill': '#fff',
                    'fill-opacity': 1,
                    'cursor': 'pointer'
                }
            },
            series: {
                regions: [{
                    values: explore_initial_map_data,
                    scale: ['#435A7A', '#CED9EB'],
                    normalizeFunction: 'polynomial', // OR LINEAR : linear
                    min: 0,
                    max: explore_map_max
                }]
            },
            onRegionTipShow: function(e, el, code) {
                var nb = (explore_map_data[code]) ? explore_map_data[code] : 0;
                var wording = (nb <= 1) ? 'Project in market' : 'Projects in market';
                var click_to_filter = (nb > 0) ? '<div class="font-montserrat-500 font-size-14 line-height-1 margin-top-5 color-84d8e5">Click to filter</div>' : '';
                el.html('<div class="title font-montserrat-700">' + el.html() + '</div><div class="subtitle">' + nb + ' ' + wording + '</div>' + click_to_filter);
            },
            onRegionClick: function(e, code) {
                explore_action_on_click_map(code);
                e.preventDefault();
                return null;
            },
            onRegionOver: function(e, code) {
                var nb = (explore_map_data[code]) ? explore_map_data[code] : 0;
                if (nb === 0) {
                    e.preventDefault();
                }
            }
        });
        setTimeout(function() {
            $('.explore-list-map').removeClass('updating');
        }, 850);
    } else if ($('#explore-new_business_models').is(':visible')) {
        // TODO
    }
}

/**
 * explore_action_on_click_map
 * @param code
 */
function explore_action_on_click_map(code) {
    var changes = false;
    var $active_filter = $("#mosaique-sticky-menu .content-active-filters .filter.active[data-family='market'][data-target='" + code + "']");
    var $target_filter = $("#mosaique-sticky-menu .content-other-filters .filter[data-family='market'][data-target='" + code + "']");
    var is_active = ($active_filter.length > 0);
    if (is_active) {
        $active_filter.remove();
        $target_filter.removeClass('active special-display-none');
        changes = true;
    } else if (explore_map_data[code] > 0) {
        $target_filter.addClass('active');
        changes = true;
    }
    if (changes) {
        mosaique_update_state_filtered();
        explore_filter_list();
        explore_save_filters();
        mosaique_optimize_content_all_filters_height();
    }
}


/**
 * explore_get_all_markets_for_list
 * @returns {{3: Array, 1: Array, 2: Array, 4: Array, 5: Array, 7: Array, 6: Array}}
 */
function explore_get_all_markets_for_list() {
    var markets_codes = [];
    var ret = [];
    var initial_collection = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_initial_innovation_collection : explore_initial_innovation_collection;
    initial_collection.query().each(function(row, index) {
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
 * explore_get_all_markets_keys_for_region_key
 * @param region_key
 * @returns {Array}
 */
function explore_get_all_markets_keys_for_region_key(region_key) {
    var markets_codes = [];
    if (region_key.charAt(0) === 'R')
        region_key = region_key.slice(1);
    region_key = parseInt(region_key);
    var all_markets = explore_get_all_markets_for_list();
    if (all_markets.hasOwnProperty(region_key)) {
        for (var i = 0; i < all_markets[region_key].length; i++) {
            markets_codes.push(all_markets[region_key][i]['country_code']);
        }
    }
    return markets_codes;
}

/**
 * explore_get_true_market_keys_for_filter
 * @param markets_keys
 * @returns {Array}
 */
function explore_get_true_market_keys_for_filter(markets_keys) {
    // gestion des régions
    var filter_array = [];
    for (var i = 0; i < markets_keys.length; i++) {
        var target = markets_keys[i];
        if (in_array(target, market_region_keys)) {
            var keys_for_region = explore_get_all_markets_keys_for_region_key(target);
            filter_array = filter_array.concat(keys_for_region);
        } else {
            filter_array.push(target);
        }
    }
    return filter_array;
}


/**
 * explore_generate_market_keys_without_region_doublons
 * @param markets_keys
 * @returns {Array.<*>}
 */
function explore_generate_market_keys_without_region_doublons(markets_keys) {
    var filter_array = [];
    var to_remove = [];
    for (var i = 0; i < markets_keys.length; i++) {
        var target = markets_keys[i];
        if (in_array(target, market_region_keys)) {
            var keys_for_region = explore_get_all_markets_keys_for_region_key(target);
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
 * explore_get_all_brands_for_list
 * @returns {{0: Array, 1: Array, 2: Array, 3: Array}}
 */
function explore_get_all_brands_for_list() {
    var brand_ids = [];
    var ret = [];
    var initial_collection = ($('#explore-new_business_models').is(':visible')) ? explore_new_business_models_initial_innovation_collection : explore_initial_innovation_collection;
    initial_collection.query().each(function(row, index) {
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