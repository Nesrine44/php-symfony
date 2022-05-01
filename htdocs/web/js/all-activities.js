(function ($) {
    $('html').addClass('background-color-f2f2f2');
    $(document).ready(function () {
        function initAllActivities() {
            var limit = parseInt($(".content-activities").attr('data-limit'));
            var the_data = {'id': 'all', 'data-type': 'html', 'limit': limit};
            if (request_load_activities != null) {
                request_load_activities.abort();
            }
            request_load_activities = $.ajax({
                url: '/api/innovation/activities/load-more',
                type: "POST",
                data: the_data,
                dataType: 'json',
                success: function (response, statut) {
                    $(".content-activities").html(response['data']);
                    init_tooltip();
                    $(".activity-loader").hide();
                    $(".content-activities").attr('data-offset', response['count']);
                    if (response['count'] == limit) {
                        $('.load-more-all-activities').removeClass('display-none');
                    } else if (response['count'] == 0) {
                        $(".content-activities").html('<p class="empty-center">No activity found</p>');
                    }
                },
                error: function (resultat, statut, e) {
                    if (!ajax_on_error(resultat, statut, e) && e != 'abort') {
                        add_flash_message('error', 'Impossible to get activities for now : ' + resultat.status + ' ' + e);
                        $(".activity-loader").hide();
                    }
                }
            });
        }

        initAllActivities();


        $('.load-more-all-activities').click(function () {
            $(".activity-loader").show();
            //console.log('load-more');
            $('.load-more-all-activities').addClass('display-none');
            var limit = parseInt($(".content-activities").attr('data-limit'));
            var offset = parseInt($(".content-activities").attr('data-offset'));
            var the_data = {'id': 'all', 'data-type': 'html', 'offset': offset, 'limit': limit};
            if (request_load_activities != null) {
                request_load_activities.abort();
            }
            request_load_activities = $.ajax({
                url: '/api/innovation/activities/load-more',
                type: "POST",
                data: the_data,
                dataType: 'json',
                success: function (response, statut) {
                    $(".content-activities").append(response['data']);
                    init_tooltip();
                    $(".activity-loader").hide();
                    $(".content-activities").attr('data-offset', (offset + response['count']));
                    if (response['count'] == limit) {
                        $('.load-more-all-activities').removeClass('display-none');
                    }
                    //console.log(response);
                },
                error: function (resultat, statut, e) {
                    if (!ajax_on_error(resultat, statut, e) && e != 'abort') {
                        add_flash_message('error', 'Impossible to get activities for now:<br> ' + resultat.status + ' ' + e);
                        $(".activity-loader").hide();
                    }
                }
            });
            return false;
        });

    });
})(jQuery);
