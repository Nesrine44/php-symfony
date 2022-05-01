var ACTIVITY_ACTION_PROMOTE_INNOVATION_VIEW = 15;
var ACTIVITY_ACTION_PROMOTE_INNOVATION_EXPORT = 16;
var METRICS_ACTION_ID_SHOW_ACTIVITY = 4;
var METRICS_ACTION_ID_SHOW_ACTIVITY_VIEWS = 5;
var METRICS_ACTION_ID_SHOW_ACTIVITY_DOWNLOADS = 6;
var METRICS_ACTION_ID_SHOW_ACTIVITY_TAB_VIEWS = 7;
var METRICS_ACTION_ID_SHOW_ACTIVITY_TAB_DOWNLOADS = 8;
var METRICS_ACTION_ID_TIDIO_EVENT_ELEVATORPITCH_COMPLETED = 9;
var METRICS_ACTION_ID_TIDIO_EVENT_FINANCIAL_COMPLETED = 10;


/**
 * get innovation classifications
 * @return {[*,*,*,*]}
 */
function get_innovation_classifications() {
    return [
        { 'id': 'Product', 'text': 'Product' },
        { 'id': 'Service', 'text': 'Service' }
    ];
}

/**
 * get innovation growth_model
 * @return {[*,*]}
 */
function get_innovation_growth_model() {
    return [
        { 'id': 'fast_growth', 'text': 'Fast Growth', 'description': '2/4 years growth model' },
        { 'id': 'slow_build', 'text': 'Slow Build', 'description': '4/8 years growth model' }
    ];
}

/**
 * get_description_for_growth_model
 * @param growth_model
 * @returns {string}
 */
function get_description_for_growth_model(growth_model) {
    var out = [],
        o = -1;
    if (growth_model == 'fast_growth') {
        out[++o] = '- Typically line extensions from strong brands<br>';
        out[++o] = '- Endorsed by a mother brand with strong equity where it is launched (less education and awareness to do)<br>';
        out[++o] = '- Success in 1 market first<br>';
        out[++o] = '- Window of 2-4 years to rollout globally<br>';
        out[++o] = '- ROI: short/mid-term payback after inflection point<br>';
    } else {
        out[++o] = '- Typically New-To-World or stretch from the mother brand + M&A and sleeping beauties (needs more time and investment to build awareness and ROI) <br>';
        out[++o] = '- Taps into clear consumer insight / opportunity<br>';
        out[++o] = '- Brand building over a sustained period > 5 years<br>';
        out[++o] = '- Acceleration once inflection point is reached due to strong advocacy<br>';
        out[++o] = '- ROI: mid/long-term payback after inflection point<br>';
    }
    return out.join('');
}

/**
 * get_innovation_growth_strategy
 * @returns {*[]}
 */
function get_innovation_growth_strategy(is_service) {
    is_service = (typeof is_service != 'undefined') ? is_service : false;
    if (is_service) {
        return [
            { 'id': 'Contributor', 'text': 'Contributor' },
            { 'id': 'New Business Acceleration', 'text': 'New Business Acceleration' }
        ];
    }
    return [
        { 'id': 'Contributor', 'text': 'Contributor' },
        { 'id': 'Big Bet', 'text': 'Big Bet' },
        { 'id': 'Top contributor', 'text': 'Top contributor' },
        { 'id': 'Negative CAAP', 'text': 'Negative CAAP' },
        { 'id': 'High investment', 'text': 'High investment' }
    ];
}

/**
 * get_innovation_categories
 *
 * @returns {*[]}
 */
function get_innovation_categories() {
    return [
        { 'id': 'Aniseed', 'text': 'Aniseed', 'key': 'aniseed' },
        { 'id': 'Bitters', 'text': 'Bitters', 'key': 'bitters' },
        { 'id': 'Brandy non Cognac', 'text': 'Brandy non Cognac', 'key': 'brandy_non_cognac' },
        { 'id': 'Cognac', 'text': 'Cognac', 'key': 'cognac' },
        { 'id': 'Gin', 'text': 'Gin', 'key': 'cin' },
        { 'id': 'Intl Champagne', 'text': 'Intl Champagne', 'key': 'intl_champagne' },
        { 'id': 'Liqueurs', 'text': 'Liqueurs', 'key': 'liqueurs' },
        { 'id': 'Non-Scotch Whisky', 'text': 'Non-Scotch Whisky', 'key': 'non_scotch_whisky' },
        { 'id': 'RTD/RTS', 'text': 'RTD/RTS', 'key': 'rtd_rts' },
        { 'id': 'Rum', 'text': 'Rum', 'key': 'rum' },
        { 'id': 'Scotch Whisky', 'text': 'Scotch Whisky', 'key': 'scotch_whisky' },
        { 'id': 'Tequila', 'text': 'Tequila', 'key': 'tequila' },
        { 'id': 'Vodka', 'text': 'Vodka', 'key': 'vodka' },
        { 'id': 'Wine', 'text': 'Wine', 'key': 'wine' },
        { 'id': 'Other', 'text': 'Other', 'key': 'other' }
    ];
}





