/**
 * explore_generate_edit_tab
 * @param id
 * @returns {string}
 */
function explore_generate_edit_tab(id) {
    var out = [],
        o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }
    var current_stage = (innovation['current_stage'] && innovation['current_stage'] != 'Empty' && innovation['current_stage'] != 'empty') ? innovation['current_stage'] : 'empty';
    var previous_stage = (innovation['previous_stage'] && innovation['previous_stage'] != 'Empty' && innovation['previous_stage'] != 'empty') ? innovation['previous_stage'] : 'empty';
    var value = "";
    var consumer_opportunity = innovation['consumer_opportunity'];
    var has_link_to_manage = (user_can_edit_this_innovation(innovation));
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    var portfolio_profile = innovation['growth_strategy'];
    if (!check_if_user_is_hq() && portfolio_profile != "Big Bet") {
        portfolio_profile = "Contributor";
    }
    var edition_mode_quali_enabled = (check_if_user_is_hq() || (user_can_edit_this_innovation(innovation)) && check_if_data_quali_capture_is_opened());
    var field_disabled_quali = ((edition_mode_quali_enabled) ? '' : 'disabled');
    var field_no_hq_disabled = ((check_if_user_is_hq()) ? '' : 'disabled');
    var innovation_is_nba = check_if_innovation_is_nba(innovation);

    var section_id = 0;
    var section_classe = (section_id % 2 == 0) ? 'background-color-f2f2f2' : 'background-color-fff';
    if (has_link_to_manage) {
        out[++o] = '<div class="background-color-fff padding-bottom-60 padding-top-30">'; // Start Background White
        out[++o] = '<div class="central-content-994">'; // Start central-content-994

        out[++o] = '<div class="font-montserrat-700 color-000 font-size-44 line-height-1 text-align-center margin-bottom-10">';
        out[++o] = '<span class="display-inline-block vertical-align-middle">' + innovation['title'] + '</span>';
        out[++o] = '<span class="explore-detail-icon-update-title-entity-brand with-tooltip action-open-popup" data-popup-target=".element-update-innovation-popup" title="Update title"></span>';
        out[++o] = '</div>';


        out[++o] = '<div class="font-montserrat-700 color-b5b5b5 font-size-16 line-height-1 text-align-center margin-bottom-40">';
        out[++o] = '<span class="display-inline-block vertical-align-middle">' + get_innovation_entity_brand_libelle(innovation) + '</span>';
        out[++o] = '<span class="explore-detail-icon-update-title-entity-brand small with-tooltip action-open-popup" data-popup-target=".element-update-innovation-popup" title="Update entity and brand"></span>';
        out[++o] = '</div>';

        out[++o] = '</div>'; // End central-content-994
        var open_question = innovation['open_question'];
        if ((innovation_is_nba && check_if_user_is_hq()) || (innovation_is_nba && open_question && open_question['message'])) {
            out[++o] = '<div class="background-color-f2f2f2 padding-top-40 padding-bottom-50 margin-bottom-50">'; // Start background-color-f2f2f2
            out[++o] = '<div class="central-content-994">'; // Start central-content-994
            out[++o] = '<div class="font-montserrat-700 color-000 font-size-30">Open questions from the Innovation Team</div>';
            if ((!open_question || (open_question && !open_question['message'])) && check_if_user_is_hq()) {
                out[++o] = '<div class="empty-open-question text-align-center">';
                out[++o] = '<div class="padding-top-60">';
                out[++o] = '<div class="font-montserrat-700 font-size-20 color-b5b5b5 line-height-1-5">No open questions yet…</div>';
                out[++o] = '<div class="font-work-sans-300 font-size-16 color-b5b5b5 line-height-1-5">Add your open questions to help the team!</div>';
                out[++o] = '<button class="hub-button action-open-popup margin-top-20" data-popup-target=".element-popup-open-question">Add open questions</button>';
                out[++o] = '</div>';
                out[++o] = '</div>';
            } else {
                var text_open_question = open_question['message'];
                var open_question_contact = open_question['contact'];
                var date_open_question = gmdate('M dd, Y', open_question['updated_at']);
                if (check_if_user_is_hq()) {
                    out[++o] = '<div class="action-open-popup explore-detail-icon-update-open-question with-tooltip" data-popup-target=".element-popup-open-question" title="Update open questions"></div>';
                }
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
            }
            out[++o] = '</div>'; // End central-content-994
            out[++o] = '</div>'; // End background-color-f2f2f2
        }
        out[++o] = '<div class="central-content-994">'; // Start central-content-994
        out[++o] = '<div class="title-form-section">Tags</div>';
        out[++o] = '<div class="form-section-presentation">Add or change tags so readers know what your story is about:</div>';

        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<div class="content-select">';
        out[++o] = '<select class="auto-save select-multiple field" ' + field_disabled_quali + ' name="tag" multiple="multiple">';
        var options = (innovation['tags']) ? innovation['tags'] : [];
        for (var i = 0; i < options.length; i++) {
            out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
        }
        out[++o] = '</select>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element

        out[++o] = '<div class="overflow-hidden padding-top-20">'; // Start Content columns
        out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column left

        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>Innovation Classification</label>';
        out[++o] = '<div class="content-select with-icon">';
        out[++o] = '<div class="icon"></div>';
        var field_is_disabled = (field_disabled_quali) ? field_disabled_quali : "";
        if (innovation_is_nba) {
            field_is_disabled = 'disabled';
        }
        out[++o] = '<select class="select field required" ' + field_is_disabled + ' data-value="' + innovation['classification_type'] + '" name="classification">';
        var options = get_innovation_classifications();
        for (var i = 0; i < options.length; i++) {
            out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
        }
        out[++o] = '</select>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element

        if (innovation_is_nba || !innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Innovation Type</label>';
            out[++o] = '<div class="content-select with-icon">';
            out[++o] = '<div class="icon"></div>';
            var field_is_disabled = (field_disabled_quali) ? field_disabled_quali : "";
            if (innovation_is_nba) {
                field_is_disabled = 'disabled';
            }
            out[++o] = '<select class="auto-save select field required" ' + field_is_disabled + ' data-value="' + innovation['innovation_type'] + '" name="innovation_type">';
            var options = get_innovation_types(innovation_is_a_service);
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (!innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Consumer Opportunity</label>';
            out[++o] = '<div class="content-select with-icon round">';
            out[++o] = '<div class="icon"></div>';
            out[++o] = '<select class="auto-save select field required" ' + field_disabled_quali + ' data-value="' + innovation['consumer_opportunity'] + '" name="consumer_opportunity">';
            var options = get_consumer_opportunities();
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '<button class="margin-top-20 hub-button yellow action-open-popup" data-popup-target=".element-consumer-opportunity-popup">Help me choose a Consumer Opportunity</button>';
            out[++o] = '</div>'; // End form-element
        }


        out[++o] = '</div>'; // End column left
        out[++o] = '<div class="column-percent-50 padding-left-20">'; // Start column right

        if (!innovation_is_nba && innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Innovation Type</label>';
            out[++o] = '<div class="content-select with-icon">';
            out[++o] = '<div class="icon"></div>';
            out[++o] = '<select class="auto-save select field required" ' + field_disabled_quali + ' data-value="' + innovation['innovation_type'] + '" name="innovation_type">';
            var options = get_innovation_types(innovation_is_a_service);
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (!innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Category</label>';
            out[++o] = '<div class="content-select with-icon no-icon-change">';
            out[++o] = '<div class="icon light category"></div>';
            out[++o] = '<select class="auto-save select field required" ' + field_disabled_quali + ' data-value="' + innovation['category'] + '" name="category">';
            var options = get_innovation_categories();
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (!innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Business Priorities</label>';
            out[++o] = '<div class="content-select with-icon round">';
            out[++o] = '<div class="icon light "></div>';
            out[++o] = '<select class="auto-save select field required" ' + field_disabled_quali + ' data-value="' + innovation['business_drivers'] + '" name="business_drivers">';
            var options = get_business_drivers(innovation_is_a_service);
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }


        if (innovation_is_nba) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>New Business Opportunity</label>';
            out[++o] = '<div class="content-select with-icon no-icon-change">';
            out[++o] = '<div class="icon nbm"></div>';
            out[++o] = '<select class="auto-save select field required" ' + field_disabled_quali + ' data-value="' + innovation['new_business_opportunity'] + '" name="new_business_opportunity">';
            var options = get_innovation_new_business_opportunity(innovation_is_a_service);
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (innovation_is_nba || (innovation_is_a_service && check_if_user_is_hq())) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Innovation Portfolio Profile<div class="helper-button" data-target="only_editable_hq">?</div></label>';
            out[++o] = '<div class="content-select with-icon round">';
            out[++o] = '<div class="icon light"></div>';
            out[++o] = '<select class="auto-save select field" ' + field_no_hq_disabled + ' data-value="' + portfolio_profile + '" name="growth_strategy">';
            var options = get_innovation_growth_strategy(innovation_is_a_service);
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (innovation_is_nba) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Investment model</label>';
            out[++o] = '<div class="content-select">';
            out[++o] = '<select class="auto-save select field required" ' + field_disabled_quali + ' data-value="' + innovation['investment_model'] + '" name="investment_model">';
            var options = get_innovation_investment_model(innovation_is_a_service);
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }


        if (!innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Innovation Portfolio Profile<div class="helper-button" data-target="only_editable_hq">?</div></label>';
            out[++o] = '<div class="content-select with-icon round">';
            out[++o] = '<div class="icon light"></div>';
            out[++o] = '<select class="auto-save select field" ' + field_no_hq_disabled + ' data-value="' + portfolio_profile + '" name="growth_strategy">';
            var options = get_innovation_growth_strategy();
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate'])) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>ABV</label>';
            out[++o] = '<div class="content-input-text  with-icon round">';
            out[++o] = '<div class="icon light abv"></div>';
            out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="abv" data-value="' + innovation["abv"] + '" />';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (!innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Global Moment of Convivialité</label>';
            out[++o] = '<div class="content-select with-icon no-icon-change">';
            out[++o] = '<div class="icon light moc"></div>';
            out[++o] = '<select class="auto-save select field required" ' + field_disabled_quali + ' data-value="' + innovation['moc'] + '" name="moc">';
            var options = get_moments_of_convivialite();
            for (var i = 0; i < options.length; i++) {
                out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + '</option>';
            }
            out[++o] = '</select>';
            out[++o] = '</div>';
            out[++o] = '<a class="margin-top-20 hub-button yellow" target="_blank" href="/documents/Moment Of Convivialité MAP.PDF">Help me choose a global MOC</a>';
            out[++o] = '</div>'; // End form-element
        }


        out[++o] = '</div>'; // End column right
        out[++o] = '</div>'; // End Content columns

        out[++o] = '<div class="margin-top-20 overflow-hidden">'; // Start content column
        out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column left

        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>Project started on</label>';
        out[++o] = '<div class="content-input-text date">';
        out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field date required" name="start_date" data-value="' + innovation["start_date"] + '" />';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element

        if (!innovation_is_a_service) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Does your innovation replace an existing ' + global_wording['company'] + ' SKU?</label>';
            out[++o] = '<div class="content-input-radio">';
            out[++o] = '<input type="radio" value="0" name="replace_existing_product" class="replace_existing_product inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
            out[++o] = ' id="replace_existing_product_no" ' + ((innovation['replace_existing_product'] == '0') ? "checked" : "") + '>';
            out[++o] = '<label class="fake-checkbox" for="replace_existing_product_no"></label>';
            out[++o] = '<label class="fake-label" for="replace_existing_product_no">No</label>';
            out[++o] = '<input type="radio" value="1" name="replace_existing_product" class="replace_existing_product inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
            out[++o] = ' id="replace_existing_product_yes" ' + ((innovation['replace_existing_product'] == '1') ? "checked" : "") + '>';
            out[++o] = '<label class="fake-checkbox" for="replace_existing_product_yes"></label>';
            out[++o] = '<label class="fake-label" for="replace_existing_product_yes"> Yes</label>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            var the_class = (innovation['replace_existing_product'] == '1') ? "" : "display-none";
            out[++o] = '<div class="detail-content-existing_product form-element ' + the_class + '">'; // Start form-element
            out[++o] = '<label>Which one?</label>';
            out[++o] = '<div class="content-input-text">';
            var value_placeholder = '';
            out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional"  name="existing_product" data-value="' + innovation["existing_product"] + '" placeholder="' + value_placeholder + '" />';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }


        if (!innovation_is_nba) {
            // CONTACT
            out[++o] = '<div class="content-overview-header-contact margin-bottom-20 padding-top-10">'; // Start content-overview-header-contact
            out[++o] = '<div class="contact-picture loading-bg" data-bg="' + innovation['contact']['picture'] + '"></div>';
            out[++o] = '<div class="contact-infos">'; // Start contact-infos
            out[++o] = '<div class="font-montserrat-500 color-b5b5b5 font-size-13 line-height-1 margin-bottom-3">Contact</div>';
            out[++o] = '<div class="position-relative height-20">'; // Start position-relative
            out[++o] = '<div class="color-000 font-montserrat-700 font-size-16 display-inline-block">' + innovation['contact']['username'] + '</div>';
            out[++o] = '<span class="explore-detail-icon-update-title-entity-brand small with-tooltip action-open-popup" data-popup-target=".element-update-innovation-contact-popup" title="Update contact"></span>';
            out[++o] = '</div>'; // End position-relative
            out[++o] = '</div>'; // End contact-infos
            out[++o] = '</div>'; // End content-overview-header-contact
        }
        if (innovation_is_nba) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Does it have a separate P&L?</label>';
            out[++o] = '<div class="content-input-radio">';
            out[++o] = '<input type="radio" value="0" name="as_seperate_pl" class="as_seperate_pl inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
            out[++o] = ' id="as_seperate_pl_no" ' + ((innovation['as_seperate_pl'] == '0') ? "checked" : "") + '>';
            out[++o] = '<label class="fake-checkbox" for="as_seperate_pl_no"></label>';
            out[++o] = '<label class="fake-label" for="as_seperate_pl_no">No</label>';
            out[++o] = '<input type="radio" value="1" name="as_seperate_pl" class="as_seperate_pl inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
            out[++o] = ' id="as_seperate_pl_yes" ' + ((innovation['as_seperate_pl'] == '1') ? "checked" : "") + '>';
            out[++o] = '<label class="fake-checkbox" for="as_seperate_pl_yes"></label>';
            out[++o] = '<label class="fake-label" for="as_seperate_pl_yes"> Yes</label>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            // CONTACT
            out[++o] = '<div class="margin-top-50">';
            out[++o] = '<div class="content-overview-header-contact margin-bottom-20 padding-top-10">'; // Start content-overview-header-contact
            out[++o] = '<div class="contact-picture loading-bg" data-bg="' + innovation['contact']['picture'] + '"></div>';
            out[++o] = '<div class="contact-infos">'; // Start contact-infos
            out[++o] = '<div class="font-montserrat-500 color-b5b5b5 font-size-13 line-height-1 margin-bottom-3">Contact</div>';
            out[++o] = '<div class="position-relative height-20">'; // Start position-relative
            out[++o] = '<div class="color-000 font-montserrat-700 font-size-16 display-inline-block">' + innovation['contact']['username'] + '</div>';
            out[++o] = '<span class="explore-detail-icon-update-title-entity-brand small with-tooltip action-open-popup" data-popup-target=".element-update-innovation-contact-popup" title="Update contact"></span>';
            out[++o] = '</div>'; // End position-relative
            out[++o] = '</div>'; // End contact-infos
            out[++o] = '</div>'; // End content-overview-header-contact
            out[++o] = '</div>';

        }


        out[++o] = '</div>'; // End column left

        out[++o] = '<div class="column-percent-50 padding-left-20">'; // Start column right

        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>Market introduction date';
        out[++o] = '<span class="helper-button" data-target="market_introduction_date">?</span>';
        out[++o] = '</label>';
        out[++o] = '<div class="content-input-text date">';
        out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required date" name="market_date" data-value="' + innovation["in_market_date"] + '" />';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element

        if (check_if_user_is_hq()) {
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Is your innovation in Prisma?<div class="helper-button" data-target="only_hq">?</div></label>';
            out[++o] = '<div class="content-input-radio">';
            out[++o] = '<input type="radio" value="0" name="in_prisma" class="in_prisma inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
            out[++o] = ' id="in_prisma_no" ' + ((innovation['in_prisma'] == '0') ? "checked" : "") + '>';
            out[++o] = '<label class="fake-checkbox" for="in_prisma_no"></label>';
            out[++o] = '<label class="fake-label" for="in_prisma_no">No</label>';
            out[++o] = '<input type="radio" value="1" name="in_prisma" class="in_prisma inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
            out[++o] = ' id="in_prisma_yes" ' + ((innovation['in_prisma'] == '1') ? "checked" : "") + '>';
            out[++o] = '<label class="fake-checkbox" for="in_prisma_yes"></label>';
            out[++o] = '<label class="fake-label" for="in_prisma_yes"> Yes</label>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
        }

        if (innovation_is_nba) {
            out[++o] = '<div class="margin-top-50">';
            //website
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Website</label>';
            out[++o] = '<div class="content-input-text with-icon">';
            out[++o] = '<div class="icon link-website"></div>';
            var value_placeholder = 'http://www.linktowebsite.com';
            out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional" name="website_url" data-value="' + innovation["website_url"] + '" placeholder="' + value_placeholder + '" />';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
            out[++o] = '</div>';
        }

        out[++o] = '</div>'; // End column right
        out[++o] = '</div>'; // End content column

        var classe_div = (innovation_is_nba) ? 'margin-top-80' : 'margin-top-40';
        out[++o] = '<div class="' + classe_div + ' overflow-hidden">'; // Start content column
        out[++o] = '<div class="section-title font-size-26 margin-bottom-0">Lifecycle</div>';
        out[++o] = '<div class="position-absolute-top-right">';
        out[++o] = '<button class="hub-button yellow action-open-popup" data-popup-target=".element-change-stage-popup">Change Stage</button>';
        out[++o] = '</div>';

        out[++o] = '<div class="content-lifecycle">'; // Start content-lifecycle
        var frozen_classe = (innovation['is_frozen']) ? ' frozen' : '';
        var classe_active = (innovation['current_stage'] == 'discover') ? 'active' + frozen_classe : '';
        var classe_helper = (innovation['current_stage'] == 'discover') ? 'helper-on-hover-top ' : '';
        out[++o] = '<div class="content-stage ' + classe_helper + classe_active + '" data-target="' + ((innovation['is_frozen']) ? 'frozen' : 'discover') + '">';
        out[++o] = '<div class="icon discover-unselected ' + classe_active + '"></div>';
        out[++o] = '<div class="libelle font-montserrat-700">DISCOVER</div>';
        out[++o] = '</div>';

        out[++o] = '<div class="separator"></div>';

        classe_active = (innovation['current_stage'] == 'ideate') ? 'active' + frozen_classe : '';
        classe_helper = (innovation['current_stage'] == 'ideate') ? 'helper-on-hover-top ' : '';
        out[++o] = '<div class="content-stage ' + classe_helper + classe_active + '" data-target="' + ((innovation['is_frozen']) ? 'frozen' : 'ideate') + '">';
        out[++o] = '<div class="icon ideate-unselected ' + classe_active + '"></div>';
        out[++o] = '<div class="libelle font-montserrat-700">IDEATE</div>';
        out[++o] = '</div>';

        out[++o] = '<div class="separator"></div>';

        classe_active = (innovation['current_stage'] == 'experiment') ? 'active' + frozen_classe : '';
        classe_helper = (innovation['current_stage'] == 'experiment') ? 'helper-on-hover-top ' : '';
        out[++o] = '<div class="content-stage ' + classe_helper + classe_active + '" data-target="' + ((innovation['is_frozen']) ? 'frozen' : 'experiment') + '">';
        out[++o] = '<div class="icon experiment-unselected ' + classe_active + '"></div>';
        out[++o] = '<div class="libelle font-montserrat-700">EXPERIMENT</div>';
        out[++o] = '</div>';

        out[++o] = '<div class="separator"></div>';

        if (!in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment', 'discontinued'])) {
            var market_date_value = ((innovation['in_market_date']) ? gmdate("M Y", innovation['in_market_date']) : 'Missing data');
            var added_classe = (innovation['in_market_date']) ? '' : 'style_missing_data';
            out[++o] = '<div class="content-in-market-in">';
            out[++o] = '<div class="font-work-sans-400 font-size-12 line-height-1-2 text-align-center color-9b9b9b">In market since</div>';
            out[++o] = '<div class="lifecycle-text-in-market-date font-montserrat-700 font-size-14 text-transform-uppercase line-height-1 text-align-center color-9b9b9b ' + added_classe + '">' + market_date_value + '</div>';
            out[++o] = '<div class="line-divider"></div>';
            out[++o] = '<div class="point"></div>';
            out[++o] = '</div>';
        }

        classe_active = (innovation['current_stage'] == 'incubate') ? 'active' + frozen_classe : '';
        classe_helper = (innovation['current_stage'] == 'incubate') ? 'helper-on-hover-top ' : '';
        out[++o] = '<div class="content-stage ' + classe_helper + classe_active + '" data-target="' + ((innovation['is_frozen']) ? 'frozen' : 'incubate') + '">';
        out[++o] = '<div class="icon incubate-unselected ' + classe_active + '"></div>';
        out[++o] = '<div class="libelle font-montserrat-700">INCUBATE</div>';
        out[++o] = '</div>';

        out[++o] = '<div class="separator"></div>';

        classe_active = (innovation['current_stage'] == 'scale_up') ? 'active' + frozen_classe : '';
        classe_helper = (innovation['current_stage'] == 'scale_up') ? 'helper-on-hover-top ' : '';
        out[++o] = '<div class="content-stage ' + classe_helper + classe_active + '"  data-target="' + ((innovation['is_frozen']) ? 'frozen' : 'scale_up') + '">';
        out[++o] = '<div class="icon scale_up-unselected ' + classe_active + '"></div>';
        out[++o] = '<div class="libelle font-montserrat-700">SCALE UP</div>';
        out[++o] = '</div>';

        out[++o] = '<div class="separator"></div>';

        if (innovation['current_stage'] == 'discontinued') {
            out[++o] = '<div class="content-stage active helper-on-hover-top" data-target="discontinued">';
            out[++o] = '<div class="icon discontinued-unselected active"></div>';
            out[++o] = '<div class="libelle font-montserrat-700">DISCONTINUED</div>';
            out[++o] = '</div>';
        } else {
            classe_active = (innovation['current_stage'] == 'permanent_range') ? 'active' : '';
            classe_helper = (innovation['current_stage'] == 'permanent_range') ? 'helper-on-hover-top ' : '';
            out[++o] = '<div class="content-stage ' + classe_helper + classe_active + '" data-target="permanent_range">';
            out[++o] = '<div class="icon permanent_range-unselected ' + classe_active + '"></div>';
            out[++o] = '<div class="libelle font-montserrat-700">PERMANENT<br>RANGE</div>';
            out[++o] = '</div>';
        }

        out[++o] = '</div>'; // End content-lifecycle


        out[++o] = '</div>'; // End content column

        out[++o] = '</div>'; // End central-content-994
        out[++o] = '</div>'; // End Background White

        if (!innovation_is_nba) {
            // ELEVATOR PITCH
            out[++o] = '<div class="background-color-f2f2f2 padding-vertical-40">'; // Start Background f2f2f2
            out[++o] = '<div class="central-content-994">'; // Start central-content-994
            out[++o] = '<div class="section-title">Elevator Pitch</div>';

            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label class="font-15">Why did we design this innovation?</label>';
            out[++o] = '<div class="under-label">Explain what you aim at with this innovation, and for whom/where it is designed for, to help markets understand why they should take it or not. </div>';
            out[++o] = '<div class="content-textarea">';
            out[++o] = '<textarea class="text-limited field required" name="why_invest_in_this_innovation" data-limit-text="450" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['why_invest_in_this_innovation'] + '</textarea>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            out[++o] = '<div class="title-form-section-with-icon">';
            out[++o] = '<div class="icon value-proposition"></div>';
            out[++o] = '<span>Value proposition</span>';
            out[++o] = '</div>';
            if (!innovation_is_a_service) {
                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label class="font-15">Story</label>';
                out[++o] = '<div class="under-label">Take us behind the scenes: what\'s the magic behind this innovation?</div>';
                out[++o] = '<div class="content-textarea">';
                out[++o] = '<textarea class="text-limited field required" name="story" data-limit-text="550" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['story'] + '</textarea>';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

            } else {

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label class="font-15">Unique experience</label>';
                out[++o] = '<div class="content-textarea">';
                out[++o] = '<textarea class="text-limited field required" name="unique_experience" data-limit-text="550" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['unique_experience'] + '</textarea>';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element
            }

            if (!innovation_is_a_service) {
                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label class="font-15">Uniqueness</label>';
                out[++o] = '<div class="under-label">In one catch phrase, WHY is unique and different from what is already in market - from a consumer point of view?</div>';
                out[++o] = '<div class="content-textarea">';
                out[++o] = '<textarea class="text-limited small field required" name="value_proposition" data-limit-text="250" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['value_proposition'] + '</textarea>';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element
            }
            out[++o] = '<div class="title-form-section-with-icon">';
            out[++o] = '<div class="icon consumer"></div>';
            out[++o] = '<span>Consumer</span>';
            out[++o] = '</div>';

            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label class="font-15">Consumer benefit</label>';
            out[++o] = '<div class="under-label">In a simple sentence, what consumer desire or need or problem does your innovation answer?</div>';
            out[++o] = '<div class="content-textarea">';
            out[++o] = '<textarea class="text-limited small field required" name="consumer_insight" data-limit-text="200" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['consumer_insight'] + '</textarea>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate'])) {
                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label class="font-15">Early adopters persona</label>';
                out[++o] = '<div class="under-label">Describe your early adopter with simple terms (no marketing jargon), and beyond our brands consumption moments: who are they, what do they do, what do they believe in, what do they desire, what are they struggling with…?</div>';
                out[++o] = '<div class="content-textarea">';
                out[++o] = '<textarea class="text-limited small field required" name="early_adopter_persona" data-limit-text="150" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['early_adopter_persona'] + '</textarea>';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element
            }

            out[++o] = '<div class="title-form-section-with-icon">';
            out[++o] = '<div class="icon business-priority"></div>';
            out[++o] = '<span>Source of Business</span>';
            out[++o] = '</div>';

            out[++o] = '<div class="form-element margin-bottom-0">'; // Start form-element
            out[++o] = '<label class="font-15">Size of the prize</label>';
            out[++o] = '<div class="under-label">How big is the pie your innovation taps into, and how much do you plan to take from it? Use market trends, business figures and facts to explain where you will make business from.</div>';
            out[++o] = '<div class="content-textarea">';
            out[++o] = '<textarea class="text-limited small field required" name="source_of_business" data-limit-text="250" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['source_of_business'] + '</textarea>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            out[++o] = '</div>'; // End central-content-994
            out[++o] = '</div>'; // End Background f2f2f2

            // KEY INFORMATIONS
            if (!innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate'])) {
                section_id += 1;
                section_classe = (section_id % 2 == 0) ? 'background-color-f2f2f2' : 'background-color-fff';
                out[++o] = '<div class="' + section_classe + ' padding-top-40 padding-bottom-40">'; // Start background-color-fff
                out[++o] = '<div class="central-content-994">'; // Start central-content-994
                out[++o] = '<div class="title-form-section-with-icon margin-top-0">';
                out[++o] = '<div class="icon size-35 light ' + current_stage + '"></div>';
                out[++o] = '<span class="font-size-26 libelle">';
                out[++o] = 'Key information';
                out[++o] = '<span class="under-libelle">What pieces of evidence show that your innovation creates value and has momentum for customers / consumers?</span>';
                out[++o] = '</span>';
                out[++o] = '</div>';

                out[++o] = '<div class="overflow-hidden padding-top-10">'; // Start content column

                out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column left
                out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 line-height-1-5 margin-bottom-20">Key info</div>';

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>Perfect Serve (eg. Spritz for Aperol):</label>';
                out[++o] = '<div class="content-input-text">';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="universal_key_information_1" data-value="' + innovation["universal_key_information_1"] + '" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>RTM (eg. high energy bars):</label>';
                out[++o] = '<div class="content-input-text">';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="universal_key_information_2" data-value="' + innovation["universal_key_information_2"] + '" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

                out[++o] = '<div class="margin-top-20 overflow-hidden">'; // Start content column
                out[++o] = '<div class="column-percent-33 padding-right-5">'; // Start column left
                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>Price index versus…</label>';
                out[++o] = '<div class="content-input-text">';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="universal_key_information_4" data-value="' + innovation["universal_key_information_4"] + '" placeholder="eg 100" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element
                out[++o] = '</div>'; // End column left
                out[++o] = '<div class="column-percent-67 padding-left-5">'; // Start column right
                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>Main competitor</label>';
                out[++o] = '<div class="content-input-text">';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="universal_key_information_4_vs" data-value="' + innovation["universal_key_information_4_vs"] + '"  placeholder="eg Chivas 12" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element
                out[++o] = '</div>'; // End column right
                out[++o] = '</div>'; // End content column


                if (!innovation['new_to_the_world']) {
                    out[++o] = '<div class="overflow-hidden">'; // Start content column
                    out[++o] = '<div class="column-percent-33 padding-right-5">'; // Start column left
                    out[++o] = '<div class="form-element">'; // Start form-element
                    out[++o] = '<label>Price index versus…</label>';
                    out[++o] = '<div class="content-input-text">';
                    out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="universal_key_information_3" data-value="' + innovation["universal_key_information_3"] + '"  placeholder="eg 100" />';
                    out[++o] = '</div>';
                    out[++o] = '</div>'; // End form-element
                    out[++o] = '</div>'; // End column left
                    out[++o] = '<div class="column-percent-67 padding-left-5">'; // Start column right
                    out[++o] = '<div class="form-element">'; // Start form-element
                    out[++o] = '<label>Main reference motherbrand</label>';
                    out[++o] = '<div class="content-input-text">';
                    out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="universal_key_information_3_vs" data-value="' + innovation["universal_key_information_3_vs"] + '"  placeholder="eg Johnnie Walker Double Black" />';
                    out[++o] = '</div>';
                    out[++o] = '</div>'; // End form-element
                    out[++o] = '</div>'; // End column right
                    out[++o] = '</div>'; // End content column
                }

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>Other (eg. taste, origin, range):</label>';
                out[++o] = '<div class="content-input-text">';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field required" name="universal_key_information_5" data-value="' + innovation["universal_key_information_5"] + '" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

                out[++o] = '</div>'; // End column left

                out[++o] = '<div class="column-percent-50 padding-left-20">'; // Start column right
                out[++o] = '<div class="font-montserrat-700 font-size-20 color-000 line-height-1-5 margin-bottom-7">Visual evidence</div>';
                out[++o] = '<div class="font-work-sans-400 font-size-14 color-787878 margin-bottom-20">What visual evidences do you have to show your innovation has a momentum? (activation pictures, social media screenshots…)</div>';

                var class_edition_mode = (edition_mode_quali_enabled) ? '' : 'cursor-default';
                var classe_pot_picture_1 = (innovation['pot_picture_1']) ? 'pictured' : '';
                var src_pot_picture_1 = (innovation['pot_picture_1']) ? 'background-image:url(' + innovation['pot_picture_1']['thumbnail'] + ')' : '';
                var classe_pot_picture_2 = (innovation['pot_picture_2']) ? 'pictured' : '';
                var src_pot_picture_2 = (innovation['pot_picture_2']) ? 'background-image:url(' + innovation['pot_picture_2']['thumbnail'] + ')' : '';

                out[++o] = '<div class="block-add-picture pot pot_picture_1 ' + classe_pot_picture_1 + '" data-width="300" ';
                out[++o] = 'data-height="200" data-title="Select drinking ritual picture" ';
                out[++o] = 'id="edition_proofs_of_traction_picture_1"> '; // Start block-add-picture
                out[++o] = '<div class="add-picture ' + class_edition_mode + '"> '; // Start add-picture
                out[++o] = '<span>Select file to upload<br>or drag and drop your picture file</span>';
                if (edition_mode_quali_enabled) {
                    out[++o] = '<input name="files[]" type="file" id="proofs_of_traction_picture_1" ';
                    out[++o] = 'class="hidden-upload-input">';
                }
                out[++o] = '<div class="progress-upload">'; // Start progress-upload
                out[++o] = '<div class="progress-bar"></div>';
                out[++o] = '</div>'; // End progress-upload
                out[++o] = '</div>'; // End add-picture
                out[++o] = '<div class="picture-added">'; // Start picture-added
                out[++o] = '<div class="fake-img" style="' + src_pot_picture_1 + '"></div>';
                if (edition_mode_quali_enabled) {
                    out[++o] = '<div class="button-delete-picture hub-button height-26" data-target="pot_picture_1">Remove</div>';
                }
                out[++o] = '</div>'; // End picture-added
                out[++o] = '<div class="error-upload"></div>';
                out[++o] = '</div>'; // End block-add-picture

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>Caption:</label>';
                out[++o] = '<div class="content-input-text">';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field text-limited" data-limit-text="25" data-has-break-line="0" placeholder="Legend (max 25 char.)" name="proofs_of_traction_picture_1_legend" data-value="' + innovation["proofs_of_traction_picture_1_legend"] + '" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

                out[++o] = '<div class="block-add-picture pot pot_picture_2 ' + classe_pot_picture_2 + '" data-width="300" ';
                out[++o] = 'data-height="200" data-title="Select key competitor picture" ';
                out[++o] = 'id="edition_proofs_of_traction_picture_2"> '; // Start block-add-picture
                out[++o] = '<div class="add-picture ' + class_edition_mode + '"> '; // Start add-picture
                out[++o] = '<span>Select file to upload<br>or drag and drop your picture file</span>';
                if (edition_mode_quali_enabled) {
                    out[++o] = '<input name="files[]" type="file" id="proofs_of_traction_picture_2" ';
                    out[++o] = 'class="hidden-upload-input">';
                }
                out[++o] = '<div class="progress-upload">'; // Start progress-upload
                out[++o] = '<div class="progress-bar"></div>';
                out[++o] = '</div>'; // End progress-upload
                out[++o] = '</div>'; // End add-picture
                out[++o] = '<div class="picture-added">'; // Start picture-added
                out[++o] = '<div class="fake-img" style="' + src_pot_picture_2 + '"></div>';
                if (edition_mode_quali_enabled) {
                    out[++o] = '<div class="button-delete-picture hub-button height-26" data-target="pot_picture_2">Remove</div>';
                }
                out[++o] = '</div>'; // End picture-added
                out[++o] = '<div class="error-upload"></div>';
                out[++o] = '</div>'; // End block-add-picture

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>Caption:</label>';
                out[++o] = '<div class="content-input-text">';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field text-limited" data-limit-text="25" data-has-break-line="0" placeholder="Legend (max 25 char.)" name="proofs_of_traction_picture_2_legend" data-value="' + innovation["proofs_of_traction_picture_2_legend"] + '" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

                out[++o] = '</div>'; // End column right
                out[++o] = '</div>'; // End content column

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label class="font-15 color-4a4a4a">Key learnings so far</label>';
                out[++o] = '<div class="under-label">What have you learnt in the past 3 months that helps you move forward / will help you gain impact with your innovation?</div>';
                out[++o] = '<div class="content-textarea">';
                out[++o] = '<textarea class="text-limited small field required" name="universal_key_learning_so_far" data-limit-text="220" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['universal_key_learning_so_far'] + '</textarea>';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label class="font-15 color-4a4a4a">Next steps</label>';
                out[++o] = '<div class="under-label">What is coming next for your innovation?</div>';
                out[++o] = '<div class="content-textarea">';
                out[++o] = '<textarea class="text-limited small field required" name="universal_next_steps" data-limit-text="220" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['universal_next_steps'] + '</textarea>';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element

                out[++o] = '</div>'; // End central-content-994
                out[++o] = '</div>'; // End background-color-fff
            }
        }


        // CONTEXT
        if (innovation_is_nba) {
            section_classe = 'background-color-fff';
            out[++o] = '<div class="' + section_classe + ' padding-top-40 padding-bottom-60">'; // Start background-color-fff
            out[++o] = '<div class="central-content-994">'; // Start central-content-994
            out[++o] = '<div class="section-title">Context</div>';

            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label class="font-15">Idea description</label>';
            out[++o] = '<div class="under-label">Describe your new business idea. What solutions do you bring to consumers? How does it work?</div>';
            out[++o] = '<div class="content-textarea">';
            out[++o] = '<textarea class="text-limited field required" name="idea_description" data-limit-text="450" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['idea_description'] + '</textarea>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            out[++o] = '<div class="form-element margin-bottom-0">'; // Start form-element
            out[++o] = '<label class="font-15">Strategic intent / Mission</label>';
            out[++o] = '<div class="under-label">Why Pernod Ricard would have a competitive advantage developing this new Business?<br>Any positive business impact on the core business identified so far?</div>';
            out[++o] = '<div class="content-textarea">';
            out[++o] = '<textarea class="text-limited field required" name="strategic_intent_mission" data-limit-text="450" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['strategic_intent_mission'] + '</textarea>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            out[++o] = '</div>'; // End central-content-994
            out[++o] = '</div>'; // End background-color-fff
        }


        // END CONTEXT


        // ASSETS
        section_id += 1;
        section_classe = ((section_id % 2 == 0) || innovation_is_nba) ? 'background-color-f2f2f2' : 'background-color-fff';
        out[++o] = '<div class="' + section_classe + ' padding-top-60 padding-bottom-60">'; // Start background-color-fff
        out[++o] = '<div class="central-content-994">'; // Start central-content-994
        out[++o] = '<div class="section-title">Assets</div>';
        out[++o] = '<div class="font-work-sans-400 color-000 font-size-19 line-height-1-58 margin-bottom-40">Pictures and videos can speak louder than words or graphs. The more assets you add in this section, the more your innovation will stand out!</div>';
        out[++o] = '<div class="section-title font-size-26">Pictures</div>';
        var classe_picture_edition_mode = (edition_mode_quali_enabled) ? '' : 'cursor-default';
        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label class="font-15 color-4a4a4a">Beauty shot</label>';
        out[++o] = '<div class="under-label">Your innovation’s main pic, what represents it best. It will be used on all Innovation Hub outputs.';
        out[++o] = '<div class="font-work-sans-400 font-size-14 color-e8485c line-height-1"><span class="icon icon-warn"></span> <span class="display-inline-block vertical-align-middle">A landscape image will display your innovation better, as all outputs are in landscape format.</span></div>';
        out[++o] = '</div>';
        var classe_picture = (innovation['beautyshot_picture']) ? 'pictured' : 'error';
        var classe_picture_add = (innovation['beautyshot_picture']) ? '' : 'error';
        var src_picture = (innovation['beautyshot_picture']) ? 'src="' + innovation['beautyshot_picture']['thumbnail'] + '"' : '';

        out[++o] = '<div class="block-add-picture true-picture beautyshot_picture ' + classe_picture + '" data-width="945" ';
        out[++o] = 'data-height="500" data-title="Select Beauty shot" ';
        out[++o] = 'id="edition_beautyshot_picture"> '; // Start block-add-picture
        out[++o] = '<div class="add-picture ' + classe_picture_add + ' ' + classe_picture_edition_mode + '"> '; // Start add-picture
        out[++o] = '<span>Select file to upload<br>or drag and drop your picture file</span>';
        if (edition_mode_quali_enabled) {
            out[++o] = '<input name="files[]" type="file" id="beautyshot_picture_upload" ';
            out[++o] = 'class="hidden-upload-input">';
        }
        out[++o] = '<div class="progress-upload">'; // Start progress-upload
        out[++o] = '<div class="progress-bar"></div>';
        out[++o] = '</div>'; // End progress-upload
        out[++o] = '</div>'; // End add-picture
        out[++o] = '<div class="picture-added">'; // Start picture-added
        out[++o] = '<img alt="" ' + src_picture + '/>';
        if (edition_mode_quali_enabled) {
            out[++o] = '<div class="button-delete-picture hub-button height-26" data-target="beautyshot_picture">Remove</div>';
        }
        out[++o] = '</div>'; // End picture-added
        out[++o] = '<div class="error-upload"></div>';
        out[++o] = '</div>'; // End block-add-picture
        out[++o] = '</div>'; // End form-element

        if (!innovation_is_a_service) {
            classe_picture = (innovation['packshot_picture']) ? 'pictured' : 'error';
            classe_picture_add = (innovation['packshot_picture']) ? '' : 'error';
            src_picture = (innovation['packshot_picture']) ? 'src="' + innovation['packshot_picture']['thumbnail'] + '"' : '';
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label class="font-15 color-4a4a4a">Packshot</label>';
            out[++o] = '<div class="under-label">Please provide PNG or jpg with a white background: this image will be used in exports where only the bottle can be shown<br>(i.e. entity performance review).</div>';
            out[++o] = '<div class="block-add-picture true-picture packshot_picture ' + classe_picture + '" data-width="600" ';
            out[++o] = 'data-height="500" data-title="Select Packshot" ';
            out[++o] = 'id="edition_packshot_picture"> '; // Start block-add-picture
            out[++o] = '<div class="add-picture ' + classe_picture_add + ' ' + classe_picture_edition_mode + '"> '; // Start add-picture
            out[++o] = '<span>Select file to upload<br>or drag and drop your picture file</span>';
            if (edition_mode_quali_enabled) {
                out[++o] = '<input name="files[]" type="file" id="packshot_picture_upload" ';
                out[++o] = 'class="hidden-upload-input">';
            }
            out[++o] = '<div class="progress-upload">'; // Start progress-upload
            out[++o] = '<div class="progress-bar"></div>';
            out[++o] = '</div>'; // End progress-upload
            out[++o] = '</div>'; // End add-picture
            out[++o] = '<div class="picture-added background-color-fff">'; // Start picture-added
            out[++o] = '<img alt="" ' + src_picture + '/>';
            if (edition_mode_quali_enabled) {
                out[++o] = '<div class="button-delete-picture hub-button height-26" data-target="packshot_picture">Remove</div>';
            }
            out[++o] = '</div>'; // End picture-added
            out[++o] = '<div class="error-upload"></div>';
            out[++o] = '</div>'; // End block-add-picture
            out[++o] = '</div>'; // End form-element
        }

        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label class="font-15 color-4a4a4a">Additional pictures</label>';
        out[++o] = '<div class="under-label">Minimum upload size is 680 x 680 px.</div>';
        out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
        if (innovation['other_pictures'] && innovation['other_pictures'].length > 0) {
            for (var i = 0; i < innovation['other_pictures'].length; i++) {
                var other_picture = innovation['other_pictures'][i];
                out[++o] = '<div class="block-add-picture true-picture other_picture pictured">';
                out[++o] = '<div class="picture-added">';
                out[++o] = '<img alt="" src="' + other_picture['thumbnail'] + '"/>';
                if (edition_mode_quali_enabled) {
                    out[++o] = '<div class="button-delete-picture hub-button height-26" data-target="additional_picture">Remove</div>';
                }
                out[++o] = '</div>';
                out[++o] = '</div>';
            }
        }
        out[++o] = '<div class="block-add-picture true-picture other_picture uploader" data-width="680" data-height="680" data-title="Select Additionnal picture" ';
        out[++o] = 'id="edition_other_picture"> '; // Start block-add-picture
        out[++o] = '<div class="add-picture ' + classe_picture_edition_mode + '"> '; // Start add-picture
        out[++o] = '<span>Select file to upload<br>or drag and drop your picture file</span>';
        if (edition_mode_quali_enabled) {
            out[++o] = '<input name="files[]" type="file" id="other_picture_upload" ';
            out[++o] = 'class="hidden-upload-input">';
        }
        out[++o] = '<div class="progress-upload">'; // Start progress-upload
        out[++o] = '<div class="progress-bar"></div>';
        out[++o] = '</div>'; // End progress-upload
        out[++o] = '</div>'; // End add-picture
        out[++o] = '<div class="picture-added background-color-fff">'; // Start picture-added
        out[++o] = '<img alt="" src="" />';
        if (edition_mode_quali_enabled) {
            out[++o] = '<div class="button-delete-picture hub-button height-26"  data-target="additional_picture">Remove</div>';
        }
        out[++o] = '</div>'; // End picture-added
        out[++o] = '<div class="error-upload"></div>';
        out[++o] = '</div>'; // End block-add-picture

        out[++o] = '</div>'; // End overflow-hidden
        out[++o] = '</div>'; // End form-element


        if (innovation_is_nba || (!in_array(current_stage, ['discover', 'ideate']) && !innovation_is_a_service)) {
            out[++o] = '<div class="overflow-hidden padding-top-10">'; // Start content column
            out[++o] = '<div class="section-title font-size-26">Video</div>';
            out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column left
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label class="color-b5b5b5">Link to video</label>';
            out[++o] = '<div class="content-input-text with-icon">';
            out[++o] = '<div class="icon link-video"></div>';
            out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional"  name="video_link" data-value="' + innovation["video_link"] + '" placeholder="http://www.linktovideohere.com"/>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
            out[++o] = '</div>'; // End column left

            out[++o] = '<div class="column-percent-50 padding-left-20">'; // Start column right
            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label class="color-b5b5b5">Password (if needed)</label>';
            out[++o] = '<div class="content-input-text">';
            out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional"  name="video_password" data-value="' + innovation["video_password"] + '"/>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element
            out[++o] = '</div>'; // End column right
            out[++o] = '</div>'; // End content column
        }

        out[++o] = '</div>'; // End central-content-994
        out[++o] = '</div>'; // End background-color-fff


        if (!innovation_is_nba) {
            // USEFUL LINKS
            section_id += 1;
            section_classe = (section_id % 2 == 0) ? 'background-color-f2f2f2' : 'background-color-fff';
            out[++o] = '<div class="' + section_classe + ' padding-top-60 padding-bottom-60">'; // Start background-color-fff
            out[++o] = '<div class="central-content-994">'; // Start central-content-994

            out[++o] = '<div class="overflow-hidden">'; // Start content column
            out[++o] = '<div class="column-percent-50 padding-right-20">'; // Start column left
            out[++o] = '<div class="section-title font-size-26">Useful Links</div>';

            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = (!innovation_is_a_service) ? '<label>Dedicated MyBrands content</label>' : '<label>Website URL</label>';
            out[++o] = '<div class="content-input-text with-icon">';
            out[++o] = (!innovation_is_a_service) ? '<div class="icon link-mybrand"></div>' : '<div class="icon link-website"></div>';
            var value_placeholder = (!innovation_is_a_service) ? 'http://www.linktomybrandshere.com' : 'http://www.yourwebsite.com';
            out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional" name="website_url" data-value="' + innovation["website_url"] + '" placeholder="' + value_placeholder + '" />';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            if (!innovation_is_a_service) {
                out[++o] = '<div class="form-element">'; // Start form-element
                out[++o] = '<label>Latest IBP toolkit</label>';
                out[++o] = '<div class="content-input-text with-icon">';
                out[++o] = '<div class="icon link-ibp"></div>';
                var value_placeholder = 'http://www.linktoipb.com';
                out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional"  name="ibp_link" data-value="' + innovation["ibp_link"] + '" placeholder="' + value_placeholder + '" />';
                out[++o] = '</div>';
                out[++o] = '</div>'; // End form-element
            }

            out[++o] = '<div class="form-element">'; // Start form-element
            out[++o] = '<label>Press release</label>';
            out[++o] = '<div class="content-input-text with-icon">';
            out[++o] = '<div class="icon link-press"></div>';
            var value_placeholder = 'http://www.linktopressrelease.com';
            out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional"  name="press_release_link" data-value="' + innovation["press_release_link"] + '" placeholder="' + value_placeholder + '" />';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End form-element

            out[++o] = '</div>'; // End column left

            out[++o] = '</div>'; // End content column

            out[++o] = '</div>'; // End central-content-994
            out[++o] = '</div>'; // End background-color-fff
        }
    }
    return out.join('');
}

