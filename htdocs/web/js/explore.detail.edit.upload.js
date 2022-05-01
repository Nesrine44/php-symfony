var accepted_files_types = ['gif', 'jpeg', 'jpg', 'png'];
var accepted_packshot_files_types = ['jpeg', 'jpg', 'png'];

function explore_detail_edit_init_upload() {
    
    /* BEAUTYSHOT PICTURE
     ----------------------------------------------------------------------------------------*/

    $('#beautyshot_picture_upload').fileupload({
        url: '/api/innovation/picture/upload',
        formData: {id: $('#detail-id-innovation').val(), target: 'beautyshot_picture'},
        dataType: 'json',
        dropZone: $('#beautyshot_picture_upload'),
        add: function (e, data) {
            var $element = $('.block-add-picture.beautyshot_picture');
            var $add_picture = $('.block-add-picture.beautyshot_picture .add-picture');
            $("#beautyshot_picture_upload").blur();
            $element.addClass('error');
            $add_picture.find('span').html("Select file to upload<br>or drag and drop your picture file");
            var file = data.originalFiles[0];
            var name = file.name;
            var extension = name.split('.').pop().toLowerCase();
            if ($.inArray(extension, accepted_files_types) == -1) {
                $element.addClass('error');
                $add_picture.find('span').html("Upload a JPG, PNG or GIF file");
            } else {
                var size = file.size;
                if (window.URL) {
                    //console.log('window.URL ok!');
                    var img = new Image();
                    img.src = window.URL.createObjectURL(file);
                    img.onload = function () {
                        var width = img.naturalWidth,
                            height = img.naturalHeight;
                        window.URL.revokeObjectURL(img.src);
                        if (width < 945 || height < 500) {
                            $add_picture.addClass('error');
                            $add_picture.find('span').html("Minimum size is 945 x 500 px");
                        }  else {
                            $element.removeClass('error');
                            $add_picture.find('span').html("Uploading...");
                            data.submit();
                        }
                    };
                } else {
                    $element.removeClass('error');
                    $add_picture.find('span').html("Uploading...");
                    data.submit();
                }
            }
        },
        done: function (e, data) {
            var $element = $('.block-add-picture.beautyshot_picture');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            var results = data.result;
            //console.log(results);
            if (results.hasOwnProperty('full_data')) {
                updateFromCollection(results['full_data']['id'], results['full_data']);
                explore_detail_update_nb_missing_fields();
                add_saved_message();
            }
            if(!results.url){
                $element.addClass('error');
                $element.find('.add-picture span').html("An error has been found.<br>It seems there was two uploads at the same time.<br>Please try again.");
            }else {
                $element.find('.picture-added img').attr('src', results.url);
                $element.find('.add-picture span').html("Select file to upload<br>or drag and drop your picture file");
                $element.addClass('pictured');
            }
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('.block-add-picture.beautyshot_picture .progress-upload .progress-bar').css(
                'width',
                progress + '%'
            );
        },
        error: function (e, data) {
            console.log("Error");
            console.log(e);
            console.log(data);
            var $element = $('.block-add-picture.beautyshot_picture');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            $element.addClass('error');
            $element.find('.add-picture span').html("File upload fail");
        }
    }).on('fileuploadfail', function (e, data) {
        console.log("fileuploadfail");
        console.log(e);
        console.log(data);
        var $element = $('.block-add-picture.beautyshot_picture');
        $element.find('.progress-upload .progress-bar').css('width', '0');
        $element.find('.add-picture').addClass('error');
        $element.find('.add-picture span').html("File upload fail");
    }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');



    /* PACKSHOT PICTURE
     ----------------------------------------------------------------------------------------*/

    $('#packshot_picture_upload').fileupload({
        url: '/api/innovation/picture/upload',
        formData: {id: $('#detail-id-innovation').val(), target: 'packshot_picture'},
        dataType: 'json',
        dropZone: $('#packshot-picture-upload'),
        add: function (e, data) {
            var $element = $('.block-add-picture.packshot_picture');
            var $add_picture = $('.block-add-picture.packshot_picture .add-picture');
            $("#packshot_picture_upload").blur();
            $element.addClass('error');
            $add_picture.find('span').html("Select file to upload<br>or drag and drop your picture file");
            var file = data.originalFiles[0];
            var name = file.name;
            var extension = name.split('.').pop().toLowerCase();
            if ($.inArray(extension, accepted_packshot_files_types) == -1) {
                $element.addClass('error');
                $add_picture.find('span').html("Upload a PNG or a JPG file");
            } else {
                var size = file.size;
                if (window.URL) {
                    //console.log('window.URL ok!');
                    var img = new Image();
                    img.src = window.URL.createObjectURL(file);
                    img.onload = function () {
                        var width = img.naturalWidth,
                            height = img.naturalHeight;
                        window.URL.revokeObjectURL(img.src);
                        if (width < 600 || height < 500) {
                            $element.addClass('error');
                            $add_picture.find('span').html("Minimum size is 600 x 500 px");
                        } else {
                            $element.removeClass('error');
                            $add_picture.removeClass('error');
                            $add_picture.find('span').html("Uploading...");
                            data.submit();
                        }
                    };
                } else {
                    $element.removeClass('error');
                    $add_picture.removeClass('error');
                    $add_picture.find('span').html("Uploading...");
                    data.submit();
                }
            }
        }
        ,
        done: function (e, data) {
            var $element = $('.block-add-picture.packshot_picture');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            var results = data.result;
            //console.log(results);
            if (results.hasOwnProperty('full_data')) {
                updateFromCollection(results['full_data']['id'], results['full_data']);
                explore_detail_update_nb_missing_fields();
                add_saved_message();
            }
            if(!results.url){
                $element.addClass('error');
                $element.find('.add-picture span').html("An error has been found.<br>It seems there was two uploads at the same time.<br>Please try again.");
            }else {
                $element.find('.picture-added img').attr('src', results.url);
                $element.find('.add-picture span').html("Select file to upload<br>or drag and drop your picture file");
                $element.addClass('pictured');
            }
        }
        ,
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('.block-add-picture.packshot_picture .progress-upload .progress-bar').css(
                'width',
                progress + '%'
            );
        },
        error: function (e, data) {
            console.log("Error");
            console.log(e);
            console.log(data);
            var $element = $('.block-add-picture.packshot_picture');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            $element.addClass('error');
            $element.find('.add-picture span').html("File upload fail");
        }
    }).on('fileuploadfail', function (e, data) {
        console.log("fileuploadfail");
        console.log(e);
        console.log(data);
        var $element = $('.block-add-picture.packshot_picture');
        $element.find('.progress-upload .progress-bar').css('width', '0');
        $element.addClass('error');
        $element.find('.add-picture span').html("File upload fail");
    }).prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');

    /* ADDITIONAL PICTURE
     ----------------------------------------------------------------------------------------*/

    $('#other_picture_upload').fileupload({
        url: '/api/innovation/picture/upload',
        formData: {
            id: $('#detail-id-innovation').val(),
            target: 'additional_picture',
            position: $('.block-add-picture.other_picture.pictured').length
        },
        dataType: 'json',
        dropZone: $('#other_picture_upload'),
        add: function (e, data) {
            var $element = $('.block-add-picture.other_picture.uploader');
            var $add_picture = $('.block-add-picture.other_picture.uploader .add-picture');
            $("#other_picture_upload").blur();
            $element.removeClass('error');
            $add_picture.find('span').html("Select file to upload<br>or drag and drop your picture file");
            var file = data.originalFiles[0];
            var name = file.name;
            data.formData = {
                id: $('#detail-id-innovation').val(),
                target: 'additional_picture',
                position: $('.block-add-picture.other_picture.pictured').length
            };
            var extension = name.split('.').pop().toLowerCase();
            if ($.inArray(extension, accepted_files_types) == -1) {
                $element.addClass('error');
                $add_picture.find('span').html("Upload a JPG, PNG or GIF file");
            } else {
                var size = file.size;
                if (window.URL) {
                    var img = new Image();
                    img.src = window.URL.createObjectURL(file);
                    img.onload = function () {
                        var width = img.naturalWidth,
                            height = img.naturalHeight;
                        window.URL.revokeObjectURL(img.src);
                        if (width < 680 || height < 680) {
                            $element.addClass('error');
                            $add_picture.find('span').html("Minimum size is 680 x 680 px");
                        }  else {
                            $element.removeClass('error');
                            $add_picture.find('span').html("Uploading...");
                            data.submit();
                        }
                    };
                } else {
                    $element.removeClass('error');
                    $add_picture.find('span').html("Uploading...");
                    data.submit();
                }
            }
        },
        done: function (e, data) {
            var $element = $('.block-add-picture.other_picture.uploader');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            var results = data.result;
            //console.log(results);
            if (results.hasOwnProperty('full_data')) {
                updateFromCollection(results['full_data']['id'], results['full_data']);

            }
            if(!results.url){
                $element.addClass('error');
                $element.find('.add-picture span').html("An error has been found.<br>It seems there was two uploads at the same time.<br>Please try again.");
            }else {
                $element.find('.picture-added img').attr('src', results.url);
                $element.find('.add-picture span').html("Select file to upload<br>or drag and drop your picture file");
                $element.before('<div class="block-add-picture true-picture other_picture pictured"><div class="picture-added"><img alt="" src="'+results.url+'"/><div class="button-delete-picture hub-button height-26" data-target="additional_picture">Remove</div></div></div>');
            }
        }, progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('.block-add-picture.other_picture.uploader .progress-upload .progress-bar').css(
                'width',
                progress + '%'
            );
        },
        error: function (e, data) {
            console.log("Error");
            console.log(e);
            console.log(data);
            var $element = $('.block-add-picture.other_picture.uploader');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            $element.addClass('error');
            $element.find('.add-picture span').html("File upload fail");
        }
    }).on('fileuploadfail', function (e, data) {
        console.log("fileuploadfail");
        console.log(e);
        console.log(data);
        var $element = $('.block-add-picture.other_picture.uploader');
        $element.find('.progress-upload .progress-bar').css('width', '0');
        $element.addClass('error');
        $element.find('.add-picture span').html("File upload fail");
    }).prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');



    var id = parseInt($('#detail-id-innovation').val());
    var innovation = getInnovationById(id, true);
    // if innovation is product and at least experiment
    if(innovation && innovation['classification_type'] == 'Product' && !in_array(innovation['current_stage'], ['discover', 'ideate'])){

        $('#proofs_of_traction_picture_1').fileupload({
            url: '/api/innovation/picture/upload',
            formData: {id: $('#detail-id-innovation').val(), target: 'pot_picture_1'},
            dataType: 'json',
            dropZone: $('#proofs_of_traction_picture_1'),
            add: function (e, data) {
                var $element = $('.block-add-picture.pot_picture_1');
                var $add_picture = $('.block-add-picture.pot_picture_1 .add-picture');
                $("#proofs_of_traction_picture_1").blur();
                $element.addClass('error');
                $add_picture.find('span').html("Select file to upload<br>or drag and drop your picture file");
                var file = data.originalFiles[0];
                var name = file.name;
                var extension = name.split('.').pop().toLowerCase();
                if (jQuery.inArray(extension, accepted_files_types) == -1) {
                    $element.addClass('error');
                    $add_picture.find('span').html("Upload a JPG, PNG or GIF file");
                } else {
                    var size = file.size;
                    if (window.URL) {
                        var img = new Image();
                        img.src = window.URL.createObjectURL(file);
                        img.onload = function () {
                            var width = img.naturalWidth,
                                height = img.naturalHeight;
                            window.URL.revokeObjectURL(img.src);
                            if (width < 300 || height < 200) {
                                $element.addClass('error');
                                $add_picture.find('span').html("Minimum size is 300 x 200 px");
                            }  else {
                                $element.removeClass('error');
                                $add_picture.find('span').html("Uploading...");
                                data.submit();
                            }
                        };
                    } else {
                        $element.removeClass('error');
                        $add_picture.find('span').html("Uploading...");
                        data.submit();
                    }
                }
            }
            ,
            done: function (e, data) {
                var $element = $('.block-add-picture.pot_picture_1');
                $element.find('.progress-upload .progress-bar').css('width', '0');
                var results = data.result;
                //console.log(results);
                if (results.hasOwnProperty('full_data')) {
                    updateFromCollection(results['full_data']['id'], results['full_data']);
                    explore_detail_update_nb_missing_fields();
                    add_saved_message();
                }
                if(!results.url){
                    $element.addClass('error');
                    $element.find('.add-picture span').html("An error has been found.<br>It seems there was two uploads at the same time.<br>Please try again.");
                }else {
                    $element.find('.picture-added .fake-img').css({'background-image': 'url("' + results.url + '")'});
                    $element.find('.add-picture span').html("Select file to upload<br>or drag and drop your picture file");
                    $element.addClass('pictured');
                }
            }
            ,
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.block-add-picture.pot_picture_1 .progress-upload .progress-bar').css(
                    'width',
                    progress + '%'
                );
            },
            error: function (e, data) {
                console.log("Error");
                console.log(e);
                console.log(data);
                var $element = $('.block-add-picture.pot_picture_1');
                $element.find('.progress-upload .progress-bar').css('width', '0');
                $element.addClass('error');
                $element.find('.add-picture span').html("File upload fail");
            }
        }).on('fileuploadfail', function (e, data) {
            console.log("fileuploadfail");
            console.log(e);
            console.log(data);
            var $element = $('.block-add-picture.pot_picture_1');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            $element.addClass('error');
            $element.find('.add-picture span').html("File upload fail");
        }).prop('disabled', !jQuery.support.fileInput)
            .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');


        $('#proofs_of_traction_picture_2').fileupload({
            url: '/api/innovation/picture/upload',
            formData: {id: $('#detail-id-innovation').val(), target: 'pot_picture_2'},
            dataType: 'json',
            dropZone: $('#proofs_of_traction_picture_2'),
            add: function (e, data) {
                var $element = $('.block-add-picture.pot_picture_2');
                var $add_picture = $('.block-add-picture.pot_picture_2 .add-picture');
                $("#proofs_of_traction_picture_2").blur();
                $element.addClass('error');
                $add_picture.find('span').html("Select file to upload<br>or drag and drop your picture file");
                var file = data.originalFiles[0];
                var name = file.name;
                var extension = name.split('.').pop().toLowerCase();
                if (jQuery.inArray(extension, accepted_files_types) == -1) {
                    $element.addClass('error');
                    $add_picture.find('span').html("Upload a JPG, PNG or GIF file");
                } else {
                    var size = file.size;
                    if (window.URL) {
                        var img = new Image();
                        img.src = window.URL.createObjectURL(file);
                        img.onload = function () {
                            var width = img.naturalWidth,
                                height = img.naturalHeight;
                            window.URL.revokeObjectURL(img.src);
                            if (width < 300 || height < 200) {
                                $element.addClass('error');
                                $add_picture.find('span').html("Minimum size is 300 x 200 px");
                            }  else {
                                $element.removeClass('error');
                                $add_picture.find('span').html("Uploading...");
                                data.submit();
                            }
                        };
                    } else {
                        $element.removeClass('error');
                        $add_picture.find('span').html("Uploading...");
                        data.submit();
                    }
                }
            }
            ,
            done: function (e, data) {
                var $element = $('.block-add-picture.pot_picture_2');
                $element.find('.progress-upload .progress-bar').css('width', '0');
                var results = data.result;
                if (results.hasOwnProperty('full_data')) {
                    updateFromCollection(results['full_data']['id'], results['full_data']);
                    explore_detail_update_nb_missing_fields();
                    add_saved_message();
                }
                if(!results.url){
                    $element.addClass('error');
                    $element.find('.add-picture span').html("An error has been found.<br>It seems there was two uploads at the same time.<br>Please try again.");
                }else {
                    $element.find('.picture-added .fake-img').css({'background-image': 'url("' + results.url + '")'});
                    $element.find('.add-picture span').html("Select file to upload<br>or drag and drop your picture file");
                    $element.addClass('pictured');
                }
            }
            ,
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.block-add-picture.pot_picture_2 .progress-upload .progress-bar').css(
                    'width',
                    progress + '%'
                );
            },
            error: function (e, data) {
                console.log("Error");
                console.log(e);
                console.log(data);
                var $element = $('.block-add-picture.pot_picture_2');
                $element.find('.progress-upload .progress-bar').css('width', '0');
                $element.addClass('error');
                $element.find('.add-picture span').html("File upload fail");
            }
        }).on('fileuploadfail', function (e, data) {
            console.log("fileuploadfail");
            console.log(e);
            console.log(data);
            var $element = $('.block-add-picture.pot_picture_2');
            $element.find('.progress-upload .progress-bar').css('width', '0');
            $element.addClass('error');
            $element.find('.add-picture span').html("File upload fail");
        }).prop('disabled', !jQuery.support.fileInput)
            .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');



    }
}

