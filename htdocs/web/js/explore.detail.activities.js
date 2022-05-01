/* Tab Activities
 ----------------------------------------------------------------------------------------*/

function explore_get_activities_tab_html_template(id) {
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    out[++o] = '<div class="central-content-994 padding-top-30 padding-bottom-30">'; // Start central-content-994
    out[++o] = '<div class="contextual-headline">';
    out[++o] = innovation['title'];
    out[++o] = '</div>';
    out[++o] = '<div class="headline padding-top-5 font-montserrat-700">Activities</div>';
    out[++o] = '<div class="content-tab-activities margin-top-25">'; // Start content-tab-activities
    out[++o] = '<div class="margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End content-tab-activities
    out[++o] = '</div>'; // End central-content-994
    return out.join('');
}


function explore_get_activities_tab_with_data_html_template(response) {
    var out = [], o = -1;
    if (response.hasOwnProperty('promote')) {
        if (response['promote']['share']['count'] > 0) {
            out[++o] = '<div class="background-color-f2f2f2 padding-vertical-20 padding-horizontal-40 margin-bottom-40">'; // Start background-color-f2f2f2
            out[++o] = '<div class="activity-icon share"></div>';
            out[++o] = '<div class="text-align-center margin-top-10 color-000 font-size-16 font-work-sans-400">Your innovation was shared to</div>';
            var title = (response['promote']['share']['count'] > 1) ? response['promote']['share']['count'] + ' people' : response['promote']['share']['count'] + ' person';
            out[++o] = '<div class="text-align-center color-000 font-size-20 font-work-sans-600 margin-bottom-30">' + title + '</div>';
            out[++o] = '<div class="content-list">'; // Start content-list
            out[++o] = response['promote']['share']['html'];
            out[++o] = '</div>'; // End content-list
            out[++o] = '<div class="margin-top-20 hub-link cursor-pointer font-montserrat-700 font-size-16 promote-see-all" data-target="share">See all</div>';
            out[++o] = '</div>'; // End background-color-f2f2f2
        }

        var classe_column = (response['promote']['view']['count'] > 0 && response['promote']['export']['count'] > 0) ? 'column-percent-50' : '';
        var classe_inner_column = (response['promote']['view']['count'] > 0 && response['promote']['export']['count'] > 0) ? 'height-320' : '';
        out[++o] = '<div class="overflow-hidden margin-bottom-40">'; // Start overflow-hidden

        if (response['promote']['view']['count'] > 0) {
            out[++o] = '<div class="' + classe_column + '">'; // Start column-percent-50
            var classe_margin = (response['promote']['export']['count'] > 0) ? 'margin-right-20' : '';
            out[++o] = '<div class="background-color-f2f2f2 padding-vertical-20 padding-horizontal-40 ' + classe_margin + ' ' + classe_inner_column + '">'; // Start background-color-f2f2f2
            out[++o] = '<div class="activity-icon view"></div>';
            var title = (response['promote']['view']['count'] > 1) ? response['promote']['view']['count'] + ' people' : response['promote']['view']['count'] + ' person';
            out[++o] = '<div class="text-align-center color-000 font-size-20 font-work-sans-600">' + title + '</div>';
            out[++o] = '<div class="text-align-center margin-bottom-30 color-000 font-size-16 font-work-sans-400">viewed your innovation</div>';
            out[++o] = '<div class="content-list">'; // Start content-list
            out[++o] = response['promote']['view']['html'];
            out[++o] = '</div>'; // End content-list
            if (response['promote']['view']['count'] > 0) {
                out[++o] = '<div class="margin-top-20 hub-link cursor-pointer font-montserrat-700 font-size-16 promote-see-all" data-target="view">See all</div>';
            }
            out[++o] = '</div>'; // End background-color-f2f2f2
            out[++o] = '</div>'; // End column-percent-50
        }
        if (response['promote']['export']['count'] > 0) {
            out[++o] = '<div class="' + classe_column + '">'; // Start column-percent-50
            var classe_margin = (response['promote']['view']['count'] > 0) ? 'margin-left-20' : '';
            out[++o] = '<div class="background-color-f2f2f2 padding-vertical-20 padding-horizontal-40 ' + classe_margin + ' ' + classe_inner_column + '">'; // Start background-color-f2f2f2
            out[++o] = '<div class="activity-icon download"></div>';
            var title = (response['promote']['export']['count'] > 1) ? response['promote']['export']['count'] + ' people' : response['promote']['export']['count'] + ' person';
            out[++o] = '<div class="text-align-center color-000 font-size-20 font-work-sans-600">' + title + '</div>';
            out[++o] = '<div class="text-align-center margin-bottom-30 color-000 font-size-16 font-work-sans-400">downloaded your innovation PPT</div>';
            out[++o] = '<div class="content-list">'; // Start content-list
            out[++o] = response['promote']['export']['html'];
            out[++o] = '</div>'; // End content-list
            if (response['promote']['export']['count'] > 0) {
                out[++o] = '<div class="margin-top-20 hub-link cursor-pointer font-montserrat-700 font-size-16 promote-see-all" data-target="export">See all</div>';
            }
            out[++o] = '</div>'; // End background-color-f2f2f2
            out[++o] = '</div>'; // End column-percent-50
        }
        out[++o] = '</div>';  // End overflow-hidden
    }
    out[++o] = '<div class="content-activities" data-offset="' + response['count'] + '" data-limit="20">';
    if (response['count'] == 0) {
        out[++o] = '<p class="empty-center">No activity found for this innovation</p>';
    } else {
        out[++o] = response['data'];
    }
    out[++o] = '</div>';
    if (response['count'] == 20) {
        out[++o] = '<div class="explore-detail-activities-loader margin-top-20 display-none">';
        out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
        out[++o] = '</div>';
        out[++o] = '<button class="hub-button grey margin-top-20 explore-detail-load-more-activities">Load more</button>';
    }
    return out.join('');
}

