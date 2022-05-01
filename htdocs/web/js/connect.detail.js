var current_user = null;
var user_request_qet_infos = null;

/**
 * goToUserProfile
 * @param link
 * @return {boolean}
 */
function goToUserProfile(user) {
    try {
        before_opening_page();
        var link = (user['id'] == $('#main').attr('data-uid')) ? "/user" : "/user/" + user['id'];
        $('#main').addClass('loading');
        opti_update_url(link, 'user-profile');
        changePageTitle(user['text'] + ' | ' + global_wording['title']);
        update_main_classes(main_classes);
        update_menu_active('menu-li-connect');
        $('#pri-main-container-without-header').html(user_get_html_template(user));
        user_profile_init(user);
    } catch (error) {
        init_unknow_error_view(error);
    }
}

/**
 * user_get_html_template
 * @param user
 * @returns {string}
 */
function user_get_html_template(user) {
    var is_current_user = (user['id'] == $('#main').attr('data-uid'));
    var out = [], o = -1;
    out[++o] = '<div class="full-content-page-user" data-user-id="' + user['id'] + '">';
    out[++o] = '<div class="background-color-gradient-161541 padding-top-40 padding-bottom-40">';
    out[++o] = '<div class="central-content-994">';
    out[++o] = '<div class="profile-user-picture loading-bg margin-right-40 background-color-f2f2f2" data-bg="' + user['picture'] + '" data-default-bg="/images/default/user.png"></div>';
    out[++o] = '<div class="profile-infos-user">';
    out[++o] = '<div class="padding-top-10 color-fff font-montserrat-700 font-size-36">' + user['username'] + '</div>';
    out[++o] = '<div class="padding-top-10 color-b5b5b5 font-montserrat-700 font-size-16">' + user['situation_and_entity'] + '</div>';
    var role_margin_top = 94;
    if (user['is_pr_employee'] && check_if_user_is_hq()) {
        out[++o] = '<div class="padding-top-40 color-fff-0-5 font-size-13">Email: ' + user['mail'] + '</div>';
        out[++o] = '<div class="color-fff-0-5 font-size-13 content-local-password">';
        out[++o] = 'Local Password: ' + get_animated_triple_dot_html_template();
        out[++o] = '</div>';
        role_margin_top = 26;
    }
    out[++o] = '<div class="content-proper-role color-fff font-montserrat-700 font-size-20" style="margin-top: ' + role_margin_top + 'px;">' + user['proper_role'] + '</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="position-absolute-top-right z-index-2">';
    if (!is_current_user) {
        var user_innovations_ids = get_access_manage_innovations_ids_for_user_id(current_user['id']);
        if (user_innovations_ids.length > 0 && user['accept_contact']) {
            out[++o] = '<a class="hub-button action-open-popup margin-right-10" href="#" data-popup-target=".element-feedback-request-popup">Ask for feedback</a>';
        }
        //out[++o] = '<a href="mailto:' + user['mail'] + '" class="hub-button">Contact</a>';
    }
    out[++o] = '</div>';
    out[++o] = '<div class="position-absolute-bottom-right z-index-2">';
    out[++o] = '<div class="color-fff font-size-16 font-montserrat-500 content-nb-feedbacks"></div>';
    out[++o] = '<div class="color-b5b5b5 font-size-15 content-last-connected">';
    out[++o] = get_animated_triple_dot_html_template();
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '<div class="padding-top-40 padding-bottom-40">';
    out[++o] = '<div class="central-content-994 content-profile">';
    out[++o] = '<div class="user-profile-loader">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * user_profile_init
 * @param user
 */
function user_profile_init(user) {
    load_background_images();
    user_get_infos(user);
}

/**
 * user_get_infos
 * @param user
 */
function user_get_infos(user) {
    if (user_request_qet_infos != null)
        user_request_qet_infos.abort();
    user_request_qet_infos = $.ajax({
        url: '/api/user/infos',
        type: "POST",
        data: {user_id: user['id']},
        dataType: 'json',
        success: function (response, statut) {
            $('.user-profile-loader').remove();
            $('#main').removeClass('loading');
            if (response.hasOwnProperty('local_password')) {
                $('.content-local-password').html('Local Password: ' + response['local_password']);
            }
            if (response.hasOwnProperty('last_connected')) {
                $('.content-last-connected').html(response['last_connected']);
            }
            if (response.hasOwnProperty('proper_role')) {
                $('.content-proper-role').html(response['proper_role']);
            }
            if (response.hasOwnProperty('nb_feedbacks') && response['nb_feedbacks'] > 0) {
                var text = (response['nb_feedbacks'] == 1) ? response['nb_feedbacks'] + ' feedback given' : response['nb_feedbacks'] + ' feedbacks given';
                $('.content-nb-feedbacks').html(text);
            }
            user_display_bottom_infos(user, response);
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            $('.user-profile-loader').remove();
            $('#main').removeClass('inner-loading');
        }
    });
}

/**
 * user_display_bottom_infos
 *
 * @param user
 * @param team_ids
 */
function user_display_bottom_infos(user, response) {
    var team_ids = response['team_ids'];
    var skills = response['skills'];
    var is_current_user = (user['id'] == $('#main').attr('data-uid'));
    var user_owner_innovations = user_get_owner_innovations_by_user_id(user['id']);
    var user_owner_innovations_early_stage = user_get_owner_innovations_by_user_id(user['id'], true);
    var owner_nb = user_owner_innovations.length + user_owner_innovations_early_stage.length;

    var user_team_innovations = user_get_team_innovations_by_innovations_id(team_ids);
    var user_team_innovations_early_stage = user_get_team_innovations_by_innovations_id(team_ids, true);
    var team_nb = user_team_innovations.length + user_team_innovations_early_stage.length;

    var user_innovations = [];
    var user_innovation_ids = [];

    for (var i = 0; i < user_owner_innovations.length; i++) {
        if (!in_array(user_owner_innovations[i]['id'], user_innovation_ids)) {
            user_innovations.push(user_owner_innovations[i]);
            user_innovation_ids.push(user_owner_innovations[i]['id'])
        }
    }
    for (var i = 0; i < user_owner_innovations_early_stage.length; i++) {
        if (!in_array(user_owner_innovations_early_stage[i]['id'], user_innovation_ids)) {
            user_innovations.push(user_owner_innovations_early_stage[i]);
            user_innovation_ids.push(user_owner_innovations_early_stage[i]['id'])
        }
    }
    for (var i = 0; i < user_team_innovations.length; i++) {
        if (!in_array(user_team_innovations[i]['id'], user_innovation_ids)) {
            user_innovations.push(user_team_innovations[i]);
            user_innovation_ids.push(user_team_innovations[i]['id'])
        }
    }
    for (var i = 0; i < user_team_innovations_early_stage.length; i++) {
        if (!in_array(user_team_innovations_early_stage[i]['id'], user_innovation_ids)) {
            user_innovations.push(user_team_innovations_early_stage[i]);
            user_innovation_ids.push(user_team_innovations_early_stage[i]['id'])
        }
    }
    var trending_tags = user_get_trending_tags(user_innovations);

    var out = [], o = -1;

    // Trending tags
    if (trending_tags.length > 0) {
        out[++o] = '<div class="margin-bottom-60">';
        out[++o] = '<div class="color-000 font-montserrat-700 font-size-18 line-height-1-67 margin-bottom-10">Tags</div>';
        for (var i = 0; i < trending_tags.length; i++) {
            out[++o] = '<div class="trending-tag no-hover" data-target="' + trending_tags[i] + '">' + trending_tags[i] + '</div>';
        }
        out[++o] = '</div>';
    }

    // Projects
    if (user_innovations.length > 0) {
        out[++o] = '<div class="pri-tab golden">'; // start pri-tab
        out[++o] = '<div class="color-000 font-montserrat-700 font-size-30 margin-bottom-40">Projects</div>';

        out[++o] = '<ul class="pri-tab-menu">';
        var tab_is_selected = false;
        if (owner_nb > 0) {
            tab_is_selected = true;
            out[++o] = '<li class="ui-tabs-active ui-state-active"><a href="#owner">Owner <span class="font-work-sans-400">(' + owner_nb + ')</span></a></li>';
        }
        if (team_nb > 0) {
            out[++o] = '<li ' + ((tab_is_selected) ? '' : 'class="ui-tabs-active ui-state-active"') + '><a href="#team-member">Team member <span class="font-work-sans-400">(' + team_nb + ')</span></a></li>';
        }
        out[++o] = '</ul>';

        out[++o] = '<div id="owner" class="padding-top-20 content-mosaique-innovation overflow-hidden">'; // Start owner
        for (var i = 0; i < user_owner_innovations.length; i++) {
            out[++o] = explore_get_innovation_html(user_owner_innovations[i]);
        }
        if (user_owner_innovations_early_stage.length > 0) {
            out[++o] = user_get_innovation_early_stage_html(user_owner_innovations_early_stage.length);
        }
        out[++o] = '</div>'; // End owner

        out[++o] = '<div id="team-member" class="padding-top-20 content-mosaique-innovation overflow-hidden">'; // Start team-member
        for (var i = 0; i < user_team_innovations.length; i++) {
            out[++o] = explore_get_innovation_html(user_team_innovations[i]);
        }
        if (user_team_innovations_early_stage.length > 0) {
            out[++o] = user_get_innovation_early_stage_html(user_team_innovations_early_stage.length);
        }
        out[++o] = '</div>'; // End team-member

        out[++o] = '</div>'; // End pri-tab
    }
    out[++o] = '<div class="margin-bottom-100 margin-top-20">';
    out[++o] = '<div class="color-000 font-montserrat-700 font-size-30 margin-bottom-40">Skills</div>';

    out[++o] = '<div class="content-skills" data-user-id="'+user['id']+'">'; // Start content-skills
    out[++o] = user_get_skills_html_template(skills, is_current_user, user);
    out[++o] = '</div>'; // End content-skills

    out[++o] = '</div>';

    $('.content-profile').html(out.join(''));

    $('.pri-tab').tabs();
    init_with_tooltip();
}

/**
 * user_skill_empty_state.
 *
 * @param user
 * @returns {string}
 */
function user_skill_empty_state(user) {
    user = (typeof user !== "undefined") ? user : null;
    var user_id = (user) ? user['id'] : $('.content-skills').attr('data-user-id');
    var is_current_user = (user_id == $('#main').attr('data-uid'));
    if (!user && user_id) {
        user = get_user_by_id(user_id);
    }
    var username = (user) ? user['username'] : 'this user';
    var out = [], o = -1;
    out[++o] = '<div class="background-color-f2f2f2 text-align-center padding-vertical-40 empty-state">';
    out[++o] = '<div class="font-montserrat-700 color-b5b5b5 font-size-20 line-height-1-5">No skills identified yet…</div>';
    var name = (is_current_user) ? 'you' : username;
    out[++o] = '<div class="font-work-sans-300 color-9b9b9b font-size-16 line-height-1-5 margin-bottom-20">Add skills to help people know more about ' + name + '</div>';
    out[++o] = '<a class="hub-button yellow action-add-skill">Add a skill</a>';
    out[++o] = '</div>';
    return out.join('');
}

/**
 * user_add_skill_html_template.
 *
 * @returns {string}
 */
function user_add_skill_html_template() {
    var out = [], o = -1;

    out[++o] = '<div class="user-skill new">'; // Start user-skill
    out[++o] = '<div class="button-action transition"></div>';
    out[++o] = '<div class="content-infos">';
    out[++o] = '<div class="skill-content-search-bar form-element">'; // Start skill-content-search-bar
    out[++o] = '<select class="skill-search-input"></select>';
    out[++o] = '</div>'; // End connect-content-search-bar
    out[++o] = '<div class="button action-select-skill transition"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End user-skill
    return out.join('');
}


function skill_init_search_input() {
    $.fn.select2.amd.require.config({
        config: {
            'select2/data/extended-ajax': {
                tags: true
            }
        }
    });

    $('.skill-search-input').each(function () {
        if (!$(this).data('select2')) {
            $(this).select2({
                dataAdapter: $.fn.select2.amd.require('select2/data/extended-ajax'),
                defaultResults: main_skills,
                placeholder: "New skill...",
                theme: "default select2-search-skill",
                tags: true,
                width: '100%',
                language: {
                    errorLoading: function () {
                        return "Searching..."
                    }
                },
                ajax: {
                    url: "/api/find/skills",
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    cache: true
                },
                minimumInputLength: 2,
                createTag: function (params) {
                    var term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term
                    };
                },
            });
        }
    });
}

