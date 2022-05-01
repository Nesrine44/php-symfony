/**
 * explore_get_feedback_empty_state
 * @param id
 * @returns {string}
 */
function explore_get_feedback_empty_state(id) {
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }

    out[++o] = '<div class="background-color-fff padding-top-20 padding-bottom-40">'; // Start Background White
    out[++o] = '<div class="central-content-786">'; // Start central-content-786
    out[++o] = '<div id="feedback-info-cta"></div>';
    out[++o] = '<div class="contextual-headline">';
    out[++o] = innovation.title;
    out[++o] = '</div>';
    out[++o] = '<div class="headline padding-top-5 font-montserrat-700">Ask for feedback to boost your innovation</div>';
    out[++o] = '<p class="font-work-sans-300 color-000 padding-top-20 font-size-15">Feedback is a great & inexpensive way to sharpen your idea\'s value. It\'s also a good indicator of early stage traction. <br/><br/>We encourage you to invite feedback early and often. Different people bring different perspectives. Different moments along the journey bring unique learnings. So don\'t hesitate: make a new feedback friend every day!</p>';
    out[++o] = '<ul class="feedback-intro-list font-work-sans-300 font-size-15 margin-top-40">'; // Start feedback-intro-list
    out[++o] = '<li>You choose who you want to invite as a feedbacker.</li>';
    out[++o] = "<li>Feedbackers will receive a link to your innovation's overview page, but will NOT have access to the other sections of your project (financials, edit, activities…), nor to the other feedbacks you receive.</li>";
    out[++o] = "<li>You can remove a feedbacker's access at any time.</li>";
    out[++o] = '<li>All the feedbacks you have received will be available to you and your project team at any time in the feedback section.</li>';
    out[++o] = '</ul>'; // End feedback-intro-list
    out[++o] = '<div class="text-align-center margin-top-30"><a class="hub-button action-open-popup" href="#" data-popup-target=".element-feedback-request-popup">Ask for feedback</a></div>';

    out[++o] = '</div>'; // End central-content-786
    out[++o] = '</div>'; // End Background f2f2f2

    return out.join('');
}

/**
 * explore_get_feedback_list
 * @param id
 * @param response
 * @returns {string}
 */
function explore_get_feedback_list(id, response) {
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }

    out[++o] = '<div class="background-color-fff padding-vertical-40">'; // Start Background fff
    out[++o] = '<div class="central-content-994 margin-bottom-40">'; // Start central-content-994
    out[++o] = '<div id="feedback-info-cta"></div>';
    out[++o] = '<div class="contextual-headline">' + innovation.title + '</div>';
    out[++o] = '<div class="display-block">';
    out[++o] = '<div class="display-inline-block font-work-sans-700 font-size-30">';
    out[++o] = 'Feedbackers ';
    out[++o] = '<span class="font-work-sans-400 color-d2d2d2">(' + response["total_feedbackers"] + ')</span>';
    out[++o] = '</div>';
    out[++o] = '<a class="hub-button action-open-popup" href="#" data-popup-target=".element-feedback-request-popup" style="float:right;">Ask for feedback</a>';
    out[++o] = '</div>';
    out[++o] = '<div id="feedbackers-list">';
    for (var i = 0; i < response.feedbackers.length; i++) {
        out[++o] = get_feedbacker_html_component(response.feedbackers[i]);
    }
    out[++o] = '</div>';
    out[++o] = '<div class="feedbackers-loader margin-top-20 display-none">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button grey margin-top-20 load-more-feedbackers display-none">Load more</button>';
    out[++o] = '<div class="margin-top-30 position-relative width-687">';
    if (response["total_feedbacks"] > 0) {
        out[++o] = '<div class="display-inline-block font-work-sans-700 font-size-30 margin-top-30">';
        out[++o] = 'Feedbacks <span class="font-work-sans-400 color-d2d2d2">(' + response["total_feedbacks"] + ')</span>';
        out[++o] = '</div>';
        /*
         out[++o] = '<div class="position-absolute-top-right margin-top-40">'; // Start position-absolute-top-right
         out[++o] = '<div class="content-sort-by">'; // Start content-sort-by
         out[++o] = 'Sort by: <span class="active-sort cursor-default">Date</span>';
         out[++o] = '</div>'; // End content-sort-by
         out[++o] = '</div>'; // End position-absolute-top-right
         */
        out[++o] = '</div>';
        out[++o] = '<div id="feedbacks-list">';
        for (var i = 0; i < response.feedbacks.length; i++) {
            out[++o] = get_feedback_html_component(response.feedbacks[i]);
        }
        out[++o] = '</div>';
        out[++o] = '<div class="feedbacks-loader margin-top-20 display-none">';
        out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
        out[++o] = '</div>';
        out[++o] = '<button class="hub-button grey margin-top-20 load-more-feedbacks display-none">Load more</button>';
    }
    out[++o] = '</div>'; // End central-content-786
    out[++o] = '</div>'; // End Background fff

    return out.join('');
}

