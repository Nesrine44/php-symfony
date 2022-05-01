var mail_contact = getContactMail();
var before_check_update = null;


/**
 * ajax_on_error
 *
 * @param resultat
 * @param statut
 * @param e
 * @param reload_on_302
 */
function ajax_on_error(resultat, statut, e, reload_on_302) {
    reload_on_302 = typeof reload_on_302 !== 'undefined' ? reload_on_302 : false;
    if (check_if_user_is_dev() && statut != "abort") {
        /*
        console.log('ajax_on_error');
        console.log('resultat', resultat);
        console.log('statut', statut);
        console.log('statut', e);
        */
    }
    if ($('.content-custom-maintenance').length > 0) {
        return false;
    }
    // Force reload
    if (resultat.status == 503 && resultat.responseText == 'force_reload') {
        location.reload();
        return true;
    }
    var no_reload_button = ($('.reload-unique-button').length == 0);

    // no internet connexion
    if (no_reload_button && navigator.onLine == false) {
        add_flash_message('error', 'It seems you lost internet connexion.<br> Please check it or <a class="reload-unique-button" href="' + window.location.href + '">reload this page</a>', false);
    }
    // is redirect 302
    var is_redirect_302 = ((statut == 'parsererror' && resultat.status == 200 && resultat.statusText == 'OK') ||
        e == 'SyntaxError: Unexpected token < in JSON at position 0');

    if (is_redirect_302) {
        if (reload_on_302) {
            location.reload();
        } else if (no_reload_button) {
            $('#main').removeClass('inner-loading');
            add_flash_message('error', 'It seems you lost session.<br>Please <a class="reload-unique-button" href="' + window.location.href + '">reload this page</a>', false);
        }
    }
    return is_redirect_302;
}


/**
 * Capitalize methode
 * @returns {string}
 */
String.prototype.capitalize = function() {
    return this.replace(/(?:^|\s)\S/g, function(a) {
        return a.toUpperCase();
    });
};
/**
 * yyyymmdd
 * @returns {string}
 */
Date.prototype.yyyymmdd = function() {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();

    return [this.getFullYear(),
        (mm > 9 ? '' : '0') + mm,
        (dd > 9 ? '' : '0') + dd
    ].join('-');
};


/**
 * Retourne une url propre à être utilisée
 * @param $url
 * @return string
 */
function return_proper_link(url) {
    if (url.indexOf('http') === -1) {
        return "http://" + url;
    } else {
        return url;
    }
}

/**
 * Retourne la photo gravatar par rapport à un email
 * @param email
 * @param s
 * @returns {string}
 */
function pri_get_gravatar(email, s) {
    var base_url = ($('#main').attr('data-is-http') == 'http') ? 'http://' + location.host : 'https://' + location.host;
    var defaut = encodeURI(base_url + '/images/default/user.png');
    var url = 'https://www.gravatar.com/avatar/';
    var add = email.trim();
    add = add.toLowerCase();
    url += md5(add);
    url += "?s=" + s + "&d=" + defaut + "&r=g";
    return url;
}

/**
 * Check if tab financial is current LE.
 * @returns {boolean}
 */
function check_if_tab_financial_is_current_le() {
    var tab_financial_date = $("#business-financial-table").attr('data-financial-date');
    var current_financial_date = get_financial_date();
    return (tab_financial_date == current_financial_date);
}

/**
 * call_custom_action_tidio_chat.
 *
 * @param id
 * @returns {boolean}
 */
function call_custom_action_tidio_chat(id) {
    var is_tidio_enabled = ($('#main').attr('data-tidio-enabled') == '1');
    if (!is_tidio_enabled) {
        return false;
    }
    var id_innovation = parseInt($('#detail-id-innovation').val());
    if (id == '#elevator-pitch') {
        tidioChatApi.track('elevatorpitch_completed', document.tidioIdentify);
        create_metrics(id_innovation, METRICS_ACTION_ID_TIDIO_EVENT_ELEVATORPITCH_COMPLETED);
    } else if (id == '#financials' && check_if_tab_financial_is_current_le()) {
        tidioChatApi.track('financial_completed', document.tidioIdentify);
        create_metrics(id_innovation, METRICS_ACTION_ID_TIDIO_EVENT_FINANCIAL_COMPLETED);
    }
    return false;
}



/**
 * Scroll vers le nouveau tr updaté
 */
function goToNewUpdate() {
    if ($('.new-update').length > 0) {
        var newTop = $(".new-update").offset().top - 200;
        $("html, body").animate({
            scrollTop: newTop
        });
        setTimeout(function() {
            $('.new-update').removeClass('new-update');
        }, 1400);
    }
}

function returnStageById(current_stage_id) {
    var ret = {
        0: '',
        1: 'discover',
        2: 'ideate',
        3: 'experiment',
        4: 'incubate',
        5: 'scale_up',
        6: '',
        7: 'discontinued',
        8: 'permanent_range'
    };
    if (ret.hasOwnProperty(current_stage_id)) {
        return ret[current_stage_id];
    }
    return current_stage_id;
}

function returnGoodStage(current_stage) {
    var ret = {
        "discover": 'Discover',
        "ideate": 'Ideate',
        "experiment": 'Experiment',
        "incubate": 'Incubate',
        "scale_up": 'Scale up',
        'empty': '',
        "Success story": 'Success story',
        "discontinued": 'Discontinued',
        "permanent_range": 'Permanent range',
        'frozen': 'Frozen'
    };
    if (ret.hasOwnProperty(current_stage)) {
        return ret[current_stage];
    }
    return current_stage;
}

function get_all_consurmer_opportunities_keys() {
    return [
        'human_authenticity',
        'easy_at_home_everywhere',
        'shaking_the_codes',
        'power_to_consumers',
        'feminine_identity',
        'doing_good',
        'better_for_me'
    ];
}

function pri_get_libelle_consumer_opportunity_by_id(id) {
    var ret = {
        '1': 'Human Authenticity',
        '2': 'Easy at Home & Everywhere',
        '3': 'Shaking The Codes',
        '4': 'Power to Consumers',
        '5': 'Feminine Identity',
        '6': 'Doing Good',
        '7': 'Better for Me',
        '8': 'Tactical Innovation'
    };
    if (global_wording['version'] == 'corellis') {
        ret = {
            '1': 'Money makers',
            '2': 'Mindfulness training',
            '3': 'Ready-to-use',
            '4': 'Mobility tech',
            '5': 'User care',
            '6': 'Make management',
            '7': 'Synthetic biology',
            '8': 'Computer vision',
            '9': 'Brick-and-mortar retail technology'
        };
    }
    if (ret.hasOwnProperty(id)) {
        return ret[id];
    }
    return 'Empty';
}

/**
 * get innovation image by type and value
 * @param type
 * @param value
 * @returns {*}
 */
