/* Tab Team
 ----------------------------------------------------------------------------------------*/


/**
 * explore_get_team_tab_html_template
 * @param id
 * @returns {*}
 */
function explore_get_team_tab_html_template(id){
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    out[++o] = '<div class="central-content-994 padding-top-30 padding-bottom-30">'; // Start central-content-994
    out[++o] = '<div class="contextual-headline">';
    out[++o] = innovation['title'];
    out[++o] = '</div>';
    out[++o] = '<div class="headline padding-top-5 font-montserrat-700">Manage your team</div>';
    out[++o] = '<div class="content-tab-team margin-top-25">'; // Start content-tab-activities
    out[++o] = '<div class="margin-top-20">';
    out[++o] = '<div class="loading-spinner loading-spinner--26"></div>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End content-tab-activities
    out[++o] = '</div>'; // End central-content-994
    return out.join('');
}

/**
 * explore_detail_get_team_tab_with_data_html_template
 * @param response
 * @returns {string}
 */
function explore_detail_get_team_tab_with_data_html_template(response) {
    var out = [], o = -1;
    out[++o] = '<div class="margin-top-20">'; // Start margin-top-20
    out[++o] = '<div class="content-notification-tab-team margin-bottom-10"></div>';

    out[++o] = '<div class="header-team padding-20">'; // Start header-team
    if(check_if_user_is_hq()) {
        out[++o] = '<div class="left font-work-sans-500 color-000 font-size-14">Member</div>';
        out[++o] = '<div class="middle font-work-sans-500 color-000 font-size-14">Role<div class="helper-button" data-target="only_hq">?</div></div>';
    }
    out[++o] = '</div>'; // End header-team
    out[++o] = '<div class="content-team">'; // Start content-team
    out[++o] = response['html'];
    out[++o] = '</div>'; // End content-team
    out[++o] = '<div class="margin-top-50 position-relative">'; // Start margin-top-50
    out[++o] = '<div class="line-height-34 text-align-left max-width-700">';
    out[++o] = '<div class="line-height-1-77 font-size-13 color-b5b5b5 display-inline-block vertical-align-middle">The global innovation team and COMEX members have a viewing access to all projects in the Group.</div>';
    out[++o] = '<div class="position-absolute-top-right"><button class="hub-button action-open-popup" data-popup-target=".element-popup-new-user-innovation-right">Add a team member</button></div>';
    out[++o] = '</div>'; // End margin-top-50
    out[++o] = '</div>'; // End margin-top-20
    return out.join('');
}

/**
 * explore_init_tab_team
 * @param id
 */
function explore_init_tab_team(id) {
    var the_data = {'id': id, 'data-type': 'html'};
    if (request_load_people_rights != null) {
        request_load_people_rights.abort();
    }
    request_load_people_rights = $.ajax({
        url: '/api/innovation/get-team',
        type: "POST",
        data: the_data,
        dataType: 'json',
        success: function (response, statut) {
            $('.full-content-explore-detail .content-tab-team').html(explore_detail_get_team_tab_with_data_html_template(response));
            init_tooltip();
            load_background_images();
            explore_detail_post_init_tab_team();
        },
        error: function (resultat, statut, e) {
            if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                add_flash_message('error', 'Impossible to get innovation team users for now : ' + resultat.status + ' ' + e);
            }
        }
    });
}

/**
 * explore_detail_post_init_tab_team
 */
function explore_detail_post_init_tab_team(){
    var content = '<div class="font-montserrat-700 color-000 font-size-14 margin-bottom-5">You can manage your innovation team members’ access directly from this section.</div>';
    content += '<div class="font-work-sans-300 color-000 font-size-14">Add new members, delete old ones.<br>Don’t hesitate to invite all members of your transversal team!</div>';
    $('.content-notification-tab-team').cookieNotificator({
        id: 'explore-detail-tab-team-top-info',
        theme: 'notice',
        type: 'html',
        cookieCheck: false,
        deleteButton: false,
        content: content,
        customStyle: {
            'margin': '0 auto',
            'max-width': '994px'
        }
    });
}

/**
 * explore_detail_team_get_infos
 * @param user
 */
