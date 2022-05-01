/**
 * introjs_get_step_content
 * @param content
 * @param nb
 * @param total
 * @returns {string}
 */
function introjs_get_step_content(content, nb, total) {
    return '<div class="introjs-text">' + content + '</div><div class="introjs-numeric-stepper">' + nb + ' on ' + total + '</div>';
}

/**
 * introjs_get_steps
 * @param nb_steps
 * @returns {Array}
 */
function introjs_get_steps(nb_steps) {
    var intro_steps = [];
    var current_step = 1;
    var case_monitor = ($('#menu-li-monitor').length > 0 && $('#menu-li-monitor').is(':visible'));
    if(!case_monitor){
        nb_steps--;
    }
    intro_steps.push({
        element: '#tab-feedback',
        intro: introjs_get_step_content("Ask for feedback on your projects, and manage your feedbackers", current_step, nb_steps)
    });
    current_step++;
    if (nb_steps == 5) {
        intro_steps.push({
            element: '#button-share-innovation',
            intro: introjs_get_step_content("Share and track interest for your innovation in your activities’ dashboard", current_step, nb_steps)
        });
        current_step++;
    }
    if($('#button-access-manage').hasClass('display-inline-block')){
        intro_steps.push({
            element: '#button-access-manage',
            intro: introjs_get_step_content('Access directly your innovations\' list from anywhere on the Hub.', current_step, nb_steps)
        });
        current_step++;
    }
    intro_steps.push({
        element: '#menu-li-learn',
        intro: introjs_get_step_content("Discover useful content to help you design your innovation (beta version)", current_step, nb_steps)
    });
    if(case_monitor) {
        current_step++;
        intro_steps.push({
            element: '#menu-li-monitor',
            intro: introjs_get_step_content('Monitor your innovations’ performance in a personalized dashboard (your Manage page is now dedicated to your innovation funnel).', current_step, nb_steps)
        });
    }
    return intro_steps;
}

/**
 * init_walkthrough
 */
function init_walkthrough() {
    var nb_steps = 5;
    var example_innovation = manage_initial_innovation_collection.query().filter({
        proper__classification_type__is: 'product',
        current_stage_id__in: [4, 5],
        is_frozen: false
    }).first();
    if (!example_innovation) {
        example_innovation = manage_initial_innovation_collection.query().first();
        nb_steps--;
        if(!$('#button-access-manage').hasClass('display-inline-block')){
            nb_steps--;
        }
    }
    if (!example_innovation) {
        close_popup();
        update_user_on_walkthrough();
        hide_walkthrough_blur();
        return;
    }
    $('html').addClass('introjs-active no-close-popup-on-change-page');
    goToExploreDetailPage(example_innovation.id);
    close_popup();
    var intro = introJs();
    intro.onbeforeexit(function () {
        $('html').removeClass('introjs-active case-button-access-manage case-header-menu-li');
        goToWelcomePage();
        update_user_on_walkthrough();
        setTimeout(function () {
            if (!$('.element-walkthrough-end-popup').hasClass('opened')) {
                hide_walkthrough_blur();
            }
        }, 50);
    }).oncomplete(function () {
        open_popup('.element-walkthrough-end-popup');
    }).onbeforechange(function (targetElement) {
        $('html').removeClass('case-button-access-manage case-header-menu-li');
        if ($(targetElement).hasClass('button-access-manage')) {
            $('html').addClass('case-button-access-manage');
        } else if ($(targetElement).hasClass('menu-li-learn') || $(targetElement).hasClass('menu-li-monitor')) {
            $('html').addClass('case-header-menu-li');
        }
    });

    intro.setOptions({
        showStepNumbers: false,
        showBullets: false,
        disableInteraction: true,
        exitOnEsc: false,
        exitOnOverlayClick: false,
        prevLabel: 'Back',
        nextLabel: 'Next',
        doneLabel: 'Next',
        steps: introjs_get_steps(nb_steps)
    });

    setTimeout(function () {
        intro.start();
    }, 300);
}

/**
 * update_user_on_walkthrough
 */
function update_user_on_walkthrough() {
    $.ajax({
        url: '/api/user/update/walkthrough',
        type: "POST",
        data: {disabled: 1},
        dataType: 'json'
    });
}

/**
 * hide_walkthrough_blur
 */
function hide_walkthrough_blur() {
    $('.walkthrough-blur').addClass('closed');
    setTimeout(function () {
        $('.walkthrough-blur').remove();
    }, 300);
}

$(document).ready(function () {
    $('html').attr('data-popup-target', '.element-walkthrough-start-popup');
    $('.action-start-walkthrough').click(function () {
        init_walkthrough();
    });

    $('.action-end-walkthrough').click(function () {
        update_user_on_walkthrough();
        hide_walkthrough_blur();
        close_popup();
    });

    $('.action-close-walkthrough').click(function () {
        hide_walkthrough_blur();
        close_popup();
    });
});