function user_get_skills_html_template(skills, is_current_user, user) {
    var out = [], o = -1;
    if (skills.length === 0) {
        out[++o] = user_skill_empty_state(user);
    } else {
        for (var i = 0; i < skills.length; i++) {
            out[++o] = user_get_skill_html_template(skills[i], is_current_user);
        }
        out[++o] = '<a class="hub-button yellow action-add-skill margin-top-30">Add a skill</a>';
    }
    return out.join('');
}

function user_get_skill_html_template(skill, is_current_user) {
    var out = [], o = -1;
    var nb_users = skill['senders'].length;
    var nb_other = skill['senders'].length - 1;
    var css_class = "";
    var action_tooltip = 'Add an endorsement for this skill';
    var first_sender = skill['senders'][0];
    var second_line = '';
    var current_user_has_added = false;
    for (var i = 0; i < skill['senders'].length; i++) {
        if (skill['senders'][i]['id'] == current_user['id']) {
            current_user_has_added = true;
            css_class = "added";
            action_tooltip = 'Remove an endorsement for this skill';
        }
    }
    if (is_current_user) {
        css_class = 'to-remove';
        action_tooltip = 'Remove this skill';
    }
    if (nb_users === 1) {
        if (current_user_has_added) {
            second_line = "You have given an endorsement for this skill";
        } else {
            second_line = first_sender['username'] + ' has given an endorsement for this skill';
        }
    } else {
        var libelle_other = (nb_other > 1) ? 'others' : 'other';
        if (current_user_has_added) {
            second_line = 'You and ' + nb_other + ' ' + libelle_other + ' have given an endorsement for this skill';
        } else {
            second_line = first_sender['username'] + ' and ' + nb_other + ' ' + libelle_other + ' have given an endorsement for this skill';
        }
    }
    out[++o] = '<div class="user-skill ' + css_class + '" data-skill-id="' + skill['skill']['id'] + '">'; // Start user-skill
    out[++o] = '<div class="button-action transition with-tooltip" title="'+action_tooltip+'"></div>';
    out[++o] = '<div class="content-infos">';
    out[++o] = '<div class="first-line font-work-sans-400 font-size-16 color-000 margin-bottom-2"><span class="title title-skill">' + skill['skill']['title'] + '</span> • <span class="nb">' + nb_users + '</span></div>';
    out[++o] = '<div class="second-line font-work-sans-400 font-size-14 color-b5b5b5">' + second_line + '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End user-skill

    return out.join('');
}


