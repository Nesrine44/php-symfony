var compare_innovation_collection = null;


/* COMPARE
 ----------------------------------------------------------------------------------------*/


/**
 * intersect
 * @param a
 * @param b
 * @returns {*}
 */
function intersect(a, b) {
    var t;
    if (b.length > a.length) t = b, b = a, a = t; // indexOf to loop over shorter
    return a.filter(function (e) {
        return b.indexOf(e) > -1;
    });
}

/**
 * compare_get_innovation_html
 *
 * @param innovation
 * @returns {string}
 */
function compare_get_innovation_html(innovation) {
    var out = [], o = -1;
    out[++o] = '';
    out[++o] = '<div class="mosaique-innovation compare" data-id="' + innovation['id'] + '">';
    out[++o] = '<div class="content">';
    out[++o] = '<div class="selected-state transition-opacity"></div>';
    var style_background = 'style="background-image:url(' + innovation['explore']['images'][0] + ');"';
    out[++o] = '<div class="picture" ' + style_background + '></div>';
    out[++o] = '<div class="title">' + innovation['title'] + '</div>';
    if (innovation['in_market_date'] && !in_array(innovation['current_stage'], ['discover', 'ideate'])) {
        var mmYY = gmdate("m/y", innovation['in_market_date']);
        out[++o] = '<div class="infos">In Market since ' + mmYY + '</div>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="selected-mask transition-opacity"></div>';
    out[++o] = '</div>';
    return out.join('');
}


function goToCompareListPage() {
    before_opening_page();
    compare_innovation_collection = new DataCollection(explore_initial_innovation_collection.query().values());
    init_explore_benchmark();
}

function init_explore_benchmark() {
    var out = [], o = -1;
    out[++o] = '<div class="full-content-compare-list">'; // Start full-content-compare-list

    out[++o] = '<div class="background-color-gradient-161541 padding-top-20 padding-bottom-20 z-index-5 position-fixed-left-right">'; // Start Entete
    out[++o] = '<div class="central-content-994">'; // central-content-994
    out[++o] = '<div class="font-montserrat-700 font-size-30 color-fff-0-9">Compare</div>';
    out[++o] = '<div class="font-work-sans-500 font-size-14 color-fff-0-7 subtitle-action">Select two innovations for a more detailed comparison</div>';
    out[++o] = '<div class="position-absolute-top-right padding-top-8 display-none">'; // Start position-absolute-top-right
    out[++o] = '<div class="button-launch-comparison hub-button">Compare innovations</div>';
    out[++o] = '</div>'; // End position-absolute-top-right
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End Entete

    out[++o] = '<div class="background-color-gradient-161541 padding-top-20 padding-bottom-20 opacity-0">'; // Start Fake Entete
    out[++o] = '<div class="central-content-994">'; // central-content-994
    out[++o] = '<div class="font-montserrat-700 font-size-30 color-fff-0-9">Compare</div>';
    out[++o] = '<div class="font-work-sans-500 font-size-14 color-fff-0-7">Select two innovations for a more detailed comparison</div>';
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End Fake Entete

    out[++o] = '<div class="background-color-fff padding-top-40 padding-bottom-40">'; // Start background-color-fff
    out[++o] = '<div class="central-content-994 content-compare-innovations overflow-hidden">'; // central-content-994

    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End full-content-compare-list
    opti_update_url("/content/compare", 'compare_page');
    update_menu_active();
    changePageTitle('Compare | ' + global_wording['title']);
    $('#pri-main-container-without-header').html(out.join(''));
    window.scrollTo(0, 0);

    var out = [], o = -1;
    compare_innovation_collection.query().each(function (row, index) {
        out[++o] = compare_get_innovation_html(row);
    });
    $('.content-compare-innovations').append(out.join(''));
    init_tooltip();

    $('.mosaique-innovation.compare').click(function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        } else if (!$('.content-compare-innovations').hasClass('full-selected')) {
            $(this).addClass('selected');
        }
        update_benchmark_message();
    });
    $('#main').removeClass('loading');

    function update_benchmark_message() {
        var nb_selected = $('.mosaique-innovation.compare.selected').length;
        var message = "";
        $('.content-compare-innovations').removeClass('full-selected');
        if (nb_selected == 0) {
            message = "Select two innovations for a more detailed comparison";
            $('.full-content-compare-list .subtitle-action').html(message);
            $('.full-content-compare-list .position-absolute-top-right').addClass('display-none');
        } else if (nb_selected == 1) {
            message = "Select another innovation to compare";
            $('.full-content-compare-list .subtitle-action').html(message);
            $('.full-content-compare-list .position-absolute-top-right').addClass('display-none');
        } else {
            message = "Innovations selected";
            $('.full-content-compare-list .subtitle-action').html(message);
            $('.full-content-compare-list .position-absolute-top-right').removeClass('display-none');
            $('.content-compare-innovations').addClass('full-selected');
        }
    }

    $('.button-launch-comparison').click(function () {
        var ids = [];
        $('.mosaique-innovation.compare.selected').each(function () {
            ids.push(parseInt($(this).attr('data-id')));
        });
        if (ids.length == 2) {
            var innovation_1 = getInnovationById(ids[0], true);
            var innovation_2 = getInnovationById(ids[1], true);
            init_explore_benchmark_comparison(innovation_1, innovation_2);
        } else {
            add_flash_message('error', 'An error appeared. Please reload this page', false);
        }
    });
}


