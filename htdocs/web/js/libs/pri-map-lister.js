/*
 *   pri-map-lister
 *   Written by @tic_le_kiwi
 */

var COUNTRY_GROUP_ID_EMPTY = 0;
var COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST = 1;
var COUNTRY_GROUP_ID_AMERICAS = 2;
var COUNTRY_GROUP_ID_USA = 3;
var COUNTRY_GROUP_ID_ASIA_OCEANIA = 4;
var COUNTRY_GROUP_ID_EUROPE = 5;
var COUNTRY_GROUP_ID_FRANCE = 6;
var COUNTRY_GROUP_ID_POLAR_LANDS = 7;
var COUNTRY_GROUP_ID_DUTY_FREE = 8;
var map_lister_request = null;

/**
 * get_country_group_name
 * @param group_id
 * @return string
 */
function get_country_group_name(group_id) {
    switch (group_id) {
        case COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST:
            return "Africa & Middle East";
        case COUNTRY_GROUP_ID_AMERICAS:
            return "Americas";
        case COUNTRY_GROUP_ID_USA:
            return "USA";
        case COUNTRY_GROUP_ID_ASIA_OCEANIA:
            return "Asia & Oceania";
        case COUNTRY_GROUP_ID_EUROPE:
            return "Europe";
        case COUNTRY_GROUP_ID_FRANCE:
            return "France";
        case COUNTRY_GROUP_ID_POLAR_LANDS:
            return "Polar lands";
        case COUNTRY_GROUP_ID_DUTY_FREE:
            return "Travel Retail";
        default:
            return "Other";
    }
}

/**
 * Get country name array by code array.
 *
 * @param code_array
 * @return Array
 */
function get_country_name_array_by_code_array(code_array)
{
    var all_countries = get_all_countries();
    var ret = [];
    for(var a = 0; a < all_countries.length; a++){
        for(var b = 0; b < code_array.length; b++){
            if(all_countries[a]['country_code'] == code_array[b]){
                ret.push(all_countries[a]['country_name']);
            }
        }
    }
    return ret;
}

/**
 * Get country name by code.
 *
 * @param code
 * @return string|null
 */
function get_country_name_by_code(code)
{
    var all_countries = get_all_countries();
    for(var a = 0; a < all_countries.length; a++){
        if(all_countries[a]['country_code'] == code){
            return all_countries[a]['country_name'];
        }
    }
    return null;
}

/**
 * get_country_array_by_code_array.
 *
 * @param code_array
 * @returns {Array}
 */
function get_country_array_by_code_array(code_array) {
    var all_countries = get_all_countries();
    var ret = [];
    for (var i = 0; i < all_countries.length; i++) {
        var country = all_countries[i];
        for (var u = 0; u < code_array.length; u++) {
            if (country['country_code'] == code_array[u]) {
                ret.push(country);
            }
        }
    }
    return ret;
}

function get_country_codes_not_in_map() {
    return ['SG'];
}

