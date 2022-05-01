var monitor_old_getters = null;
var monitor_data_stage = null;
var monitor_data_stage_other = null;
var monitor_statistics = null;
var monitor_stats_loaded = false;
var monitor_stageChart = null;
var monitor_stageChartOther = null;
var monitor_innovation_list_limit = default_innovation_list_limit;
var monitor_innovation_list_offset = 0;
var monitor_innovation_list_filters = null;
var monitor_innovation_list_order = null;
var monitor_data_filters = null;
var monitor_filteredinnovationsDC = new DataCollection();
var monitor_filteredOuterInnovationsDC = new DataCollection();
var monitor_color_empty = "rgba(255,255,255,0.1)"; //"#79879E";
var monitor_border_color = "#18346A";
var request_dashboard_init = null;
var request_dashboard_detail = null;

/**
 * Reset les statistiques de la consolidation
 */
function monitor_resetStatistics() {
    monitor_statistics = null;
    monitor_data_stage = {
        "labels": ["discover", "ideate", "experiment", "incubate", "scale_up", "empty"],
        "datasets": [{
            "data": [0, 0, 0, 0, 0, 100],
            "borderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
            "hoverBorderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
            "backgroundColor": ["#4f6e99", "#fab700", "#45ab34", "#e8485c", "#80a7d0", monitor_color_empty],
            "hoverBackgroundColor": ["#4f6e99", "#fab700", "#45ab34", "#e8485c", "#80a7d0", monitor_color_empty]
        }]
    };
    monitor_data_stage_other = {
        "labels": ["permanent_range", "discontinued", "frozen", "empty"],
        "datasets": [{
            "data": [0, 0, 0, 100],
            "borderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
            "hoverBorderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
            "backgroundColor": ["#FFFFFF", "#BCC3CE", "#99CDD8", monitor_color_empty],
            "hoverBackgroundColor": ["#FFFFFF", "#BCC3CE", "#99CDD8", monitor_color_empty]
        }]
    };
}

monitor_resetStatistics();

/**
 * gotToMonitorList
 * @return {boolean}
 */
function gotToMonitorList(link, tab, type) {
    before_opening_page();
    link = link || "/content/monitor";
    tab = tab || 'performance-le';
    type = type || null;
    var user_perimeter = $('#main').attr('data-perimeter');
    var getVars = getUrlVars();
    if (user_perimeter != '-1' && !getVars['filter[perimeter]']) {
        link += '?filter[perimeter]=' + user_perimeter;
    }
    if(tab == 'dashboard') {
        $('html').addClass('background-color-f2f2f2 height-100');
    }
    $('#main').addClass('loading');
    opti_update_url(link, 'list-monitor');
    update_menu_active('menu-li-monitor');
    changePageTitle('Monitor | ' + global_wording['title']);
    monitor_old_getters = null;
    $('#pri-main-container-without-header').html(monitor_get_html_template(tab));
    monitor_init(tab);
    update_main_classes(main_classes);
    window.scrollTo(0, 0);
    if (type) {
        dashboard_detail_init(type);
    }
}

function optListMonitor() {
    $('.full-content-monitor .other-info-consolidation.volume').attr('data-devise', global_wording['volume_unit']);
    /* LIST INNOVATIONS
     ----------------------------------------------------------------------------------------*/

    monitor_initialize_perimeter_selector();

    monitor_loadCharts();
    monitor_load_toast();


    if (monitor_data_filters) {
        monitor_load_list();
        monitor_load_entete();
        $('#main').removeClass('loading');
        monitor_optimization_init_list();
    }


    $(".pri-select").select2({
        minimumResultsForSearch: -1,
        width: 'resolve'
    });

    $(".pri-select-pagination").select2({
        width: '60px',
        tags: true,
        createTag: function(params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            }
        }
    });

    $(".pri-select-pagination").change(function() {
        monitor_resetStatistics();
        monitor_updatePage();
    });

    optimization_update();

    $(document).on("click", ".list-monitor .pri-pagination ul li a", function(e) {
        e.stopImmediatePropagation();
        var $parent = $(this).parent();
        var $grand_parent = $parent.parent().parent();
        var done = false;
        if ($parent.hasClass('state-active') || $parent.hasClass('disabled')) {
            return false;
        }
        if ($parent.hasClass('prev') && !$parent.hasClass('disabled')) {
            // prev
            done = true;
            var nb = parseInt($grand_parent.find('ul li.state-active a').text());
            nb--;
            $grand_parent.find('ul li').removeClass('state-active');
            $grand_parent.find('ul li.pagination-' + nb).addClass('state-active');
        } else if ($parent.hasClass('next') && !$parent.hasClass('disabled')) {
            // next
            done = true;
            var nb = parseInt($('.pri-pagination ul li.state-active a').text());
            nb++;
            $grand_parent.find('ul li').removeClass('state-active');
            $grand_parent.find('ul li.pagination-' + nb).addClass('state-active');
        } else if (!$parent.hasClass('prev') && !$parent.hasClass('next') && !$parent.hasClass('state-active')) {
            done = true;
            $grand_parent.find('ul li').removeClass('state-active');
            $parent.addClass('state-active');
        }
        // check disabled state
        if (done) {
            $grand_parent.find('ul li.prev').removeClass('disabled');
            $grand_parent.find('ul li.next').removeClass('disabled');
            if ($grand_parent.find('ul li.state-active').hasClass('first')) {
                $grand_parent.find('ul li.prev').addClass('disabled');
            }
            if ($grand_parent.find('ul li.state-active').hasClass('last')) {
                $grand_parent.find('ul li.next').addClass('disabled');
            }
        }
        var classe = 'list-monitor';
        var url = $(this).attr('href');
        opti_update_url(url, classe);
        monitor_load_list();
        return false;
    });

    init_tooltip();

    $('.filter-classification').click(function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
        }
        monitor_resetStatistics();
        monitor_updatePage();
    });

    $('.filter-group-stage').click(function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
        }
        monitor_resetStatistics();
        monitor_updatePage();
    });


    monitor_set_activated_top_filters();
}

/**
 * monitor_init.
 *
 * @param tab
 */
function monitor_init(tab) {
    tab = (typeof tab != 'undefined') ? tab : 'performance-le';
    $('.pri-tab').tabs({
        activate: function(event, ui) {
            if ($(event.currentTarget).length > 0) {
                var target = $(event.currentTarget).attr('href').replace('#', '');
                monitor_load_tab(target);
            }
        }
    });

    $('.pri-tab-menu li a').click(function() {
        var target = $(this).attr('href').replace('#', '');
        // if we are on dashboard detail and we wanna go on dashboard
        if (target == "dashboard" && !$('.full-content-page-dashboard-detail').hasClass('display-none')) {
            monitor_load_tab(target);
        }
    });

    dashboard_init();
}

/**
 * monitor_load_tab.
 *
 * @param tab
 */
function monitor_load_tab(tab) {
    if (tab == 'dashboard') {
        opti_update_url("/content/dashboard", 'list-monitor');
        changePageTitle('Dashboard | ' + global_wording['title']);
        $('html').addClass('background-color-f2f2f2 height-100');
        $('.full-content-page-dashboard-detail').addClass('display-none');
        $('.full-content-page-dashboard').removeClass('display-none');
    } else if (tab == 'performance-le') {
        opti_update_url("/content/monitor", 'list-monitor');
        changePageTitle('Monitor | ' + global_wording['title']);
        $('html').removeClass('background-color-f2f2f2 height-100');

    }
    $('#main').removeClass('loading');
    window.scrollTo(0, 0);
}

/**
 * monitor_set_activated_top_filters
 */
function monitor_set_activated_top_filters() {
    var target_classification = getUrlVars()['filter[classification]'];
    if (target_classification) {
        target_classification = decodeURIComponent(target_classification);
        $('.filter-classification').removeClass('active');
        $('.filter-classification[data-target="' + target_classification + '"]').addClass('active');
    } else {
        $('.filter-classification').addClass('active');
    }

    var target_group_stages = getUrlVars()['filter[group_stage]'];
    $('.filter-group-stage').removeClass('active');
    if (target_group_stages) {
        target_group_stages = target_group_stages.split(',');
        if (target_group_stages.length == 0 || target_group_stages.length === 2 && in_array('early_stage', target_group_stages) && in_array('in_market', target_group_stages)) {
            $('.filter-group-stage.default').addClass('active');
        } else {
            for (var i = 0; i < target_group_stages.length; i++) {
                $('.filter-group-stage[data-target="' + target_group_stages[i] + '"]').addClass('active');
            }
        }
    } else {
        $('.filter-group-stage.default').addClass('active');
    }
}

/**
 * monitor_get_classification_adders
 */
function monitor_get_classification_adders() {
    if ($('.filter-classification.active').length === 2 || $('.filter-classification.active').length == 0) {
        // si tout est activé ou rien n'est activé
        return '';
    }
    return '&filter[classification]=' + $('.filter-classification.active').attr('data-target');
}

/**
 * monitor_get_group_stage_adders
 * @returns {string}
 */
function monitor_get_group_stage_adders() {
    var group_stages = [];
    $('.filter-group-stage.active').each(function () {
        group_stages.push($(this).attr('data-target'));
    });
    if (group_stages.length === 2 && in_array('early_stage', group_stages) && in_array('in_market', group_stages)) {
        return '';
    }
    return '&filter[group_stage]=' + group_stages.join(',');
}

function monitor_get_perimeter_adders() {
    var perimeters = other_datas['perimeter_json'];
    if (!perimeters) {
        return '';
    }
    var adder = '';
    var perimeter = $('#monitor-select-perimeter').val();
    if (perimeter != -1) {
        adder += '&filter[perimeter]=' + perimeter;
    }
    var strategic_international_brand = $('#monitor-select-strategic-international-brand').val();
    if (strategic_international_brand != -1) {
        adder += '&filter[strategic_international]=' + strategic_international_brand;
    }
    return adder;
}

function monitor_updatePage(classe, return_url) {
    if (typeof classe === 'undefined') {
        classe = 'list-monitor';
    }
    if (typeof return_url == 'undefined') {
        return_url = false;
    }
    var brand_filter = $('.filter-brand').contentFilter('getFilters');
    var title_filter = $('.filter-title').contentFilter('getFilters');
    var years_since_launch_filter = $('.filter-years_since_launch').contentFilter('getFilters');
    var entity_filter = $('.filter-entity').contentFilter('getFilters');
    var growth_model_filter = $('.filter-growth_model').contentFilter('getFilters');
    var latest_a_p_filter = $('.filter-latest_a_p').contentFilter('getFilters');
    var latest_net_sales_filter = $('.filter-latest_net_sales').contentFilter('getFilters');
    var latest_caap_filter = $('.filter-latest_caap').contentFilter('getFilters');
    var latest_volume_filter = $('.filter-latest_volume').contentFilter('getFilters');
    var portfolio_filter = $('.filter-portfolio').contentFilter('getFilters');
    var toAdd = portfolio_filter.full_get + brand_filter.full_get + title_filter.full_get + years_since_launch_filter.full_get + entity_filter.full_get + growth_model_filter.full_get + latest_net_sales_filter.full_get + latest_a_p_filter.full_get + latest_volume_filter.full_get + latest_caap_filter.full_get;
    toAdd += monitor_get_classification_adders();
    toAdd += monitor_get_group_stage_adders();
    toAdd += monitor_get_perimeter_adders();
    var pathname = window.location.pathname;
    var val = $(".pri-select-pagination").val();
    var add = (pathname.indexOf('?') > -1) ? '&limit=' + val : '?limit=' + val;
    if (val == default_innovation_list_limit && toAdd == '') {
        add = '';
    }
    var url = pathname + add + toAdd;
    if (return_url) {
        return url;
    } else {
        opti_update_url(url, classe);
        monitor_load_list();
    }
}

/**
 * Monitor optimization init list
 */