/**
 * get_user_inno
 * @param id
 * @return {*}
 */
function user_get_owner_innovations_by_user_id(user_id, is_early_stage) {
    is_early_stage = is_early_stage || false;
    var stage_ids = (is_early_stage) ? [1, 2, 3] : [4, 5];
    var classifications = (is_early_stage) ? ['product', 'service'] : ['product'];
    return allInnovationsDC.query().filter({
        proper__classification_type__in: classifications,
        current_stage_id__in: stage_ids,
        is_frozen: false,
        contact__uid__is: user_id
    }).values();
}

/**
 * get_user_inno
 * @param id
 * @return {*}
 */
function user_get_team_innovations_by_innovations_id(innovation_ids, is_early_stage) {
    is_early_stage = is_early_stage || false;
    var stage_ids = (is_early_stage) ? [1, 2, 3] : [4, 5];
    var classifications = (is_early_stage) ? ['product', 'service'] : ['product'];
    return allInnovationsDC.query().filter({
        proper__classification_type__in: classifications,
        current_stage_id__in: stage_ids,
        is_frozen: false,
        id__in: innovation_ids
    }).values();
}


function user_get_trending_tags(innovations) {
    var innovation_collection = new DataCollection(innovations);
    var dc = new DataCollection();
    innovation_collection.query().each(function (row, index) {
        for (var i = 0; i < row['tags_array'].length; i++) {
            var a_tag = row['tags_array'][i];
            if (dc.query().filter({title: a_tag}).count() > 0) {
                var element = dc.query().filter({title: a_tag}).first();
                dc.query()
                    .filter({title: a_tag})
                    .update({nb: (element['nb'] + 1)});
            } else {
                dc.insert({
                    'title': a_tag,
                    'nb': 1
                });
            }

        }
    });
    dc = new DataCollection(dc.query().order('nb', true).limit(0, 6).values());
    var ret = [];
    dc.query().each(function (row, index) {
        ret.push(row['title']);
    });
    return ret;
}

