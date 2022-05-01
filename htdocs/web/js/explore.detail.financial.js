/* Tab Financial
 ----------------------------------------------------------------------------------------*/

var need_to_update_financial_graph_picture = false;

/**
 * explore_get_financial_tab_html_template
 * @param id
 * @returns {*}
 */
function explore_get_financial_tab_html_template(id) {
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    var innovation_is_a_service = check_if_innovation_is_a_service(innovation);
    var current_stage = innovation['current_stage'];
    var edition_mode_quanti_enabled = get_edition_mode_for_innovation(innovation);
    var edition_mode_quali_enabled = (check_if_user_is_hq() || (user_can_edit_this_innovation(innovation)) && check_if_data_quali_capture_is_opened());
    var field_disabled_quali = ((edition_mode_quali_enabled) ? '' : 'disabled');
    var field_disabled_quanti = ((edition_mode_quanti_enabled) ? '' : 'disabled');
    var field_no_hq_disabled = ((check_if_user_is_hq()) ? '' : 'disabled');
    var financial_date = get_financial_date();
    var innovation_is_nba  = check_if_innovation_is_nba(innovation);

    out[++o] = '<div class="central-content-994 padding-top-30 padding-bottom-30">'; // Start central-content-994
    out[++o] = '<div class="overflow-hidden" id="first-content-financial"></div>'; // first-content-financial
    out[++o] = '<div class="contextual-headline">';
    out[++o] = innovation['title'];
    out[++o] = '</div>';
    if (innovation_is_a_service && !innovation_is_nba) {
        out[++o] = '<div class="headline padding-top-5 font-montserrat-700">Investment</div>';
    } else {
        out[++o] = '<div class="headline padding-top-5 font-montserrat-700">Business figures</div>';
    }
    out[++o] = '<div class="content-tab-financial margin-top-25">'; // Start content-tab-financial
    if (!innovation_is_nba && !innovation_is_a_service && !in_array(current_stage, ['discover', 'ideate'])) {
        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>Comment your performance review</label>';
        out[++o] = '<div class="content-textarea">';
        out[++o] = '<textarea name="complement_' + financial_date + '" class="performance-review" data-limit-text="600" ' + field_disabled_quanti + '>' + innovation['performance_review'] + '</textarea>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element
    }

    out[++o] = '<div id="business-financial-table" class="margin-top-50" data-financial-date="' + get_financial_date() + '" data-trimester="' + get_current_trimestre() + '">'; // Start business-financial-table
    out[++o] = '<div class="table-changer-financial-date margin-bottom-20">'; // Start table-changer-financial-date
    out[++o] = '<div class="arrow left transition-background-color"></div>';
    out[++o] = '<div class="libelle font-montserrat-500">LE3 18</div>';
    out[++o] = '<div class="arrow right transition-background-color disabled"></div>';
    out[++o] = '</div>'; // End table-changer-financial-date

    out[++o] = '<div class="content-parent-table-financial position-relative">'; // Start content-parent-table-financial
    out[++o] = '<table class="table-financial ' + current_stage + '">'; // Start table-financial
    out[++o] = '<tbody>';
    out[++o] = '<tr><td>Year</td><td class="invisible"></td></tr>';
    if(innovation_is_nba){
        out[++o] = '<tr><td>Investment (k€)</td><td class="invisible"></td></tr>';
        out[++o] = '<tr><td>Revenue (k€)</td><td class="invisible"></td></tr>';
    }else if (innovation_is_a_service) {
        out[++o] = '<tr><td>Central Invest<br>(k€)</td><td class="invisible"></td></tr>';
        out[++o] = '<tr><td>A&P (k€)</td><td class="invisible"></td></tr>';
    } else {
        if (!in_array(current_stage, ['discover', 'ideate'])) {
            out[++o] = '<tr><td>Volume (' + global_wording['volume_unit'] + ')</td><td class="invisible"></td></tr>';
            if (!in_array(current_stage, ['experiment'])) {
                out[++o] = '<tr><td>Net sales (k€)</td><td class="invisible"></td></tr>';
                out[++o] = '<tr><td>Contributing Margin<br>(k€)</td><td class="invisible"></td></tr>';
            }
        }
        out[++o] = '<tr><td>Central Invest (k€)</td><td class="invisible"></td></tr>';
        if (!in_array(current_stage, ['discover', 'ideate'])) {
            out[++o] = '<tr><td>A&P (k€)</td><td class="invisible"></td></tr>';
        }
        if (!in_array(current_stage, ['discover', 'ideate', 'experiment'])) {
            out[++o] = '<tr><td>COGS (k€)</td><td class="invisible"></td></tr>';
        }
    }
    out[++o] = '</tbody>';
    out[++o] = '</table>'; // End table-financial

    if(!innovation_is_nba) {
        out[++o] = '<div class="parent-table-automated-financial">'; // Start parent-table-automated-financial
        out[++o] = '<table class="table-financial-automated">';
        out[++o] = '<tbody>';
        out[++o] = '<tr><td>Total A&P (k€)*</td><td class="invisible"></td></tr>';
        if (!innovation_is_a_service) {
            out[++o] = '<tr><td>CAAP (k€)*</td><td class="invisible"></td></tr>';
            out[++o] = '<tr><td>CM per case (€)*</td><td class="invisible"></td></tr>';
            if (!in_array(current_stage, ['discover', 'ideate', 'experiment'])) {
                out[++o] = '<tr><td>COGS per case (€)*</td><td class="invisible"></td></tr>';
            }
        }
        out[++o] = '</tbody>';
        out[++o] = '</table>';
        out[++o] = '</div>'; // End parent-table-automated-financial
    }

    out[++o] = '<div class="table-financial-loading"><div class="loading-spinner loading-spinner--26"></div></div>';

    out[++o] = '</div>'; // End content-parent-table-financial
    if(!innovation_is_nba) {
        out[++o] = '<div class="text-align-right margin-top-10 font-size-13 color-b5b5b5 font-work-sans-500">*This data is automatically generated</div>';
    }
    out[++o] = '</div>'; // End business-financial-table

    if (innovation_is_nba || !innovation_is_a_service) {
        out[++o] = '<div class="margin-top-40 margin-bottom-20 font-size-15 color-000 font-work-sans-300 line-height-1-53">Want to check the latest exchange rates as given by the central finance / Prisma team? Download the document below:</div>';
        out[++o] = '<div class="content-link-file">'; // Start content-link-file
        out[++o] = '<div class="picto"></div>';
        out[++o] = '<div class="infos">'; // Start infos
        out[++o] = '<a class="hub-link-on-hover font-size-15 color-000 font-montserrat-700 line-height-1-53" href="/documents/FX_rates_LE2_19.pdf " target="_blank">Download LE2 19 FX rate</a>';
        out[++o] = '<div class="color-b5b5b5 font-montserrat-500 font-size-13 line-height-1">558 ko</div>';
        out[++o] = '</div>'; // End infos
        out[++o] = '</div>'; // End content-link-file
    }

    // GRAPH
    if (!innovation_is_nba && !innovation_is_a_service && !in_array(current_stage, ['empty', 'Empty', 'discover', 'ideate', 'experiment'])) {
        out[++o] = '<div class="background-color-f8f8f8 margin-top-40 padding-top-30 padding-bottom-30 padding-left-40 padding-right-40">'; // Start background-color-f8f8f8
        out[++o] = '<div class="font-work-sans-600 font-size-20 line-height-1-5 color-000">CAAP evolution</div>';
        out[++o] = '<div class="font-work-sans-300 font-size-15 line-height-1-53 color-000">Follow your innovation\'s ROI over time.</div>';
        out[++o] = '<div class="explore-detail-content-financial-graph margin-top-40 position-relative content-masked">'; // Start explore-detail-content-financial-graph
        out[++o] = '<img class="mask-img" src="/images/masks/915x520.png"/>';
        out[++o] = '<canvas id="explore-detail-innovation-quanti-line-chart-financial" width="915" height="520" class="absolute-chart"></canvas>';
        out[++o] = '</div>'; // End explore-detail-content-financial-graph
        out[++o] = '</div>'; // End background-color-f8f8f8

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

        out[++o] = '<div class="padding-top-35 padding-bottom-15">'; // Start padding-top-35
        out[++o] = '<div class="font-montserrat-500 margin-bottom-30 line-height-1-5 color-9b9b9b font-size-20">Performance over time</div>';
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
        out[++o] = '</div>'; // End padding-top-35

        out[++o] = '<div class="position-relative font-work-sans-400 font-size-12 line-height-1 color-03AFF0-0-8 margin-bottom-20">*Evolutions at current rate. All figures from <span class="libelle_current_fy">FY19</span> are restated for IFRS15 norm.</div>';
    }

    out[++o] = '<div class="margin-top-40 margin-bottom-60">'; // Start margin-top-40
    if (!innovation_is_nba && !innovation_is_a_service) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 margin-bottom-30">Growth model</div>';

        out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
        out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<div class="content-select with-icon">';
        out[++o] = '<div class="icon"></div>';
        out[++o] = '<select class="auto-save select field growth_model" ' + field_disabled_quanti + ' data-value="' + innovation['growth_model'] + '" name="growth_model">';
        var options = get_innovation_growth_model();
        for (var i = 0; i < options.length; i++) {
            out[++o] = '<option value="' + options[i]['id'] + '">' + options[i]['text'] + ' = ' + options[i]['description'] + '</option>';
        }
        out[++o] = '</select>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element
        out[++o] = '</div>'; // End column-percent-50
        out[++o] = '</div>'; // End overflow-hidden
        out[++o] = '<div class="description-growth-model font-work-sans-300 font-size-15 line-height-1-53 color-787878"></div>';

        out[++o] = '<div class="margin-top-40 margin-bottom-20 font-size-15 color-000 font-work-sans-300 line-height-1-53">Want to know more about innovation lifecycles and investment behaviors related to your growth model?<br>This document is for you:</div>';
        out[++o] = '<div class="content-link-file">'; // Start content-link-file
        out[++o] = '<div class="picto"></div>';
        out[++o] = '<div class="infos">'; // Start infos
        out[++o] = '<a class="hub-link-on-hover font-size-15 color-000 font-montserrat-700 line-height-1-53" href="/documents/Innovation growth models for products.pdf" target="_blank">Innovation growth models for products</a>';
        out[++o] = '<div class="color-b5b5b5 font-montserrat-500 font-size-13 line-height-1">455 ko</div>';
        out[++o] = '</div>'; // End infos
        out[++o] = '</div>'; // End content-link-file

    } else if(!innovation_is_nba) {
        out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 margin-bottom-30">Your plan for monetization</div>';

        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>Have you earned any money yet?</label>';
        out[++o] = '<div class="content-input-radio">';
        out[++o] = '<input type="radio" value="0" name="have_earned_any_money_yet" class="have_earned_any_money_yet inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
        out[++o] = ' id="have_earned_any_money_yet_no" ' + ((innovation['have_earned_any_money_yet'] == '0') ? "checked" : "") + '>';
        out[++o] = '<label class="fake-checkbox" for="have_earned_any_money_yet_no"></label>';
        out[++o] = '<label class="fake-label" for="have_earned_any_money_yet_no">No</label>';
        out[++o] = '<input type="radio" value="1" name="have_earned_any_money_yet" class="have_earned_any_money_yet inline-radio-edition checkbox-content-filter" ' + field_disabled_quali;
        out[++o] = ' id="have_earned_any_money_yet_yes" ' + ((innovation['have_earned_any_money_yet'] == '1') ? "checked" : "") + '>';
        out[++o] = '<label class="fake-checkbox" for="have_earned_any_money_yet_yes"></label>';
        out[++o] = '<label class="fake-label" for="have_earned_any_money_yet_yes"> Yes</label>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element

        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>How do you plan to make money?</label>';
        out[++o] = '<div class="content-textarea">';
        out[++o] = '<textarea name="plan_to_make_money" data-limit-text="255" data-has-break-line="0" ' + field_disabled_quali + '>' + innovation['plan_to_make_money'] + '</textarea>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element
    }
    if(innovation_is_nba){

        out[++o] = '<div class="padding-top-40 color-9b9b9b font-montserrat-500 font-size-20 line-height-1-5 padding-bottom-30">Financials since '+gmdate('F Y', innovation['start_date'])+'</div>';
        out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
        out[++o] = '<div class="column-percent-70">'; // Start column-percent-70
        out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
        out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
        out[++o] = '<div class="other-info-consolidation cumul_investment" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">';
        out[++o] = '<div class="libelle"><span>Total investment since start</span></div>';
        out[++o] = '<div class="under-libelle"><span>'+number_format_en(innovation['financial']['data']['cumul']['investment'])+'</span> k€</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End column-percent-50
        out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
        out[++o] = '<div class="other-info-consolidation cumul_revenue" data-percent="0" data-old-value="0" data-old-date="" data-devise="k€">';
        out[++o] = '<div class="libelle"><span>Total revenue since start</span></div>';
        out[++o] = '<div class="under-libelle"><span>'+number_format_en(innovation['financial']['data']['cumul']['revenue'])+'</span> k€</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End column-percent-50
        out[++o] = '</div>'; // End overflow-hidden
        out[++o] = '</div>'; // End column-percent-70
        out[++o] = '</div>'; // End overflow-hidden

        out[++o] = '<div class="padding-top-75 color-9b9b9b font-montserrat-500 font-size-20 line-height-1-5 padding-bottom-30">People working on the project</div>';
        out[++o] = '<div class="overflow-hidden">'; // Start overflow-hidden
        out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
        out[++o] = '<div class="padding-right-20">'; // Start padding-right-20
        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>Project owner disponibility</label>';
        out[++o] = '<div class="content-input-text">';
        out[++o] = '<input type="text" ' + field_disabled_quanti + ' class="field required" name="project_owner_disponibility" data-value="' + innovation["project_owner_disponibility"] + '" />';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element
        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>Full time employees (FTE)<div class="helper-button" data-target="full_time_employees">?</div></label>';
        out[++o] = '<div class="content-input-text">';
        out[++o] = '<input type="text" ' + field_disabled_quanti + ' class="field required" name="full_time_employees" data-value="' + innovation["full_time_employees"] + '" />';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element
        out[++o] = '</div>'; // End padding-right-20
        out[++o] = '</div>'; // End column-percent-50
        out[++o] = '<div class="column-percent-50">'; // Start column-percent-50
        out[++o] = '<div class="padding-left-20">'; // Start padding-left-20
        out[++o] = '<div class="form-element">'; // Start form-element
        out[++o] = '<label>External support<div class="helper-button" data-target="external_text">?</div></label>';
        out[++o] = '<div class="content-textarea">';
        out[++o] = '<textarea class="field required" name="external_text" ' + field_disabled_quanti + ' style="min-height: 108px;">' + innovation['external_text'] + '</textarea>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End form-element
        out[++o] = '</div>'; // End padding-left-20
        out[++o] = '</div>'; // End column-percent-50
        out[++o] = '</div>'; // End overflow-hidden

    }
    out[++o] = '</div>'; // End margin-top-40


    out[++o] = '</div>'; // End content-tab-financial
    out[++o] = '</div>'; // End central-content-994
    return out.join('');
}

