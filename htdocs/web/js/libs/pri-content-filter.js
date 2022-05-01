/*
 *   pri-content-filter
 *   Written by @tic_le_kiwi
 */

(function ($) {
    $.fn.contentFilter = function (options) {
        var isInited = false;
        var onSearch = false;
        //On définit nos paramètres par défaut
        var defauts =
        {
            data: {},
            identifier: 'f-identifier',
            title: "",
            id: null,
            order: null,
            typeValue: "text",
            special_case: true,
            disallow_order: false,
            on_after_each_time: false,
            onAfter: function (filters) {
            }
        };
        if (options === 'getFilters' || options === 'reset') {

        } else if ("data" in options || "title" in options) {
            var options = $.extend(defauts, options);
        } else {
            defauts.data = options;
            var options = defauts;
        }
        function getFullFilters($this, identifier) {
            var filters = new Object();
            filters.datas = [];
            filters.datas_get = '';
            filters.order_get = '';
            var get = '';
            if ($this.hasClass('date')) {
                var from = $this.find('#the-' + identifier + '-from').val();
                var to = $this.find('#the-' + identifier + '-to').val();
                if (from != '') {
                    filters.datas_get += '&filter[' + identifier + '][from]=' + from;
                }
                if (to != '') {
                    filters.datas_get += '&filter[' + identifier + '][to]=' + to;
                }
            } else {
                $this.find('.' + identifier + ':checked').each(function () {
                    filters.datas.push($(this).val());
                    get += $(this).val() + ',';
                });
                if ((filters.datas).length > 0) {
                    filters.datas_get = '&filter[' + identifier + ']=' + get.slice(0, -1);
                }
                if($this.hasClass('no_special_case') && $this.find(".content-filter-fake-checkbox").hasClass('checked')){
                    filters.datas_get = '';
                }else if ($this.find(".content-filter-fake-checkbox").hasClass('checked') && identifier != 'current_stage' && !(identifier == 'other' && check_if_user_is_management())) { // special current_stage && special service for management
                    filters.datas_get = '';
                }
                if (!$this.hasClass('no_special_case') && identifier == 'current_stage' && filters.datas.equals([1, 2, 3, 4, 5])) { // special current_stage
                    filters.datas_get = '';
                }
                if (identifier == 'other' && check_if_user_is_management() && filters.datas.equals(["1", "2", "3", "4", "0", "201"])) {  // special service for management
                    filters.datas_get = '';
                }
            }
            if ($this.find('.order-ascending').hasClass('active')) {
                filters.order = 'ASC';
            } else if ($this.find('.order-descending').hasClass('active')) {
                filters.order = 'DESC';
            } else {
                filters.order = null;
            }
            if (filters.order !== null) {
                filters.order_get = '&order[' + identifier + ']=' + filters.order;
            }
            filters.full_get = filters.datas_get + filters.order_get;
            return filters;
        }

        function resetFilter($celui, identifier) {
            if ($celui.hasClass('date')) {
                $celui.find('.content-filter-order-option').removeClass('active');
                $celui.find('.preselected-date').removeClass('active');
                $celui.find('#the-' + identifier + '-to').removeClass('error').val('');
                $celui.find('#the-' + identifier + '-from').removeClass('error').val('');
                $celui.find('#the-' + identifier + '-to').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
                $celui.find('#the-' + identifier + '-from').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
            } else {
                $celui.find('.content-filter-option input').prop('checked', true);
                if (!$celui.hasClass('no_special_case') && identifier == 'current_stage') {  // special current_stage
                    $('#current_stage-7, #current_stage-8, #current_stage-88, #current_stage-99, #current_stage-100').prop('checked', false);
                }
                if (identifier == 'other' && check_if_user_is_management()) {  // special service for management
                    $('#other-202').prop('checked', false);
                }
                $celui.find('.content-filter-order-option').removeClass('active');

                var $fake_checkbox = $celui.find(".content-filter-fake-checkbox");
                var atLeastOneIsChecked = ($celui.find('.' + identifier + ':checked').length > 0);
                var allChecked = (($celui.find('.' + identifier).length - $celui.find('.' + identifier + ':checked').length) == 0);
                var noCheck = ($celui.find('.' + identifier + ':checked').length == 0);
                if (atLeastOneIsChecked) {
                    $fake_checkbox.removeClass('checked').addClass('intermediate');
                }
                if (allChecked) {
                    $fake_checkbox.removeClass('intermediate').addClass('checked');
                    $celui.find('.content-filter-input-search').val('');
                    $celui.find('.content-filter-option').show();
                }
                if (noCheck) {
                    $fake_checkbox.removeClass('checked intermediate');
                }
            }
            $celui.removeClass('filter-opened filtered');
        }

        if (options === 'getFilters') {
            var $this = $(this);
            var identifier = $this.attr('data-identifier');
            return getFullFilters($this, identifier);
        } else if (options === 'reset') {
            var $this = $(this);
            var identifier = $this.attr('data-identifier');
            resetFilter($this, identifier);
        } else {
            return this.each(function () {
                var $this = $(this);
                var data = options.data;
                var identifier = options.identifier;
                var title = options.title;
                var order = options.order;
                if (!$this.hasClass('content-filter-active')) {
                    $this.addClass('content-filter-' + options.typeValue);
                    addHtml();
                }

                function checkAllStatus() {
                    var $celui = $this.find(".content-filter-fake-checkbox");
                    var atLeastOneIsChecked = ($this.find('.' + identifier + ':checked').length > 0);
                    var allChecked = (($this.find('.' + identifier).length - $this.find('.' + identifier + ':checked').length) == 0);
                    var noCheck = ($this.find('.' + identifier + ':checked').length == 0);
                    if (atLeastOneIsChecked) {
                        $celui.removeClass('checked').addClass('intermediate');
                    }
                    if (allChecked) {
                        $celui.removeClass('intermediate').addClass('checked');
                        $this.find('.content-filter-input-search').val('');
                        $this.find('.content-filter-option').show();
                    }
                    if (noCheck) {
                        $celui.removeClass('checked intermediate');
                    }
                    if (isInited && options.on_after_each_time) {
                        var filters = getFilters();
                        getFilteredState();
                        options.onAfter(filters);
                    }
                }

                function getFilteredState() {
                    var filters = getFilters();
                    if (filters.full_get != '') {
                        $this.addClass('filtered');
                    } else {
                        $this.removeClass('filtered');
                    }
                }

                function getFilters() {
                    return getFullFilters($this, identifier);
                }

                function openFilters($celui) {
                    var distance_to_right = $(window).width() - ($celui.offset().left + $celui.width());
                    if (distance_to_right > 270) {
                        $celui.removeClass('to-right').addClass('to-left');
                    } else {
                        $celui.removeClass('to-left').addClass('to-right');
                    }
                    if ($celui.hasClass('filter-opened')) {
                        $celui.removeClass('filter-opened');
                    } else {
                        $('.filter-opened').removeClass('filter-opened');
                        $celui.addClass('filter-opened');
                    }
                }

                function isDate(val) {
                    var date_array = val.split('/');
                    if (date_array.length != 3) {
                        return false;
                    }
                    if (date_array[0].length != 2 || date_array[1].length != 2 || date_array[2].length != 4) {
                        return false;
                    }
                    return true;
                }

                function formatDate(date) {
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    if (month < 10) {
                        month = '0' + month;
                    }
                    if (day < 10) {
                        day = '0' + day;
                    }
                    var year = date.getFullYear();
                    return month + '/' + day + '/' + year;
                }

                function getClassForDatePresselected(dateStringFrom, dateStringTo, nb) {
                    if (dateStringFrom == '' || dateStringTo == '') {
                        return '';
                    }
                    var to = new Date();
                    var from = new Date();
                    from.setMonth(from.getMonth() - nb);
                    var dateTo = formatDate(to);
                    var dateFrom = formatDate(from);
                    if (dateStringFrom == dateFrom && dateStringTo == dateTo) {
                        return 'active';
                    } else {
                        return '';
                    }
                }

                function dateHasError() {
                    var from = $this.find('#the-' + identifier + '-from').val();
                    var to = $this.find('#the-' + identifier + '-to').val();
                    var isError = false;
                    if (from != '' && !isDate(from)) {
                        isError = true;
                        $this.find('#the-' + identifier + '-from').addClass('error');
                        $this.find('#the-' + identifier + '-from').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar-error.png');
                    }
                    if (to != '' && !isDate(to)) {
                        $this.find('#the-' + identifier + '-to').addClass('error');
                        $this.find('#the-' + identifier + '-to').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar-error.png');
                        isError = true;
                    }
                    return isError;
                }

                function addHtml() {
                    $this.addClass('content-filter-active filter ' + options.typeValue);
                    if(!options.special_case){
                        $this.addClass('no_special_case');
                    }
                    $this.attr('data-identifier', identifier);
                    var text = $this.text().replace(' ?', '').replace('?', '');
                    var out = [], o = -1;
                    if (options.id) {
                        var element = document.getElementById(options.id);
                        out[++o] = element.innerHTML;
                    }
                    out[++o] = '<div class="content-filter-highlight">' + text + '<div class="link"></div><div class="content-filter-highlight-action"></div></div>';
                    out[++o] = '<div class="content-filter">';
                    out[++o] = '<span class="content-filter-span-title">';
                    out[++o] = '<span>' + title + '</span>';
                    if (options.disallow_order == false) {
                        out[++o] = '<div class="content-filter-order">';
                        var classeOrder = (order == 'ASC') ? 'active' : '';
                        out[++o] = '<span class="content-filter-order-option order-ascending ' + classeOrder + '"></span>';
                        classeOrder = (order == 'DESC') ? 'active' : '';
                        out[++o] = '<span class="content-filter-order-option order-descending ' + classeOrder + '"></span>';
                        out[++o] = '</div>';
                    }
                    out[++o] = '</span>';
                    if (options.typeValue == 'date') {
                        var classePresselected = '';
                        out[++o] = '<span class="content-filter-span-preselected-dates">';
                        classePresselected = getClassForDatePresselected(data.from, data.to, 12);
                        out[++o] = '<span class="preselected-date ' + classePresselected + '" data-name="' + identifier + '" data-target="12">Last year</span>';
                        classePresselected = getClassForDatePresselected(data.from, data.to, 6);
                        out[++o] = '<span class="preselected-date ' + classePresselected + '" data-name="' + identifier + '" data-target="6">Last 6 month</span>';
                        classePresselected = getClassForDatePresselected(data.from, data.to, 3);
                        out[++o] = '<span class="preselected-date ' + classePresselected + '" data-name="' + identifier + '" data-target="3">Last 3 month</span>';
                        out[++o] = '</span>';
                        out[++o] = '<div class="content-filter-dates-from-to">';
                        out[++o] = '<div class="pri-content-export-picker">';
                        out[++o] = '<span class="span-datepicker">From</span>';
                        out[++o] = '<div class="content-picker">';
                        out[++o] = '<input type="text" placeholder="mm/dd/yyyy" class="datepicker" id="the-' + identifier + '-from" name="' + identifier + '[from]" value="' + data.from + '" />';
                        out[++o] = '</div>';
                        out[++o] = '</div>';
                        out[++o] = '<div class="pri-content-export-picker">';
                        out[++o] = '<span class="span-datepicker">To</span>';
                        out[++o] = '<div class="content-picker">';
                        out[++o] = '<input type="text" placeholder="mm/dd/yyyy" class="datepicker" id="the-' + identifier + '-to" name="' + identifier + '[to]" value="' + data.to + '" />';
                        out[++o] = '</div>';
                        out[++o] = '</div>';
                        out[++o] = '</div>';
                    } else {
                        out[++o] = '<span class="content-filter-span-search">';
                        out[++o] = '<input class="content-filter-input-search" type="search">';
                        out[++o] = '</span>';
                        out[++o] = '<span class="content-filter-span-all">';
                        out[++o] = '<div class="content-to-hover">';
                        out[++o] = '<div class="content-filter-fake-checkbox checked" data-target="' + identifier + '"></div> <span>All</span>';
                        out[++o] = '</div>';
                        out[++o] = '</span>';
                        out[++o] = '<div class="content-filter-options" id="content-filter-options-' + identifier + '">';
                        for (var key = 0; key < data.length; key++) {
                            out[++o] = '<div class="content-filter-option content-to-hover content-filter-option-' + identifier + '" data-value="' + data[key]['id'] + '"><input type="checkbox" onKeyDown="this.value=this.value;" id="' + identifier + '-' + data[key]['id'] + '" class="' + identifier + ' checkbox-content-filter" name="filter[' + data[key]['id'] + '][]" ' + data[key]['checked'] + ' value="' + data[key]['id'] + '"/><label class="fake-checkbox"></label><label>' + data[key]['libelle'] + '</label>';
                            out[++o] = '</div>';
                            if(data[key].hasOwnProperty('separator') && data[key]['separator'] === true){
                                out[++o] = '<div class="content-filter-separator"></div>';
                            }
                        }
                        out[++o] = '</div>';
                    }
                    out[++o] = '<div class="content-filter-actions">';
                    out[++o] = '<button class="hub-button height-26 transparent-yellow  content-filter-clear">Clear filter</button>';
                    if (options.typeValue == 'date' || !options.on_after_each_time) {
                        out[++o] = '<button class="hub-button height-26 content-filter-apply">Apply</button>';
                    }
                    out[++o] = '</div>';
                    out[++o] = '</div>';
                    if (options.id) {
                        var element = document.getElementById(options.id);
                        element.innerHTML = out.join('');
                    } else {
                        $this.append(out.join(''));
                    }
                    checkAllStatus();
                    getFilteredState();


                    $this.find('.inner-th .open-filter-action').click(function () {
                        var $celui = $(this).parent().parent();
                        openFilters($celui);
                    });

                    $this.find('.content-filter-option').click(function(){
                        $(this).find('input[type=checkbox]').click();
                    });

                    $this.find('.content-filter-highlight-action').click(function () {
                        var $celui = $(this).parent().parent();
                        if ($celui.hasClass('filtered')) {
                            onSearch = true;
                            if (options.typeValue == 'date') {
                                $celui.find('.content-filter-order-option').removeClass('active');
                                $celui.find('.preselected-date').removeClass('active');
                                $celui.find('#the-' + identifier + '-to').removeClass('error').val('');
                                $celui.find('#the-' + identifier + '-from').removeClass('error').val('');
                                $celui.find('#the-' + identifier + '-to').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
                                $celui.find('#the-' + identifier + '-from').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
                            } else {
                                $celui.find('.content-filter-option input').prop('checked', true);
                                if (!$this.hasClass('no_special_case') && identifier == 'current_stage') {  // special current_stage
                                    $('#current_stage-7, #current_stage-8, #current_stage-88, #current_stage-99, #current_stage-100').prop('checked', false);
                                }
                                if (identifier == 'other' && check_if_user_is_management()) {  // special service for management
                                    $('#other-202').prop('checked', false);
                                }
                                $celui.find('.content-filter-order-option').removeClass('active');
                                checkAllStatus();
                            }
                            $celui.removeClass('filter-opened');
                            var filters = getFilters();
                            getFilteredState();
                            options.onAfter(filters);
                            onSearch = false;
                        } else {
                            openFilters($celui);
                        }
                    });

                    if (options.typeValue == 'date') {
                        $this.find('.preselected-date').click(function () {
                            if (!$(this).hasClass('active')) {
                                $this.find('.preselected-date').removeClass('active');
                                $(this).addClass('active');
                                var nb = parseInt($(this).attr('data-target'));
                                var to = new Date();
                                var from = new Date();
                                from.setMonth(from.getMonth() - nb);
                                $this.find('#the-' + identifier + '-to').val(formatDate(to));
                                $this.find('#the-' + identifier + '-from').val(formatDate(from));
                            }
                        });


                        $this.find(".content-filter-dates-from-to .datepicker").datepicker({
                            dateFormat: 'mm/dd/yy',
                            showOn: "both",
                            buttonImageOnly: true,
                            buttonImage: "/images/datepicker/pic-calendar.png",
                            yearRange: '1900:+5',
                            onSelect: function (dateText, inst) {
                                $(this).removeClass('error');
                                $(this).parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
                            }
                        });

                        $this.find(".content-filter-dates-from-to .datepicker").blur(function () {
                            var val = $(this).val();
                            if (val != '' && !isDate(val)) {
                                $(this).addClass('error');
                                $(this).parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar-error.png');
                            } else {
                                $(this).removeClass('error');
                                $(this).parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
                            }
                        });

                        $this.find('.content-filter-apply').click(function () {
                            if (!dateHasError()) {
                                $this.removeClass('filter-opened');
                                var filters = getFilters();
                                getFilteredState();
                                options.onAfter(filters);
                                $this.removeClass('filter-opened');
                            }
                        });

                        $(document).keypress(function(e) {
                            if(e.which == 13  && $this.hasClass('filter-opened')) {
                                if (!dateHasError()) {
                                    $this.removeClass('filter-opened');
                                    var filters = getFilters();
                                    getFilteredState();
                                    options.onAfter(filters);
                                    $this.removeClass('filter-opened');
                                }
                            }
                        });

                        $this.find('.content-filter-clear').click(function () {
                            $this.find('.content-filter-order-option').removeClass('active');
                            $this.find('.preselected-date').removeClass('active');
                            $this.find('#the-' + identifier + '-to').removeClass('error').val('');
                            $this.find('#the-' + identifier + '-from').removeClass('error').val('');
                            $this.find('#the-' + identifier + '-to').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
                            $this.find('#the-' + identifier + '-from').parent().find('.ui-datepicker-trigger').attr('src', '/images/datepicker/pic-calendar.png');
                        });
                    } else {
                        $this.find('.content-filter-span-all').click(function () {
                            onSearch = true;
                            var $celui = $(this).find(".content-filter-fake-checkbox");
                            if ($celui.hasClass('intermediate')) {
                                $celui.removeClass('intermediate');
                                $('.' + $celui.attr('data-target')).prop('checked', false);
                            } else if ($celui.hasClass('checked')) {
                                $celui.removeClass('checked');
                                $('.' + $celui.attr('data-target')).prop('checked', false);
                            } else {
                                $celui.removeClass('intermediate checked').addClass('checked');
                                $('.' + $celui.attr('data-target')).prop('checked', true);
                            }
                            if (options.on_after_each_time) {
                                var filters = getFilters();
                                getFilteredState();
                                options.onAfter(filters);
                            }
                            onSearch = false;
                        });

                        $this.find('.content-filter-apply').click(function () {
                            $this.removeClass('filter-opened');
                            var filters = getFilters();
                            getFilteredState();
                            options.onAfter(filters);
                        });

                        $(document).keypress(function(e) {
                            if(e.which == 13  && $this.hasClass('filter-opened')) {
                                $this.removeClass('filter-opened');
                                var filters = getFilters();
                                getFilteredState();
                                options.onAfter(filters);
                            }
                        });

                        $this.find('.content-filter-clear').click(function () {
                            $this.find('.content-filter-input-search').val('');
                            $this.find('.content-filter-option').show();
                            $this.find('.content-filter-option input').prop('checked', true);
                            if (!$this.hasClass('no_special_case') && identifier == 'current_stage') {  // special current_stage
                                $('#current_stage-7, #current_stage-8, #current_stage-88, #current_stage-99, #current_stage-100').prop('checked', false);
                            }
                            if (identifier == 'other' && check_if_user_is_management()) {  // special service for management
                                $('#other-202').prop('checked', false);
                            }
                            $this.find('.content-filter-order-option').removeClass('active');
                            checkAllStatus();
                        });


                        $this.find('.content-filter-input-search').on('input', function (e) {
                            onSearch = true;
                            var searchWord = $(this).val().toLowerCase();
                            if (searchWord.length === 0) {
                                $this.find('.content-filter-option.sub_filtered').show();
                                $this.find('.content-filter-option.sub_filtered input').prop('checked', true);
                            } else {
                                $this.find('.content-filter-option.sub_filtered').each(function () {
                                    if ($(this).find('label').text().toLowerCase().indexOf(searchWord) >= 0) {
                                        $(this).show();
                                    } else {
                                        $(this).hide();
                                        $(this).find('input').prop('checked', false);
                                    }
                                });
                                $this.find('.content-filter-option.sub_filtered:visible input').prop('checked', true);
                                $this.find('.content-filter-option:hidden input').prop('checked', false);
                            }
                            checkAllStatus();
                            onSearch = false;
                        });


                        $this.find('.content-filter-option .' + identifier).on('change', function (event) {
                            if (!onSearch) {
                                checkAllStatus();
                            }
                        });

                    }

                    $(document).mouseup(function (e) {
                        if (!$this.is(e.target) && $this.has(e.target).length === 0) {
                            if ($this.hasClass('filter-opened')) {
                                $this.removeClass('filter-opened');
                                var filters = getFilters();
                                getFilteredState();
                                options.onAfter(filters);
                            }
                        }
                    });

                    $this.find('.content-filter-order-option').click(function () {
                        if (!$(this).hasClass('active')) {
                            $this.find('.content-filter-order-option').removeClass('active');
                            $(this).addClass('active');
                        } else {
                            $this.find('.content-filter-order-option').removeClass('active');
                        }
                        if (options.on_after_each_time) {
                            var filters = getFilters();
                            getFilteredState();
                            options.onAfter(filters);
                        }
                    });
                    //*/
                    isInited = true;
                }
            });
        }
    };

})(jQuery);