function pri_get_innovation_image(type, value) {
    if (type == 'current_stage') {
        switch (value) {
            case 'discover':
                return 'stage-dis-small@2x.png';
            case 'ideate':
                return 'stage-ide-small@2x.png';
            case 'experiment':
                return 'stage-exp-small@2x.png';
            case 'incubate':
                return 'stage-inc-small@2x.png';
            case 'scale_up':
                return 'stage-sca-small@2x.png';
            case 'discontinued':
                return 'stage-discont-small@2x.png';
            case 'permanent_range':
                return 'stage-permanent-small@2x.png';
            default:
                return 'stage-none-small@2x.png';
        }
    } else if (type == 'classification_type') {
        switch (value) {
            case 'Product':
                return 'class-pro-small@2x.png';
            case "Service":
                return 'class-ser-small@2x.png';
            default:
                return 'class-none-small@2x.png';
        }
    } else if (type == 'innovation_type') {
        switch (value) {
            case 'Stretch':
                return 'type-str-small@2x.png';
            case 'Incremental':
                return 'type-inc-small@2x.png';
            case 'Breakthrough':
                return 'type-bre-small@2x.png';
            default:
                return 'type-none-small@2x.png';
        }
    } else if (type == 'status') {
        switch (value) {
            case 1:
                return 'pic-draft@2x.png';
            case 2:
                return 'pic-submitted@2x.png';
            case 3:
                return 'pic-validated@2x.png';
            case 4:
                return 'pic-new@2x.png';
            case 5:
                return 'pic-modified@2x.png';
            default:
                return null;
        }
    } else if (type == 'online') {
        if (value == '1' || value == true) {
            return 'pic-online@2x.png';
        } else {
            return null;
        }
    } else if (type == 'consumer_opportunity') {
        if (global_wording == 'corellis') {
            //TODO
            switch (value) {
                case '1':
                case 1:
                    return null;
                case '2':
                case 2:
                    return null;
                case '3':
                case 3:
                    return null;
                case '4':
                case 4:
                    return null;
                case '5':
                case 5:
                    return null;
                case '6':
                case 6:
                    return null;
                case '7':
                case 7:
                    return null;
                case '8':
                case 8:
                    return null;
                case '9':
                case 9:
                    return null;
                default:
                    return null;
            }
        }
        switch (value) {
            case '1':
            case 1:
                return 'authenticity-small@2x.png';
            case '2':
            case 2:
                return 'home-small@2x.png';
            case '3':
            case 3:
                return 'codes-small@2x.png';
            case '4':
            case 4:
                return 'consumer-small@2x.png';
            case '5':
            case 5:
                return 'feminine-small@2x.png';
            case '6':
            case 6:
                return 'good-small@2x.png';
            case '7':
            case 7:
                return 'better-small@2x.png';
            case '8':
            case 8:
                return 'other-small@2x.png';
            default:
                return null;
        }
    } else if (type == 'business_drivers') {
        switch (value) {
            case 'RTD / RTS':
                return 'icon-rtd_rts-light.png';
            case 'No & Low Alcohol':
                return 'icon-no_low_alcohol-light.png';
            case 'Specialty':
                return 'icon-specialty-light.png';
            default:
                return null;
        }
    }
}
/**
 * Retourne le mailto pour le lien de contact
 * @returns {string}
 */
function getContactMail() {
    return 'mailto:' + $('#main').attr('data-contact-mail');
}

/**
 * get_current_tab_target
 * @returns {*}
 */
function get_current_tab_target() {
    if (!$('.pri-tab').hasClass('ui-tabs')) {
        return undefined;
    }
    return $('.pri-tab .pri-tab-menu li.ui-state-active a').attr('href').replace('#', '');
}

/**
 * get_loading_spinner_html
 * @returns {*}
 */
function get_loading_spinner_html(added_class) {
    added_class = (typeof added_class != 'undefined') ? added_class : '';
    return '<div class="loading-spinner ' + added_class + '"></div>';
}

/**
 * get_financial_data_html_for_table
 * @param value
 * @param unit
 * @param movement
 * @param state
 * @returns {string}
 */
function get_financial_data_html_for_table(value, unit, movement, state) {
    unit = (typeof unit != 'undefined') ? unit : '';
    movement = (typeof movement != 'undefined') ? movement : null;
    state = (typeof state != 'undefined') ? state : 'auto';
    var is_valid = (value !== null && value !== "" && value != "N/A");
    if (typeof value === 'string') {
        value = value.replaceAll(' ', '');
    }
    var calc_value = (is_valid) ? value : 0;
    var is_negative = (state == 'negative') ? true : (calc_value < 0);
    calc_value = Math.abs(calc_value);
    var value_display = number_format(calc_value, 0, '.', ' ');
    var out = [],
        o = -1;
    out[++o] = '<span class="financial-data-in-table">';
    if (!is_valid) {
        value = (value !== null) ? value : '';
        out[++o] = '<span class="value">' + value + '</span>';
    } else {
        if (value !== 0 && movement) {
            var classe = (movement >= 0) ? 'positive' : 'negative';
            out[++o] = '<span class="consolidation-movement ' + classe + '">' + movement + '%</span>';
        }
        if (is_negative) {
            out[++o] = '<span class="state-negative">(</span>';
        }
        out[++o] = '<span class="value">' + value_display + '</span>';
        if (is_negative) {
            out[++o] = '<span class="state-negative">)</span>';
        }
        if (unit != '') {
            out[++o] = '<span class="unit">' + unit + '</span>';
        }
    }
    out[++o] = '</span>';
    return out.join('');
}
/**
 * String toCssClass
 * @param search
 * @param replacement
 * @returns {string}
 */
String.prototype.toCssClass = function() {
    var target = this;
    if (!target) {
        return 'empty';
    }
    target = target.toLowerCase().trim().replaceAll('/', '').replaceAll('&', '');
    target = target.replaceAll(' ', '_');
    target = target.replaceAll('__', '_');
    return target;
};

/**
 * str replace for all
 * @param search
 * @param replacement
 * @returns {string}
 */
String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

/**
 * add_flash_message.
 *
 * @param type
 * @param content
 * @param auto_close
 */
function add_flash_message(type, content, auto_close) {
    if (!content) {
        return;
    }
    auto_close = (typeof auto_close != 'undefined') ? auto_close : true;
    var unique_id = 'flash-message-' + get_current_timestamp();
    var flash_message = $('<div class="flash-message ' + type + '" id="' + unique_id + '"><div class="content"><span class="font-montserrat-700">' + content + '</span><div class="close-flash-message"></div></div></div>').hide().slideUp(300);
    $('.content-flash-messages').append(flash_message);
    flash_message.slideDown(300);
    if (auto_close) {
        auto_close_message(unique_id);
    }
}

/**
 * add_saved_message.
 */
function add_saved_message() {
    var unique_id = 'flash-message-' + get_current_timestamp();
    var flash_message = $('<div class="flash-message success" id="' + unique_id + '"><div class="content"><span class="font-montserrat-700">Saved</span><div class="close-flash-message"></div></div></div>').hide().slideUp(300);
    $('.content-flash-messages').append(flash_message);
    flash_message.slideDown(300);
    var $message = $('#' + unique_id);
    setTimeout(function() {
        $message.hide('fade');
        setTimeout(function() {
            $message.remove();
        }, 350);
    }, 800);
}

/**
 * auto_close_message
 * @param message_id
 */
function auto_close_message(message_id) {
    var $message = $('#' + message_id);
    setTimeout(function() {
        $message.hide('fade');
        setTimeout(function() {
            $message.remove();
        }, 350);
    }, 10000);
}

/**
 * Permet de fermer les flash messages
 */
$(document).on("click", ".close-flash-message", function() {
    var $message = $(this).parent().parent();
    $message.hide('fade');
    setTimeout(function() {
        $message.remove();
    }, 350);
});