/**
 * explore_init_tab_financial
 * @param id
 */
function explore_init_tab_financial(id) {
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }

    $('.table-changer-financial-date .arrow').click(function () {
        if (!$(this).hasClass('disabled')) {
            var current_date = $('#business-financial-table').attr('data-financial-date');
            var old_trimester = parseInt($('#business-financial-table').attr('data-trimester'));
            if ($(this).hasClass('left')) {
                var trimester = old_trimester - 1;
                if (trimester <= 0) {
                    trimester = 4;
                }
            } else {
                var trimester = old_trimester + 1;
                if (trimester >= 5) {
                    trimester = 1;
                }
            }
            var next_date = get_financial_date_by_old_trimester_trimester_and_date(old_trimester, trimester, current_date);
            $('#business-financial-table').attr('data-financial-date', next_date);
            $('#business-financial-table').attr('data-trimester', trimester);
            explore_set_tab_financial();
        }
    });
    $('.content-tab-financial .libelle_current_le').html(get_libelle_current_le());
    $('.content-tab-financial .libelle_versus_date').html(get_libelle_last_a());
    var financial_date = get_financial_date();
    $('.content-tab-financial .libelle_current_fy').html(get_financial_date_FY(financial_date));
    explore_detail_after_update_financial_data(id);
    explore_detail_financial_init_graph();
    explore_set_tab_financial();
    init_information_open_date_in_financial_tab();
}