function monitor_optimization_init_list() {
    monitor_update_data_filters_by_url();

    // NAME
    if (monitor_data_filters['filter'].hasOwnProperty('title')) {
        var titles = monitor_data_filters['filter']['title'];
        var order_title = (monitor_data_filters['order']['title']) ? monitor_data_filters['order']['title'] : null;
        $('.filter-title').contentFilter({
            title: 'Select Names',
            identifier: 'title',
            id: 'filter-title',
            data: titles,
            order: order_title,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }

    // BRAND
    if (monitor_data_filters['filter'].hasOwnProperty('brand')) {
        var brands = monitor_data_filters['filter']['brand'];
        var order_brand = (monitor_data_filters['order']['brand']) ? monitor_data_filters['order']['brand'] : null;
        $('.filter-brand').contentFilter({
            title: 'Select Brands',
            identifier: 'brand',
            id: 'filter-brand',
            data: brands,
            order: order_brand,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }

    // ENTITY
    if (monitor_data_filters['filter'].hasOwnProperty('entity')) {
        var entities = monitor_data_filters['filter']['entity'];
        var order_entity = (monitor_data_filters['order']['entity']) ? monitor_data_filters['order']['entity'] : null;
        $('.filter-entity').contentFilter({
            title: 'Select Entities',
            identifier: 'entity',
            id: 'filter-entity',
            data: entities,
            order: order_entity,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }

    // STARTED
    if (monitor_data_filters['filter'].hasOwnProperty('years_since_launch')) {
        var years_since_launch = monitor_data_filters['filter']['years_since_launch'];
        var order_years_since_launch = (monitor_data_filters['order']['years_since_launch']) ? monitor_data_filters['order']['years_since_launch'] : null;
        $('.filter-years_since_launch').contentFilter({
            title: 'Select years since launch',
            identifier: 'years_since_launch',
            id: 'filter-years_since_launch',
            data: years_since_launch,
            order: order_years_since_launch,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }

    // growth_model
    if (monitor_data_filters['filter'].hasOwnProperty('growth_model')) {
        var growth_model = monitor_data_filters['filter']['growth_model'];
        var order_growth_model = (monitor_data_filters['order']['growth_model']) ? monitor_data_filters['order']['growth_model'] : null;
        $('.filter-growth_model').contentFilter({
            title: 'Select Growth Model',
            identifier: 'growth_model',
            id: 'filter-growth_model',
            data: growth_model,
            order: order_growth_model,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }


    // LATEST NET SALES
    if (monitor_data_filters['filter'].hasOwnProperty('latest_net_sales')) {
        var latest_net_sales = monitor_data_filters['filter']['latest_net_sales'];
        var order_latest_net_sales = (monitor_data_filters['order']['latest_net_sales']) ? monitor_data_filters['order']['latest_net_sales'] : null;
        $('.filter-latest_net_sales').contentFilter({
            title: 'Select ' + get_libelle_current_le() + ' Net Sales',
            identifier: 'latest_net_sales',
            id: 'filter-latest_net_sales',
            data: latest_net_sales,
            order: order_latest_net_sales,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }

    // Latest Total A-P
    if (monitor_data_filters['filter'].hasOwnProperty('latest_a_p')) {
        var latest_a_p = monitor_data_filters['filter']['latest_a_p'];
        var order_latest_a_p = (monitor_data_filters['order']['latest_a_p']) ? monitor_data_filters['order']['latest_a_p'] : null;
        $('.filter-latest_a_p').contentFilter({
            title: 'Select ' + get_libelle_current_le() + ' Total A&P',
            identifier: 'latest_a_p',
            id: 'filter-latest_a_p',
            data: latest_a_p,
            order: order_latest_a_p,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }

    // Latest CAAP
    if (monitor_data_filters['filter'].hasOwnProperty('latest_caap')) {
        var latest_caap = monitor_data_filters['filter']['latest_caap'];
        var order_latest_caap = (monitor_data_filters['order']['latest_caap']) ? monitor_data_filters['order']['latest_caap'] : null;
        $('.filter-latest_caap').contentFilter({
            title: 'Select ' + get_libelle_current_le() + ' CAAP',
            identifier: 'latest_caap',
            id: 'filter-latest_caap',
            data: latest_caap,
            order: order_latest_caap,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }

    // Latest Volume
    if (monitor_data_filters['filter'].hasOwnProperty('latest_volume')) {
        var latest_volume = monitor_data_filters['filter']['latest_volume'];
        var order_latest_volume = (monitor_data_filters['order']['latest_volume']) ? monitor_data_filters['order']['latest_volume'] : null;
        $('.filter-latest_volume').contentFilter({
            title: 'Select ' + get_libelle_current_le() + ' Volumes',
            identifier: 'latest_volume',
            id: 'filter-latest_volume',
            data: latest_volume,
            order: order_latest_volume,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }


    // PORTFOLIO
    if (monitor_data_filters['filter'].hasOwnProperty('portfolio')) {
        var portfolio = monitor_data_filters['filter']['portfolio'];
        var order_portfolio = (monitor_data_filters['order']['portfolio']) ? monitor_data_filters['order']['portfolio'] : null;
        $('.filter-portfolio').contentFilter({
            title: 'Select Portfolio',
            identifier: 'portfolio',
            id: 'filter-portfolio',
            data: portfolio,
            order: order_portfolio,
            disallow_order: true,
            on_after_each_time: false,
            onAfter: function(filters) {
                monitor_resetStatistics();
                monitor_reset_top_filters();
                monitor_updatePage();
            }
        });
    }


    $(".pri-select-pagintation").change(function() {
        monitor_updatePage('list-monitor');
    });

    monitor_update_filters_lists();
}

/**
 * Chargement de la liste
 * @param scroll_to_top
 */
function monitor_load_list(scroll_to_top) {
    scroll_to_top = typeof scroll_to_top !== 'undefined' ? scroll_to_top : false;
    $('#main').addClass('loading');
    $('.pri-table-list tbody').html('');
    monitor_update_list_elements();

    var out = [], o = -1;
    monitor_filteredinnovationsDC.query().limit(monitor_innovation_list_offset, monitor_innovation_list_limit).each(function (row, index) {
        out[++o] = monitor_get_table_line_by_innovation(row);
    });
    monitor_initialize_list_pagination();
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
    monitor_set_libelle_h1_for_list();
    var link = window.location.href;
    if (link.indexOf('inno/') === -1) {
        $('a.link-list-innovations').attr('href', link);
    }
    optimization_update();
    monitor_update_stats_consolidation();
}

/**
 * Met à jour la liste (après une modification des filtres, ou de page, etc..)
 */
function monitor_update_list_elements() {
    var limit = findGetParameter('limit');
    monitor_innovation_list_limit = (limit) ? parseInt(limit) : default_innovation_list_limit;
    var page = findGetParameter('page');
    monitor_innovation_list_offset = (page) ? ((parseInt(page) - 1) * monitor_innovation_list_limit) : 0;
    var getVars = getUrlVars();
    var filters = {};
    // Default value
    filters['group_stage'] = ['early_stage', 'in_market'];

    var in_market_date_added = false;
    for (var key in getVars) {
        if (key != 'page' && key != 'limit' && getVars.hasOwnProperty(key) && key.indexOf('filter[') !== -1) {
            var value = false;
            if (key.indexOf('in_market_date') !== -1) {
                if (!in_market_date_added) {
                    in_market_date_added = true;
                    var libelle = 'in_market_date';
                    value = {};
                    if (key.indexOf('from') !== -1) {
                        value['from'] = getVars[key];
                        var other_key = 'filter[in_market_date][to]';
                        if (getVars.hasOwnProperty(other_key)) {
                            value['to'] = getVars[other_key];
                        }
                    } else if (key.indexOf('to') !== -1) {
                        value['to'] = getVars[key];
                        var other_key = 'filter[in_market_date][from]';
                        if (getVars.hasOwnProperty(other_key)) {
                            value['from'] = getVars[other_key];
                        }
                    }
                }
            } else {
                var libelle = key.replace('filter[', '').replace(']', '');
                value = getVars[key];
                if (in_array(libelle, ['current_stage', 'entity', 'title', 'brand', 'perimeter', 'strategic_international'])) {
                    value = splitToInt(value, ',');
                } else if (libelle == 'classification') {
                    value = decodeURIComponent(value);
                } else if (libelle == 'group_stage') {
                    filters['group_stage'] = null;
                    value = value.split(',');
                } else {
                    value = value.split(',');
                }
            }
            if (value) {
                filters[libelle] = value;
            }
        }
    }
    monitor_innovation_list_filters = filters;
    var orders = {};
    for (var key in getVars) {
        if (key != 'page' && key != 'limit' && getVars.hasOwnProperty(key) && key.indexOf('order[') !== -1) {
            var libelle = key.replace('order[', '').replace(']', '');
            var value = getVars[key];
            if (value) {
                orders[libelle] = value;
            }
        }
    }
    monitor_innovation_list_order = orders;
    monitor_update_list_data_filters();
    monitor_update_list_data_orders();
    monitor_update_filteredInnovationsDC();
    monitor_update_filters_lists();
}

/**
 * Met à jour le tableau de data des filtres
 */
function monitor_update_list_data_filters() {
    for (var filter_key in monitor_data_filters['filter']) {
        if (monitor_data_filters['filter'].hasOwnProperty(filter_key)) {
            var change_array = monitor_data_filters['filter'][filter_key];
            for (var index = 0; index < change_array.length; index++) {
                var part = change_array[index];
                if (part !== undefined && part.hasOwnProperty('checked')) {
                    part['checked'] = 'checked=""';
                    change_array[index] = part;
                }
            }
            monitor_data_filters['filter'][filter_key] = change_array;
        }
    }
    for (var filter_key in monitor_innovation_list_filters) {
        if (monitor_innovation_list_filters.hasOwnProperty(filter_key) && monitor_data_filters['filter'].hasOwnProperty(filter_key)) {
            var change_array = monitor_data_filters['filter'][filter_key];
            if (filter_key == 'in_market_date') {
                var the_values = monitor_innovation_list_filters[filter_key];
                if (the_values.hasOwnProperty('from')) {
                    change_array['from'] = the_values['from'];
                }
                if (the_values.hasOwnProperty('to')) {
                    change_array['to'] = the_values['to'];
                }
            } else {
                for (var index = 0; index < change_array.length; index++) {
                    var part = change_array[index];
                    if (!($.inArray(part.id, monitor_innovation_list_filters[filter_key]) !== -1)) {
                        part['checked'] = '';
                    } else {
                        part['checked'] = 'checked=""';
                    }
                    change_array[index] = part;
                }
            }
            monitor_data_filters['filter'][filter_key] = change_array;
        }
    }
}

/**
 * Met à jour le tableau de data des orders
 */
function monitor_update_list_data_orders() {
    for (var order_key in monitor_data_filters['order']) {
        if (monitor_data_filters['order'].hasOwnProperty(order_key)) {
            monitor_data_filters['order'][order_key] = null;
        }
    }
    for (var order_key in monitor_innovation_list_order) {
        if (monitor_innovation_list_order.hasOwnProperty(order_key) && monitor_data_filters['order'].hasOwnProperty(order_key)) {
            monitor_data_filters['order'][order_key] = monitor_innovation_list_order[order_key];
        }
    }
}

/**
 * Met à jour la liste des innovations selon les filtres
 */
function monitor_update_filteredInnovationsDC() {
    monitor_filteredinnovationsDC = new DataCollection(manage_data_innovations);
    for (var filter_key in monitor_innovation_list_filters) {
        if (monitor_innovation_list_filters.hasOwnProperty(filter_key)) {
            var value = monitor_innovation_list_filters[filter_key];
            if (filter_key == 'title') {
                monitor_filteredinnovationsDC.query().filter({ id__not_in: value }).remove();
            } else if (filter_key == 'brand') {
                monitor_filteredinnovationsDC.query().filter({ brand__id__not_in: value }).remove();
            } else if (filter_key == 'entity') {
                monitor_filteredinnovationsDC.query().filter({ entity__id__not_in: value }).remove();
            } else if (filter_key == 'innovation_type') {
                monitor_filteredinnovationsDC.query().filter({ innovation_type__not_in: value }).remove();
            } else if (filter_key == 'classification') {
                monitor_filteredinnovationsDC.query().filter({ classification_type__not_in: value }).remove();
            } else if (filter_key == 'group_stage') {
                if (!in_array('early_stage', value)) { // No early stage
                    monitor_filteredinnovationsDC.query().filter({
                        current_stage_id__in: [1, 2, 3],
                        is_frozen: false
                    }).remove();
                }
                if (!in_array('in_market', value)) { // No in market
                    monitor_filteredinnovationsDC.query().filter({
                        current_stage_id__in: [4, 5],
                        is_frozen: false
                    }).remove();
                }
                if (!in_array('out_of_funnel', value)) { // No out of funnel
                    monitor_filteredinnovationsDC.query().filter({current_stage_id__in: [6, 7, 8]}).remove();
                    monitor_filteredinnovationsDC.query().filter({is_frozen: true}).remove();
                }
            } else if (filter_key == 'perimeter' && !in_array(-1, value)) {
                monitor_filteredinnovationsDC.query().filter({ entity__group_id__not_in: value }).remove();
            } else if (filter_key == 'strategic_international' && !in_array(-1, value)) {
                monitor_filteredinnovationsDC.query().filter({ brand__id__not_in: value }).remove();
            } else if (filter_key == 'in_market_date') {
                var the_values = monitor_innovation_list_filters[filter_key];
                if (the_values.hasOwnProperty('from')) {
                    var from_timestamp = get_in_market_date_string_to_timestamp(the_values['from']);
                    monitor_filteredinnovationsDC.query().filter({ in_market_date__lt: from_timestamp }).remove();
                }
                if (the_values.hasOwnProperty('to')) {
                    var to_timestamp = get_in_market_date_string_to_timestamp(the_values['to']);
                    monitor_filteredinnovationsDC.query().filter({ in_market_date__gt: to_timestamp }).remove();
                }
            } else if (filter_key == 'volume') {
                monitor_filteredinnovationsDC.query().filter({ total_volume__not_in: value }).remove();
            } else if (filter_key == 'latest_a_p') {
                /**
                 * latest_a_p value :
                 * O : Rien
                 * 1 : Under 500k€
                 * 2 : Between 500k€ & 1M€
                 * 3 : Between 1M€ & 2M€
                 * 4 : Between 2M€ & 5M€
                 * 5 : Between 5M€ & 10M€
                 * 6 : More than 10M€
                 */
                if (!in_array('1', value)) { // No Under 500k€
                    monitor_filteredinnovationsDC.query().filter({ financial__abs__latest__total_ap__lt: 500 }).remove();
                }
                if (!in_array('2', value)) { // No Between 500k€ & 1M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__abs__latest__total_ap__gte: 500,
                        financial__abs__latest__total_ap__lt: 1000
                    }).remove();
                }
                if (!in_array('3', value)) { // No Between 1M€ & 2M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__abs__latest__total_ap__gte: 1000,
                        financial__abs__latest__total_ap__lt: 2000
                    }).remove();
                }
                if (!in_array('4', value)) { // No Between 2M€ & 5M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__abs__latest__total_ap__gte: 2000,
                        financial__abs__latest__total_ap__lt: 5000
                    }).remove();
                }
                if (!in_array('5', value)) { // No Between 5M€ & 10M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__abs__latest__total_ap__gte: 5000,
                        financial__abs__latest__total_ap__lt: 10000
                    }).remove();
                }
                if (!in_array('6', value)) { // No More than 10M€
                    monitor_filteredinnovationsDC.query().filter({ financial__abs__latest__total_ap__gte: 10000 }).remove();
                }
            } else if (filter_key == 'years_since_launch') {
                /**
                 * years_since_launch value :
                 * 1 : Y1
                 * 2 : Y2
                 * 3 : Y3
                 * 4 : Y4
                 * 5 : 5 years or more
                 * 6 : Not in market
                 */
                if (!in_array('1', value)) { // No Y1
                    monitor_filteredinnovationsDC.query().filter({ years_since_launch: 1 }).remove();
                }
                if (!in_array('2', value)) { // No Y2
                    monitor_filteredinnovationsDC.query().filter({ years_since_launch: 2 }).remove();
                }
                if (!in_array('3', value)) { // No Y3
                    monitor_filteredinnovationsDC.query().filter({ years_since_launch: 3 }).remove();
                }
                if (!in_array('4', value)) { // No Y4
                    monitor_filteredinnovationsDC.query().filter({ years_since_launch: 4 }).remove();
                }
                if (!in_array('5', value)) { // No 5 years or more
                    monitor_filteredinnovationsDC.query().filter({ years_since_launch__gte: 5 }).remove();
                }
                if (!in_array('6', value)) { // No Not in market
                    monitor_filteredinnovationsDC.query().filter({ years_since_launch__lte: 0 }).remove();
                }
            } else if (filter_key == 'growth_model') {
                /**
                 * growth_model value :
                 * fast_growth : Fast growth
                 * slow_build : Slow build
                 */
                if (!in_array('fast_growth', value)) { // No Fast growth
                    monitor_filteredinnovationsDC.query().filter({ growth_model: 'fast_growth' }).remove();
                }
                if (!in_array('slow_build', value)) { // No Slow build
                    monitor_filteredinnovationsDC.query().filter({ growth_model: 'slow_build' }).remove();
                }
                if ((!in_array('fast_growth', value) && in_array('slow_build', value)) || (in_array('fast_growth', value) && !in_array('slow_build', value))) { // No Slow build
                    monitor_filteredinnovationsDC.query().filter({ growth_model: null }).remove();
                    monitor_filteredinnovationsDC.query().filter({ growth_model: '' }).remove();
                }
            } else if (filter_key == 'latest_net_sales') {
                /**
                 * latest_net_sales value :
                 * O : Rien
                 * 1 : Under 500k€
                 * 2 : Between 500k€ & 1M€
                 * 3 : Between 1M€ & 2M€
                 * 4 : Between 2M€ & 5M€
                 * 5 : Between 5M€ & 10M€
                 * 6 : More than 10M€
                 */
                if (!in_array('1', value)) { // No Under 500k€
                    monitor_filteredinnovationsDC.query().filter({ financial__data__latest__calc__net_sales__lt: 500 }).remove();
                }
                if (!in_array('2', value)) { // No Between 500k€ & 1M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__net_sales__gte: 500,
                        financial__data__latest__calc__net_sales__lt: 1000
                    }).remove();
                }
                if (!in_array('3', value)) { // No Between 1M€ & 2M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__net_sales__gte: 1000,
                        financial__data__latest__calc__net_sales__lt: 2000
                    }).remove();
                }
                if (!in_array('4', value)) { // No Between 2M€ & 5M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__net_sales__gte: 2000,
                        financial__data__latest__calc__net_sales__lt: 5000
                    }).remove();
                }
                if (!in_array('5', value)) { // No Between 5M€ & 10M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__net_sales__gte: 5000,
                        financial__data__latest__calc__net_sales__lt: 10000
                    }).remove();
                }
                if (!in_array('6', value)) { // No More than 10M€
                    monitor_filteredinnovationsDC.query().filter({ financial__data__latest__calc__net_sales__gte: 10000 }).remove();
                }
            } else if (filter_key == 'latest_volume') {
                /**
                 * latest_volume value :
                 * O : Rien
                 * 1 : Under 10 k9Lcs
                 * 2 : Between 10 & 20 k9Lcs
                 * 3 : Between 100 & 1000 k9Lcs
                 * 4 : More than 1000 k9Lcs
                 */
                if (!in_array('1', value)) { // No Under 10 k9Lcs
                    monitor_filteredinnovationsDC.query().filter({ financial__data__latest__calc__volume__lt: 10 }).remove();
                }
                if (!in_array('2', value)) { // No Between 10 & 20 k9Lcs
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__volume__gte: 10,
                        financial__data__latest__calc__volume__lt: 20
                    }).remove();
                }
                if (!in_array('3', value)) { // Between 100 & 1000 k9Lcs
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__volume__gte: 100,
                        financial__data__latest__calc__volume__lt: 1000
                    }).remove();
                }
                if (!in_array('4', value)) { // No More than 10M€
                    monitor_filteredinnovationsDC.query().filter({ financial__data__latest__calc__volume__gte: 1000 }).remove();
                }
            } else if (filter_key == 'latest_caap') {
                /**
                 * latest_caap value :
                 * O : Rien
                 * 1 : Under 500k€
                 * 2 : Between 500k€ & 1M€
                 * 3 : Between 1M€ & 2M€
                 * 4 : Between 2M€ & 5M€
                 * 5 : Between 5M€ & 10M€
                 * 6 : More than 10M€
                 */
                if (!in_array('1', value)) { // No Under 500k€
                    monitor_filteredinnovationsDC.query().filter({ financial__data__latest__calc__caap__lt: 500 }).remove();
                }
                if (!in_array('2', value)) { // No Between 500k€ & 1M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__caap__gte: 500,
                        financial__data__latest__calc__caap__lt: 1000
                    }).remove();
                }
                if (!in_array('3', value)) { // No Between 1M€ & 2M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__caap__gte: 1000,
                        financial__data__latest__calc__caap__lt: 2000
                    }).remove();
                }
                if (!in_array('4', value)) { // No Between 2M€ & 5M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__caap__gte: 2000,
                        financial__data__latest__calc__caap__lt: 5000
                    }).remove();
                }
                if (!in_array('5', value)) { // No Between 5M€ & 10M€
                    monitor_filteredinnovationsDC.query().filter({
                        financial__data__latest__calc__caap__gte: 5000,
                        financial__data__latest__calc__caap__lt: 10000
                    }).remove();
                }
                if (!in_array('6', value)) { // No More than 10M€
                    monitor_filteredinnovationsDC.query().filter({ financial__data__latest__calc__caap__gte: 10000 }).remove();
                }
            } else if (filter_key == 'portfolio') {
                var remove_big_bets = (!in_array('big_bet', value));
                if (!in_array('craft', value)) { // craft
                    if (remove_big_bets) {
                        monitor_filteredinnovationsDC.query().filter({ business_drivers: "Craft" }).remove();
                    } else {
                        monitor_filteredinnovationsDC.query().filter({ big_bet: '', business_drivers: "Craft" }).remove();
                    }
                }
                if (!in_array('rtd_rts', value)) { // rtd_rts
                    if (remove_big_bets) {
                        monitor_filteredinnovationsDC.query().filter({ business_drivers: "RTD / RTS" }).remove();
                    } else {
                        monitor_filteredinnovationsDC.query().filter({
                            big_bet: '',
                            business_drivers: "RTD / RTS"
                        }).remove();
                    }
                }
                if (!in_array('specialty', value)) { // specialty
                    if (remove_big_bets) {
                        monitor_filteredinnovationsDC.query().filter({ business_drivers: "Specialty" }).remove();
                    } else {
                        monitor_filteredinnovationsDC.query().filter({
                            big_bet: '',
                            business_drivers: "Specialty"
                        }).remove();
                    }
                }
                if (!in_array('no_low_alcohol', value)) { // no_low_alcohol
                    if (remove_big_bets) {
                        monitor_filteredinnovationsDC.query().filter({ business_drivers: "No & Low Alcohol" }).remove();
                    } else {
                        monitor_filteredinnovationsDC.query().filter({
                            big_bet: '',
                            business_drivers: "No & Low Alcohol"
                        }).remove();
                    }
                }
                if (!in_array('none', value)) { // None
                    if (remove_big_bets) {
                        monitor_filteredinnovationsDC.query().filter({ business_drivers: "None" }).remove();
                        monitor_filteredinnovationsDC.query().filter({ business_drivers: null }).remove();
                    } else {
                        monitor_filteredinnovationsDC.query().filter({ big_bet: '', business_drivers: "None" }).remove();
                        monitor_filteredinnovationsDC.query().filter({ big_bet: '', business_drivers: null }).remove();
                    }

                }
                if (!in_array('big_bet', value)) { // Big Bet
                    monitor_filteredinnovationsDC.query().filter({
                        big_bet: 'BB',
                        business_drivers__not_in: ["Craft", "RTD / RTS", "No & Low Alcohol", "Specialty"]
                    }).remove();
                }

            }
        }
    }
    monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('financial__data__latest__calc__net_sales', true).values());
    for (var order_key in monitor_innovation_list_order) {
        if (monitor_innovation_list_order.hasOwnProperty(order_key)) {
            var value = (monitor_innovation_list_order[order_key] == 'DESC');
            if (order_key == 'title') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('proper__title', value).values());
            } else if (order_key == 'brand') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('proper__brand_title', value).values());
            } else if (order_key == 'entity') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('proper__entity_title', value).values());
            } else if (order_key == 'innovation_type') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('proper__innovation_type', value).values());
            } else if (order_key == 'classification') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('proper__classification_type', value).values());
            } else if (order_key == 'in_market_date') { //
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('in_market_date', value).values());
            } else if (order_key == 'latest_a_p') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('financial__abs__latest__total_ap', value).values());
            } else if (order_key == 'years_since_launch') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('years_since_launch', value).values());
            } else if (order_key == 'latest_net_sales') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('financial__data__latest__calc__net_sales', value).values());
            } else if (order_key == 'latest_caap') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('financial__data__latest__calc__caap', value).values());
            } else if (order_key == 'latest_volume') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('financial__data__latest__calc__volume', value).values());
            } else if (order_key == 'portfolio') {
                // NO ORDER FOR PORTFOLIO
            } else if (order_key == 'consumer_opportunity') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('consumer_opportunity', value).values());
            } else if (order_key == 'growth_model') {
                monitor_filteredinnovationsDC = new DataCollection(monitor_filteredinnovationsDC.query().order('growth_model', value).values());
            }
        }
    }
    var ids = [];
    monitor_filteredinnovationsDC.query().each(function(product, index) {
        ids.push(product['id']);
    });
    monitor_filteredOuterInnovationsDC = new DataCollection(manage_data_innovations);
    monitor_filteredOuterInnovationsDC.query().filter({ id__in: ids }).remove();
}