/**
 * is Integer ?
 * @param value
 */
function isInt(value) {
    if (!value) {
        return false;
    }
    var val = value.replace(/\s/g, '');
    return is.integer(Number(val));
}
/**
 * is Float ?
 * @param value
 * @returns {*}
 */
function isFloat(value) {
    var val = value.replace(/\s/g, '');
    return (is.decimal(Number(val)) || is.integer(Number(val)));
}
/**
 * is Date ?
 * @param val
 * @returns {number}
 */
function isDate(val) {
    return (Date.parse(val));
}
/**
 * is a Phone number ?
 * @param value
 * @returns {boolean}
 */
function isPhone(value) {
    if (!is.string(value)) {
        return false;
    }
    var regexPhone = /^\+(?:[0-9 ()]●?){6,30}$/;
    return (regexPhone.test(value));
}
/**
 * Met à jour le lien vers la liste
 */
function setListUrl() {
    var url = $('#logo').attr('data-url-list');
    $('#main-menu-links .menu-li-manage a').attr('href', url);
}

/**
 * check if innovation is terminated
 * @param product
 * @return {*|boolean}
 */
function innovation_is_terminated(product) {
    var current_stage = product['current_stage'];
    return (current_stage && in_array(current_stage, ['discontinued', 'permanent_range']));
}

/**
 * retourne le stage d'une innovation à ajouter à l'url envoyé à mouseflow
 * @param product
 * @returns {*}
 */
function get_proper_stage_for_url(product) {
    var current_stage_id = product['current_stage_id'];
    if (current_stage_id) {
        var ret = {
            "0": 'discover',
            "1": 'ideate',
            "2": 'experiment',
            "3": 'incubate',
            "4": 'scale-up',
            "5": 'success-story',
            "6": 'discontinued',
            "7": 'permanent-range'
        };
        if (ret.hasOwnProperty(current_stage_id)) {
            return "&stage=" + ret[current_stage_id];
        }
    }
    return "";
}

/**
 * retourne le tab d'une url à ajouter à l'url envoyé à mouseflow
 * @param current_path
 * @returns {*}
 */
function get_proper_tab_for_url(current_path) {

    if (current_path.indexOf('/explore/') !== -1) {
        if (current_path.indexOf('/tab/') !== -1) {
            var exploder = current_path.split("/tab/");
            return '&tab=' + exploder[1];
        }
        return '&tab=overview';
    }
    return '';
}

/**
 * innovation_is_out_of_funnel
 * @param innovation
 * @returns {*|boolean}
 */
function innovation_is_out_of_funnel(innovation) {
    return (innovation['is_frozen'] || (in_array(innovation['current_stage'], ['discontinued', 'permanent_range'])));
}

/**
 * retourne le classification_type d'une innovation à ajouter à l'url envoyé à mouseflow
 * @param product
 * @returns {*}
 */
function get_proper_classification_type_for_url(product) {
    if (product && check_if_innovation_is_a_service(product)) {
        return '&classification=service';
    }
    return "&classification=product";
}

function user_can_edit_this_innovation(product) {
    var user_uid = parseInt($('#main').attr('data-uid'));
    if (check_if_user_is_hq()) {
        return 'hq';
        //return true;
    }
    if (product['contact'] && product['contact']['uid'] == user_uid) {
        return 'contact';
        //return true;
    }
    var user_innovation_right = get_user_innovation_right_for_innovation(product);
    if (user_innovation_right) {
        return (user_innovation_right['right'] == 'write');
    }
    return false;
}

/**
 * get_current_timestamp.
 *
 * @returns {number}
 */
function get_current_timestamp() {
    return Math.round(new Date().getTime() / 1000);
}

/**
 * check_background_image_before_loading
 * @param target
 * @param background_image
 * @param default_background_image
 */
function check_background_image_before_loading(target, background_image, default_background_image) {
    if (default_background_image) {
        $(target).css('background-image', 'url(' + default_background_image + ')');
    }
    $('<img/>').attr('src', background_image).on('load', function(e) {
        $(this).remove(); // prevent memory leaks as @benweet suggested
        $(target).css('background-image', 'url(' + background_image + ')');
    });
}

/**
 * load_background_images
 */
function load_background_images() {
    $('.loading-bg:not(.bg-loaded)').each(function() {
        var background_image = $(this).attr('data-bg');
        var default_background_image = $(this).attr('data-default-bg');
        $(this).addClass('bg-loaded').removeAttr('data-bg').removeAttr('data-default-bg');
        check_background_image_before_loading(this, background_image, default_background_image);
    });
}

function user_has_access_to_manage_innovation(product) {
    var user_uid = parseInt($('#main').attr('data-uid'));
    if (check_if_user_is_hq()) {
        return 'hq';
    }
    if (check_if_user_is_management()) {
        return 'management';
    }
    if (product['contact'] && product['contact']['uid'] == user_uid) {
        return 'contact';
    }
    if (get_user_innovation_right_for_innovation(product)) {
        return true;
    }
    return false;
}


/**
 * check_if_user_has_new_business_model_access.
 *
 * @returns {boolean}
 */
function check_if_user_has_new_business_model_access() {
    if (check_if_user_is_hq() || check_if_user_is_management()) {
        return true;
    }
    if (!user_rights) {
        return false;
    }
    for (var i = 0; i < user_rights.length; i++) {
        var innovation = getInnovationById(user_rights[i]['innovation_id']);
        if (innovation && check_if_innovation_is_nba(innovation)) {
            return true;
        }
    }
    return false;
}

function user_is_team_of_innovation(product) {
    var user_uid = parseInt($('#main').attr('data-uid'));
    if (product['contact'] && product['contact']['uid'] == user_uid) {
        return true;
    }
    if (get_user_innovation_right_for_innovation(product)) {
        return true;
    }
    return false;
}

function get_user_innovation_right_for_innovation(product) {
    if (!user_rights) {
        return null;
    }
    var id = product['id'];
    for (var i = 0; i < user_rights.length; i++) {
        if (product['id'] == user_rights[i]['innovation_id']) {
            return user_rights[i];
        }
    }
    return null;
}

function user_is_entity_team_leader(product) {
    if (check_if_user_is_hq()) {
        return true;
    }
    if (check_if_user_is_management()) {
        return true;
    }
    var user_innovation_right = get_user_innovation_right_for_innovation(product);
    if (!user_innovation_right) {
        return false;
    }
    return (in_array(user_innovation_right['role'], ['team_leader', 'entity_leader', 'financial_leader']));
}

/**
 * js equivalent to php htmlentities
 * @param str
 * @returns {string}
 */
function htmlentities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/**
 * js equivalent to php number_format
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_sep
 * @returns {string}
 */
function number_format(number, decimals, dec_point, thousands_sep) {
    dec_point = (typeof dec_point != 'undefined') ? dec_point : '.';
    thousands_sep = (typeof thousands_sep != 'undefined') ? thousands_sep : ',';
    number = number * 1; //makes sure `number` is numeric value
    var str = number.toFixed(decimals ? decimals : 0).toString().split('.');
    var parts = [];
    for (var i = str[0].length; i > 0; i -= 3) {
        parts.unshift(str[0].substring(Math.max(0, i - 3), i));
    }
    str[0] = parts.join(thousands_sep);
    return str.join(dec_point);
}

