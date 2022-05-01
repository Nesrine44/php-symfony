var opened_canvas = null;
var request_load_opened_canvas = null;

/**
 * get_detail_popup_canvas_by_key.
 *
 * @param key
 * @returns {boolean}
 */
function get_detail_popup_canvas_by_key(key) {
    var ret = {};
    switch (key) {
        case '1':
            ret['title'] = 'Innovation sweet spot';
            ret['class'] = 'popup-block-1';
            ret['steps'] = {
                '1_a': {
                    'picture_icon': 'consumer light',
                    'step': '1-A',
                    'nb': '1/3',
                    'title': 'Consumer Benefit',
                    'subtitle': 'The consumer unmet need, new usage, pain point',
                    'questions':
                        [
                            'What about people unmet needs? Any specific frustrations, pain points, desires...?',
                            'Is it expressed putting yourself in the consumer shoes “I wish, need, desire...” ?',
                            'Have you chosen the strongest consumer insight, specific, easy to understand for anybody outside the project?',
                            'Does it unlock a new usage?',
                            'Any functional and emotional benefits?'
                        ],
                    'field_name': 'block_1_a',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Cancel',
                                'action': {
                                    'do': 'close_popup',
                                    'save': false
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Next',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '1',
                                    'step': '1_b'
                                }
                            }
                        ]
                },
                '1_b': {
                    'picture_icon': 'business-priority light',
                    'step': '1-B',
                    'nb': '2/3',
                    'title': 'Source of business',
                    'subtitle': 'Business potential, size of the price',
                    'questions':
                        [
                            'What market opportunity does this innovation tap into? Can you express the size of the opportunity with key figures? What’s the main competitor and why?',
                            'Instead of what people will buy your offer?',
                            'How much could you grab? Build Hypothesis = X% of the market worth X€ Where do you see it working? Its geographical Scope',
                            'Will people see your product or service as a major purchase, a mid-range purchase or an incidental purchase?'
                        ],
                    'field_name': 'block_1_b',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Previous',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '1',
                                    'step': '1_a'
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Next',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '1',
                                    'step': '1_c'
                                }
                            }
                        ]
                },
                '1_c': {
                    'picture_icon': 'value-proposition light',
                    'step': '1-C',
                    'nb': '3/3',
                    'title': 'Unique Value Proposition',
                    'subtitle': 'A clear compelling message that states why it is worth buying',
                    'questions':
                        [
                            'Why consumers would love it? Why is it attractive? Is you offer polarizing enough?',
                            'Why your idea would attract people’s attention?',
                            'What’s so new & different that creates value? Do people perceive points of differentiation?',
                            'What clear and aspirational nuggets does capture consumers\' attention?',
                            'Is that easy and simple to communicate? What story will become the key nugget people will talk about?',
                            'Does your brand name reflect you value proposition easily? Does your name clearly communicates the benefit or personality of your Innovation?'
                        ],
                    'field_name': 'block_1_c',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Previous',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '1',
                                    'step': '1_b'
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Done',
                                'action': {
                                    'do': 'close_popup',
                                    'save': true
                                }
                            }
                        ]
                }
            };
            return ret;
        case '2':
            ret['title'] = 'Consumer traction and business stickiness';
            ret['class'] = 'popup-block-2';
            ret['steps'] = {
                '2_a': {
                    'picture_icon': null,
                    'step': '2-A',
                    'nb': '1/3',
                    'title': 'Early adopters',
                    'subtitle': 'People sharing the same values with the strongest pain point',
                    'questions':
                        [
                            'For whom do you create value?',
                            'Who are the ones who will buy you first, spread faster the word of mouth and facilitate anchoring your initiative long term?',
                            'Can you portrait the people that would love best your idea? Building a persona, a consumer profile described as human as possible: who are they, how old, what do they do, what do they believe in, what are their struggles and desires in life?... Any gender, age, income? Where do they live? How do they live? What type of hobbies?'
                        ],
                    'field_name': 'block_2_a',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Cancel',
                                'action': {
                                    'do': 'close_popup',
                                    'save': false
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Next',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '2',
                                    'step': '2_b'
                                }
                            }
                        ]
                },
                '2_b': {
                    'picture_icon': null,
                    'step': '2-B',
                    'nb': '2/3',
                    'title': 'Usage & Occasion',
                    'subtitle': 'When and how the value proposition works best',
                    'questions':
                        [
                            'When the value proposition works best? What’s the main occasion?',
                            'Where consumers will best adopt your idea? Buy your offer?',
                            'Can you describe precisely the way people interact with the product/ service?',
                            'Have an hypothesis on the best/optimum way for your idea to be enjoyed?',
                            'How to get share of voice, share of visibility during the main occasion?',
                            'Which market/place is the best one for starting the journey?'
                        ],
                    'field_name': 'block_2_b',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Previous',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '2',
                                    'step': '2_a'
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Next',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '2',
                                    'step': '2_c'
                                }
                            }
                        ]
                },
                '2_c': {
                    'picture_icon': null,
                    'step': '2-C',
                    'nb': '3/3',
                    'title': 'Route to Consumers /Customers',
                    'subtitle': 'The most adapted Route to Market',
                    'questions':
                        [
                            'Where will you reach your early adopters?',
                            'What’s the most adapted Route to Market? Will be it in on trade, off trade, e-commerce...?',
                            'How to reinsure repurchase? There is no business impact without repurchase: Why people will come back to the offer?',
                            'Would your offer bring value to customers (B to B)?',
                            'Do you need any specific partnerships with customers?',
                            'Do you need direct or indirect RTM? B to B to C, B to C, B to B?'
                        ],
                    'field_name': 'block_2_c',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Previous',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '2',
                                    'step': '2_b'
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Done',
                                'action': {
                                    'do': 'close_popup',
                                    'save': true
                                }
                            }
                        ]
                }
            };
            return ret;
        case '3':
            ret['title'] = 'New key activities and value chain expertise';
            ret['class'] = 'popup-block-3';
            ret['steps'] = {
                '3_a': {
                    'picture_icon': null,
                    'step': '3-A',
                    'nb': '1/3',
                    'title': 'Value Chain Expertise',
                    'subtitle': 'To make it happen',
                    'questions':
                        [
                            'Do you understand clearly which part of the value chain disrupted? From procurement to production, from exploring new route to markets to engaging with customer/consumers in a different way ...',
                            'Is there new expertise required accordingly?',
                            'If yes, who should be involved internally from an early stage?'
                        ],
                    'field_name': 'block_3_a',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Cancel',
                                'action': {
                                    'do': 'close_popup',
                                    'save': false
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Next',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '3',
                                    'step': '3_b'
                                }
                            }
                        ]
                },
                '3_b': {
                    'picture_icon': null,
                    'step': '3-B',
                    'nb': '2/3',
                    'title': 'External Partnerships',
                    'subtitle': 'To accelerate the development of your activity',
                    'questions':
                        [
                            'Where to grab expertise you are lacking off? Any partnerships required?',
                            'Who could help you to accelerate the project development? Any new technology involved requiring expertise?',
                            'Any collaborations with Start Up or other corporates? Open Innovation?',
                            'Any M&A involved?'
                        ],
                    'field_name': 'block_3_b',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Previous',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '3',
                                    'step': '3_a'
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Next',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '3',
                                    'step': '3_c'
                                }
                            }
                        ]
                },
                '3_c': {
                    'picture_icon': null,
                    'step': '3-C',
                    'nb': '3/3',
                    'title': 'Key activities & Metrics',
                    'subtitle': 'To engage with customers/ consumers at an early stage',
                    'questions':
                        [
                            'What key activities will make a difference?',
                            'What do you need to learn? What type of experiments to put in place? What to be tested, when? How? What resources required to capture evidences from the field?',
                            'What key activities do the Value proposition require to get impact in market? Pull & Push activities?',
                            'Can you describe a minimum viable offer? Describing a product/service good enough for experimentation (not the final one)',
                            'What type of metrics to follow up? Any experimentation plan? What if you have limited resources to introduce your offer in market it?',
                            'What could you do that competitors cannot?'
                        ],
                    'field_name': 'block_3_c',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Previous',
                                'action': {
                                    'do': 'go_to',
                                    'save': true,
                                    'key': '3',
                                    'step': '3_b'
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Done',
                                'action': {
                                    'do': 'close_popup',
                                    'save': true
                                }
                            }
                        ]
                }
            };
            return ret;
        case '4-1':
            ret['title'] = 'Pricing and revenue streams';
            ret['class'] = 'popup-block-4';
            ret['steps'] = {
                '4_a': {
                    'picture_icon': null,
                    'step': '4-A',
                    'nb': '1/1',
                    'title': 'Pricing and revenue streams',
                    'subtitle': 'Offer positioning, Pricing, Revenue Stream, CM Long term',
                    'questions': [],
                    'field_name': 'block_4_a',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Cancel',
                                'action': {
                                    'do': 'close_popup',
                                    'save': false
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Done',
                                'action': {
                                    'do': 'close_popup',
                                    'save': true
                                }
                            }
                        ]
                }
            };
            return ret;
        case '4-2':
            ret['title'] = 'Burn rate and cost structure';
            ret['class'] = 'popup-block-4';
            ret['steps'] = {
                '4_b': {
                    'picture_icon': null,
                    'step': '4-B',
                    'nb': '1/1',
                    'title': 'Burn rate and cost structure',
                    'subtitle': 'Funding till inflexion point Investment model',
                    'questions': [],
                    'field_name': 'block_4_b',
                    'actions':
                        [
                            {
                                'theme': 'grey',
                                'libelle': 'Cancel',
                                'action': {
                                    'do': 'close_popup',
                                    'save': false
                                }
                            },
                            {
                                'theme': '',
                                'libelle': 'Done',
                                'action': {
                                    'do': 'close_popup',
                                    'save': true
                                }
                            }
                        ]
                }
            };
            return ret;
        default:
            return false;
    }
}