function explore_set_tab_financial() {
    var selected_date = $('#business-financial-table').attr('data-financial-date');
    var max_date = get_financial_date_by_trimester(get_current_trimestre());
    var libelle = get_financial_date_libelle(selected_date);

    $('.table-changer-financial-date .libelle').text(libelle);
    $('.table-changer-financial-date .arrow').removeClass('disabled');
    if (selected_date == max_date) {
        $('.table-changer-financial-date .right').addClass('disabled');
    }
    var data = {};
    data['id'] = $('#detail-id-innovation').val();
    data['date'] = selected_date;
    $('.table-financial-loading').removeClass('display-none');
    if (explore_financial_tab_table_request != null)
        explore_financial_tab_table_request.abort();
    explore_financial_tab_table_request = $.ajax({
        url: '/api/innovation/financial/data-for-date',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response, statut) {
            $('.table-financial-loading').addClass('display-none');
            $('.table-financial tbody').html(response['html']);
            if($('.table-financial-automated').length > 0) {
                $('.table-financial-automated tbody').html(response['automated_data']);
            }
        },
        error: function (resultat, statut, e) {
            $('.table-financial-loading').addClass('display-none');
            if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                add_flash_message('error', 'Error: ' + resultat.status + ' ' + e);
            }
        }
    });
}

