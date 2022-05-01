var currentMousePos = {x: -1, y: -1};
$(document).mousemove(function (event) {
    currentMousePos.x = event.pageX;
    currentMousePos.y = event.pageY;
});

/**
 * get_content_helper_button()
 * @param target
 * @returns {*}
 */
function get_content_helper_button(target) {
    var data = null;
    var financial_date = get_financial_date();
    var all_consumer_opportunities = get_all_consurmer_opportunities_keys();
    if (target == 'only_hq') {
        data = "<div class='pri-content-tooltip'><div class='pri-arrow-popup-left'></div><div class='pri-body-tooltip'>This field is only visible by HQ</div></div></div>";
    } else if (target == 'only_editable_hq') {
        data = "<div class='pri-content-tooltip'><div class='pri-arrow-popup-left'></div><div class='pri-body-tooltip'>This field is only editable by HQ</div></div></div>";
    } else if (target == 'canvas_text_helper') {
        data = "<div class='pri-content-tooltip'><div class='pri-arrow-popup-left'></div><div class='pri-body-tooltip'>Think about it as an elevator pitch! Being capable of expressing this block in a short format will enable you to transmit it more easily to the different project's stakeholders.</div></div></div>";
    } else if (target == 'full_time_employees') { // TODO Good wording
        data = "<div class='pri-content-tooltip'><div class='pri-arrow-popup-left'></div><div class='pri-body-tooltip'>TODO good wording for full time employees</div></div></div>";
    } else if (target == 'external_text') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">External support</div>';
        data += "Any external resources working indirectly on the project to accelerate the new business development or bring expertise you do not have yet internally?";
        data += "</div></div></div>";
    } else if (target == 'local_password') {
        data = "<div class='pri-content-tooltip'><div class='pri-arrow-popup-left'></div><div class='pri-body-tooltip'><div class='title'>Local Password</div>This field is only visible by HQ (administrators), If you aren't an administrator please contact us using contact link in header..</div><div class='button-close-tooltip'></div></div></div>";
    } else if (target == 'big_bet') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Big Bet</div>Priority portfolio designed for expansion, acceleration and sustained investment with an ambition for each innovation to reach > 400k 9Lcs or >50M€ NS.<br>Selection validated by Comex and regions.</div></div></div>";
    } else if (target == 'top_contributor') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Top Contributor</div>10 innovations with highest cumulative CAAP since A15</div></div></div>";
    } else if (target == 'cumul_ap' || target == 'cumul_total_ap') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>Cumulative Total A&P (Central invest  + A&P) since A15</div></div></div>";
    } else if (target == 'cumul_caap') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>Cumulative CAAP since A15</div></div></div>";
    } else if (target == 'value_creation') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>Net sales evolution : N - (N-1)</div></div></div>";
    } else if (target == 'growth_dynamism') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>Volumes evolution : N - (N-1)</div></div></div>";
    } else if (target == 'level_of_investment') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>A&P / Net sales N</div></div></div>";
    } else if (target == 'level_of_profitability') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>Contributive Margin / Net Sales N</div></div></div>";
    } else if (target == 'market_introduction_date') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>When did you officially launch your innovation in market? (if you haven’t launched it yet, when do you plan to launch it?)</div></div></div>";
    }
    if (target == 'total_ap') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>Consolidation of A&P and central investment: all investments from brand owner and markets on the innovation, apart from CAPEX</div></div></div>";
    } else if (target == 'total_caap') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>Contribution after A&P = Contributive margin - Total A&P</div></div></div>";
    } else if (target == 'fast_growth') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Fast Growth</div>";
        data += "2/4 years investment model";
        data += "<br><br>";
        data += "Endorsed by a mother brand with strong equity<br>";
        data += "Success in 1 market first<br>";
        data += "Window of 2-4 years to rollout globally<br>";
        data += "ROI: reaching >200Kcs rapidly after launch";
        data += "</div></div></div>";
    } else if (target == 'slow_build') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Slow Build</div>";
        data += "4/8 years investment model";
        data += "<br><br>";
        data += "Typically New-To-World brand or stretch from the mother brand<br>";
        data += "Brand building over a sustained period > 5 years<br>";
        data += "Acceleration once inflexion point is reached due to strong advocacy<br>";
        data += "ROI: mid/long-term payback after inflexion point";
        data += "</div></div></div>";
    } else if (target == 'big_bet') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Big Bets " + get_financial_date_FY(financial_date) + "</div>Priority portfolio designed for expansion, acceleration and sustained investment with an ambition for each innovation to reach > 400k 9Lcs or >50M€ NS.<br>Selection validated by Comex and regions.</div></div></div>";
    } else if (target == 'top_contributor') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Top Contributors " + get_financial_date_libelle(financial_date) + "</div>10 innovations with highest cumulative CAAP since A15</div></div></div>";
    } else if (target == 'discover') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Discover</div>";
        data += "Mapping relevant opportunities for " + global_wording['company'];
        data += "<br><br>";
        data += "You are in Discover if:<br>You are deep diving into innovations opportunities, searching and collecting consumers insights.";
        data += "</div>";
    } else if (target == 'ideate') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Ideate</div>";
        data += "Crafting ideas & exploring sourcing options";
        data += "<br><br>";
        data += "You are in Ideate if:<br>You are still at a concept phase, having value hypothesis and areas of uncertainty on your starter idea.";
        data += "</div>";
    } else if (target == 'frozen') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Frozen</div>";
        data += "A frozen project is considered on pause.";
        data += "</div>";
    } else if (target == 'experiment') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Experiment</div>";
        data += "Testing value with consumers at an early stage in a real environment";
        data += "<br><br>";
        data += "You are in Experiment if:<br>You have put in market an experimental edition (batch limited in time) , with the ability to iterate it thanks to learnings from consumers (NO CAPEX)";
        data += "</div>";
    } else if (target == 'incubate') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Incubate</div>";
        data += "Learning about business potential in a regional pilot market";
        data += "<br><br>";
        data += "You are in Incubate if:<br>";
        data += "You have invested in CAPEX and cannot easily iterate the product anymore, and you need to search for the best RTM and the optimum Marketing mix";
        data += "</div></div></div>";
    } else if (target == 'scale_up') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Scale up</div>";
        data += "Phasing the investment over a minimum of 3 years to achieve growth ambition";
        data += "<br><br>";
        data += "You are in Scale-Up if:<br>";
        data += "Your packaging, marketing mix, RTM is now final";
        data += "</div></div></div>";
    } else if (target == 'default_coop') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += "Consumer Opportunity";
        data += "</div></div>";
    } else if (target == 'default_moc') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += "Moment of Convivialité";
        data += "</div></div>";
    } else if (target == 'overview-business_drivers') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += "Business Driver";
        data += "</div></div>";
    } else if (target == 'default_category') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += "Category";
        data += "</div></div>";
    } else if (target == 'default_abv') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += "Alcohol by volume";
        data += "</div></div>";
    } else if (target == 'volume_le') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += "Volume in " + get_financial_date_libelle(get_financial_date());
        data += "</div></div></div>";
    } else if (in_array(target, all_consumer_opportunities)) {
        var infos = get_detail_consumer_opportunity_by_key(target);
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>" + infos['title'] + "</div>";
        var line = infos['block-left-1'];
        if (line) {
            line.replace('<br>', '');
            data += line;
        }
        line = infos['block-left-2'];
        if (line) {
            line.replace('<br>', '');
            data += "<br>";
            data += line;
        }
        data += "</div></div></div>";
    } else if (target == 'tactical_innovation') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>TACTICAL INNOVATION</div>";
        data += "Projects based only on business insights, with no consumer opportunity behind";
        data += "</div></div>";
    } else if (target == "negative_caap") {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'><div class='pri-header-tooltip'>Negative CAAP</div>";
        data += "</div></div>";
    } else if (target === 'canvas_block_1_a') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">1A</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Consumer benefit</div>';
        data += "Any people's unmet need, pain point, frustration...? Putting yourself in the consumer shoes";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_1_b') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">1B</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Source of Business</div>';
        data += "What’s the business potential, size of the price? Instead of what people will buy your offer?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_1_c') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">1C</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Unique Value proposition</div>';
        data += "A clear compelling message that states why it is worth buying? What value do you deliver to consumers?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_2_a') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">2A</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Early Adopters</div>';
        data += "For whom do you create value? People sharing the same values with the strongest pain point?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_2_b') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">2B</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Usage & Occasion</div>';
        data += "When and how the value proposition works best? Do you unlock any new usage?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_2_c') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">2C</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Route to consumers/channels</div>';
        data += "What’s the most adapted Route to Market to reach early adopters? Which ones work bests?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_3_a') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">3A</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Value Chain Expertise</div>';
        data += "Out of all the activities to transform an idea into revenue: procurement, production, marketing, commercial, distribution, consumer experience, after sales... What could be done with the support from the core business? What require new external expertise?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_3_b') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">3B</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">External partnerships</div>';
        data += "What collaborations will help you to grab expertise you do not have yet to accelerate the development of the new business?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_3_c') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">3C</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Key activities & metrics</div>';
        data += "What’s the most adapted Route to Market to reach early adopters? Which ones work bests?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_4_a') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">4A</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">New Revenue streams & pricing</div>';
        data += "What’s the new Business revenue model? For what value are your early adopter willing to pay for? How would they prefer to pay?";
        data += "</div></div></div>";
    } else if (target === 'canvas_block_4_b') {
        data = "<div class='pri-content-tooltip'><div class='pri-body-tooltip'>";
        data += '<div class="font-montserrat-700 color-fff-0-6 font-size-16">4B</div>';
        data += '<div class="font-montserrat-700 color-fff font-size-16 padding-bottom-10">Burn rate & Costs structure</div>';
        data += "What’s the funding till inflexion point, the investment model? What’s the most important costs inherent to your business model? Resources, spending...";
        data += "</div></div></div>";
    }

    return data;
}