/**
 * Fonction non terminée, car provoque des filtres involontaires
 */
function monitor_update_filters_lists() {
    var ids = [];
    var brands = [];
    var entities = [];
    var available_in = [];
    var le_volume = [];
    var other = [];
    monitor_filteredinnovationsDC.query().each(function(product, index) {
        if (!in_array(product['id'], ids)) {
            ids.push(product['id']);
        }
        if (!in_array(product['brand']['id'], brands)) {
            brands.push(product['brand']['id']);
        }
        if (!in_array(product['entity']['id'], entities)) {
            entities.push(product['entity']['id']);
        }
        if (!in_array(product['total_volume'], le_volume)) {
            le_volume.push(product['total_volume']);
        }
        if (!in_array(product['proper']['other'], other)) {
            other.push(product['proper']['other']);
        }
    });

    if (!$('#filter-title').hasClass('filtered')) {
        $('.content-filter-option-title').each(function() {
            var the_value = $(this).attr('data-value');
            if (in_array(the_value, ids)) {
                $(this).addClass('sub_filtered').show();
            } else {
                $(this).removeClass('sub_filtered').hide();
            }
        });
    }
    if (!$('#filter-brand').hasClass('filtered')) {
        $('.content-filter-option-brand').each(function() {
            var the_value = $(this).attr('data-value');
            if (in_array(the_value, brands)) {
                $(this).addClass('sub_filtered').show();
            } else {
                $(this).removeClass('sub_filtered').hide();
            }
        });
    }
    if (!$('#filter-entity').hasClass('filtered')) {
        $('.content-filter-option-entity').each(function() {
            var the_value = $(this).attr('data-value');
            if (in_array(the_value, entities)) {
                $(this).addClass('sub_filtered').show();
            } else {
                $(this).removeClass('sub_filtered').hide();
            }
        });
    }
    if (!$('#filter-availability').hasClass('filtered')) {
        $('.content-filter-option-market').each(function() {
            var the_value = $(this).attr('data-value');
            if (in_array(the_value, available_in)) {
                $(this).addClass('sub_filtered').show();
            } else {
                $(this).removeClass('sub_filtered').hide();
            }
        });
    }

    monitor_update_export_selection_params_array(ids);
}