function get_all_countries(by_group, with_usa) {
    by_group = typeof by_group !== 'undefined' ? by_group : false;
    with_usa = typeof with_usa !== 'undefined' ? with_usa : true;
    var countries = [
        {
            "country_code": "BD",
            "country_name": "Bangladesh",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 2,
            "country_code": "BE",
            "country_name": "Belgium",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 3,
            "country_code": "BF",
            "country_name": "Burkina Faso",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 4,
            "country_code": "BG",
            "country_name": "Bulgaria",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 5,
            "country_code": "BA",
            "country_name": "Bosnia and Herz.",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 6,
            "country_code": "BN",
            "country_name": "Brunei",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 7,
            "country_code": "BO",
            "country_name": "Bolivia",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 8,
            "country_code": "JP",
            "country_name": "Japan",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 9,
            "country_code": "BI",
            "country_name": "Burundi",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 10,
            "country_code": "BJ",
            "country_name": "Benin",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 11,
            "country_code": "BT",
            "country_name": "Bhutan",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 12,
            "country_code": "JM",
            "country_name": "Jamaica",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 13,
            "country_code": "BW",
            "country_name": "Botswana",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 14,
            "country_code": "BR",
            "country_name": "Brazil",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 15,
            "country_code": "BS",
            "country_name": "Bahamas",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 16,
            "country_code": "BY",
            "country_name": "Belarus",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 17,
            "country_code": "BZ",
            "country_name": "Belize",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 18,
            "country_code": "RU",
            "country_name": "Russia",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 19,
            "country_code": "RW",
            "country_name": "Rwanda",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 20,
            "country_code": "RS",
            "country_name": "Serbia",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 21,
            "country_code": "TL",
            "country_name": "Timor-Leste",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 22,
            "country_code": "TM",
            "country_name": "Turkmenistan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 23,
            "country_code": "TJ",
            "country_name": "Tajikistan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 24,
            "country_code": "RO",
            "country_name": "Romania",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 25,
            "country_code": "GW",
            "country_name": "Guinea-Bissau",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 26,
            "country_code": "GT",
            "country_name": "Guatemala",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 27,
            "country_code": "GR",
            "country_name": "Greece",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 28,
            "country_code": "GQ",
            "country_name": "Eq. Guinea",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 29,
            "country_code": "GY",
            "country_name": "Guyana",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 30,
            "country_code": "GE",
            "country_name": "Georgia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 31,
            "country_code": "GB",
            "country_name": "United Kingdom",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 32,
            "country_code": "GA",
            "country_name": "Gabon",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 33,
            "country_code": "GN",
            "country_name": "Guinea",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 34,
            "country_code": "GM",
            "country_name": "Gambia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        /*
         {
         "country_id": 35,
         "country_code": "GL",
         "country_name": "Greenland",
         "group_id": COUNTRY_GROUP_ID_POLAR_LANDS
         },*/
        {
            "country_id": 36,
            "country_code": "GH",
            "country_name": "Ghana",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 37,
            "country_code": "OM",
            "country_name": "Oman",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 38,
            "country_code": "TN",
            "country_name": "Tunisia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 39,
            "country_code": "JO",
            "country_name": "Jordan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 40,
            "country_code": "HR",
            "country_name": "Croatia",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 41,
            "country_code": "HT",
            "country_name": "Haiti",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 42,
            "country_code": "HU",
            "country_name": "Hungary",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 43,
            "country_code": "HN",
            "country_name": "Honduras",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 44,
            "country_code": "PR",
            "country_name": "Puerto Rico",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 45,
            "country_code": "PS",
            "country_name": "Palestine",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 46,
            "country_code": "PT",
            "country_name": "Portugal",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 47,
            "country_code": "PY",
            "country_name": "Paraguay",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 48,
            "country_code": "PA",
            "country_name": "Panama",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 49,
            "country_code": "PG",
            "country_name": "Papua New Guinea",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 50,
            "country_code": "PE",
            "country_name": "Peru",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 51,
            "country_code": "PK",
            "country_name": "Pakistan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 52,
            "country_code": "PH",
            "country_name": "Philippines",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 53,
            "country_code": "PL",
            "country_name": "Poland",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 54,
            "country_code": "ZM",
            "country_name": "Zambia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 55,
            "country_code": "EH",
            "country_name": "W. Sahara",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 56,
            "country_code": "EE",
            "country_name": "Estonia",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 57,
            "country_code": "EG",
            "country_name": "Egypt",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 58,
            "country_code": "ZA",
            "country_name": "South Africa",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 59,
            "country_code": "EC",
            "country_name": "Ecuador",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 60,
            "country_code": "IT",
            "country_name": "Italy",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 61,
            "country_code": "VN",
            "country_name": "Vietnam",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 62,
            "country_code": "SB",
            "country_name": "Solomon Is.",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 63,
            "country_code": "ET",
            "country_name": "Ethiopia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 64,
            "country_code": "SO",
            "country_name": "Somalia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 65,
            "country_code": "ZW",
            "country_name": "Zimbabwe",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 66,
            "country_code": "ES",
            "country_name": "Spain",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 67,
            "country_code": "ER",
            "country_name": "Eritrea",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 68,
            "country_code": "ME",
            "country_name": "Montenegro",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 69,
            "country_code": "MD",
            "country_name": "Moldova",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 70,
            "country_code": "MG",
            "country_name": "Madagascar",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 71,
            "country_code": "MA",
            "country_name": "Morocco",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 72,
            "country_code": "UZ",
            "country_name": "Uzbekistan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 73,
            "country_code": "MM",
            "country_name": "Myanmar",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 74,
            "country_code": "ML",
            "country_name": "Mali",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 75,
            "country_code": "MN",
            "country_name": "Mongolia",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 76,
            "country_code": "MK",
            "country_name": "Macedonia",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 77,
            "country_code": "MW",
            "country_name": "Malawi",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 78,
            "country_code": "MR",
            "country_name": "Mauritania",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 79,
            "country_code": "UG",
            "country_name": "Uganda",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 80,
            "country_code": "MY",
            "country_name": "Malaysia",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 81,
            "country_code": "MX",
            "country_name": "Mexico",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 82,
            "country_code": "IL",
            "country_name": "Israel",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 83,
            "country_code": "FR",
            "country_name": "France",
            "group_id": COUNTRY_GROUP_ID_FRANCE
        },
        {
            "country_id": 84,
            "country_code": "XS",
            "country_name": "Somaliland",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 85,
            "country_code": "FI",
            "country_name": "Finland",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 86,
            "country_code": "FJ",
            "country_name": "Fiji",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 87,
            "country_code": "FK",
            "country_name": "Falkland Is.",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 88,
            "country_code": "NI",
            "country_name": "Nicaragua",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 89,
            "country_code": "NL",
            "country_name": "Netherlands",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 90,
            "country_code": "NO",
            "country_name": "Norway",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 91,
            "country_code": "NA",
            "country_name": "Namibia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 92,
            "country_code": "VU",
            "country_name": "Vanuatu",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 93,
            "country_code": "NC",
            "country_name": "New Caledonia",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 94,
            "country_code": "NE",
            "country_name": "Niger",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 95,
            "country_code": "NG",
            "country_name": "Nigeria",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 96,
            "country_code": "NZ",
            "country_name": "New Zealand",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 97,
            "country_code": "NP",
            "country_name": "Nepal",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 98,
            "country_code": "XK",
            "country_name": "Kosovo",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 99,
            "country_code": "CI",
            "country_name": "Côte d'Ivoire",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 100,
            "country_code": "CH",
            "country_name": "Switzerland",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 101,
            "country_code": "CO",
            "country_name": "Colombia",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 102,
            "country_code": "CN",
            "country_name": "China",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 103,
            "country_code": "CM",
            "country_name": "Cameroon",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 104,
            "country_code": "CL",
            "country_name": "Chile",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 105,
            "country_code": "XC",
            "country_name": "N. Cyprus",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 106,
            "country_code": "CA",
            "country_name": "Canada",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 107,
            "country_code": "CG",
            "country_name": "Congo",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 108,
            "country_code": "CF",
            "country_name": "Central African Rep.",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 109,
            "country_code": "CD",
            "country_name": "Dem. Rep. Congo",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 110,
            "country_code": "CZ",
            "country_name": "Czech Rep.",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 111,
            "country_code": "CY",
            "country_name": "Cyprus",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 112,
            "country_code": "CR",
            "country_name": "Costa Rica",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 113,
            "country_code": "CU",
            "country_name": "Cuba",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 114,
            "country_code": "SZ",
            "country_name": "Swaziland",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 115,
            "country_code": "SY",
            "country_name": "Syria",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 116,
            "country_code": "KG",
            "country_name": "Kyrgyzstan",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 117,
            "country_code": "KE",
            "country_name": "Kenya",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 118,
            "country_code": "SS",
            "country_name": "S. Sudan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 119,
            "country_code": "SR",
            "country_name": "Suriname",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 120,
            "country_code": "KH",
            "country_name": "Cambodia",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 121,
            "country_code": "SV",
            "country_name": "El Salvador",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 122,
            "country_code": "SK",
            "country_name": "Slovakia",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 123,
            "country_code": "KR",
            "country_name": "Korea",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 124,
            "country_code": "SI",
            "country_name": "Slovenia",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 125,
            "country_code": "KP",
            "country_name": "Dem. Rep. Korea",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 126,
            "country_code": "KW",
            "country_name": "Kuwait",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 127,
            "country_code": "SN",
            "country_name": "Senegal",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 128,
            "country_code": "SL",
            "country_name": "Sierra Leone",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 129,
            "country_code": "KZ",
            "country_name": "Kazakhstan",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 130,
            "country_code": "SA",
            "country_name": "Saudi Arabia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 131,
            "country_code": "SE",
            "country_name": "Sweden",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 132,
            "country_code": "SD",
            "country_name": "Sudan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 133,
            "country_code": "DO",
            "country_name": "Dominican Rep.",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 134,
            "country_code": "DJ",
            "country_name": "Djibouti",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 135,
            "country_code": "DK",
            "country_name": "Denmark",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 136,
            "country_code": "DE",
            "country_name": "Germany",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 137,
            "country_code": "YE",
            "country_name": "Yemen",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 138,
            "country_code": "DZ",
            "country_name": "Algeria",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 139,
            "country_code": "US",
            "country_name": "United States",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 140,
            "country_code": "UY",
            "country_name": "Uruguay",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 141,
            "country_code": "LB",
            "country_name": "Lebanon",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 142,
            "country_code": "LA",
            "country_name": "Lao PDR",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 143,
            "country_code": "TW",
            "country_name": "Taiwan",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 144,
            "country_code": "TT",
            "country_name": "Trinidad and Tobago",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 145,
            "country_code": "TR",
            "country_name": "Turkey",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 146,
            "country_code": "LK",
            "country_name": "Sri Lanka",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 147,
            "country_code": "LV",
            "country_name": "Latvia",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 148,
            "country_code": "LT",
            "country_name": "Lithuania",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 149,
            "country_code": "LU",
            "country_name": "Luxembourg",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 150,
            "country_code": "LR",
            "country_name": "Liberia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 151,
            "country_code": "LS",
            "country_name": "Lesotho",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 152,
            "country_code": "TH",
            "country_name": "Thailand",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        /*
         {
         "country_id": 153,
         "country_code": "TF",
         "country_name": "Fr. S. Antarctic Lands",
         "group_id": COUNTRY_GROUP_ID_POLAR_LANDS
         },*/
        {
            "country_id": 154,
            "country_code": "TG",
            "country_name": "Togo",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 155,
            "country_code": "TD",
            "country_name": "Chad",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 156,
            "country_code": "LY",
            "country_name": "Libya",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 157,
            "country_code": "AE",
            "country_name": "United Arab Emirates",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 158,
            "country_code": "VE",
            "country_name": "Venezuela",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 159,
            "country_code": "AF",
            "country_name": "Afghanistan",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 160,
            "country_code": "IQ",
            "country_name": "Iraq",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 161,
            "country_code": "IS",
            "country_name": "Iceland",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 162,
            "country_code": "IR",
            "country_name": "Iran",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 163,
            "country_code": "AM",
            "country_name": "Armenia",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 164,
            "country_code": "AL",
            "country_name": "Albania",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 165,
            "country_code": "AO",
            "country_name": "Angola",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 166,
            "country_code": "AR",
            "country_name": "Argentina",
            "group_id": COUNTRY_GROUP_ID_AMERICAS
        },
        {
            "country_id": 167,
            "country_code": "AU",
            "country_name": "Australia",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 168,
            "country_code": "AT",
            "country_name": "Austria",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 169,
            "country_code": "IN",
            "country_name": "India",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 170,
            "country_code": "TZ",
            "country_name": "Tanzania",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 171,
            "country_code": "AZ",
            "country_name": "Azerbaijan",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 172,
            "country_code": "IE",
            "country_name": "Ireland",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 173,
            "country_code": "ID",
            "country_name": "Indonesia",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 174,
            "country_code": "UA",
            "country_name": "Ukraine",
            "group_id": COUNTRY_GROUP_ID_EUROPE
        },
        {
            "country_id": 175,
            "country_code": "QA",
            "country_name": "Qatar",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 176,
            "country_code": "MZ",
            "country_name": "Mozambique",
            "group_id": COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
        },
        {
            "country_id": 1001,
            "country_code": "HK",
            "country_name": "Hong Kong",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        },
        {
            "country_id": 1002,
            "country_code": "DF1",
            "country_name": "TR Americas",
            "group_id": COUNTRY_GROUP_ID_DUTY_FREE
        },
        {
            "country_id": 1003,
            "country_code": "DF2",
            "country_name": "TR Europe",
            "group_id": COUNTRY_GROUP_ID_DUTY_FREE
        },
        {
            "country_id": 1004,
            "country_code": "DF3",
            "country_name": "TR Africa and Middle East",
            "group_id": COUNTRY_GROUP_ID_DUTY_FREE
        },
        {
            "country_id": 1005,
            "country_code": "DF4",
            "country_name": "TR Asia",
            "group_id": COUNTRY_GROUP_ID_DUTY_FREE
        },
        {
            "country_id": 5001,
            "country_code": "SG",
            "country_name": "Singapore",
            "group_id": COUNTRY_GROUP_ID_ASIA_OCEANIA
        }
    ];
    var usa_countries = [
        {
            "country_code": "US-VA",
            "country_name": "Virginia",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-PA",
            "country_name": "Pennsylvania",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-TN",
            "country_name": "Tennessee",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-WV",
            "country_name": "West Virginia",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-NV",
            "country_name": "Nevada",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-TX",
            "country_name": "Texas",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-NH",
            "country_name": "New Hampshire",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-NY",
            "country_name": "New York",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-HI",
            "country_name": "Hawaii",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-VT",
            "country_name": "Vermont",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-NM",
            "country_name": "New Mexico",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-NC",
            "country_name": "North Carolina",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-ND",
            "country_name": "North Dakota",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-NE",
            "country_name": "Nebraska",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-LA",
            "country_name": "Louisiana",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-SD",
            "country_name": "South Dakota",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-DC",
            "country_name": "District of Columbia",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-DE",
            "country_name": "Delaware",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-FL",
            "country_name": "Florida",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-CT",
            "country_name": "Connecticut",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-WA",
            "country_name": "Washington",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-KS",
            "country_name": "Kansas",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-WI",
            "country_name": "Wisconsin",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-OR",
            "country_name": "Oregon",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-KY",
            "country_name": "Kentucky",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-ME",
            "country_name": "Maine",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-OH",
            "country_name": "Ohio",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-OK",
            "country_name": "Oklahoma",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-ID",
            "country_name": "Idaho",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-WY",
            "country_name": "Wyoming",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-UT",
            "country_name": "Utah",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-IN",
            "country_name": "Indiana",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-IL",
            "country_name": "Illinois",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-AK",
            "country_name": "Alaska",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-NJ",
            "country_name": "New Jersey",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-CO",
            "country_name": "Colorado",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-MD",
            "country_name": "Maryland",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-MA",
            "country_name": "Massachusetts",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-AL",
            "country_name": "Alabama",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-MO",
            "country_name": "Missouri",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-MN",
            "country_name": "Minnesota",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-CA",
            "country_name": "California",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-IA",
            "country_name": "Iowa",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-MI",
            "country_name": "Michigan",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-GA",
            "country_name": "Georgia",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-AZ",
            "country_name": "Arizona",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-MT",
            "country_name": "Montana",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-MS",
            "country_name": "Mississippi",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-SC",
            "country_name": "South Carolina",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-RI",
            "country_name": "Rhode Island",
            "group_id": COUNTRY_GROUP_ID_USA
        },
        {
            "country_code": "US-AR",
            "country_name": "Arkansas",
            "group_id": COUNTRY_GROUP_ID_USA
        }
    ];
    if(with_usa){
        countries = countries.concat(usa_countries);
    }
    if (by_group) {
        var ret = {};
        for (var i = 0; i < countries.length; i++) {
            var country = countries[i];
            if (!ret.hasOwnProperty(country['group_id'])) {
                ret[country['group_id']] = [];
            }
            ret[country['group_id']].push(country);
        }
        return ret;
    }
    return countries;
}

function orderByCountryName(a, b) {
    if (a.country_name < b.country_name)
        return -1;
    if (a.country_name > b.country_name)
        return 1;
    return 0;
}

/**
 * update_array_by_adding_or_removing_data
 * @param array
 * @param data
 * @returns {*}
 */
function update_array_by_adding_or_removing_data(array, data){
    if(!in_array(data, array)){
        array.push(data);
        return array;
    }else{
        var ret = [];
        for(var i = 0; i < array.length; i++){
            if(array[i] != data){
                ret.push(array[i]);
            }
        }
        return ret;
    }
}
/**
 * collection_contain_usa_markets
 * @param collection
 * @returns {boolean}
 */
function collection_contain_usa_markets(collection){
    for (var i = 0; i < collection.length; i++){
        if(collection[i].indexOf('US-') !== -1){
            return true;
        }
    }
    return false;
}

/**
 * string_to_html_class
 * @param the_string
 * @return string
 */
function string_to_html_class(the_string) {
    String.prototype.replaceAll = function (search, replacement) {
        var target = this;
        return target.replace(new RegExp(search, 'g'), replacement);
    };
    the_string = the_string.toLowerCase();
    the_string = the_string.replace(/[^a-zA-Z0-9_. ]/g, '');
    the_string = the_string.replaceAll(' ', '_');
    the_string = the_string.replaceAll('__', '_');
    return the_string;
}

(function ($) {
    $.fn.mapLister = function (options) {
        var isInited = false;
        //On définit nos paramètres par défaut
        var defauts =
        {
            data: [],
            identifier: 'f-identifier',
            title: "",
            full_infos: true,
            disabled: false,
            id: null,
            with_usa: false,
            onAfter: function (filters) {
            }
        };
        if (options === 'getData') {

        }else if ("data" in options || "title" in options) {
            var options = $.extend(defauts, options);
        } else {
            defauts.data = options;
            var options = defauts;
        }
        function getData($this) {
            var some_data = [];
            $this.find('.a_market_on_list.active').each(function () {
                some_data.push($(this).attr('data-target'));
            });
            return some_data;
        }

        if (options === 'getData') {
            var $this = $(this);
            return getData($this);
        } else {
            return this.each(function () {
                var $this = $(this);
                var string_data = ($this.attr('data-value')) ? $this.attr('data-value') : "[]";
                var data = JSON.parse(string_data);
                var identifier = options.identifier;
                var the_map = null;
                var the_map_usa = null;
                var stepped_graph = null;
                var the_map_list = [];
                var the_map_id = identifier + '-the-map';
                var the_map_usa_id = identifier + '-the-map-usa';
                var the_stepped_graph_id = identifier + '-stepped-graph';
                var innovation_id = parseInt($this.attr('data-id'));
                var innovation = getInnovationById(innovation_id, true);
                if (!$this.hasClass('map-lister-active')) {
                    addHtml();
                }


                function arrayKeys(input, force_int) {
                    force_int = typeof force_int !== 'undefined' ? force_int : false;
                    var output = [];
                    var counter = 0;
                    for (i in input) {
                        if (force_int) {
                            i = parseInt(i);
                        }
                        output[counter++] = i;
                    }
                    return output;
                }

                function updateAll(the_data) {
                    updateList(the_data);
                    updateMap(the_data);
                    updateFooter(the_data);
                    the_map_list = the_data;
                }

                
                function updateMap(the_data) {
                    updateWorldMap(the_data);
                    updateUsaMap(the_data);
                }

                function updateWorldMap(the_data) {
                    if (the_map) {
                        the_map.clearSelectedRegions();
                        the_map.setSelectedRegions(the_data);
                    }
                }
                function updateUsaMap(the_data) {
                    if(the_map_usa){
                        the_map_usa.clearSelectedRegions();
                        the_map_usa.setSelectedRegions(the_data);
                    }
                }

                function updateFooter(the_data) {
                    $this.find('.map-other-info .content-nb-markets').html(the_data.length + ' markets selected:');
                    var all_regions_html = "";
                    var names = [];
                    for (var i = 0; i < the_data.length; i++) {
                        var a_region = the_data[i];
                        var a_name = {};
                        a_name['country_code'] = a_region;
                        a_name['country_name'] = get_country_name_by_code(a_region);
                        names.push(a_name);
                    }
                    names.sort(orderByCountryName);
                    for (var i = 0; i < names.length; i++) {
                        var a_name = names[i];
                        var a_region_name = a_name['country_name'];
                        var a_region_code = a_name['country_code'];
                        all_regions_html += '<div class="item-region transition font-work-sans-400" data-target="' + a_region_code + '">';
                        all_regions_html += '<span>' + a_region_name + '</span>';
                        all_regions_html += '<span class="close"></span>';
                        all_regions_html += '</div>';
                    }
                    $this.find('.map-other-info .all_markets').html(all_regions_html);
                    if (!$this.hasClass('disabled')) {
                        $this.find('.item-region').click(function () {
                            var target = $(this).attr('data-target');
                            var the_data = remove_element_from_array(the_map_list, target);
                            $(this).remove();
                            $this.find('.map-other-info .content-nb-markets').html(the_data.length + ' markets selected:');
                            updateMap(the_data);
                            updateList(the_data);
                            the_map_list = the_data;
                            saveData();
                        });
                    }
                }

                function updateList(the_data) {
                    $this.find('.a_market_on_list').removeClass('active');
                    $this.find('.a_market_on_list').each(function () {
                        for (var i = 0; i < the_data.length; i++) {
                            if ($(this).attr('data-target') == the_data[i]) {
                                $(this).addClass('active');
                            }
                        }
                    });
                    updateListCategories();
                }


                function addActiveClassToSelectAllUsaButton(the_data){
                    var countries = get_all_countries(true);
                    var usa_countries = countries[COUNTRY_GROUP_ID_USA];
                    var selected_usa_contries = [];
                    for (var i = 0; i < the_data.length; i++) {
                        if(the_data[i].indexOf('US-') !== -1){
                            selected_usa_contries.push(the_data[i]);
                        }
                    }
                    if(selected_usa_contries.length == usa_countries.length){
                        $this.find('.select-all-usa').addClass('active');
                    }else{
                        $this.find('.select-all-usa').removeClass('active');
                    }
                }

                function updateListCategories() {
                    $this.find('.mapLister-list-by-category').each(function () {
                        var id = $(this).attr('id');
                        var current_nb = $(this).find('.a_market_on_list.active').length;
                        var li_target = $this.find('.li-' + id);
                        if (current_nb <= 0) {
                            li_target.find('a span').html('');
                        } else {
                            li_target.find('a span').html(current_nb);
                        }
                    });
                }

                function getDataFromClick() {
                    var some_data = [];
                    $this.find('.a_market_on_list.active').each(function () {
                        some_data.push($(this).attr('data-target'));
                    });
                    return some_data;
                }

                function saveData() {
                    if (isInited) {
                        $this.addClass('loading');
                        $('#main').addClass('inner-loading');
                        var the_data = {};
                        the_data['name'] = $this.attr('data-name');
                        the_data['value'] = JSON.stringify(the_map_list);
                        the_data['id'] = parseInt($this.attr('data-id'));
                        the_data['token'] = $('#hub-token').val();
                        if (map_lister_request != null)
                            map_lister_request.abort();
                        map_lister_request = $.ajax({
                            url: '/api/innovation/update',
                            type: "POST",
                            data: the_data,
                            dataType: 'json',
                            success: function (response) {
                                if (response.hasOwnProperty('full_data')) {
                                    updateFromCollection(response['full_data']['id'], response['full_data']);
                                    explore_detail_update_nb_missing_fields();
                                    add_saved_message();

                                    if (stepped_graph != null && $('#'+the_stepped_graph_id).length > 0) {
                                        var market_dataset = response['full_data']['financial']['market_stepped_data']['datasets'];
                                        var maxY = 0;
                                        for (var i = 0; i < market_dataset[0]['data'].length; i++){
                                            if(market_dataset[0]['data'][i]['y'] > maxY){
                                                maxY = market_dataset[0]['data'][i]['y'];
                                            }
                                        }
                                        maxY += 10;
                                        maxY = Math.ceil((maxY/10)) * 10;
                                        stepped_graph.data.datasets = market_dataset;
                                        stepped_graph.config.options.scales.yAxes[0].ticks.max = maxY;
                                        stepped_graph.update();
                                    }
                                }
                                $this.removeClass('loading');
                                $('#main').removeClass('inner-loading');
                            },
                            error: function (resultat, statut, e) {
                                ajax_on_error(resultat, statut, e);
                                if (resultat.statusText == 'abort' || e == 'abort') {
                                    return;
                                }
                                $this.removeClass('loading');
                                $('#main').removeClass('inner-loading');
                                console.log(resultat);
                                //add_flash_message('error', 'Error: ' + resultat.status + ' ' + e);
                            }
                        });
                    }
                }

                function init_usa_map(){
                    if(options.with_usa && !the_map_usa){
                        if ($('#' + the_map_usa_id).length > 0) {
                            var regionsSelectableUsa = (!$this.hasClass('disabled'));
                            var regionStyleHoverUsa = {
                                fill: '#82BBD6'
                            };
                            if ($this.hasClass('disabled')) {
                                regionStyleHoverUsa = {
                                    fill: '#82BBD6',
                                    'fill-opacity': 1,
                                    cursor: "default"
                                };
                            }

                            the_map_usa = new jvm.Map({
                                container: $('#' + the_map_usa_id),
                                map: 'us_aea_en',
                                backgroundColor: "#fff",
                                zoomOnScroll: false,
                                regionsSelectable: regionsSelectableUsa,
                                regionStyle: {
                                    initial: {
                                        fill: '#E1E1E1'
                                    },
                                    selected: {
                                        fill: '#4295CB'
                                    },
                                    hover: regionStyleHoverUsa
                                },
                                panOnDrag: true,
                                zoomMin: 0.85,
                                focusOn: {
                                    x: 0.5,
                                    y: 0.5,
                                    scale: 0.85,
                                    animate: true
                                },
                                onRegionSelected: function (e, code, isSelected, selectedRegions) {
                                    if ($this.hasClass("onClick")) {
                                        var the_data = update_array_by_adding_or_removing_data(the_map_list, code);
                                        updateFooter(the_data);
                                        updateList(the_data);
                                        updateWorldMap(the_data);
                                        the_map_list = the_data;
                                        saveData();
                                    }
                                },
                                onRegionClick: function (e, code) {
                                    if ($this.hasClass('disabled')) {
                                        e.preventDefault();
                                        return;
                                    }
                                    if(options.with_usa && code == 'US'){
                                        $this.find('.content-map-world').addClass('inactive');
                                        $this.find('.content-map-usa').removeClass('inactive');
                                        init_usa_map();
                                        e.preventDefault();
                                        return;
                                    }
                                    $this.addClass("onClick");
                                    setTimeout(function () {
                                        $this.removeClass("onClick");
                                    }, 300);
                                },
                            });

                            updateUsaMap(the_map_list);
                        }
                    }
                }

                function addHtml() {
                    $this.addClass('map-lister-active');
                    var out = [], o = -1;
                    if (options.id) {
                        var element = document.getElementById(options.id);
                        out[++o] = element.innerHTML;
                    }

                    var financial_date = get_financial_date();
                    out[++o] = '<div class="overflow-hidden content-global-market-tabs">'; // Start overflow-hidden
                    out[++o] = '<div class="position-relative">'; // Start position-relative
                    out[++o] = '<div class="font-work-sans-600 font-size-20 color-000 line-height-1-5 height-34 position-relative margin-bottom-40">';
                    if(options.full_infos) {
                        out[++o] = 'Markets activated ' + get_financial_date_FY(financial_date);
                    }
                    out[++o] = '</div>';
                    out[++o] = '<div class="position-absolute-top-right">'; // Start position-absolute-top-right
                    out[++o] = '<div class="content-buttons-tab-markets">'; // Start content-buttons-tab-markets
                    out[++o] = '<div class="button-tab-market transition map active" data-target="' + identifier + '-map"></div>';
                    out[++o] = '<div class="button-tab-market transition graph" data-target="' + identifier + '-graph"></div>';
                    out[++o] = '</div>'; // content-buttons-tab-markets
                    out[++o] = '</div>'; // End position-absolute-top-right
                    out[++o] = '</div>'; // End position-relative

                    out[++o] = '<div class="content-map '+((options.with_usa) ? "with-usa" : "")+'">'; // Start content-map
                    out[++o] = '<div id="' + identifier + '-map" class="market-tab active">'; // Start market-tab
                    out[++o] = '<div class="content-map-world position-relative">';
                    out[++o] = '<img class="mask-img" src="/images/masks/994x500.png"/>';
                    out[++o] = '<div id="' + the_map_id + '" class="mapLister-map"></div>';
                    out[++o] = '</div>';
                    if(options.with_usa){
                        out[++o] = '<div class="content-map-usa position-relative inactive">';
                        out[++o] = '<img class="mask-img" src="/images/masks/994x500.png"/>';
                        out[++o] = '<div id="'+the_map_usa_id+'" class="mapLister-map"></div>';
                        out[++o] = '<div class="content-buttons-map-usa">';
                        out[++o] = '<div class="hub-button height-26 back-to-world-map">Back</div>';
                        out[++o] = '<div class="hub-button height-26 select-all-usa margin-left-10"><span class="fake-radio"></span><span>Select all</span></div>';
                        out[++o] = '</div>';
                        out[++o] = '</div>';
                    }
                    if(options.full_infos) {
                        // Download map button
                        out[++o] = '<div class="position-relative text-align-right padding-bottom-40">';
                        out[++o] = '<div id="button-download-' + identifier + '" class="button-download-map transition font-montserrat-700"><span class="icon"></span><span>Download map</span></div>';
                        out[++o] = '</div>';
                    }

                    out[++o] = '</div>'; // End market-tab

                    out[++o] = '<div id="' + identifier + '-graph" class="market-tab">'; // Start market-tab
                    out[++o] = '<div class="content-market-graph position-relative">'; // Start content-market-graph
                    out[++o] = '<img class="mask-img" src="/images/masks/994x500.png"/>';
                    var added_classe = (options.full_infos) ? '' : ' margin-bottom-0';
                    out[++o] = '<div class="mapLister-graph background-color-f8f8f8'+added_classe+'">'; // Start mapLister-graph
                    out[++o] = '<div class="padding-40">';
                    out[++o] = '<div class="position-relative content-masked">';
                    out[++o] = '<img class="mask-img" src="/images/masks/914x420.png"/>';
                    out[++o] = '<canvas id="' + the_stepped_graph_id + '" class="absolute-chart" width="914" height="420"></canvas>';
                    out[++o] = '</div>';
                    out[++o] = '</div>';
                    out[++o] = '</div>'; // End content-market-graph
                    out[++o] = '</div>'; // End mapLister-graph
                    out[++o] = '</div>'; // End market-tab


                    if(options.full_infos) {
                        out[++o] = '<div id="' + identifier + '-list" class="content-tab-menu-inner">'; // Start content-tab-menu-inner

                        // pri_return_market_countries_html_list
                        var all_countries = get_all_countries(true);
                        var country_groups = arrayKeys(all_countries, true);
                        country_groups.sort();
                        out[++o] = '<div class="content-market-list-tabs">'; // Start content-market-list-tabs
                        out[++o] = '<ul>';
                        for (var i = 0; i < country_groups.length; i++) {
                            var group = country_groups[i];
                            if((group == COUNTRY_GROUP_ID_USA && options.with_usa) || group != COUNTRY_GROUP_ID_USA) {
                                var title_group = get_country_group_name(group);
                                var classe = string_to_html_class(title_group);
                                out[++o] = '<li class="transition li-' + identifier + '-' + classe + '">';
                                out[++o] = '<a class="transition" href="#' + identifier + '-' + classe + '">' + title_group + '<span></span></a>';
                                out[++o] = '</li>';
                            }
                        }
                        out[++o] = '</ul>';
                        for (var a = 0; a < country_groups.length; a++) {
                            var group = country_groups[a];
                            if((group == COUNTRY_GROUP_ID_USA && options.with_usa) || group != COUNTRY_GROUP_ID_USA) {
                                var title_group = get_country_group_name(group);
                                var classe = string_to_html_class(title_group)
                                var group_countries = all_countries[group];
                                group_countries = group_countries.sort(orderByCountryName);
                                var is_opened = true;
                                out[++o] = '<div id="' + identifier + '-' + classe + '" class="mapLister-list-by-category">';
                                for (var i = 0; i < group_countries.length; i++) {
                                    if (!options.with_usa || (options.with_usa && group_countries[i]['country_code'] != 'US')) {
                                        if (i == 0 || !is_opened) {
                                            out[++o] = '<div class="column-market">';
                                            is_opened = true;
                                        }
                                        out[++o] = '<div class="a_market_on_list" data-target="' + group_countries[i]['country_code'] + '">' + group_countries[i]['country_name'] + '</div>';
                                        if (i != 0 && (i + 1) % 20 == 0) {
                                            out[++o] = '</div>';
                                            is_opened = false;
                                        }
                                    }
                                }
                                if (is_opened) {
                                    out[++o] = '</div>';
                                }
                                out[++o] = '</div>';
                            }
                        }
                        out[++o] = '</div>';
                        // END pri_return_market_countries_html_list

                        out[++o] = '</div>'; // End content-tab-menu-inner
                        out[++o] = '</div>'; // End content-map
                        out[++o] = '<div class="map-other-info">'; // Start map-other-info
                        out[++o] = '<div class="content-nb-markets font-work-sans-300">'; // Start content-nb-markets
                        out[++o] = data.length + ' markets selected:';
                        out[++o] = '</div>'; // End content-nb-markets
                        out[++o] = '<div class="all_markets">'; // Start all_markets
                        out[++o] = '</div>'; // End all_markets
                        out[++o] = '</div>'; // End map-other-info
                    }

                    out[++o] = '</div>'; // End overflow-hidden

                    if (options.id) {
                        var element = document.getElementById(options.id);
                        element.innerHTML = out.join('');
                    } else {
                        $this.append(out.join(''));
                    }
                    updateList(data);
                    addActiveClassToSelectAllUsaButton(data);


                    if ($('#' + the_map_id).length > 0) {
                        var regionsSelectable = (!$this.hasClass('disabled'));
                        var regionStyleHover = {
                            fill: '#82BBD6'
                        };
                        if ($this.hasClass('disabled')) {
                            regionStyleHover = {
                                fill: '#82BBD6',
                                'fill-opacity': 1,
                                cursor: "default"
                            };
                        }
                        the_map = new jvm.Map({
                            container: $('#' + the_map_id),
                            map: 'world_mill_en',
                            backgroundColor: "#fff",
                            zoomOnScroll: false,
                            regionsSelectable: regionsSelectable,
                            regionStyle: {
                                initial: {
                                    fill: '#E1E1E1'
                                },
                                selected: {
                                    fill: '#4295CB'
                                },
                                hover: regionStyleHover
                            },
                            panOnDrag: true,
                            focusOn: {
                                x: 0.5,
                                y: 0.5,
                                scale: 1,
                                animate: true
                            },
                            onRegionSelected: function (e, code, isSelected, selectedRegions) {
                                if ($this.hasClass("onClick")) {
                                    var the_data = update_array_by_adding_or_removing_data(the_map_list, code);
                                    updateFooter(the_data);
                                    updateList(the_data);
                                    the_map_list = the_data;
                                    saveData();
                                }
                            },
                            onRegionClick: function (e, code) {
                                if ($this.hasClass('disabled')) {
                                    e.preventDefault();
                                    return;
                                }
                                if(options.with_usa && code == 'US'){ // Open USA Map
                                    $this.find('.content-map-world').addClass('inactive');
                                    $this.find('.content-map-usa').removeClass('inactive');
                                    init_usa_map();
                                    e.preventDefault();
                                    return;
                                }
                                $this.addClass("onClick");
                                setTimeout(function () {
                                    $this.removeClass("onClick");
                                }, 300);
                            },
                        });
                    }

                    if ($('#' + the_stepped_graph_id).length > 0) {
                        Chart.defaults.global.legend.display = false;
                        Chart.defaults.global.hover.intersect = false;

                        var customTooltipsSteppedMarket = function (tooltip) {
                            // Tooltip Element
                            var tooltipEl = $('#tooltip-step-market');
                            if (!tooltipEl[0]) {
                                $('body').append('<div id="tooltip-step-market" class="tooltipster-default"></div>');
                                tooltipEl = $('#tooltip-step-market');
                            }
                            if (!tooltip.opacity) {
                                tooltipEl.remove();
                                $('.chartjs-wrap canvas')
                                    .each(function (index, el) {
                                        $(el).css('cursor', 'default');
                                    });
                                return;
                            }
                            $(this._chart.canvas).css('cursor', 'pointer');
                            if (tooltip.body) {
                                tooltipEl.removeClass('right');
                                var the_index = tooltip.dataPoints[0]['index'];
                                if (the_index !== null) {
                                    var innovation = getInnovationById(innovation_id, true);
                                    var market_stepped_data = innovation['financial']['market_stepped_data'];
                                    var market_dataset = market_stepped_data['datasets'];

                                    var date_string = market_dataset[0]['data'][the_index]['x'];
                                    if (date_string) {
                                        var infos_tooltip = market_dataset[0]['infos_tooltip'][date_string];
                                        var nb_markets = infos_tooltip['nb'] > 1 ? infos_tooltip['nb']+ ' markets' : infos_tooltip['nb']+ ' market';
                                        var info_movement = '';
                                        if(infos_tooltip['movements'] != 0){
                                            var movement_number = (infos_tooltip['movements'] > 0) ? '+'+infos_tooltip['movements'] : infos_tooltip['movements'];
                                            var nb_movements = (Math.abs(infos_tooltip['movements']) > 1) ? movement_number +' markets' : movement_number +' market';
                                            var classe_movement = (infos_tooltip['movements'] > 0) ? 'positive' : 'negative';
                                            info_movement += '<span class="nb-movements '+classe_movement+'">'+nb_movements+'</span>';
                                        }
                                        info_movement += '<span class="date-change">In '+moment(date_string).format("MMM, YYYY");+'</span>';
                                        var top_tooltip = '<div class="top-tooltip"><div class="first-line">' + nb_markets + '</div><div class="second-line">' + info_movement + '</div></div>';
                                        var markets_string = '';
                                        var max_markets = 6;
                                        if(infos_tooltip['market_movements']['added'].length == 0 && infos_tooltip['market_movements']['removed'].length == 0 && infos_tooltip['markets'].length > 0){ // First case
                                            markets_string += '<div class="market_title">Added:</div>';
                                            var markets_array = get_country_name_array_by_code_array(infos_tooltip['markets']);
                                            for(var i = 0; i < markets_array.length; i++){
                                                if(i < max_markets) {
                                                    markets_string += '<div class="a_market">' + markets_array[i] + '</div>';
                                                }
                                            }
                                            if(markets_array.length > max_markets){
                                                markets_string += '<div class="a_market more">'+(markets_array.length - max_markets)+' more...</div>';
                                            }
                                        }
                                        if(infos_tooltip['market_movements']['added'].length > 0){
                                            markets_string += '<div class="market_title">Added:</div>';
                                            var markets_array = get_country_name_array_by_code_array(infos_tooltip['market_movements']['added']);
                                            for(var i = 0; i < markets_array.length; i++){
                                                if(i < max_markets) {
                                                    markets_string += '<div class="a_market">' + markets_array[i] + '</div>';
                                                }
                                            }
                                            if(markets_array.length > max_markets){
                                                markets_string += '<div class="a_market more">'+(markets_array.length - max_markets)+' more...</div>';
                                            }
                                        }
                                        if(infos_tooltip['market_movements']['removed'].length > 0){
                                            markets_string += '<div class="market_title">Removed:</div>';
                                            var markets_array = get_country_name_array_by_code_array(infos_tooltip['market_movements']['removed']);
                                            for(var i = 0; i < markets_array.length; i++){
                                                if(i < max_markets) {
                                                    markets_string += '<div class="a_market">' + markets_array[i] + '</div>';
                                                }
                                            }
                                            if(markets_array.length > max_markets){
                                                markets_string += '<div class="a_market more">'+(markets_array.length - max_markets)+' more...</div>';
                                            }
                                        }

                                        var markets_tooltip = '<div class="content-tooltip">'+markets_string+'</div>';
                                        var data = '<div class="pri-content-tooltip"><div class="pri-body-tooltip">' + top_tooltip + markets_tooltip + '</div><div class="triangle"></div></div>';
                                        tooltipEl.html(data);

                                        var height = tooltipEl.height();
                                        var width = tooltipEl.width();
                                        var left = currentMousePos.x - (width / 2);
                                        var top = currentMousePos.y - height - 20;
                                        tooltipEl.css({
                                            opacity: 1,
                                            left: left + 'px',
                                            top: top + 'px'
                                        });
                                    }
                                }
                            }
                        };


                        var market_stepped_data = innovation['financial']['market_stepped_data'];
                        var market_dataset = market_stepped_data['datasets'];

                        var maxY = 0;
                        for (var i = 0; i < market_dataset[0]['data'].length; i++){
                            if(market_dataset[0]['data'][i]['y'] > maxY){
                                maxY = market_dataset[0]['data'][i]['y'];
                            }
                        }
                        maxY += 10;
                        maxY = Math.ceil((maxY/10)) * 10;
                        var stepped_graph_canvas = document.getElementById(the_stepped_graph_id).getContext('2d');
                        stepped_graph = new Chart(stepped_graph_canvas, {
                            type: 'line',
                            //showTooltips: false,
                            data: market_stepped_data,
                            options: {
                                responsive: false,
                                maintainAspectRatio: false,
                                hover: {
                                    mode: false,
                                    intersect: false,
                                    animationDuration: 0
                                },
                                tooltips: {
                                    enabled: false,
                                    custom: customTooltipsSteppedMarket
                                },
                                scales: {
                                    xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        type: "time",
                                        time: {
                                            unit: 'year',
                                            displayFormats: {
                                                hour: 'YYYY'
                                            }
                                        },
                                        ticks: {
                                            fontSize: 14,
                                            fontColor: "#b5b5b5",
                                            fontFamily: "Work Sans"
                                        }
                                    }],
                                    yAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        ticks: {
                                            'min': 0,
                                            'max': maxY,
                                            fontSize: 14,
                                            fontColor: "#b5b5b5",
                                            fontFamily: "Work Sans"
                                        }

                                    }]
                                },
                                animation: {
                                    onComplete: function () {
                                        var ctx = this.chart.ctx;
                                        ctx.textAlign = "center";
                                        ctx.textBaseline = "bottom";

                                        // Y Axis
                                        ctx.fillStyle = "#4a4a4a";
                                        ctx.font = "16px Work Sans";
                                        ctx.fillText("Number of markets", 115, 18);
                                        // X Axis
                                        ctx.fillText("Years", 870, 380);
                                    }
                                }
                            }
                        });

                    }

                    updateAll(data);


                    $this.find('.content-market-list-tabs').tabs().addClass("ui-tabs-vertical ui-helper-clearfix");

                    $this.find('.a_market_on_list').click(function () {
                        if (!$this.hasClass('disabled')) {
                            if ($(this).hasClass('active')) {
                                $(this).removeClass('active');
                            } else {
                                $(this).addClass('active');
                            }
                            var the_data = getDataFromClick();
                            updateListCategories();
                            updateMap(the_data);
                            updateFooter(the_data);
                            the_map_list = the_data;
                            saveData();
                        }
                    });

                    $this.find('.back-to-world-map').click(function () {
                        $this.find('.content-map-world').removeClass('inactive');
                        $this.find('.content-map-usa').addClass('inactive');
                    });

                    $this.find('.select-all-usa').click(function () {
                        if($(this).hasClass('active')){
                            $(this).removeClass('active');
                            // Remove all USA MARKETS
                            $this.find('.a_market_on_list').each(function () {
                                if ($(this).attr('data-target').indexOf('US-') !== -1) {
                                    $(this).removeClass('active');
                                }
                            });
                        } else {
                            $(this).addClass('active');
                            // Add all USA MARKETS
                            $this.find('.a_market_on_list').each(function () {
                                if ($(this).attr('data-target').indexOf('US-') !== -1) {
                                    $(this).addClass('active');
                                }
                            });
                        }
                        var the_data = getDataFromClick();
                        updateListCategories();
                        updateMap(the_data);
                        updateFooter(the_data);
                        the_map_list = the_data;
                        saveData();
                    });

                    $this.find('.button-tab-market').click(function () {
                        if (!$(this).hasClass('active')) {
                            var $content_global = $(this).closest('.content-global-market-tabs');
                            $content_global.find('.button-tab-market').removeClass('active');
                            $content_global.find('.market-tab').removeClass('active');
                            $(this).addClass('active');
                            var target = $(this).attr('data-target');
                            $('#'+target).addClass('active');
                        }
                    });

                    $this.find('#button-download-' + identifier + '').click(function () {
                        if ($('#' + the_map_id + ' svg').attr('id') != 'map_svg_' + identifier) {
                            $('#' + the_map_id + ' svg').attr('id', 'map_svg_' + identifier);
                        }
                        var id = parseInt($this.attr('data-id'));
                        var innovation = getInnovationById(id, true);
                        var title = innovation['title'];
                        saveSvgAsPng(document.getElementById('map_svg_' + identifier), title + " - markets.png", {backgroundColor: '#F6F7F9', canvg:window.canvg});
                    });

                    isInited = true;
                }
            });
        }
    };

})(jQuery);