$(document).ready(function () {
        $(document).on('click', '.button-delete-picture:not(.md)', function (e) {
            e.stopImmediatePropagation();
            var $target_img = $(this).parent().parent();
            $target_img.find('img').attr('src', '');
            $target_img.find('.fake-img').css('background-image', 'none');
            $target_img.removeClass('pictured');
            var target_picture = $(this).attr('data-target');
            var data = {id: $('#detail-id-innovation').val(), target: target_picture};
            if(in_array(target_picture, ['beautyshot_picture', 'packshot_picture'])){
                $target_img.addClass('error');
            }
            if (target_picture == 'additional_picture') {
                $target_img.addClass('toDelete');
                var i = 0;
                var position = 0;
                $('.block-add-picture.other_picture').each(function () {
                    if ($(this).hasClass('toDelete')) {
                        position = i;
                    }
                    i++;
                });
                $target_img.remove();
                data = {id: $('#detail-id-innovation').val(), target: target_picture, position: position};
            }
            data['token'] = $('#hub-token').val();
            $.ajax({
                url: '/api/innovation/picture/delete',
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response, statut) {
                    if (response.hasOwnProperty('full_data')) {
                        updateFromCollection(response['full_data']['id'], response['full_data']);
                        explore_detail_update_nb_missing_fields();
                        add_saved_message();
                    }
                },
                error: function (resultat, statut, e) {
                    ajax_on_error(resultat, statut, e);
                    if (e != 'abort') {
                        add_flash_message('error', 'Error deleting picture : ' + resultat.status + ' ' + e);
                        console.log(e);
                    }
                }
            });
        });
    });