/**
 * explore_get_innovation_html
 *
 * @param innovation
 * @returns {string}
 */
function user_get_innovation_early_stage_html(nb) {
    var out = [], o = -1;
    out[++o] = '';
    out[++o] = '<div class="mosaique-innovation no-hover">';
    out[++o] = '<div class="content">';
    out[++o] = '<div class="round-early-stage">';
    out[++o] = '<div class="text-align-center color-0097d0 font-size-50">' + nb + '</div>';
    out[++o] = '<div class="text-align-center color-000-0-4 font-size-14">Projects<br>in Early stage</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    out[++o] = '</div>';
    return out.join('');
}

function user_ajax_update_skill(data, successCallback, errorCallBack){
    data['token'] = $('#hub-token').val();
    $('#main').addClass('inner-loading');
    $.ajax({
        url: '/api/user/skill/update',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response, statut) {
            $('#main').removeClass('inner-loading');
            if(response['status'] === 'success') {
                add_saved_message();
                if (typeof successCallback != 'undefined') {
                    successCallback(response['skills']);
                }
            }else{
                add_flash_message('error', response['message'], false);
                close_popup();
                if (typeof errorCallBack != 'undefined') {
                    errorCallBack(response);
                }
            }
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            if (typeof errorCallBack != 'undefined') {
                errorCallBack(resultat);
            }
            $('#main').removeClass('inner-loading');
        }
    });
}