/**
 * get_feedbacker_html_component
 * @param feedbackInv
 * @returns {string}
 */
function get_feedbacker_html_component(feedbackInv) {
    var out = [], o = -1;
    var default_background = '/images/default/user.png';
    out[++o] = feedbackInv['status'] === 'REMOVED' ? '<div class="c-feedbacker disabled">' : '<div class="c-feedbacker" data-target="' + feedbackInv['id'] + '">';
    out[++o] = '<div class="content">'; // Start content
    out[++o] = '<div class="picture loading-bg background-color-f2f2f2" data-bg="' + feedbackInv.user['picture'] + '" data-default-bg="' + default_background + '" ></div>';
    out[++o] = '<a href="' + feedbackInv.user['url'] + '" class="link-profile display-block font-montserrat-700 color-000 transition color-on-hover-fcb400 text-decoration-none font-size-15 margin-top-10">' + feedbackInv.user.username + '</a>';
    out[++o] = '<div class="role font-work-sans-400 color-9b9b9b font-size-13">' + explore_get_feedbacker_role_for_feedback_or_invitation(feedbackInv.role, feedbackInv.user) + '</div>';
    out[++o] = '</div>'; // End content
    out[++o] = '<div class="margin-top-10">'; // Start margin-top-10
    if (feedbackInv['status'] == 'REMOVED') {
        out[++o] = '<div class="font-work-sans-500 color-000 font-size-13 line-height-26">Access removed</div>';
    } else {
        var id = $('#detail-id-innovation').val();
        var innovation = getInnovationById(id, true);
        if (innovation && in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment'])) {
            out[++o] = '<div class="element-on-hover">'; // Start element-on-hover
            out[++o] = '<div class="before-hover font-work-sans-500 color-000 font-size-13">';
            out[++o] = '<div class="font-work-sans-500 color-000 font-size-13 line-height-26">';
            out[++o] = (feedbackInv['status'] == 'PENDING') ? 'Pending' : '&nbsp;';
            out[++o] = '</div>';
            out[++o] = '</div>';
            out[++o] = '<div class="after-hover">';
            out[++o] = '<button class="hub-button height-26 action-remove-feedback-invitation-access">Remove</button>';
            out[++o] = '</div>';
            out[++o] = '</div>'; // End element-on-hover
        }else {
            out[++o] = '<div class="font-work-sans-500 color-000 font-size-13 line-height-26">';
            out[++o] = (feedbackInv['status'] == 'PENDING') ? 'Pending' : '&nbsp;';
            out[++o] = '</div>';
        }
    }
    out[++o] = '</div>'; // End margin-top-10
    out[++o] = '</div>';
    return out.join('');
}


/**
 * explore_get_feedbacker_role_for_feedback
 * @param feedback
 * @returns {string}
 */
function explore_get_feedbacker_role_for_feedback_or_invitation(role, user) {
    var feedbacker_role = '';
    if (role) {
        feedbacker_role += role;
    }
    if (feedbacker_role && user['situation_and_entity']) {
        feedbacker_role += ' • ';
    }
    feedbacker_role += user['situation_and_entity'];
    return feedbacker_role;
}