/**
 * number_format_en
 * @param nb
 * @returns {*}
 */
function number_format_en(nb) {
    if (nb == 'N/A' || !nb) {
        return 'N/A';
    }
    var abs_nb = Math.abs(nb);
    var ret = "";
    if (nb < 0) {
        ret += "(";
    }
    ret += number_format(abs_nb, 0, '.', ',');
    if (nb < 0) {
        ret += ")";
    }
    return ret;
}

/**
 * renvoi la valeur du $_GET par son nom
 * @param parameterName
 * @returns {*}
 */
function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function(item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

/**
 * splitToInt
 * @param text
 * @param splitter
 * @returns {*}
 */
function splitToInt(text, splitter) {
    return $.map(text.split(splitter), function(value) {
        return parseInt(value);
    });
}

/**
 * retourne tous les $_GET de l'url courrante
 * @returns {{}}
 */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars;
}

/**
 * retourne tous les $_GET de l'url courante pour les filtres (sans page ni order)
 * @returns {{}}
 */
function getUrlVarsForFilter() {
    var vars = [];
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        if (key != 'limit' && key != 'page' && key.indexOf('order[') === -1) {
            var the_val = {}
            the_val[key] = value;
            vars.push(the_val);
        }
    });
    return vars;
}

/**
 * js equivalent to php ucfirst
 * @param string
 * @returns {string}
 */
function ucFirst(string) {
    return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
}

/**
 * js equivalent to php in_array
 * @param needle
 * @param haystack
 * @returns {boolean}
 */
function in_array(needle, haystack) {
    if (!haystack) {
        return false;
    }
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

/**
 * js equivalent to php gmdate
 * @param format
 * @param timestamp
 * @return {*}
 */
function gmdate(format, timestamp) {
    var expTime = new Date(parseInt(timestamp) * 1000);
    var m = expTime.getUTCMonth() + 1;
    var mm = (m < 10) ? "0" + m : m;
    var m_key = expTime.getUTCMonth();
    var d = expTime.getUTCDate();
    var dd = (d < 10) ? '0' + d : d;
    var y = expTime.getUTCFullYear();
    var yy = (y + "").slice(-2);
    var h = expTime.getHours();
    var i = expTime.getMinutes();
    var s = expTime.getSeconds();
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dev'];
    var monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    if (format == "Y-m-d" || format == 'yy-mm-dd') {
        return y + '-' + mm + '-' + d;
    } else if (format == "Y/m/d") {
        return y + '/' + mm + '/' + d;
    } else if (format == "m/d/Y") {
        return mm + '/' + d + '/' + y;
    } else if (format == "m/y") {
        return mm + '/' + yy;
    } else if (format == "M Y") {
        return months[m_key] + ' ' + y;
    } else if (format == "d M Y") {
        return d + ' ' + (months[m_key]).toLowerCase() + ' ' + y;
    } else if (format == "M dd, Y") {
        return ucFirst(months[m_key]) + ' ' + dd + ', ' + y;
    } else if (format == "F Y") {
        return (monthNames[m_key]) + ' ' + y;
    } else {
        return timestamp;
    }
}

/**
 * js equivalent to php strpos
 * @param haystack
 * @param needle
 * @param offset
 * @return {*}
 */
function strpos(haystack, needle, offset) {
    var i = (haystack + '')
        .indexOf(needle, (offset || 0))
    return i === -1 ? false : i
}

/**
 * js equivalent to php substr
 * @param str
 * @param start
 * @param len
 * @return {*}
 */
function substr(str, start, len) {
    str += ''
    var end = str.length

    var iniVal = (typeof require !== 'undefined' ? require('../info/ini_get')('unicode.emantics') : undefined) || 'off'

    if (iniVal === 'off') {
        if (start < 0) {
            start += end
        }
        if (typeof len !== 'undefined') {
            if (len < 0) {
                end = len + end
            } else {
                end = len + start
            }
        }
        if (start >= str.length || start < 0 || start > end) {
            return false
        }

        return str.slice(start, end)
    }
    var i = 0
    var allBMP = true
    var es = 0
    var el = 0
    var se = 0
    var ret = ''

    for (i = 0; i < str.length; i++) {
        if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
            allBMP = false
            break
        }
    }

    if (!allBMP) {
        if (start < 0) {
            for (i = end - 1, es = (start += end); i >= es; i--) {
                if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
                    start--
                    es--
                }
            }
        } else {
            var surrogatePairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g
            while ((surrogatePairs.exec(str)) !== null) {
                var li = surrogatePairs.lastIndex
                if (li - 2 < start) {
                    start++
                } else {
                    break
                }
            }
        }

        if (start >= end || start < 0) {
            return false
        }
        if (len < 0) {
            for (i = end - 1, el = (end += len); i >= el; i--) {
                if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
                    end--
                    el--
                }
            }
            if (start > end) {
                return false
            }
            return str.slice(start, end)
        } else {
            se = start + len
            for (i = start; i < se; i++) {
                ret += str.charAt(i)
                if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
                    // Go one further, since one of the "characters" is part of a surrogate pair
                    se++
                }
            }
            return ret
        }
    }
}

/**
 * Retourne le trimestre courant
 * @return {number}
 */
function get_current_trimestre() {
    return parseInt($('#main').attr('data-current-trimester'));
}

/**
 * Fonction qui initialise le site
 */
function init() {
    $('input, textarea').placeholder();
    load_background_images();
    setListUrl();
}

/**
 * check if is open date now
 * @return {boolean}
 */
function check_if_is_open_date() {
    var dateObj = new Date();
    var month = dateObj.getUTCMonth() + 1; //months from 1-12
    month = (month >= 10) ? month : "0" + month;
    var day = dateObj.getUTCDate();
    day = (day >= 10) ? day : "0" + day;
    var year = dateObj.getUTCFullYear();
    var date_now = year + "-" + month + "-" + day;
    var open_date = $('#main').attr('data-open-date');
    var closure_date = $('#main').attr('data-closure-date');
    if (date_now < open_date || date_now > closure_date) {
        return false;
    } else {
        return true;
    }
}

/**
 * check if is open date before
 * @return {boolean}
 */
function check_if_is_before_open_date() {
    var dateObj = new Date();
    var month = dateObj.getUTCMonth() + 1; //months from 1-12
    month = (month >= 10) ? month : "0" + month;
    var day = dateObj.getUTCDate();
    day = (day >= 10) ? day : "0" + day;
    var year = dateObj.getUTCFullYear();
    var date_now = year + "-" + month + "-" + day;
    var open_date = $('#main').attr('data-open-date');
    return (date_now < open_date);
}
/**
 * check if is data capture toast enabled
 * @return {boolean}
 */
function check_if_is_data_capture_toast_enabled() {
    return ($('#main').attr('data-data-capture-toast-enabled') == '1');
}

/**
 * check_if_user_is_dev
 * @return {boolean}
 */
function check_if_user_is_dev() {
    return (in_array('dev', user_roles));
}

/**
 * check_if_user_is_hq
 * @return {boolean}
 */
function check_if_user_is_hq() {
    return (in_array('hq', user_roles));
}

/**
 * check_if_user_is_managing_director
 * @return {boolean}
 */
function check_if_user_is_managing_director() {
    return (in_array('managing_director', user_roles));
}

/**
 * check_if_user_is_md
 * @return {boolean}
 */
