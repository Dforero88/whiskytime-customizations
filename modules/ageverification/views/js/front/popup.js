/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 */

$(document).ready(function () {
    $('body').addClass('kbageVerificationActive');
});

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function kbagesetCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function kbVerifyYearOfBirth() {
    var entered_year = $('#kbageverification_year_of_birth').val();
    var success = 1;
    var show_err_message = 0;
    $('.kb-age-error-message_validtion_message').remove();

    if (entered_year.length < 4) {
        var success = 0;
        show_err_message = 1;
    } else if (entered_year > kbcurrentyear) {
        var success = 0;
//        show_err_message = 1;
    } else if (parseInt(kbcurrentyear) - parseInt(entered_year) >= kbageverification_age_value && parseInt(kbcurrentyear) - parseInt(entered_year) <= 100) {
        var success = 1;
    } else {
        var success = 0;
    }

    if (success == 0) {
        if (kbageverification_under_age_action == 1) {
            if (show_err_message == 1) {
                $('.kb-age-error-message').after($('<div class="kb-age-error-message_validtion_message kb-age-error-message">' + kb_invalid_year + '</div>'));
                $('.kb-age-error-message_validtion_message').addClass('kb-age-error-message-show');
            } else {
                $('.kb-age-error-message').addClass('kb-age-error-message-show');
            }
        } else {
            if (show_err_message == 1) {
                $('.kb-age-error-message').after($('<div class="kb-age-error-message_validtion_message kb-age-error-message">' + kb_invalid_year + '</div>'));
                $('.kb-age-error-message_validtion_message').addClass('kb-age-error-message-show');
            } else {
                location.href = kbageverification_redirect_url;
            }
        }
    } else {
        var value = 1;
        var expiry_date = kbageverification_cookie_value;
        kbagesetCookie("kbage_popup_check", value, expiry_date);
        location.reload();
    }
}

function kbAgeYesButtonClick() {
    var value = 1;
    var expiry_date = kbageverification_cookie_value;
    kbagesetCookie("kbage_popup_check", value, expiry_date);
    location.reload();
}

function kbAgeNoButtonClick() {
    if (kbageverification_under_age_action == 1) {
        $('.kb-age-error-message').addClass('kb-age-error-message-show');
    } else {
        location.href = kbageverification_redirect_url;
    }
}

function kbVerifyDateOfBirth() {
    var kbentered_date = $('#kbageverification_dateofbirth_date').val();
    var kbentered_month = $('#kbageverification_dateofbirth_month').val();
    var entered_year = $('#kbageverification_dateofbirth_year').val();
    entered_date = kbentered_date.replace(/^0+/, '');
    entered_month = kbentered_month.replace(/^0+/, '');
    var success = 1;
    var error = 0;
    var show_err_message = 0;
    $('.kb-age-error-message_validtion_message').remove();

    if (kbentered_date.length < 2 || kbentered_month.length < 2 || entered_year.length < 4) {
        error = 1;
        show_err_message = 1;
    } else if (entered_date > 31 || entered_month > 12 || entered_year > kbcurrentyear) {
        error = 1;
        show_err_message = 1;
    } else if (entered_month == 2) { // checking for leap year
        if ((0 == entered_year % 4) && (0 != entered_year % 100) || (0 == entered_year % 400)) {
            if (entered_date > 29) { // checking if date exceeds 29 if its a leap year
                error = 1;
                show_err_message = 1;
            }
        } else if (entered_date > 28) { // checking if date exceeds 28 if its not a leap year
            error = 1;
            show_err_message = 1;
        }
    } else if (entered_month == 1 || entered_month == 3 || entered_month == 5 || entered_month == 7 || entered_month == 8 || entered_month == 10 || entered_month == 12) { //checking dates for months having 31 days
        if (entered_date > 31) {
            error = 1;
            show_err_message = 1;
        }
    } else {
        if (entered_date > 30) { // checking dates of months having 30 days
            error = 1;
            show_err_message = 1;
        }
    }

    if (error == 1) {
        success = 0;
    } else {
        if (entered_date < 10) {
            entered_date = '0' + entered_date;
        }
        if (entered_month < 10) {
            entered_month = '0' + entered_month;
        }
        var date_input = new Date(entered_year + "-" + entered_month + "-" + entered_date).getTime() / 1000;
        var final_time = current_timest - (kbageverification_age_value * 86400 * 365) - (1 + (kbageverification_age_value / 4)) * 86400;

        if (date_input < final_time) {
            success = 1;
        } else {
            success = 0;
        }
    }

    if (success == 0) {
        if (kbageverification_under_age_action == 1) {
            if (show_err_message == 1) {
                $('.kb-age-error-message').after($('<div class="kb-age-error-message_validtion_message kb-age-error-message">' + kb_invalid_DOB + '</div>'));
                $('.kb-age-error-message_validtion_message').addClass('kb-age-error-message-show');
            } else {
                $('.kb-age-error-message').addClass('kb-age-error-message-show');
            }
//            $('.kb-age-error-message').addClass('kb-age-error-message-show');
        } else {
            if (show_err_message == 1) {
                $('.kb-age-error-message').after($('<div class="kb-age-error-message_validtion_message kb-age-error-message">' + kb_invalid_DOB + '</div>'));
                $('.kb-age-error-message_validtion_message').addClass('kb-age-error-message-show');
            } else {
                location.href = kbageverification_redirect_url;
            }
//            location.href = kbageverification_redirect_url;
        }
    } else {
        var value = 1;
        var expiry_date = kbageverification_cookie_value;
        kbagesetCookie("kbage_popup_check", value, expiry_date);
        location.reload();
    }
}