/**
 * Get monitor status title.
 *
 * @param status
 * @returns {*}
 */
function get_monitor_status_title(status) {
    if (status == 'NEW & SOON') {
        return "New in the Pipeline & Soon in market";
    } else if (status == 'NEW') {
        return "New in the Pipeline";
    } else if (status == 'SOON') {
        return "Soon in market";
    }
    return status;
}

/**
 * retourne la ligne de tableau <tr> pour une innovation
 * @param innovation
 * @returns {string}
 */
function monitor_get_table_line_by_innovation(innovation) {
    var out = [], o = -1;
    out[++o] = '';
    var url = '/explore/' + innovation['id'];
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    out[++o] = '<tr class="' + innovation['current_stage'] + ' hovered-tr transition cursor-pointer">';


    // Portfolio
    out[++o] = '<td class="text-align-left">';
    out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail">';
    if (innovation['big_bet']) {
        out[++o] = '<img class="pri-pic-priorities tooltip-right-tr" data-no-retina title="Big Bet" alt="" src="/images/icons/icon-big_bet-light.png"/>';
    }
    var picto_business_drivers = pri_get_innovation_image('business_drivers', innovation['business_drivers']);
    if (picto_business_drivers) {
        out[++o] = '<img class="pri-pic-priorities tooltip-right-tr" data-no-retina title="' + innovation['business_drivers'] +
            '" alt="" src="/images/icons/' + picto_business_drivers + '"/>';
    }
    out[++o] = '</a>';
    out[++o] = '</td>';

    // (entities)
    var entity_title = '';
    if (innovation['entity']) {
        entity_title = innovation['entity']['title'];
    }
    out[++o] = '<td class="td-micro-loading text-align-left font-size-14">';
    out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail color-9b9b9b">';
    out[++o] = '<span class="inner-td-ellipsis tooltip-right-tr" title="' + entity_title + '">';
    out[++o] = entity_title;
    out[++o] = '</span>';
    out[++o] = '</a>';
    out[++o] = '</td>';

    // title
    out[++o] = '<td class="td-micro-loading-title text-align-left text-transform-uppercase">';
    out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail">';
    out[++o] = '<span class="inner-td-ellipsis right-10 tooltip-right-tr color-000 font-size-14" title="' + innovation['title'] + '">';
    out[++o] = innovation['title'];
    out[++o] = '</span>';
    out[++o] = '</a>';
    out[++o] = '</td>';

    // brand
    var brand_title = '';
    if (innovation['brand']['title']) {
        brand_title = innovation['brand']['title'];
    } else if (innovation['new_to_the_world']) {
        brand_title = 'New to the world';
    }
    out[++o] = '<td class="text-align-left font-size-14">';
    out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail color-9b9b9b">';
    out[++o] = '<span class="inner-td-ellipsis tooltip-right-tr" title="' + brand_title + '">';
    out[++o] = brand_title;
    out[++o] = '</span>';
    out[++o] = '</a>';
    out[++o] = '</td>';

    // Years since launch
    out[++o] = '<td class="text-align-left font-size-14">';
    out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail color-9b9b9b">';
    if (innovation['in_market_date'] != null) {
        out[++o] = '<span class="padding-left-10">Y' + innovation['years_since_launch'] + '</span>';
    }
    out[++o] = '</a>';
    out[++o] = '</td>';

    // Growth model
    var growth_model = innovation['growth_model'];
    out[++o] = '<td class="td-micro-loading text-align-center font-size-14">';
    out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail color-9b9b9b">';
    if (!innovation_is_a_service) {
        if (growth_model == 'fast_growth') { // Fast Growth
            out[++o] = '<img class="pri-pic-list-growth-model tooltip-right-tr" data-no-retina title="Fast growth" alt="" src="/images/icons/icon-fast_growth-light.png"/>';
        } else { // Slow build
            out[++o] = '<img class="pri-pic-list-growth-model tooltip-right-tr" data-no-retina title="Slow build" alt="" src="/images/icons/icon-slow_build-light.png"/>';
        }
    }
    out[++o] = '</a>';
    out[++o] = '</td>';


    if (in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment', 'incubate', 'scale_up', 'discontinued', 'permanent_range'])) {

        // LATEST VOLUME
        if (!innovation_is_a_service && in_array(innovation['current_stage'], ['experiment', 'incubate', 'scale_up', 'discontinued', 'permanent_range'])) {
            var latest_saved_volume = innovation['financial']['data']['latest']['saved']['volume'];
            var movement = Math.round(get_percent_diff_between_two_values(
                innovation['financial']['data']['latest_a']['calc']['volume'],
                innovation['financial']['data']['latest']['calc']['volume']
            ));
            out[++o] = '<td class="text-align-right">';
            out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail">';
            out[++o] = get_financial_data_html_for_table(latest_saved_volume, "k9Lcs", movement);
            out[++o] = '</a>';
            out[++o] = '</td>';
        } else {
            out[++o] = '<td><a href="' + url + '" class="td-inner-link link_explore_detail"></a></td>';
        }

        if (in_array(innovation['current_stage'], ['incubate', 'scale_up', 'discontinued', 'permanent_range'])) {
            // LATEST NET SALES
            var latest_saved_net_sales = innovation['financial']['data']['latest']['saved']['net_sales'];
            var movement = Math.round(get_percent_diff_between_two_values(
                innovation['financial']['data']['latest_a']['calc']['net_sales'],
                innovation['financial']['data']['latest']['calc']['net_sales']
            ));
            out[++o] = '<td class="text-align-right">';
            out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail">';
            out[++o] = get_financial_data_html_for_table(latest_saved_net_sales, "k€", movement, "positive");
            out[++o] = '</a>';
            out[++o] = '</td>';
        } else {
            out[++o] = '<td><a href="' + url + '" class="td-inner-link link_explore_detail"></a></td>';
        }

        // LATEST TOTAL A_P
        var latest_saved_total_ap = innovation['financial']['data']['latest']['calc']['total_ap'];
        var movement = Math.round(get_percent_diff_between_two_values(innovation['financial']['data']['latest_a']['calc']['total_ap'], latest_saved_total_ap));
        out[++o] = '<td class="text-align-right">';
        out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail">';
        out[++o] = get_financial_data_html_for_table(latest_saved_total_ap, "k€", movement, "negative");
        out[++o] = '</a>';
        out[++o] = '</td>';

        // LATEST CAAP
        if (!innovation_is_a_service && in_array(innovation['current_stage'], ['incubate', 'scale_up', 'discontinued', 'permanent_range'])) {
            var latest_saved_caap = innovation['financial']['data']['latest']['calc']['caap'];
            var movement = Math.round(get_percent_diff_between_two_values(
                innovation['financial']['data']['latest_a']['calc']['caap'],
                innovation['financial']['data']['latest']['calc']['caap']
            ));
            var state = (latest_saved_caap >= 0) ? 'positive' : 'negative';
            out[++o] = '<td class="text-align-right">';
            out[++o] = '<a href="' + url + '" class="td-inner-link link_explore_detail">';
            out[++o] = get_financial_data_html_for_table(latest_saved_caap, "k€", movement, state);
            out[++o] = '</a>';
            out[++o] = '</td>';
        } else {
            out[++o] = '<td><a href="' + url + '" class="td-inner-link link_explore_detail"></a></td>';
        }

    } else {
        out[++o] = '<td><a href="' + url + '" class="td-inner-link link_explore_detail"></a></td>';
        out[++o] = '<td><a href="' + url + '" class="td-inner-link link_explore_detail"></a></td>';
        out[++o] = '<td><a href="' + url + '" class="td-inner-link link_explore_detail"></a></td>';
        out[++o] = '<td><a href="' + url + '" class="td-inner-link link_explore_detail"></a></td>';
    }


    out[++o] = '</tr>';
    return out.join('');
}

/**
 * Initialise ou update la pagination
 */
function monitor_initialize_list_pagination() {
    var page = (findGetParameter('page')) ? parseInt(findGetParameter('page')) : 1;
    var classeA = 'list-monitor opti-link';
    var nbTotal = monitor_filteredinnovationsDC.query().count();
    var limit = monitor_innovation_list_limit;
    var ceilValue = Math.ceil(nbTotal / limit);
    var content = '';
    var hasPPT = (Math.ceil(nbTotal / limit) > 20);
    var beforePPT = false;
    var afterPPT = false;
    var defaultRequest = "/content/monitor";
    var addToUrl = "";
    var getParameters = getUrlVars();
    for (var key in getParameters) {
        if (key != 'page' && key != 'limit' && getParameters.hasOwnProperty(key)) {
            addToUrl += "&" + key + "=" + getParameters[key];
        }
    }
    if (limit != default_innovation_list_limit) {
        addToUrl += '&limit=' + limit;
    }
    if (nbTotal > limit) {
        if (page == 1) {
            content += '<li class="prev disabled">';
            content += '<a class="no-smoothState"> &lt; </a>';
            content += '</li>';
        } else {
            var link = '/?page=' + (page - 1) + addToUrl;
            content += '<li class="prev">';
            content += '<a class="' + classeA + '" href="' + link + '"> &lt; </a>';
            content += '</li>';
        }
        for (var i = 1; i <= ceilValue; i++) {
            if (
                hasPPT === false ||
                (
                    (i == 1) ||
                    (i == ceilValue) ||
                    (i == page) ||
                    ((i < page) && (i > (page - 5))) ||
                    ((i > page) && (i < (page + 5)))
                )
            ) {
                var aClass = 'pagination-' + i + ' ';
                if (i == ceilValue) {
                    aClass += 'last';
                } else if (i == 1) {
                    aClass += 'first';
                }
                var classe = (page == i) ? 'class="state-active ' + aClass + '"' : 'class="' + aClass + '"';
                content += '<li ' + classe + '><a class="' + classeA + '" href="' + defaultRequest + '?' + 'page' + '=' + i + addToUrl + '">' + i + '</a></li>';
            } else {
                if (beforePPT == false && i < page && i < page - 5) {
                    beforePPT = true;
                    content += '<li style="color: #acc4e0;">...</li>';
                }
                if (afterPPT == false && i > page && i > page + 5) {
                    afterPPT = true;
                    content += '<li style="color: #acc4e0;">...</li>';
                }
            }
        }
        if (page == ceilValue) {
            content += '</li>';
            content += '<li class="next disabled"><a class="no-smoothState"> &gt; </a>';
            content += '</li>';
        } else {
            var link = defaultRequest + '?' + 'page' + '=' + (page + 1) + addToUrl;
            content += '</li>';
            content += '<li class="next"><a class="' + classeA + '" href="' + link + '"> &gt; </a>';
            content += '</li>';
        }
    } else {
        content += '<li class="prev disabled">';
        content += '<a class="no-smoothState"> &lt; </a>';
        content += '</li>';
        content += '<li class="state-active">';
        content += '<a href="">1</a>';
        content += '</li>';
        content += '<li class="next disabled"><a class="no-smoothState"> &gt; </a>';
        content += '</li>';
    }
    $('.pri-pagination ul').html(content);

}

/**
 * Retourne le libelle du h1 pour la liste, en fonction du user
 */
function monitor_set_libelle_h1_for_list() {
    var all_filters = monitor_innovation_list_filters;
    var financial_date = get_financial_date();
    $('.full-content-monitor .libelle_current_le').html(get_libelle_current_le());
    $('.full-content-monitor .libelle_versus_date').html(get_libelle_last_a());
    $('.full-content-monitor .libelle_current_fy').html(get_financial_date_FY(financial_date));
}


function monitor_get_detail_statistics_for_consolidation() {
    var ret = {
        'latest': {
            'main': [],
            'other': [],
            1: [],
            2: [],
            3: [],
            4: [],
            5: [],
            6: [],
            8: [],
            7: [],
            88: []
        },
        'latest_a': {
            'main': [],
            'other': [],
            1: [],
            2: [],
            3: [],
            4: [],
            5: [],
            6: [],
            8: [],
            7: [],
            88: []
        }
    };
    monitor_filteredinnovationsDC.query().each(function(product, index) {
        var id = product['id'];
        var latest_stage_id = product['current_stage_id'];
        var latest_a_stage_id = product['last_a_stage_id'];
        var latest_a_is_frozen = product['last_a_is_frozen'];
        var latest_is_frozen = product['is_frozen'];
        if (latest_stage_id && !latest_is_frozen) {
            ret['latest'][latest_stage_id].push(id);
            if (in_array(latest_stage_id, [1, 2, 3, 4, 5])) {
                ret['latest']['main'].push(id);
            } else if (in_array(latest_stage_id, [7, 8])) {
                ret['latest']['other'].push(id);
            }
        }
        if (latest_is_frozen) {
            ret['latest'][88].push(id);
            ret['latest']['other'].push(id);
        }
        if (latest_a_stage_id && !latest_a_is_frozen) {
            ret['latest_a'][latest_a_stage_id].push(id);
            if (in_array(latest_a_stage_id, [1, 2, 3, 4, 5])) {
                ret['latest_a']['main'].push(id);
            } else if (in_array(latest_a_stage_id, [7, 8])) {
                ret['latest_a']['other'].push(id);
            }
        }
        if (latest_a_is_frozen) {
            ret['latest_a'][88].push(id);
            ret['latest_a']['other'].push(id);
        }
    });
    return ret;
}

