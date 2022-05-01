/*
 *   jQuery.manga-reader
 *   Written by @tic_le_kiwi
 */

(function ($) {
    $.fn.priLoader = function (options) {
        //On définit nos paramètres par défaut
        var defauts =
        {
            theme: 'blue',
            type: 'html'
        };

        if(typeof options == 'undefined'){
            options = defauts;
        }
        return this.each(function () {
            var $this = $(this);
            if (options === 'remove') {
                var $this = $(this);
                $this.find('sk-folding-cube').remove();
            } else {
                addHtml();
            }
            function addHtml() {
                var html = '<div class="sk-folding-cube ' + options.theme + '">';
                html += '<div class="sk-cube1 sk-cube"></div>';
                html += '<div class="sk-cube2 sk-cube"></div>';
                html += '<div class="sk-cube4 sk-cube"></div>';
                html += '<div class="sk-cube3 sk-cube"></div>';
                html += '</div>';
                if (options.type == 'append') {
                    $this.append(html);
                }
                if (options.type == 'prepend') {
                    $this.prepend(html);
                }
                if (options.type == 'html') {
                    $this.html(html);
                }
            }

        });

    };
})(jQuery);