/**
 * explore_detail_init_edit
 * @param innovation
 */
function explore_detail_init_edit(innovation) {
    if ($('.full-content-explore-detail').hasClass('pri-tab')) {
        $('.full-content-explore-detail.pri-tab').tabs({
            activate: function(event, ui) {
                window.scrollTo(0, 0);
                $('.error-empty').removeClass('pulse').addClass('pulse');
                if ($(event.currentTarget).length > 0) {
                    var target = $(event.currentTarget).attr('href').replace('#', '');
                    var current_url = window.location.href;
                    var id = $('#detail-id-innovation').val();
                    var url = "/explore/" + id + "/tab/" + target;
                    opti_update_url(url, 'no_change');
                    opti_update_mouseflow(current_url, target);
                    if (target == 'overview') {
                        // Regenerate Overview;
                        var innovation = getInnovationById(id, true);
                        if (!innovation) {
                            return;
                        }
                        $('.full-content-explore-detail #overview').html(explore_generate_overview_tab(innovation['id']));
                        init_explore_detail(innovation);
                    } else if (target == 'markets') {
                        explore_detail_init_markets_tab();
                    }
                }
            }
        });
        explore_detail_edit_init_forms();
        explore_detail_edit_init_upload();
        explore_detail_update_nb_missing_fields();
    }
}

