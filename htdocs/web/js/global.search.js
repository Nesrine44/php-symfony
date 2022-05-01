var search_request = null;
var search_result_request = null;
var search_result_searchWord = null;
var search_result_tag = null;
var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();


/**
 * closeSearchBar
 */
function closeSearchBar(with_popup) {
    with_popup = (typeof with_popup != 'undefined') ? with_popup : true;
    $('#true-search-input').val('');
    $('.content-search-popup .hub-button').prop('disabled', true).removeAttr('data-target');
    if (with_popup) {
        close_popup('.element-search-popup');
    }
}

/**
 * search by type
 * @param search
 * @param type
 * @return {*}
 */
function search_by_type(search, type, limit) {
    var default_limit = 1000000000;
    var ret = [];
    limit = (typeof  limit != 'undefined') ? limit : default_limit;
    var listinnovationsDC = new DataCollection(search_data_innovations);
    if (type == 'title') {
        if (limit < default_limit) {
            ret = listinnovationsDC.query().filter({title__startsWith: search}).order('title', false).limit(0, limit).values();
            if (ret.length == limit) {
                return ret;
            }
        }
        return listinnovationsDC.query().filter({title__icontains: search}).order('title', false).limit(0, limit).values();
    } else if (type == 'story') {
        if (limit < default_limit) {
            ret = listinnovationsDC.query().filter({proper__story__startsWith: search}).order('proper__story', false).limit(0, limit).values();
            if (ret.length == limit) {
                return ret;
            }
        }
        return listinnovationsDC.query().filter({proper__story__icontains: search}).order('proper__story', false).limit(0, limit).values();
    } else if (type == 'brand') {
        if (limit < default_limit) {
            ret = listinnovationsDC.query().filter({proper__brand_title__startsWith: search}).order('proper__brand_title', false).limit(0, limit).values();
            if (ret.length == limit) {
                return ret;
            }
        }
        return listinnovationsDC.query().filter({proper__brand_title__icontains: search}).order('proper__brand_title', false).limit(0, limit).values();
    } else if (type == 'entity') {
        if (limit < default_limit) {
            ret = listinnovationsDC.query().filter({proper__entity_title__startsWith: search}).order('proper__entity_title', false).limit(0, limit).values();
            if (ret.length == limit) {
                return ret;
            }
        }
        return listinnovationsDC.query().filter({proper__entity_title__icontains: search}).order('proper__entity_title', false).limit(0, limit).values();
    } else if (type == 'contact') {
        if (limit < default_limit) {
            ret = listinnovationsDC.query().filter({proper__contact_username__startsWith: search}).order('proper__contact_username', false).limit(0, limit).values();
            if (ret.length == limit) {
                return ret;
            }
        }
        return listinnovationsDC.query().filter({proper__contact_username__icontains: search}).order('proper__contact_username', false).limit(0, limit).values();
    }
    return ret;
}

/**
 * search contacts
 * @param search
 * @return {*}
 */
function search_contacts(search, limit) {
    var default_limit = 1000000000;
    var ret = [];
    limit = (typeof  limit != 'undefined') ? limit : default_limit;
    var listContacts = new DataCollection(other_datas['contacts_json']);
    if (limit < default_limit) {
        ret = listContacts.query().filter({
            text__startsWith: search,
            is_pr_employee: true
        }).order('text', false).limit(0, limit).values();
        if (ret.length == limit) {
            return ret;
        }
    }
    return listContacts.query().filter({
        text__icontains: search,
        is_pr_employee: true
    }).order('text', false).limit(0, limit).values();
}


/**
 * get_search_url_by_type
 * @param search
 * @param type
 * @return {string}
 */
