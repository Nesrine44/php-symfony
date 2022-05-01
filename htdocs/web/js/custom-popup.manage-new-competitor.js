var competitor_target_file = null;

/**
 * Validates the competitor form in manage.
 * @returns {boolean}
 */
function manageNewCompetitorValidation() {

    var isValid = true;
    var $firstError = null;
    var $form = $('form#form-manage-new-competitor-popup');
    var $nameField = $form.find('input[name="name"]');
    var $categoryField = $form.find('select[name="category"]');
    var $brandField = $form.find('input[name="brand"]');

    $form.find('.form-element').removeClass('error');
    $form.find('.form-element-error').remove();
    $form.find('.error-message').html('');

    if ($nameField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $nameField : $firstError;
        errorInput($nameField);
    }

    if ($categoryField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $categoryField : $firstError;
        errorInput($categoryField);
    }

    if ($brandField.val() === '') {
        isValid = false;
        $firstError = (!$firstError) ? $brandField : $firstError;
        errorInput($brandField);
    }

    if (!isValid) {
        scroll_to_popup_element($firstError);
    }

    return isValid;
}

function manage_new_competitor_clear_form_error() {
    var $form = $('form#form-manage-new-competitor-popup');
    $form.removeClass('editing');
    $form.find('.content-step-1').removeClass("display-none");
    $form.find('.form-element').removeClass('error');
    $form.find('.form-element-error').remove();
    $form.find('.error-message').html('');

    $form.get(0).reset();
    competitor_target_file = null;
    $form.find('input[name="competitor_id"]').val('');
    $form.find('input[name="picture_id"]').val('');
    $form.find('input[name="name"]').val('');
    $form.find('select[name="category"]').val('').trigger('change');
    $form.find('input[name="brand"]').val('');
    $form.find('select[name="tags"] option').remove();
    $form.find('select[name="tags"]').val('').trigger('change');
    $('#edition_manage_competitor_picture').find('.fake-img').css('background-image', 'none');
    $('#edition_manage_competitor_picture').removeClass('pictured error');
}