/**
 * monitor_get_nb_negative_between_two_arrays
 * @param old_array
 * @param new_array
 */
function monitor_get_nb_differences_between_two_arrays(old_array, new_array) {
    var diff = old_array.diff(new_array);
    return diff.length;
}

function monitor_get_statistics_percent_movements() {
    var total = {
        'latest': {
            'total_ap': 0,
            'caap': 0,
            'net_sales': 0,
            'volume': 0,
            'contributing_margin': 0,
            'level_of_investment': 0,
            'level_of_profitability': 0

        },
        'latest_a': {
            'total_ap': 0,
            'caap': 0,
            'net_sales': 0,
            'volume': 0,
            'contributing_margin': 0,
            'level_of_investment': 0,
            'level_of_profitability': 0
        }
    };
    var early_stages = ['discover', 'ideate', 'experiment'];
    monitor_filteredinnovationsDC.query().each(function(product, index) {
        var current_stage = product['current_stage'];
        total['latest']['total_ap'] += product['financial']['data']['latest']['calc']['total_ap'];
        total['latest']['net_sales'] += product['financial']['data']['latest']['calc']['net_sales'];
        total['latest']['volume'] += product['financial']['data']['latest']['calc']['volume'];
        total['latest']['contributing_margin'] += product['financial']['data']['latest']['calc']['contributing_margin'];

        total['latest_a']['total_ap'] += product['financial']['data']['latest_a']['calc']['total_ap'];
        total['latest_a']['net_sales'] += product['financial']['data']['latest_a']['calc']['net_sales'];
        total['latest_a']['volume'] += product['financial']['data']['latest_a']['calc']['volume'];
        total['latest_a']['contributing_margin'] += product['financial']['data']['latest_a']['calc']['contributing_margin'];

        if (!in_array(current_stage, early_stages)) {
            total['latest']['caap'] += product['financial']['data']['latest']['calc']['caap'];
            total['latest_a']['caap'] += product['financial']['data']['latest_a']['calc']['caap'];
        }
    });
    total['latest']['level_of_investment'] = calculate_level_of_investment(total['latest']['total_ap'], total['latest']['net_sales']);
    total['latest_a']['level_of_investment'] = calculate_level_of_investment(total['latest_a']['total_ap'], total['latest_a']['net_sales']);
    total['latest']['level_of_profitability'] = calculate_level_of_profitability(total['latest']['contributing_margin'], total['latest']['net_sales']);
    total['latest_a']['level_of_profitability'] = calculate_level_of_profitability(total['latest_a']['contributing_margin'], total['latest_a']['net_sales']);
    var pts_level_of_investment = (total['latest_a']['level_of_investment'] != "NEW" && total['latest']['level_of_investment'] != 'NEW') ? Math.round(total['latest']['level_of_investment'] - total['latest_a']['level_of_investment']) : 0;
    var pts_level_of_probability = Math.round(total['latest']['level_of_profitability'] - total['latest_a']['level_of_profitability']);
    return {
        'net_sales': Math.round(get_percent_diff_between_two_values(total['latest_a']['net_sales'], total['latest']['net_sales'])),
        'total_ap': Math.round(get_percent_diff_between_two_values(total['latest_a']['total_ap'], total['latest']['total_ap'])),
        'caap': Math.round(get_percent_diff_between_two_values(total['latest_a']['caap'], total['latest']['caap'])),
        'volume': Math.round(get_percent_diff_between_two_values(total['latest_a']['volume'], total['latest']['volume'])),
        'level_of_investment': pts_level_of_investment,
        'level_of_profitability': pts_level_of_probability,
    };
}

/**
 * Initialise la variable statistics pour la consolidation, selon le filtre des innovations
 */
function monitor_update_stats_consolidation() {
    var total = {
        'value': {
            'nb': monitor_filteredinnovationsDC.query().filter({
                is_frozen: false,
                current_stage_id__in: [1, 2, 3, 4, 5]
            }).count(),
            'nb_other': 0,
            'discover': monitor_filteredinnovationsDC.query().filter({ is_frozen: false, current_stage_id: 1 }).count(),
            'ideate': monitor_filteredinnovationsDC.query().filter({ is_frozen: false, current_stage_id: 2 }).count(),
            'experiment': monitor_filteredinnovationsDC.query().filter({ is_frozen: false, current_stage_id: 3 }).count(),
            'incubate': monitor_filteredinnovationsDC.query().filter({ is_frozen: false, current_stage_id: 4 }).count(),
            'scale_up': monitor_filteredinnovationsDC.query().filter({ is_frozen: false, current_stage_id: 5 }).count(),
            'discontinued': monitor_filteredinnovationsDC.query().filter({
                is_frozen: false,
                current_stage_id: 7
            }).count(),
            'permanent_range': monitor_filteredinnovationsDC.query().filter({
                is_frozen: false,
                current_stage_id: 8
            }).count(),
            'frozen': monitor_filteredinnovationsDC.query().filter({ is_frozen: true }).count()
        }
    };
    total['value']['nb_other'] = total['value']['permanent_range'] + total['value']['discontinued'] + total['value']['frozen'];
    var ret = {
        'net_sales': 0,
        'total_ap': 0,
        'caap': 0,
        'volume': 0,
        'level_of_investment': 0,
        'level_of_profitability': 0
    };
    var total_volume_le = 0;
    var total_volume_la = 0;
    var total_total_ap = 0;
    var total_ns_value = 0;
    var total_cm_value = 0;
    var early_stages = ['discover', 'ideate', 'experiment'];
    monitor_filteredinnovationsDC.query().each(function(product, index) {
        var current_stage = product['current_stage'];
        ret['net_sales'] += Math.round(product['financial']['data']['latest']['calc']['net_sales']);
        ret['total_ap'] += Math.round(product['financial']['data']['latest']['calc']['total_ap']);
        ret['volume'] += Math.round(product['financial']['data']['latest']['calc']['volume']);
        if (!in_array(current_stage, early_stages)) {
            ret['caap'] += Math.round(product['financial']['data']['latest']['calc']['caap']);
        }
        total_volume_le += parseInt(product['financial']['data']['latest']['calc']['volume']);
        total_volume_la += parseInt(product['financial']['data']['latest_a']['calc']['volume']);
        total_total_ap += parseInt(product['financial']['data']['latest']['calc']['total_ap']);
        total_ns_value += parseInt(product['financial']['data']['latest']['calc']['net_sales']);
        total_cm_value += parseInt(product['financial']['data']['latest']['calc']['contributing_margin']);
    });
    ret['level_of_investment'] = calculate_level_of_investment(total_total_ap, total_ns_value);
    ret['level_of_profitability'] = calculate_level_of_profitability(total_cm_value, total_ns_value);

    monitor_statistics = {};
    monitor_statistics['nb'] = total['value']['nb'];
    monitor_statistics['nb_other'] = total['value']['nb_other'];
    monitor_statistics['percent'] = monitor_get_statistics_percent_movements();

    // Set donuts datas
    var nb_state_empty_value = (total['value']['nb'] == 0) ? 100 : 0;
    var nb_state_empty_value_other = (total['value']['nb_other'] == 0) ? 100 : 0;
    var datasets = {
        'data': [
            total['value']['discover'],
            total['value']['ideate'],
            total['value']['experiment'],
            total['value']['incubate'],
            total['value']['scale_up'],
            nb_state_empty_value
        ],
        "borderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
        "hoverBorderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
        "backgroundColor": ["#4f6e99", "#fab700", "#45ab34", "#e8485c", "#80a7d0", monitor_color_empty],
        "hoverBackgroundColor": ["#4f6e99", "#fab700", "#45ab34", "#e8485c", "#80a7d0", monitor_color_empty]
    };
    var datasets_other = {
        'data': [
            total['value']['permanent_range'],
            total['value']['discontinued'],
            total['value']['frozen'],
            nb_state_empty_value_other
        ],
        "borderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
        "hoverBorderColor": [monitor_border_color, monitor_border_color, monitor_border_color, monitor_border_color],
        "backgroundColor": ["#FFFFFF", "#BCC3CE", "#99CDD8", monitor_color_empty],
        "hoverBackgroundColor": ["#FFFFFF", "#BCC3CE", "#99CDD8", monitor_color_empty]
    };
    monitor_statistics['data_stage'] = {
        "labels": ["discover", "ideate", "experiment", "incubate", "scale_up", "empty"],
        'datasets': [datasets]
    };
    monitor_statistics['data_stage_other'] = {
        "labels": ["permanent_range", "discontinued", "frozen", "empty"],
        'datasets': [datasets_other]
    };
    // En donuts datas

    var consolidation = {};

    // Set list_item_stage
    var list_item_stage = '';
    var filters_stage = monitor_innovation_list_filters['current_stage'];
    var detail_stats = monitor_get_detail_statistics_for_consolidation();
    var item_stages = [
        { id: 1, class: 'discover' },
        { id: 2, class: 'ideate' },
        { id: 3, class: 'experiment' },
        { id: 4, class: 'incubate' },
        { id: 5, class: 'scale_up' },
        { id: 8, class: 'permanent_range' },
        { id: 7, class: 'discontinued' },
        { id: 88, class: 'frozen' }
    ];
    for (var i = 0; i < item_stages.length; i++) {
        var item = item_stages[i];
        var total_latest = detail_stats['latest'][item['id']];
        var total_latest_a = detail_stats['latest_a'][item['id']];
        var nb_negative = monitor_get_nb_differences_between_two_arrays(total_latest_a, total_latest);
        var nb_positive = monitor_get_nb_differences_between_two_arrays(total_latest, total_latest_a);
        list_item_stage += monitor_return_list_item_stage_html((!filters_stage || (filters_stage && in_array(item['id'], filters_stage))), (total['value'][item['class']] + ' ' + returnGoodStage(item['class'])), item['class'], nb_negative, nb_positive);
    }
    monitor_statistics['list_item_stage'] = list_item_stage;
    monitor_statistics['detail'] = detail_stats;
    // End list_item_stage

    monitor_statistics['net_sales'] = ret['net_sales'];
    monitor_statistics['total_ap'] = ret['total_ap'];
    monitor_statistics['caap'] = ret['caap'];
    monitor_statistics['volume'] = ret['volume'];
    monitor_statistics['level_of_investment'] = (isNaN(ret['level_of_investment'])) ? 0 : ret['level_of_investment'];
    monitor_statistics['level_of_profitability'] = (isNaN(ret['level_of_profitability'])) ? 0 : ret['level_of_profitability'];
    monitor_statistics['other_data'] = data_consolidation_all['other_data'];

    var current_getter = getUrlVarsForFilter();
    if (!monitor_old_getters || (monitor_old_getters && JSON.stringify(monitor_old_getters) != JSON.stringify(current_getter))) {
        monitor_initiate_consolidation_libelles();
        monitor_old_getters = current_getter;
        monitor_loadStatistics();
    }
}

/**
 * monitor_return_list_item_stage_html
 * @param is_active
 * @param text
 * @param class_icon
 * @param nb_negative
 * @param nb_positive
 * @returns {string}
 */
function monitor_return_list_item_stage_html(is_active, text, class_icon, nb_negative, nb_positive) {
    var out = [], o = -1;
    var with_movement = (typeof nb_negative != 'undefined' && typeof nb_positive != 'undefined');
    var classe_margin_bottom = 'margin-bottom-5';
    if (class_icon == 'scale_up') {
        classe_margin_bottom = 'margin-bottom-40';
    }
    if (class_icon == 'frozen') {
        classe_margin_bottom = 'margin-bottom-0';
    }
    out[++o] = '<div class="list-item ' + classe_margin_bottom + ' ' + ((is_active) ? '' : 'disabled') + '"><span class="micro-icon-stage ' + class_icon + '"></span>' + text;
    if (with_movement) {
        out[++o] = '<div class="content-movements inline"><div class="negative">' + nb_negative + '</div><div class="positive">' + nb_positive + '</div></div>'
    }
    out[++o] = '</div>';
    return out.join('');
}

/**
 * Initiate consolidation libelles
 */
function monitor_initiate_consolidation_libelles() {
    $('.full-content-monitor .other-info-consolidation .libelle').attr('data-old-date', data_consolidation_all['other_data']['old_date_libelle']);
}

/**
 * Charge la consolidation statistique
 */