function get_search_url_by_type(search, type) {
    var innovations = search_by_type(search, type);
    var url = "/";
    if ($('select.pri-select-blue[name=limit]').val()) {
        url += "?=limit=" + jQuery('select.pri-select-blue[name=limit]').val();
    }
    url += ( url == "/") ? "?" : "&";
    url += "filter[current_stage]=1,2,3,4,5,7";
    url += "&filter[" + type + "]=";
    var ids = [];
    for (var key in innovations) {
        if (innovations.hasOwnProperty(key)) {
            var innovation = innovations[key];
            if (type == 'title') {
                ids.push(innovation['id']);
            } else if (type == 'brand') {
                if (!in_array(innovation['brand']['id'], ids)) {
                    ids.push(innovation['brand']['id']);
                }
            } else if (type == 'entity') {
                if (!in_array(innovation['entity']['id'], ids)) {
                    ids.push(innovation['entity']['id']);
                }
            }
        }
    }
    url += ids.join();
    return url;
}

function search_get_loading_innovation(i) {
    i = i || 1;
    var out = [], o = -1;
    out[++o] = '';
    out[++o] = '<div class="mosaique-innovation no-hover">';
    out[++o] = '<div class="content">';
    var id = ((i % 6) + 1);
    var background = '/images/default/product/default-' + id + '.png';
    out[++o] = '<div class="picture position-relative" style="background-image:url(' + background + ');">';
    out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="fake-text  position-relative">';
    out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

function search_get_loading_picture(i) {
    i = i || 1;
    var out = [], o = -1;
    out[++o] = '';
    out[++o] = '<div class="mosaique-picture no-hover">';
    out[++o] = '<div class="content">';
    var id = ((i % 6) + 1);
    var background = '/images/default/product/default-' + id + '.png';
    out[++o] = '<div class="fake-picture" style="background-image:url(' + background + ');">';
    out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="fake-text position-relative">';
    out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

function search_get_picture(picture) {
    var out = [], o = -1;
    out[++o] = '<div class="mosaique-picture no-hover">';
    out[++o] = '<div class="content">';
    out[++o] = '<div class="content-picture">';
    out[++o] = '<a class="venobox" href="' + picture['picture'] + '"  data-gall="search-result">';
    out[++o] = '<img src="' + picture['picture'] + '" />';
    out[++o] = '</a>';
    out[++o] = '</div>';
    out[++o] = '<div class="infos position-relative font-work-sans-400 font-size-13 line-height-1-62 color-787878">';
    out[++o] = 'From <a title="'+picture['innovation_title']+'" data-title="'+picture['innovation_title']+'" href="/explore/' + picture['innovation_id'] + '" data-css-class="link_explore_detail"  class="link_explore_detail save-search-action link-underline color-787878 text-transform-uppercase tooltip-on-ellipsis on-parent">' + picture['innovation_title'] + '</a>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}


function before_close_search_popup() {
    if (search_request != null)
        search_request.abort();
}


/**
 * displaySearchResults
 * @param searchWord
 */
function displaySearchResults(searchWord) {
    $('.content-search-popup .hub-button').prop('disabled', false).attr('data-target', searchWord);
    $('.search-result').html(get_search_loader());
    if (search_request != null)
        search_request.abort();
    var data = {search: searchWord, 'limit': 3};
    data['token'] = $('#hub-token').val();
    search_request = $.ajax({
        url: '/api/search',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response, statut) {
            if (response['status'] == 'error') {
                $('.search-result').html(get_search_error(response['message']));
            } else if (response['no_result']) {
                $('.search-result').html(get_search_no_result());
            } else {
                $('.search-result').html(get_search_global_html(response, searchWord));
                load_background_images();
            }
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            $('.search-result').html(get_search_error(resultat.status + ' ' + e));
        }
    });
}


function before_open_search_popup(){
    $('#true-search-input').val('');
    $('.content-search-popup .hub-button').prop('disabled', true).removeAttr('data-target');
    if (search_request != null)
        search_request.abort();
    displaySearchHistory();

}

/**
 * displaySearchHistory
 */
function displaySearchHistory() {
    delay(function () {
        $('.content-search-popup .hub-button').prop('disabled', true).removeAttr('data-target');
        var out = [], o = -1;
        out[++o] = '<div class="content-search-history padding-bottom-40 position-relative">'; // Start content-search-history
        for(var i = 0; i < user_search_history.length; i++){
            var history = user_search_history[i];
            out[++o] = '<a href="'+history['url']+'" class="'+history['css_class']+'" data-title="'+history['title']+'">'+history['title']+'</a>';
        }
        out[++o] = '</div>'; // End content-search-history
        $('.search-result').html(out.join(''));
    }, 10);
}

/**
 * get_search_loader
 * @returns {string}
 */
function get_search_error(message) {
    var out = [], o = -1;
    out[++o] = '<div class="content-search-all-results padding-bottom-40 position-relative">'; // Start content-search-all-results
    out[++o] = '<div class="font-montserrat-700 color-000 line-height-1 font-size-26 padding-horizontal-30 text-align-center ">An error appeared…</div>';
    out[++o] = '<div class="font-work-sans-300 color-000 line-height-1 font-size-18 padding-horizontal-30 text-align-center margin-top-10 margin-bottom-60">' + message + '</div>';
    out[++o] = '</div>'; // End content-search-all-results
    return out.join('');
}

/**
 * search_get_html_no_result
 * @returns {string}
 */
function search_get_html_no_result() {
    return '<div class="font-montserrat-700 color-000 line-height-1 font-size-18 padding-vertical-20">No result found…</div>';
}


/**
 * get_search_no_result
 * @returns {string}
 */
function get_search_no_result() {
    var out = [], o = -1;
    out[++o] = '<div class="content-search-all-results padding-bottom-40">'; // Start content-search-all-results
    out[++o] = '<div class="font-montserrat-700 color-000 line-height-1 font-size-26 padding-horizontal-30 text-align-center ">No result found…</div>';
    var a_collection = new DataCollection(search_data_innovations);
    var trending_tags = mosaique_get_trendings_tags_by_innovation_collection(a_collection, false);
    if (trending_tags.length > 0) {
        out[++o] = '<div class="font-work-sans-300 color-000 line-height-1 font-size-18 padding-horizontal-30 text-align-center margin-top-10 margin-bottom-60">Why not explore the most trending tags?</div>';
        out[++o] = '<div class="content-tags text-align-center margin-bottom-10">'; // Start content-tags
        for (var i = 0; i < trending_tags.length; i++) {
            var url = '/content/search/tags?search=' + trending_tags[i];
            out[++o] = '<a class="trending-tag transition save-search-action link-to-search-results-tag" data-css-class="link-to-search-results-tag" href="' + url + '" data-title="' + trending_tags[i] + '">' + trending_tags[i] + '</a>';
        }
        out[++o] = '</div>'; // End content-tags
    }
    out[++o] = '</div>'; // End content-search-all-results

    return out.join('');
}

/**
 * get_search_loader
 * @returns {string}
 */
function get_search_loader() {
    var out = [], o = -1;
    out[++o] = '<div class="content-search-all-results loading padding-bottom-40 position-relative">'; // Start content-search-all-results
    out[++o] = '<div class="left-column">'; // Start left-column
    out[++o] = '<div class="padding-20">'; // Start padding-20
    out[++o] = '<div class="fake-text-filter">';
    out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content-tags">'; // Start content-tags
    for (var i = 0; i < 5; i++) {
        out[++o] = '<div class="trending-tag no-hover transition">';
        out[++o] = '<div class="fake-text"></div>';
        out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End content-tags
    out[++o] = '</div>'; // End padding-20
    out[++o] = '</div>'; // End left-column

    out[++o] = '<div class="right-column">'; // Start right-column
    out[++o] = '<div class="padding-20">'; // Start padding-20
    out[++o] = '<div class="fake-text-explore">';
    out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
    out[++o] = '</div>';
    out[++o] = '<div class="content-search-innovations margin-bottom-40 overflow-hidden">'; // Start content-search-innovations
    for (var i = 0; i < 3; i++) {
        out[++o] = search_get_loading_innovation(i);
    }
    out[++o] = '</div>'; // End content-search-innovations
    out[++o] = '</div>'; // End padding-20
    out[++o] = '</div>'; // End right-column
    out[++o] = '</div>'; // End content-search-all-results

    return out.join('');
}

/**
 * search_get_all_brands_for_filters
 * @param innovations
 * @returns {Array}
 */
function search_get_all_brands_for_filters(innovations) {
    var ret = [];
    var brands_ids = [];
    var a_collection = new DataCollection(innovations);
    a_collection.query().each(function (row, index) {
        var brand = row['brand'];
        if (brand && brand['id'] && !in_array(brand['id'], brands_ids)) {
            brands_ids.push(brand['id']);
            brand['nb'] = mosaique_get_nb_filtered_list_for_collection_family_and_target(a_collection, 'brand', brand['id']);
            ret.push(brand);
        }
    });
    var ordered_collection = new DataCollection(ret);
    return ordered_collection.query().order('nb', true).limit(0, 5).values();
}


/**
 * get search innovation html
 * @param search
 * @return {string}
 */
function get_search_global_html(response, search) {
    var out = [], o = -1;

    var innovations_brand = response['brands'];
    var trending_tags = response['trending_tags'];
    var displayed_innovations = response['innovations'];
    var displayed_contacts = response['users'];
    var nb_results = response['nb_results'];
    out[++o] = '<div class="content-search-all-results padding-bottom-40">'; // Start content-search-all-results
    out[++o] = '<div class="left-column">'; // Start left-column
    if ((innovations_brand.length + trending_tags.length) == 0) {
        out[++o] = '<br>';
    }
    if (innovations_brand.length > 0) {
        out[++o] = '<div class="content-filters padding-vertical-20 padding-right-20 padding-left-40 background-color-f2f2f2">'; // Start content-filters
        out[++o] = '<div class="font-work-sans-600 color-000 line-height-1-5 font-size-20 margin-bottom-10">Filters</div>';
        out[++o] = '<div class="separator"></div>';
        if (innovations_brand.length > 0) {
            out[++o] = '<div class="font-work-sans-500 color-000 line-height-1 font-size-15 margin-bottom-20">Brands</div>';
            out[++o] = '<div class="content-links margin-bottom-20">'; // Start content-links
            for (var i = 0; i < innovations_brand.length; i++) {
                var url = '/content/explore?filter[brand]=' + innovations_brand[i]['id'];
                var nb = mosaique_get_nb_filtered_list_for_collection_family_and_target(explore_initial_innovation_collection, 'brand', innovations_brand[i]['id']);
                out[++o] = '<a class="filter-link font-work-sans-400 link-list-explore-href" href="' + url + '">';
                out[++o] = '<span class="nb">' + nb + '</span>';
                out[++o] = '<span class="libelle transition">' + innovations_brand[i]['title'] + '</span>';
                out[++o] = '</a>';
            }
            out[++o] = '</div>'; // End  content-links
        }
        out[++o] = '</div>'; // End content-filters
    }


    if (trending_tags.length > 0) {
        out[++o] = '<div class="content-trending-tags margin-top-40 padding-horizontal-40">'; // Start content-trending-tags
        out[++o] = '<div class="font-work-sans-600 color-000 line-height-1-5 font-size-20 margin-bottom-20">Trending tags</div>';
        out[++o] = '<div class="content-tags">'; // Start content-tags
        for (var i = 0; i < trending_tags.length; i++) {
            var url = '/content/search/tags?search=' + trending_tags[i]['text'];
            out[++o] = '<a class="trending-tag transition save-search-action link-to-search-results-tag" data-css-class="link-to-search-results-tag" href="' + url + '" data-title="' + trending_tags[i]['text'] + '">' + trending_tags[i]['text'] + '</a>';
        }
        out[++o] = '</div>'; // End content-tags
        out[++o] = '</div>';  // End content-trending-tags
    }

    out[++o] = '</div>'; // End left-column

    out[++o] = '<div class="right-column">'; // Start right-column
    out[++o] = '<div class="padding-20">'; // Start padding-20
    if (displayed_innovations.length > 0) {
        out[++o] = '<div class="font-work-sans-600 color-000 line-height-1 font-size-26 margin-bottom-10">Explore</div>';
        out[++o] = '<div class="content-search-innovations margin-bottom-40 overflow-hidden">'; // Start content-search-innovations
        for (var i = 0; i < displayed_innovations.length; i++) {
            var an_innovation = displayed_innovations[i];
            out[++o] = explore_get_innovation_html(an_innovation, false, true);
        }
        out[++o] = '</div>'; // End content-search-innovations
    }

    if (displayed_contacts.length > 0) {
        out[++o] = '<div class="font-work-sans-600 color-000 line-height-1 font-size-26 margin-bottom-10">Connect</div>';
        out[++o] = '<div class="content-search-contacts margin-bottom-40 overflow-hidden">'; // Start content-search-innovations
        for (var i = 0; i < displayed_contacts.length; i++) {
            var a_contact = displayed_contacts[i];
            out[++o] = connect_get_user_html(a_contact, true);
        }
        out[++o] = '</div>'; // End content-search-innovations
    }
    if ((displayed_innovations.length + displayed_contacts.length) > 0) {
        out[++o] = '<div class="text-align-center">'; // Start text-align-center
        out[++o] = '<a href="/content/search?search=' + search + '" data-target="' + search + '" class="hub-button link-to-search-results">See '+nb_results+' results</a>';
        out[++o] = '</div>'; // End text-align-center
    }
    out[++o] = '</div>'; // End padding-20
    out[++o] = '</div>'; // End right-column

    out[++o] = '</div>'; // End content-search-all-results

    return out.join('');
}


function goToSearchResultsPage(searchWord, update_url) {
    try {
        update_url = (typeof update_url != 'undefined') ? update_url : true;
        before_opening_page();
        var link = "/content/search?search=" + searchWord;
        $('#main').addClass('loading');
        if (update_url) {
            opti_update_url(link, 'search-results');
        }
        $('html, body').addClass('background-color-fff');
        update_menu_active();
        changePageTitle('"' + searchWord + '" search results | ' + global_wording['title']);
        update_main_classes(main_classes);
        $('#pri-main-container-without-header').html(search_results_get_html_template(searchWord));
        search_results_init(searchWord);
    } catch (error) {
        init_unknow_error_view(error);
    }
}

function goToSearchResultsTagsPage(tag, update_url) {
    try {
        update_url = (typeof update_url != 'undefined') ? update_url : true;
        before_opening_page();
        var link = "/content/search/tags?search=" + tag['title'];
        $('#main').addClass('loading');
        if (update_url) {
            opti_update_url(link, 'search-results');
        }
        $('html, body').addClass('background-color-fff');
        changePageTitle(tag['title'] + ' search results | ' + global_wording['title']);
        update_main_classes(main_classes);
        $('#pri-main-container-without-header').html(search_results_tags_get_html_template(tag));
        search_results_tags_init(tag);
    } catch (error) {
        init_unknow_error_view(error);
    }
}

/**
 * search_results_get_html_template
 * @param searchWord
 * @returns {string}
 */
function search_results_get_html_template(searchWord) {
    var out = [], o = -1;
    out[++o] = '<div class="full-content-search-result">'; // Start full-content-compare-list

    out[++o] = '<div class="background-color-gradient-161541 padding-top-20 padding-bottom-20">'; // Start Entete
    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<div class="font-work-sans-500 font-size-14 color-fff-0-7">Results for</div>';
    out[++o] = '<div class="font-montserrat-700 font-size-30 line-height-1 color-fff-0-9">"' + searchWord + '"</div>';
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End Entete

    out[++o] = '<div class="background-color-fff tab-search">'; // Start background-color-fff
    out[++o] = '<div class="tab-menu">'; // Start tab-menu
    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<ul>';
    out[++o] = '<li class="ui-tabs-active ui-state-active"><a href="#explore">Explore</a></li>';
    out[++o] = '<li><a href="#connect">Connect</a></li>';
    out[++o] = '<li><a href="#pictures">Pictures</a></li>';
    out[++o] = '</ul>';
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End tab-menu
    out[++o] = '<div class="central-content padding-top-20 padding-bottom-60">'; // central-content

    out[++o] = '<div id="explore">'; // Start Explore
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    for (var i = 0; i < 20; i++) {
        out[++o] = search_get_loading_innovation(i);
    }
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '<div class="search-result-loader margin-top-20 display-none">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 load-more-search display-none" data-target="innovations">Load more</button>';
    out[++o] = '</div>'; // End Explore

    out[++o] = '<div id="connect">'; // Start Connect
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    for (var i = 0; i < 20; i++) {
        out[++o] = connect_get_user_html();
    }
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '<div class="search-result-loader margin-top-20 display-none">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 load-more-search display-none" data-target="users">Load more</button>';
    out[++o] = '</div>'; // End Connect

    out[++o] = '<div id="pictures">'; // Start Pictures
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    for (var i = 0; i < 20; i++) {
        out[++o] = search_get_loading_picture(i);
    }
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '<div class="search-result-loader  margin-top-20 display-none">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 load-more-search display-none" data-target="pictures">Load more</button>';
    out[++o] = '</div>'; // End Pictures


    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End background-color-fff


    out[++o] = '</div>';
    return out.join('');
}

/**
 * search_results_get_html_template
 * @param searchWord
 * @returns {string}
 */
function search_results_tags_get_html_template(tag) {
    var out = [], o = -1;
    out[++o] = '<div class="full-content-search-result">'; // Start full-content-compare-list

    out[++o] = '<div class="background-color-gradient-161541 padding-top-20 padding-bottom-20">'; // Start Entete
    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<div class="font-work-sans-500 font-size-14 color-fff-0-7">Tagged in</div>';
    out[++o] = '<div class="font-montserrat-700 font-size-30 line-height-1 color-fff-0-9">' + tag['title'] + '</div>';
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End Entete

    out[++o] = '<div class="background-color-fff tab-search">'; // Start background-color-fff
    out[++o] = '<div class="tab-menu">'; // Start tab-menu
    out[++o] = '<div class="central-content">'; // Start central-content
    out[++o] = '<ul>';
    out[++o] = '<li class="ui-tabs-active ui-state-active"><a href="#explore">Explore</a></li>';
    out[++o] = '<li><a href="#pictures">Pictures</a></li>';
    out[++o] = '</ul>';
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End tab-menu
    out[++o] = '<div class="central-content padding-top-20 padding-bottom-60">'; // central-content

    out[++o] = '<div id="explore">'; // Start Explore
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    for (var i = 0; i < 20; i++) {
        out[++o] = search_get_loading_innovation(i);
    }
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '<div class="search-result-loader margin-top-20 display-none">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 load-more-search-tag display-none" data-target="innovations">Load more</button>';
    out[++o] = '</div>'; // End Explore

    out[++o] = '<div id="pictures">'; // Start Pictures
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    for (var i = 0; i < 20; i++) {
        out[++o] = search_get_loading_picture(i);
    }
    out[++o] = '</div>'; // End overflow-hidden
    out[++o] = '<div class="search-result-loader  margin-top-20 display-none">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 load-more-search-tag display-none" data-target="pictures">Load more</button>';
    out[++o] = '</div>'; // End Pictures


    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End background-color-fff


    out[++o] = '</div>';
    return out.join('');
}

/**
 * search_results_display_initial
 * @param response
 * @param limit
 */
function search_results_display_initial(response, limit) {
    if (response.hasOwnProperty('innovations') && response['innovations'].length > 0) {
        var out = [], o = -1;
        for (var i = 0; i < response['innovations'].length; i++) {
            var an_innovation = response['innovations'][i];
            out[++o] = explore_get_innovation_html(an_innovation, false, true);
        }
        $('.full-content-search-result #explore > .overflow-hidden').html(out.join(''));
        if (response['innovations'].length >= limit) {
            $('.full-content-search-result #explore .hub-button').removeClass('display-none').attr('data-offset', limit).attr('data-limit', limit);
        }
    } else {
        $('.full-content-search-result #explore > .overflow-hidden').html(search_get_html_no_result());
    }

    if (response.hasOwnProperty('users') &&  response['users'].length > 0) {
        var out = [], o = -1;
        for (var i = 0; i < response['users'].length; i++) {
            var a_contact = response['users'][i];
            out[++o] = connect_get_user_html(a_contact, true);
        }
        $('.full-content-search-result #connect > .overflow-hidden').html(out.join(''));
        if (response['users'].length >= limit) {
            $('.full-content-search-result #connect .hub-button').removeClass('display-none').attr('data-offset', limit).attr('data-limit', limit);
        }
    } else {
        $('.full-content-search-result #connect > .overflow-hidden').html(search_get_html_no_result());
    }

    if (response.hasOwnProperty('pictures') &&  response['pictures'].length > 0) {
        var out = [], o = -1;
        for (var i = 0; i < response['pictures'].length; i++) {
            var a_picture = response['pictures'][i];
            out[++o] = search_get_picture(a_picture);
        }
        $('.full-content-search-result #pictures > .overflow-hidden').html(out.join(''));
        if (response['innovations'].length >= limit) {
            $('.full-content-search-result #pictures .hub-button').removeClass('display-none').attr('data-offset', limit).attr('data-limit', limit);
        }
    } else {
        $('.full-content-search-result #pictures > .overflow-hidden').html(search_get_html_no_result());
    }

    load_background_images();
    init_tooltip();
    $('.venobox').venobox({
        infinigall: true            // default: false
    });
}



/**
 * search_results_display_more
 * @param response
 * @param limit
 */
function search_results_display_more(type, response, offset, limit) {
    if (type == 'innovations') {
        if (response['innovations'].length > 0) {
            var out = [], o = -1;
            for (var i = 0; i < response['innovations'].length; i++) {
                var an_innovation = response['innovations'][i];
                out[++o] = explore_get_innovation_html(an_innovation, false, true);
            }
            $('.full-content-search-result #explore > .overflow-hidden').append(out.join(''));
            $('.full-content-search-result #explore .search-result-loader').addClass('display-none');
            if (response['innovations'].length >= limit) {
                $('.full-content-search-result #explore .hub-button').removeClass('display-none').attr('data-offset', (offset + response['innovations'].length));
            }
        }
    }

    if (type == 'users') {
        if (response['users'].length > 0) {
            var out = [], o = -1;
            for (var i = 0; i < response['users'].length; i++) {
                var a_contact = response['users'][i];
                out[++o] = connect_get_user_html(a_contact, true);
            }
            $('.full-content-search-result #connect > .overflow-hidden').append(out.join(''));
            $('.full-content-search-result #connect .search-result-loader').addClass('display-none');
            if (response['users'].length >= limit) {
                $('.full-content-search-result #connect .hub-button').removeClass('display-none').attr('data-offset', (offset + response['users'].length));
            }
        }
    }
    if (type == 'pictures') {
        if (response['pictures'].length > 0) {
            var out = [], o = -1;
            for (var i = 0; i < response['pictures'].length; i++) {
                var a_picture = response['pictures'][i];
                out[++o] = search_get_picture(a_picture);
            }
            $('.full-content-search-result #pictures > .overflow-hidden').append(out.join(''));
            $('.full-content-search-result #pictures .search-result-loader').addClass('display-none');
            if (response['innovations'].length >= limit) {
                $('.full-content-search-result #pictures .hub-button').removeClass('display-none').attr('data-offset', (offset + response['innovations'].length));
            }
        }
    }

    load_background_images();
    $('.venobox').venobox({
        infinigall: true            // default: false
    });
}



/**
 * search_results_init
 * @param searchWord
 */
function search_results_init(searchWord) {
    search_result_searchWord = searchWord;
    window.scrollTo(0, 0);
    $('.full-content-search-result .tab-search').tabs({
        activate: function (event, ui) {
            window.scrollTo(0, 0);
        }
    });
    $('#main').removeClass('loading');

    var limit = 20;
    if (search_result_request != null)
        search_result_request.abort();
    search_result_request = $.ajax({
        url: '/api/search/results',
        type: "POST",
        data: {search: searchWord, 'limit': limit, token: $('#hub-token').val()},
        dataType: 'json',
        success: function (response, statut) {
            if (response['status'] == 'error') {
                add_flash_message('error', response['message']);
            } else {
                search_results_display_initial(response, limit);
            }
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            add_flash_message('error', resultat.status + ' ' + e);
        }
    });
}

/**
 * search_results_tags_init
 * @param tag
 */
function search_results_tags_init(tag) {
    search_result_tag = tag;
    window.scrollTo(0, 0);
    $('.full-content-search-result .tab-search').tabs({
        activate: function (event, ui) {
            window.scrollTo(0, 0);
        }
    });
    $('#main').removeClass('loading');
    var limit = 20;
    if (search_result_request != null)
        search_result_request.abort();
    search_result_request = $.ajax({
        url: '/api/search/tag/results',
        type: "POST",
        data: {title: tag['title'], 'limit': limit, token: $('#hub-token').val()},
        dataType: 'json',
        success: function (response, statut) {
            if (response['status'] == 'error') {
                add_flash_message('error',response['message']);
            } else {
                search_results_display_initial(response, limit);
            }
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            add_flash_message('error',resultat.status + ' ' + e);
        }
    });
}

$(document).on("focus", '#pri-search-input', function () {
    open_popup('.element-search-popup', function () {
        setTimeout(function () {
            $('#true-search-input').focus();
        }, 100);
    })
});

$(document).on("click", '.pri-content-input-search .button-search-input', function () {
    open_popup('.element-search-popup', function () {
        setTimeout(function () {
            $('#true-search-input').focus();
        }, 100);
    })
});


$(document).ready(function () {

    $('#pri-search-input').val('');
    $('#true-search-input').val('');

    $(document).on("click", '.content-search-popup .button-search-input', function () {
        $('#true-search-input').focus();
    });

    $(document).on("keyup", '#true-search-input', function () {
        var searchWord = $('#true-search-input').val();
        if (searchWord.length === 0) {
            displaySearchHistory();
        } else {
            displaySearchResults(searchWord);
        }
    });

    $(document).on("click", '.load-more-search', function (e) {
        e.stopImmediatePropagation();
        var offset = parseInt($(this).attr('data-offset'));
        var limit = parseInt($(this).attr('data-limit'));
        var target = $(this).attr('data-target');
        $(this).parent().find('.search-result-loader').removeClass('display-none');
        $(this).addClass('display-none');
        if (search_result_request != null)
            search_result_request.abort();
        search_result_request = $.ajax({
            url: '/api/search/results',
            type: "POST",
            data: {search: search_result_searchWord, 'offset': offset, 'limit': limit, 'type': target},
            dataType: 'json',
            success: function (response, statut) {
                if (response['status'] == 'error') {
                    add_flash_message('error',response['message']);
                } else {
                    search_results_display_more(target, response, offset, limit);
                }
            },
            error: function (resultat, statut, e) {
                ajax_on_error(resultat, statut, e);
                if (resultat.statusText == 'abort' || e == 'abort') {
                    return;
                }
                add_flash_message('error',resultat.status + ' ' + e);
            }
        });
    });

    $(document).on("click", '.load-more-search-tag', function (e) {
        e.stopImmediatePropagation();
        var offset = parseInt($(this).attr('data-offset'));
        var limit = parseInt($(this).attr('data-limit'));
        var target = $(this).attr('data-target');
        $(this).parent().find('.search-result-loader').removeClass('display-none');
        $(this).addClass('display-none');
        if (search_result_request != null)
            search_result_request.abort();
        search_result_request = $.ajax({
            url: '/api/search/tag/results',
            type: "POST",
            data: {title: search_result_tag['title'], 'offset': offset, 'limit': limit, 'type': target},
            dataType: 'json',
            success: function (response, statut) {
                if (response['status'] == 'error') {
                    add_flash_message('error',response['message']);
                } else {
                    search_results_display_more(target, response, offset, limit);
                }
            },
            error: function (resultat, statut, e) {
                ajax_on_error(resultat, statut, e);
                if (resultat.statusText == 'abort' || e == 'abort') {
                    return;
                }
                add_flash_message('error',resultat.status + ' ' + e);
            }
        });

    });


});