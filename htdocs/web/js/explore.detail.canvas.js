var request_load_canvas = null;

/**
 * explore_get_canvas_tab_html_template
 * @param id
 * @returns {*}
 */
function explore_get_canvas_tab_html_template(id) {
    var out = [], o = -1;
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    out[++o] = '<div class="central-content-994 padding-top-30">'; // Start central-content-994
    out[++o] = '<div class="contextual-headline">';
    out[++o] = innovation['title'];
    out[++o] = '</div>';
    out[++o] = '<div class="headline padding-top-5 font-montserrat-700">Canvas</div>';

    out[++o] = '<div class="font-work-sans-300 font-size-19 color-000 line-height-1-58 margin-top-40">';
    out[++o] = 'The Business Model Canvas is a strategic management template assessing value on a new business idea & highlighting area to experiment. Its visual chart captures 4 key elements: Innovation Sweet Spot, Consumer Traction & Business Stickiness, New key Activities & Value Chain Expertise, New Revenue Streams & Burn Rate. This tool comes from the Start Up world and has been adapted to Pernod Ricard Culture to capture any New Business in the same format and build on our collective intelligence at a group level.';
    out[++o] = '</div>';

    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 margin-top-110 margin-bottom-40">New Revenue coming from</div>';

    out[++o] = '</div>'; // End central-content-994

    out[++o] = '<div class="explore-detail-content-canvas">'; // Start explore-detail-content-canvas
    out[++o] = '<div class="container-all-canvas">';
    var canvas_array = innovation['canvas_collection'];
    if (canvas_array.length > 0) {
        for (var i = 0; i < canvas_array.length; i++) {
            out[++o] = explore_get_canvas_html_template(canvas_array[i], innovation['id']);
        }
    } else {
        out[++o] = explore_get_empty_canvas_html_template(innovation['id']);
    }
    out[++o] = '</div>';
    out[++o] = explore_get_new_canvas_button_html_template(innovation['id']);
    out[++o] = '</div>'; // End explore-detail-content-canvas


    return out.join('');
}

/**
 * explore_init_tab_canvas
 * @param id
 */
function explore_init_tab_canvas(id) {
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return;
    }
    if (innovation['canvas_collection'].length === 0) {
        $('.explore-detail-button-action-add-new-canvas').prop('disabled', true);
    }
}


/**
 * explore_get_canvas_html_template.
 *
 * @param canvas
 * @param innovation_id
 * @returns {string}
 */
function explore_get_canvas_html_template(canvas, innovation_id) {
    var id = (innovation_id) ? innovation_id : $('#detail-id-innovation').val();
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    var out = [], o = -1;
    var edition_mode_quali_enabled = (check_if_user_is_hq() || (user_can_edit_this_innovation(innovation)) && check_if_data_quali_capture_is_opened());
    var field_disabled_quali = ((edition_mode_quali_enabled) ? '' : 'disabled');
    var canvas_id = (canvas) ? canvas['id'] : 'new';
    var canvas_class = 'canvas-' + canvas_id;
    out[++o] = '<div class="content-container-canvas ' + canvas_class + '">';
    out[++o] = '<input type="hidden" name="canvas_id" value="' + canvas_id + '">';

    out[++o] = '<div class="central-content-994">'; // Start central-content-994
    out[++o] = '<div class="form-element">'; // Start form-element
    out[++o] = '<label class="font-15">Canvas name (revenue coming from…)</label>';
    out[++o] = '<div class="content-input-text">';
    out[++o] = '<input type="text" ' + field_disabled_quali + ' class="field optional not-innovation-data"  name="title" placeholder="ie: advertising" data-value="' + ((canvas) ? canvas['title'] : '') + '"/>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End form-element

    out[++o] = '<div class="form-element margin-bottom-0">'; // Start form-element
    out[++o] = '<label class="font-15">Inspiring existing business model</label>';
    out[++o] = '<div class="under-label">There is probably an existing Business Model in another industry that you could learn from, replicating some of its principles to your activity.</div>';
    out[++o] = '<div class="content-textarea">';
    out[++o] = '<textarea class="field optional not-innovation-data" name="description" ' + field_disabled_quali + '>' + ((canvas) ? canvas['description'] : '') + '</textarea>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End form-element
    out[++o] = '</div>'; // End central-content-994

    out[++o] = '<div class="central-content padding-vertical-50">';
    out[++o] = '<div class="content-canvas">'; // Start content-canvas
    out[++o] = explore_get_content_canvas_html_template(canvas, innovation_id);
    out[++o] = '</div>'; // End content-canvas
    out[++o] = '</div>';

    out[++o] = '</div>';
    return out.join('');
}