/**
 * explore_detail_after_update_financial_data
 * @param id
 */
function explore_detail_after_update_financial_data(id, automated_data) {
    var innovation = getInnovationById(id, true);
    automated_data = automated_data || null;
    if (!innovation) {
        return;
    }
    if(automated_data){
        $('.table-financial-automated tbody').html(automated_data);
    }
    explore_detail_dataset_innovation = innovation['financial']['dataset'];
    var current_stage = innovation['current_stage'];
    var early_stages = ['discover', 'ideate', 'experiment'];
    var $element = $('.content-tab-financial');

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
        if($element.find('.other-info-consolidation.' + el_key).length > 0) {
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
        if($element.find('.other-info-consolidation.' + el_key + " .consolidation-movement").length > 0) {
            var percent_class = (percent >= 0) ? 'positive' : 'negative';
            $element.find('.other-info-consolidation.' + el_key + " .consolidation-movement").removeClass('positive negative').addClass(percent_class).html(Math.abs(percent) + ' %');
        }
    }
    var innovation_is_nba  = check_if_innovation_is_nba(innovation);
    if(innovation_is_nba){
        var keys = ['cumul_investment', 'cumul_revenue'];
        for (var i = 0; i < keys.length; i++) {
            var el_key = keys[i];
            var el_data = statistics[el_key];
            $('.other-info-consolidation.' + el_key + ' .under-libelle span').html(number_format_en(el_data));
        }
    }

    var min_tick = explore_detail_dataset_innovation[2]['min_value'];
    if (explore_financial_tab_innovationFinancialLineChart != null && $('#explore-detail-innovation-quanti-line-chart-financial').length > 0) {
        explore_financial_tab_innovationFinancialLineChart.data.datasets = explore_detail_dataset_innovation;
        explore_financial_tab_innovationFinancialLineChart.config.options.scales.xAxes[0].ticks.min = min_tick;
        explore_financial_tab_innovationFinancialLineChart.config.options.scales.yAxes[0].ticks.min = min_tick;
        explore_financial_tab_innovationFinancialLineChart.update();
    }
}