function explore_detail_team_get_infos(user) {
    if (user_request_qet_infos != null)
        user_request_qet_infos.abort();
    user_request_qet_infos = $.ajax({
        url: '/api/user/infos',
        type: "POST",
        data: {user_id: user['id']},
        dataType: 'json',
        success: function (response, statut) {
            $('.form-new-team-member-content-skills').html('');
            $('#main').removeClass('loading');
            explore_detail_team_display_skills(user, response);
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
 * explore_detail_team_display_skills
 *
 * @param user
 * @param team_ids
 */
function explore_detail_team_display_skills(user, response) {
    var skills = response['skills'];
    var is_current_user = (user['id'] == $('#main').attr('data-uid'));
    var out = [], o = -1;
    out[++o] = '<div class="margin-bottom-100 margin-top-40 text-align-left">';
    out[++o] = '<div class="color-b5b5b5 font-montserrat-700 font-size-13 margin-bottom-20">Innovation skills you would recommend them for</div>';
    out[++o] = '<div class="content-skills" data-user-id="'+user['id']+'">'; // Start content-skills
    out[++o] = user_get_skills_html_template(skills, is_current_user, user);
    out[++o] = '</div>'; // End content-skills
    out[++o] = '</div>';
    $('.form-new-team-member-content-skills').html(out.join(''));
    init_with_tooltip();
}




$(document).ready(function () {

    $(document).on('click', '.ui-right-action-create-new-member', function(e) {
        e.stopImmediatePropagation();
        $('.element-popup-new-user-innovation-right .choose-select-member').addClass('display-none');
        $('.element-popup-new-user-innovation-right .choose-create-member').removeClass('display-none');
        $('#new-ui-right-uid-select').select2('close');
    });

    $(document).on('click', '.explore-detail-button-remove-team-member', function (e) {
        e.stopImmediatePropagation();
        var $target = $(this);
        var id = $('#detail-id-innovation').val();
        var uid = $target.attr('data-uid');
        var the_data = {'id': id, 'uid': uid, 'action': 'delete'};
        the_data['token'] = $('#hub-token').val();
        $target.parent().addClass('loading');
        $.ajax({
            url: '/api/innovation/user-right/update',
            type: "POST",
            data: the_data,
            dataType: 'json',
            success: function (response, statut) {
                if (response['status'] == 'success') {
                    $target.parent().parent().remove();
                } else {
                    add_flash_message('error', response['message']);
                    $target.parent().removeClass('loading');
                }
            },
            error: function (resultat, statut, e) {
                if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                    add_flash_message('error', 'Impossible to update innovation people rights for now : ' + resultat.status + ' ' + e);
                    $target.parent().removeClass('loading');
                }
            }
        });
        return false;
    });

    $('#form-new-user-innovation-right').submit(function () {
        addNewUIRMember();
        return false;
    });

    $(document).on('click', '.add-new-user-innovation-right', function (e) {
        e.stopImmediatePropagation();
        addNewUIRMember();
    });

    function addNewUIRMember() {
        if ($('.custom-content-popup.opened').hasClass('loading')) {
            return false;
        }
        $('.element-popup-new-user-innovation-right .form-element').removeClass('error');
        $('.element-popup-new-user-innovation-right .form-element-error').remove();
        var data = {};
        data['token'] = $('#hub-token').val();
        data['action'] = 'add';
        data['id'] = $('#detail-id-innovation').val();
        var hasError = false;
        var role = $('#new-ui-role-select').val();
        data['role'] = role;
        if (!check_if_user_is_hq()) {
            role = 'Other';
        }
        if (!role || role == '') {
            $('#new-ui-role-select').parent().parent().addClass('error');
            $('#new-ui-role-select').parent().parent().append('<div class="form-element-error">This field is required</div>');
            hasError = true;
        }
        var user_id = $('#new-ui-right-uid-select').val();
        var user_name = $('#new-ui-name').val();
        var user_email = $('#new-ui-email').val();
        if ($('#form-new-user-innovation-right .choose-select-member').hasClass('display-none')) {
            data['user_name'] = user_name;
            data['user_email'] = user_email;
            if (!user_name || user_name == '') {
                $('#new-ui-name').parent().parent().addClass('error');
                $('#new-ui-name').parent().parent().append('<div class="form-element-error">This field is required</div>');
                hasError = true;
            }
            if (!user_email || user_email == '') {
                $('#new-ui-email').parent().parent().addClass('error');
                $('#new-ui-email').parent().append('<div class="form-element-error">This field is required</div>');
                hasError = true;
            } else if (!is.email(user_email)) {
                $('#new-ui-email').parent().parent().addClass('error');
                $('#new-ui-email').parent().append('<div class="form-element-error">This field must be a valid email</div>');
                hasError = true;
            }
        } else {
            data['uid'] = user_id;
            if (!user_id || user_id == '') {
                $('#new-ui-right-uid-select').parent().parent().addClass('error');
                $('#new-ui-right-uid-select').parent().parent().append('<div class="form-element-error">This field is required</div>');
                hasError = true;
            }
        }
        if (!hasError) {
            enable_load_popup();
            $.ajax({
                url: '/api/innovation/user-right/update',
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response, statut) {
                    disable_load_popup();
                    if (response['status'] == 'field_error') {
                        $('#' + response['error_id']).parent().parent().addClass('error').append('<div class="form-element-error">' + response.message + '</div>');
                    } else if (response['status'] == 'error') {
                        add_flash_message('error', response['message']);
                        close_popup();
                    } else {
                        $('.full-content-explore-detail .content-tab-team .content-team').html(response['html']);
                        init_tooltip();
                        load_background_images();
                        close_popup();
                    }
                },
                error: function (resultat, statut, e) {
                    if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                        add_flash_message('error', 'Impossible to add innovation people rights for now : ' + resultat.status + ' ' + e);
                    }
                    disable_load_popup();
                    close_popup();
                }
            });
        }
    }

    $(document).on('click', '.explore-detail-update-team-member', function (e) {
        e.stopImmediatePropagation();
        var $target = $(this);
        var uid = $target.attr('data-uid');
        var role = $target.attr('data-role');

        open_popup('.element-popup-update-user-innovation-right', function () {
            if (!$('#update-ui-role-select').data('select2')) {
                $('#update-ui-role-select').select2({
                    minimumResultsForSearch: -1,
                    width: '100%',
                    placeholder: "Select a role",
                    allowClear: false,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });
            }
            $('#update-ui-role-select').val(role).trigger('change');
            $('#update-ui-uid').val(uid);
        });
        return false;
    });


    $('#form-update-user-innovation-right').submit(function () {
        updateUIRMember();
        return false;
    });

    $(document).on('click', '.update-user-innovation-right', function (e) {
        e.stopImmediatePropagation();
        updateUIRMember();
    });


    function updateUIRMember() {
        if ($('.custom-content-popup.opened').hasClass('loading')) {
            return false;
        }
        $('.element-popup-update-user-innovation-right .form-element').removeClass('error');
        $('.element-popup-update-user-innovation-right .form-element-error').remove();
        var data = {};
        data['token'] = $('#hub-token').val();
        data['action'] = 'update';
        data['id'] = $('#detail-id-innovation').val();
        var hasError = false;
        var role = $('#update-ui-role-select').val();
        data['role'] = role;
        if (!role || role == '') {
            $('#update-ui-role-select').parent().parent().addClass('error');
            $('#update-ui-role-select').parent().parent().append('<div class="form-element-error">This field is required</div>');
            hasError = true;
        }
        var user_id = $('#update-ui-uid').val();
        data['uid'] = user_id;
        if (!hasError) {
            enable_load_popup();
            $.ajax({
                url: '/api/innovation/user-right/update',
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response, statut) {
                    disable_load_popup();
                    if (response['status'] == 'field_error') {
                        $('#' + response['error_id']).parent().parent().addClass('error').append('<div class="form-element-error">' + response.message + '</div>');
                    } else if (response['status'] == 'error') {
                        add_flash_message('error', response['message']);
                        close_popup();
                    } else {
                        $('.full-content-explore-detail .content-tab-team .content-team').html(response['html']);
                        init_tooltip();
                        load_background_images();
                        close_popup();
                    }
                },
                error: function (resultat, statut, e) {
                    if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                        add_flash_message('error', 'Impossible to update innovation people rights for now : ' + resultat.status + ' ' + e);
                    }
                    disable_load_popup();
                    close_popup();
                }
            });
        }
    }
});