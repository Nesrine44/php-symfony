<?php
/**
 * Created by PhpStorm.
 * User: jonathan.garcia
 * Date: 11/07/2018
 * Time: 14:13
 */


namespace AppBundle;

use AppBundle;


class UtilsCountry{

    const COUNTRY_GROUP_ID_EMPTY = 0;
    const COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST = 1;
    const COUNTRY_GROUP_ID_AMERICAS = 2;
    const COUNTRY_GROUP_ID_USA = 3;
    const COUNTRY_GROUP_ID_ASIA_OCEANIA = 4;
    const COUNTRY_GROUP_ID_EUROPE = 5;
    const COUNTRY_GROUP_ID_FRANCE = 6;
    const COUNTRY_GROUP_ID_POLAR_LANDS = 7;
    const COUNTRY_GROUP_ID_DUTY_FREE = 8;

    /**
     * Get country group name.
     *
     * @param int $group_id
     * @return string
     */
    function get_country_group_name($group_id)
    {
        switch ($group_id) {
            case self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST:
                return "Africa & Middle East";
            case self::COUNTRY_GROUP_ID_AMERICAS:
                return "Americas";
            case self::COUNTRY_GROUP_ID_ASIA_OCEANIA:
                return "Asia & Oceania";
            case self::COUNTRY_GROUP_ID_EUROPE:
                return "Europe";
            case self::COUNTRY_GROUP_ID_FRANCE:
                return "France";
            case self::COUNTRY_GROUP_ID_POLAR_LANDS:
                return "Polar lands";
            case self::COUNTRY_GROUP_ID_DUTY_FREE:
                return "Travel Retail";
            case self::COUNTRY_GROUP_ID_USA:
                return "USA";
            default:
                return "Other";
        }
    }

