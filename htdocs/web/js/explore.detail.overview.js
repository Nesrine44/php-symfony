/**
 * explore_generate_overview_tab
 * @param innovation
 * @returns {string}
 */
function explore_generate_overview_tab(id, isFeedbackInv) {
    var out = [],
        o = -1;
    isFeedbackInv = isFeedbackInv || false;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return "";
    }
    var current_stage = (innovation['current_stage'] && innovation['current_stage'] != 'Empty' && innovation['current_stage'] != 'empty') ? innovation['current_stage'] : 'empty';
    var previous_stage = (innovation['previous_stage'] && innovation['previous_stage'] != 'Empty' && innovation['previous_stage'] != 'empty') ? innovation['previous_stage'] : 'empty';
    var versus_date = get_libelle_last_a();
    var financial_date = get_financial_date();
    var consumer_opportunity = innovation['consumer_opportunity'];
    var has_link_to_manage = (user_can_edit_this_innovation(innovation));
    var innovation_is_early_stage = (in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment']));
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    var portfolio_profile = innovation['growth_strategy'];
    var innovation_is_nba = check_if_innovation_is_nba(innovation);
    var cities = innovation['key_cities'];
    var innovation_id = innovation['id'];


    out[++o] = '<div class="explore-detail-content-overview content-tab-overview" >'; // Start explore-detail-content-overview

    out[++o] = explore_overview_get_right_buttons_html_template(id);



    if (!innovation_is_nba) {
        out[++o] = '<div class="central-content padding-top-20">'; // Start central-content
        if (isFeedbackInv) {
            out[++o] = '<div id="feedback-invitation-cta"></div>';
        }
        if (innovation_is_early_stage || innovation_is_a_service) {
            out[++o] = '<div id="overview-cta"></div>';
        }
        out[++o] = '<div class="overview-header margin-bottom-20">'; // Start overview-header
        var picture = innovation['explore']['images_detail'][0];
        out[++o] = '<div class="picture z-index-2">';
        out[++o] = '<img src="' + picture + '" alt="' + innovation['title'] + '"/>';
        out[++o] = '</div>';
        out[++o] = '<div class="content-infos">'; // Start content-infos
        out[++o] = '<div class="infos background-color-f2f2f2">'; // Start infos
        out[++o] = '<div class="inner">'; // Start inner
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-44 line-height-1 margin-bottom-10">' + innovation['title'] + '</div>';
        out[++o] = '<div class="font-montserrat-700 color-b5b5b5 font-size-16 line-height-1 margin-bottom-20">' + get_innovation_entity_brand_libelle(innovation) + '</div>';
        if (innovation['tags_array'].length > 0) {
            out[++o] = '<div class="content-tags margin-bottom-15">'; // Start content-tags
            for (var i = 0; i < innovation['tags_array'].length; i++) {
                out[++o] = '<div class="trending-tag transparent no-hover" data-target="' + innovation['tags_array'][i] + '">' + innovation['tags_array'][i] + '</div>';
            }
            out[++o] = '</div>'; // End content-tags
        }
        out[++o] = '<div class="margin-top-10 margin-bottom-10 color-000 font-size-20 font-work-sans-600 line-height-1">Why did we design this innovation?</div>';
        var classe_why_invest = (innovation['why_invest_in_this_innovation']) ? "" : 'style_missing_data';
        var text_why_invest = (innovation['why_invest_in_this_innovation']) ? innovation['why_invest_in_this_innovation'] : 'Missing data';
        out[++o] = '<div class="color-000 margin-bottom-30 font-size-15 line-height-1-53 font-work-sans-300 ' + classe_why_invest + '">' + text_why_invest + '</div>';
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
                out[++o] = '<a href="' + return_proper_link(innovation['website_url']) + '" target="_blank" title="' + round_title + '" class="explore-detail-round-link with-tooltip  ' + round_class + '"></a>';
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
        out[++o] = '</div>'; // End inner
        out[++o] = '</div>'; // End infos
        out[++o] = '</div>'; // End content-infos
        out[++o] = '</div>'; // End overview-header

        out[++o] = '</div>'; // End central-content
        out[++o] = '<div class="central-content-994">'; // Start central-content-994
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-30">Project ID</div>';
        out[++o] = '<div class="content-infos-project-id">'; // Start content-infos-project-id

        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + current_stage + '">'; // Start info-project-id
        out[++o] = '<div class="icon darker ' + current_stage + '"></div>';
        var style_text = (current_stage) ? "" : 'style_missing_data';
        var text_libelle = returnGoodStage(current_stage);
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
        if (data_target == 'new business model') {
            data_target = 'new_business_model';
        }
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start new business model
        out[++o] = '<div class="icon ' + data_target + '"></div>';
        var style_text = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? innovation['innovation_type'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End new business model

        if (!innovation_is_a_service) {
            var value_growth_model = (innovation['growth_model'] == "fast_growth") ? "Fast Growth" : "Slow Build";
            var classe_growth_model = innovation['growth_model'];
            out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + classe_growth_model + '">'; // Start info-project-id
            out[++o] = '<div class="icon size-26 light ' + classe_growth_model + '"></div>';
            out[++o] = '<div class="text font-montserrat-500"> ' + value_growth_model + '</div>';
            out[++o] = '</div>'; // End info-project-id
        }

        if (!innovation_is_a_service) {
            var data_classe = string_to_classe(pri_get_libelle_consumer_opportunity_by_id(consumer_opportunity));
            var libelle_coop = pri_get_libelle_consumer_opportunity_by_id(consumer_opportunity);
            var data_target = (data_classe != 'empty') ? data_classe : 'default_coop';
            out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start info-project-id
            out[++o] = '<div class="icon size-26 ' + data_target + '"></div>';
            var style_text = (libelle_coop != "Empty") ? "" : 'style_missing_data';
            var text_libelle = (libelle_coop != "Empty") ? libelle_coop : 'Missing data';
            out[++o] = '<div class="text font-montserrat-700 text-transform-uppercase color-' + data_target + ' ' + style_text + '"> ' + text_libelle + '</div>';
            out[++o] = '</div>'; // End info-project-id
        }

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

        out[++o] = '</div>'; // End content-infos-project-id
        out[++o] = '</div>'; // End central-content-994

        if (!innovation_is_a_service) {
            out[++o] = '<div class="central-content-994">'; // Start central-content-994
            out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden

            out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
            out[++o] = '<div class="margin-right-20">'; // Start margin-right-20
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-50">Elevator Pitch</div>';
            if (!in_array(current_stage, ['discover', 'ideate', 'experiment'])) {
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
                var style_universal_information_4 = (innovation['universal_key_information_4']) ? "" : 'style_missing_data';
                var text_universal_information_4 = (innovation['universal_key_information_4']) ? innovation['universal_key_information_4'] : 'Missing data';
                if (innovation['universal_key_information_4'] && innovation['universal_key_information_4_vs']) {
                    text_universal_information_4 += ' vs ' + innovation['universal_key_information_4_vs'];
                } else if (!innovation['universal_key_information_4'] && innovation['universal_key_information_4_vs']) {
                    text_universal_information_4 = innovation['universal_key_information_4_vs'];
                }
                var text_universal_information_5 = (innovation['universal_key_information_5']) ? innovation['universal_key_information_5'] : 'Missing data';
                out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Perfect Serve</div>';
                out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 margin-bottom-15 ' + style_universal_information_1 + '">' + text_universal_information_1 + '</div>';
                out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">RTM</div>';
                out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 margin-bottom-15 ' + style_universal_information_2 + '">' + text_universal_information_2 + '</div>';

                out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Price index versus main competitor</div>';
                out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 margin-bottom-15 ' + style_universal_information_4 + '">' + text_universal_information_4 + '</div>';
                if (!innovation['new_to_the_world']) {
                    out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1">Price index versus main reference motherbrand</div>';
                    out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ' + style_universal_information_3 + '">' + text_universal_information_3 + '</div>';
                }
                if (innovation['universal_key_information_5']) {
                    out[++o] = '<div class="font-work-sans-600 color-b5b5b5 font-size-14 line-height-1 margin-top-15">Other</div>';
                    out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ">' + text_universal_information_5 + '</div>';
                }
                out[++o] = '<div class="margin-bottom-40"></div>';
            }
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-20">Story</div>';
            var style_story = (innovation['story']) ? "" : 'style_missing_data';
            var text_story = (innovation['story']) ? innovation['story'] : 'Missing data';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ' + style_story + '">' + text_story + '</div>';
            out[++o] = '</div>'; // End margin-right-20
            out[++o] = '</div>'; // End column-percent-50

            out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
            out[++o] = '<div class="margin-left-20">'; // Start margin-left-20
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
                out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation"></a>';
                out[++o] = '</div>';
            } else if (innovation['beautyshot_picture']) {
                used = innovation['beautyshot_picture']['thumbnail'];
                out[++o] = '<div class="explore-detail-main-picture">';
                out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
                out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation"></a>';
                out[++o] = '</div>';
            } else {
                used = innovation['explore']['images_detail'][0];
                out[++o] = '<div class="explore-detail-main-picture">';
                out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
                out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation"></a>';
                out[++o] = '</div>';
            }
            if (innovation['explore']['images_detail'].length > 0) {
                out[++o] = '<div class="content-other-pictures position-relative margin-top-20">';
                for (var i = 0; i < innovation['explore']['images_detail'].length; i++) {
                    var other_picture = innovation['explore']['images_detail'][i];
                    if (used != other_picture) {
                        out[++o] = '<div class="explore-detail-other-picture">';
                        out[++o] = '<img alt="" src="' + other_picture + '" data-action="zoom"/>';
                        out[++o] = '<a href="' + other_picture + '" class="hover-effect venobox" data-gall="innovation"></a>';
                        out[++o] = '</div>';
                    }
                }
                out[++o] = '</div>';
            }
            out[++o] = '</div>'; // End margin-left-20
            out[++o] = '</div>'; // End column-percent-50

            out[++o] = '</div>'; // End overflow-hidden
            out[++o] = '</div>'; // End central-content-994
        }

        out[++o] = '<div class="central-content margin-top-40 margin-bottom-60">'; // Start central-content
        out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden

        out[++o] = '<div class="column-percent-30">'; // Start column-percent-30
        out[++o] = '<div class="icon value-proposition light explore-detail-overview-icon-title-big"></div>';
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Value Proposition</div>';
        if (innovation_is_a_service) {
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Unique experience</div>';
            var style_text = (innovation['unique_experience']) ? "" : 'style_missing_data';
            var text_libelle = (innovation['unique_experience']) ? innovation['unique_experience'] : 'Missing data';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
        } else {
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Uniqueness</div>';
            var style_text = (innovation['value_proposition']) ? "" : 'style_missing_data';
            var text_libelle = (innovation['value_proposition']) ? innovation['value_proposition'] : 'Missing data';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
        }
        out[++o] = '</div>'; // End column-percent-30

        out[++o] = '<div class="column-percent-40">'; // Start column-percent-40
        out[++o] = '<div class="margin-right-40 margin-left-40">'; // Start margins
        out[++o] = '<div class="icon consumer light explore-detail-overview-icon-title-big"></div>';
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Consumer</div>';
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Consumer benefit</div>';
        var style_text = (innovation['consumer_insight']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['consumer_insight']) ? innovation['consumer_insight'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';

        if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate'])) {
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13 margin-top-40">Early adopters persona</div>';
            var style_text = (innovation['early_adopter_persona']) ? "" : 'style_missing_data';
            var text_libelle = (innovation['early_adopter_persona']) ? innovation['early_adopter_persona'] : 'Missing data';
            out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';
        }

        out[++o] = '</div>'; // End margins
        out[++o] = '</div>'; // End column-percent-40

        out[++o] = '<div class="column-percent-30">'; // Start column-percent-30
        out[++o] = '<div class="icon business-priority light explore-detail-overview-icon-title-big"></div>';
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-26 line-height-1 margin-bottom-50 text-align-center">Source of Business</div>';

        out[++o] = '<div class="font-montserrat-700 color-000 font-size-20 line-height-1 margin-bottom-13">Size of the prize</div>';
        var style_text = (innovation['source_of_business']) ? "" : 'style_missing_data';
        var text_libelle = (innovation['source_of_business']) ? innovation['source_of_business'] : 'Missing data';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-19 line-height-1-58 ' + style_text + '">' + text_libelle + '</div>';

        out[++o] = '</div>'; // End column-percent-30

        out[++o] = '</div>'; // End overflow-hidden
        out[++o] = '</div>'; // End central-content

        if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate', 'experiment'])) {
            out[++o] = '<div class="central-content-994 margin-top-40 margin-bottom-60">'; // Start central-content-994
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-0">Markets today</div>';
            var markets_in = (innovation["markets_in"] != undefined) ? innovation["markets_in"] : '[]';
            out[++o] = "<div class='content-market-in-overview  not-loaded disabled no-zoom-map' data-name='markets_in' data-value='" + markets_in + "' data-id='" + innovation['id'] + "'></div>";
            /*
             out[++o] = '<div class="subtitle_bordered margin-bottom-20">' + innovation['title'] + ' markets today</div>';
             out[++o] = '<div class="content_market_in_libelles margin-bottom-60">';

             var all_markets = pri_get_country_name_array_by_code_array(innovation['markets_in_array']);
             if (all_markets.length == 0) {
             out[++o] = '<div class="content_market_in_libelles_column style_missing_data">Missing data</div>';
             } else {
             all_markets.sort();
             var opened = false;
             for (var i = 0; i < (all_markets.length); i++) {
             if (!opened) {
             opened = true;
             out[++o] = '<div class="content_market_in_libelles_column">';
             }
             out[++o] = all_markets[i] + '<br>';
             if (((i + 1) % 5 == 0)) {
             out[++o] = '</div>';
             opened = false;
             }
             }
             if (opened) {
             out[++o] = '</div>';
             }
             }
             out[++o] = '</div>';
             */
            out[++o] = '</div>'; // End central-content-994
        }

        if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate'])) {
            out[++o] = '<div class="central-content-994 margin-top-40 margin-bottom-60">'; // Start central-content-994
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-40">Volumes <span class="color-d2d2d2">(' + global_wording['volume_unit'] + ')</span></div>';
            out[++o] = '<div class="content-bars-volume margin-bottom-60">';
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
            out[++o] = '</div>'; // End central-content-994
        }

        if ((has_link_to_manage || check_if_user_is_management()) && !innovation_is_a_service && !innovation_is_early_stage) {
            var libelle_current_date = get_financial_date_libelle(financial_date);
            out[++o] = '<div class="background-color-gradient-161541 padding-top-60 padding-bottom-60">'; // Start background-color-gradient-161541
            out[++o] = '<div class="central-content-994">'; // Start central-content-994
            if (in_array(current_stage, ['permanent_range', 'discontinued']) || innovation['is_frozen']) {
                out[++o] = '<div class="font-montserrat-700 color-fff font-size-30 line-height-1 margin-bottom-10">Performance over time</div>';
            } else {
                out[++o] = '<div class="font-montserrat-700 color-fff font-size-30 line-height-1 margin-bottom-10">Monitor Performance ' + libelle_current_date + '</div>';
            }
            out[++o] = '<div class="font-work-sans-400 font-size-14 color-03aff0 line-height-1 margin-bottom-40">';
            out[++o] = '<span class="icon confidential"></span>';
            out[++o] = '<span class="display-inline vertical-align-middle margin-left-5"> Only members of your project team, the Global Innovation Team and Group top management can view this information.</span>';
            out[++o] = '</div>';

            if (!in_array(current_stage, ['permanent_range', 'discontinued']) && !innovation['is_frozen']) {
                out[++o] = '<div class="overflow-hidden margin-top-40">'; // Start overflow-hidden
                out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
                out[++o] = '<div class="font-montserrat-500 margin-bottom-30 line-height-1-5 color-9b9b9b font-size-20">Consolidated performance <span class="libelle_current_le"></span> <span class="color-03AFF0-0-8">vs <span class="libelle_versus_date"></span>*</span></div>';

                out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
                out[++o] = '<div class="column-percent-50">'; // Start column-percent-50

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

                out[++o] = '</div>'; // End column-percent-50
                out[++o] = '<div class="column-percent-50">'; // Start column-percent-50

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

                out[++o] = '</div>'; // End column-percent-50
                out[++o] = '</div>'; // End overflow-hidden
                out[++o] = '</div>'; // End column-percent-50


                out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
                out[++o] = '<div class="font-montserrat-500 margin-bottom-30 line-height-1-5 color-9b9b9b font-size-20">Key indicators</div>';

                out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
                out[++o] = '<div class="column-percent-50">'; // Start column-percent-50

                out[++o] = '<div class="other-info-consolidation level_of_investment" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">'; // Start other-info-consolidation
                out[++o] = '<div class="libelle"><span>A&P/Net Sales ratio</span>';
                out[++o] = '<div class="helper-button" data-target="level_of_investment">?</div>';
                out[++o] = '</div>';
                out[++o] = '<div class="under-libelle color-9b9b9b"><span>0</span> % <div class="consolidation-movement positive">0 pts</div></div>';
                out[++o] = '</div>'; // End other-info-consolidation

                out[++o] = '<div class="other-info-consolidation level_of_profitability" data-percent="0" data-old-value="0" data-old-date="">'; // Start other-info-consolidation
                out[++o] = '<div class="libelle">';
                out[++o] = '<span>CM/Net Sales ratio</span>';
                out[++o] = '<div class="helper-button" data-target="level_of_profitability">?</div>';
                out[++o] = '</div>';
                out[++o] = '<div class="under-libelle color-9b9b9b"><span>0</span> % <div class="consolidation-movement positive">0 pts</div></div>';
                out[++o] = '</div>'; // End other-info-consolidation

                out[++o] = '</div>'; // End column-percent-50
                out[++o] = '<div class="column-percent-50">'; // Start column-percent-50

                out[++o] = '<div class="other-info-consolidation cm_per_case" data-percent="0" data-old-value="0" data-old-date="">'; // Start other-info-consolidation
                out[++o] = '<div class="libelle">';
                out[++o] = '<span>CM per case</span>';
                out[++o] = '</div>';
                out[++o] = '<div class="under-libelle color-9b9b9b"><span>0</span> € <div class="consolidation-movement positive">0 %</div></div>';
                out[++o] = '</div>'; // End other-info-consolidation

                out[++o] = '<div class="other-info-consolidation cogs_per_case" data-percent="0" data-old-value="0" data-old-date="">'; // Start other-info-consolidation
                out[++o] = '<div class="libelle">';
                out[++o] = '<span>COGS per case</span>';
                out[++o] = '</div>';
                out[++o] = '<div class="under-libelle color-9b9b9b"><span>0</span> € <div class="consolidation-movement positive">0 %</div></div>';
                out[++o] = '</div>'; // End other-info-consolidation

                out[++o] = '</div>'; // End column-percent-50
                out[++o] = '</div>'; // End overflow-hidden

                out[++o] = '</div>'; // End column-percent-50
                out[++o] = '</div>'; // End overflow-hidden
            }

            if (!in_array(current_stage, ['permanent_range', 'discontinued']) && !innovation['is_frozen']) {
                out[++o] = '<div class="padding-top-35 padding-bottom-15"></div>'; // padding-top-35
                out[++o] = '<div class="font-montserrat-500 margin-bottom-30 line-height-1-5 color-9b9b9b font-size-20">Performance over time</div>';
            }
            out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
            out[++o] = '<div class="column-percent-50">'; // Start column-percent-50


            out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
            out[++o] = '<div class="column-percent-50">'; // Start column-percent-50

            out[++o] = '<div class="other-info-consolidation cumul_total_ap" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">'; // Start other-info-consolidation
            out[++o] = '<div class="libelle"><span>Cumul Total A&P</span>';
            out[++o] = '<div class="helper-button" data-target="cumul_total_ap">?</div>';
            out[++o] = '</div>';
            out[++o] = '<div class="under-libelle"><span>0</span> k€ <div class="consolidation-movement positive">0 %</div></div>';
            out[++o] = '</div>'; // End other-info-consolidation

            out[++o] = '</div>'; // End column-percent-50
            out[++o] = '<div class="column-percent-50">'; // Start column-percent-50

            out[++o] = '<div class="other-info-consolidation cumul_caap" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">'; // Start other-info-consolidation
            out[++o] = '<div class="libelle"><span>Cumul CAAP</span>';
            out[++o] = '<div class="helper-button" data-target="cumul_caap">?</div>';
            out[++o] = '</div>';
            out[++o] = '<div class="under-libelle"><span>0</span> k€ <div class="consolidation-movement positive">0 %</div></div>';
            out[++o] = '</div>'; // End other-info-consolidation

            out[++o] = '</div>'; // End column-percent-50
            out[++o] = '</div>'; // End overflow-hidden


            out[++o] = '</div>'; // End column-percent-50
            out[++o] = '</div>'; // End overflow-hidden
            out[++o] = '<div class="padding-bottom-15"></div>'; // padding-bottom-15

            if (!in_array(current_stage, ['permanent_range', 'discontinued']) && !innovation['is_frozen']) {
                out[++o] = '<div class="font-work-sans-400 font-size-12 line-height-1 color-03AFF0-0-8 margin-bottom-20">*Evolutions at current rate. All figures from <span class="libelle_current_fy">FY19</span> are restated for IFRS15 norm.</div>';
            } else {
                var last_financial_update = (innovation['financial_updated_at']) ? gmdate('M dd, Y', innovation['financial_updated_at']) : 'N/A';
                out[++o] = '<div class="font-montserrat-500 color-fff-0-4 font-size-20 line-height-1-5 margin-bottom-40">Last updated <span class="color-03AFF0-0-8">' + last_financial_update + '</span></div>';
            }
            out[++o] = '</div>'; // End central-content-994
            out[++o] = '<div class="central-content-994 background-color-fff">'; // Start central-content-994
            out[++o] = '<div class="padding-top-30 padding-bottom-30 padding-left-40 padding-right-40">'; // Start paddings
            out[++o] = '<div class="font-work-sans-600 font-size-20 line-height-1-5 color-000">CAAP evolution</div>';
            out[++o] = '<div class="font-work-sans-300 font-size-15 line-height-1-53 color-000 margin-bottom-40">Follow your innovation\'s ROI over time.</div>';
            out[++o] = '<div class="content-financial-overview-chart position-relative">';
            out[++o] = '<img class="mask-img" src="/images/masks/915x520.png"/>';
            out[++o] = '<canvas id="explore-detail-chart-financial" class="absolute-chart" width="915" height="520"></canvas>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End paddings
            out[++o] = '</div>'; // End central-content-994
            out[++o] = '</div>'; // End background-color-gradient-161541
        }
        if (innovation['tags_array'].length > 0) {
            out[++o] = '<div class="central-content-994 margin-top-60 margin-bottom-60">'; // Start central-content-994
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-20">Tags</div>';
            out[++o] = '<div class="content-tags">'; // Start content-tags
            for (var i = 0; i < innovation['tags_array'].length; i++) {
                out[++o] = '<div class="trending-tag transparent no-hover" data-target="' + innovation['tags_array'][i] + '">' + innovation['tags_array'][i] + '</div>';
            }
            out[++o] = '</div>'; // End content-tags
            out[++o] = '</div>'; // End central-content-994
        }

    }

    //affichage nba
    if (innovation_is_nba) {

        out[++o] = '<div class="central-content padding-top-30 height-500 ">'; // Start central-content



        out[++o] = '<div class="central-content border-radius-3 padding-bottom-20">'; // Start central-content-994

        out[++o] = '<div class=" cookie-notificator error no-cookie-check ns-show">';
        out[++o] = '<div class="position relative">'; // Start content-infos
        out[++o] = '<div class="content-percent-50 padding-left-20 padding-top-5 ">'; // Start content-infos
        out[++o] = '<div class="beta position-absolute">'; // Start beta
        out[++o] = '<div class="font-montserrat color-fff text-align-center font-size-10 font-weight-bold padding-top-5">Beta</div>';
        out[++o] = '</div>'; // End beta
        out[++o] = '</div>'; // End 
        out[++o] = '<div class="content-percent-50 margin-left-60 ">'; // Start content-infos
        out[++o] = '<div class="content background-color-f2f2f2">';
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-14  padding-bottom-5">You can manage your innovation team members’ access directly from this section.</div>';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-14">Only members of your project team and people you specifically invited have access.</div>';
        out[++o] = '<div class="font-work-sans-300 color-000 font-size-14 color-b5b5b5">NB: Global Innovation Team and Group top management have access to all projects.</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End central-content-994


        // START OVERVIEw-HEADER
        out[++o] = '<div class="overview-header margin-bottom-20 nba">'; // Start overview-header

        out[++o] = '<div class="content-infos">'; // Start content-infos
        out[++o] = '<div class="infos background-color-f2f2f2">'; // Start infos
        out[++o] = '<div class="inner">'; // Start inner
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-44 line-height-1 margin-bottom-10">' + innovation['title'] + '</div>';
        out[++o] = '<div class="font-montserrat-700 color-b5b5b5 font-size-16 line-height-1 margin-bottom-20">' + get_innovation_entity_brand_libelle(innovation) + '</div>';
        if (innovation['tags_array'].length > 0) {
            out[++o] = '<div class="content-tags margin-bottom-15">'; // Start content-tags
            for (var i = 0; i < innovation['tags_array'].length; i++) {
                out[++o] = '<div class="trending-tag transparent no-hover" data-target="' + innovation['tags_array'][i] + '">' + innovation['tags_array'][i] + '</div>';
            }
            out[++o] = '</div>'; // End content-tags
        }
        out[++o] = '<div class="content-overview-header-contact">'; // Start content-overview-header-contact
        out[++o] = '<div class="contact-picture loading-bg" data-bg="' + innovation['contact']['picture'] + '"></div>';
        out[++o] = '<div class="contact-infos">'; // Start contact-infos
        out[++o] = '<div class="font-montserrat-500 color-b5b5b5 font-size-13 line-height-1 margin-bottom-3">Contact</div>';
        out[++o] = '<div class="position-relative height-20">'; // Start position-relative
        out[++o] = '<a class="link-profile hub-link text-decoration-none font-montserrat-700 font-size-16" href="/user/' + innovation['contact']['uid'] + '">' + innovation['contact']['username'] + '</a>';
        out[++o] = '<a href="mailto:' + innovation['contact']['email'] + '" class="pri-mail-contact-action explore-detail-round-link mail with-tooltip" title="Contact ' + innovation['contact']['username'] + '"></a>';
        out[++o] = '</div>'; // End position-relative
        out[++o] = '</div>'; // End contact-infos
        out[++o] = '</div>'; // End content-overview-header-contact
        out[++o] = '</div>'; // End inner
        out[++o] = '</div>'; // End infos

        if (innovation['website_url']) {
            out[++o] = '<div class="block-link margin-top-20">';
            out[++o] = '<div class="form-element margin-left-40">'; // Start form-element
            out[++o] = '<div class="content-input-text with-icon">';
            out[++o] = '<div class="icon link-website "></div>';
            out[++o] = '<a class="font-montserrat-700 font-size-20 text-decoration-none hub-link line-height-1-58 display-inline-block vertical-align-middle" target="_blank" href="' + innovation['website_url'] + '">' + innovation['website_url'] + '</a>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; // End content-infos

        var picture = innovation['explore']['images_detail'][0];
        out[++o] = '<div class="picture z-index-2">';
        out[++o] = '<img src="' + picture + '" alt="' + innovation['title'] + '"/>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End overview-header

        out[++o] = '<div class="position-relative">'; // Start position-relative

        out[++o] = '<div class="showBlock-img-container cursor-pointer " id="bouton">'; // Start showBloack
        out[++o] = '<img class="showBlock-img position-absolute" src="/images/masks/pic-ancre.png "/>';
        out[++o] = '</div>'; // End showBlock


        out[++o] = '<div class="bitchou-container">'; // Start bitchou-container
        out[++o] = '<img class="bitchou-img" src="/images/masks/bitchou.png"/>';
        out[++o] = '<div class="gotocanvas font-work-sans padding-top-5 padding-right-5  text-align-center  color-787878 font-size-12">Go to Business Canvas</div>';
        out[++o] = '</div>'; // End bitchou-container
        out[++o] = '</div>'; // End position-relative

        out[++o] = '</div>'; // End central-content
        // END OVERVIEW HEADER


        out[++o] = '<div class="central-content-994">'; // Start central-content-994
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 line-height-1 margin-bottom-30">Project ID</div>';
        out[++o] = '<div class="content-infos-project-id">'; // Start content-infos-project-id

        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + current_stage + '">'; // Start info-project-id
        out[++o] = '<div class="icon darker ' + current_stage + '"></div>';
        var style_text = (current_stage) ? "" : 'style_missing_data';
        var text_libelle = returnGoodStage(current_stage);
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End current_stage

        var data_target = innovation['proper']['classification_type'];
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start
        out[++o] = '<div class="icon ' + data_target + '"></div>';
        var style_text = (innovation['classification_type'] && innovation['classification_type'] != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (innovation['classification_type'] && innovation['classification_type'] != "Empty") ? innovation['classification_type'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End classification_type

        var data_target = innovation['proper']['innovation_type'];
        if (data_target == 'new business model') {
            data_target = 'new_business_model';
        }
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start 
        out[++o] = '<div class="icon ' + data_target + '"></div>';
        var style_text = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (innovation['innovation_type'] && innovation['innovation_type'] != "Empty") ? innovation['innovation_type'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End innovation_type


        var data_target = innovation['proper']['new_business_opportunity'];
        if (data_target == 'new business opportunity') {
            data_target = 'new business opportunity';
        }
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start 
        out[++o] = '<div class="icon nbm"></div>';
        var style_text = (innovation['new_business_opportunity'] && innovation['new_business_opportunity'] != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (innovation['new_business_opportunity'] && innovation['new_business_opportunity'] != "Empty") ? innovation['new_business_opportunity'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End new business opportunity



        if (!innovation_is_a_service) {
            var value_growth_model = (innovation['growth_model'] == "fast_growth") ? "Fast Growth" : "Slow Build";
            var classe_growth_model = innovation['growth_model'];
            out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + classe_growth_model + '">'; // Start
            out[++o] = '<div class="icon size-26 light ' + classe_growth_model + '"></div>';
            out[++o] = '<div class="text font-montserrat-500"> ' + value_growth_model + '</div>';
            out[++o] = '</div>'; // End growth model
        }

        if (!innovation_is_a_service) {
            var data_classe = string_to_classe(pri_get_libelle_consumer_opportunity_by_id(consumer_opportunity));
            var libelle_coop = pri_get_libelle_consumer_opportunity_by_id(consumer_opportunity);
            var data_target = (data_classe != 'empty') ? data_classe : 'default_coop';
            out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start 
            out[++o] = '<div class="icon size-26 ' + data_target + '"></div>';
            var style_text = (libelle_coop != "Empty") ? "" : 'style_missing_data';
            var text_libelle = (libelle_coop != "Empty") ? libelle_coop : 'Missing data';
            out[++o] = '<div class="text font-montserrat-700 text-transform-uppercase color-' + data_target + ' ' + style_text + '"> ' + text_libelle + '</div>';
            out[++o] = '</div>'; // End consumer opportunity
        }

        var data_target = innovation['proper']['growth_strategy'];
        if (data_target == 'new business acceleration') {
            data_target = 'new business acceleration';
        }
        out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start
        out[++o] = '<div class="light icon new_business_acceleration"></div>';
        var style_text = (innovation['growth_strategy'] && innovation['growth_strategy'] != "Empty") ? "" : 'style_missing_data';
        var text_libelle = (innovation['growth_strategy'] && innovation['growth_strategy'] != "Empty") ? innovation['growth_strategy'] : 'Missing data';
        out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
        out[++o] = '</div>'; // End new business acceleration

        if (!innovation_is_a_service) {
            out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_moc">'; // Start info-project-id
            out[++o] = '<div class="icon moc light size-26"></div>';
            var style_text = (innovation['moc'] && innovation['moc'] != "Empty") ? "" : 'style_missing_data';
            var text_libelle = (innovation['moc'] && innovation['moc'] != "Empty") ? innovation['moc'] : 'Missing data';
            out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
            out[++o] = '</div>'; // End 
        }

        if (!innovation_is_a_service) {
            out[++o] = '<div class="info-project-id helper-on-hover" data-target="default_category">'; // Start info-project-id
            out[++o] = '<div class="icon category light size-26"></div>';
            var style_text = (innovation['category']) ? "" : 'style_missing_data';
            var text_libelle = (innovation['category']) ? innovation['category'] : 'Missing data';
            out[++o] = '<div class="text font-montserrat-500 ' + style_text + '"> ' + text_libelle + '</div>';
            out[++o] = '</div>'; // End info-project-id
        }



        if (in_array(innovation['growth_strategy'], ['Big Bet'])) {
            var data_target = string_to_classe(innovation['growth_strategy']);
            out[++o] = '<div class="info-project-id helper-on-hover" data-target="' + data_target + '">'; // Start 
            out[++o] = '<div class="icon ' + data_target + ' light size-26"></div>';
            out[++o] = '<div class="text font-montserrat-500"> ' + innovation['growth_strategy'] + '</div>';
            out[++o] = '</div>'; // End 
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

        out[++o] = '</div>'; // End content-infos-project-id
        out[++o] = '</div>'; // End central-content-994


        out[++o] = '<div class="central-content-994 padding-top-30 padding-bottom-30 ">'; // Start central-content-994
        out[++o] = '<div class="headline padding-top-53 font-montserrat-700">Context</div>';
        // Content column
        out[++o] = '<div class="block overflow-hidden margin-bottom-100">';
        out[++o] = '<div class="column-percent-50  ">';
        out[++o] = '<div class="headline padding-top-50 margin-bottom-20 font-montserrat-700 ">Idea description</div>';
        var style_idea_description = (innovation['idea_description']) ? "" : 'style_missing_data';
        var text_idea_description = (innovation['idea_description']) ? innovation['idea_description'] : 'Missing data';
        out[++o] = '<div class="font-family-workSans   font-size-19  color-000 line-height-1-58 ' + style_idea_description + '">' + text_idea_description + '</div>';
        out[++o] = '</div>';
        out[++o] = '<div class="column-percent-50 heigt-471">';
        out[++o] = '<div class="headline padding-top-50 margin-bottom-90 font-montserrat-700 margin-bottom-20 ">Strategic intent / Mission</div>';
        var style_strategic_intent_mission = (innovation['strategic_intent_mission']) ? "" : 'strategic_intent_mission';
        var text_strategic_intent_mission = (innovation['strategic_intent_mission']) ? innovation['strategic_intent_mission'] : 'Missing data';
        out[++o] = '<div class="font-family-workSans    font-size-19   color-000 line-height-1-58 ' + style_strategic_intent_mission + '">' + text_strategic_intent_mission + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        // End Content column
        out[++o] = '<div class="headline padding-top-53  font-montserrat-700 ">Key cities</div>';
        for (var i = 0; i < cities.length; i++) {
            var city = cities[i];
            out[++o] = '<div class="  margin-top-70 display-inline-block margin-right-80">';
            out[++o] = '<div class="picture background-image-cover loading-bg border-radius-50 width-200 height-200 margin-left-200" data-bg="' + city['picture_url'] + '" data-default-bg="/images/default/city.png"></div>';
            out[++o] = '<div class="infos margin-top-20 ">';
            out[++o] = '<div class="name text-ellipsis  text-align-center  line-height-1-2 font-montserrat-700 color-000">' + city['city_name'] + '</div>';
            out[++o] = '<div class="subname text-ellipsis text-align-center  line-height-1-2 color-9b9b9b font-size-14 font-work-sans-400">' + city['country_name'] + '</div>';
            out[++o] = '</div>';
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; //End central-content-994


        out[++o] = '<div class="central-content-994"  id="monancre">'; // Start central-content-994
        out[++o] = '<div class="font-montserrat-700  padding-top-30  padding-bottom-55 font-size-30 font-weight-bold">  Revenue coming from</div>';
        out[++o] = '</div>';


        var canvas_array = innovation['canvas_collection'];
        if (canvas_array.length > 0) {
            var first_canvas = canvas_array[0];

            out[++o] = '<div class="central-content-994  margin-left-140 " >'; // Start central-content-994
            out[++o] = '<div class="form-element margin-top-20 width-477 height-44  ">'; // Start form-element
            out[++o] = '<div class="content-select">';
            out[++o] = '<select class="field font-size-26 padding-bottom-34 font-weight-bold font-montserrat-700" id="select-canvas_first_title" ' + +' data-value="' + first_canvas['title'] + '">';
            for (var i = 0; i < canvas_array.length; i++) {
                out[++o] = '<option value="' + canvas_array[i]['id'] + '" ' + ((i == 0) ? 'selected="selected"' : '') + '>' + canvas_array[i]['title'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
            out[++o] = '</div>'; // End central-content-994


            out[++o] = '<div class="explore-detail-content-overview-canvas">'; // Start explore-detail-content-overview-canvas

            out[++o] = explore_detail_overview_get_canvas_content(first_canvas, innovation_id);
            out[++o] = '</div>'; // End explore-detail-content-overview-canvas"

        }


        out[++o] = '<div class="central-content-994 padding-top-30">'; // Start central-content-994

        out[++o] = '<div class="position-center text-align-center padding-bottom-60">';
        out[++o] = '<a href="/learn/article/new-business" class="hub-button link-list-innovations">Learn more</a>';
        out[++o] = '</div>';


        out[++o] = '</div>'; // End central-content-994

        out[++o] = '<div class="overflow-hidden"">'; // Start overflow-hidden
        out[++o] = '<div class="central-content-994 ">'; // Start column-percent-50

        var used = null;

        out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column le
        if (!innovation_is_a_service && innovation['packshot_picture']) {
            used = innovation['packshot_picture']['thumbnail'];
            out[++o] = '<div class="explore-detail-main-picture">';
            out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
            out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation"></a>';
            out[++o] = '</div>';
            out[++o] = '</div>';
        } else {
            used = innovation['explore']['images_detail'][0];
            out[++o] = '<div class="explore-detail-main-picture">';
            out[++o] = '<img alt="" src="' + used + '" data-action="zoom"/>';
            out[++o] = '<a href="' + used + '" class="hover-effect venobox" data-gall="innovation"></a>';
            out[++o] = '</div>';

        }
        out[++o] = '</div>';

        if (innovation['explore']['images_detail'].length > 0) {
            out[++o] = '<div class="content-other-pictures position-relative margin-top-20">';
            for (var i = 0; i < innovation['explore']['images_detail'].length; i++) {
                var other_picture = innovation['explore']['images_detail'][i];
                if (used != other_picture) {
                    out[++o] = '<div class="explore-detail-other-picture">';
                    out[++o] = '<img alt="" src="' + other_picture + '" data-action="zoom"/>';
                    out[++o] = '<a href="' + other_picture + '" class="hover-effect venobox" data-gall="innovation"></a>';
                    out[++o] = '</div>';
                }
            }
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; // end overflow-hidden
        out[++o] = '</div>'; // End central-content-994


        var open_question = innovation['open_question'];
        if (open_question && open_question['message']) {
            out[++o] = '<div class="BlockQuestion margin-top-50">'; // Start BlockQuestion
            out[++o] = '<div class="background-color-f2f2f2 padding-top-40 padding-bottom-40">'; // Start background-color-f2f2f2
            out[++o] = '<div class="central-content-994">'; // Start central-content-994
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-30">Open questions from the Innovation Team</div>';
            var text_open_question = open_question['message'];
            var open_question_contact = open_question['contact'];
            var date_open_question = gmdate('M dd, Y', open_question['updated_at']);
            out[++o] = '<div class="content-open-questions position-relative">'; // Start content-open-question
            out[++o] = '<div class="content-overview-header-contact margin-bottom-30">'; // Start content-overview-header-contact
            out[++o] = '<div class="contact-picture loading-bg" data-bg="' + open_question_contact['picture'] + '"></div>';
            out[++o] = '<div class="contact-infos">'; // Start contact-infos
            out[++o] = '<div class="position-relative height-20 margin-bottom-3">'; // Start position-relative
            out[++o] = '<div class="color-000 font-montserrat-700 font-size-16 display-inline-block">' + open_question_contact['username'] + '</div>';
            out[++o] = '</div>'; // End position-relative
            out[++o] = '<div class="font-montserrat-500 color-b5b5b5 font-size-13 line-height-1">' + date_open_question + '</div>';
            out[++o] = '</div>'; // End contact-infos
            out[++o] = '</div>'; // End content-overview-header-contact
            out[++o] = '<div class="open-questions font-work-sans-300 font-size-19 color-000 line-height-1-58">';
            out[++o] = '<div class="quote left"></div>';
            out[++o] = '<div class="quote right"></div>';
            out[++o] = text_open_question;
            out[++o] = '</div>';
            out[++o] = '</div>'; // End content-open-question
            out[++o] = '</div>'; // End central-content-994
            out[++o] = '</div>'; // End background-color-f2f2f2
            out[++o] = '</div>';
        }

        //footer
        out[++o] = '<div class="background-color-gradient-161541  padding-bottom-60 padding-top-40 height-600" >'; // Start background-color-gradient-161541
        out[++o] = '<div class="central-content-994">'; // Start central-content-994

        out[++o] = '<div class="font-montserrat-700 color-fff font-size-30 line-height-1 margin-bottom-10">Monitor Performance LE1 19</div>';


        out[++o] = '<div class="font-work-sans-400 font-size-14 color-03aff0 line-height-1 margin-bottom-40">';
        out[++o] = '<span class="icon confidential"></span>';
        out[++o] = '<span class="display-inline vertical-align-middle margin-left-5"> Only members of your project team, the Global Innovation Team and Group top management can view this information.</span>';
        out[++o] = '</div>';

        out[++o] = '<div class="overflow-hidden margin-top-40 margin-bottom-100">'; // Start overflow-hidden
        out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
        out[++o] = '<div class="font-montserrat-500 margin-bottom-30 line-height-1-5 color-9b9b9b font-size-20">People working on the project <span class="libelle_current_le"></div>';
        out[++o] = '<div class="width-1280 ">';
        out[++o] = '<div class="column-percent-30 ">';
        out[++o] = '<div class="margin-bottom-5 font-size-13 color-d2d2d2  font-family-WorkSans">Project owner disponibility </div>';

        var style_project_owner_disponibility = (innovation['project_owner_disponibility']) ? "" : 'project_owner_disponibility';
        var text_project_owner_disponibility = (innovation['project_owner_disponibility']) ? innovation['project_owner_disponibility'] : 'Missing data';
        out[++o] = '<div class=" font-size-36 color-fff   font-family-WorkSans ' + style_project_owner_disponibility + '">' + text_project_owner_disponibility + '</div>';

        out[++o] = '</div>';
        out[++o] = '<div class="column-percent-30">';
        out[++o] = '<div class="  font-size-13 color-d2d2d2   font-family-WorkSans">Full time employees (FTE)<div class="helper-button" data-target="full_time_employees">?</div></div>';
        var style_full_time_employees = (innovation['full_time_employees']) ? "" : 'full_time_employees';
        var text_full_time_employees = (innovation['full_time_employees']) ? innovation['full_time_employees'] : 'Missing data';
        out[++o] = '<div class=" font-size-36 color-fff margin-bottom-5   font-family-WorkSans ' + style_full_time_employees + '">' + text_full_time_employees + '</div>';
        out[++o] = '</div>';
        out[++o] = '<div class="column-percent-30">';
        out[++o] = '<div class=" font-size-13 color-d2d2d2   font-family-WorkSans">External<div class="helper-button" data-target="external_text">?</div></div>';
        var style_external_text = (innovation['external_text']) ? "" : 'external_text';
        var text_external_text = (innovation['external_text']) ? innovation['external_text'] : 'Missing data';
        out[++o] = '<div class="   margin-bottom-5  font-size-14   font-family-WorkSans  color-fff   line-height-1-43' + style_external_text + '">' + text_external_text + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';


        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
        out[++o] = '<div class="font-montserrat-500 margin-bottom-30 line-height-1-5 color-9b9b9b font-size-20">Financials over time <span class="libelle_current_le"></div>';
        out[++o] = '<div class="width-1280 ">';
        out[++o] = '<div class="column-percent-30">';
        out[++o] = '<div class="other-info-consolidation cumul_investment" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">';
        out[++o] = '<div class="libelle"><span>Cumul investments</span></div>';
        out[++o] = '<div class="under-libelle"><span>' + number_format_en(innovation['financial']['data']['cumul']['investment']) + '</span> k€</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '<div class="column-percent-30">';
        out[++o] = '<div class="other-info-consolidation cumul_revenue" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">';
        out[++o] = '<div class="libelle"><span>Cumul revenues</span></div>';
        out[++o] = '<div class="under-libelle"><span>' + number_format_en(innovation['financial']['data']['cumul']['revenue']) + '</span> k€</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '<div class="column-percent-30">';
        var style_investment_model = (innovation['investment_model']) ? "" : 'investment_model';
        var text_investment_model = (innovation['investment_model']) ? innovation['investment_model'] : 'Missing data';
        out[++o] = '<div class=" font-size-13 color-d2d2d2   font-family-WorkSans"> Investment model</div>';
        out[++o] = '<div class="   margin-bottom-5  font-size-20   font-family-WorkSans  color-fff ' + style_investment_model + '">' + text_investment_model + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>';

        out[++o] = '</div>'; // End central-content-994

        out[++o] = '</div>'; // End background-color-gradient-161541
        // END footer

    }

    out[++o] = '</div>'; // End explore-detail-content-overview
    return out.join('');


}

function explore_detail_overview_get_canvas_content(canvas, innovation_id) {
    var out = [],
        o = -1;
    out[++o] = ' <div   id="canvas1"  class=" demo-container ">';
    out[++o] = '<div class="central-content-994">'; // Start central-content-994
    out[++o] = '<div class="font-family-montserrat font-size-20 font-weight-bold color-000 margin-top-20 margin-bottom-20 "> Inspiring existing business model</div>';
    var style_description = (canvas['description']) ? "" : 'description';
    var text_description = (canvas['description']) ? canvas['description'] : 'Missing data';
    out[++o] = '<div class="font-family-WorkSans font-size-19 font-weight-300 color-000 line-height-1-58 display-inline-block vertical-align-middle ' + style_description + '">' + text_description + '</div>';

    out[++o] = '<a href="' + text_description + '" class="font-montserrat-700 font-size-20  color-fcb400 line-height-1-58 display-inline-block vertical-align-middle "' + style_description + '></a>';
    out[++o] = '</div>'; // End central-content-994


    out[++o] = '<div class="central-content padding-vertical-50">';
    out[++o] = '<div class="content-canvas">'; // Start content-canvas
    out[++o] = explore_get_content_canvas_html_template(canvas, innovation_id, false);
    out[++o] = '</div>'; // End content-canvas
    out[++o] = '</div>';
    out[++o] = '</div>'; //End demo-container


    return out.join('');
    out[++o] = '</div>'; //End content-overview
}


/**
 * explore_overview_get_right_buttons_html_template
 * @param id
 * @returns {*}
 */
function explore_overview_get_right_buttons_html_template(id) {
    var out = [],
        o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    out[++o] = '<div class="explore-overview-right-buttons-container">';
    out[++o] = '<div class="async-export link-export-ppt explore-overview-right-button transition-background-color with-tooltip-left" data-action="/api/export/innovation-to-ppt" data-id="' + innovation['id'] + '" data-export-type="quali" data-title="Exporting to PPT…" title="Export to PPT"><div class="visual-pitchou-for-round hidden nb-exports"><span>0</span><div class="triangle"></div></div></div>';
    out[++o] = '<div class="round-nb-views explore-overview-right-button transition-background-color with-tooltip-left" title="Number of views"><div class="visual-pitchou-for-round hidden nb-views"><span>0</span><div class="triangle"></div></div></div>';
    if (check_if_innovation_is_on_explore(id)) {
        out[++o] = '<div class="round-share explore-overview-right-button action-open-popup with-tooltip-left"  data-popup-target=".element-share-innovation-popup" title="Share this innnovation"><div class="visual-pitchou-for-round hidden nb-shares"><span>0</span><div class="triangle"></div></div></div>';
    }
    out[++o] = '</div>';
    return out.join('');
}

/**
 * Init visual explore pitchous (counters of exports and views).
 *
 * @param id
 * @param inherit_action
 */
function explore_initVisualExplorePitchous(id, inherit_action) {
    inherit_action = (typeof inherit_action != 'undefined') ? inherit_action : null;
    var the_data = { 'innovation_id': id, 'inherit_action': inherit_action };
    $.ajax({
        url: '/api/innovation/metrics/count',
        type: "POST",
        data: the_data,
        dataType: 'json',
        success: function(response, statut) {
            if (response['status'] == 'success') {
                $('.visual-pitchou-for-round.nb-views span').text(response['nb_views']);
                if (response['nb_views'] > 0) {
                    $('.visual-pitchou-for-round.nb-views').removeClass('hidden');
                }
                $('.visual-pitchou-for-round.nb-exports span').text(response['nb_exports']);
                if (response['nb_exports'] > 0) {
                    $('.visual-pitchou-for-round.nb-exports').removeClass('hidden');
                }
                $('.visual-pitchou-for-round.nb-shares span').text(response['nb_shares']);
                if (response['nb_shares'] > 0) {
                    $('.visual-pitchou-for-round.nb-shares').removeClass('hidden');
                }
            }
        },
        error: function(resultat, statut, e) {
            if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                console.log(resultat);
            }
        }
    });
}

/**
 * explore_detail_oveview_init_financial_data
 * @param id
 */
function explore_detail_oveview_init_financial_data(id) {
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }
    explore_detail_dataset_innovation = innovation['financial']['dataset'];
    var current_stage = innovation['current_stage'];
    var early_stages = ['discover', 'ideate', 'experiment'];
    var $element = $('.content-tab-overview');

    var statistics = {};
    statistics['net_sales'] = Math.round(innovation['financial']['data']['latest']['calc']['net_sales']);
    statistics['total_ap'] = Math.round(innovation['financial']['data']['latest']['calc']['total_ap']);
    statistics['caap'] = (!in_array(current_stage, early_stages)) ? Math.round(innovation['financial']['data']['latest']['calc']['caap']) : 0;
    statistics['volume'] = Math.round(innovation['financial']['data']['latest']['calc']['volume']);

    statistics['percent'] = explore_detail_financial_get_statistics_percent_movements(innovation);

    var total_volume_le = parseInt(innovation['financial']['data']['latest']['calc']['volume']);
    var total_total_ap = parseInt(innovation['financial']['data']['latest']['calc']['total_ap']);
    var total_ns_value = parseInt(innovation['financial']['data']['latest']['calc']['net_sales']);
    var total_cm_value = parseInt(innovation['financial']['data']['latest']['calc']['contributing_margin']);
    var total_cogs_value = parseInt(innovation['financial']['data']['latest']['calc']['cogs']);

    var cm_per_case = (total_cm_value != 0 && total_volume_le != 0) ? (total_cm_value / total_volume_le) : "N/A";
    var cogs_per_case = (total_cogs_value != 0 && total_volume_le != 0) ? (total_cogs_value / total_volume_le) : "N/A";
    statistics['cm_per_case'] = cm_per_case;
    statistics['cogs_per_case'] = cogs_per_case;

    var level_of_investment = calculate_level_of_investment(total_total_ap, total_ns_value);
    var level_of_profitability = calculate_level_of_profitability(total_cm_value, total_ns_value);

    statistics['level_of_investment'] = (isNaN(level_of_investment)) ? 0 : level_of_investment;
    statistics['level_of_profitability'] = (isNaN(level_of_profitability)) ? 0 : level_of_profitability;

    statistics['cumul_caap'] = parseInt(innovation['financial']['data']['cumul']['caap']);
    statistics['cumul_total_ap'] = parseInt(innovation['financial']['data']['cumul']['total_ap']);


    statistics['cumul_investment'] = Math.round(innovation['financial']['data']['cumul']['investment']);
    statistics['cumul_revenue'] = Math.round(innovation['financial']['data']['cumul']['revenue']);


    var keys = ['net_sales', 'total_ap', 'caap', 'volume', 'cumul_caap', 'cumul_total_ap'];
    for (var i = 0; i < keys.length; i++) {
        var el_key = keys[i];
        var el_data = statistics[el_key];
        var percent = statistics['percent'][el_key];
        if ($element.find('.other-info-consolidation.' + el_key).length > 0) {
            $element.find('.other-info-consolidation.' + el_key + ' .under-libelle span').html(number_format_en(el_data));
            if ($element.find('.other-info-consolidation.' + el_key + " .consolidation-movement").length > 0) {
                var percent_class = (percent >= 0) ? 'positive' : 'negative';
                $element.find('.other-info-consolidation.' + el_key + " .consolidation-movement").removeClass('positive negative').addClass(percent_class).html(Math.abs(percent) + ' %');
            }
        }
    }
    var percent_keys = ['level_of_investment', 'level_of_profitability'];
    for (var i = 0; i < percent_keys.length; i++) {
        var el_key = percent_keys[i];
        var el_data = statistics[el_key];
        var percent = statistics['percent'][el_key];
        if (el_data == 'N/A') {
            $element.find('.other-info-consolidation.' + el_key + ' .under-libelle span').html(el_data);
        } else {
            $element.find('.other-info-consolidation.' + el_key + ' .under-libelle span').html(number_format_en(el_data));
        }
        var percent_class = (percent >= 0) ? 'positive' : 'negative';
        $element.find('.other-info-consolidation.' + el_key + " .consolidation-movement").removeClass('positive negative').addClass(percent_class).html(Math.abs(percent) + ' pts');
    }
    var percent_euros = ['cm_per_case', 'cogs_per_case'];
    for (var i = 0; i < percent_euros.length; i++) {
        var el_key = percent_euros[i];
        var el_data = statistics[el_key];
        var percent = statistics['percent'][el_key];
        if (el_data == 'N/A') {
            $element.find('.other-info-consolidation.' + el_key + ' .under-libelle span').html(el_data);
        } else {
            $element.find('.other-info-consolidation.' + el_key + ' .under-libelle span').html(number_format_en(el_data));
        }
        if ($element.find('.other-info-consolidation.' + el_key + " .consolidation-movement").length > 0) {
            var percent_class = (percent >= 0) ? 'positive' : 'negative';
            $element.find('.other-info-consolidation.' + el_key + " .consolidation-movement").removeClass('positive negative').addClass(percent_class).html(Math.abs(percent) + ' %');
        }
    }
    var innovation_is_nba = check_if_innovation_is_nba(innovation);
    if (innovation_is_nba) {
        var keys = ['cumul_investment', 'cumul_revenue'];
        for (var i = 0; i < keys.length; i++) {
            var el_key = keys[i];
            var el_data = statistics[el_key];
            $('.other-info-consolidation.' + el_key + ' .under-libelle span').html(number_format_en(el_data));
        }
    }
}

/**
 * init explore detail.
 * @param innovation
 */
function init_explore_detail(innovation) {
    jQuery('#main').addClass('explore_detail');
    var debug = findGetParameter('debug');
    if (debug && debug == 'js') {
        console.log(innovation);
    }
    var $ = jQuery;

    //init_not_yet();
    $('#main').removeClass('loading');
    $('.venobox').venobox({
        infinigall: true // default: false
    });

    jQuery('.explore_detail .block-header-slider .content-caroussel.owl-carousel').owlCarousel({
        responsiveClass: true,
        loop: true,
        items: 1,
        nav: true,
        autoHeight: true,
        responsive: {
            0: {
                items: 1,
                nav: true
            }
        }
    });
    if ($('.content-market-in-overview').length > 0 && $('.content-market-in-overview').hasClass('not-loaded')) {
        $('.content-market-in-overview').removeClass('not-loaded').mapLister({
            identifier: 'overview-market-in',
            data: [],
            full_infos: false
        });
    }
    if ($('#map_explore_detail').length > 0) {
        var container = $('#map_explore_detail');
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
                selected: {
                    fill: '#0097d0' //'#1A3562'
                },
                hover: {
                    fill: '#DBE3EA',
                    'fill-opacity': 1,
                    cursor: "default"
                }
            },
            panOnDrag: false,
            focusOn: {
                x: 0.5,
                y: 0.5,
                scale: 1,
                animate: true
            },
        });
        var the_data = innovation['markets_in_array'];
        the_map.clearSelectedRegions();
        the_map.setSelectedRegions(the_data);
    }

    if ($('#explore-detail-chart-financial').length > 0) {
        Chart.defaults.global.legend.display = false;
        //Chart.defaults.global.tooltips.enabled = false;
        //Chart.interaction.enabled = false;
        Chart.defaults.global.hover.intersect = false;

        Chart.defaults.global.defaultFontColor = "#8ca0b3";
        Chart.defaults.global.defaultFontFamily = "Work Sans";
        Chart.defaults.global.defaultFontSize = 10;
        var the_dataset_innovation = innovation['financial']['dataset'];

        var explore_detail_innovationFinancialLineChartCanvas = document.getElementById('explore-detail-chart-financial').getContext('2d');
        var ticks_array = {
            min: the_dataset_innovation[2]['min_value']
        };

        var customTooltipsFinancial = function(tooltip) {
            // Tooltip Element
            var tooltipEl = jQuery('#tooltip-financial');
            if (!tooltipEl[0]) {
                jQuery('body').append('<div id="tooltip-financial" class="tooltipster-default pri-tooltip-consolidation"></div>');
                tooltipEl = jQuery('#tooltip-financial');
            }
            if (!tooltip.opacity) {
                tooltipEl.remove();
                jQuery('.chartjs-wrap canvas')
                    .each(function(index, el) {
                        jQuery(el).css('cursor', 'default');
                    });
                return;
            }
            jQuery(this._chart.canvas).css('cursor', 'pointer');
            if (tooltip.body) {
                tooltipEl.removeClass('right');
                var the_index = tooltip.dataPoints[0]['index'];
                var the_dataset_index = tooltip.dataPoints[0]['datasetIndex'];
                if (the_index !== null) {
                    var label = the_dataset_innovation[the_dataset_index]['data'][the_index]['label'];
                    if (label) {
                        var label_id = label.replace(' ', '_');
                        var caap_k = the_dataset_innovation[the_dataset_index]['data'][the_index]['caap'];
                        var cm_k = the_dataset_innovation[the_dataset_index]['data'][the_index]['cm'];
                        var ap_k = the_dataset_innovation[the_dataset_index]['data'][the_index]['ap'];
                        caap_k = (isNaN(caap_k)) ? 'N/A' : number_format_en(caap_k) + ' k€';
                        cm_k = (isNaN(cm_k)) ? 'N/A' : number_format_en(cm_k) + ' k€';
                        ap_k = (isNaN(ap_k)) ? 'N/A' : number_format_en(ap_k) + ' k€';
                        var top_tooltip = '<div class="top-tooltip"><div class="first-line">' + label + ' CAAP</div><div class="second-line">' + caap_k + '</div></div>';
                        var cm_tooltip = '<div class="content-tooltip"><div class="title">Contributing Margin</div><div class="value">' + cm_k + '</div></div>';
                        var ap_tooltip = '<div class="content-tooltip"><div class="title">Total A&P</div><div class="value">' + ap_k + '</div></div>';
                        var data = '<div class="pri-content-tooltip"><div class="pri-body-tooltip">' + top_tooltip + cm_tooltip + ap_tooltip + '</div><div class="triangle"></div></div>';
                        tooltipEl.html(data);

                        var height = tooltipEl.height();
                        var width = tooltipEl.width();
                        var left = currentMousePos.x - (width / 2);
                        var top = currentMousePos.y - height - 15;
                        if (left < 100) {
                            tooltipEl.addClass('right');
                            left = currentMousePos.x + 15;
                            top = currentMousePos.y - (height / 2);
                        }
                        tooltipEl.css({
                            opacity: 1,
                            left: left + 'px',
                            top: top + 'px'
                        });
                    }
                }
            }
        };


        var explore_detail_innovationFinancialLineChart = new Chart(explore_detail_innovationFinancialLineChartCanvas, {
            type: 'line',
            //showTooltips: false,
            data: {
                datasets: the_dataset_innovation
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                tooltips: {
                    enabled: false,
                    custom: customTooltipsFinancial
                },
                hover: {
                    mode: false,
                    intersect: false,
                    animationDuration: 0
                },
                scales: {
                    xAxes: [{
                        type: 'linear',
                        position: 'bottom',
                        ticks: ticks_array,
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        ticks: ticks_array,
                        gridLines: {
                            display: false
                        }
                    }]
                },
                animation: {
                    onComplete: function() {
                        var ctx = this.chart.ctx;

                        ctx.textAlign = "center";
                        ctx.textBaseline = "bottom";

                        // Y Axis
                        ctx.fillStyle = "#4a4a4a";
                        ctx.font = "14px 'Work Sans'";
                        ctx.fillText("CM (k€)", 75, 18);
                        // X Axis
                        ctx.fillText("Total A&P (k€)", 867, 468);

                        // positive-info
                        ctx.fillStyle = "#45ab34";
                        ctx.font = "14px 'Work Sans'";
                        ctx.fillText("Positive CAAP", 420, 15);

                        // neutral-info
                        ctx.fillStyle = "#b5b5b5";
                        ctx.font = "14px Work Sans";
                        ctx.fillText("Neutral CAAP", 863, 15);

                        // negative-info
                        ctx.fillStyle = "#e8485c";
                        ctx.fillText("Negative CAAP", 859, 267);

                        this.chart.config.data.datasets.forEach(function(dataset) {
                            if (dataset.label == "good_line") {
                                ctx.fillStyle = dataset.borderColor;
                                ctx.font = "14px 'Work Sans'";
                                var metas = dataset._meta;
                                var loaded = false;
                                for (var propertyName in metas) {
                                    if (!loaded) {
                                        var preview_position = null;
                                        var future_id = 1;
                                        dataset._meta[propertyName]['data'].forEach(function(p) {
                                            var new_position = { x: (p._model.x + 10), y: p._model.y };
                                            var the_case = null;
                                            var future_position = null;
                                            var future_p = dataset._meta[propertyName]['data'][future_id];
                                            if (future_p) {
                                                future_position = {
                                                    x: (future_p._model.x + 10),
                                                    y: future_p._model.y,
                                                    label: dataset.data[future_id]['label']
                                                };
                                            }
                                            var positions = {
                                                previous: preview_position,
                                                current: new_position,
                                                future: future_position,
                                                label: dataset.data[p._index]['label']
                                            };
                                            var best_position = get_graph_libelle_best_position(positions);

                                            var all_p = {
                                                previous: preview_position,
                                                current: best_position,
                                                future: future_position,
                                                the_case: the_case,
                                                label: dataset.data[p._index]['label']
                                            };
                                            //ctx.fillStyle = '#FFFFFF';
                                            //ctx.fillRect(new_position.x - 19, new_position.y - 26,38,18);
                                            ctx.fillStyle = dataset.borderColor;
                                            if (dataset.line_type != "second" || (dataset.line_type == "second" && preview_position)) {
                                                ctx.fillText(dataset.data[p._index]['label'], best_position.x, best_position.y);
                                            }
                                            preview_position = { x: p._model.x, y: p._model.y };
                                            future_id++;
                                        });
                                        loaded = true;
                                    }
                                }
                            }
                        });
                    }
                }
            }
        });
    }

    if ($('#feedback-invitation-cta').length > 0) {
        // Initialize feedback invitation tooltip
        var content = ''.concat(
            '<span class="vertical-align-middle">',
            '<span class="bold display-block" style="margin-bottom:5px;">',
            'The project team would like your feedback!',
            '</span>',
            'Help them turn this idea into a blockbuster success.',
            '</span>',
            '<button class="hub-button action-open-popup" data-popup-target=".element-feedback-answer-popup" style="float:right;">Give feedback</button>'
        );

        $('#feedback-invitation-cta').cookieNotificator({
            id: 'feedback-invitation-cta-toast',
            theme: 'notice',
            type: 'html',
            cookieCheck: false,
            deleteButton: false,
            content: content,
            customStyle: {
                'margin': '0 auto 20px',
                'max-width': '1200px'
            }
        });
    }

    var has_link_to_manage = (user_can_edit_this_innovation(innovation));
    var innovation_is_early_stage = (in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment']));
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    if ((check_if_user_is_management() || has_link_to_manage) && (innovation_is_early_stage || innovation_is_a_service)) {
        if (innovation_is_a_service) {
            if (check_if_user_is_management() || check_if_user_is_hq() || check_if_user_is_dev()) {
                var content = ''.concat(
                    '<div class="icon-private-toast"></div>',
                    '<span class="vertical-align-middle">',
                    '<span class="bold display-block" style="margin-bottom:5px;">',
                    'This innovation is a Service',
                    '</span>',
                    'Only members of the project team and people specifically invited have access.<br>',
                    '<span class="color-b5b5b5 font-work-sans-400"><span class="font-work-sans-500">NB:</span> Global Innovation Team and Group top management have access to all projects.</span>',
                    '</span>'
                );
            } else {
                var content = ''.concat(
                    '<div class="icon-private-toast"></div>',
                    '<span class="vertical-align-middle">',
                    '<span class="bold display-block" style="margin-bottom:5px;">',
                    'Your innovation is a Service',
                    '</span>',
                    'Only members of your project team and people you specifically invited have access.<br>',
                    '<span class="color-b5b5b5 font-work-sans-400"><span class="font-work-sans-500">NB:</span> Global Innovation Team and Group top management have access to all projects.</span>',
                    '</span>'
                );
            }
        } else if (innovation_is_early_stage) {
            if (check_if_user_is_management() || check_if_user_is_hq() || check_if_user_is_dev()) {
                var content = ''.concat(
                    '<div class="icon-private-toast"></div>',
                    '<span class="vertical-align-middle">',
                    '<span class="bold display-block" style="margin-bottom:5px;">',
                    'This innovation is in Early Stage',
                    '</span>',
                    'Only members of the project team and people specifically invited can see it.<br>',
                    '<span class="color-b5b5b5 font-work-sans-400"><span class="font-work-sans-500">NB:</span> Global Innovation Team and Group top management have access to all projects.</span>',
                    '</span>'
                );
            } else {
                var content = ''.concat(
                    '<div class="icon-private-toast"></div>',
                    '<span class="vertical-align-middle">',
                    '<span class="bold display-block" style="margin-bottom:5px;">',
                    'Your innovation is in Early Stage',
                    '</span>',
                    'Only members of your project team and people you specifically invited can see it.<br>',
                    '<span class="color-b5b5b5 font-work-sans-400"><span class="font-work-sans-500">NB:</span> Global Innovation Team and Group top management have access to all projects.</span>',
                    '</span>'
                );
            }
        }
        $('#overview-cta').cookieNotificator({
            id: 'overview-cta-toast',
            theme: 'error',
            type: 'html',
            cookieCheck: false,
            deleteButton: false,
            content: content,
            customStyle: {
                'margin': '0 auto 20px',
                'max-width': '1200px'
            }
        });
    }

    $('.content-tab-overview .libelle_current_le').html(get_libelle_current_le());
    $('.content-tab-overview .libelle_versus_date').html(get_libelle_last_a());
    var financial_date = get_financial_date();
    $('.content-tab-overview .libelle_current_fy').html(get_financial_date_FY(financial_date));
    if ((has_link_to_manage || check_if_user_is_management()) && !innovation_is_a_service && !innovation_is_early_stage) {
        explore_detail_oveview_init_financial_data(innovation['id']);
    }

    $('#select-canvas_first_title').select2({
        minimumResultsForSearch: -1,
        width: 'resolve',
        theme: "default select2-canvas",
    });

    $('#select-canvas_first_title').change(function() {
        var canvas_id = $(this).val();
        var innovation_id = $('#detail-id-innovation').val();
        var canvas = getCanvasById(innovation_id, canvas_id);
        $('#canvas1').html(explore_detail_overview_get_canvas_content(canvas, innovation_id));

    });


    $('#bouton').click(function() {
        $('html,body').animate({ scrollTop: $("#monancre").offset().top }, 'slow');
    });


    init_tooltip();
    init_explore_bars(80);
    launchBlueArrowScroll();
    load_background_images();
    explore_initVisualExplorePitchous(innovation['id'], ACTIVITY_ACTION_PROMOTE_INNOVATION_VIEW);
}


/**
 * init_explore_bars
 * @param max_width
 */
function init_explore_bars(max_width) {
    var $ = jQuery;
    var all_volumes = [];
    var max_volume = 0;
    $('.block-bar-volume').each(function() {
        var value = $(this).find('.value').html();
        if (value != 'N/A') {
            value = parseInt(value);
            all_volumes.push(value);
            if (value > max_volume) {
                max_volume = value;
            }
        }
    });
    $('.block-bar-volume').each(function() {
        var value = $(this).find('.value').html();
        var width = 0;
        if (value != 'N/A') {
            value = parseInt(value);
            if (value == 0) {
                width = 0;
            } else if (max_volume == value) {
                width = max_width;
            } else {
                var percent = get_percent_between_two_values(value, max_volume);
                width = percent * (max_width / 100);
            }
        } else {
            $(this).find('.bar').css('margin-right', '0');
        }
        $(this).find('.bar').css('width', width + '%');
    });
}