function check_if_user_has_manage_list_access() {
    if (check_if_user_is_hq() || check_if_user_is_management()) {
        return true;
    }
    return (user_rights.length > 0);
}


/**
 * check_if_user_has_no_role
 * @return {boolean}
 */
function check_if_user_has_no_role() {
    return (user_roles.length === 0);
}

/**
 * check_if_user_is_management
 * @return {boolean}
 */
function check_if_user_is_management() {
    return (in_array('management', user_roles));
}

/**
 * check_if_user_has_monitor_access
 * @return {boolean}
 */
function check_if_user_has_monitor_access() {
    if (check_if_user_is_hq() || check_if_user_is_management()) {
        return true;
    }
    return (user_rights.length > 0);
}

/**
 * check_if_user_is_readonly
 * @return {boolean}
 */
function check_if_user_is_readonly() {
    return (in_array('readonly', user_roles));
}

/**
 * check_if_user_is_readonly
 * @return {boolean}
 */
function check_if_user_is_innovation_contact(innovation) {
    var user_uid = parseInt($('#main').attr('data-uid'));
    return (innovation['contact']['uid'] == user_uid);
}

/**
 * check_if_user_is_maker
 * @return {boolean}
 */
function check_if_user_is_maker() {
    return ($('#main').hasClass('maker'));
}
/**
 * check_if_data_capture_is_opened
 * @return {boolean}
 */
function check_if_data_capture_is_opened() {
    return ($('#main').hasClass('data-opened'));
}

function check_if_data_quali_capture_is_opened() {
    return ($('#main').hasClass('data-quali-opened'));
}

function pri_user_has_edition_rights() {
    return ($('#main').hasClass('edition-rights'));
}

function get_libelle_for_nb_markets(type) {
    var ret = '';
    var actual_date = get_financial_date();
    var actual_date_explode = actual_date.split("-");
    var year = (parseInt(actual_date_explode[0]) - 1999);
    var month = parseInt(actual_date_explode[1]);
    if (type == 1) {
        ret = 'B';
        ret += (month < 6) ? year : (year + 1);
    } else if (type == 2) {
        ret = 'FY';
        ret += (month < 6) ? (year + 3) : (year + 4);
    }
    return ret;
}

/**
 * get_financial_date
 * @return {string}
 */
function get_financial_date(type) {
    var current_date = $('#main').attr('data-displayable-date');
    var current_trimester = get_current_trimestre();
    var trimester = current_trimester;
    if (type == 'previous') {
        trimester = current_trimester - 1;
        if (trimester <= 0) {
            trimester = 4;
        }
        return get_financial_date_by_old_trimester_trimester_and_date(current_trimester, trimester, current_date);
    } else if (type == 'next') {
        trimester = current_trimester - 1;
        if (trimester >= 5) {
            trimester = 1;
        }
        return get_financial_date_by_old_trimester_trimester_and_date(current_trimester, trimester, current_date);
    } else {
        return current_date;
    }
}


/**
 * get_financial_date_by_trimester
 * @param trimestre
 * @return {string}
 */
function get_financial_date_by_trimester(trimestre) {
    var financial_date = get_financial_date();
    var date_explode = financial_date.split('-');
    var year = parseInt(date_explode[0]);
    if (trimestre == 2) {
        //year -= 1;
    }
    var dateM = '';
    if (trimestre == 3) {
        dateM = '01';
    } else if (trimestre == 4) {
        dateM = '04';
    } else if (trimestre == 1) {
        dateM = '07';
    } else {
        dateM = 10;
    }
    return year + '-' + dateM + '-15';
}

/**
 * get_financial_date_by_old_trimester_trimester_and_date
 * @param trimestre
 * @return {string}
 */
function get_financial_date_by_old_trimester_trimester_and_date(old_trimestre, trimestre, financial_date) {
    var date_explode = financial_date.split('-');
    var year = parseInt(date_explode[0]);
    if (trimestre == 2) {
        //year -= 1;
    }
    var dateM = '';
    if (trimestre == 3) {
        dateM = '01';
        if (old_trimestre == 2) {
            year += 1;
        }
    } else if (trimestre == 4) {
        dateM = '04';
    } else if (trimestre == 1) {
        dateM = '07';
    } else {
        dateM = 10;
        if (old_trimestre == 3) {
            year -= 1;
        }
    }
    return year + '-' + dateM + '-15';
}
/**
 * get_libelle_current_le
 * @return {string}
 */
function get_libelle_current_le() {
    return $('#main').attr('data-libelle-current-le');
}

/**
 * get_libelle_last_a
 * @return {string}
 */
function get_libelle_last_a() {
    return $('#main').attr('data-libelle-last-a');
}

/**
 * retourne le libelle trimestriel de la date courante
 * @param the_date
 * @return {string}
 */
function get_financial_date_libelle(the_date) {
    var date = new Date(the_date);
    var date_explode = the_date.split('-');
    var year = parseInt(date_explode[0]);
    year = (year - 2000);
    var month = (parseInt(date.getMonth()) + 1);
    var futur_year = year + 1;

    if (month < 4) {
        return 'LE2 ' + year;
    } else if (month < 7) {
        return 'LE3 ' + year;
    } else if (month < 10) {
        return 'B' + futur_year;
    } else {
        return 'LE1 ' + futur_year;
    }
}

/**
 * retourne le libelle FY de la date courante
 * @param the_date
 * @return {string}
 */
function get_financial_date_FY(the_date) {
    var date = new Date(the_date);
    var year = (parseInt(date.getFullYear()) - 2000);
    var month = (parseInt(date.getMonth()) + 1);
    var futur_year = year + 1;

    if (month < 4) {
        return 'FY' + year;
    } else if (month < 7) {
        return 'FY' + futur_year;
    } else if (month < 10) {
        return 'FY' + futur_year;
    } else {
        return 'FY' + futur_year;
    }
}


// Warn if overriding existing method
if (Array.prototype.equals)
    console.warn("Overriding existing Array.prototype.equals. Possible causes: New API defines the method, there's a framework conflict or you've got double inclusions in your code.");
// attach the .equals method to Array's prototype to call it on any array
Array.prototype.equals = function(array) {
        // if the other array is a falsy value, return
        if (!array)
            return false;

        // compare lengths - can save a lot of time
        if (this.length != array.length)
            return false;

        for (var i = 0, l = this.length; i < l; i++) {
            // Check if we have nested arrays
            if (this[i] instanceof Array && array[i] instanceof Array) {
                // recurse into the nested arrays
                if (!this[i].equals(array[i]))
                    return false;
            } else if (this[i] != array[i]) {
                // Warning - two different object instances will never be equal: {x:20} != {x:20}
                return false;
            }
        }
        return true;
    }
    // Hide method from for-in loops
Object.defineProperty(Array.prototype, "equals", { enumerable: false });

Array.prototype.diff = function(a) {
    return this.filter(function(i) { return a.indexOf(i) < 0; });
};

/**
 * rawSplit
 */
function rawSplit(array, separator) {
    if (!array || !separator)
        return [];
    var splitted = array.split(separator);
    var ret = [];
    for (var i = 0; i < splitted.length; i++) {
        ret[i] = decodeURI(splitted[i]);
    }
    return ret;
}

/**
 * launchBlueArrowScroll
 */