var current_choice = null;
var customTooltipsStage = function (tooltip) {
    // Tooltip Element
    var tooltipEl = $('#tooltip-stage');
    if (!tooltipEl[0]) {
        $('body').append('<div id="tooltip-stage" class="tooltipster-default tooltip-consolidation-donut"></div>');
        tooltipEl = $('#tooltip-stage');
    }
    if (!tooltip.opacity) {
        tooltipEl.remove();
        $('.chartjs-wrap canvas')
            .each(function (index, el) {
                $(el).css('cursor', 'default');
            });
        return;
    }
    $(this._chart.canvas).css('cursor', 'pointer');
    if (tooltip.body) {
        var text = tooltip.body[0].lines[0];
        var splitter = text.split(': ');
        current_choice = splitter[0];
        if (current_choice != 'empty') {
            var target = splitter[0];
            tooltipEl.removeClass('discover ideate experiment incubate scale_up discontinued permanent_range frozen other fast_growth slow_build out_of_funnel').addClass(target);
            var nb = parseInt(splitter[1]);
            var type_stage = 'Other';
            var under_type_stage = 'Stage';
            if (target == 'discover') {
                type_stage = 'Discover';
                under_type_stage = 'Early stage';
            } else if (target == 'ideate') {
                type_stage = 'Ideate';
                under_type_stage = 'Early stage';
            } else if (target == 'experiment') {
                type_stage = 'Experiment';
                under_type_stage = 'Acceleration';
            } else if (target == 'incubate') {
                type_stage = 'Incubate';
                under_type_stage = 'Acceleration';
            } else if (target == 'scale_up') {
                type_stage = 'Scale-up';
                under_type_stage = 'Scale up';
            } else if (target == 'discontinued') {
                type_stage = 'Discontinued';
                under_type_stage = '';
            } else if (target == 'permanent_range') {
                type_stage = 'Permanent range';
                under_type_stage = '';
            } else if (target == 'frozen') {
                type_stage = 'Frozen';
                under_type_stage = '';
            } else if (target == 'fast_growth') {
                type_stage = 'Fast Growth';
                under_type_stage = '2/4 years investment model';
            } else if (target == 'slow_build') {
                type_stage = 'Slow Build';
                under_type_stage = '4/8 years investment model';
            } else if (target == 'out_of_funnel') {
                type_stage = 'Out of funnel';
                under_type_stage = 'Permanent range, frozen, discontinued';
            }
            var top_tooltip = '<div class="title-stage"><span class="img"></span><span class="content"><span class="type-stage font-montserrat-700 text-transform-uppercase">' + type_stage + '</span><span class="under-type-stage">' + under_type_stage + '</span></span></div>';
            var content_tooltip = "";
            if (target) {
                if (target == 'discover') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-500 color-fff margin-bottom-10\">Mapping relevant opportunities for " + global_wording['company'] + "<\/span>";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">You are in Discover if:<br>You are deep diving into innovations opportunities, searching and collecting consumers insights.<\/span>";
                    content_tooltip += "</div>";
                } else if (target == 'ideate') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-500 color-fff margin-bottom-10\">Crafting ideas & exploring sourcing options<\/span>";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">You are in Ideate if:<br>You are still at a concept phase, having value hypothesis and areas of uncertainty on your starter idea.<\/span>";
                    content_tooltip += "</div>";
                } else if (target == 'experiment') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-500 color-fff margin-bottom-10\">Testing value with consumers at an early stage in a real environment<\/span>";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">You are in Experiment if:<br>You have put in market an experimental edition (batch limited in time) , with the ability to iterate it thanks to learnings from consumers (NO CAPEX).<\/span>";
                    content_tooltip += "</div>";
                } else if (target == 'incubate') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-500 color-fff margin-bottom-10\">Learning about business potential in a regional pilot market<\/span>";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">You are in Incubate if:<br>You have invested in CAPEX and cannot easily iterate the product anymore, and you need to search for the best RTM and the optimum Marketing mix.<\/span>";
                    content_tooltip += "</div>";
                } else if (target == 'scale_up') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-500 color-fff margin-bottom-10\">Phasing the investment over a minimum of 3 years to achieve growth ambition<\/span>";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">You are in Scale-Up if:<br>Your packaging, marketing mix, RTM is now final.<\/span>";
                    content_tooltip += "</div>";
                } else if (target == 'permanent_range') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">After maximum 4 years in Fast Growth and 8 years in Slow Build, your project will not be considered as an innovation anymore. It should either be stopped with a phase out plan, or enter the " + global_wording['company'] + " Permanent Range.<\/span>";
                    content_tooltip += "</div>";
                } else if (target == 'fast_growth') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">Endorsed by a mother brand with strong equity<br>Success in 1 market first<br>Window of 2-4 years to rollout globally<br>ROI: reaching >200Kcs rapidly after launch<\/span>";
                    content_tooltip += "</div>";
                } else if (target == 'slow_build') {
                    content_tooltip = "<div class=\"content-description font-size-13\">";
                    content_tooltip += "<span class=\"display-block font-work-sans-400 color-b5b5b5\">Typically New-To-World brand or stretch from the mother brand<br>Brand building over a sustained period > 5 years<br>Acceleration once inflexion point is reached due to strong advocacy<br>ROI: mid/long-term payback after inflexion point<\/span>";
                    content_tooltip += "</div>";
                }
            }
            var data = '<div class="pri-content-tooltip"><div class="body-tooltip">' + top_tooltip + content_tooltip + '</div></div>';
            tooltipEl.html(data);
        }
    }
    if (current_choice != 'empty') {
        var height = tooltipEl.height();
        var left = currentMousePos.x + 25;
        var top = currentMousePos.y - (height / 2);
        tooltipEl.css({
            opacity: 1,
            left: left + 'px',
            top: top + 'px'
        });
    }
};

