var connect_request_qet_infos = null;
var connect_user_offset = 0;
var connect_user_limit = 20;
var connect_user_filters = [];
var connect_loading_more = false;
var connect_all_are_loaded = false;


/**
 * goToConnectPage
 * @param link
 * @return {boolean}
 */
function goToConnectPage(link) {
    try {
        before_opening_page();
        var link = link || '/content';
        opti_update_url(link, 'list-connect');
        $('#main').removeClass('loading');
        changePageTitle('Connect' + ' | ' + global_wording['title']);
        update_menu_active('menu-li-connect');
        $('html, body').addClass('background-color-fff');
        $('#pri-main-container-without-header').html(connect_get_html_template());
        window.scrollTo(0, 0);
        connect_init();
    } catch (error) {
        init_unknow_error_view(error);
    }
}

/**
 * connect_get_html_template
 * @returns {string}
 */
function connect_get_html_template() {
    var out = [], o = -1;
    out[++o] = '<div class="full-content-page-connect">';
    out[++o] = '<div class="padding-top-20 text-align-center">'; // Start Entete
    out[++o] = '<div class="central-content-994">'; // central-content
    out[++o] = '<div class="font-montserrat-700 font-size-48 color-000 line-height-1">Connect</div>';
    out[++o] = '<div class="font-work-sans-500 font-size-21 color-000 padding-top-10 line-height-1">innovation is ALL about people</div>';
    //out[++o] = '<div class="text-align-center margin-top-20 position-relative z-index-2"><div class="font-montserrat-700 beta-version-chip">Beta version</div></div>';
    out[++o] = '<div class="font-work-sans-400 font-size-18 color-000 padding-top-60 line-height-1-5 text-align-left">';
    out[++o] = 'Because innovation requires a highly transversal team work to address all the challenges on the road to success, this section is designed to help you connect to experts and peers across the Group that can help you find solutions and accelerate your innovation’s journey.';
    out[++o] = '</div>';
    out[++o] = '<div class="connect-content-search-bar form-element">'; // Start connect-content-search-bar
    out[++o] = '<select id="connect-search-input"></select>';
    out[++o] = '</div>'; // End connect-content-search-bar
    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End Entete
    out[++o] = '<div class="padding-top-40 padding-bottom-40 backgroud-color-fff">'; // Start backgroud-color-fff
    out[++o] = '<div class="central-content-994 content-mosaique-user overflow-hidden">'; // Start central-content-994
    out[++o] = connect_get_empty_state_html();
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End backgroud-color-fff
    out[++o] = '</div>';
    return out.join('');
}


/**
 * explore_get_innovation_html
 *
 * @param innovation
 * @param action_save_search
 * @returns {string}
 */