function launchBlueArrowScroll() {
    clearTimeout($.data(this, 'scrollTimer'));
    $.data(this, 'scrollTimer', setTimeout(function() {
        if ((
                $('#main').hasClass('explore_detail') ||
                $('#main').hasClass('welcome_page')
            ) && $(window).scrollTop() < 50) {
            $('.button-scroll-to-bottom').fadeIn();
        }
    }, 5000));
}

function init_scroll_blue_arrow() {
    /*
    $(window).scroll(function () {
        launchBlueArrowScroll();
        $('.button-scroll-to-bottom').fadeOut();
    });
    */
}

function launchFunctionWhenDOMIsReady(a_function) {
    if ($.isReady) {
        setTimeout(function() {
            a_function();
        }, 25);
    } else {
        $(document).ready(function() {
            setTimeout(function() {
                a_function();
            }, 25);
        });
    }
}
/**
 * Met à jour les classes dynamiques de la div#main
 * @param new_classes
 */
function update_main_classes(new_classes) {
    if (new_classes == '') {
        return;
    }
    var classes_to_check = [
        'data-opened',
        'data-closed',
        'data-quali-opened',
        'data-quali-closed',
        'maker',
        'no-role',
        'hq',
        'management',
        'readonly',
        'entity_team_leader',
        'edition-rights'
    ];
    var classes_to_remove = '';
    for (var u = 0; u < classes_to_check.length; u++) {
        if (new_classes.indexOf(classes_to_check[u]) === -1) {
            classes_to_remove += ' ' + classes_to_check[u];
        }
    }
    $('#main').removeClass(classes_to_remove).addClass(new_classes);
}

/**
 * removejscssfile from DOM
 * @param filename
 * @param filetype
 */
function removejscssfile(filename, filetype) {
    var targetelement = (filetype == "js") ? "script" : (filetype == "css") ? "link" : "none" //determine element type to create nodelist from
    var targetattr = (filetype == "js") ? "src" : (filetype == "css") ? "href" : "none" //determine corresponding attribute to test for
    var allsuspects = document.getElementsByTagName(targetelement)
    for (var i = allsuspects.length; i >= 0; i--) { //search backwards within nodelist for matching elements to remove
        if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1) {
            allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
            console.log(filename + " removed");
        }
    }
}

function uniq_fast(a) {
    var seen = {};
    var out = [];
    var len = a.length;
    var j = 0;
    for (var i = 0; i < len; i++) {
        var item = a[i];
        if (seen[item] !== 1) {
            seen[item] = 1;
            out[j++] = item;
        }
    }
    return out;
}

function get_market_checklist(markets) {
    var response = [];
    for (var i = 0; i < markets.length; i++) {
        var a_market = markets[i];
        var new_data = {};
        new_data.value = a_market.id;
        new_data.text = a_market.text;
        response.push(new_data);
    }
    return response;
}

function get_graph_libelle_best_position(positions, f0_case_adder) {
    f0_case_adder = (typeof f0_case_adder === 'undefined') ? -6 : f0_case_adder;
    var new_position = positions.current;
    var nb_char = positions.label.length;
    var standard = (nb_char == 3) ? 0 : 4;
    var the_case = null;
    if (positions.previous) {
        if (new_position.y > positions.previous.y) {
            if (new_position.x < positions.previous.x) {
                the_case = "P0";
                new_position.y = new_position.y + (f0_case_adder - 4);
            } else {
                the_case = "P1";
                new_position.x = new_position.x + (20 + standard);
                new_position.y = new_position.y + 10;
            }
        }
    }
    if (positions.future) {
        if (new_position.y > positions.future.y) {
            the_case = 'F';
            if (new_position.x < positions.future.x) {
                the_case = 'F0';
                new_position.x = new_position.x - (20 + standard);
                new_position.y = new_position.y + f0_case_adder;
            } else {
                the_case = 'F1';
                new_position.x = new_position.x + (11 + standard);
                new_position.y = new_position.y + 6;
            }
        }
    }
    if (!the_case) {
        if (new_position.y < 30) {
            the_case = 'N0';
            new_position.x = new_position.x + (25 + standard);
        }
        if (new_position.x < 50) {
            the_case = 'N1';
            new_position.x = new_position.x + (35 + standard);
            new_position.y = new_position.y + 20;
        } else if (nb_char > 3) {
            the_case = 'N2';
            new_position.x = new_position.x + 20;
            new_position.y = new_position.y + 6;
        } else {
            the_case = 'N3';
            new_position.x = new_position.x + 10;
            new_position.y = new_position.y - 2;
        }
    }

    new_position.case = the_case;
    var changes = {
        label: positions.label,
        default: positions.current,
        new: new_position,
        all: positions
    };
    //console.log(changes);

    return new_position;
}


/**
 * Permet de rechercher un object dans une collection par son id Drupal
 * utilisé par au moins la fonction set_libelle_h1_for_list()
 * @param collection
 * @param id
 * @returns object|null
 */
function get_object_on_collection_by_id(collection, id) {
    var the_object = null;
    for (var i = 0; i < collection.length; i++) {
        var an_object = collection[i];
        if (an_object['id'] == id) {
            return an_object;
        }
    }
    return the_object;
}

/**
 * Permet de retourner le player video
 * @param video_link
 * @returns {*}
 */
function get_player_video(video_link) {
    if (!video_link || video_link == '') {
        return null;
    }
    var url = null;
    var is_youtube_link = (video_link.indexOf('youtu') !== -1);
    var is_vimeo_link = (video_link.indexOf('vimeo') !== -1);

    if (is_youtube_link) {
        var id_exploder = video_link.split('/');
        if (video_link.indexOf('watch?v=') !== -1) {
            id_exploder = video_link.split('watch?v=');
        }
        url = "//www.youtube.com/embed/" + id_exploder[(id_exploder.length - 1)];
    } else if (is_vimeo_link) {
        var id_exploder = video_link.split('/');
        url = "//player.vimeo.com/video/" + id_exploder[(id_exploder.length - 1)];
    }
    if (!url) {
        return null;
    }
    var player = '<div class="content-iframe">';
    player += '<img src="/images/masks/video.png"/>';
    player += '<iframe src="' + url + '" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>';
    player += '</div>';
    return player;
}

/**
 * remove_element_from_array
 * @param array
 * @param element
 * @returns {*}
 */
function remove_element_from_array(array, element) {
    var index = array.indexOf(element);
    if (index > -1) {
        array.splice(index, 1);
    }
    return array;
}


/**
 * @param unix_timestamp
 * @return string
 */
function pri_get_nb_year_since_launch(unix_timestamp) {
    var startDate = new Date(unix_timestamp * 1000);
    var actualDate = new Date();
    var yearDiff = actualDate.getFullYear() - startDate.getFullYear();
    if (startDate.getMonth() > actualDate.getMonth()) {
        yearDiff--;
    } else if (startDate.getMonth() === actualDate.getMonth()) {
        if (startDate.getDate() > actualDate.getDate()) {
            yearDiff--;
        } else if (startDate.getDate() === actualDate.getDate()) {
            if (startDate.getHours() > actualDate.getHours()) {
                yearDiff--;
            } else if (startDate.getHours() === actualDate.getHours()) {
                if (startDate.getMinutes() > actualDate.getMinutes()) {
                    yearDiff--;
                }
            }
        }
    }
    if (yearDiff >= 1 && (startDate.getMonth() > actualDate.getMonth() || startDate.getDate() > actualDate.getDate())) {
        yearDiff += 1;
    } else if (yearDiff == 0) {
        yearDiff = 1;
    }
    return yearDiff;
}