$(document).ready(function () {
    $(document).on('click', '.action-add-skill', function (e) {
        e.stopImmediatePropagation();
        if ($('.content-skills').find('.user-skill.new').length > 0) {
            return;
        }
        if ($('.content-skills').find('.empty-state').length > 0) {
            $('.content-skills').find('.empty-state').remove();
            $('.content-skills').append('<a class="hub-button yellow action-add-skill margin-top-30">Add a skill</a>');
        }
        $('.content-skills').find('.action-add-skill').before(user_add_skill_html_template());
        skill_init_search_input();
    });

    $(document).on('click', '.user-skill .button-action', function (e) {
        e.stopImmediatePropagation();
        var removed = false;
        var $parent = $(this).parent();
        var $self = $(this);
        if($parent.hasClass('loading')){
            return;
        }
        var data = {
            'skill_id': parseInt($parent.attr('data-skill-id')),
            'action': 'add',
            'user_id': parseInt($('.content-skills').attr('data-user-id'))
        };

        if ($parent.hasClass('new')) {
            $parent.remove();
            if ($('.content-skills').find('.user-skill').length === 0) {
                $('.content-skills').html(user_skill_empty_state());
            }
            return;
        }
        if ($parent.hasClass('to-remove')) {
            open_popup('.element-popup-delete-skill', function () {
                $('.element-popup-delete-skill .button-delete-skill').attr('data-skill-id', data['skill_id']);
                $('.element-popup-delete-skill .button-delete-skill').attr('data-user-id', data['user_id']);
            });
            return;
        }

        if ($parent.hasClass('added')) {
            var nb_users = parseInt($parent.find('span.nb').html());
            if ((nb_users - 1) <= 0) {
                $parent.remove();
                removed = true;
                data['action'] = 'remove';
            }
        }
        $parent.addClass('loading');
        $self.html(get_loading_spinner_html('loading-spinner--26 centered'));

        if (removed && $('.content-skills').find('.user-skill').length === 0) {
            $('.content-skills').html(user_skill_empty_state());
        }
        user_ajax_update_skill(data, function(skills){
            $parent.removeClass('loading');
            $self.html("");
            var is_current_user = (data['user_id'] == $('#main').attr('data-uid'));
            var user = get_user_by_id(data['user_id']);
            $('.content-skills').html(user_get_skills_html_template(skills, is_current_user, user));
            init_with_tooltip();
        },function (response) {
            $parent.removeClass('loading');
            $self.html("");
        });
    });


    $(document).on('click', '.element-popup-delete-skill .button-delete-skill', function (e) {
        e.stopImmediatePropagation();
        var $self = $(this);
        var data = {
            'skill_id': parseInt($self.attr('data-skill-id')),
            'action': 'remove-all',
            'user_id': parseInt($self.attr('data-user-id'))
        };
        enable_load_popup();
        user_ajax_update_skill(data, function(skills){
            disable_load_popup();
            close_popup();
            var is_current_user = (data['user_id'] == $('#main').attr('data-uid'));
            var user = get_user_by_id(data['user_id']);
            $('.content-skills').html(user_get_skills_html_template(skills, is_current_user, user));
            init_with_tooltip();
        },function (response) {
            disable_load_popup();
            $parent.removeClass('loading');
            $self.html("");
        });
    });

    $(document).on('click', '.user-skill .action-select-skill', function (e) {
        e.stopImmediatePropagation();
        var $parent = $(this).parent();
        var $select = $parent.find('select');
        var skill_value = $select.val();
        if(!skill_value){
            return;
        }
        var data = {
            'skill_id': skill_value,
            'action': 'add',
            'user_id': parseInt($('.content-skills').attr('data-user-id'))
        };

        var $user_skill = $parent.parent();
        var $button_action = $user_skill.find('.button-action');
        $user_skill.addClass('loading');
        $select.prop("disabled", true);
        $button_action.html(get_loading_spinner_html('loading-spinner--26 centered'));

        user_ajax_update_skill(data, function(skills){
            $user_skill.removeClass('loading');
            $button_action.html("");
            $select.prop("disabled", false);
            var is_current_user = (data['user_id'] == $('#main').attr('data-uid'));
            var user = get_user_by_id(data['user_id']);
            $('.content-skills').html(user_get_skills_html_template(skills, is_current_user, user));
            init_with_tooltip();
        }, function (response) {
            $user_skill.removeClass('loading');
            $button_action.html("");
            $select.prop("disabled", false);
        });
    });

});