/**
 * explore_detail_financial_get_statistics_percent_movements
 * @param product
 * @returns {{net_sales: number, total_ap: number, caap: number, volume: number, level_of_investment: number, level_of_profitability: number}}
 */
function explore_detail_financial_get_statistics_percent_movements(product) {
    var total = {
        'latest': {
            'total_ap': 0,
            'caap': 0,
            'net_sales': 0,
            'volume': 0,
            'contributing_margin': 0,
            'level_of_investment': 0,
            'level_of_profitability': 0,
            'cm_per_case': 0,
            'cogs_per_case': 0,
            'cumul_total_ap': 0,
            'cumul_caap': 0

        },
        'latest_a': {
            'total_ap': 0,
            'caap': 0,
            'net_sales': 0,
            'volume': 0,
            'contributing_margin': 0,
            'level_of_investment': 0,
            'level_of_profitability': 0,
            'cm_per_case': 0,
            'cogs_per_case': 0,
            'cumul_total_ap': 0,
            'cumul_caap': 0
        }
    };
    var early_stages = ['discover', 'ideate', 'experiment'];
    var current_stage = product['current_stage'];
    total['latest']['total_ap'] += product['financial']['data']['latest']['calc']['total_ap'];
    total['latest']['net_sales'] += product['financial']['data']['latest']['calc']['net_sales'];
    total['latest']['volume'] += product['financial']['data']['latest']['calc']['volume'];
    total['latest']['contributing_margin'] += product['financial']['data']['latest']['calc']['contributing_margin'];
    total['latest']['cogs'] += product['financial']['data']['latest']['calc']['cogs'];

    total['latest']['cumul_total_ap'] += product['financial']['data']['cumul']['total_ap'];

    var total_volume_le = parseInt(product['financial']['data']['latest']['calc']['volume']);
    var total_cm_value = parseInt(product['financial']['data']['latest']['calc']['contributing_margin']);
    var total_cogs_value = parseInt(product['financial']['data']['latest']['calc']['cogs']);
    var cm_per_case_value = (total_cm_value != 0 && total_volume_le != 0) ? (total_cm_value / total_volume_le) : "N/A";
    var cogs_per_case_value = (total_cogs_value != 0 && total_volume_le != 0) ? (total_cogs_value / total_volume_le) : "N/A";
    total['latest']['cm_per_case'] = cm_per_case_value;
    total['latest']['cogs_per_case'] = cogs_per_case_value;

    total['latest_a']['total_ap'] += product['financial']['data']['latest_a']['calc']['total_ap'];
    total['latest_a']['net_sales'] += product['financial']['data']['latest_a']['calc']['net_sales'];
    total['latest_a']['volume'] += product['financial']['data']['latest_a']['calc']['volume'];
    total['latest_a']['contributing_margin'] += product['financial']['data']['latest_a']['calc']['contributing_margin'];
    total['latest_a']['cogs'] += product['financial']['data']['latest_a']['calc']['cogs'];

    var total_volume_le = parseInt(product['financial']['data']['latest_a']['calc']['volume']);
    var total_cm_value = parseInt(product['financial']['data']['latest_a']['calc']['contributing_margin']);
    var total_cogs_value = parseInt(product['financial']['data']['latest_a']['calc']['cogs']);
    var cm_per_case_value = (total_cm_value != 0 && total_volume_le != 0) ? (total_cm_value / total_volume_le) : "N/A";
    var cogs_per_case_value = (total_cogs_value != 0 && total_volume_le != 0) ? (total_cogs_value / total_volume_le) : "N/A";
    total['latest_a']['cm_per_case'] = cm_per_case_value;
    total['latest_a']['cogs_per_case'] = cogs_per_case_value;

    total['latest_a']['cumul_total_ap'] = total['latest']['cumul_total_ap'] - total['latest']['total_ap'];
    if (!in_array(current_stage, early_stages)) {
        total['latest']['caap'] += product['financial']['data']['latest']['calc']['caap'];
        total['latest']['cumul_caap'] += product['financial']['data']['cumul']['caap'];

        total['latest_a']['caap'] += product['financial']['data']['latest_a']['calc']['caap'];
        total['latest_a']['cumul_caap'] = total['latest']['cumul_caap'] - total['latest']['caap'];
    }

    total['latest']['level_of_investment'] = calculate_level_of_investment(total['latest']['total_ap'], total['latest']['net_sales']);
    total['latest_a']['level_of_investment'] = calculate_level_of_investment(total['latest_a']['total_ap'], total['latest_a']['net_sales']);
    total['latest']['level_of_profitability'] = calculate_level_of_profitability(total['latest']['contributing_margin'], total['latest']['net_sales']);
    total['latest_a']['level_of_profitability'] = calculate_level_of_profitability(total['latest_a']['contributing_margin'], total['latest_a']['net_sales']);
    var pts_level_of_investment = (total['latest_a']['level_of_investment'] != "NEW" && total['latest']['level_of_investment'] != 'NEW') ? Math.round(total['latest']['level_of_investment'] - total['latest_a']['level_of_investment']) : 0;
    var pts_level_of_probability = Math.round(total['latest']['level_of_profitability'] - total['latest_a']['level_of_profitability']);

    var percent_cm_per_case = (total['latest']['cm_per_case'] != "N/A" && total['latest_a']['cm_per_case'] != "N/A") ? Math.round(get_percent_diff_between_two_values(total['latest_a']['cm_per_case'], total['latest']['cm_per_case'])) : 0;
    var percent_cogs_per_case = (total['latest']['cogs_per_case'] != "N/A" && total['latest_a']['cogs_per_case'] != "N/A") ? Math.round(get_percent_diff_between_two_values(total['latest_a']['cogs_per_case'], total['latest']['cogs_per_case'])) : 0;


    return {
        'net_sales': Math.round(get_percent_diff_between_two_values(total['latest_a']['net_sales'], total['latest']['net_sales'])),
        'total_ap': Math.round(get_percent_diff_between_two_values(total['latest_a']['total_ap'], total['latest']['total_ap'])),
        'caap': Math.round(get_percent_diff_between_two_values(total['latest_a']['caap'], total['latest']['caap'])),
        'volume': Math.round(get_percent_diff_between_two_values(total['latest_a']['volume'], total['latest']['volume'])),
        'cm_per_case': percent_cm_per_case,
        'cogs_per_case': percent_cogs_per_case,
        'cumul_total_ap': Math.round(get_percent_diff_between_two_values(total['latest_a']['cumul_total_ap'], total['latest']['cumul_total_ap'])),
        'cumul_caap': Math.round(get_percent_diff_between_two_values(total['latest_a']['cumul_caap'], total['latest']['cumul_caap'])),
        'level_of_investment': pts_level_of_investment,
        'level_of_profitability': pts_level_of_probability
    };
}