function init_explore_benchmark_comparison(innovation_1, innovation_2) {
    var innovation = null;
    var style = null;
    var classe = null;
    var text = null;
    var innovation_is_a_service = null;
    var out = [], o = -1;
    out[++o] = '<div class="full-content-compare-detail">'; // Start full-content-compare-detail
    out[++o] = '<div class="background-color-gradient-161541 padding-top-20 padding-bottom-20">'; // Start Entete
    out[++o] = '<div class="central-content-994">'; // central-content-994
    out[++o] = '<div class="font-montserrat-700 font-size-30 color-fff-0-9">Compare</div>';
    out[++o] = '<div class="font-work-sans-500 font-size-14 color-fff-0-7">Innovations selected</div>';
    out[++o] = '<div class="position-absolute-top-right padding-top-8">'; // Start position-absolute-top-right
    out[++o] = '<div class="action-go-back-to-compare hub-button">Compare other innovations</div>';
    out[++o] = '</div>'; // End position-absolute-top-right
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End Entete
    out[++o] = '<div class="background-color-fff padding-top-40 padding-bottom-60">'; // Start background-color-fff
    out[++o] = '<div class="central-content">'; // central-content

    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-bottom-10">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    style = 'style="background-image:url(' + innovation['explore']['images'][0] + ');"';
    out[++o] = '<div class="compare-detail-main-picture" ' + style + '></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 text-align-center text-transform-uppercase line-height-1 font-size-30 margin-bottom-10">' + innovation['title'] + '</div>';
    out[++o] = '<div class="font-montserrat-700 color-b5b5b5 text-align-center font-size-16 line-height-1 margin-bottom-20">' + get_innovation_entity_brand_libelle(innovation) + '</div>';
    if (innovation['tags_array'].length > 0) {
        out[++o] = '<div class="content-tags text-align-center  margin-bottom-20">'; // Start content-tags
        for (var i = 0; i < innovation['tags_array'].length; i++) {
            out[++o] = '<div class="trending-tag transparent no-hover" data-target="' + innovation['tags_array'][i] + '">' + innovation['tags_array'][i] + '</div>';
        }
        out[++o] = '</div>'; // End content-tags
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    style = 'style="background-image:url(' + innovation['explore']['images'][0] + ');"';
    out[++o] = '<div class="compare-detail-main-picture" ' + style + '></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 text-align-center text-transform-uppercase font-size-30 margin-bottom-10">' + innovation['title'] + '</div>';
    out[++o] = '<div class="font-montserrat-700 color-b5b5b5 text-align-center font-size-16 line-height-1 margin-bottom-20">' + get_innovation_entity_brand_libelle(innovation) + '</div>';
    if (innovation['tags_array'].length > 0) {
        out[++o] = '<div class="content-tags text-align-center margin-bottom-20">'; // Start content-tags
        for (var i = 0; i < innovation['tags_array'].length; i++) {
            out[++o] = '<div class="trending-tag transparent no-hover" data-target="' + innovation['tags_array'][i] + '">' + innovation['tags_array'][i] + '</div>';
        }
        out[++o] = '</div>'; // End content-tags
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK

    // START BLOCK
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    out[++o] = '<div class="margin-top-10 margin-bottom-10 color-000 font-size-20 font-work-sans-600 line-height-1">Why did we design this innovation?</div>';
    classe = (innovation['why_invest_in_this_innovation']) ? "" : 'style_missing_data';
    text = (innovation['why_invest_in_this_innovation']) ? innovation['why_invest_in_this_innovation'] : 'Missing data';
    out[++o] = '<div class="color-000 margin-bottom-30 font-size-15 line-height-1-53 font-work-sans-300 ' + classe + '">' + text + '</div>';
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    out[++o] = '<div class="margin-top-10 margin-bottom-10 color-000 font-size-20 font-work-sans-600 line-height-1">Why did we design this innovation?</div>';
    classe = (innovation['why_invest_in_this_innovation']) ? "" : 'style_missing_data';
    text = (innovation['why_invest_in_this_innovation']) ? innovation['why_invest_in_this_innovation'] : 'Missing data';
    out[++o] = '<div class="color-000 margin-bottom-30 font-size-15 line-height-1-53 font-work-sans-300 ' + classe + '">' + text + '</div>';
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK

    // START BLOCK
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    out[++o] = '<div class="content-overview-header-contact">'; // Start content-overview-header-contact
    out[++o] = '<div class="contact-picture loading-bg" data-bg="' + innovation['contact']['picture'] + '"></div>';
    out[++o] = '<div class="contact-infos">'; // Start contact-infos
    out[++o] = '<div class="font-montserrat-500 color-b5b5b5 font-size-13 line-height-1 margin-bottom-3">Contact</div>';
    out[++o] = '<div class="position-relative height-20">'; // Start position-relative
    out[++o] = '<a class="link-profile hub-link text-decoration-none font-montserrat-700 font-size-16" href="/user/' + innovation['contact']['uid'] + '">' + innovation['contact']['username'] + '</a>';
    out[++o] = '<a href="mailto:' + innovation['contact']['email'] + '" class="pri-mail-contact-action explore-detail-round-link mail with-tooltip" title="Contact ' + innovation['contact']['username'] + '"></a>';
    out[++o] = '</div>'; // End position-relative
    out[++o] = '</div>'; // End contact-infos
    if (innovation['website_url'] || innovation['ibp_link'] || innovation['press_release_link'] || (!innovation_is_a_service && innovation['video_link'])) {
        out[++o] = '<div class="assets-links">';
        if (innovation['website_url']) {
            var round_class = (innovation_is_a_service) ? 'website_url' : 'my_brand';
            var round_title = (innovation_is_a_service) ? 'Go to dedicated website' : 'Go to dedicated MyBrand content';
            out[++o] = '<a href="' + return_proper_link(innovation['website_url']) + '" target="_blank" title="' + round_title + '" class="explore-detail-round-link with-tooltip ' + round_class + '"></a>';
        }
        if (innovation['ibp_link']) {
            out[++o] = '<a href="' + return_proper_link(innovation['ibp_link']) + '" target="_blank" title="Checkout latest IBP toolkit" class="explore-detail-round-link ibp with-tooltip"></a>';
        }
        if (innovation['press_release_link']) {
            out[++o] = '<a href="' + return_proper_link(innovation['press_release_link']) + '" target="_blank" title="Access to press release" class="explore-detail-round-link press_release with-tooltip"></a>';
        }
        if (!innovation_is_a_service && innovation['video_link']) {
            out[++o] = '<a href="' + return_proper_link(innovation['video_link']) + '" target="_blank" title="Watch video" class="explore-detail-round-link with-tooltip video"></a>';
        }
        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End content-overview-header-contact
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    out[++o] = '<div class="content-overview-header-contact">'; // Start content-overview-header-contact
    out[++o] = '<div class="contact-picture loading-bg" data-bg="' + innovation['contact']['picture'] + '"></div>';
    out[++o] = '<div class="contact-infos">'; // Start contact-infos
    out[++o] = '<div class="font-montserrat-500 color-b5b5b5 font-size-13 line-height-1 margin-bottom-3">Contact</div>';
    out[++o] = '<div class="position-relative height-20">'; // Start position-relative
    out[++o] = '<a class="link-profile hub-link text-decoration-none font-montserrat-700 font-size-16" href="/user/' + innovation['contact']['uid'] + '">' + innovation['contact']['username'] + '</a>';
    out[++o] = '<a href="mailto:' + innovation['contact']['email'] + '" class="pri-mail-contact-action explore-detail-round-link mail with-tooltip" title="Contact ' + innovation['contact']['username'] + '"></a>';
    out[++o] = '</div>'; // End position-relative
    out[++o] = '</div>'; // End contact-infos
    if (innovation['website_url'] || innovation['ibp_link'] || innovation['press_release_link'] || (!innovation_is_a_service && innovation['video_link'])) {
        out[++o] = '<div class="assets-links">';
        if (innovation['website_url']) {
            var round_class = (innovation_is_a_service) ? 'website_url' : 'my_brand';
            var round_title = (innovation_is_a_service) ? 'Go to dedicated website' : 'Go to dedicated MyBrand content';
            out[++o] = '<a href="' + return_proper_link(innovation['website_url']) + '" target="_blank" title="' + round_title + '" class="explore-detail-round-link with-tooltip ' + round_class + '"></a>';
        }
        if (innovation['ibp_link']) {
            out[++o] = '<a href="' + return_proper_link(innovation['ibp_link']) + '" target="_blank" title="Checkout latest IBP toolkit" class="explore-detail-round-link ibp with-tooltip"></a>';
        }
        if (innovation['press_release_link']) {
            out[++o] = '<a href="' + return_proper_link(innovation['press_release_link']) + '" target="_blank" title="Access to press release" class="explore-detail-round-link press_release with-tooltip"></a>';
        }
        if (!innovation_is_a_service && innovation['video_link']) {
            out[++o] = '<a href="' + return_proper_link(innovation['video_link']) + '" target="_blank" title="Watch video" class="explore-detail-round-link with-tooltip video"></a>';
        }
        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End content-overview-header-contact
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-top-60">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-30">Project ID</div>';

    out[++o] = '<div class="overflow-hidden content-infos-project-id block">'; // Start content-infos-project-id
    out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column-percent-50

    out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + innovation['current_stage'] + '">'; // Start info-project-id
    out[++o] = '<div class="icon darker ' + innovation['current_stage'] + '"></div>';
    var style_text = (innovation['current_stage']) ? "" : 'style_missing_data';
    var text_libelle = returnGoodStage(innovation['current_stage']);
    out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
    out[++o] = '</div>'; // End info-project-id

    var data_target = innovation['proper']['classification_type'];
    out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
    out[++o] = '<div class="icon ' + data_target + '"></div>';
    var style_text = (innovation['classification_type'] && innovation['classification_type'] != "Empty") ? "" : 'style_missing_data';
    var text_libelle = (innovation['classification_type'] && innovation['classification_type'] != "Empty") ? innovation['classification_type'] : 'Missing data';
    out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
    out[++o] = '</div>'; // End info-project-id

    var data_target = innovation['proper']['innovation_type'];
    out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
    out[++o] = '<div class="icon ' + data_target + '"></div>';
    var style_text = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? "" : 'style_missing_data';
    var text_libelle = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? innovation['innovation_type'] : 'Missing data';
    out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
    out[++o] = '</div>'; // End info-project-id

    if (!innovation_is_a_service) {
        var value_growth_model = (innovation['growth_model'] == "fast_growth") ? "Fast Growth" : "Slow Build";
        var classe_growth_model = innovation['growth_model'];
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + classe_growth_model + '">'; // Start info-project-id
        out[++o] = '<div class="icon size-26 light ' + classe_growth_model + '"></div>';
        out[++o] = '<div class="text font-montserrat-500"> ' + value_growth_model + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (!innovation_is_a_service) {
        var data_classe = string_to_classe(pri_get_libelle_consumer_opportunity_by_id(innovation['consumer_opportunity']));
        var libelle_coop = pri_get_libelle_consumer_opportunity_by_id(innovation['consumer_opportunity']);
        var data_target = (data_classe != 'empty') ? data_classe : 'default_coop';
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
        out[++o] = '<div class="icon size-26 ' + data_target + '"></div>';
        var style_text = (libelle_coop != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (libelle_coop != "Empty") ? libelle_coop : 'Missing data';
        out[++o] = '<div class="text font-montserrat-700 text-transform-uppercase color-' + data_target + ' ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-20">'; // Start column-percent-50

    if (!innovation_is_a_service) {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_moc">'; // Start info-project-id
        out[++o] = '<div class="icon moc light size-26"></div>';
        var style_text = (innovation['moc'] && innovation['moc'] != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (innovation['moc'] && innovation['moc'] != "Empty") ? innovation['moc'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (!innovation_is_a_service) {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_category">'; // Start info-project-id
        out[++o] = '<div class="icon category light size-26"></div>';
        var style_text = (innovation['category']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['category']) ? innovation['category'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (!innovation_is_a_service) {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_abv">'; // Start info-project-id
        out[++o] = '<div class="icon abv light size-26"></div>';
        var style_text = (innovation['abv']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['abv']) ? innovation['abv'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (innovation['business_drivers'] && innovation['business_drivers'] != 'None') {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="overview-business_drivers">'; // Start info-project-id
        var icon_class = string_to_classe(innovation['business_drivers']);
        out[++o] = '<div class="icon ' + icon_class + ' light size-26"></div>';
        out[++o] = '<div class="text font-montserrat-500"> ' + innovation['business_drivers'] + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (in_array(innovation['growth_strategy'], ['Big Bet'])) {
        var data_target = string_to_classe(innovation['growth_strategy']);
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
        out[++o] = '<div class="icon ' + data_target + ' light size-26"></div>';
        out[++o] = '<div class="text font-montserrat-500"> ' + innovation['growth_strategy'] + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    out[++o] = '<div class="info-project-id">'; // Start info-project-id
    out[++o] = '<div class="icon introduction-date light size-26"></div>';
    var style_text = (innovation['in_market_date']) ? "" : 'style_missing_data';
    var in_market_date = (innovation['in_market_date']) ? gmdate("F Y", innovation['in_market_date']) : 'Missing data';
    var nb_year_since_launch = (innovation['in_market_date']) ? ' • Y' + innovation['years_since_launch'] + ' in market' : '';
    out[++o] = '<div class="text font-montserrat-500">';
    out[++o] = 'Introduced in<br>';
    out[++o] = '<span class="' + style_text + '">' + in_market_date + nb_year_since_launch + '</span>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End info-project-id

    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End content-infos-project-id

    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-30">Project ID</div>';

    out[++o] = '<div class="overflow-hidden content-infos-project-id block">'; // Start content-infos-project-id
    out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column-percent-50

    out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + innovation['current_stage'] + '">'; // Start info-project-id
    out[++o] = '<div class="icon darker ' + innovation['current_stage'] + '"></div>';
    var style_text = (innovation['current_stage']) ? "" : 'style_missing_data';
    var text_libelle = returnGoodStage(innovation['current_stage']);
    out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
    out[++o] = '</div>'; // End info-project-id

    var data_target = innovation['proper']['classification_type'];
    out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
    out[++o] = '<div class="icon ' + data_target + '"></div>';
    var style_text = (innovation['classification_type'] && innovation['classification_type'] != "Empty") ? "" : 'style_missing_data';
    var text_libelle = (innovation['classification_type'] && innovation['classification_type'] != "Empty") ? innovation['classification_type'] : 'Missing data';
    out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
    out[++o] = '</div>'; // End info-project-id

    var data_target = innovation['proper']['innovation_type'];
    out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
    out[++o] = '<div class="icon ' + data_target + '"></div>';
    var style_text = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? "" : 'style_missing_data';
    var text_libelle = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? innovation['innovation_type'] : 'Missing data';
    out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
    out[++o] = '</div>'; // End info-project-id

    if (!innovation_is_a_service) {
        var value_growth_model = (innovation['growth_model'] == "fast_growth") ? "Fast Growth" : "Slow Build";
        var classe_growth_model = innovation['growth_model'];
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + classe_growth_model + '">'; // Start info-project-id
        out[++o] = '<div class="icon size-26 light ' + classe_growth_model + '"></div>';
        out[++o] = '<div class="text font-montserrat-500"> ' + value_growth_model + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (!innovation_is_a_service) {
        var data_classe = string_to_classe(pri_get_libelle_consumer_opportunity_by_id(innovation['consumer_opportunity']));
        var libelle_coop = pri_get_libelle_consumer_opportunity_by_id(innovation['consumer_opportunity']);
        var data_target = (data_classe != 'empty') ? data_classe : 'default_coop';
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
        out[++o] = '<div class="icon size-26 ' + data_target + '"></div>';
        var style_text = (libelle_coop != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (libelle_coop != "Empty") ? libelle_coop : 'Missing data';
        out[++o] = '<div class="text font-montserrat-700 text-transform-uppercase color-' + data_target + ' ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-20">'; // Start column-percent-50

    if (!innovation_is_a_service) {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_moc">'; // Start info-project-id
        out[++o] = '<div class="icon moc light size-26"></div>';
        var style_text = (innovation['moc'] && innovation['moc'] != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (innovation['moc'] && innovation['moc'] != "Empty") ? innovation['moc'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (!innovation_is_a_service) {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_category">'; // Start info-project-id
        out[++o] = '<div class="icon category light size-26"></div>';
        var style_text = (innovation['category']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['category']) ? innovation['category'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (!innovation_is_a_service) {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_abv">'; // Start info-project-id
        out[++o] = '<div class="icon abv light size-26"></div>';
        var style_text = (innovation['abv']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['abv']) ? innovation['abv'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (innovation['business_drivers'] && innovation['business_drivers'] != 'None') {
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="overview-business_drivers">'; // Start info-project-id
        var icon_class = string_to_classe(innovation['business_drivers']);
        out[++o] = '<div class="icon ' + icon_class + ' light size-26"></div>';
        out[++o] = '<div class="text font-montserrat-500"> ' + innovation['business_drivers'] + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    if (in_array(innovation['growth_strategy'], ['Big Bet'])) {
        var data_target = string_to_classe(innovation['growth_strategy']);
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
        out[++o] = '<div class="icon ' + data_target + ' light size-26"></div>';
        out[++o] = '<div class="text font-montserrat-500"> ' + innovation['growth_strategy'] + '</div>';
        out[++o] = '</div>'; // End info-project-id
    }

    out[++o] = '<div class="info-project-id">'; // Start info-project-id
    out[++o] = '<div class="icon introduction-date light size-26"></div>';
    var style_text = (innovation['in_market_date']) ? "" : 'style_missing_data';
    var in_market_date = (innovation['in_market_date']) ? gmdate("F Y", innovation['in_market_date']) : 'Missing data';
    var nb_year_since_launch = (innovation['in_market_date']) ? ' • Y' + innovation['years_since_launch'] + ' in market' : '';
    out[++o] = '<div class="text font-montserrat-500">';
    out[++o] = 'Introduced in<br>';
    out[++o] = '<span class="' + style_text + '">' + in_market_date + nb_year_since_launch + '</span>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End info-project-id

    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End content-infos-project-id

    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK

    out[++o] = '<br/><br/><br/><br/>';

    // START BLOCK
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-50">Elevator Pitch</div>';
    if (!innovation_is_a_service) {
        if (!in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment'])) {
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-20">Key info</div>';
            var style_universal_information_1 = (innovation['universal_key_information_1']) ? "" : 'style_missing_data';
            var text_universal_information_1 = (innovation['universal_key_information_1']) ? innovation['universal_key_information_1'] : 'Missing data';
            var style_universal_information_2 = (innovation['universal_key_information_2']) ? "" : 'style_missing_data';
            var text_universal_information_2 = (innovation['universal_key_information_2']) ? innovation['universal_key_information_2'] : 'Missing data';
            var style_universal_information_3 = (innovation['universal_key_information_3']) ? "" : 'style_missing_data';
            var text_universal_information_3 = (innovation['universal_key_information_3']) ? innovation['universal_key_information_3'] : 'Missing data';
            if (innovation['universal_key_information_3'] && innovation['universal_key_information_3_vs']) {
                text_universal_information_3 += ' vs ' + innovation['universal_key_information_3_vs'];
            } else if (!innovation['universal_key_information_3'] && innovation['universal_key_information_3_vs']) {
                text_universal_information_3 = innovation['universal_key_information_3_vs'];
            }
            if (innovation['new_to_the_world']) {
                text_universal_information_3 = 'N/A';
            }
            var style_universal_information_4 = (innovation['universal_key_information_4']) ? "" : 'style_missing_data';
            var text_universal_information_4 = (innovation['universal_key_information_4']) ? innovation['universal_key_information_4'] : 'Missing data';
            if (innovation['universal_key_information_4'] && innovation['universal_key_information_4_vs']) {
                text_universal_information_4 += ' vs ' + innovation['universal_key_information_4_vs'];
            } else if (!innovation['universal_key_information_4'] && innovation['universal_key_information_4_vs']) {
                text_universal_information_4 = innovation['universal_key_information_4_vs'];
            }
            var text_universal_information_5 = (innovation['universal_key_information_5']) ? innovation['universal_key_information_5'] : 'Missing data';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Perfect Serve</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 margin-bottom-15 ' + style_universal_information_1 + '">' + text_universal_information_1 + '</div>';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">RTM</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 margin-bottom-15 ' + style_universal_information_2 + '">' + text_universal_information_2 + '</div>';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Price index versus main competitor</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 margin-bottom-15 ' + style_universal_information_3 + '">' + text_universal_information_3 + '</div>';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Price index versus main reference motherbrand</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_universal_information_4 + '">' + text_universal_information_4 + '</div>';
            if (innovation['universal_key_information_5']) {
                out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1 margin-top-15">Other</div>';
                out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ">' + text_universal_information_5 + '</div>';
            }
            out[++o] = '<div class="margin-bottom-40"></div>';
        }
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-50">Elevator Pitch</div>';
    if (!innovation_is_a_service) {
        if (!in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment'])) {
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-20">Key info</div>';
            var style_universal_information_1 = (innovation['universal_key_information_1']) ? "" : 'style_missing_data';
            var text_universal_information_1 = (innovation['universal_key_information_1']) ? innovation['universal_key_information_1'] : 'Missing data';
            var style_universal_information_2 = (innovation['universal_key_information_2']) ? "" : 'style_missing_data';
            var text_universal_information_2 = (innovation['universal_key_information_2']) ? innovation['universal_key_information_2'] : 'Missing data';
            var style_universal_information_3 = (innovation['universal_key_information_3']) ? "" : 'style_missing_data';
            var text_universal_information_3 = (innovation['universal_key_information_3']) ? innovation['universal_key_information_3'] : 'Missing data';
            if (innovation['universal_key_information_3'] && innovation['universal_key_information_3_vs']) {
                text_universal_information_3 += ' vs ' + innovation['universal_key_information_3_vs'];
            } else if (!innovation['universal_key_information_3'] && innovation['universal_key_information_3_vs']) {
                text_universal_information_3 = innovation['universal_key_information_3_vs'];
            }
            if (innovation['new_to_the_world']) {
                text_universal_information_3 = 'N/A';
            }
            var style_universal_information_4 = (innovation['universal_key_information_4']) ? "" : 'style_missing_data';
            var text_universal_information_4 = (innovation['universal_key_information_4']) ? innovation['universal_key_information_4'] : 'Missing data';
            if (innovation['universal_key_information_4'] && innovation['universal_key_information_4_vs']) {
                text_universal_information_4 += ' vs ' + innovation['universal_key_information_4_vs'];
            } else if (!innovation['universal_key_information_4'] && innovation['universal_key_information_4_vs']) {
                text_universal_information_4 = innovation['universal_key_information_4_vs'];
            }
            var text_universal_information_5 = (innovation['universal_key_information_5']) ? innovation['universal_key_information_5'] : 'Missing data';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Perfect Serve</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 margin-bottom-15 ' + style_universal_information_1 + '">' + text_universal_information_1 + '</div>';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">RTM</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 margin-bottom-15 ' + style_universal_information_2 + '">' + text_universal_information_2 + '</div>';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Price index versus main competitor</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 margin-bottom-15 ' + style_universal_information_3 + '">' + text_universal_information_3 + '</div>';
            out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Price index versus main reference motherbrand</div>';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_universal_information_4 + '">' + text_universal_information_4 + '</div>';
            if (innovation['universal_key_information_5']) {
                out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1 margin-top-15">Other</div>';
                out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ">' + text_universal_information_5 + '</div>';
            }
            out[++o] = '<div class="margin-bottom-40"></div>';
        }
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK

    // START BLOCK
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if (!innovation_is_a_service) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-20">Story</div>';
        var style_story = (innovation['story']) ? "" : 'style_missing_data';
        var text_story = (innovation['story']) ? innovation['story'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_story + '">' + text_story + '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if (!innovation_is_a_service) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-20">Story</div>';
        var style_story = (innovation['story']) ? "" : 'style_missing_data';
        var text_story = (innovation['story']) ? innovation['story'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_story + '">' + text_story + '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-top-40">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    var used = null;
    if (!innovation_is_a_service && innovation['video_link'] != "" && get_player_video(innovation['video_link']) != null) {
        out[++o] = '<div class="position-relative max-height-354">';
        out[++o] = get_player_video(innovation['video_link']);
        out[++o] = '</div>';
        if (innovation['video_password'] != '') {
            out[++o] = '<div class="font-work-sans-600 font-size-14 color-b5b5b5 line-height-1 margin-top-10">Video password: ' + innovation['video_password'] + '</div>';
        }
    } else if (!innovation_is_a_service && innovation['packshot_picture']) {
        used = innovation['packshot_picture']['thumbnail'];
        out[++o] = '<div class="explore-detail-main-picture">';
        out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
        out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
        out[++o] = '</div>';
    } else if (innovation['beautyshot_picture']) {
        used = innovation['beautyshot_picture']['thumbnail'];
        out[++o] = '<div class="explore-detail-main-picture">';
        out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
        out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
        out[++o] = '</div>';
    } else {
        used = innovation['explore']['images_detail'][0];
        out[++o] = '<div class="explore-detail-main-picture">';
        out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
        out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
        out[++o] = '</div>';
    }
    if (innovation['explore']['images_detail'].length > 0) {
        out[++o] = '<div class="content-other-pictures position-relative margin-top-20">';
        for (var i = 0; i < innovation['explore']['images_detail'].length; i++) {
            var other_picture = innovation['explore']['images_detail'][i];
            if (used != other_picture) {
                out[++o] = '<div class="explore-detail-other-picture">';
                out[++o] = '<img alt="" src="' + other_picture + '" data-action="zoom"/>';
                out[++o] = '<a href="' + other_picture + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
                out[++o] = '</div>';
            }
        }
        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    var used = null;
    if (!innovation_is_a_service && innovation['video_link'] != "" && get_player_video(innovation['video_link']) != null) {
        out[++o] = '<div class="position-relative max-height-354">';
        out[++o] = get_player_video(innovation['video_link']);
        out[++o] = '</div>';
        if (innovation['video_password'] != '') {
            out[++o] = '<div class="font-work-sans-600 font-size-14 color-b5b5b5 line-height-1 margin-top-10">Video password: ' + innovation['video_password'] + '</div>';
        }
    } else if (!innovation_is_a_service && innovation['packshot_picture']) {
        used = innovation['packshot_picture']['thumbnail'];
        out[++o] = '<div class="explore-detail-main-picture">';
        out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
        out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
        out[++o] = '</div>';
    } else if (innovation['beautyshot_picture']) {
        used = innovation['beautyshot_picture']['thumbnail'];
        out[++o] = '<div class="explore-detail-main-picture">';
        out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
        out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
        out[++o] = '</div>';
    } else {
        used = innovation['explore']['images_detail'][0];
        out[++o] = '<div class="explore-detail-main-picture">';
        out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
        out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
        out[++o] = '</div>';
    }
    if (innovation['explore']['images_detail'].length > 0) {
        out[++o] = '<div class="content-other-pictures position-relative margin-top-20">';
        for (var i = 0; i < innovation['explore']['images_detail'].length; i++) {
            var other_picture = innovation['explore']['images_detail'][i];
            if (used != other_picture) {
                out[++o] = '<div class="explore-detail-other-picture">';
                out[++o] = '<img alt="" src="' + other_picture + '" data-action="zoom"/>';
                out[++o] = '<a href="' + other_picture + '" class="hover-effect venobox" data-gall="innovation-' + innovation['id'] + '"></a>';
                out[++o] = '</div>';
            }
        }
        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-top-90">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    out[++o] = '<div class="icon value-proposition light explore-detail-overview-icon-title-big"></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Value Proposition</div>';
    if (innovation_is_a_service) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Unique experience</div>';
        var style_text = (innovation['unique_experience']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['unique_experience']) ? innovation['unique_experience'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    } else {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Uniqueness</div>';
        var style_text = (innovation['value_proposition']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['value_proposition']) ? innovation['value_proposition'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    out[++o] = '<div class="icon value-proposition light explore-detail-overview-icon-title-big"></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Value Proposition</div>';
    if (innovation_is_a_service) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Unique experience</div>';
        var style_text = (innovation['unique_experience']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['unique_experience']) ? innovation['unique_experience'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    } else {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Uniqueness</div>';
        var style_text = (innovation['value_proposition']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['value_proposition']) ? innovation['value_proposition'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-top-50">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    out[++o] = '<div class="icon consumer light explore-detail-overview-icon-title-big"></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Consumer</div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Consumer benefit</div>';
    var style_text = (innovation['consumer_insight']) ? "" : 'style_missing_data';
    var text_libelle = (innovation['consumer_insight']) ? innovation['consumer_insight'] : 'Missing data';
    out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    out[++o] = '<div class="icon consumer light explore-detail-overview-icon-title-big"></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Consumer</div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Consumer benefit</div>';
    var style_text = (innovation['consumer_insight']) ? "" : 'style_missing_data';
    var text_libelle = (innovation['consumer_insight']) ? innovation['consumer_insight'] : 'Missing data';
    out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if (!innovation_is_a_service && !in_array(innovation['current_stage'], ['discover', 'ideate'])) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13 margin-top-40">Early adopters persona</div>';
        var style_text = (innovation['early_adopter_persona']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['early_adopter_persona']) ? innovation['early_adopter_persona'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if (!innovation_is_a_service && !in_array(innovation['current_stage'], ['discover', 'ideate'])) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13 margin-top-40">Early adopters persona</div>';
        var style_text = (innovation['early_adopter_persona']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['early_adopter_persona']) ? innovation['early_adopter_persona'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-top-50">'; // Start overflow-hidden
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    out[++o] = '<div class="icon business-priority light explore-detail-overview-icon-title-big"></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Source of Business</div>';

    out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Size of the prize</div>';
    var style_text = (innovation['source_of_business']) ? "" : 'style_missing_data';
    var text_libelle = (innovation['source_of_business']) ? innovation['source_of_business'] : 'Missing data';
    out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';

    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    out[++o] = '<div class="icon business-priority light explore-detail-overview-icon-title-big"></div>';
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Source of Business</div>';

    out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Size of the prize</div>';
    var style_text = (innovation['source_of_business']) ? "" : 'style_missing_data';
    var text_libelle = (innovation['source_of_business']) ? innovation['source_of_business'] : 'Missing data';
    out[++o] = '<div class="font-work-sans-300 color-000 font-size-17 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-top-100">'; // Start overflow-hidden
    out[++o] = '<div class="central-content-994 no-zoom-map">'; // Start central-content-994
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-0">Markets today</div>';
    out[++o] = '<div class="content-explore-detail-map" id="map_explore_comparison"></div>';
    out[++o] = '<div class="text-align-right">'; // Start text-align-right
    out[++o] = '<div class="display-inline-block font-montserrat-700 color-compare-innovation-1 text-transform-uppercase font-size-17">' + innovation_1['title'] + '</div>';
    out[++o] = '<div class="margin-left-20 display-inline-block font-montserrat-700 color-compare-innovation-2 text-transform-uppercase font-size-17">' + innovation_2['title'] + '</div>';
    out[++o] = '</div>'; // End text-align-right
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK


    // START BLOCK
    out[++o] = '<div class="overflow-hidden padding-top-40">'; // Start overflow-hidden
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-40">Volumes <span class="color-d2d2d2">(' + global_wording['volume_unit'] + ')</span></div>';
    out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
    innovation = innovation_1;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if (!innovation_is_a_service && !in_array(innovation['current_stage'], ['discover', 'ideate'])) {
        out[++o] = '<div class="content-bars-volume">';
        for (var key in innovation['financial']['explore_all_volumes']) {
            if (innovation['financial']['explore_all_volumes'].hasOwnProperty(key)) {
                out[++o] = '<div class="block-bar-volume">';
                out[++o] = '<div class="libelle">' + key + '</div>';
                out[++o] = '<div class="bar" style="width: 0px;"></div>';
                var classe_volume = (innovation['financial']['explore_all_volumes'][key] === 'Missing data') ? 'style_missing_data' : '';
                out[++o] = '<div class="value ' + classe_volume + '">' + innovation['financial']['explore_all_volumes'][key] + '</div>';
                out[++o] = '</div>';
            }
        }
        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
    innovation = innovation_2;
    innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if (!innovation_is_a_service && !in_array(innovation['current_stage'], ['discover', 'ideate'])) {
        out[++o] = '<div class="content-bars-volume">';
        for (var key in innovation['financial']['explore_all_volumes']) {
            if (innovation['financial']['explore_all_volumes'].hasOwnProperty(key)) {
                out[++o] = '<div class="block-bar-volume">';
                out[++o] = '<div class="libelle">' + key + '</div>';
                out[++o] = '<div class="bar" style="width: 0px;"></div>';
                var classe_volume = (innovation['financial']['explore_all_volumes'][key] === 'Missing data') ? 'style_missing_data' : '';
                out[++o] = '<div class="value ' + classe_volume + '">' + innovation['financial']['explore_all_volumes'][key] + '</div>';
                out[++o] = '</div>';
            }
        }
        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End column-percent-50
    out[++o] = '</div>'; // End overflow-hidden
    // END BLOCK

    // START BLOCK
    if (innovation_1['tags_array'].length > 0 || innovation_2['tags_array'].length > 0) {
        out[++o] = '<div class="overflow-hidden padding-top-100">'; // Start overflow-hidden
        out[++o] = '<div class="column-percent-50 padding-right-60">'; // Start column-percent-50
        innovation = innovation_1;
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-20">Tags</div>';
        if (innovation['tags_array'].length > 0) {
            out[++o] = '<div class="content-tags">'; // Start content-tags
            for (var i = 0; i < innovation['tags_array'].length; i++) {
                out[++o] = '<div class="trending-tag transparent no-hover" data-target="' + innovation['tags_array'][i] + '">' + innovation['tags_array'][i] + '</div>';
            }
            out[++o] = '</div>'; // End content-tags
        }
        out[++o] = '</div>'; // End column-percent-50
        out[++o] = '<div class="column-percent-50 padding-left-60">'; // Start column-percent-50
        innovation = innovation_2;
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-20">Tags</div>';
        if (innovation['tags_array'].length > 0) {
            out[++o] = '<div class="content-tags">'; // Start content-tags
            for (var i = 0; i < innovation['tags_array'].length; i++) {
                out[++o] = '<div class="trending-tag transparent no-hover" data-target="' + innovation['tags_array'][i] + '">' + innovation['tags_array'][i] + '</div>';
            }
            out[++o] = '</div>'; // End content-tags
        }
        out[++o] = '</div>'; // End column-percent-50
        out[++o] = '</div>'; // End overflow-hidden
    }
    // END BLOCK


    out[++o] = '</div>'; // End central-content
    out[++o] = '</div>'; // End background-color-fff
    out[++o] = '</div>'; // End full-content-compare-detail

    var url = "/content/compare?inno=" + innovation_1['id'] + "," + innovation_2['id'];
    var title = "Compare : " + innovation_1['title'] + " vs " + innovation_2['title'];
    opti_update_url(url, 'compare_page_detail');
    update_menu_active();
    changePageTitle(title + ' | ' + global_wording['title']);
    $('#pri-main-container-without-header').html(out.join(''));
    window.scrollTo(0, 0);
    $('#main').removeClass('loading');
    init_tooltip();

    var container = $('#map_explore_comparison');

    var color_innovation_1 = '#84d8e5';
    var color_innovation_2 = '#005095';
    var color_common_markets = '#0097d0';
    var innovation1_markets = innovation_1['markets_in_array'];
    var innovation2_markets = innovation_2['markets_in_array'];
    var inno_common_markets = intersect(innovation1_markets, innovation2_markets);
    var colors = {};

    for (var i = 0; i < inno_common_markets.length; i++) {
        var country_code = inno_common_markets[i];
        innovation1_markets.splice(innovation1_markets.indexOf(country_code), 1);
        innovation2_markets.splice(innovation2_markets.indexOf(country_code), 1);
        colors[inno_common_markets[i]] = color_common_markets;
    }

    for (var i = 0; i < innovation1_markets.length; i++) {
        colors[innovation1_markets[i]] = color_innovation_1;
    }

    for (var i = 0; i < innovation2_markets.length; i++) {
        colors[innovation2_markets[i]] = color_innovation_2;
    }

    var the_map = new jvm.Map({
        container: container,
        map: 'world_mill_en',
        backgroundColor: "#FFFFFF",
        zoomOnScroll: false,
        regionsSelectable: false,
        regionStyle: {
            initial: {
                fill: '#DBE3EA'
            },
            hover: {
                'fill-opacity': 1,
                cursor: "default"
            }
        },
        series: {
            regions: [{
                attribute: 'fill'
            }]
        },
        panOnDrag: false,
        focusOn: {
            x: 0.5,
            y: 0.5,
            scale: 1,
            animate: true
        }
    });
    try {
        the_map.series.regions[0].setValues(colors);
    } catch (error) {

    }

    $('.venobox').venobox({
        infinigall: true            // default: false
    });
    init_explore_bars(75);
    init_tooltip();
    load_background_images();

    $('.action-go-back-to-compare').click(function () {
        init_explore_benchmark();
    });
}
