var primary_color_palette = {
    red: '#FC7068',
    yellow: '#FCB64C',
    green: '#A9CF4C',
    turquoise: '#7ADED2',
    blue: '#66BDFC',
    purple: '#A583DE',
    pink: '#F2A0CC'
};

var secondary_color_palette = {
    red: '#DB3D3D',
    yellow: '#E8922C',
    green: '#52A355',
    turquoise: '#39BDB5',
    blue: '#2D97E3',
    purple: '#7D5DB2',
    pink: '#E563A9'
};

var primary_default_color = {
    name: 'primary_default',
    value: '#66BDFC'
};

var secondary_default_color = {
    name: 'secondary_default',
    value: '#FCB64C'
};



var common_color = {
    name: 'common',
    value: '#394F68'
};

var log_colortool = false;

/**
 * Méthode utilsé par rgbToHex
 * @param c
 * @returns {*}
 */
function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

/**
 * Convertis une couleur rgb en hex
 * @param r
 * @param g
 * @param b
 * @returns {string}
 */
function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}


/**
 * Get dominant color from image
 * @param image_url
 * @returns {*}
 */
function getDominantColorFromImage(image_url){
    return null;
    //console.info("getDominantColorFromImage => "+image_url);
    try {
        var image = new Image;
        image.src = image_url;
        var colorThief = new ColorThief();
        var palette = colorThief.getPalette(image, 8);
        if(log_colortool) {
            console.log(palette);
        }
        for(var i = 0; i < palette.length; i++){
            var a_dominantColor = palette[i];
            var too_light = (a_dominantColor[0] > 200 && a_dominantColor[1] > 200 && a_dominantColor[2] > 200);
            var too_dark = (a_dominantColor[0] < 80 && a_dominantColor[1] < 80 && a_dominantColor[2] < 80);
            if(!too_light && !too_dark){
                var hex = rgbToHex(a_dominantColor[0], a_dominantColor[1], a_dominantColor[2]);
                return hex;
            }
        }
        var dominantColor = colorThief.getColor(image);
        if(dominantColor){
            var hex = rgbToHex(dominantColor[0], dominantColor[1], dominantColor[2]);
            return hex;
        }
    }
    catch(error) {
        console.log("ColorThief error");
        console.log(error);
        jQuery('body > canvas').remove();
    }
    return null;
}

/**
 * getNearestColorFromHexAndColorsArray
 * @param hex_color
 * @param colors_array
 * @returns {*}
 */
function getNearestColorFromHexAndColorsArray(hex_color, colors_array){
    if(log_colortool) {
        console.info("getNearestColorFromHexAndColorsArray => " + hex_color);
        console.info(colors_array);
    }
    var proper_colors = mapColors(colors_array);
    //console.info(proper_colors);
    var result = nearestColor(hex_color, proper_colors);
    return result;
}


/**
 * Get color from url
 * @param image_url
 * @returns {*}
 */
function getColorFromUrl(image_url){
    var hex_color = getDominantColorFromImage(image_url);
    if(log_colortool) {
        console.info("getColorFromUrl => " + image_url + " // hex => " + hex_color);
    }
    if(hex_color){
        return getNearestColorFromHexAndColorsArray(hex_color, primary_color_palette);
    }
    return primary_default_color;
}

/**
 * Get color for Innovation
 * @param innovation
 * @returns {*}
 */
function getColorForInnovation(innovation){
    if(innovation['explore']['images'].length > 0){
        for (var i = 0; i < innovation['explore']['images'].length; i++){
            var image_url = innovation['explore']['images'][i];
            return getColorFromUrl(image_url);
        }
        return primary_default_color;
    }else if(check_if_innovation_is_a_service(innovation) && innovation['packshot_picture']){
        var image_url = innovation['packshot_picture']['thumbnail'];
        return getColorFromUrl(image_url);
    }else if(innovation['beautyshot_picture']){
        var image_url = innovation['beautyshot_picture']['thumbnail'];
        return getColorFromUrl(image_url);
    }else{
        return primary_default_color;
    }
}

/**
 * getProperColorForAnInnovation
 * @param innovation
 * @returns {{classes: (*|string), color: *}}
 */
function getProperColorForAnInnovation(innovation){
    var color = getColorForInnovation(innovation);
    var proper_color = {
        classes : color.name,
        color : color.value
    };
    if(log_colortool) {
        console.info(proper_color);
    }
    return proper_color;
}


/**
 * Get color for 2 innovations
 * @param innovation_1
 * @param innovation_2
 * @returns {{innovation_1: *, innovation_2: *}}
 */
function getProperColorFor2Innovations(innovation_1, innovation_2){
    var color_1 = getColorForInnovation(innovation_1);
    var color_2 = getColorForInnovation(innovation_2);
    var proper_color_1 = {
        classes : color_1.name,
        color : color_1.value
    };

    if(color_1['name'] == color_2['name']){
        if(color_2['name'] == 'default'){
            color_2 = secondary_default_color;
            color_2['type'] = 'secondary';
        }else{
            color_2 = {
                name: color_2['name'],
                value: secondary_color_palette[color_2['name']],
                type: 'secondary'
            };
        }
    }else{
        color_2['type'] = '';
    }

    var proper_color_2 = {
        classes : color_2.name+' '+color_2.type,
        color : color_2.value
    };

    var retour = {
        'innovation_1' : proper_color_1,
        'innovation_2' : proper_color_2
    };
    if(log_colortool) {
        console.info(retour);
    }
    return retour;
}