/**
 * explore_detail_financial_init_graph
 */
function explore_detail_financial_init_graph() {
    if ($('#explore-detail-innovation-quanti-line-chart-financial').length > 0) {

        Chart.defaults.global.legend.display = false;
        //Chart.defaults.global.tooltips.enabled = false;
        //Chart.interaction.enabled = false;
        Chart.defaults.global.hover.intersect = false;

        Chart.defaults.global.defaultFontColor = "#8ca0b3";
        Chart.defaults.global.defaultFontFamily = "Work Sans";
        Chart.defaults.global.defaultFontSize = 10;
        var explore_detail_innovationFinancialLineChartCanvas = document.getElementById('explore-detail-innovation-quanti-line-chart-financial').getContext('2d');

        var ticks_array = {
            min: explore_detail_dataset_innovation[2]['min_value']
        }

        explore_financial_tab_innovationFinancialLineChart = new Chart(explore_detail_innovationFinancialLineChartCanvas, {
            type: 'line',
            //showTooltips: false,
            data: {
                datasets: explore_detail_dataset_innovation
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
                    onComplete: function () {
                        var ctx = this.chart.ctx;

                        ctx.textAlign = "center";
                        ctx.textBaseline = "bottom";

                        // Y Axis
                        ctx.fillStyle = "#4a4a4a";
                        ctx.font = "14px Work Sans";
                        ctx.fillText("CM (k€)", 75, 18);
                        // X Axis
                        ctx.fillText("Total A&P (k€)", 867, 468);

                        // positive-info
                        ctx.fillStyle = "#45ab34";
                        ctx.font = "14px Work Sans";
                        ctx.fillText("Positive CAAP", 420, 15);

                        // neutral-info
                        ctx.fillStyle = "#b5b5b5";
                        ctx.font = "14px Work Sans";
                        ctx.fillText("Neutral CAAP", 863, 15);

                        // negative-info
                        ctx.fillStyle = "#e8485c";
                        ctx.fillText("Negative CAAP", 859, 267);
                        this.chart.config.data.datasets.forEach(function (dataset) {
                            if (dataset.label == "good_line") {
                                ctx.fillStyle = dataset.borderColor;
                                ctx.font = "14px 'Work Sans'";
                                var metas = dataset._meta;
                                var loaded = false;
                                for (var propertyName in metas) {
                                    if (!loaded) {
                                        var preview_position = null;
                                        var future_id = 1;
                                        dataset._meta[propertyName]['data'].forEach(function (p) {
                                            var aspectRatio = 1; // 1.47
                                            var new_position = {
                                                x: Math.floor((p._model.x + 10) * aspectRatio),
                                                y: Math.floor((p._model.y + 10) * aspectRatio)
                                            };
                                            var the_case = null;
                                            var future_position = null;
                                            var future_p = dataset._meta[propertyName]['data'][future_id];
                                            if (future_p) {
                                                future_position = {
                                                    x: (Math.floor(future_p._model.x + 10) * aspectRatio),
                                                    y: Math.floor((future_p._model.y + 10) * aspectRatio),
                                                    label: dataset.data[future_id]['label']
                                                };
                                            }
                                            var positions = {
                                                previous: preview_position,
                                                current: new_position,
                                                future: future_position,
                                                label: dataset.data[p._index]['label']
                                            };
                                            var best_position = get_graph_libelle_best_position(positions, 6);

                                            var all_p = {
                                                previous: preview_position,
                                                current: best_position,
                                                future: future_position,
                                                the_case: the_case,
                                                label: dataset.data[p._index]['label']
                                            };
                                            ctx.fillStyle = dataset.borderColor;
                                            if (dataset.line_type != "second" || (dataset.line_type == "second" && preview_position)) {
                                                ctx.fillText(dataset.data[p._index]['label'], best_position.x, best_position.y);
                                            }
                                            preview_position = {
                                                x: Math.floor(p._model.x * aspectRatio),
                                                y: Math.floor(p._model.y * aspectRatio)
                                            };
                                            future_id++;
                                        });
                                        loaded = true;
                                    }
                                }
                            }
                        });
                        if (this.animating && need_to_update_financial_graph_picture) {
                            var base64_image = this.toBase64Image();
                            updateFinancialGraph(base64_image);
                        }
                    }
                }
            }
        });


    }
}