/**
 * explore_get_content_canvas_html_template.
 *
 * @param canvas
 * @param innovation_id
 * @param editable
 * @returns {string}
 */
function explore_get_content_canvas_html_template(canvas, innovation_id, editable) {
    editable = (typeof editable != 'undefined') ? editable : null;
    innovation_id = (typeof  innovation_id != 'undefined') ? innovation_id : null;
    var id = (innovation_id) ? innovation_id : $('#detail-id-innovation').val();
    var tooltip_class = "";
    var tooltip_title = "";
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    var out = [], o = -1;
    var edition_mode_quali_enabled = (check_if_user_is_hq() || (user_can_edit_this_innovation(innovation)) && check_if_data_quali_capture_is_opened());
    if(editable !== null){
        edition_mode_quali_enabled = editable;
    }
    var active_block = (canvas) ? canvas['active_block'] : '1';
    var class_block = (canvas && !canvas['to_complete']['1']) ? 'to-hover' : '';
    if (active_block == '1') {
        class_block += ' active';
    }
    out[++o] = '<div class="canvas-block canvas-block-1 ' + class_block + '">'; // Start canvas-block-1
    if (!canvas || canvas['to_complete']['1']) {
        out[++o] = '<div class="empty-state-with-button">'; // Start empty-state-with-button
        out[++o] = '<div class="number font-montserrat-700 font-size-30 padding-bottom-10">1.</div>';
        out[++o] = '<div class="description font-montserrat-700 font-size-30 transition-background-color">INNOVATION SWEET SPOT</div>';
        if (active_block == '1' && edition_mode_quali_enabled) {
            out[++o] = '<div class="content-button transition-opacity padding-top-30">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="1" data-step="1_a">Start</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; // End empty-state-with-button
        out[++o] = '<div class="picto-3-bulles"></div>';
    } else {
        if(edition_mode_quali_enabled) {
            out[++o] = '<div class="canvas-block-border transition-opacity"></div>';
            out[++o] = '<div class="content-button-edit transition-opacity">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="1" data-step="1_a">Edit</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }

        out[++o] = '<div class="position-relative padding-15">';
        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_1_a"';
        out[++o] = '<div class="block-text-canvas '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="icon consumer light"></div>';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><span class="color-000-0-3">1A • </span>Consumer Benefit</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_1_a'] + '</div>';
        out[++o] = '</div>'; // End block-text-canvas

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_1_b"';
        out[++o] = '<div class="block-text-canvas '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="icon business-priority light"></div>';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><span class="color-000-0-3">1B • </span>Source of business</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_1_b'] + '</div>';
        out[++o] = '</div>'; // End block-text-canvas

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_1_c"';
        out[++o] = '<div class="block-text-canvas '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="icon value-proposition light"></div>';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><span class="color-000-0-3">1C • </span>Unique Value Proposition</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_1_c'] + '</div>';
        out[++o] = '</div>'; // End block-text-canvas

        out[++o] = '</div>';
    }
    out[++o] = '</div>'; // End canvas-block-1

    class_block = (canvas && !canvas['to_complete']['2']) ? 'to-hover' : '';
    if (active_block == '2') {
        class_block += ' active';
    }
    out[++o] = '<div class="canvas-block canvas-block-2 ' + class_block + '">'; // Start canvas-block-2
    if (!canvas || canvas['to_complete']['2']) {
        out[++o] = '<div class="empty-state-with-button">'; // Start empty-state-with-button
        out[++o] = '<div class="number font-montserrat-700 font-size-30 padding-bottom-10">2.</div>';
        out[++o] = '<div class="description font-montserrat-700 font-size-30 transition-background-color">CONSUMER TRACTION<br>& BUSINESS STICKINESS</div>';
        if (active_block == '2' && edition_mode_quali_enabled) {
            out[++o] = '<div class="content-button transition-opacity padding-top-30">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="2" data-step="2_a">Start</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; // End empty-state-with-button
    } else {
        if(edition_mode_quali_enabled) {
            out[++o] = '<div class="canvas-block-border transition-opacity"></div>';
            out[++o] = '<div class="content-button-edit transition-opacity">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="2" data-step="2_a">Edit</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_2_a"';
        out[++o] = '<div class="block-text-canvas btc-step-a '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">2A</div>Early adopters</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_2_a'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_2_b"';
        out[++o] = '<div class="block-text-canvas btc-step-b '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">2B</div>Usage & Occasion</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_2_b'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_2_c"';
        out[++o] = '<div class="block-text-canvas btc-step-c '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">2C</div>Route to Consumers/Channels</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_2_c'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas
    }
    out[++o] = '</div>'; // End canvas-block-2

    class_block = (canvas && !canvas['to_complete']['3']) ? 'to-hover' : '';
    if (active_block == '3') {
        class_block += ' active';
    }
    out[++o] = '<div class="canvas-block canvas-block-3 ' + class_block + '">'; // Start canvas-block-3
    if (!canvas || canvas['to_complete']['3']) {
        out[++o] = '<div class="empty-state-with-button">'; // Start empty-state-with-button
        out[++o] = '<div class="number font-montserrat-700 font-size-30 padding-bottom-10">3.</div>';
        out[++o] = '<div class="description font-montserrat-700 font-size-30 transition-background-color">NEW KEY ACTIVITIES<br>& VALUE CHAIN<br>EXPERTISE</div>';
        if (active_block == '3' && edition_mode_quali_enabled) {
            out[++o] = '<div class="content-button transition-opacity padding-top-30">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="3" data-step="3_a">Start</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; // End empty-state-with-button
    } else {
        if (edition_mode_quali_enabled) {
            out[++o] = '<div class="canvas-block-border transition-opacity"></div>';
            out[++o] = '<div class="content-button-edit transition-opacity">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="3" data-step="3_a">Edit</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_3_a"';
        out[++o] = '<div class="block-text-canvas btc-step-a '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">3A</div>Value Chain Expertise</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_3_a'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_3_b"';
        out[++o] = '<div class="block-text-canvas btc-step-b '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">3B</div>External Partnerships</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_3_b'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_3_c"';
        out[++o] = '<div class="block-text-canvas btc-step-c '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">3C</div>Key activities & metrics</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_3_c'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas
    }
    out[++o] = '</div>'; // End canvas-block-3

    class_block = (canvas && !canvas['to_complete']['4-1']) ? 'to-hover' : '';
    if (active_block == '4-1') {
        class_block += ' active';
    }
    out[++o] = '<div class="canvas-block canvas-block-4-1 ' + class_block + '">'; // Start canvas-block-4-1
    if (!canvas || canvas['to_complete']['4-1']) {
        out[++o] = '<div class="empty-state-with-button">'; // Start empty-state-with-button
        out[++o] = '<div class="number font-montserrat-700 font-size-30 padding-bottom-10">4.</div>';
        out[++o] = '<div class="description font-montserrat-700 font-size-30 transition-background-color">NEW REVENUE<br>STREAMS</div>';
        if (active_block == '4-1' && edition_mode_quali_enabled) {
            out[++o] = '<div class="content-button transition-opacity padding-top-30">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="4-1" data-step="4_a">Start</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; // End empty-state-with-button
    } else {
        if (edition_mode_quali_enabled) {
            out[++o] = '<div class="canvas-block-border transition-opacity"></div>';
            out[++o] = '<div class="content-button-edit transition-opacity">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="4-1" data-step="4_a">Edit</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_4_a"';
        out[++o] = '<div class="block-text-canvas '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">4A</div>Pricing & Revenue streams</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_4_a'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas
    }
    out[++o] = '</div>'; // End canvas-block-4-1

    class_block = (canvas && !canvas['to_complete']['4-2']) ? 'to-hover' : '';
    if (active_block == '4-2') {
        class_block += ' active';
    }
    out[++o] = '<div class="canvas-block canvas-block-4-2 ' + class_block + '">'; // Start canvas-block-4-2
    if (!canvas || canvas['to_complete']['4-2']) {
        out[++o] = '<div class="empty-state-with-button">'; // Start empty-state-with-button
        out[++o] = '<div class="number font-montserrat-700 font-size-30 padding-bottom-10">4.</div>';
        out[++o] = '<div class="description font-montserrat-700 font-size-30 transition-background-color">BURN RATE</div>';
        if (active_block == '4-2' && edition_mode_quali_enabled) {
            out[++o] = '<div class="content-button transition-opacity padding-top-30">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="4-2" data-step="4_b">Start</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }
        out[++o] = '</div>'; // End empty-state-with-button
    } else {
        if (edition_mode_quali_enabled) {
            out[++o] = '<div class="canvas-block-border transition-opacity"></div>';
            out[++o] = '<div class="content-button-edit transition-opacity">';
            out[++o] = '<button class="hub-button height-34 action-open-popup-canvas" data-key="4-2" data-step="4_b">Edit</button>';
            out[++o] = '<div class="loading-spinner centered loading-spinner--26"></div>';
            out[++o] = '</div>';
        }

        tooltip_class = (editable) ? '' : ' helper-on-hover';
        tooltip_title = (editable) ? '' : ' data-target="canvas_block_4_b"';
        out[++o] = '<div class="block-text-canvas '+tooltip_class+'"'+tooltip_title+'>'; // Start block-text-canvas
        out[++o] = '<div class="padding-20">';
        out[++o] = '<div class="libelle-line margin-bottom-10 font-size-16 font-montserrat-700 color-000"><div class="color-000-0-3">4B</div>Burn rate & Cost structure</div>';
        out[++o] = '<div class="description-line font-work-sans-400 font-size-14 line-height-1-43 color-4a4a4a">' + canvas['block_4_b'] + '</div>';
        out[++o] = '</div>';
        out[++o] = '</div>'; // End block-text-canvas
    }
    out[++o] = '</div>'; // End canvas-block-4-2

    return out.join('');
}

/**
 * explore_get_empty_canvas_html_template.
 *
 * @param innovation_id
 * @returns {*}
 */
function explore_get_empty_canvas_html_template(innovation_id) {
    return explore_get_canvas_html_template(null, innovation_id);
}

/**
 * explore_get_new_canvas_button_html_template.
 *
 * @param innovation_id
 * @returns {string}
 */
function explore_get_new_canvas_button_html_template(innovation_id) {
    var id = (innovation_id) ? innovation_id : $('#detail-id-innovation').val();
    var innovation = getInnovationById(id, true);
    if (!innovation) {
        return '';
    }
    var out = [], o = -1;
    var edition_mode_quali_enabled = (check_if_user_is_hq() || (user_can_edit_this_innovation(innovation)) && check_if_data_quali_capture_is_opened());
    if(!edition_mode_quali_enabled){
        return '';
    }
    out[++o] = '<div class="background-color-f2f2f2 padding-vertical-50">';
    out[++o] = '<div class="central-content-994">'; // Start central-content-994
    out[++o] = '<div class="font-montserrat-700 color-000 font-size-30 padding-bottom-20">New canvas</div>';
    out[++o] = '<div class="font-work-sans-300 font-size-19 color-000 line-height-1-58 padding-bottom-20">';
    out[++o] = 'In a Business Model Canvas, each block is interlinked. When you iterate the content of one block, other blocks could be impacted. When you identify an opportunity for a new revenue stream with the same business idea, it could be for a different type of early adopters, or a slightly different usage using a different RTM. For example, if you extend your new activity from “B to C” to “B to B”, you will need to amend different blocks, it becomes then a different business model.';
    out[++o] = '</div>';
    out[++o] = '<button class="hub-button explore-detail-button-action-add-new-canvas">Add new canvas</button>';

    out[++o] = '</div>'; // End central-content-994
    out[++o] = '</div>';
    return out.join('');
}


/**
 * explore_canvas_update_inline_field.
 *
 * @param $field
 */
function explore_canvas_update_inline_field($field) {
    $field.addClass('loading').prop('disabled', true);
    $field.parent().append(get_loading_spinner_html());
    var innovation_id = $('#detail-id-innovation').val();
    var key = $field.attr('name');
    var value = $field.val();
    var canvas_id = $field.closest('.content-container-canvas').find('input[name="canvas_id"]').val();
    var update_datas = [
        {
            name: key,
            value: value
        }
    ];
    var request_data = {
        innovation_id: innovation_id,
        canvas_id: canvas_id,
        update_datas: update_datas
    };
    explore_update_inline_canvas_data(request_data, key, function (response) {
        $field.removeClass('loading').prop('disabled', false);
        $field.parent().find('.loading-spinner').remove();
        var canvas = response['canvas'];
        if (canvas_id == 'new') {
            $field.closest('.content-container-canvas').find('input[name="canvas_id"]').val(canvas['id']);
            $field.closest('.content-container-canvas').removeClass('canvas-new').addClass('canvas-' + canvas['id']);
            $('.explore-detail-button-action-add-new-canvas').prop('disabled', false);
        }
    }, function () {
        $field.removeClass('loading').prop('disabled', false);
        $field.parent().find('.loading-spinner').remove();
    });
}

/**
 * explore_update_inline_canvas_data.
 *
 * @param data
 * @param the_class
 * @param successCallBack
 * @param errorCallBack
 */
function explore_update_inline_canvas_data(data, the_class, successCallBack, errorCallBack) {
    data['token'] = $('#hub-token').val();
    $.ajax({
        url: '/api/innovation/canvas/update',
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.hasOwnProperty('status') && response['status'] == 'error') {
                add_flash_message('error', response['message']);
                if (typeof errorCallBack != "undefined") {
                    errorCallBack(response);
                }
            } else {
                if (typeof successCallBack != "undefined") {
                    successCallBack(response);
                }
                if (response.hasOwnProperty('full_data')) {
                    updateFromCollection(response['full_data']['id'], response['full_data']);
                    explore_detail_update_nb_missing_fields();
                }
                var canvas = response['canvas'];
                var $container_canvas = $('.content-container-canvas.canvas-' + canvas['id']);
                if ($container_canvas.length > 0) {
                    $container_canvas.find('.content-canvas').html(explore_get_content_canvas_html_template(canvas));
                }
                add_saved_message();
            }
        },
        error: function (resultat, statut, e) {
            ajax_on_error(resultat, statut, e);
        }
    });
}

$(document).ready(function () {
    $(document).on('click', '.explore-detail-button-action-add-new-canvas', function (e) {
        e.stopImmediatePropagation();
        if ($(this).is(":disabled") || $('.container-all-canvas .canvas-new').length > 0) {
            return false;
        }
        var innovation_id = $('#detail-id-innovation').val();
        $('.container-all-canvas').append(explore_get_empty_canvas_html_template(innovation_id));
        $('.explore-detail-button-action-add-new-canvas').prop('disabled', true);
        return false;
    });

    $(document).on('focus', '.container-all-canvas .form-element .content-input-text input, .container-all-canvas .form-element .content-textarea textarea', function (e) {
        e.stopImmediatePropagation();
        $(this).attr('data-old-value', $(this).val());
    });

    $(document).on('blur', '.container-all-canvas .form-element .content-input-text input, .container-all-canvas .form-element .content-textarea textarea', function (e) {
        e.stopImmediatePropagation();
        if ($(this).attr('data-old-value') != $(this).val()) {
            explore_canvas_update_inline_field($(this));
        }
    });
});