/**
 * explore_detail_edit_update_manage_forms
 */
function explore_detail_edit_update_manage_forms() {
    $(".full-content-explore-detail .form-element .content-select.with-icon:not(.no-icon-change) select").each(function() {
        var value = $(this).val();
        if (isInt(value)) {
            var name = $(this).attr('name');
            value = get_text_from_id(value, get_collection_for_name(name));
        }
        if (value) {
            var value_class = value.toCssClass();
            var added_class = '';
            if ($(this).parent().find('.icon').hasClass('light')) {
                added_class = 'light ';
            }
            $(this).parent().find('.icon').removeClass().addClass(added_class + 'icon ' + value_class);
        }
    });
    $('.full-content-explore-detail .input-disabled-stage').each(function() {
        var value = $(this).val();
        if (value) {
            var value_class = value.toCssClass();
            var added_class = '';
            if ($(this).parent().find('.icon').hasClass('light')) {
                added_class = 'light ';
            }
            $(this).parent().find('.icon').removeClass().addClass(added_class + 'icon ' + value_class);
        }
    });
    explore_detail_edit_check_error_classes();
}

/**
 * explore_detail_edit_check_error_classes
 */
function explore_detail_edit_check_error_classes() {
    $(".full-content-explore-detail .form-element .field.required").each(function() {
        $(this).closest('.form-element').removeClass('error-empty');
        if (!$(this).val() || $(this).val() == '') {
            $(this).closest('.form-element').addClass('error-empty');
        }
    });
}

