var cntrlIsPressed = false;
(function ($) {
    $(document).ready(function () {
        // smoothstate = $('#main').smoothState(options_smoothstate);
        init();

        /**
         * Update les données de l'utilsateur
         */
        function update_data() {
            $('#main').addClass('inner-loading');
            updating_data = true;
            old_getters = null;
            var ajaxTime = new Date().getTime();
            if (request_update_datas != null)
                request_update_datas.abort();
            request_update_datas = $.ajax({
                url: '/api/global/full-data',
                type: "POST",
                data: {},
                dataType: 'json',
                success: function (response, statut) {
                    var totalTime = new Date().getTime() - ajaxTime;
                    console.log('update_date delay => ' + (totalTime / 1000) + 'sec');
                    if ($('#main').hasClass('full-loading')) {
                        location.reload();
                    } else {
                        optimization_init(response);
                        $('#main').removeClass('inner-loading');
                        if ($('#main').hasClass('list-monitor')) {
                            monitor_load_list(false);
                        }
                        updating_data = false;
                    }
                },
                error: function (resultat, statut, e) {
                    ajax_on_error(resultat, statut, e);
                    if (resultat.statusText == 'abort' || e == 'abort') {
                        return;
                    }
                    $('#main').removeClass('inner-loading');
                }
            });
        }

        $(window).unload(function () {
            if (request_update_datas != null)
                request_update_datas.abort();
            if (request_check_to_update_datas != null)
                request_check_to_update_datas.abort();
        });

        $('body').bind('beforeunload', function () {
            if (request_update_datas != null)
                request_update_datas.abort();
            if (request_check_to_update_datas != null)
                request_check_to_update_datas.abort();
        });

        /**
         * Update les données de l'utilsateur
         */
        function check_to_update() {
            if ($('#main').hasClass('full-loading')) {
                if (checkPing) clearInterval(checkPing);
                return;
            }
            $('#main').addClass('inner-loading');
            if (request_check_to_update_datas != null)
                request_check_to_update_datas.abort();
            request_check_to_update_datas = $.ajax({
                url: '/api/global/ping',
                type: "POST",
                data: {},
                dataType: 'json',
                success: function (response, statut) {
                    if (data_ping != response['ping'] || response['need_update']) {
                        if (response['need_update']) {
                            console.log('Need update  => update_data()');
                        } else {
                            console.log('Ping ' + data_ping + " / " + response['ping'] + ' => update_data()');
                        }
                        update_data();
                    } else {
                        $('#main').removeClass('inner-loading');
                    }
                    if (response['main_classes']) {
                        main_classes = response['main_classes'];
                        update_main_classes(main_classes);
                    }
                    user_roles = response['user_roles'];
                    update_csrf_tokens(response['csrf']);
                },
                error: function (resultat, statut, e) {
                    ajax_on_error(resultat, statut, e);
                    $('#main').removeClass('inner-loading');
                }
            });
        }

        if (!updating_data) {
            setTimeout(function () {
                check_to_update();
            }, 5000);
        }
        var checkPing = setInterval(function () {
            if (!updating_data) {
                check_to_update();
            }
        }, 60 * 1000); //  60 * 1000 milsec every minute


        $(document).on('keydown', '.select2-input', function (ev) {
            var me = $(this);
            if (me.data('listening') != 1) {
                me
                    .data('listening', 1)
                    .keydown(function (ev) {
                        if ((ev.keyCode ? ev.keyCode : ev.which) == 13)
                            onKeyEnterSelect2(me, ev);
                    })
                ;
            }
        });

        function onKeyEnterSelect2(el, ev) {
            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
            //console.log(keycode, el);
        }

        /* Search bar
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', '.link-to-search-results', function (e) {
            e.stopImmediatePropagation();
            if (!$(this).prop('disabled')) {
                if (!cntrlIsPressed) {
                    var target = $(this).attr('data-target');
                    goToSearchResultsPage(target);
                    closeSearchBar();
                    return false;
                }
            }
        });

        $(document).on('click', '.save-search-action', function () {
            var title = $(this).attr('data-title');
            var url = $(this).attr('href');
            var css_class = $(this).attr('data-css-class');
            $.ajax({
                url: '/api/search-history/create',
                type: "POST",
                data: {title: title, url: url, css_class: css_class},
                dataType: 'json',
                success: function (response, statut) {
                    if(response.hasOwnProperty('search_history') && response['search_history'].length > 0){
                        user_search_history = response['search_history'];
                    }
                },
                error: function (resultat, statut, e) {

                }
            });
        });


        $(document).on('click', '.link-to-search-results-tag', function () {
            if (!$(this).prop('disabled')) {
                if (!cntrlIsPressed) {
                    var id = parseInt($(this).attr('data-id'));
                    var title = $(this).attr('data-title');
                    var tag = {
                        'title': title
                    };
                    goToSearchResultsTagsPage(tag);
                    closeSearchBar();
                    return false;
                }
            }
        });

        $(document).mouseup(function (e) {
            var container = $('.element-search-popup .element-popup');
            if (!container.is(e.target) && container.has(e.target).length === 0 && $('.element-search-popup').hasClass('opened') && !$('.element-search-popup').hasClass('opening')) {
                closeSearchBar();
            }
            var container_search_tag = $('.content-trending-tags-filter .form-element');
            if (!container_search_tag.is(e.target) && container_search_tag.has(e.target).length === 0) {
                closeSearchTagBar();
            }
        });



        /* Global
         ----------------------------------------------------------------------------------------*/

        $(document).keydown(function (event) {
            if (event.ctrlKey || event.metaKey) {
                cntrlIsPressed = true;
            }
        });

        $(document).keyup(function () {
            cntrlIsPressed = false;
        });

        $(document).on('click', '#element-main-header .button-menu-responsive', function (e) {
            e.stopImmediatePropagation();
            $('#element-main-header').toggleClass('menu-opened');
        });

        /* Explore list
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', 'a.link-list-explore', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                goToExploreListPage();
                return false;
            }
        });

        $(document).on('click', 'a.link-list-explore-href', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                goToExploreListPage($(this).attr('href'));
                return false;
            }
        });


        $(document).on('click', 'a.link_explore_detail', function () {
            if (!cntrlIsPressed) {
                var url = $(this).attr('href');
                var exploder_url = url.split('/explore/');
                var id = exploder_url[1];
                goToExploreDetailPage(id);
                return false;
            }
        });

        $(document).on('click', 'a.link-list-explore-new-business-model', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                goToExploreListPage('/content/explore/tab/new_business_models', 'new_business_models');
                return false;
            }
        });



        /* Welcome page
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', 'a.link-welcome', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                goToWelcomePage();
                return false;
            }
        });


        /* Connect page
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', 'a.link-connect', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                var link = $(this).attr('href');
                goToConnectPage(link);
                return false;
            }
        });

        /* Profile
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', 'a.link-profile', function () {
            if (!cntrlIsPressed) {
                var url = $(this).attr('href');
                var exploder_url = url.split('/user/');
                var target_user = null;
                if (exploder_url.length == 1) {
                    target_user = current_user;
                } else {
                    var id = parseInt(exploder_url[1]);
                    target_user = get_user_by_id(id);
                }
                if (!target_user) {
                    add_flash_message('error', "User not found");
                    goToWelcomePage();
                } else {
                    goToUserProfile(target_user);
                }
                return false;
            }
        });

        /* User Settings
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', 'a.link-user-settings', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                goToUserSettings(current_user);
                return false;
            }
        });

        /* Monitor page
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', 'a.link-monitor-innovations', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                gotToMonitorList();
                return false;
            }
        });

        /* List Innovation
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', 'a.link-list-innovations', function (e) {
            e.stopImmediatePropagation();
            if (!cntrlIsPressed) {
                goToManageList();
                return false;
            }
        });

        /* DETAIL INNOVATION
         ----------------------------------------------------------------------------------------*/

        // Gestion du bouton de scroll to bottom
        launchBlueArrowScroll();

        init_scroll_blue_arrow();

        $('.button-scroll-to-bottom').click(function () {
            $("html, body").animate({scrollTop: ($(window).scrollTop() + 500)}, 700);
        });


        /* CLOSE POPUP
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', '.action-close-popup', function (e) {
            e.stopImmediatePropagation();
            if (!$('html').hasClass('popup-opened') || $('.custom-content-popup.opened').hasClass('loading')) {
                return;
            }
            var close_target = $('html').attr('data-popup-target');
            close_popup(close_target);
            return false;
        });

        $(document).on('click', '.action-back-popup', function (e) {
            e.stopImmediatePropagation();
            if (!$('html').hasClass('popup-opened') || $('.custom-content-popup.opened').hasClass('loading')) {
                return;
            }
            var close_target = $('html').attr('data-popup-target');
            back_on_popup(close_target);
            return false;
        });

        $(document).keyup(function (e) {
            if (e.keyCode === 27) {
                close_or_back_popup();
            }
        });

        $(document).on('click', '.action-open-popup', function (e) {
            e.stopImmediatePropagation();
            var open_target = $(this).attr('data-popup-target');
            open_popup(open_target);
            return false;
        });

        /* AJAX EXPORT
         ----------------------------------------------------------------------------------------*/

        /**
         * Ajax export on export page
         */
        $(document).on('submit', '#export_form', function (e) {
            e.stopImmediatePropagation();
            return false;
        });

        $(document).on('click', '.async-export', function (e) {
            e.stopImmediatePropagation();
            var url = $(this).attr('data-action');
            var title = $(this).attr('data-title');
            var type = $(this).attr('data-type');
            var params_array = $(this).attr('data-params_array');
            var innovation_id = $(this).attr('data-id');
            var export_type = $(this).attr('data-export-type');
            asyncExport(url, title, type, params_array, innovation_id, export_type);
            return false;
        });

        $(document).on('click', '.todo-export', function (e) {
            e.stopImmediatePropagation();
            alert('TODO');
            return false;
        });

        $(window).on("popstate", function () {
            location.reload();
        });

        $(document).on('click', '.pri-mail-contact-action', function (e) {
            e.stopImmediatePropagation();
            var innovation_id = $(this).attr('data-id');
            //console.log('Click Action with innovation id : ' + innovation_id);
            $.ajax({
                url: '/ws/contact_mail',
                type: "POST",
                data: {id: innovation_id},
                dataType: 'json',
                success: function (response, statut) {

                },
                error: function (resultat, statut, e) {

                }
            });
        });

        $(document).on('click', '.pri-generate-activity-download-action', function (e) {
            e.stopImmediatePropagation();
            var action_id = $(this).attr('data-action_id');
            var type = $(this).attr('data-type');
            $.ajax({
                url: '/api/activity/create',
                type: "POST",
                data: {action_id: action_id, type: type},
                dataType: 'json',
                success: function (response, statut) {

                },
                error: function (resultat, statut, e) {

                }
            });
        });

        $(document).on('click', '.button-create-metrics', function (e) {
            e.stopPropagation();
            var action_id = parseInt($(this).attr('data-action_id'));
            var innovation_id = $(this).attr('data-innovation_id');
            if (typeof innovation_id == 'undefined') {
                innovation_id = null;
            }
            if (action_id) {
                create_metrics(innovation_id, action_id);
            }
        });

        /* Dashboard
         ----------------------------------------------------------------------------------------*/

        $(document).on('click', '.button-dashboard-view-all', function (e) {
            e.stopImmediatePropagation();
            var type = $(this).attr('data-type');
            dashboard_detail_init(type);
            return false;
        });

        $(document).on('click', '.load-more-dashboard-detail', function (e) {
            e.stopImmediatePropagation();
            dashboard_detail_load_more();
            return false;
        });

        /* Search Tags
         ----------------------------------------------------------------------------------------*/

        var tagSearchQueue = null;
        $(document).on("keyup", '.input-search-tag', function () {
            var searchWord = $(this).val();
            if (searchWord.length === 0) {
                $(this).parent().parent().removeClass('searching');
            } else {
                $(this).parent().parent().addClass('searching');
            }
        });

        $(document).on("click", '.clear-input-search-tag', function () {
            $(this).parent().find('.input-search-tag').val('').focus();
            $(this).parent().parent().removeClass('searching');
        });

        function closeSearchTagBar() {
            $('.content-trending-tags-filter .form-element').removeClass('searching');
            $('.content-trending-tags-filter .input-search-tag').val('');
            $('.content-trending-tags-filter .content-result-search-tag').html('');
        }




        /* Back button control
         ----------------------------------------------------------------------------------------*/
        /*
        window.addEventListener("popstate", function(e) { // if a back or forward button is clicked
            if (window.history.pushState) {
                e.stopImmediatePropagation();
                window.stop();
                if(e.state) {
                    var target_url = e.state.href;
                    console.log('popstate.target_url', target_url);
                    console.log('previous_url', previous_url);
                    if (target_url === previous_url) {
                        console.log('IS BACK');
                        is_popstate_back = true;
                    } else {
                        console.log('IS FORWARD');
                    }
                    goToByUrl(target_url);
                }
                e.preventDefault();
                return false;
            }
        }, false);
        */


    });
})(jQuery);