var customTooltipsFinancial = function (tooltip) {
    // Tooltip Element
    var tooltipEl = $('#tooltip-financial');
    if (!tooltipEl[0]) {
        $('body').append('<div id="tooltip-financial" class="tooltipster-default pri-tooltip-consolidation"></div>');
        tooltipEl = $('#tooltip-financial');
    }
    if (!tooltip.opacity) {
        tooltipEl.remove();
        $('.chartjs-wrap canvas')
            .each(function (index, el) {
                $(el).css('cursor', 'default');
            });
        return;
    }
    $(this._chart.canvas).css('cursor', 'pointer');
    if (tooltip.body) {
        tooltipEl.removeClass('right');
        var the_index = tooltip.dataPoints[0]['index'];
        var the_dataset_index = tooltip.dataPoints[0]['datasetIndex'];
        if (the_index !== null) {
            var label = explore_detail_dataset_innovation[the_dataset_index]['data'][the_index]['label'];
            if (label) {
                var label_id = label.replace(' ', '_');
                var caap_k = explore_detail_dataset_innovation[the_dataset_index]['data'][the_index]['caap'];
                var cm_k = explore_detail_dataset_innovation[the_dataset_index]['data'][the_index]['cm'];
                var ap_k = explore_detail_dataset_innovation[the_dataset_index]['data'][the_index]['ap'];
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

/**
 * init_helper_button()
 */
function init_helper_button() {
    $('.helper-button:not(.tooltipstered)').tooltipster({
        // speed: 0,
        position: 'right',
        trigger: 'hover',
        onlyOne: true,
        //autoClose: false,
        contentAsHTML: true,
        theme: "tooltipster-default tooltip-helper helper",
        interactive: true,
        functionBefore: function (origin, content) {
            var target = $(this).attr('data-target');
            var data = get_content_helper_button(target);
            if (data) {
                origin.tooltipster('content', $(data));
                content();
            }
        }
    });
}

/**
 * init_helper_on_hover
 */
function init_helper_on_hover() {
    $('.helper-on-hover:not(.tooltipstered)').tooltipster({
        // speed: 0,
        position: 'right',
        trigger: 'hover',
        onlyOne: true,
        //autoClose: false,
        contentAsHTML: true,
        theme: "tooltipster-default pri-tooltip-tr tooltip-helper-on-hover helper",
        interactive: true,
        functionBefore: function (origin, content) {
            var target = $(this).attr('data-target');
            var data = get_content_helper_button(target);
            if (data) {
                origin.tooltipster('content', $(data));
                content();
            }
        }
    });

    $('.helper-on-hover-top:not(.tooltipstered)').tooltipster({
        // speed: 0,
        position: 'top',
        trigger: 'hover',
        onlyOne: true,
        //autoClose: false,
        contentAsHTML: true,
        theme: "tooltipster-default pri-tooltip-tr tooltip-helper-on-hover helper",
        interactive: true,
        functionBefore: function (origin, content) {
            var target = $(this).attr('data-target');
            var data = get_content_helper_button(target);
            if (data) {
                origin.tooltipster('content', $(data));
                content();
            }
        }
    });
}

/**
 * init_tooltip_ellipsis.
 */
function init_tooltip_ellipsis() {
    $('.tooltip-right-tr:not(.tooltipstered)').tooltipster({
        // speed: 0,
        position: 'right',
        theme: "tooltipster-default pri-tooltip-tr",
        functionBefore: function (origin, content) {
            if ($(this)[0].tagName == 'IMG' || $(this).hasClass('pri-pic-list-div') || $(this).hasClass('force-tooltip')) {
                content();
            } else if ($(this).parent()[0].tagName == 'A' && $(this).parent()[0].offsetWidth < $(this)[0].offsetWidth) {
                content();
            } else if ($(this)[0].offsetWidth < $(this)[0].scrollWidth) {
                content();
            }
        }
    });

    $('.tooltip-on-ellipsis').tooltipster({
        theme: "tooltipster-default",
        functionBefore: function (origin, content) {
            if ($(this).hasClass('on-parent') && $(this).parent()[0].offsetWidth < $(this)[0].offsetWidth) {
                content();
            } else if ($(this)[0].offsetWidth < $(this)[0].scrollWidth) {
                content();
            }
        }
    });
}

/**
 * init_with_tooltip
 */
function init_with_tooltip() {
    $('.with-tooltip:not(.tooltipstered)').tooltipster({
        // speed: 0,
        //autoClose: false,
        theme: "tooltipster-default"
    });
    $('.with-tooltip-left:not(.tooltipstered)').tooltipster({
        position: 'left',
        theme: "tooltipster-default"
    });

    $('.with-tooltip-user:not(.tooltipstered)').tooltipster({
        position: 'bottom',
        contentAsHTML: true,
        //autoClose: false,
        theme: "tooltipster-default tooltipster-default-white",
        interactive: true,
        functionBefore: function (origin, content) {
            var default_picture = '/images/default/user.png';
            var picture = $(this).attr('data-picture');
            var username = $(this).attr('data-username');
            var situation_and_entity = $(this).attr('data-situation-and-entity');
            var url = $(this).attr('href');

            var out = [], o = -1;
            out[++o] = '<div class="content-tooltip-with-user">';
            out[++o] = '<div class="picture loading-bg" data-bg="' + picture + '" data-default-bg="' + default_picture + '"></div>';
            out[++o] = '<div class="infos">';
            out[++o] = '<div class="name text-ellipsis line-height-1-2 font-montserrat-700"><a href="' + url + '" class="link-profile color-000 hub-link-on-hover line-height-1-2">' + username + '</a></div>';
            out[++o] = '<div class="subname text-ellipsis line-height-1-2 color-9b9b9b font-size-14 font-work-sans-400 margin-top-5">' + situation_and_entity + '</div>';
            out[++o] = '</div>';
            out[++o] = '</div>';
            var data = out.join('');
            if (data) {
                origin.tooltipster('content', $(data));
                content();
                load_background_images();
            }
        }
    });
}

/**
 * init_tooltip();
 */
function init_tooltip() {
    init_with_tooltip();
    init_tooltip_ellipsis();
    init_helper_on_hover();
    init_helper_button();
}