/**
 * explore_detail_edit_init_forms
 */
function explore_detail_edit_init_forms() {
    $(".full-content-explore-detail .form-element .field:not(textarea)").each(function() {
        var value = $(this).attr('data-value');
        if (!value || value == 'null') {
            value = '';
        }
        if ($(this).hasClass('date') && value != '') {
            value = gmdate("yy-mm-dd", value);
        }
        if ($(this).hasClass('input-disabled-stage')) {
            value = returnGoodStage(value);
        }
        if ($(this).hasClass('growth_model')) {
            $('.description-growth-model').html(get_description_for_growth_model(value));
        }
        $(this).val(value);
    });
    explore_detail_edit_update_manage_forms();


    $(".full-content-explore-detail .form-element .content-select .select").select2({
        minimumResultsForSearch: -1,
        width: 'resolve'
    });

    $(".full-content-explore-detail .form-element .content-select .select-multiple option").attr('selected', 'selected');
    $(".full-content-explore-detail .form-element .content-select .select-multiple").select2({
        width: 'resolve',
        placeholder: 'Add a tag…',
        tags: true,
        minimumInputLength: 2,
        language: {
            errorLoading: function() {
                return "Searching..."
            }
        },
        ajax: {
            url: '/api/find/tags',
            dataType: 'json',
            type: "GET",
            processResults: function(data) {
                return {
                    results: data.items
                };
            }
        }
    })
    $(".full-content-explore-detail .form-element .content-select .select-multiple").parent().find('input.select2-search__field').attr('placeholder', 'Add a tag…');

    $('.full-content-explore-detail .text-limited').each(function() {
        var limit_text_value = parseInt($(this).attr('data-limit-text'));
        var hasBreakLine = ($(this).attr('data-has-break-line') !== undefined && $(this).attr('data-has-break-line') == "0") ? false : true;
        if (limit_text_value > 0) {
            $(this).parent().append('<div class="content-counter-editable">You have <span class="the-counter">' + limit_text_value + '</span> characters left. </div>');
            initLimitText($(this), $(this).parent().find('.the-counter'), limit_text_value, hasBreakLine);
        }
    });


    $(".full-content-explore-detail .form-element .content-select .select").change(function() {
        explore_update_inline_field($(this));
    });

    $(".full-content-explore-detail .form-element .content-select .select-multiple").change(function() {
        explore_update_inline_field($(this));
    });

    $(".full-content-explore-detail .form-element .content-input-text input, .full-content-explore-detail .form-element .content-textarea textarea").focus(function() {
        $(this).attr('data-old-value', $(this).val());
    });

    $(".full-content-explore-detail .form-element .content-input-text input, .full-content-explore-detail .form-element .content-textarea textarea").blur(function() {
        if ($(this).attr('data-old-value') != $(this).val() && !$(this).hasClass('not-innovation-data')) {
            explore_update_inline_field($(this));
        }
    });

    $('.full-content-explore-detail input.inline-radio-edition').change(function() {
        explore_update_inline_field($(this));
    });

    $(".full-content-explore-detail .content-input-text.date .field.date").datepicker({
        dateFormat: "yy-mm-dd",
        showOn: "both",
        buttonImageOnly: true,
        buttonImage: "/images/datepicker/pic-calendar.png",
        yearRange: '1900:+5',
        onSelect: function(dateText) {
            explore_update_inline_field($(this));
        }
    });
}