/**
 * get_investment_models
 *
 * @returns {*[]}
 */
function get_innovation_investment_model() {
    return [
        { 'id': 'Internal Development', 'text': 'Internal Development' },
        { 'id': 'External Acquisition', 'text': 'External Acquisition', },
        { 'id': 'Co-partnership', 'text': 'Co-partnership' },
        { 'id': 'Empty', 'text': 'Empty' }
    ];
}



/**
 * get_innovation_new_business_opportunity
 *
 * @returns {*[]}
 */
function get_innovation_new_business_opportunity() {
    return [
        { 'id': 'Products beyond liquids', 'text': 'Products beyond liquids' },
        { 'id': 'Valuing assets and expertise', 'text': 'Valuing assets and expertise', },
        { 'id': 'B to C services', 'text': 'B to C services' },
        { 'id': 'B to B services', 'text': 'B to B services' },
        { 'id': 'Empty', 'text': 'Empty' }
    ];
}





/**
 * get_moments_of_convivialite
 * @returns {*[]}
 */
function get_moments_of_convivialite() {
    return [
        { 'id': "Kicking back", 'text': "Kicking back", 'key': "kicking_back" },
        { 'id': "My time", 'text': "My time", 'key': "my_time" },
        { 'id': "Daily Calm", 'text': "Daily Calm", 'key': "daily_calm" },
        { 'id': "Special dinner and drinks", 'text': "Special dinner and drinks", 'key': "special_dinner_and_drinks" },
        { 'id': "Sip & Savour", 'text': "Sip & Savour", 'key': "sip_savour" },
        { 'id': "Chilling with friends", 'text': "Chilling with friends", 'key': "chilling_with_friends" },
        { 'id': "Living the good life", 'text': "Living the good life", 'key': "living_the_good_life" },
        { 'id': "Premium socialising", 'text': "Premium socialising", 'key': "premium_socialising" },
        { 'id': "Enjoying the good times", 'text': "Enjoying the good times", 'key': "enjoying_the_good_times" },
        { 'id': "Be seen while socialising", 'text': "Be seen while socialising", 'key': "be_see_while_socialising" },
        { 'id': "The night is ours", 'text': "The night is ours", 'key': "the_night_is_ours" },
        { 'id': "Trendu night out", 'text': "Trendu night out", 'key': "trendu_night_out" },
        { 'id': 'Other', 'text': 'Other', 'key': 'other' },
        { 'id': 'Empty', 'text': 'Empty', 'key': 'empty' }
    ];
}

/**
 * get_business_drivers
 * @param is_a_service
 * @returns {*[]}
 */
function get_business_drivers(is_a_service) {
    if (is_a_service) {
        return [
            //{'id': 'Craft', 'text': 'Craft', 'key': 'craft'},
            { 'id': 'RTD / RTS', 'text': 'RTD / RTS', 'key': 'rtd_rts' },
            { 'id': 'No & Low Alcohol', 'text': 'No & Low Alcohol', 'key': 'no_low_alcohol' },
            { 'id': 'Monetizing Experiences', 'text': 'Monetizing Experiences', 'key': 'monetizing_experiences' },
            { 'id': 'Specialty', 'text': 'Specialty', 'key': 'specialty' },
            { 'id': 'None', 'text': 'None', 'key': 'none' }
        ];
    } else {
        return [
            //{'id': 'Craft', 'text': 'Craft', 'key': 'craft'},
            { 'id': 'RTD / RTS', 'text': 'RTD / RTS', 'key': 'rtd_rts' },
            { 'id': 'No & Low Alcohol', 'text': 'No & Low Alcohol', 'key': 'no_low_alcohol' },
            { 'id': 'Specialty', 'text': 'Specialty', 'key': 'specialty' },
            { 'id': 'None', 'text': 'None', 'key': 'none' }
        ];
    }
}

