/*
 *   jQuery.cookie-notificator
 *   Written by @tic_le_kiwi
 */

(function ($) {
    $.fn.cookieNotificator = function (options) {
        var defauts =
        {
            id: '',
            theme: 'error',
            type: 'html',
            content: '',
            deleteButton: true,
            expires: 30,
            cookieCheck: true,
            customStyle: null
        };

        if(typeof options == 'undefined'){
            options = defauts;
        } else if ("id" in options) {
            options = $.extend(defauts, options);
        }
        return this.each(function () {
            var $this = $(this);
            // DISABLE cookieCheck for testing
            //options.cookieCheck = false;
            var current_path = window.location.pathname;
            if (options.type ==  'reset') {
                resetCookieNotificator(options.id);
            } else if (options === 'close') {
                var $cookieNotificator = $this.find('.cookie-notificator');
                closeCookieNotificator($cookieNotificator);
            } else {
                if(!options.cookieCheck || (options.cookieCheck && !Cookies.get(options.id, { path: current_path }))){
                    addHtml();
                }
            }

            if(!$('html').hasClass('cookie-notificated')){
                $('html').addClass('cookie-notificated');
                $(document).on('click', '.cookie-notificator .delete-action', function (e) {
                    e.stopImmediatePropagation();
                    var $cookieNotificator = $(this).closest('.cookie-notificator');
                    closeCookieNotificator($cookieNotificator);
                });
            }
            function addHtml() {
                var classe = (options.cookieCheck) ? '' : 'no-cookie-check';
                var style = "";
                if(options.customStyle){
                    $.each(options.customStyle, function(index, value) {
                        style+= index+':'+value+';';
                    });
                }
                var html = '<div class="cookie-notificator ' + options.theme + ' '+classe+'" '
                html += 'style="'+style+'display:none;" ';
                html += 'data-cookie-notificator-id="'+options.id+'" ';
                html += 'id="'+options.id+'" ';
                html += 'data-cookie-notificator-expires="'+options.expires+'" >';
                html += '<div class="content">'+options.content+'</div>';
                if(options.deleteButton) {
                    html += '<div class="delete-button delete-action"></div>';
                }
                html += '</div>';
                if (options.type == 'append') {
                    $this.append(html);
                }else if (options.type == 'prepend') {
                    $this.prepend(html);
                }else if (options.type == 'html') {
                    $this.html(html);
                }else if (options.type == 'after') {
                    $this.after(html);
                }else if (options.type == 'before') {
                    $this.before(html);
                }
                $('#'+options.id).addClass('ns-show').show();
            }

            function closeCookieNotificator($cookieNotificator){
                var id = $cookieNotificator.attr('data-cookie-notificator-id');
                var expires = parseInt($cookieNotificator.attr('data-cookie-notificator-expires'));
                if(!$cookieNotificator.hasClass('no-cookie-check')) {
                    Cookies.set(id, 'cookie-notificated', { expires: expires, path: current_path });
                }
                $cookieNotificator.removeClass('ns-show');
                setTimeout( function() {
                    $cookieNotificator.addClass('ns-hide');
                    setTimeout(function(){
                        $cookieNotificator.remove();
                    }, 280);
                }, 25 );

            }

            function resetCookieNotificator(id){
                Cookies.remove(id);
                Cookies.remove(id, { path: current_path });
            }

        });

    };
})(jQuery);