$(document).ready(function () {

    var $form = $('form#form-manage-new-competitor-popup');


    $(document).on('click', '.button-edit-md-competitor', function (e) {
        e.stopImmediatePropagation();
        var target = $(this).attr('data-popup-target');
        var competitor_id = parseInt($(this).attr('data-competitor_id'));
        var competitor = manage_md_get_competitor_by_id(competitor_id);
        if(!competitor){
            add_flash_message('error', "An unknown error occurred while editing your recommandations");
        }
        open_popup(target, function(){
            var $form = $('form#form-manage-new-competitor-popup');
            $form.addClass('editing');
            $form.find('input[name="competitor_id"]').val(competitor_id);
            if(competitor['picture'] && competitor['picture']['thumbnail']){
                $('#edition_manage_competitor_picture').find('.fake-img').css('background-image', 'url('+competitor['picture']['thumbnail']+')');
                $('#edition_manage_competitor_picture').addClass('pictured');
                $form.find('input[name="picture_id"]').val(competitor['picture']['id']);
            }
            $form.find('input[name="name"]').val(competitor['product_name']);
            $form.find('select[name="category"]').val(competitor['category']).trigger('change');
            $form.find('input[name="brand"]').val(competitor['brand']);;
            var tags_array = [];
            for(var i = 0; i < competitor['tags'].length; i++){
                var a_tag = competitor['tags'][i];
                var newOption = new Option(a_tag['text'], a_tag['id'], true, true);
                $form.find('select[name="tags"]').append(newOption).trigger('change');
                tags_array.push(a_tag['id']);
            }
            $form.find('select[name="tags"]').val(tags_array).trigger('change');
        });
    });

    $form.find('select[name="category"]').select2({}),

        $form.find('select[name=tags] option').attr('selected', 'selected');
    $form.find('select[name=tags]').select2({
        width: 'resolve',
        placeholder: 'Enter tag…',
        multiple: true,
        tags: true,
        minimumInputLength: 2,
        maximumSelectionLength: 5,
        language: {
            errorLoading: function () {
                return "Searching..."
            }
        },
        ajax: {
            url: '/api/find/tags',
            dataType: 'json',
            type: "GET",
            processResults: function (data) {
                return {
                    results: data.items
                };
            }
        }
    });

    $form.find('select[name=tags]').change(function(){
        $form.find('select[name=tags]').parent().find('input.select2-search__field').attr('placeholder', 'Enter tag…');
    });
    $form.find('select[name=tags]').parent().find('input.select2-search__field').attr('placeholder', 'Enter tag…');

    $('#manage_competitor_picture_upload').fileupload({
        dropZone: $('#manage_competitor_picture_upload'),
        add: function (e, data) {
            var $element = $('#edition_manage_competitor_picture');
            var $add_picture = $('#edition_manage_competitor_picture .add-picture');
            $("#edition_manage_competitor_picture").blur();
            $element.addClass('error').removeClass('pictured');
            $add_picture.find('span').html("Select file to upload<br>or drag and drop your picture file");
            var file = data.originalFiles[0];
            competitor_target_file = file;
            var name = file.name;
            var extension = name.split('.').pop().toLowerCase();
            if ($.inArray(extension, accepted_files_types) == -1) {
                $element.addClass('error');
                $add_picture.find('span').html("Upload a JPG, PNG or GIF file");
            } else {
                var size = file.size;
                if (window.URL) {
                    var img = new Image();
                    var proper_src = window.URL.createObjectURL(file);
                    img.src = proper_src;
                    img.onload = function () {
                        var width = img.naturalWidth,
                            height = img.naturalHeight;
                        window.URL.revokeObjectURL(img.src);
                        if (width < 300 || height < 200) {
                            $element.addClass('error');
                            $add_picture.find('span').html("Minimum size is 300 x 200 px");
                        } else {
                            $element.removeClass('error').addClass('pictured');
                            if (data.files && data.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    $element.find('.fake-img').css('background-image', 'url(' + e.target.result + ')');
                                }
                                reader.readAsDataURL(data.files[0]);
                            }
                        }
                    };
                } else {
                    $element.removeClass('error');
                    $add_picture.find('span').html("Picture added");
                }
            }
        }
    }).prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');


    $(document).on('click', '.button-delete-picture.md', function (e) {
        e.stopImmediatePropagation();
        $('#edition_manage_competitor_picture').find('.fake-img').css('background-image', 'none');
        $('#edition_manage_competitor_picture').removeClass('pictured');
        $('form#form-manage-new-competitor-popup').find('input[name="picture_id"]').val('');
    });


    var requestXHR = null;
    $(document).on('submit', '#form-manage-new-competitor-popup', function (e) {
        e.preventDefault();

        $form = $(this);

        if (!manageNewCompetitorValidation()) {
            return false;
        }

        enable_load_popup();

        if (requestXHR !== null) {
            requestXHR.abort();
        }


        var fields = new FormData($form[0]);

        var tags = $form.find('select[name=tags]').val() || [];

        if(!$form.hasClass('editing')) {
            fields.delete('competitor_id');
            fields.delete('picture_id');
        }
        // Input file is not set by fileupload, so this is a hack
        if (competitor_target_file) {
            fields.delete('image');
            fields.append('image', competitor_target_file);
        }

        // Setting tags
        fields.delete('tags');
        for (var i = 0; i < tags.length; i++) {
            fields.append('tags[]', tags[i]);
        }
        fields['token'] = $('#hub-token').val();
        requestXHR = $.ajax({
            url: '/api/manage/md/competitors',
            type: 'POST',
            data: fields,
            contentType: false,
            processData: false,
            success: function (response, status) {
                disable_load_popup();
                if (response.hasOwnProperty('status') && response['status'] == 'error') {
                    $form.find('.error-message').html(response['message']);
                } else {
                    close_popup();
                    var message = 'Competitor saved!';
                    add_flash_message('success', message);
                    manage_md_generate_competitors();
                }
            },
            error: function (response, status, e) {
                ajax_on_error(response, status, e);
                console.log('form-manage-new-competitor-popup submit', e);
                disable_load_popup();
                $form.find('.error-message').html(response.status + ' ' + e);
            }
        });
        return false;
    });
});