/**
 * get_innovation_price_bands
 * @returns {*[]}
 */
function get_innovation_price_bands() {
    return [
        { 'id': 'Local / Value (<10$)', 'text': 'Local / Value (<10$)' },
        { 'id': 'Standard (10 – 16$)', 'text': 'Standard (10 – 16$)' },
        { 'id': 'Premium (17 – 25$)', 'text': 'Premium (17 – 25$)' },
        { 'id': 'Super Premium (26 – 41$)', 'text': 'Super Premium (26 – 41$)' },
        { 'id': 'Ultra Premium (42 – 84$)', 'text': 'Ultra Premium (42 – 84$)' },
        { 'id': 'Prestige (> 84$)', 'text': 'Prestige (> 84$)' }
    ];
}

/**
 * get_consumer_opportunities
 * @returns {*[]}
 */
function get_consumer_opportunities() {
    return [{
            'id': '1',
            'text': 'Human Authenticity',
            'help': 'Craft, Hyper-local<br><br>- Build future roots & craft<br>- Connect with real people behind products'
        },
        {
            'id': '2',
            'text': 'Easy at Home & Everywhere',
            'help': 'Convenience, Catering, Delivery @ home<br><br>- Create seamless memorable experiences<br>- At home & everywhere on-demand'
        },
        {
            'id': '3',
            'text': 'Shaking The Codes',
            'help': 'Flavours, New origins, Experience beyond product<br><br>- Give a new take on something familiar<br>- Discovery & experiences'
        },
        {
            'id': '4',
            'text': 'Power to Consumers',
            'help': 'Personalization, Customization, Crowdsourcing, DIY<br><br>- Contribute, not just consume<br>- Creative collectives'
        },
        {
            'id': '5',
            'text': 'Feminine Identity',
            'help': 'Late family, Renagade fiminism, Economic empowerment<br><br>- Recognize female needs beyond clichés<br>- Gender neutrality & fluid identities'
        },
        {
            'id': '6',
            'text': 'Doing Good',
            'help': 'Social entrepreneurship, Activism, Upcycling, Transparency<br><br>- Longview consumption<br>- Make a positive societal impact'
        },
        {
            'id': '7',
            'text': 'Better for Me',
            'help': 'Moderation, Balance<br><br>- Healthy hedonism<br>- Self-optimisation'
        },
        {
            'id': '8',
            'text': 'Tactical Innovation',
            'help': 'Your innovation is not based on<br>one of these opportunities<br>i.e.: price gap...'
        }
    ];
}

/**
 * get_innovation_moment_of_convivialite
 * @returns {*[]}
 */
function get_innovation_moment_of_convivialite() {
    return [
        { 'id': 'Kicking back', 'text': 'Kicking back' },
        { 'id': 'My time', 'text': 'My time' },
        { 'id': 'Special dinner & drinks', 'text': 'Special dinner & drinks' },
        { 'id': 'Sip & savour', 'text': 'Sip & savour' },
        { 'id': 'Chilling with friends', 'text': 'Chilling with friends' },
        { 'id': 'Living the good life', 'text': 'Living the good life' },
        { 'id': 'Premium socialising', 'text': 'Premium socialising' },
        { 'id': 'Enjoying the good times', 'text': 'Enjoying the good times' },
        { 'id': 'Be seen while socialising', 'text': 'Be seen while socialising' },
        { 'id': 'The night is ours', 'text': 'The night is ours' },
        { 'id': 'Trendy night out', 'text': 'Trendy night out' }
    ];
}

/**
 * get innovations type
 * @return {[*,*,*,*,*,*]}
 */
function get_innovation_types(is_service) {
    is_service = (typeof is_service != 'undefined') ? is_service : false;
    if (is_service) {
        return [
            { 'id': 'New business model', 'text': 'New business model' },
            { 'id': 'Empty', 'text': 'Empty' }
        ];
    }
    return [
        { 'id': 'Stretch', 'text': 'Stretch' },
        { 'id': 'Incremental', 'text': 'Incremental' },
        { 'id': 'Breakthrough', 'text': 'Breakthrough' },
        { 'id': 'Empty', 'text': 'Empty' }
    ];
}