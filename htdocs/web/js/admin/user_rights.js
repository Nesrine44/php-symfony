
function displayLoader(){
    cleanBottom();
    var loader = '<div class="box"> <div class="box-body"><br><br><br><div class="overlay"><i class="fa fa-refresh fa-spin"></i></div></div></div>';
    $('.search-result').css({'display': 'none'});
    $('.wrong-result').css({'display': 'block'}).html(loader);
}

function displayTdLoader($td){
    $td.find('.btn-group').removeClass('open').append('<div class="overlay" style="position: absolute; top: 0;left: 0;right: 0;bottom: 0;"><i class="fa fa-refresh fa-spin"></i></div>');
}

function displayInnovationsLoader(){
    var loader = '<div class="box"> <div class="box-body"><br><br><br><div class="overlay"><i class="fa fa-refresh fa-spin"></i></div></div></div>';
    $('.search-innovation-result').css({'display': 'none'});
    $('.wrong-innovation-result').css({'display': 'block'}).html(loader);
}

function displayCenterMessage(message){
    $('.search-result').css({'display': 'none'});
    var message = '<div class="box"><div class="box-body"><div style="text-align: center;">'+message+'</div></div></div>';
    $('.wrong-result').css({'display': 'block'}).html(message);
    $('#form-search-users .btn').prop("disabled", false);
}

function displayCurrentRights(results){
    $('.wrong-result').css({'display': 'none'});
    $('.search-result').css({'display': 'block'});
    $('#form-search-users .btn').prop("disabled", false);
    $('.table-current-rights tbody').html(results);
    addICheck();
}

function checkEmptyList(){
    if($('.table-current-rights tbody tr').length == 0){
        $('.empty-list').css({'display': 'block'});
    }else{
        $('.empty-list').css({'display': 'none'});
    }
}

function displayInnovationCenterMessage(message){
    $('.search-innovation-result').css({'display': 'none'});
    var message = '<div class="box"><div class="box-body"><div style="text-align: center;">'+message+'</div></div></div>';
    $('.wrong-innovation-result').css({'display': 'block'}).html(message);
    $('#form-search-innovations .btn').prop("disabled", false);
}

function displayCurrentInnovations(results){
    $('.wrong-innovation-result').css({'display': 'none'});
    $('.search-innovation-result').css({'display': 'block'});
    $('#form-search-innovations .btn').prop("disabled", false);
    $('.table-next-rights tbody').html(results);
    addICheck();
}

function addICheck(){
    $('.checkbox-select:not(.icheckboxed)').iCheck({
        checkboxClass: 'icheckbox_square-blue',
    });
    $('.td-checkbox .icheckbox_square-blue .checkbox-select').addClass('icheckboxed');
    $('.iCheck-helper').css('position', 'relative');
    checkEmptyList();
}

