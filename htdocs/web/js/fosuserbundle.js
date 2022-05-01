$(document).ready(function () {
    $('.login-with-email').click(function () {
        $('.content-login-mode.fosuserbundle-login').removeClass('display-none');
        $('.content-login-mode.prauth-login').addClass('display-none');
        $('.fosuserbundle-content').addClass('with-email');
    });

    $('.login-with-employe').click(function () {
        $('.content-login-mode.fosuserbundle-login').addClass('display-none');
        $('.content-login-mode.prauth-login').removeClass('display-none');
        $('.fosuserbundle-content').removeClass('with-email');
    });
});