function connect_get_user_html(user, action_save_search) {
    action_save_search = (typeof action_save_search != 'undefined') ? action_save_search : false;
    var out = [], o = -1;
    var default_background = '/images/default/user.png';
    out[++o] = '';
    out[++o] = '<div class="mosaique-user ' + ((user) ? '' : 'empty-state no-hover') + '">';
    out[++o] = '<div class="content">';
    if (user) {
        if (action_save_search) {
            out[++o] = '<a href="' + user['url'] + '" class="link-profile global-link save-search-action" data-title="' + user['username'] + '" data-css-class="link-profile">';
        } else {
            out[++o] = '<a href="' + user['url'] + '" class="link-profile global-link">';
        }
        out[++o] = '<div class="position-relative content-picture">';
        out[++o] = '<div class="picture loading-bg background-color-f2f2f2" data-bg="' + user['picture'] + '" data-default-bg="' + default_background + '"></div>';
        if (!action_save_search && user['minimal_last_login']) {
            out[++o] = '<div class="last-connexion">' + user['minimal_last_login'] + '</div>';
        }
        out[++o] = '</div>';
        out[++o] = '<div class="title">' + user['username'] + '</div>';
        out[++o] = '<div class="infos">';
        out[++o] = user['situation_and_entity'];
        out[++o] = '</div>';
        if (!action_save_search && (user['nb_projects'] > 0 || user['nb_skills'] > 0)) {
            out[++o] = '<div class="under-infos">';
            var nb_projects = null;
            if (user['nb_projects'] > 0) {
                nb_projects = (user['nb_projects'] > 1) ? user['nb_projects'] + ' projects' : user['nb_projects'] + ' project';
            }
            var nb_skills = null;
            if (user['nb_skills'] > 0) {
                nb_skills = (user['nb_skills'] > 1) ? user['nb_skills'] + ' skills' : user['nb_skills'] + ' skill';
            }
            var libelle = (nb_projects) ? nb_projects : '';
            if (libelle !== "" && nb_skills) {
                libelle += " • ";
            }
            if (nb_skills) {
                libelle += nb_skills;
            }
            out[++o] = libelle;
            out[++o] = '</div>';
        }
        out[++o] = '</a>';
    } else { // Empty placeholder
        out[++o] = '<div class="picture position-relative" style="background-image:url(' + default_background + ');">';
        out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
        out[++o] = '</div>';
        out[++o] = '<div class="fake-text  position-relative">';
        out[++o] = '<div class="position-absolute-inner animated-placeholder-shimmer"></div>';
        out[++o] = '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * connect_get_empty_state_html
 * @param limit
 * @returns {string}
 */
function connect_get_empty_state_html(limit) {
    limit = limit || 18;
    var out = [], o = -1;
    for (var i = 0; i < limit; i++) {
        out[++o] = connect_get_user_html();
    }
    return out.join('');
}

function connect_loadMore() {
    connect_get_infos();
}


function connect_formatSearchSelection(item) {
    return '<span class="connect-search-selection" data-id="' + item.id + '">' + item.text + '</span>';
}


function connect_init_search_input() {
    $('#connect-search-input').select2({
        multiple: true,
        placeholder: "Entity, brand, Innovation, Expertise…",
        width: '100%',
        language: {
            errorLoading: function () {
                return "Searching..."
            }
        },
        ajax: {
            url: "/user/search",
            dataType: 'json',
            type: "POST",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    filters: connect_user_filters
                };
            },
            processResults: function (data, page) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data.items
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 3,
        templateSelection: connect_formatSearchSelection
    });

    $('#connect-search-input').change(function () {
        connect_user_filters = $(this).val();
        connect_user_offset = 0;
        $('.content-mosaique-user').html(connect_get_empty_state_html());
        connect_get_infos();

    });
}

/**
 * connect_display_no_result
 */
function connect_display_no_result(){
    $('.content-mosaique-user').html('<div class="font-montserrat-700 color-000 line-height-1 font-size-18 padding-vertical-20 text-align-center">No result found…</div>');
}

/**
 * connect_init
 */
function connect_init() {
    connect_user_offset = 0;
    connect_user_filters = [];
    connect_loading_more = false;
    connect_all_are_loaded = false;

    $(window).scroll(function () {
        if (($(window).scrollTop() + $(window).height() + 300) >= $('html')[0].scrollHeight) {
            if (!connect_all_are_loaded && !connect_loading_more) {
                connect_loadMore();
            }
        }
    });
    connect_get_infos();
    connect_init_search_input();
}


/**
 * connect_get_infos
 */
function connect_get_infos(filters) {
    connect_loading_more = true;
    if (connect_request_qet_infos != null)
        connect_request_qet_infos.abort();
    connect_request_qet_infos = $.ajax({
        url: '/api/user/connect/load-more',
        type: "POST",
        data: {offset: connect_user_offset, limit: connect_user_limit, filters: connect_user_filters},
        dataType: 'json',
        success: function (response, statut) {
            connect_loading_more = false;
            connect_load_more_list(response['users'], response['total']);
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            connect_loading_more = false;
            $('.mosaique-user.empty-state').remove();

        }
    });

}

/**
 * connect_load_more_list
 * @param users
 * @param total
 */
function connect_load_more_list(users, total) {
    $('.mosaique-user.empty-state').remove();
    if(connect_user_offset === 0 && users.length === 0){
        connect_display_no_result();
        connect_all_are_loaded = true;
    } else {
        var out = [], o = -1;
        for (var i = 0; i < users.length; i++) {
            out[++o] = connect_get_user_html(users[i]); // End Entete
        }
        connect_user_offset += connect_user_limit;
        if (total <= connect_user_offset) {
            connect_all_are_loaded = true;
        } else {
            var nb_empty_state = 6 - (connect_user_offset % 3);
            out[++o] = connect_get_empty_state_html(nb_empty_state);
        }
        $('.content-mosaique-user').append(out.join(''));
        load_background_images();
    }
}