function explore_init_tab_activities(id) {
    var the_data = {'id': id, 'data-type': 'html'};
    if (request_load_activities != null) {
        request_load_activities.abort();
    }
    request_load_activities = $.ajax({
        url: '/api/innovation/activities/load-more',
        type: "POST",
        data: the_data,
        dataType: 'json',
        success: function (response, statut) {
            $('.full-content-explore-detail .content-tab-activities').html(explore_get_activities_tab_with_data_html_template(response));
            tab_activities_view_all_popup_update_with_initial_datas(response);
            init_tooltip();
            load_background_images();
            explore_post_init_tab_activities();
        },
        error: function (resultat, statut, e) {
            if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                add_flash_message('error', 'Impossible to get innovation activities for now : ' + resultat.status + ' ' + e);
            }
        }
    });
}

/**
 * explore_post_init_tab_activities.
 */
function explore_post_init_tab_activities() {

    $('.explore-detail-load-more-activities').click(function () {
        $(".explore-detail-activities-loader").removeClass('display-none');
        $('.explore-detail-load-more-activities').addClass('display-none');
        var id = $('#detail-id-innovation').val();
        var offset = parseInt($(".content-activities").attr('data-offset'));
        var the_data = {'id': id, 'data-type': 'html', 'offset': offset};
        if (request_load_activities != null) {
            request_load_activities.abort();
        }
        request_load_activities = jQuery.ajax({
            url: '/api/innovation/activities/load-more',
            type: "POST",
            data: the_data,
            dataType: 'json',
            success: function (response, statut) {
                $(".full-content-explore-detail .content-tab-activities .content-activities").append(response['data']);
                init_tooltip();
                $(".explore-detail-activities-loader").addClass('display-none');
                $(".full-content-explore-detail .content-tab-activities .content-activities").attr('data-offset', (offset + response['count']));
                if (response['count'] == 20) {
                    $('.explore-detail-load-more-activities').removeClass('display-none');
                }
            },
            error: function (resultat, statut, e) {
                if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                    add_flash_message('error', 'Impossible to get innovation activities for now : ' + resultat.status + ' ' + e);
                    $(".explore-detail-activities-loader").addClass('display-none');
                    $('.explore-detail-load-more-activities').removeClass('display-none');
                }
            }
        });
        return false;
    });

    $('.promote-see-all').click(function () {
        var target = $(this).attr('data-target');
        var index = 0;
        var i = 0;
        $('.element-tab-activities-view-all-popup .tab-menu .header-tab-popup li').each(function () {
            if ($(this).attr('data-target') == target) {
                index = i;
            }
            i++;
        });
        $('.element-tab-activities-view-all-popup .tab-menu').tabs("option", "active", index);
        open_popup('.element-tab-activities-view-all-popup');
    });
}
