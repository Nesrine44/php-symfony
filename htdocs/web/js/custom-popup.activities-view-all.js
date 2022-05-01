

function tab_activities_view_all_popup_update_with_initial_datas(response){
    if (response.hasOwnProperty('promote')) {
        var tabs = ['view', 'share', 'export'];
        for(var i = 0; i < tabs.length; i++){
            var the_tab = tabs[i];
            var count_total = response['promote'][the_tab]['count'];
            $('.element-tab-activities-view-all-popup .count-'+the_tab).html(count_total);
            var $target = $('#tab-activities-view-all-popup-'+the_tab);
            $target.find('.list-popup').attr('data-offset', 0).attr('data-limit', 20);
            if(count_total > 0){
                $target.find('.list-popup').html('');
                $target.find('.tab-activities-view-all-popup-loader').addClass('display-none');
                $target.find('.tab-activities-view-all-popup-load-more').removeClass('display-none');
            }else{
                $target.find('.list-popup').html('<p class="font-work-sans-400 color-000 font-size-15">No activity found for this innovation.</p>');
                $target.find('.tab-activities-view-all-popup-loader').addClass('display-none');
                $target.find('.tab-activities-view-all-popup-load-more').addClass('display-none');
            }
        }
    }
}


function tab_activities_view_all_popup_load_more() {
    var tab_active = $('.element-tab-activities-view-all-popup .tab-menu .header-tab-popup li.ui-tabs-active').attr('data-target');
    var $target = $('#tab-activities-view-all-popup-'+tab_active);
    if (!$target.find('.tab-activities-view-all-popup-load-more').hasClass('display-none')) {
        
        $target.find('.tab-activities-view-all-popup-loader').removeClass('display-none');
        $target.find('.tab-activities-view-all-popup-load-more').addClass('display-none');
        
        var offset = parseInt($target.find('.list-popup').attr('data-offset'));
        var limit = parseInt($target.find('.list-popup').attr('data-limit'));
        var id = $('#detail-id-innovation').val();
        var the_data = {'id': id, 'data-type': 'html', 'offset': offset, 'limit': limit, 'target': tab_active};
        if (request_get_promote_infos != null) {
            request_get_promote_infos.abort();
        }
        request_get_promote_infos = jQuery.ajax({
            url: '/api/innovation/activity/promoted',
            type: "POST",
            data: the_data,
            dataType: 'json',
            success: function (response, statut) {
                $target.find('.tab-activities-view-all-popup-loader').addClass('display-none');
                $target.find('.list-popup').append(response['html']).attr('data-offset', (offset + response['count']));
                if (response['count'] == limit) {
                    $target.find('.tab-activities-view-all-popup-load-more').removeClass('display-none');
                }
                init_tooltip();
                load_background_images();
            },
            error: function (resultat, statut, e) {
                if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                    add_flash_message('error', 'Impossible to get informations for now : ' + resultat.status + ' ' + e);
                    $target.find('.tab-activities-view-all-popup-loader').addClass('display-none');
                    $target.find('.tab-activities-view-all-popup-load-more').removeClass('display-none');
                }
            }
        });
        return false;
    }
}



$(document).ready(function () {
    $('.element-tab-activities-view-all-popup .tab-menu').tabs({
        activate: function (event, ui) {
            tab_activities_view_all_popup_load_more();
        }
    });

});