/**
 * explore_detail_update_popup_content_template.
 *
 * @param innovation_id
 * @param canvas_id
 * @param key
 * @param step
 * @returns {boolean}
 */
function explore_detail_update_popup_content_template(innovation_id, canvas_id, key, step) {
    var innovation = getInnovationById(innovation_id, true);
    if (!innovation) {
        return false;
    }
    var popup_data = get_detail_popup_canvas_by_key(key);
    if (!popup_data) {
        return false;
    }
    var current_step = (popup_data['steps'].hasOwnProperty(step)) ? popup_data['steps'][step] : null;
    if (!current_step) {
        return false;
    }
    $('.element-canvas-popup').attr('data-innovation_id', innovation_id);
    $('.element-canvas-popup').attr('data-canvas_id', canvas_id);


    var out = [], o = -1;
    out[++o] = '<div class="title font-montserrat-700">' + popup_data['title'] + '</div>';
    if (current_step['picture_icon']) {
        out[++o] = '<div class="popup-canvas-icon icon ' + current_step['picture_icon'] + '"></div>';
    }else{
        out[++o] = '<div class="padding-top-30"></div>';
    }
    out[++o] = '<div class="content ' + popup_data['class'] + '">'; // Start content
    out[++o] = '<div class="canvas-step font-montserrat-700 font-size-20">' + current_step['step'] + '</div>';
    out[++o] = '<div class="font-montserrat-700 font-size-30 color-000">' + current_step['title'] + '</div>';
    out[++o] = '<div class="sub-title font-work-sans-400 font-size-19 line-height-1-58 padding-bottom-10 color-000">' + current_step['subtitle'] + '</div>';
    out[++o] = '</div>'; // End content
    if (current_step['questions'].length > 0) {
        out[++o] = '<ul class="canvas-popup-questions font-work-sans-400 font-size-14">';
        for (var i = 0; i < current_step['questions'].length; i++) {
            out[++o] = '<li>' + current_step['questions'][i] + '</li>';
        }
        out[++o] = '</ul>';
    }
    var current_value = (opened_canvas && opened_canvas[current_step['field_name']]) ? opened_canvas[current_step['field_name']] : '';
    var placeholder = 'In one tweet, what is the ' + current_step['title'].toLowerCase() + '?';
    out[++o] = '<div class="form-element">'; // Start form-element
    out[++o] = '<div class="content-textarea">';
    out[++o] = '<textarea class="text-limited field required" data-old-value="'+current_value+'" name="' + current_step['field_name'] + '" data-limit-text="144" data-has-break-line="0" placeholder="' + placeholder + '">' + current_value + '</textarea>';
    out[++o] = '</div>';
    out[++o] = '</div>'; // End form-element
    var update = (opened_canvas['active_block'] == '0');
    if(update) {
        out[++o] = '<div class="font-work-sans-400 font-size-14 color-e8485c line-height-1 margin-bottom-40"><span class="icon icon-warn"></span> <span class="display-inline-block vertical-align-middle">The 4 parts of the canvas are all interlinked, when you amend one part, the others should be iterated.</span></div>';
    }
    out[++o] = '<div class="content-buttons position-relative">';
    out[++o] = '<div class="step-number font-work-sans-500 font-size-15 color-b5b5b5">' + current_step['nb'] + '</div>';
    out[++o] = '<div class="error-message-canvas info-inner-content-button font-work-sans-400 color-e8485c"></div>';
    out[++o] = '<div class="loader-popup">';
    out[++o] = '<div class="loading-spinner"></div>';
    out[++o] = '</div>';
    for (var j = 0; j < current_step['actions'].length; j++) {
        var action = current_step['actions'][j];
        var data_do = action['action']['do'];
        var data_save = (action['action']['save']) ? '1' : '0';
        var data_key = (action['action']['key']) ? action['action']['key'] : '';
        var data_step = (action['action']['step']) ? action['action']['step'] : '';
        out[++o] = '<button class="hub-button ' + action['theme'] + ' action-canvas-button margin-left-10" data-do="' + data_do + '" data-save="' + data_save + '" data-key="' + data_key + '" data-step="' + data_step + '">' + action['libelle'] + '</button>';
    }
    out[++o] = '</div>';
    $('.element-canvas-popup .content-popup').html(out.join(''));
    $('.element-canvas-popup .content-popup .text-limited').each(function () {
        var limit_text_value = parseInt($(this).attr('data-limit-text'));
        var hasBreakLine = ($(this).attr('data-has-break-line') !== undefined && $(this).attr('data-has-break-line') == "0") ? false : true;
        if (limit_text_value > 0) {
            $(this).parent().append('<div class="content-counter-editable"><span class="the-counter">' + limit_text_value + '</span> characters left.<div class="helper-button" data-target="canvas_text_helper">?</div></div>');
            initLimitText($(this), $(this).parent().find('.the-counter'), limit_text_value, hasBreakLine);
        }
    });
    init_tooltip();

    return true;
}