function monitor_loadStatistics() {
    var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
    if (monitor_statistics) {
        // chart Stage
        var $span_total = $('.content-donut-stages .donut-inner-stage span');
        $span_total.text(monitor_statistics.nb);
        $span_total.attr('data-value', monitor_statistics.nb);
        if (!$('html').hasClass('ie')) {
            $span_total.animateNumber({
                number: monitor_statistics.nb,
                numberStep: comma_separator_number_step
            }, 800);
        } else {
            $span_total.html(monitor_statistics.nb);
        }
        var total_latest = monitor_statistics['detail']['latest']['main'];
        var total_latest_a = monitor_statistics['detail']['latest_a']['main'];
        var move_negative = monitor_get_nb_differences_between_two_arrays(total_latest_a, total_latest);
        var move_positive = monitor_get_nb_differences_between_two_arrays(total_latest, total_latest_a);
        $('.content-movements.main-stages .negative').html(move_negative);
        $('.content-movements.main-stages .positive').html(move_positive);

        // chart Stage Other
        var $span_total_other = $('.content-donut-stages-other .donut-inner-stage span');
        $span_total_other.text(monitor_statistics.nb_other);
        $span_total_other.attr('data-value', monitor_statistics.nb_other);
        if (!$('html').hasClass('ie')) {
            $span_total_other.animateNumber({
                number: monitor_statistics.nb_other,
                numberStep: comma_separator_number_step
            }, 800);
        } else {
            $span_total_other.html(monitor_statistics.nb_other);
        }
        total_latest = monitor_statistics['detail']['latest']['other'];
        total_latest_a = monitor_statistics['detail']['latest_a']['other'];
        move_negative = monitor_get_nb_differences_between_two_arrays(total_latest_a, total_latest);
        move_positive = monitor_get_nb_differences_between_two_arrays(total_latest, total_latest_a);
        $('.content-movements.other-stages .negative').html(move_negative);
        $('.content-movements.other-stages .positive').html(move_positive);

        $('.consolidation-list-item-stage').html(monitor_statistics.list_item_stage);
        monitor_data_stage = monitor_statistics.data_stage;
        monitor_data_stage_other = monitor_statistics.data_stage_other;

        if (!monitor_stageChart || !monitor_stageChartOther) {
            monitor_loadCharts();
        }
        monitor_stageChart.data.datasets[0].data = monitor_data_stage.datasets[0].data;
        monitor_stageChart.update();
        monitor_stageChartOther.data.datasets[0].data = monitor_data_stage_other.datasets[0].data;
        monitor_stageChartOther.update();

        monitor_stats_loaded = true;
        var keys = ['net_sales', 'total_ap', 'caap', 'volume'];
        for (var i = 0; i < keys.length; i++) {
            var el_key = keys[i];
            var el_data = monitor_statistics[el_key];
            var percent = monitor_statistics['percent'][el_key];
            if (!$('html').hasClass('ie')) {
                $('.full-content-monitor .other-info-consolidation.' + el_key + ' .under-libelle span').animateNumber({
                    number: el_data,
                    numberStep: comma_separator_number_step
                }, 800);
            } else {
                $('.full-content-monitor .other-info-consolidation.' + el_key + ' .under-libelle span').html(number_format_en(el_data));
            }
            var percent_class = (percent >= 0) ? 'positive' : 'negative';
            $('.full-content-monitor .other-info-consolidation.' + el_key + " .consolidation-movement").removeClass('positive negative').addClass(percent_class).html(Math.abs(percent) + ' %');
            $('.full-content-monitor .other-info-consolidation.' + el_key).attr('data-old-date', monitor_statistics['other_data']['old_date_libelle']);
        }
        var percent_keys = ['level_of_investment', 'level_of_profitability'];
        for (var i = 0; i < percent_keys.length; i++) {
            var el_key = percent_keys[i];
            var el_data = monitor_statistics[el_key];
            var percent = monitor_statistics['percent'][el_key];
            if (el_data == 'N/A') {
                $('.full-content-monitor .other-info-consolidation.' + el_key + ' .under-libelle span').html(el_data);
            } else if (!$('html').hasClass('ie')) {
                $('.full-content-monitor .other-info-consolidation.' + el_key + ' .under-libelle span').animateNumber({
                    number: el_data,
                    numberStep: function(now, tween) {
                        var floored_number = now,
                            target = $(tween.elem);
                        floored_number = parseFloat(floored_number.toFixed(1));
                        var int = Math.trunc(floored_number);
                        var float = Math.abs(Math.trunc((floored_number - int) * 10));
                        target.text(int + ',' + float);
                    }
                }, 800);
            } else {
                $('.full-content-monitor .other-info-consolidation.' + el_key + ' .under-libelle span').html(number_format_en(el_data));
            }
            var percent_class = (percent >= 0) ? 'positive' : 'negative';
            $('.full-content-monitor .other-info-consolidation.' + el_key + " .consolidation-movement").removeClass('positive negative').addClass(percent_class).html(Math.abs(percent) + ' pts');
            $('.full-content-monitor .other-info-consolidation.' + el_key).attr('data-old-date', monitor_statistics['other_data']['old_date_libelle']);
        }
    } else {
        monitor_stats_loaded = false;
        console.log('stats not loaded');
    }
}


function monitor_initialize_perimeter_selector() {
    var out = [], o = -1;
    var perimeters = other_datas['perimeter_json'];
    var user_perimeter = $('#main').attr('data-perimeter');
    var target = getUrlVars()['filter[perimeter]'];
    if (target) {
        user_perimeter = parseInt(target);
    }
    if (!perimeters) {
        return;
    }
    var selected = (user_perimeter == '-1') ? 'selected' : '';
    out[++o] = '<div class="display-inline-block margin-left-60"></div>';
    out[++o] = '<select id="monitor-select-perimeter">';
    out[++o] = '<option value="-1" ' + selected + '>PR Group</option>';
    for (var i = 0; i < perimeters.length; i++) {
        selected = (user_perimeter == perimeters[i]["id"]) ? 'selected' : '';
        out[++o] = '<option value="' + perimeters[i]["id"] + '" ' + selected + '>' + perimeters[i]["text"] + '</option>';
    }
    out[++o] = '</select>';
    $('.monitor-content-upper-filters .left').append(out.join(''));

    $("#monitor-select-perimeter").select2({
        minimumResultsForSearch: -1,
        theme: "default monitor-select-filter",
        containerCssClass: "select-perimeter",
        dropdownCssClass: "select-perimeter",
        width: '180px'
    }).on('change', function(e) {
        if ($("#monitor-select-perimeter").val() == '-1') {
            $('.select-perimeter').addClass('default-value');
        } else {
            $('.select-perimeter').removeClass('default-value');
        }
        monitor_resetStatistics();
        monitor_reset_top_filters('perimeter');
        monitor_reset_bottom_filters();
        monitor_updatePage();
    });
    if (user_perimeter == '-1') {
        $('.select-perimeter').addClass('default-value');
    }
    monitor_initialize_strategic_international_selector();
}

function monitor_initialize_strategic_international_selector() {
    var out = [], o = -1;
    var strategic_international_brands = get_strategic_international_brands();
    var user_strategic_international_brand = '-1';
    var target = getUrlVars()['filter[strategic_international]'];
    if (target) {
        user_strategic_international_brand = parseInt(target);
    }
    var selected = (user_strategic_international_brand == '-1') ? 'selected' : '';
    out[++o] = '<div class="display-inline-block margin-left-10"></div>';
    out[++o] = '<select id="monitor-select-strategic-international-brand">';
    out[++o] = '<option value="-1" ' + selected + '>Strategic International</option>';
    for (var i = 0; i < strategic_international_brands.length; i++) {
        selected = (user_strategic_international_brand == strategic_international_brands[i]["id"]) ? 'selected' : '';
        out[++o] = '<option value="' + strategic_international_brands[i]["id"] + '" ' + selected + '>' + strategic_international_brands[i]["text"] + '</option>';
    }
    out[++o] = '</select>';
    $('.monitor-content-upper-filters .left').append(out.join(''));
    $("#monitor-select-strategic-international-brand").select2({
        minimumResultsForSearch: -1,
        theme: "default monitor-select-filter",
        containerCssClass: "select-strategic-international",
        dropdownCssClass: "select-strategic-international",
        width: '200px'
    }).on('change', function(e) {
        if ($("#monitor-select-strategic-international-brand").val() == '-1') {
            $('.select-strategic-international').addClass('default-value');
        } else {
            $('.select-strategic-international').removeClass('default-value');
        }
        monitor_resetStatistics();
        monitor_reset_top_filters('strategic_international');
        monitor_reset_bottom_filters();
        monitor_updatePage();
    });
    if (user_strategic_international_brand == '-1') {
        $('.select-strategic-international').addClass('default-value');
    }
}

/**
 * monitor_reset_top_filters
 * @param except_target
 */
function monitor_reset_top_filters(except_target) {
    except_target = except_target || null;
    if (!except_target || except_target == 'perimeter') {
        $("#monitor-select-strategic-international-brand").val('-1').trigger('change.select2');
        $('.select-strategic-international').addClass('default-value');
    }
    if (!except_target || except_target == 'strategic_international') {
        $("#monitor-select-perimeter").val('-1').trigger('change.select2');
        $('.select-perimeter').addClass('default-value');
    }
    $('.filter-classification').addClass('active');
    $('.filter-group-stage').removeClass('active');
    $('.filter-group-stage.default').addClass('active');
}

/**
 * monitor_reset_bottom_filters
 * @param except_target
 */
function monitor_reset_bottom_filters() {
    $('.content-filter-active').contentFilter('reset');
}

/**
 * Load Chart
 */
function monitor_loadCharts() {
    // Chart options
    Chart.defaults.global.legend.display = false;
    //Chart.defaults.global.tooltips.enabled = false;
    Chart.defaults.global.hover.enabled = false;

    if (monitor_stageChart !== null) {
        monitor_stageChart.destroy();
    }
    var ctx_stage = document.getElementById("monitor-chartStages").getContext("2d");
    monitor_stageChart = new Chart(ctx_stage, {
        type: 'doughnut',
        data: monitor_data_stage,
        animation: {
            animateScale: true
        },
        options: {
            cutoutPercentage: 68,
            tooltips: {
                enabled: false,
                custom: customTooltipsStage
            }
        }
    });

    if (monitor_stageChartOther !== null) {
        monitor_stageChartOther.destroy();
    }
    var ctx_stage_other = document.getElementById("monitor-chartStages-other").getContext("2d");
    monitor_stageChartOther = new Chart(ctx_stage_other, {
        type: 'doughnut',
        data: monitor_data_stage_other,
        animation: {
            animateScale: true
        },
        options: {
            cutoutPercentage: 68,
            tooltips: {
                enabled: false,
                custom: customTooltipsStage
            }
        }
    });

    /* Add action on click
     document.getElementById("monitor-chartStages").onclick = function (evt) {
     var activePoints = monitor_stageChart.getElementsAtEvent(evt);
     if (activePoints.length > 0) {
     //get the internal index of slice in pie chart
     var clickedElementindex = activePoints[0]["_index"];
     //get specific label by index
     var label = monitor_stageChart.data.labels[clickedElementindex];
     //get value by index
     var value = monitor_stageChart.data.datasets[0].data[clickedElementindex];
     // other stuff that requires slice's label and value
     } else {

     }
     };*/

}

function monitor_load_toast() {
    if (!check_if_is_data_capture_toast_enabled() || !check_if_data_capture_is_opened()) {
        return;
    }
    var contentInfo = '<div class="bold">The ' + get_libelle_current_le() + ' financial data capture is currently in progress.</div>';
    contentInfo += 'Please note that the consolidated numbers below are not final during this period.';
    $('#pri-toast-monitor').cookieNotificator({
        id: 'monitor-infos-saisie',
        theme: 'notice',
        type: 'html',
        cookieCheck: true,
        content: contentInfo,
        customStyle: {
            'margin': '0 auto 30px',
            'max-width': '100%'
        }
    });
}

/**
 * Update data filters by url
 */
function monitor_update_data_filters_by_url() {
    for (var key in monitor_innovation_list_filters) {
        if (monitor_data_filters['filter'].hasOwnProperty(key)) {
            for (var i = 0; i < monitor_innovation_list_filters[key].length; i++) {
                var to_check_id = monitor_innovation_list_filters[key][i];
                if (in_array(key, ['current_stage', 'consumer_opportunity', 'entity', 'title', 'brand', 'perimeter', 'strategic_international'])) {
                    to_check_id = parseInt(to_check_id);
                }
                for (var j = 0; j < monitor_data_filters['filter'][key].length; j++) {
                    if (monitor_data_filters['filter'][key][j]['id'] == to_check_id) {
                        monitor_data_filters['filter'][key][j]['checked'] = "checked=\"checked\"";
                    }
                }
            }
        }
    }
    monitor_update_list_data_orders();
}

/**
 * monitor_update_export_selection_params_array
 * @param ids
 */
function monitor_update_export_selection_params_array(ids) {
    var params = {
        'innovations_ids': ids,
        'filters': monitor_innovation_list_filters
    };
    $('.export-selection.async-export').attr('data-params_array', JSON.stringify(params));
}

function monitor_load_entete() {
    init_sticky_elements();
}