function capitalizeStr(str) {
    return str.replace(/\w\S*/g, function(txt){
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

function cleanBottom(){
    $('.search-result, .wrong-innovation-result, .search-innovation-result').css({'display': 'none'});
    $('.wrong-result').css({'display': 'none'}).html('');
    $('.table-current-rights tbody').html('');
    $('.table-next-rights tbody').html('');

    $('#search-by-entity').val('all').trigger('change.select2');
    $('#search-by-title').val('');
    $('.checkbox-select-all').iCheck('uncheck');
}


function updateTd($td, right){
    $td.find('.overlay').remove();
    $td.parent().addClass('tr-updated');
    if($td.parent().hasClass('to-add')){
        addTd($td, right);
    } else {
        if (right == 'remove') {
            $td.parent().remove();
            checkEmptyList();
        } else {
            $td.find('.text-info').text(capitalizeStr(right));
            $td.find('ul .pull-right').remove();
            $td.find('.right-' + right).prepend('<span class="pull-right"><i class="fa fa-check"></i></span>');
            if(right == 'owner') {
                $td.parent().find('.td-user-role').html('');
                $td.parent().find('.td-user-right').css({'vertical-align': 'middle'}).html('Contact Owner');
            }
        }
    }
}

function updateRoleTd($td, role){
    $td.find('.overlay').remove();
    $td.parent().addClass('tr-updated');
    $td.find('.text-info').text(role);
    $td.find('ul .pull-role').remove();
    $td.find('.change-role[data-role="'+role+'"]').prepend('<span class="pull-role"><i class="fa fa-check"></i></span>');
}

function addTd($td, right){
    $td.parent().find('.icheckboxed').removeClass('icheckboxed').iCheck('destroy');
    var $tr_clone = $td.parent().clone();
    $td.parent().remove();
    $tr_clone.removeClass('to-add').addClass('to-update');
    $tr_clone.find('.checkbox-to-add').addClass('checkbox-to-update').removeClass('checkbox-to-add');
    $tr_clone.find('.td-user-right .text-info').text(capitalizeStr(right));
    $tr_clone.find('ul .pull-right').remove();
    $tr_clone.find('.td-user-role').show();
    $tr_clone.find('.right-' + right).prepend('<span class="pull-right"><i class="fa fa-check"></i></span>');
    if(right === 'owner'){
        $tr_clone.find('.td-user-role').html('');
        $tr_clone.find('.td-user-right').css({'vertical-align': 'middle'}).html('Contact Owner');
    }
    var clone_title = $tr_clone.find('.td-innovation-title').text();
    var added = false;
    $('.table-current-rights tbody tr.to-update').each(function(){
        var title = $(this).find('.td-innovation-title').text();
        if(title > clone_title){
            $(this).before($tr_clone);
            added = true;
            addICheck();
            return false;
        }
    });
    if(!added){
        $('.table-current-rights tbody').append($tr_clone);
        added = true;
        addICheck();
    }
    if($('.table-next-rights tbody tr.to-add').length == 0){
        $('.search-innovation-result').css({'display': 'none'});
    }
}

function removeUpdatedState(){
    setTimeout(function() {
        $('.tr-updated').removeClass('tr-updated');
    }, 2000);
}

function loadUser(){
    var user_id = $('#form-search-users select[name=user]').val();
    var url = $('#form-search-users').attr('action');
    displayLoader();
    $.ajax({
        url: url,
        type: "POST",
        data: {user_id: user_id},
        dataType: 'json',
        success: function (response, statut) {
            if(response['status'] == 'error'){
                displayCenterMessage(response['message']);
            }else if(response['message']){
                displayCenterMessage(response['message']);
            }else{
                displayCurrentRights(response['html']);
            }
        },
        error: function (resultat, statut, e) {
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            console.error(resultat);
        }
    });
}

function searchInnovations(){
    var user_id = $('#form-search-users select[name=user]').val();
    var entity_id = $('#search-by-entity').val();
    var search_word = $('#search-by-title').val();
    var url = $('#form-search-innovations').attr('action');
    displayInnovationsLoader();
    $.ajax({
        url: url,
        type: "POST",
        data: {user_id: user_id, entity_id: entity_id, search_word: search_word},
        dataType: 'json',
        success: function (response, statut) {
            if(response['status'] == 'error'){
                displayInnovationCenterMessage(response['message']);
            }else if(response['message']){
                displayInnovationCenterMessage(response['message']);
            }else{
                displayCurrentInnovations(response['html']);
            }
        },
        error: function (resultat, statut, e) {
            if (resultat.statusText == 'abort' || e == 'abort') {
                return;
            }
            console.error(resultat);
        }
    });
}

/* LIVE METHODS
 ----------------------------------------------------------------------------------------*/
(function ($) {
    $(document).ready(function () {
        $('#form-search-users').submit(function(){
            loadUser();
            return false;
        });

        $(document).on('click', '.change-right', function (e) {
            e.stopImmediatePropagation();
            var user_id = $('#form-search-users select[name=user]').val();
            var innovation_id = $(this).attr('data-id');
            var right = $(this).attr('data-right');
            var $td = $(this).parent().parent().parent().parent();
            displayTdLoader($td);
            var url = $('#form-search-users').attr('data-update-user-right-url');
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    user_id: user_id,
                    innovation_ids: [innovation_id],
                    right: right
                },
                dataType: 'json',
                success: function (response, statut) {
                    $td.find('.overlay').remove();
                    updateTd($td, right);
                    removeUpdatedState();
                },
                error: function (resultat, statut, e) {
                    if (resultat.statusText == 'abort' || e == 'abort') {
                        return;
                    }
                    console.error(resultat);
                }
            });
            return false;
        });

        $(document).on('click', '.change-role', function (e) {
            e.stopImmediatePropagation();
            var user_id = $('#form-search-users select[name=user]').val();
            var innovation_id = $(this).attr('data-id');
            var role = $(this).attr('data-role');
            var $td = $(this).parent().parent().parent().parent();
            displayTdLoader($td);
            var url = $('#form-search-users').attr('data-update-user-role-url');
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    user_id: user_id,
                    innovation_id: innovation_id,
                    role: role
                },
                dataType: 'json',
                success: function (response, statut) {
                    $td.find('.overlay').remove();
                    updateRoleTd($td, role);
                    removeUpdatedState();
                },
                error: function (resultat, statut, e) {
                    if (resultat.statusText == 'abort' || e == 'abort') {
                        return;
                    }
                    console.error(resultat);
                }
            });
            return false;
        });
        var toUpdateTriggeredByChild = false;
        var toAddTriggeredByChild = false;

        $('#form-search-innovations').submit(function(){
            searchInnovations();
            return false;
        });

        $('.checkbox-select-all').on('ifChecked', function (event) {
            if($(this).hasClass('to-add')){
                $('.checkbox-to-add:not(.disabled)').iCheck('check');
                toAddTriggeredByChild = false;
            }else{
                $('.checkbox-to-update:not(.disabled)').iCheck('check');
                toUpdateTriggeredByChild = false;
            }
        });

        $('.checkbox-select-all').on('ifUnchecked', function (event) {
            if($(this).hasClass('to-add')){
                if (!toAddTriggeredByChild) {
                    $('.checkbox-to-add:not(.disabled)').iCheck('uncheck');
                }
                toAddTriggeredByChild = false;
            }else{
                if (!toUpdateTriggeredByChild) {
                    $('.checkbox-to-update:not(.disabled)').iCheck('uncheck');
                }
                toUpdateTriggeredByChild = false;
            }
        });

        $(document).on('ifUnchecked', '.checkbox-select', function (event) {
            if($(this).hasClass('checkbox-to-add')){
                toAddTriggeredByChild = true;
                $('#checkbox-select-all-add').iCheck('uncheck');
            }else{
                toUpdateTriggeredByChild = true;
                $('#checkbox-select-all-update').iCheck('uncheck');
            }
        });

        $(document).on('ifChecked', '.checkbox-select', function (event) {
            if($(this).hasClass('checkbox-to-add')){
                if ($('.checkbox-to-add:not(.disabled)').filter(':checked').length == $('.checkbox-to-add:not(.disabled)').length) {
                    $('#checkbox-select-all-add').iCheck('check');
                }
            }else{
                if ($('.checkbox-to-update:not(.disabled)').filter(':checked').length == $('.checkbox-to-update:not(.disabled)').length) {
                    $('#checkbox-select-all-update').iCheck('check');
                }
            }
        });


        function updateAfterMultipleChange(type_class, right, innovation_ids){
            toUpdateTriggeredByChild = true;
            $('.'+type_class).iCheck('uncheck');
            if(type_class == 'checkbox-to-update'){
                $('#checkbox-select-all-update').iCheck('uncheck');
            }else{
                $('#checkbox-select-all-add').iCheck('uncheck');
            }
            if(right == 'remove'){
                for(var i = 0; i < innovation_ids.length; i++){
                    $('#tr-item-'+innovation_ids[i]).remove();
                }
                checkEmptyList();
            }else if(type_class === 'checkbox-to-update'){
                for(var i = 0; i < innovation_ids.length; i++){
                    var $td = $('#tr-item-'+innovation_ids[i]+' td.td-user-right');
                    $td.find('.overlay').remove();
                    updateTd($td, right);
                }
            }else if(type_class === 'checkbox-to-add'){
                for(var i = 0; i < innovation_ids.length; i++){
                    var $td = $('#tr-item-'+innovation_ids[i]+' td.td-user-right');
                    $td.find('.overlay').remove();
                    updateTd($td, right);
                }
            }
        }

        $(document).on('click', '.change-right-selected', function (e) {
            e.stopImmediatePropagation();
            var user_id = $('#form-search-users select[name=user]').val();
            var type_class = ($(this).hasClass('to-add')) ? 'checkbox-to-add' : 'checkbox-to-update';
            var right = $(this).attr('data-right');
            var innovations_ids = [];
            $(this).parent().parent().parent().parent().find('.btn-group').removeClass('open');
            $('.'+type_class).each(function(){
                if($(this).parent().hasClass('checked')){
                    var $tr = $(this).parent().parent().parent();
                    innovations_ids.push(parseInt($tr.attr('data-id')));
                    displayTdLoader($tr.find('.td-user-right'));
                }
            });
            if(innovations_ids.length > 0){
                var url = $('#form-search-users').attr('data-update-user-right-url');
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        user_id: user_id,
                        innovation_ids: innovations_ids,
                        right: right
                    },
                    dataType: 'json',
                    success: function (response, statut) {
                        updateAfterMultipleChange(type_class, right, innovations_ids);
                        removeUpdatedState();
                    },
                    error: function (resultat, statut, e) {
                        if (resultat.statusText == 'abort' || e == 'abort') {
                            return;
                        }
                        console.error(resultat);
                    }
                });
            }
            return false;
        });


    });
})(jQuery);