$(document).ready(function () {
    $(document).on('click', '.action-open-popup-canvas', function (e) {
        e.stopImmediatePropagation();
        $button = $(this);
        opened_canvas = null;
        var innovation_id = $('#detail-id-innovation').val();
        var canvas_id = $button.closest('.content-container-canvas').find('input[name="canvas_id"]').val();
        var key = $button.attr('data-key');
        var step = $button.attr('data-step');
        if(canvas_id == 'new'){
            explore_detail_update_popup_content_template(innovation_id, canvas_id, key, step);
            open_popup('.element-canvas-popup');
        }else{
            opened_canvas = getCanvasById(innovation_id, canvas_id);
            if(opened_canvas) {
                explore_detail_update_popup_content_template(innovation_id, canvas_id, key, step);
                open_popup('.element-canvas-popup');
            } else {
                $button.parent().addClass('loading');
                if ($button.parent().parent().hasClass('canvas-block')) {
                    $button.parent().parent().addClass('loading');
                }
                var the_data = {'innovation_id': innovation_id, 'canvas_id': canvas_id};
                if (request_load_opened_canvas != null) {
                    request_load_opened_canvas.abort();
                }
                request_load_canvas = $.ajax({
                    url: '/api/innovation/canvas/get',
                    type: "POST",
                    data: the_data,
                    dataType: 'json',
                    success: function (response, statut) {
                        $button.parent().removeClass('loading');
                        if ($button.parent().parent().hasClass('canvas-block')) {
                            $button.parent().parent().removeClass('loading');
                        }
                        opened_canvas = response['canvas'];
                        explore_detail_update_popup_content_template(innovation_id, canvas_id, key, step);
                        open_popup('.element-canvas-popup');
                    },
                    error: function (resultat, statut, e) {
                        $button.parent().removeClass('loading');
                        if ($button.parent().parent().hasClass('canvas-block')) {
                            $button.parent().parent().removeClass('loading');
                        }
                        if (!ajax_on_error(resultat, statut, e) && e != 'abort' && e != 0 && resultat.status != 0) {
                            add_flash_message('error', 'Impossible to get innovation canvas for now : ' + resultat.status + ' ' + e);
                        }
                    }
                });
            }
        }
        return false;
    });

    $(document).on('click', '.action-canvas-button', function (e) {
        e.stopImmediatePropagation();
        $('.error-message-canvas').html('');
        var action_do = $(this).attr('data-do');
        var save = $(this).attr('data-save');
        var innovation_id = $('.element-canvas-popup').attr('data-innovation_id');
        var canvas_id = $('.element-canvas-popup').attr('data-canvas_id');
        var key = $(this).attr('data-key');
        var step = $(this).attr('data-step');
        var field_name = $('.element-canvas-popup').find('textarea').attr('name');
        var field_value = $('.element-canvas-popup').find('textarea').val();
        var field_old_value = $('.element-canvas-popup').find('textarea').attr('data-old-value');
        if (save == '1' && field_value != field_old_value) {
            var update_datas = [
                {
                    name: field_name,
                    value: field_value
                }
            ];
            var request_data = {
                innovation_id: innovation_id,
                canvas_id: canvas_id,
                update_datas: update_datas
            };
            enable_load_popup();
            explore_update_inline_canvas_data(
                request_data,
                field_name,
                function (response) {
                    disable_load_popup();
                    if (action_do == 'close_popup') {
                        close_popup();
                    } else if (action_do == 'go_to') {
                        explore_detail_update_popup_content_template(innovation_id, canvas_id, key, step);
                        $(".custom-content-popup.opened .content-popup-for-scroll").animate({scrollTop: 0}, 300);
                    }
                    var canvas = response['canvas'];
                    opened_canvas = canvas;
                    if (canvas_id == 'new') {
                        $('.content-container-canvas.canvas-new').find('input[name="canvas_id"]').val(canvas['id']);
                        $('.content-container-canvas.canvas-new').removeClass('canvas-new').addClass('canvas-' + canvas['id']);
                        $('.explore-detail-button-action-add-new-canvas').prop('disabled', false);
                    }
                }, function (response) {
                    disable_load_popup();
                    $('.error-message-canvas').html(response['message']);
                    // display error message
                });
        } else {
            if (action_do == 'close_popup') {
                close_popup();
            } else if (action_do == 'go_to') {
                explore_detail_update_popup_content_template(innovation_id, canvas_id, key, step);
                $(".custom-content-popup.opened .content-popup-for-scroll").animate({scrollTop: 0}, 300);
            }
        }
        return false;
    });
});