function init_sticky_elements() {
    setTimeout(function() {
        var stickies = [];
        $('.sticky-entete').each(function() {
            var data_sticky_to = parseInt($(this).attr('data-sticky-to'));
            if ($('#main').hasClass('beta-header-activated')) {
                data_sticky_to += 30;
            }
            var a_sticky = {
                'id': $(this).attr('id'),
                'target_top': data_sticky_to
            };
            stickies.push(a_sticky);
            $('.sticky-entete').addClass('active-stick');
        });

        $(window).scroll(function() {
            var windowTop = $(window).scrollTop();
            for (var i = 0; i < stickies.length; i++) {
                var $to_stick = $('#' + stickies[i]['id']);
                if ($to_stick.length > 0) {
                    var offset_top = ($('.sticky-ghost-' + stickies[i]['id']).length > 0) ? $('.sticky-ghost-' + stickies[i]['id']).offset().top : $to_stick.offset().top;
                    if (offset_top < (windowTop + stickies[i]['target_top'])) {
                        if (!$to_stick.hasClass('ghosted')) {
                            $to_stick.clone().removeClass('active-stick').addClass('sticky-ghost sticky-ghost-' + stickies[i]['id']).removeAttr('id').insertBefore('#' + stickies[i]['id']);
                            $to_stick.addClass('ghosted').addClass('sticky-fixed').css({ 'top': stickies[i]['target_top'] + 'px' });
                            $('.sticky-ghost-' + stickies[i]['id']).find('.content-filter').remove();
                        }
                    } else {
                        if ($to_stick.hasClass('ghosted')) {
                            $('.sticky-ghost-' + stickies[i]['id']).remove();
                            $to_stick.removeClass('ghosted').removeClass('sticky-fixed').removeAttr('style');
                        }
                    }
                }
            }
        });
    }, 50);
}

function monitor_get_html_template(tab) {
    tab = tab || 'performance-le';
    var out = [], o = -1;
    var classe = (check_if_user_is_hq() || check_if_user_is_management()) ? "full-content-monitor pri-tab" : "full-content-monitor";
    out[++o] = '<div class="' + classe + '">'; // Start full-content-monitor
    out[++o] = '<div class="element-sub-header-menu-tab">'; // Start element-sub-header-menu-tab
    out[++o] = '<div class="central-content text-align-right">'; // Start central-content
    if (check_if_user_is_hq() || check_if_user_is_management()) {
        out[++o] = '<ul class="pri-tab-menu color-white display-inline-block">';
        if (tab == 'dashboard') {
            out[++o] = '<li class="ui-tabs-active ui-state-active"><a href="#dashboard">Dashboard</a></li>';
            out[++o] = '<li><a href="#performance-le">Performance LE</a></li>';
        } else {
            out[++o] = '<li><a href="#dashboard">Dashboard</a></li>';
            out[++o] = '<li class="ui-tabs-active ui-state-active"><a href="#performance-le">Performance LE</a></li>';
        }
        out[++o] = '</ul>';
    }
    out[++o] = '<div class="content-buttons display-inline-block">'; // Start content-buttons
    out[++o] = '<button class="hub-button height-26 light-blue export-selection async-export margin-left-10" data-title="Exporting selection in PPT…" data-action="/api/export/innovations-ppt" data-type="innovations-ppt-selection"> <span class="icon-ddl"></span> <span>Export .ppt</span> </button>';
    out[++o] = '<button class="hub-button height-26 light-blue export-selection async-export margin-left-6" data-title="Exporting selection in Excel…" data-action="/api/export/innovations-excel" data-type="innovations-excel-selection"> <span class="icon-ddl"></span> <span>Export .xls</span> </button>';
    out[++o] = '</div>'; // End content-buttons
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End element-sub-header-menu-tab
    out[++o] = '<div class="fake-element-sub-header-menu-tab"></div>';

    if (check_if_user_is_hq() || check_if_user_is_management()) {
        out[++o] = '<div id="dashboard">'; // Start dashboard
        out[++o] = dashboard_get_html_template();
        out[++o] = '</div>'; // End dashboard
    }


    out[++o] = '<div id="performance-le">'; // Start Performance LE

    out[++o] = '<div class="content-monitor-consolidation background-color-gradient-161541">'; // Start content-monitor-consolidation
    out[++o] = '<div class="central-content no-responsive padding-top-20 padding-bottom-10 overflow-hidden">'; // Start central-content
    out[++o] = '<div id="pri-toast-monitor"></div>';
    out[++o] = '<div class="overflow-hidden margin-bottom-30">';
    out[++o] = '<div class="column-percent-45">'; // Start column-percent-60
    out[++o] = '<div class="font-montserrat-500 color-fff-0-4 font-size-20 line-height-1">'; // Start font-montserrat-500
    out[++o] = 'Portfolio ';
    out[++o] = ' <span class="color-4693C4-0-8"><span class="libelle_versus_date">A18</span></span>';
    out[++o] = '</div>'; // End font-montserrat-500
    out[++o] = '</div>'; // End column-percent-45
    out[++o] = '<div class="column-percent-55">'; // Start column-percent-40
    out[++o] = '<div class="font-montserrat-500 font-size-20 color-fff-0-4 line-height-1">';
    out[++o] = 'Consolidated performance <span class="libelle_current_le">LE1 19</span>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End column-percent-55
    out[++o] = '</div>';
    out[++o] = '<div class="column-percent-75">'; // Start column-percent-75
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden

    out[++o] = '<div class="column-percent-60">'; // Start column-percent-60
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-40 text-align-right padding-right-20">'; // Start column-percent-40
    out[++o] = '<div class="content-full-donut">';
    out[++o] = '<div class="content-movements main-stages margin-right-10 color-1eb3ea font-size-14"><div class="negative">0</div><div class="positive">0</div></div>';
    out[++o] = '<div class="content-donut content-donut-stages display-inline-block" style="width: 135px;height: 135px;">';
    out[++o] = '<canvas id="monitor-chartStages" width="135" height="135"></canvas>';
    out[++o] = '<div class="donut-inner-stage"><span>0</span> Projects</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '<div class="content-full-donut margin-top-17">';
    out[++o] = '<div class="content-movements other-stages margin-right-10 color-1eb3ea font-size-13"><div class="negative">0</div><div class="positive">0</div></div>';
    out[++o] = '<div class="content-donut content-donut-stages-other mini display-inline-block">';
    out[++o] = '<canvas id="monitor-chartStages-other" width="50" height="50"></canvas>';
    out[++o] = '<div class="donut-inner-stage"><span>0</span></div>';
    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '</div>'; // End column-percent-40
    out[++o] = '<div class="column-percent-60">'; // Start column-percent-60
    out[++o] = '<div class="consolidation-list-item-stage">';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End column-percent-60
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '</div>'; // End column-percent-60

    out[++o] = '<div class="column-percent-40">'; // Start column-percent-40
    out[++o] = '<div class="other-info-consolidation net_sales" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">'; // Start other-info-consolidation
    out[++o] = '<div class="libelle"><span>Net Sales</span></div>';
    out[++o] = '<div class="under-libelle"><span>0</span> k€ <div class="consolidation-movement positive">0 %</div></div>';
    out[++o] = '</div>'; // End other-info-consolidation
    out[++o] = '<div class="other-info-consolidation total_ap" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">'; // Start other-info-consolidation
    out[++o] = '<div class="libelle"><span>Total A&P</span>';
    out[++o] = '<div class="helper-button" data-target="total_ap">?</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="under-libelle"><span>0</span> k€ <div class="consolidation-movement positive">0 %</div></div>';
    out[++o] = '</div>'; // End other-info-consolidation
    out[++o] = '<div class="other-info-consolidation level_of_investment" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">'; // Start other-info-consolidation
    out[++o] = '<div class="libelle"><span class="color-210-0-7">A&P/Net Sales ratio</span>';
    out[++o] = '<div class="helper-button" data-target="level_of_investment">?</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="under-libelle color-fff-0-5"><span>0</span> % <div class="consolidation-movement positive">0 pts</div></div>';
    out[++o] = '</div>'; // End other-info-consolidation
    out[++o] = '</div>'; // End column-percent-40
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '</div>'; // End column-percent-75

    out[++o] = '<div class="column-percent-25">'; // Start column-percent-25
    out[++o] = '<div class="other-info-consolidation volume" data-percent="0" data-old-value="0" data-old-date="">'; // Start other-info-consolidation
    out[++o] = '<div class="libelle">';
    out[++o] = '<span>Volumes</span>';
    out[++o] = '</div>';
    out[++o] = '<div class="under-libelle"><span>0</span> k9Lcs <div class="consolidation-movement positive">0 %</div></div>';
    out[++o] = '</div>'; // End other-info-consolidation

    out[++o] = '<div class="other-info-consolidation caap" data-percent="0" data-old-value="0" data-old-date="">'; // Start other-info-consolidation
    out[++o] = '<div class="libelle">';
    out[++o] = '<span>CAAP</span>';
    out[++o] = '<div class="helper-button" data-target="total_caap">?</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="under-libelle"><span>0</span> k€ <div class="consolidation-movement positive">0 %</div></div>';
    out[++o] = '</div>'; // End other-info-consolidation

    out[++o] = '<div class="other-info-consolidation level_of_profitability" data-percent="0" data-old-value="0" data-old-date="">'; // Start other-info-consolidation
    out[++o] = '<div class="libelle">';
    out[++o] = '<span class="color-210-0-7">CM/Net Sales ratio</span>';
    out[++o] = '<div class="helper-button" data-target="level_of_profitability">?</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="under-libelle color-fff-0-5"><span>0</span> % <div class="consolidation-movement positive">0 pts</div></div>';
    out[++o] = '</div>'; // End other-info-consolidation
    out[++o] = '</div>'; // End column-percent-25
    out[++o] = '<div class="font-work-sans-500 color-03AFF0-0-8 font-size-12 text-align-right">Evolutions at current rate. All figures from <span class="libelle_current_fy">FY19</span> are restated for IFRS15 norm.</div>';
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End content-monitor-consolidation
    out[++o] = '<div class="monitor-content-upper-filters background-color-005095" id="monitor-upper-filters">';
    out[++o] = '<div class="overflow-hidden central-content">';
    out[++o] = '<div class="overflow-hidden">';
    out[++o] = '<div class="left">'; // Start left
    out[++o] = '<div class="filter-group-stage default active" data-target="early_stage">';
    out[++o] = '<div class="libelle font-montserrat-700">Early stage</div>';
    out[++o] = '<div class="fake-radio"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="filter-group-stage default active" data-target="in_market">';
    out[++o] = '<div class="libelle font-montserrat-700">In market</div>';
    out[++o] = '<div class="fake-radio"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="filter-group-stage" data-target="out_of_funnel">';
    out[++o] = '<div class="libelle font-montserrat-700">Out of funnel</div>';
    out[++o] = '<div class="fake-radio"></div>';
    out[++o] = '</div>';


    out[++o] = '</div>'; // End left
    out[++o] = '<div class="right">'; // Start right
    out[++o] = '<div class="filter-classification product active" data-target="Product">';
    out[++o] = '<div class="icon"></div>';
    out[++o] = '<div class="libelle font-montserrat-700">PRODUCTS</div>';
    out[++o] = '<div class="fake-radio"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="filter-classification service active" data-target="Service">';
    out[++o] = '<div class="icon"></div>';
    out[++o] = '<div class="libelle font-montserrat-700">SERVICES</div>';
    out[++o] = '<div class="fake-radio"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End right
    out[++o] = '</div>';
    out[++o] = '<div class="fake-border-bottom"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="position-relative box-sizing-content-box display-table width-percent-100">';
    out[++o] = '<div class="position-relative">';
    out[++o] = '<div class="position-relative min-height-400">';
    out[++o] = '<div class="background-color-005095 sticky-entete" id="monitor-table-thead" data-sticky-to="50">';
    out[++o] = '<div class="central-content">';
    out[++o] = '<table class="background-color-fff admin">';
    out[++o] = '<thead class="background-color-005095">';
    out[++o] = '<tr>';
    out[++o] = '<th id="filter-portfolio" class="text-align-left filter-portfolio width-100">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Portfolio</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-entity" class="width-100 text-align-left filter-entity">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Entity</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-title" class="text-align-left filter-title" style="min-width: 180px;">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Innovation name</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-brand" class="width-90 text-align-left filter-brand">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Brand</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-years_since_launch" class="text-align-left filter-years_since_launch width-70">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Years</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-growth_model" class="text-align-left filter-growth_model width-70">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Model</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-latest_volume" class="text-align-right filter-latest_volume width-130">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Volumes</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-latest_net_sales" class="text-align-right filter-latest_net_sales width-140">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Net Sales</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-latest_a_p" class="text-align-right filter-latest_a_p width-150">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>Total A&P</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';
    out[++o] = '<th id="filter-latest_caap" class="text-align-right filter-latest_caap width-150">';
    out[++o] = '<div class="inner-th"><div class="open-filter-action"></div><span>CAAP</span><span class="content-filter-highlight-action pitchou"></span></div>';
    out[++o] = '</th>';


    out[++o] = '</tr>';
    out[++o] = '</thead>';
    out[++o] = '</table>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // end sticky entete
    out[++o] = '<div class="background-color-fff">';
    out[++o] = '<div class="central-content">';
    out[++o] = '<table class="background-color-fff pri-table-list admin">';
    out[++o] = '<thead class="flat-thead background-color-005095">';
    out[++o] = '<tr>';
    out[++o] = '<th class="width-100"></th>';
    out[++o] = '<th class="width-100"></th>';
    out[++o] = '<th style="min-width: 180px;"></th>';
    out[++o] = '<th class="width-90"></th>';
    out[++o] = '<th class="width-70"></th>';
    out[++o] = '<th class="width-70"></th>';
    out[++o] = '<th class="width-130"></th>';
    out[++o] = '<th class="width-140"></th>';
    out[++o] = '<th class="width-150"></th>';
    out[++o] = '<th class="width-150"></th>';
    out[++o] = '</tr>';
    out[++o] = '</thead>';
    out[++o] = '<tbody>';
    out[++o] = '</tbody>';
    out[++o] = '</table>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    if (manage_data_innovations.length < monitor_innovation_list_limit) {
        out[++o] = '<div class="background-color-fff padding-vertical-30">';
        out[++o] = '</div>';
    }
    var custom_classe = (manage_data_innovations.length < monitor_innovation_list_limit) ? 'display-none' : '';
    out[++o] = '<div class="background-color-fff ' + custom_classe + '">';
    out[++o] = '<div class="central-content">';
    out[++o] = '<div class="padding-20 position-relative overflow-hidden">';
    out[++o] = '<div class="pri-pagination">';
    out[++o] = '<ul>';
    out[++o] = '<li class="prev disabled"><a class="no-smoothState"> &lt; </a></li>';
    out[++o] = '<li class="state-active"><a href="" class="opti-link">1</a></li>';
    out[++o] = '<li class="next disabled"><a class="no-smoothState"> &gt; </a></li>';
    out[++o] = '</ul>';
    out[++o] = '<div class="position-relative float-right">';
    out[++o] = '<select class="pri-select-pagination" name="limit">';
    out[++o] = '<option>50</option>';
    out[++o] = '<option>100</option>';
    out[++o] = '<option>150</option>';
    out[++o] = '<option>200</option>';
    out[++o] = '<option>250</option>';
    out[++o] = '</select>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '</div>';
    out[++o] = '</div>';

    out[++o] = '</div>'; // End Performance LE

    out[++o] = '<script type="text/javascript">';
    out[++o] = 'optListMonitor();';
    out[++o] = '</script>';
    out[++o] = '</div>'; // End full-content-monitor
    return out.join('');
}