function explore_update_inline_field($field) {
    $field.addClass('loading').prop('disabled', true);
    $field.parent().append(get_loading_spinner_html());
    var id = $('#detail-id-innovation').val();
    var key = $field.attr('name');
    var value = $field.val();
    if (key == 'tag') {
        if (!value) {
            value = [];
        }
        var request_data = { id: id, tag_titles: value };
        explore_add_tag(request_data, function() {
            $field.removeClass('loading').prop('disabled', false);
            $field.parent().find('.loading-spinner').remove();
            explore_detail_edit_update_manage_forms();
        });
    } else if ($field.hasClass('performance-review')) {
        var request_data = { id: id, name: key, value: value };
        explore_update_inline_performance_review(request_data, function() {
            $field.removeClass('loading').prop('disabled', false);
            $field.parent().find('.loading-spinner').remove();
        });
    } else {
        var request_data = { id: id, name: key, value: value };
        explore_update_inline_quarterly_data(request_data, key, function() {
            $field.removeClass('loading').prop('disabled', false);
            $field.parent().find('.loading-spinner').remove();
            explore_detail_edit_update_manage_forms();
        }, function() {
            $field.removeClass('loading').prop('disabled', false);
            $field.parent().find('.loading-spinner').remove();
        });
    }
}


function explore_update_inline_quarterly_data(data, the_class, sucessCallBack, errorCallBack) {
    data['token'] = $('#hub-token').val();
    $.ajax({
        url: '/api/innovation/update',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.hasOwnProperty('status') && response['status'] == 'error') {
                add_flash_message('error', response['message']);
                if (typeof errorCallBack != "undefined") {
                    errorCallBack();
                }
            } else {
                if (response.hasOwnProperty('full_data')) {
                    updateFromCollection(response['full_data']['id'], response['full_data']);
                    explore_detail_update_nb_missing_fields();
                    add_saved_message();
                }
                if (typeof sucessCallBack != "undefined") {
                    sucessCallBack();
                }
                var reload_classes = ['is_frozen', 'classification'];
                if (in_array(the_class, reload_classes)) {
                    close_popup();
                    var id = $('#detail-id-innovation').val();
                    goToExploreDetailPage(id, 'edit')
                } else if (the_class == 'replace_existing_product') {
                    if (data['value'] == '1') {
                        $('.detail-content-existing_product, .overview-existing-product').removeClass('display-none');
                    } else {
                        $('.detail-content-existing_product, .overview-existing-product').addClass('display-none');
                    }
                } else if (the_class == 'growth_model') {
                    $('.description-growth-model').html(get_description_for_growth_model(data['value']));
                } else if (the_class == 'consumer_opportunity' && $('.element-consumer-opportunity-popup').is(':visible')) {
                    close_popup();
                    var id = $('#detail-id-innovation').val();
                    goToExploreDetailPage(id, 'edit');
                } else if (the_class == 'in_market_date') {
                    var id = $('#detail-id-innovation').val();
                    var innovation = getInnovationById(id, true);
                    if (innovation && innovation['in_market_date']) {
                        var market_date_value = ((innovation['in_market_date']) ? gmdate("M Y", innovation['in_market_date']) : 'Missing data');
                        var added_classe = (!innovation['in_market_data']) ? 'style_missing_data' : '';
                        $('.lifecycle-text-in-market-date').removeClass('style_missing_data').addClass(added_classe).html(market_date_value);
                    }
                } else if (
                    the_class == 'growth_strategy' &&
                    (data['value'] == 'New Business Acceleration' || response['data']['old_value'] == 'New Business Acceleration')
                ) {
                    var id = $('#detail-id-innovation').val();
                    goToExploreDetailPage(id, 'edit');
                    // I REFRESH PAGE
                }
            }
        },
        error: function(resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
        }
    });
}