function update_menu_active(target) {
    $('#main-menu li a').removeClass('active');
    if (target) {
        $('.' + target + ' a').addClass('active');
    }
}
/**
 * Get percent between two values
 * @param minValue
 * @param maxValue
 * @returns {number}
 */
function get_percent_between_two_values(minValue, maxValue) {
    return (minValue / maxValue) * 100;
}

function get_width_by_value_and_maxSize(value, valueArray, maxSize) {
    var maxValue = null;
    for (var i = 0; i < valueArray.length; i++) {
        if (!maxValue || (maxValue && maxValue < valueArray[i])) {
            maxValue = valueArray[i];
        }
    }
    if (maxValue && maxValue == value) {
        return maxSize;
    } else {
        var percent = get_percent_between_two_values(value, maxValue);
        return percent * maxSize / 100;
    }
}

/**
 * calculate_growth_dynamism.
 * @param volume_la
 * @param volume_le
 */
function calculate_growth_dynamism(volume_la, volume_le) {
    if (volume_la == 0) {
        return 'NEW';
    }
    return parseFloat(((volume_le - volume_la) / volume_la * 100));
}

/**
 * calculate_cm_per_case.
 * @param volume_latest_a
 * @param volume_latest
 * @param cm_latest_a
 * @param cm_latest
 */
function calculate_cm_per_case(volume_latest, cm_latest) {
    var cm_per_case_latest = 0;
    if (cm_latest && volume_latest) {
        cm_per_case_latest += cm_latest / volume_latest;
    }
    return cm_per_case_latest;
}

/**
 * calculate_cm_per_case percent.
 * @param volume_latest_a
 * @param volume_latest
 * @param cm_latest_a
 * @param cm_latest
 */
function calculate_cm_per_case_percent(volume_latest_a, volume_latest, cm_latest_a, cm_latest) {
    var cm_per_case_latest_a = calculate_cm_per_case(volume_latest_a, cm_latest_a);
    var cm_per_case_latest = calculate_cm_per_case(volume_latest, cm_latest);
    if (cm_per_case_latest_a == 0) {
        return "NEW";
    }
    var total = (cm_per_case_latest - cm_per_case_latest_a) / cm_per_case_latest_a * 100;
    return Math.round(total);
}


/**
 * calculate_level_of_investment
 * @param total_ap
 * @param ns_value
 */
function calculate_level_of_investment(total_ap, ns_value) {
    if (ns_value == 0) {
        return "NEW";
    }
    if (total_ap != 0 && ns_value != 0) {
        return (Math.abs(total_ap) / ns_value) * 100;
    }
    return 0;
}

/**
 * calculate_level_of_profitability
 * @param cm_value
 * @param ns_value
 * @returns {number}
 */
function calculate_level_of_profitability(cm_value, ns_value) {
    if (!cm_value || !ns_value) {
        return 0;
    }
    return parseFloat((cm_value / ns_value * 100));
}

function get_random_loading_citation() {
    var all_citations = other_datas['citations'];
    return all_citations[Math.floor(Math.random() * all_citations.length)];
}


/**
 * get_special_round
 * @param nb
 * @returns {number}
 */
function get_special_round(nb) {
    if (nb < 10) {
        return Number(Number(nb).toFixed(2));
    } else if (nb < 100) {
        return Number(Number(nb).toFixed(1));
    } else {
        return Math.round(nb);
    }
}

/**
 * get_special_percent_evolution_between_two_values.
 *
 * @param old_value
 * @param new_value
 * @returns {number}
 */
function get_special_percent_evolution_between_two_values(old_value, new_value) {
    if (old_value == 0) {
        return 0;
    }
    var percent_change = ((new_value - old_value) / Math.abs(old_value)) * 100;
    return get_special_round(number_format_en(percent_change));
}

/**
 * Get financial data class.
 * @param financial_data
 */
function get_financial_data_class(financial_data) {
    if (financial_data == 'NEW') {
        return 'new';
    }
    return (financial_data === 'N/A' || financial_data > 0) ? 'positive' : 'negative';
}

/**
 * get_contactable_users.
 * @returns {Array}
 */
function get_contactable_users() {
    var ret = [];
    if (!other_datas) {
        return ret;
    }
    for (var i = 0; i < other_datas['contacts_json'].length; i++) {
        if (other_datas['contacts_json'][i]['accept_contact']) {
            ret.push(other_datas['contacts_json'][i]);
        }
    }
    return ret;
}

/**
 * Calculate value creation
 */
function calculate_value_creation(value_latest_a, value_latest) {
    // ( NS LEn Y - A[Y-1] ) / A[Y-1] * 100
    if (value_latest_a == 0) {
        return "NEW";
    }
    if (value_latest != 0 && value_latest_a != 0) {
        return ((value_latest - value_latest_a) / value_latest_a) * 100;
    }
    return 0;
}

/* WORDING */
function getAllConsumerOpportunitiesClasses() {
    if (global_wording['version'] == 'corellis') {
        return "empty money_makers mindfulness_training ready_to_use mobility_tech user_care make_management synthetic_biology computer_vision brick_and_mortar_retail_technology";
    }
    return "empty human_authenticity easy_at_home_everywhere shaking_the_codes power_to_consumers feminine_identity doing_good better_for_me tactical_innovation";
}

function getAllMocClasses() {
    return "empty aperitif_and_meals soirees_chic get_togethers_with_friends nights_out";
}

function getAllBusinessDriversClasses() {
    return "empty craft rtd_rts specialty no_low_alcohol monetizing_experiences";
}

function get_all_consumers_opportunities() {
    return [{
            'id': 7,
            'libelle': 'Better for Me',
            'key': 'better_for_me'
        },
        {
            'id': 6,
            'libelle': 'Doing Good',
            'key': 'doing_good'
        },
        {
            'id': 2,
            'libelle': 'Easy at Home & Everywhere',
            'key': 'easy_at_home_everywhere'
        },
        {
            'id': 5,
            'libelle': 'Feminine Identity',
            'key': 'feminine_identity'
        },
        {
            'id': 1,
            'libelle': 'Human Authenticity',
            'key': 'human_authenticity'
        },
        {
            'id': 4,
            'libelle': 'Power to Consumers',
            'key': 'power_to_consumers'
        },
        {
            'id': 3,
            'libelle': 'Shaking The Codes',
            'key': 'shaking_the_codes'
        },
        {
            'id': 8,
            'libelle': 'Tactical Innovation',
            'key': 'tactical_innovation'
        },
        {
            'id': 0,
            'libelle': 'Empty',
            'key': 'unknown'
        }
    ];
}

/**
 * Get ids for user.
 * @returns {Array}
 */
function get_ids_for_user() {
    var id = [];
    allInnovationsDC.query().each(function(row, index) {
        if (user_is_team_of_innovation(row)) {
            id.push(row['id']);
        }
    });
    return id;
}

/**
 * Get related innovations
 * @param innovation
 * @returns {*}
 */