/**
 * get_feedback_html_component
 * @param feedbackInv
 * @returns {string}
 */
function get_feedback_html_component(feedbackInv) {
    var out = [], o = -1;
    var default_background = '/images/default/user.png';
    out[++o] = '<div class="c-feedback margin-top-40">'; // Start c-feedback
    out[++o] = '<div class="header">'; // Start header
    out[++o] = '<div class="picture loading-bg background-color-f2f2f2" data-bg="' + feedbackInv.invitation.user['picture'] + '" data-default-bg="' + default_background + '" ></div>';
    out[++o] = '<div class="infos">'; // Start infos
    out[++o] = '<a href="' + feedbackInv.invitation.user['url'] + '" class="link-profile display-block font-montserrat-700 color-000 transition color-on-hover-fcb400 text-decoration-none font-size-15 line-height-1">' + feedbackInv.invitation.user.username + '</a>';
    out[++o] = '<div class="font-work-sans-400 color-9b9b9b font-size-13 line-height-1 padding-top-2">' + explore_get_feedbacker_role_for_feedback_or_invitation(feedbackInv.role, feedbackInv.invitation.user) + '</div>';
    out[++o] = '</div>'; // End infos
    out[++o] = '<div class="position-absolute-top-right text-align-right">'; // Start position-absolute-top-right
    out[++o] = '<div class="content-rate">'; // Start content-rate
    for (var i = 0; i < 5; i++) {
        out[++o] = '<div class="star ' + ((i < feedbackInv.rating) ? "" : "empty") + '"></div>'; // Start content-rate
    }
    out[++o] = '</div>'; // End content-rate
    out[++o] = '<div class="margin-top-5 font-work-sans-400 font-size-13 line-height-1 color-9b9b9b">' + feedbackInv["created_at"] + '</div>';
    out[++o] = '</div>'; // End position-absolute-top-right
    out[++o] = '</div>'; // End header
    out[++o] = '<div class="font-work-sans-400 font-size-15 color-000 nl2br">' + feedbackInv.message + '</div>';
    out[++o] = '</div>'; // End c-feedback
    return out.join('');
}

/**
 * explore_generate_feedback_tab
 * @param id
 */
function explore_generate_feedback_tab(id) {
    var $page = $('#feedback');
    $.ajax({
        url: '/api/feedback/explore/infos',
        type: 'POST',
        data: {innovation_id: id},
        dataType: 'json',
        success: function (response, status) {
            if(response['status'] == 'error'){
                $page.html(explore_get_feedback_empty_state(id));
                add_flash_message('error', response['message']);
            }else {
                if (!response.feedbackers || !response.feedbackers.length) {
                    $page.html(explore_get_feedback_empty_state(id));
                } else {
                    feedback_tab_info = {
                        'total_feedbackers': response.total_feedbackers,
                        'total_feedbacks': response.total_feedbacks,
                        'offset_feedbackers': response.feedbackers.length,
                        'offset_feedbacks': response.feedbacks.length
                    };
                    $page.html(explore_get_feedback_list(id, response));
                    if (response.feedbackers.length < feedback_tab_info['total_feedbackers']) {
                        $('.load-more-feedbackers').removeClass('display-none');
                    }
                    if (response.feedbacks.length < feedback_tab_info['total_feedbacks']) {
                        $('.load-more-feedbacks').removeClass('display-none');
                    }
                }
                explore_init_feedback_list(id);
            }
        },
        error: function (response, status, e) {
            ajax_on_error(response, status, e);
        }
    });
}

/**
 * feedbacker_remove_access.
 * @param $element
 */
