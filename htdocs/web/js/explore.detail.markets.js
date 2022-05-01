/* Tab Team
 ----------------------------------------------------------------------------------------*/

var explore_detail_innovationFinancialLineChart = null;

/**
 * explore_get_markets_tab_html_template
 * @param id
 * @returns {*}
 */
function explore_get_markets_tab_html_template(id){
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    var edition_mode_quali_enabled = (check_if_user_is_hq() || (user_can_edit_this_innovation(innovation)) && check_if_data_quali_capture_is_opened());
    out[++o] = '<div class="explore-detail-content-markets content-tab-markets padding-bottom-60">'; // Start content-tab-markets
    out[++o] = '<div class="central-content-994 padding-top-30 padding-bottom-30">'; // Start central-content-994
    out[++o] = '<div class="contextual-headline">';
    out[++o] = innovation['title'];
    out[++o] = '</div>';
    out[++o] = '<div class="headline padding-top-5 font-montserrat-700 margin-bottom-40">Presence in markets today</div>';
    var markets_in = (innovation["markets_in"] != undefined) ? innovation["markets_in"] : '[]';
    out[++o] = "<div class='content-market-in  not-loaded " + ((edition_mode_quali_enabled) ? "" : "disabled") + "' data-name='markets_in' data-value='" + markets_in + "' data-id='" + innovation['id'] + "'></div>";
    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>'; // End content-tab-markets
    return out.join('');
}


function explore_detail_init_markets_tab(){
    if($('.content-market-in').length > 0 && $('.content-market-in').hasClass('not-loaded')) {
        $('.content-market-in').removeClass('not-loaded').mapLister({
            identifier: 'business-market-in',
            data: [],
            //with_usa: true // USA state
        });
    }
}