function explore_update_financial_data_input($input) {
    need_to_update_financial_graph_picture = true;
    var date = $('#business-financial-table').attr('data-financial-date');
    var data = {};
    data['id'] = $('#detail-id-innovation').val();
    data['date'] = date;
    data['token'] = $('#hub-token').val();
    var value = $input.val();
    var name = $input.attr('name');
    data[name] = value;
    $input.removeClass('error-empty').prop('disabled', true);
    $input.closest("td").addClass('loading').removeClass('empty');
    $.ajax({
        url: '/api/innovation/financial/update',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response, statut) {
            $input.prop('disabled', false);
            if (response.hasOwnProperty('full_data')) {
                updateFromCollection(response['full_data']['id'], response['full_data']);
                explore_detail_update_nb_missing_fields();
                add_saved_message();
            }
            $input.closest("td").removeClass('loading');
            explore_detail_after_update_financial_data(response['id'], response['automated_data']);
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            $input.prop('disabled', false);
            $input.closest("td").removeClass('loading');
        }
    });
}

function explore_detail_financial_generateNAToast($input) {
    var el_name = $input.attr('name');
    if ($('.toast-button_' + el_name).length == 0) {
        var name = el_name.replace(/_/gi, ' ');
        var capitalized = name[0].toUpperCase() + name.substr(1);
        var link = '<a class="margin-left-0 toast_replace toast-button_' + el_name + '" data-name="' + el_name + '">Replace it by N/A</a>';
        var message = capitalized + ' is not zero?<br>' + link;
        add_flash_message('notice', message);
    }
}