function feedbacker_remove_access($element) {
    $element.addClass('disabled');
    var $old_on_hover = $element.find('.element-on-hover').html();
    $element.find('.element-on-hover').replaceWith('<div class="font-work-sans-500 color-000 font-size-13 line-height-26">Access removed</div>');
    var id = $element.attr('data-target');
    var data = {invitation_id: id};
    data['token'] = $('#hub-token').val();
    $.ajax({
        url: '/api/feedback/remove-access',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (response, status) {
            if(response['status'] == 'error'){
                add_flash_message('error', response['message']);
                $element.removeClass('disabled');
                $element.find('.element-on-hover').replaceWith($old_on_hover);
            }
        },
        error: function (response, status, e) {
            ajax_on_error(response, status, e);
        }
    });
}
/**
 * explore_init_feedback_list
 * @param id
 */
function explore_init_feedback_list(id) {
    var innovation = getInnovationById(id, true);
    load_background_images();
    if (in_array(innovation['current_stage'], ['discover', 'ideate', 'experiment'])) { // is early stage
        // Initialize feedback invitation tooltip
        var content = ''.concat(
            '<div class="icon-private-toast"></div>',
            '<span class="vertical-align-middle">',
            '<span class="bold display-block" style="margin-bottom:5px;">',
            'Your innovation is in Early Stage',
            '</span>',
            'Only members of your project team and people you specifically invited can see it.',
            '</span>'
        );

        $('#feedback-info-cta').cookieNotificator({
            id: 'feedback-info-cta-toast',
            theme: 'error',
            type: 'html',
            cookieCheck: false,
            deleteButton: false,
            content: content,
            customStyle: {
                'margin': '0 auto 30px',
                'max-width': '1200px'
            }
        });
    }

    $('.load-more-feedbackers').click(function () {
        $(this).addClass('display-none');
        $('.feedbackers-loader').removeClass('display-none');
        var id = $('#detail-id-innovation').val();
        var data =  {offset: 0, limit: 6, innovation_id: id};
        $.ajax({
            url: '/api/feedback/feedbackers/load-more',
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response, status) {
                $('.feedbackers-loader').addClass('display-none');
                if (response.hasOwnProperty('status') && response['status'] == 'error') {
                    add_flash_message('error', response['message']);
                    $('.load-more-feedbackers').removeClass('display-none');
                }else {
                    var out = [], o = -1;
                    for (var i = 0; i < response.list.length; i++) {
                        out[++o] = get_feedbacker_html_component(response.list[i]);
                    }
                    $('#feedbackers-list').append(out.join(''));
                    if ($('#feedbackers-list .c-feedbacker').length < feedback_tab_info['total_feedbackers']) {
                        $('.load-more-feedbackers').removeClass('display-none');
                    }
                    load_background_images();
                }
            },
            error: function (response, status, e) {
                ajax_on_error(response, status, e);
            }
        });
    });

    $('.load-more-feedbacks').click(function () {
        $(this).addClass('display-none');
        $('.feedbacks-loader').removeClass('display-none');
        var id = $('#detail-id-innovation').val();
        $.ajax({
            url: '/api/feedback/feedbacks/load-more',
            type: "POST",
            data: {offset: 0, limit: 20, innovation_id: id},
            dataType: 'json',
            success: function (response, status) {
                $('.feedbacks-loader').addClass('display-none');
                if (response.hasOwnProperty('status') && response['status'] == 'error') {
                    add_flash_message('error', response['message']);
                    $('.load-more-feedbacks').removeClass('display-none');
                }else {
                    var out = [], o = -1;
                    for (var i = 0; i < response.list.length; i++) {
                        out[++o] = get_feedback_html_component(response.list[i]);
                    }
                    $('#feedbacks-list').append(out.join(''));
                    if ($('#feedbacks-list .c-feedback').length < feedback_tab_info['total_feedbacks']) {
                        $('.load-more-feedbacks').removeClass('display-none');
                    }
                    load_background_images();
                }
            },
            error: function (response, status, e) {
                ajax_on_error(response, status, e);
            }
        });
    });
}

$(document).ready(function () {
    $(document).on("click", '.action-remove-feedback-invitation-access', function () {
        var $element = $(this).parent().parent().parent().parent();
        feedbacker_remove_access($element);
    });
});