function explore_update_inline_performance_review(data, sucessCallBack) {
    data['token'] = $('#hub-token').val();
    $.ajax({
        url: '/api/innovation/performance-review/update',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.hasOwnProperty('status') && response['status'] == 'error') {
                add_flash_message('error', response['message']);
            } else {
                if (response.hasOwnProperty('full_data')) {
                    updateFromCollection(response['full_data']['id'], response['full_data']);
                    explore_detail_update_nb_missing_fields();
                    add_saved_message();
                }
                if (typeof sucessCallBack != "undefined") {
                    sucessCallBack();
                }
            }
        },
        error: function(resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
        }
    });
}

function explore_add_tag(data, sucessCallBack) {
    data['token'] = $('#hub-token').val();
    $.ajax({
        url: '/api/innovation/tags/update',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.hasOwnProperty('status') && response['status'] == 'error') {
                add_flash_message('error', response['message']);
            } else if (response.hasOwnProperty('full_data')) {
                updateFromCollection(response['full_data']['id'], response['full_data']);
                explore_detail_update_nb_missing_fields();
                add_saved_message();
            }
            $(".full-content-explore-detail .form-element .content-select .select-multiple").parent().find('input.select2-search__field').attr('placeholder', 'Add a tag…');
            if (typeof sucessCallBack != "undefined") {
                sucessCallBack();
            }
        },
        error: function(resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
        }
    });
}