    /**
     * Get all countries.
     *
     * @return array
     */
    public static function getAllCountries($by_group = false)
    {
        $countries = array(
            array(
                "country_code" => "BD",
                "country_name" => "Bangladesh",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "BE",
                "country_name" => "Belgium",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "BF",
                "country_name" => "Burkina Faso",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "BG",
                "country_name" => "Bulgaria",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "BA",
                "country_name" => "Bosnia and Herz.",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "BN",
                "country_name" => "Brunei",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "BO",
                "country_name" => "Bolivia",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "JP",
                "country_name" => "Japan",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "BI",
                "country_name" => "Burundi",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "BJ",
                "country_name" => "Benin",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "BT",
                "country_name" => "Bhutan",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "JM",
                "country_name" => "Jamaica",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "BW",
                "country_name" => "Botswana",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "BR",
                "country_name" => "Brazil",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "BS",
                "country_name" => "Bahamas",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "BY",
                "country_name" => "Belarus",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "BZ",
                "country_name" => "Belize",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "RU",
                "country_name" => "Russia",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "RW",
                "country_name" => "Rwanda",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "RS",
                "country_name" => "Serbia",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "TL",
                "country_name" => "Timor-Leste",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "TM",
                "country_name" => "Turkmenistan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "TJ",
                "country_name" => "Tajikistan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "RO",
                "country_name" => "Romania",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "GW",
                "country_name" => "Guinea-Bissau",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "GT",
                "country_name" => "Guatemala",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "GR",
                "country_name" => "Greece",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "GQ",
                "country_name" => "Eq. Guinea",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "GY",
                "country_name" => "Guyana",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "GE",
                "country_name" => "Georgia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "GB",
                "country_name" => "United Kingdom",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "GA",
                "country_name" => "Gabon",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "GN",
                "country_name" => "Guinea",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "GM",
                "country_name" => "Gambia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "GL",
                "country_name" => "Greenland",
                "group_id" => self::COUNTRY_GROUP_ID_POLAR_LANDS
            ),
            array(
                "country_code" => "GH",
                "country_name" => "Ghana",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "OM",
                "country_name" => "Oman",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "TN",
                "country_name" => "Tunisia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "JO",
                "country_name" => "Jordan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "HR",
                "country_name" => "Croatia",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "HT",
                "country_name" => "Haiti",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "HU",
                "country_name" => "Hungary",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "HN",
                "country_name" => "Honduras",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "PR",
                "country_name" => "Puerto Rico",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "PS",
                "country_name" => "Palestine",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "PT",
                "country_name" => "Portugal",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "PY",
                "country_name" => "Paraguay",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "PA",
                "country_name" => "Panama",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "PG",
                "country_name" => "Papua New Guinea",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "PE",
                "country_name" => "Peru",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "PK",
                "country_name" => "Pakistan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "PH",
                "country_name" => "Philippines",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "PL",
                "country_name" => "Poland",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "ZM",
                "country_name" => "Zambia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "EH",
                "country_name" => "W. Sahara",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "EE",
                "country_name" => "Estonia",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "EG",
                "country_name" => "Egypt",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "ZA",
                "country_name" => "South Africa",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "EC",
                "country_name" => "Ecuador",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "IT",
                "country_name" => "Italy",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "VN",
                "country_name" => "Vietnam",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "SB",
                "country_name" => "Solomon Is.",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "ET",
                "country_name" => "Ethiopia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "SO",
                "country_name" => "Somalia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "ZW",
                "country_name" => "Zimbabwe",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "ES",
                "country_name" => "Spain",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "ER",
                "country_name" => "Eritrea",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "ME",
                "country_name" => "Montenegro",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "MD",
                "country_name" => "Moldova",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "MG",
                "country_name" => "Madagascar",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "MA",
                "country_name" => "Morocco",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "UZ",
                "country_name" => "Uzbekistan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "MM",
                "country_name" => "Myanmar",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "ML",
                "country_name" => "Mali",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "MN",
                "country_name" => "Mongolia",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "MK",
                "country_name" => "Macedonia",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "MW",
                "country_name" => "Malawi",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "MR",
                "country_name" => "Mauritania",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "UG",
                "country_name" => "Uganda",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "MY",
                "country_name" => "Malaysia",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "MX",
                "country_name" => "Mexico",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "IL",
                "country_name" => "Israel",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "FR",
                "country_name" => "France",
                "group_id" => self::COUNTRY_GROUP_ID_FRANCE
            ),
            array(
                "country_code" => "XS",
                "country_name" => "Somaliland",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "FI",
                "country_name" => "Finland",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "FJ",
                "country_name" => "Fiji",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "FK",
                "country_name" => "Falkland Is.",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "NI",
                "country_name" => "Nicaragua",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "NL",
                "country_name" => "Netherlands",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "NO",
                "country_name" => "Norway",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "NA",
                "country_name" => "Namibia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "VU",
                "country_name" => "Vanuatu",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "NC",
                "country_name" => "New Caledonia",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "NE",
                "country_name" => "Niger",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "NG",
                "country_name" => "Nigeria",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "NZ",
                "country_name" => "New Zealand",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "NP",
                "country_name" => "Nepal",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "XK",
                "country_name" => "Kosovo",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "CI",
                "country_name" => "Côte d'Ivoire",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "CH",
                "country_name" => "Switzerland",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "CO",
                "country_name" => "Colombia",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "CN",
                "country_name" => "China",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "CM",
                "country_name" => "Cameroon",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "CL",
                "country_name" => "Chile",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "XC",
                "country_name" => "N. Cyprus",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "CA",
                "country_name" => "Canada",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "CG",
                "country_name" => "Congo",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "CF",
                "country_name" => "Central African Rep.",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "CD",
                "country_name" => "Dem. Rep. Congo",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "CZ",
                "country_name" => "Czech Rep.",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "CY",
                "country_name" => "Cyprus",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "CR",
                "country_name" => "Costa Rica",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "CU",
                "country_name" => "Cuba",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "SZ",
                "country_name" => "Swaziland",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "SY",
                "country_name" => "Syria",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "KG",
                "country_name" => "Kyrgyzstan",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "KE",
                "country_name" => "Kenya",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "SS",
                "country_name" => "S. Sudan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "SR",
                "country_name" => "Suriname",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "KH",
                "country_name" => "Cambodia",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "SV",
                "country_name" => "El Salvador",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "SK",
                "country_name" => "Slovakia",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "KR",
                "country_name" => "Korea",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "SI",
                "country_name" => "Slovenia",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "KP",
                "country_name" => "Dem. Rep. Korea",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "KW",
                "country_name" => "Kuwait",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "SN",
                "country_name" => "Senegal",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "SL",
                "country_name" => "Sierra Leone",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "KZ",
                "country_name" => "Kazakhstan",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "SA",
                "country_name" => "Saudi Arabia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "SE",
                "country_name" => "Sweden",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "SD",
                "country_name" => "Sudan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "DO",
                "country_name" => "Dominican Rep.",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "DJ",
                "country_name" => "Djibouti",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "DK",
                "country_name" => "Denmark",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "DE",
                "country_name" => "Germany",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "YE",
                "country_name" => "Yemen",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "DZ",
                "country_name" => "Algeria",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "US",
                "country_name" => "United States",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "UY",
                "country_name" => "Uruguay",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "LB",
                "country_name" => "Lebanon",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "LA",
                "country_name" => "Lao PDR",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "TW",
                "country_name" => "Taiwan",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "TT",
                "country_name" => "Trinidad and Tobago",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "TR",
                "country_name" => "Turkey",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "LK",
                "country_name" => "Sri Lanka",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "LV",
                "country_name" => "Latvia",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "LT",
                "country_name" => "Lithuania",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "LU",
                "country_name" => "Luxembourg",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "LR",
                "country_name" => "Liberia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "LS",
                "country_name" => "Lesotho",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "TH",
                "country_name" => "Thailand",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "TF",
                "country_name" => "Fr. S. Antarctic Lands",
                "group_id" => self::COUNTRY_GROUP_ID_POLAR_LANDS
            ),
            array(
                "country_code" => "TG",
                "country_name" => "Togo",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "TD",
                "country_name" => "Chad",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "LY",
                "country_name" => "Libya",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "AE",
                "country_name" => "United Arab Emirates",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "VE",
                "country_name" => "Venezuela",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "AF",
                "country_name" => "Afghanistan",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "IQ",
                "country_name" => "Iraq",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "IS",
                "country_name" => "Iceland",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "IR",
                "country_name" => "Iran",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "AM",
                "country_name" => "Armenia",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "AL",
                "country_name" => "Albania",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "AO",
                "country_name" => "Angola",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "AR",
                "country_name" => "Argentina",
                "group_id" => self::COUNTRY_GROUP_ID_AMERICAS
            ),
            array(
                "country_code" => "AU",
                "country_name" => "Australia",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "AT",
                "country_name" => "Austria",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "IN",
                "country_name" => "India",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "TZ",
                "country_name" => "Tanzania",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "AZ",
                "country_name" => "Azerbaijan",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "IE",
                "country_name" => "Ireland",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "ID",
                "country_name" => "Indonesia",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "UA",
                "country_name" => "Ukraine",
                "group_id" => self::COUNTRY_GROUP_ID_EUROPE
            ),
            array(
                "country_code" => "QA",
                "country_name" => "Qatar",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "MZ",
                "country_name" => "Mozambique",
                "group_id" => self::COUNTRY_GROUP_ID_AFRICA_MIDDLE_EAST
            ),
            array(
                "country_code" => "HK",
                "country_name" => "Hong Kong",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "DF1",
                "country_name" => "TR Americas",
                "group_id" => self::COUNTRY_GROUP_ID_DUTY_FREE
            ),
            array(
                "country_code" => "DF2",
                "country_name" => "TR Europe",
                "group_id" => self::COUNTRY_GROUP_ID_DUTY_FREE
            ),
            array(
                "country_code" => "DF3",
                "country_name" => "TR Africa and Middle East",
                "group_id" => self::COUNTRY_GROUP_ID_DUTY_FREE
            ),
            array(
                "country_code" => "DF4",
                "country_name" => "TR Asia",
                "group_id" => self::COUNTRY_GROUP_ID_DUTY_FREE
            ),
            array(
                "country_code" => "SG",
                "country_name" => "Singapore",
                "group_id" => self::COUNTRY_GROUP_ID_ASIA_OCEANIA
            ),
            array(
                "country_code" => "US-VA",
                "country_name" => "Virginia",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-PA",
                "country_name" => "Pennsylvania",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-TN",
                "country_name" => "Tennessee",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-WV",
                "country_name" => "West Virginia",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-NV",
                "country_name" => "Nevada",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-TX",
                "country_name" => "Texas",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-NH",
                "country_name" => "New Hampshire",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-NY",
                "country_name" => "New York",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-HI",
                "country_name" => "Hawaii",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-VT",
                "country_name" => "Vermont",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-NM",
                "country_name" => "New Mexico",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-NC",
                "country_name" => "North Carolina",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-ND",
                "country_name" => "North Dakota",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-NE",
                "country_name" => "Nebraska",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-LA",
                "country_name" => "Louisiana",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-SD",
                "country_name" => "South Dakota",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-DC",
                "country_name" => "District of Columbia",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-DE",
                "country_name" => "Delaware",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-FL",
                "country_name" => "Florida",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-CT",
                "country_name" => "Connecticut",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-WA",
                "country_name" => "Washington",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-KS",
                "country_name" => "Kansas",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-WI",
                "country_name" => "Wisconsin",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-OR",
                "country_name" => "Oregon",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-KY",
                "country_name" => "Kentucky",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-ME",
                "country_name" => "Maine",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-OH",
                "country_name" => "Ohio",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-OK",
                "country_name" => "Oklahoma",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-ID",
                "country_name" => "Idaho",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-WY",
                "country_name" => "Wyoming",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-UT",
                "country_name" => "Utah",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-IN",
                "country_name" => "Indiana",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-IL",
                "country_name" => "Illinois",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-AK",
                "country_name" => "Alaska",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-NJ",
                "country_name" => "New Jersey",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-CO",
                "country_name" => "Colorado",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-MD",
                "country_name" => "Maryland",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-MA",
                "country_name" => "Massachusetts",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-AL",
                "country_name" => "Alabama",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-MO",
                "country_name" => "Missouri",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-MN",
                "country_name" => "Minnesota",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-CA",
                "country_name" => "California",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-IA",
                "country_name" => "Iowa",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-MI",
                "country_name" => "Michigan",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-GA",
                "country_name" => "Georgia",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-AZ",
                "country_name" => "Arizona",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-MT",
                "country_name" => "Montana",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-MS",
                "country_name" => "Mississippi",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-SC",
                "country_name" => "South Carolina",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-RI",
                "country_name" => "Rhode Island",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            ),
            array(
                "country_code" => "US-AR",
                "country_name" => "Arkansas",
                "group_id" => self::COUNTRY_GROUP_ID_USA
            )
        );
        if ($by_group) {
            $ret = array();
            foreach ($countries as $country) {
                $ret[$country['group_id']][] = $country;
            }
            return $ret;
        }
        return $countries;
    }

    /**
     * get country name by code.
     *
     * @param string $code
     * @return string
     */
    public static function getCountryNameByCode($code)
    {
        $all_countries = self::getAllCountries();
        foreach ($all_countries as $country) {
            if ($country['country_code'] == $code) {
                return $country['country_name'];
            }
        }
        return $code;
    }

    /**
     * Get country array by code array.
     *
     * @param array $code_array
     * @return array
     */
    public static function getCountryArrayByCodeArray($code_array)
    {
        $all_countries = self::getAllCountries();
        $ret = array();
        foreach ($all_countries as $country) {
            foreach ($code_array as $code) {
                if ($country['country_code'] == $code) {
                    $ret[] = $country;
                }
            }
        }
        return $ret;
    }

    /**
     * Get country name array by code array.
     *
     * @param array $code_array
     * @return array
     */
    public static function getCountryNameArrayByCodeArray($code_array)
    {
        $all_countries = self::getAllCountries();
        $ret = array();
        foreach ($all_countries as $country) {
            foreach ($code_array as $code) {
                if ($country['country_code'] == $code) {
                    $ret[] = $country['country_name'];
                }
            }
        }
        return $ret;
    }
}