$(document).ready(function () {
    var financial_tab_oldData = null;
    $(document).on("focus", '.table-financial td input', function () {
        $(this).parent().addClass('focused');
        financial_tab_oldData = $(this).val();
    });


    $(document).on('click', '.toast_replace', function (e) {
        e.stopImmediatePropagation();
        var name = $(this).attr('data-name');
        $('#' + name).val('N/A');
        explore_update_financial_data_input($('#' + name));
        var $toast = $(this).parent().parent();
        $toast.hide('fade');
        setTimeout(function () {
            $toast.remove();
        }, 350);
        return false;
    });


    $(document).on("click", '.table-financial td input', function (e) {
        e.stopImmediatePropagation();
        $(this).select();
    });

    $(document).on("blur", '.table-financial td .financial-td-input', function (e) {
        e.stopImmediatePropagation();
        var isError = false;
        $(this).parent().removeClass('focused');
        var value = $(this).val();
        if (value && !isFloat(value) && value != "N/A") {
            $(this).addClass('error');
            isError = true;
        } else {
            $(this).removeClass('error');
            if ($(this).hasClass('negative') && value != "N/A") {
                var new_value = (value <= 0) ? value : -value;
                $(this).val(new_value);
            }
        }
        if (value === '0') {
            explore_detail_financial_generateNAToast($(this));
        }
        if (value === '') {
            $(this).val('N/A');
        }
        if (!isError && financial_tab_oldData != $(this).val()) {
            explore_update_financial_data_input($(this));
        }
        financial_tab_oldData = null;
    });

    /* OTHER
     ----------------------------------------------------------------------------------------*/

    $(document).on('keypress', ".financial-td-input", function (e) {
        if (e.which === 13) {
            return false;
        }
    });
});

function updateFinancialGraph(base64) {
    var the_date = $('#business-financial-table').attr('data-financial-date');
    var data = {id: $('#detail-id-innovation').val(), base64: base64, the_date: the_date};
    data['token'] = $('#hub-token').val();
    jQuery.ajax({
        url: '/api/innovation/financial/update-graph-picture',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response, statut) {
            need_to_update_financial_graph_picture = false;
            if (response.hasOwnProperty('full_data')) {
                updateFromCollection(response['full_data']['id'], response['full_data']);
            }
        },
        error: function (resultat, statut, e) {
            need_to_update_financial_graph_picture = false;
            ajax_on_error(resultat, statut, e);
            if (e != 'abort') {
                console.log(e);
            }
        }
    });
}

/**
 * init_information_open_date_in_financial_tab
 */
function init_information_open_date_in_financial_tab() {
    if(!check_if_is_data_capture_toast_enabled()){
        return;
    }
    if ($('#first-content-financial').length > 0) {
        var before_seizure_date_libelle = $('#main').attr('data-before-seazure-date-libelle');
        var during_seizure_date_libelle = $('#main').attr('data-during-seazure-date-libelle');
        var start_date = $('#main').attr('data-open-date');
        var cookieId = '';
        var theme = 'notice';
        var content = '';
        var id = jQuery('#detail-id-innovation').val();
        if (check_if_is_open_date()) {
            cookieId = 'detail-business-figure-opened-' + id;
            theme = 'success';
            content = '<span class="bold">'+get_libelle_current_le()+' data capture open: ' + during_seizure_date_libelle + '</span>';
        } else if (check_if_is_before_open_date()) {
            cookieId = 'detail-business-figure-next-' + id;
            theme = 'error';
            content = '<span class="bold">'+get_financial_date_libelle(start_date)+' data capture: '+before_seizure_date_libelle+'</span>';
        } else {
            cookieId = 'detail-business-figure-closed-' + id;
            theme = 'error';
            content = 'Data capture closed';
        }
        jQuery('#first-content-financial').cookieNotificator({
            id: cookieId,
            theme: theme,
            type: 'prepend',
            content: content,
        });
    }
}