$(document).ready(function() {


    $('#form-open-question').submit(function() {
        updateOpenQuestion();
        return false;
    });

    $(document).on('click', '.update-open-question-popup-button', function(e) {
        e.stopImmediatePropagation();
        updateOpenQuestion();
    });

    function updateOpenQuestion() {
        if ($('.custom-content-popup.opened').hasClass('loading')) {
            return false;
        }
        $('.element-popup-open-question .form-element').removeClass('error');
        $('.element-popup-open-question .form-element-error').remove();
        var data = {};
        data['token'] = $('#hub-token').val();
        data['id'] = $('#detail-id-innovation').val();
        var hasError = false;
        var message = $('#form-open-question-message').val();
        var old_message = $('#form-open-question-message').attr('data-old-value');
        data['message'] = message;
        if (message == old_message) {
            hasError = true;
            close_popup();
        }
        if (!hasError) {
            enable_load_popup();
            $.ajax({
                url: '/api/innovation/open-question/update',
                type: "POST",
                data: data,
                dataType: 'json',
                success: function(response, statut) {
                    disable_load_popup();
                    if (response['status'] == 'field_error') {
                        $('#' + response['error_id']).parent().parent().addClass('error').append('<div class="form-element-error">' + response.message + '</div>');
                    } else if (response['status'] == 'error') {
                        add_flash_message('error', response['message']);
                        close_popup();
                    } else {
                        close_popup();
                        if (response.hasOwnProperty('full_data')) {
                            updateFromCollection(response['full_data']['id'], response['full_data']);
                            explore_detail_update_nb_missing_fields();
                            var id = $('#detail-id-innovation').val();
                            goToExploreDetailPage(id, 'edit');
                        }
                        add_saved_message();
                    }
                },
                error: function(resultat, statut, e) {
                    if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                        add_flash_message('error', 'Impossible to update open questions for now : ' + resultat.status + ' ' + e);
                    }
                    disable_load_popup();
                    close_popup();
                }
            });
        }
    }
});