/* Dashboard
-------------------------------------------------------------- */

/**
 * dashboard_get_html_template
 * @returns {string}
 */
function dashboard_get_html_template() {
    if (check_if_user_is_hq()) {
        return dashboard_get_hq_html_template();
    } else if (check_if_user_is_management()) {
        return dashboard_get_management_html_template();
    } else {
        return '';
    }
}

/**
 * dashboard_get_hq_html_template
 * @returns {string}
 */
function dashboard_get_hq_html_template() {
    var out = [], o = -1;
    out[++o] = '<div class="full-content-page-dashboard">'; // full-content-page-dashboard

    out[++o] = '<div class="background-color-gradient-161541 padding-top-30 padding-bottom-30">'; // entete
    out[++o] = '<div class="central-content">'; // central-content
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-fff margin-bottom-30">Export your innovation projects</div>';
    out[++o] = '<div class="overflow-hidden">'; // overflow-hidden
    out[++o] = '<div class="column-percent-30">'; // column-percent-30
    out[++o] = '<div class="font-montserrat-700 font-size-16 color-fff-0-8 margin-bottom-20">Quanti & Quali</div>';
    out[++o] = '<button class="hub-button height-26 light-blue async-export display-block margin-bottom-10" data-title="Exporting in complete Excel…" data-action="/api/export/innovations-complete-excel">Complete Excel (.xls)</button>';
    out[++o] = '<button class="hub-button height-26 light-blue async-export display-block" data-title="Exporting in Quali PPT…" data-action="/api/export/innovations-ppt">Power Point Quali (.ppt)</button>';
    out[++o] = '</div>'; // End column-percent-30
    out[++o] = '<div class="column-percent-40">'; // column-percent-40
    out[++o] = '<div class="font-montserrat-700 font-size-16 color-fff-0-8 margin-bottom-20">Team Matrix</div>';
    out[++o] = '<button class="hub-button height-26 light-blue async-export display-block margin-bottom-10" data-title="Exporting Team Matrix Update…" data-action="/api/export/team-matrix-update">Team Matrix update (.xls)</button>';
    out[++o] = '<button class="hub-button height-26 light-blue async-export display-block" data-title="Exporting Team Matrix Update without duplicate…" data-action="/api/export/team-matrix-update-without-duplicate">Team Matrix update without duplicate (.xls)</button>';
    out[++o] = '</div>'; // End column-percent-40
    out[++o] = '<div class="column-percent-30">'; // column-percent-30
    out[++o] = '<div class="font-montserrat-700 font-size-16 color-fff-0-8 margin-bottom-20">Users</div>';
    out[++o] = '<button class="hub-button height-26 light-blue async-export display-block margin-bottom-10" data-title="Exporting in Excel…" data-action="/api/export/active-users">Active users (.xls)</button>';
    out[++o] = '<button class="hub-button height-26 light-blue async-export display-block" data-title="Exporting in Excel…" data-action="/api/export/newsletter-users">Newsletter users (.xls)</button>';
    out[++o] = '</div>'; // End column-percent-30
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End entete
    out[++o] = '<div class="background-color-f2f2f2 padding-top-40 padding-bottom-40">'; // background-color-f2f2f2
    out[++o] = '<div class="central-content">'; // central-content
    out[++o] = '<div class="overflow-hidden">'; // overflow-hidden

    out[++o] = '<div class="column-percent-50 content-monitor-innovations_new_back">'; // column-percent-50
    out[++o] = '<div class="padding-20 background-color-fff margin-right-20 margin-bottom-40">'; // background-color-fff
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = 'New/back in the pipeline';
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 display-none button-dashboard-view-all" data-type="new_back">View all</button>';
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End column-percent-50

    out[++o] = '<div class="column-percent-50 content-monitor-innovations_out">'; // column-percent-50
    out[++o] = '<div class="padding-20 background-color-fff margin-left-20 margin-bottom-40">'; // background-color-fff
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = 'Out of pipeline';
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 display-none button-dashboard-view-all" data-type="out">View all</button>';
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End column-percent-50

    out[++o] = '<div class="column-percent-50 content-monitor-innovations_in_market_date_modifications">'; // column-percent-50
    out[++o] = '<div class="padding-20 background-color-fff margin-right-20 margin-bottom-40">'; // background-color-fff
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = 'In market date modification';
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 display-none button-dashboard-view-all" data-type="in_market_update">View all</button>';
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End column-percent-50

    out[++o] = '<div class="column-percent-50 content-monitor-innovations_in_market_soon">'; // column-percent-50
    out[++o] = '<div class="padding-20 background-color-fff margin-left-20 margin-bottom-40">'; // background-color-fff
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = 'In market soon';
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 display-none button-dashboard-view-all" data-type="in_market_soon">View all</button>';
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End column-percent-50

    out[++o] = '<div class="column-percent-50 content-monitor-innovations_incomplete_financial_data">'; // column-percent-50
    out[++o] = '<div class="padding-20 background-color-fff margin-right-20">'; // background-color-fff
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = 'Incomplete financial data capture';
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 display-none button-dashboard-view-all" data-type="incomplete_financial_data">View all</button>';
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End column-percent-50


    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End background-color-f2f2f2
    out[++o] = '</div>'; // End  full-content-page-dashboard
    out[++o] = '<div class="full-content-page-dashboard-detail display-none"></div>';
    return out.join('');
}


/**
 * dashboard_get_management_html_template
 * @returns {string}
 */
function dashboard_get_management_html_template() {
    var out = [], o = -1;
    out[++o] = '<div class="full-content-page-dashboard">'; // full-content-page-dashboard

    out[++o] = '<div class="background-color-f2f2f2 padding-top-40 padding-bottom-40">'; // background-color-f2f2f2
    out[++o] = '<div class="central-content">'; // central-content
    out[++o] = '<div class="overflow-hidden">'; // overflow-hidden

    out[++o] = '<div class="column-percent-50 content-monitor-innovations_new">'; // column-percent-50
    out[++o] = '<div class="padding-20 background-color-fff margin-right-20 margin-bottom-40">'; // background-color-fff
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = 'New in the pipeline';
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 display-none button-dashboard-view-all" data-type="new">View all</button>';
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End column-percent-50

    out[++o] = '<div class="column-percent-50 content-monitor-innovations_in_market_soon">'; // column-percent-50
    out[++o] = '<div class="padding-20 background-color-fff margin-left-20 margin-bottom-40">'; // background-color-fff
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = 'In market soon';
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 display-none button-dashboard-view-all" data-type="in_market_soon">View all</button>';
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End column-percent-50


    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End background-color-f2f2f2
    out[++o] = '</div>'; // End  full-content-page-dashboard
    out[++o] = '<div class="full-content-page-dashboard-detail display-none"></div>';
    return out.join('');
}

/**
 * dashboard_init.
 */
function dashboard_init() {
    if (!check_if_user_is_hq() && !check_if_user_is_management()) {
        return;
    }
    if (request_dashboard_init != null) {
        request_dashboard_init.abort();
    }
    request_dashboard_init = $.ajax({
        url: '/api/dashboard/data',
        type: "POST",
        data: {},
        dataType: 'json',
        success: function (response, statut) {
            if (response['status'] == 'error') {
                add_flash_message('error', response['message']);
            } else {
                var cases = [
                    'innovations_new_back',
                    'innovations_new',
                    'innovations_out',
                    'innovations_in_market_date_modifications',
                    'innovations_in_market_soon',
                    'innovations_incomplete_financial_data'
                ];
                for (var i = 0; i < cases.length; i++) {
                    var $target = $(".content-monitor-" + cases[i]);
                    if ($target.length > 0) {
                        $target.find(".table-dashboard tbody").append(response[cases[i]]);
                        $target.find('.button-dashboard-view-all').removeClass('display-none');
                        $target.find(".dashboard-loader").hide();
                    }
                }
                init_tooltip();
            }
        },
        error: function(resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
        }
    });
}

function dashboard_get_detail_by_type(type) {
    switch (type) {
        case 'new_back':
            return 'New/back in the pipeline';
        case 'new':
            return 'New in the pipeline';
        case 'out':
            return 'Out of pipeline';
        case 'in_market_soon':
            return 'In market soon';
        case 'in_market_update':
            return 'In market date modification';
        case 'incomplete_financial_data':
            return 'Incomplete financial data capture';
        default:
            return 'Innovations in the pipeline';
    }
}

function dashboard_detail_init(type) {
    var link = "/content/dashboard/" + type;
    var out = [], o = -1;
    out[++o] = '<div class="background-color-f2f2f2 padding-top-40 padding-bottom-40">';
    out[++o] = '<div class="central-content padding-20 background-color-fff">';
    out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 margin-bottom-20">';
    out[++o] = dashboard_get_detail_by_type(type);
    out[++o] = '</div>';
    out[++o] = '<table class="table-dashboard content-dashboard-detail" data-offset="0" data-limit="50" data-type="' + type + '"><tbody></tbody></table>';
    out[++o] = '<div class="dashboard-detail-loader margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 load-more-dashboard-detail display-none">Load more</button>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    opti_update_url(link, 'list-monitor');
    $('html').addClass('background-color-f2f2f2');
    update_menu_active('menu-li-monitor');
    changePageTitle(dashboard_get_detail_by_type(type) + ' | ' + global_wording['title']);
    $('.full-content-page-dashboard-detail').html(out.join(''));
    $('.full-content-page-dashboard-detail').removeClass('display-none');
    $('.full-content-page-dashboard').addClass('display-none');
    dashboard_detail_load_more();
    update_main_classes(main_classes);
    $('#main').removeClass('loading');
    window.scrollTo(0, 0);

}

function dashboard_detail_load_more() {
    $(".dashboard-detail-loader").show();
    $('.load-more-dashboard-detail').addClass('display-none');

    if (request_dashboard_detail != null) {
        request_dashboard_detail.abort();
    }
    var type = $('.content-dashboard-detail').attr('data-type');
    var offset = parseInt($('.content-dashboard-detail').attr('data-offset'));
    var limit = parseInt($('.content-dashboard-detail').attr('data-limit'));
    request_dashboard_detail = $.ajax({
        url: '/api/dashboard/detail/data',
        type: "POST",
        data: { 'type': type, 'offset': offset, 'limit': limit },
        dataType: 'json',
        success: function(response, statut) {
            if (response['status'] == 'error') {
                add_flash_message('error', response['message']);
            } else {
                $('.content-dashboard-detail tbody').append(response['data']);
                $(".dashboard-detail-loader").hide();
                $(".content-dashboard-detail").attr('data-offset', (offset + response['count']));
                if (response['count'] == limit) {
                    $('.load-more-dashboard-detail').removeClass('display-none');
                }
                init_tooltip();
            }
        },
        error: function(resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
        }
    });
}