function get_related_innovations(innovation) {
    var user_team_ids = get_ids_for_user();
    user_team_ids.push(innovation['id']);
    return allInnovationsDC.query().filter({
        current_stage_id: 5,
        is_frozen: false,
        proper__classification_type__is: 'product',
        category: innovation['category'],
        id__not_in: user_team_ids
    }).limit(0, 3).values();
}

/**
 * get_strategic_international_brands()
 * @returns {*}
 */
function get_strategic_international_brands() {
    var brands_collection = new DataCollection(other_datas['brands_json']);
    return brands_collection.query().filter({ group_id: 1 }).order('title', false).values();
}


/**
 * getInnovationById
 * @param id
 * @return {null}
 */
function getInnovationById(id, full_data) {
    if (typeof full_data === 'undefined') {
        full_data = false;
    }
    id = parseInt(id);
    var data_check = (!full_data) ? manage_data_innovations : data_all_innovations;
    var myInnovationsDC = new DataCollection(data_check);
    var target = myInnovationsDC.query().filter({ id__is: id }).values();
    if (!target || target && target.length <= 0) {
        return null;
    }
    return target[0];
}

/**
 * getCanvasById
 * @param innovation_id
 * @param id
 * @return {null}
 */
function getCanvasById(innovation_id, id) {
    var innovation = getInnovationById(innovation_id, true);
    if (!innovation) {
        return null;
    }
    id = parseInt(id);
    var canvas_collection = innovation['canvas_collection'];
    if (!canvas_collection || (canvas_collection && canvas_collection.length === 0)) {
        return null;
    }
    for (var i = 0; i < canvas_collection.length; i++) {
        if (canvas_collection[i]['id'] == id) {
            return canvas_collection[i];
        }
    }
    return null;
}

/**
 * hide_and_scroll
 */
function hide_and_scroll() {
    window.scrollTo(0, 0);
    $('#pri-main-container').scrollTop(0);
}

/**
 * get_animated_triple_dot_html_template.
 *
 * @returns {string}
 */
function get_animated_triple_dot_html_template() {
    return '<span class="animated-triple-dot"><span>.</span><span>.</span><span>.</span></span>';
}

/**
 * get_select_option_html_template.
 *
 * @param value
 * @param text
 * @param selected
 * @returns {string}
 */
function get_select_option_html_template(value, text, selected) {
    text = text || '';
    selected = (typeof selected != 'undefined') ? selected : false;
    return '<option value="' + value + '" ' + ((selected) ? "selected" : "") + '>' + text + '</option>';
}


/**
 * retourne l'evolution entre 2 nombres en pourcentage
 * @param oldValue
 * @param newValue
 * @returns {*}
 */
function get_percent_diff_between_two_values(oldValue, newValue) {
    if (oldValue == 0) {
        return 0;
    }
    if (newValue == 0) {
        return -100;
    }
    var denominator = (oldValue < 0 && newValue >= 0) ? Math.abs(oldValue) : oldValue;
    var percentChange = ((newValue - oldValue) / denominator) * 100;
    return number_format(percentChange, 0, '.', '');
}


/**
 * retourne le total entre 2 nombres en pourcentage
 * @param value
 * @param total
 * @returns {*}
 */
function get_percent_total_between_two_values(value, total) {
    if (value == 0 || total == 0) {
        return 0;
    }
    return number_format((value / total) * 100, 0);
}

/**
 *
 * @param libelle
 * @returns {string}
 */
function string_to_classe(libelle) {
    return libelle.toLowerCase().replaceAll(" & ", "_").replaceAll(' ', '_');
}

function get_edition_mode_for_innovation(the_innovation) {
    var edition_mode = (check_if_user_is_hq() || !innovation_is_terminated(the_innovation) || user_can_edit_this_innovation(the_innovation));
    if (edition_mode && !check_if_user_is_hq() && !check_if_data_capture_is_opened()) {
        edition_mode = false;
    }
    if (pri_user_has_edition_rights() && !innovation_is_terminated(the_innovation)) {
        edition_mode = true;
    }
    if (check_if_user_is_readonly()) {
        edition_mode = false;
    }
    return edition_mode;
}


function get_good_text(texte, type) {
    var choices = [];
    if (type == 'growth_strategy') {
        choices = get_innovation_growth_strategy();
    }
    for (var i = 0; i < choices.length; i++) {
        var obj = choices[i];
        if (obj['id'] == texte) {
            return obj['text'];
        }
    }
    return texte;
}

function get_text_from_id(id, tableau) {
    for (var i = 0; i < tableau.length; i++) {
        if (tableau[i]['id'] == id) {
            return tableau[i]['text'];
        }
    }
    return "";
}


function check_if_user_is_only_reader_of_innovation(the_innovation) {
    var is_only_reader = (!user_can_edit_this_innovation(the_innovation));

    if (check_if_user_is_readonly()) {
        is_only_reader = true;
    }
    if (check_if_user_is_innovation_contact(the_innovation)) {
        is_only_reader = false;
    }
    return is_only_reader;
}

function arrayUnique(array) {
    var a = array.concat();
    for (var i = 0; i < a.length; ++i) {
        for (var j = i + 1; j < a.length; ++j) {
            if (a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
}

/**
 * goToByUrl
 * @param url
 */
function goToByUrl(url) {
    if (url === '/content/dashboard') {
        gotToMonitorList(url, 'dashboard');
    } else if (url.indexOf('/content/dashboard/') !== -1) {
        var type = url.split('/dashboard/')[1];
        dashboard_detail_init(type);
    } else if (url.indexOf('/content/explore') !== -1) {
        goToExploreListPage(url);
    } else if (url.indexOf('/content/compare') !== -1) {
        goToCompareListPage();
    } else if (url.indexOf('/explore/') !== -1) {
        var exploder_url = url.split('/explore/');
        var id = exploder_url[1];
        goToExploreDetailPage(id);
    } else if (url === '/') {
        goToWelcomePage();
    } else if (url.indexOf('/content/manage/tab/') !== -1) {
        var exploder_url = url.split('manage/tab/');
        var tab = exploder_url[1];
        goToManageList(url, tab);
    } else if (url.indexOf('/content/manage') !== -1) {
        goToManageList(url);
    } else if (url.indexOf('/content/monitor') !== -1) {
        gotToMonitorList(url);
    } else if (url.indexOf('/content/connect') !== -1) {
        goToConnectPage(url);
    } else if (url.indexOf('/user') !== -1) {
        var exploder_url = url.split('/user/');
        var target_user = null;
        if (exploder_url.length === 1) {
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
    } else {
        window.location = url;
    }
}

/**
 * check_if_innovation_is_a_service.
 *
 * @param innovation
 * @returns {boolean}
 */
function check_if_innovation_is_a_service(innovation) {
    if (!innovation) {
        return false;
    }
    return (innovation['classification_type'] == 'Service');
}

/**
 * check_if_innovation_is_nba.
 *
 * @param innovation
 * @returns {boolean}
 */
function check_if_innovation_is_nba(innovation) {
    if (!innovation) {
        return false;
    }
    return (check_if_innovation_is_a_service(innovation) && innovation['growth_strategy'] == "New Business Acceleration");
}

/**
 * Update CSRF tokens.
 *
 * @param csrf_tokens
 */
function update_csrf_tokens(csrf_tokens) {
    $('#form-new-innovation-token').val(csrf_tokens['create_innovation']);
    $('#hub-token').val(csrf_tokens['hub_token']);
}