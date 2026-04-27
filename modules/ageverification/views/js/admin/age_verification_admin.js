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
var h = 0;

$(document).ready(function() {
    $("#link-General_Settings").addClass('active');
    
    $(".velovalidation_age_verification").click( function() {
        if(form_validation() == false){
           return false;
        }
        $('.velovalidation_age_verification').attr("disabled", "disabled");
        $('.ageverification').submit();
    });
    
    $('.kbageverification_popup_preview_btn').bind('click', function () {
        $.ajax({
            url: module_path + '&ajax=true&method=kbagepopuppreview&rand=' + new Date().getTime(),
            data: $('#configuration_form').serialize(),
            success: function (result) {
                if (result == 'Success') {
                    $('#kbageverification_preview_html_form').submit();
                }
            }
        });
//        $('.ageverification').submit();
    });

    $('#configuration_form').addClass('col-lg-10 col-md-9');
    $('label').css('margin-top', '0px');
        
    $("[name^='age_verification_popup_message']").parent().parent().parent().closest('.form-group').hide();
    $("[name^='age_verification_popup_dob_message']").parent().parent().parent().closest('.form-group').hide();
    $("[name^='age_verification_yes_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
    $("[name^='age_verification_no_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
    $("[name^='age_verification_submit_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
    $("[name^='age_verification_popup_additional_info_message']").parent().parent().parent().closest('.form-group').hide();
    $("[name='age_verification[choose_theme]']").closest('.form-group').hide();
    $("[name='age_verification[enable_default_images]']").closest('.form-group').hide();
    
    $('[id^="age_verification[choose_theme]"]').click(function () {
        kbChangeDefaultImages();
    });
    
    $('input[name="age_verification[enable_default_images]"]').on('change', function() {
        kbChangeDefaultImages();
    });
    
//    $('[id^="age_verification[choose_theme]"]').click(function () {
//        if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
//            var logoimage_path = imagedir_path + 'theme' + $(this).val() + '/logo.png';
//            var sideimage_path = imagedir_path + 'theme' + $(this).val() + '/side-img.jpg';
//            var backgroundimage_path = imagedir_path + 'theme' + $(this).val() + '/main-bg.jpg';
//            $('.default-ageverification_logo_image').attr('src', logoimage_path);
//            $('.default-ageverification_window_image').attr('src', sideimage_path);
//            $('.default-ageverification_background_image').attr('src', backgroundimage_path);
//        }
//    });
    
    if ($('[id^="age_verification[verification_method]"]').val() == '3') {
        $("[name='age_verification[dob_format]']").closest('.form-group').show();
    } else {
        $("[name='age_verification[dob_format]']").closest('.form-group').hide();
    }

    $('[id^="age_verification[verification_method]"]').click(function () {
        if ($(this).val() == '3') {
            $("[name='age_verification[dob_format]']").closest('.form-group').show();
        } else {
            $("[name='age_verification[dob_format]']").closest('.form-group').hide();
        }
    });
    
    if ($('[id^="age_verification[under_age_action]"]').val() == '1') {
        $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').show();
        $("[name='age_verification[underage_redirect_url]']").closest('.form-group').hide();
    } else {
        $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').hide();
        $("[name='age_verification[underage_redirect_url]']").closest('.form-group').show();
    }
    
    $('[id^="age_verification[under_age_action]"]').click(function () {
        if ($(this).val() == '1') {
            $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').show();
            $("[name='age_verification[underage_redirect_url]']").closest('.form-group').hide();
        } else {
            $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').hide();
            $("[name='age_verification[underage_redirect_url]']").closest('.form-group').show();
        }
    });
    
    showHideShopTypeSetting();
    $('input[name="age_verification[popup_display_method]"]').on('change', function() {
        showHideShopTypeSetting();
    });
    
    $("#age_verification_logo_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
    $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
    $("#age_verification_background_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
    $("[name='age_verification[text_align]']").closest('.form-group').hide();
    $("[name='age_verification[popup_shape]']").closest('.form-group').hide();
    $("[name='age_verification[popup_background_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_opacity]']").closest('.form-group').hide();
    $("[name='age_verification[popup_text_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_submit_button_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_submit_button_text_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_yes_button_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_yes_button_text_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_no_button_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_no_button_text_color]']").parents('.form-group').hide();
    $("[name='age_verification[popup_message_font_size]']").closest('.form-group').hide();
    $("[name='age_verification[text_font_size]']").closest('.form-group').hide();
    $("[name='age_verification[additional_info_font_size]']").closest('.form-group').hide();
    $("[name='age_verification[custom_css]']").closest('.form-group').hide();
    $("[name='age_verification[custom_js]']").closest('.form-group').hide();
    $("[name='age_verification[custom_css]']").closest('.form-group').hide();
    $("[name='age_verification[custom_js]']").closest('.form-group').hide();
    
    $('input[name="age_verification[product_name]"]').autocomplete(path_fold + 'ajax_products_list.php', {
        delay: 10,
        minChars: 1,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: true,
        scroll: false,
        cacheLength: 0,
        // param multipleSeparator:'||' ajoutÃ© Ã  cause de bug dans lib autocomplete
        multipleSeparator: '||',
        formatItem: function (item) {
            return item[1] + ' - ' + item[0];
        },
        extraParams: {
            excludeIds: function () {
                var selected_pro = $('input[name="age_verification[excluded_products_hidden]"]').val();
                return selected_pro.replace(/\-/g, ',');
            },
            excludeVirtuals: 0,
            exclude_packs: 0
        }
    }).result(function (event, item, formatted) {
        addProductToExclude(item);
        event.stopPropagation();
    });
    
    $("#age_verification_logo_file").on('change', function (e) {
        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        var image_holder = $("#ageverification_logo_image-holder");
        $('.ageverification_image_logo_error').remove();
        var img_vali = velovalidation.checkImage($(this), 4194304, 'mb');
        $('.default-ageverification_logo_image').hide();
        $('.thumb-ageverification_logo_image').remove();
        if (img_vali == true) {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("<img />", {
                        "src": e.target.result,
                        "class": "thumb-ageverification_logo_image",
                        "width": "200px",
                        "id": "new-ageverification_logo_image"
                    }).appendTo(image_holder);
                }
                logoimageexist = 1;
                $('#ageverification_logo_remove-button').show();
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            } else {
                alert(browser_support_text);
            }
//            $('#pvtshop_background_imageerr').remove();
        } else {
            $('#ageverification_logo_remove-button').hide();
            $('.default-ageverification_logo_image').show();
            $('.thumb-ageverification_logo_image').remove();
            document.getElementById("age_verification_logo_file").value = "";
            $('#age_verification_logo_file-name').parent('.dummyfile').after($('<p class="imageerr ageverification_image_logo_error">' + img_vali + '</p>'));
        }
    });
    
    $("#age_verification_window_file").on('change', function (e) {
        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        var image_holder = $("#ageverification_window_image-holder");
        $('.ageverification_image_window_error').remove();
        var img_vali = velovalidation.checkImage($(this), 4194304, 'mb');
        $('.default-ageverification_window_image').hide();
        $('.thumb-ageverification_window_image').remove();
        if (img_vali == true) {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("<img />", {
                        "src": e.target.result,
                        "class": "thumb-ageverification_window_image",
                        "width": "200px",
                        "id": "new-ageverification_window_image"
                    }).appendTo(image_holder);
                }
                verificationwindowimageexist = 1;
                $('#ageverification_window_remove-button').show();
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            } else {
                alert(browser_support_text);
            }
//            $('#pvtshop_background_imageerr').remove();
        } else {
            $('#ageverification_window_remove-button').hide();
            $('.default-ageverification_window_image').show();
            $('.thumb-ageverification_window_image').remove();
            document.getElementById("age_verification_window_file").value = "";
            $('#age_verification_window_file-name').parent('.dummyfile').after($('<p class="imageerr ageverification_image_window_error">' + img_vali + '</p>'));
        }
    });
    
    $("#age_verification_background_file").on('change', function (e) {
        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        var image_holder = $("#ageverification_background_image-holder");
        $('.ageverification_image_background_error').remove();
        var img_vali = velovalidation.checkImage($(this), 4194304, 'mb');
        $('.default-ageverification_background_image').hide();
        $('.thumb-ageverification_background_image').remove();
        if (img_vali == true) {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("<img />", {
                        "src": e.target.result,
                        "class": "thumb-ageverification_background_image",
                        "width": "200px",
                        "id": "new-ageverification_background_image"
                    }).appendTo(image_holder);
                }
                verificationbackgroundimageexist = 1;
                $('#ageverification_background_remove-button').show();
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            } else {
                alert(browser_support_text);
            }
//            $('#pvtshop_background_imageerr').remove();
        } else {
            $('#ageverification_background_remove-button').hide();
            $('.default-ageverification_background_image').show();
            $('.thumb-ageverification_background_image').remove();
            document.getElementById("age_verification_background_file").value = "";
            $('#age_verification_background_file-name').parent('.dummyfile').after($('<p class="imageerr ageverification_image_background_error">' + img_vali + '</p>'));
        }
    });
    
});

function addProductToExclude(data) {
    if (data == null)
        return false;

    var productId = data[1];
    var productName = data[0];
    var $divAccessories = $('#kb_excluded_product_holder');
    var delButtonClass = 'delExcludedProduct';

    var current_excluded_pro = $('input[name="age_verification[excluded_products_hidden]"]').val();
    if (current_excluded_pro != '') {
        var prod_arr_exclude = current_excluded_pro.split(",");
        if ($.inArray(productId, prod_arr_exclude) != '-1') {
            return false;
        }
    }

    $divAccessories.html($divAccessories.html() + '<div class="form-control-static"><button type="button" onclick="deleteSelectedProduct(' + productId + ',this);" class="' + delButtonClass + ' btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + productName + '</div>');

    $('input[name="age_verification[product_name]"]').val('');

    if (current_excluded_pro != '') {
        $('input[name="age_verification[excluded_products_hidden]"]').val(current_excluded_pro + ',' + productId);
    } else {
        $('input[name="age_verification[excluded_products_hidden]"]').val(productId);
    }
}

function deleteSelectedProduct(productId, current) {
    $('input[name="age_verification[excluded_products_hidden]"]').val(removeIdFromCommaString($('input[name="age_verification[excluded_products_hidden]"]').val(), productId, ','));
    $('input[name="age_verification[product_name]"]').val('');
    $(current).parent().remove();
}

function removeIdFromCommaString(list, value, separator) {
    separator = separator || ",";
    var values = list.split(separator);
    for (var i = 0; i < values.length; i++) {
        if (values[i] == value) {
            values.splice(i, 1);
            return values.join(separator);
        }
    }
    return list;
}

function change_tab(a, b)
{
    if (b == 1) {
        $("[id^='fieldset'] h3").html(general_settings);
        $(".panel-heading").html(general_settings);
        $("[name='age_verification[enable]']").closest('.form-group').show();
        $("[name='age_verification[choose_theme]']").closest('.form-group').hide();
        $("[name='age_verification[enable_default_images]']").closest('.form-group').hide();
        $("[name='age_verification[age]']").closest('.form-group').show();
        $("[name='age_verification[verification_method]']").closest('.form-group').show();
        if ($('[id^="age_verification[verification_method]"]').val() == '3') {
            $("[name='age_verification[dob_format]']").closest('.form-group').show();
        } else {
            $("[name='age_verification[dob_format]']").closest('.form-group').hide();
        }
        $("[name='age_verification[remember_visitor]']").closest('.form-group').show();
        $("[name='age_verification[under_age_action]']").closest('.form-group').show();
        if ($('[id^="age_verification[under_age_action]"]').val() == '1') {
            $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').show();
            $("[name='age_verification[underage_redirect_url]']").closest('.form-group').hide();
        } else {
            $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').hide();
            $("[name='age_verification[underage_redirect_url]']").closest('.form-group').show();
        }
        $("[name='age_verification[popup_display_method]']").closest('.form-group').show();
        showHideShopTypeSetting();
        $("[name^='age_verification_popup_message']").parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_popup_dob_message']").parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_yes_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_no_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_submit_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_popup_additional_info_message']").parent().parent().parent().closest('.form-group').hide();
        $("#age_verification_logo_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
        $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
        $("#age_verification_background_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
        $("[name='age_verification[text_align]']").closest('.form-group').hide();
        $("[name='age_verification[popup_shape]']").closest('.form-group').hide();
        $("[name='age_verification[popup_background_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_opacity]']").closest('.form-group').hide();
        $("[name='age_verification[popup_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_submit_button_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_submit_button_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_yes_button_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_yes_button_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_no_button_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_no_button_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_message_font_size]']").closest('.form-group').hide();
        $("[name='age_verification[text_font_size]']").closest('.form-group').hide();
        $("[name='age_verification[additional_info_font_size]']").closest('.form-group').hide();
        $("[name='age_verification[custom_css]']").closest('.form-group').hide();
        $("[name='age_verification[custom_js]']").closest('.form-group').hide();
    } else if (b == 2) {
        $("[id^='fieldset'] h3").html(content_settings);
        $(".panel-heading").html(content_settings);
        $("[name='age_verification[enable]']").closest('.form-group').hide();
        $("[name='age_verification[choose_theme]']").closest('.form-group').show();
        $("[name='age_verification[enable_default_images]']").closest('.form-group').show();
        $("[name='age_verification[age]']").closest('.form-group').hide();
        $("[name='age_verification[verification_method]']").closest('.form-group').hide();
        $("[name='age_verification[dob_format]']").closest('.form-group').hide();
        $("[name='age_verification[remember_visitor]']").closest('.form-group').hide();
        $("[name='age_verification[under_age_action]']").closest('.form-group').hide();
        $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').hide();
        $("[name='age_verification[underage_redirect_url]']").closest('.form-group').hide();
        $("[name='age_verification[popup_display_method]']").closest('.form-group').hide();
        $("#prestashop_category").parents('.form-group').hide();
        $("[name='age_verification[product_name]']").parents('.form-group').hide();
        $("#kb_excluded_product_holder").parents('.form-group').hide();
        $("[name='age_verification[enable_product_page]']").closest('.form-group').hide();
        $("[name='kbageverification_private_pages[]']").parents('.form-group').hide();
        $("[name^='age_verification_popup_message']").parent().parent().parent().closest('.form-group').show();
        $("[name^='age_verification_popup_dob_message']").parent().parent().parent().closest('.form-group').show();
        if ($('[id^="age_verification[verification_method]"]').val() == '1') {
            $("[name^='age_verification_yes_button_text']").parent().parent().parent().parent().closest('.form-group').show();
            $("[name^='age_verification_no_button_text']").parent().parent().parent().parent().closest('.form-group').show();
            $("[name^='age_verification_submit_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
        } else {
            $("[name^='age_verification_yes_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
            $("[name^='age_verification_no_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
            $("[name^='age_verification_submit_button_text']").parent().parent().parent().parent().closest('.form-group').show();
        }
        $("[name^='age_verification_popup_additional_info_message']").parent().parent().parent().closest('.form-group').show();
        $("#age_verification_logo_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
        if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
            if ($('[id^="age_verification[choose_theme]"]').val() == '1' || $('[id^="age_verification[choose_theme]"]').val() == '2' || $('[id^="age_verification[choose_theme]"]').val() == '3') {
                $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
            } else {
                $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
            }
        } else {
            $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
        }
        if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
            var logoimage_path = imagedir_path + 'theme' + $('[id^="age_verification[choose_theme]"]').val() + '/logo.png';
            var sideimage_path = imagedir_path + 'theme' + $('[id^="age_verification[choose_theme]"]').val() + '/side-img.jpg';
            $('.default-ageverification_logo_image').attr('src', logoimage_path);
            $('.default-ageverification_window_image').attr('src', sideimage_path);
            $('input[name="age_verification[logo_file]"]').closest('.form-group').hide();
            $('input[name="age_verification[verification_window_file]"]').closest('.form-group').hide();
            $('#ageverification_logo_remove-button').hide();
            $('#ageverification_window_remove-button').hide();
            $('#new-ageverification_logo_image').hide();
            $('#new-ageverification_window_image').hide();
            $('.default-ageverification_logo_image').show();
            $('.default-ageverification_window_image').show();;
        } else {
            $('input[name="age_verification[logo_file]"]').closest('.form-group').show();
            $('input[name="age_verification[verification_window_file]"]').closest('.form-group').show();
            if (logoimageexist == 1) {
                $('#ageverification_logo_remove-button').show();
            } else {
                $('#ageverification_logo_remove-button').hide();
            }
            if (verificationwindowimageexist == 1) {
                $('#ageverification_window_remove-button').show();
            } else {
                $('#ageverification_window_remove-button').hide();
            }
            if ($('#new-ageverification_logo_image').length) {
                $('#new-ageverification_logo_image').show();
                $('.default-ageverification_logo_image').hide();
            } else {
                $('.default-ageverification_logo_image').attr('src', display_logo_image_path);
            }
            if ($('#new-ageverification_window_image').length) {
                $('#new-ageverification_window_image').show();
                $('.default-ageverification_window_image').hide();
            } else {
                $('.default-ageverification_window_image').attr('src', display_window_image_path);
            }
        }
        $("#age_verification_background_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
        if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
            var backgroundimage_path = imagedir_path + 'theme' + $('[id^="age_verification[choose_theme]"]').val() + '/main-bg.jpg';
            $('.default-ageverification_background_image').attr('src', backgroundimage_path);
            $('#ageverification_background_remove-button').hide();
            $('input[name="age_verification[verification_background_file]"]').closest('.form-group').hide();
            $('#new-ageverification_background_image').hide();
            $('.default-ageverification_background_image').show();
        } else {
            $('input[name="age_verification[verification_background_file]"]').closest('.form-group').show();
            if (verificationbackgroundimageexist == 1) {
                $('#ageverification_background_remove-button').show();
            } else {
                $('#ageverification_background_remove-button').hide();
            }
            if ($('#new-ageverification_background_image').length) {
                $('#new-ageverification_background_image').show();
                $('.default-ageverification_background_image').hide();
            } else {
                $('.default-ageverification_background_image').attr('src', display_background_image_path);
            }
        }
        $("[name='age_verification[text_align]']").closest('.form-group').hide();
        $("[name='age_verification[popup_shape]']").closest('.form-group').hide();
        $("[name='age_verification[popup_background_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_opacity]']").closest('.form-group').hide();
        $("[name='age_verification[popup_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_submit_button_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_submit_button_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_yes_button_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_yes_button_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_no_button_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_no_button_text_color]']").parents('.form-group').hide();
        $("[name='age_verification[popup_message_font_size]']").closest('.form-group').hide();
        $("[name='age_verification[text_font_size]']").closest('.form-group').hide();
        $("[name='age_verification[additional_info_font_size]']").closest('.form-group').hide();
        $("[name='age_verification[custom_css]']").closest('.form-group').hide();
        $("[name='age_verification[custom_js]']").closest('.form-group').hide();
    } else if (b == 3) {
        $("[id^='fieldset'] h3").html(lookfeel_settings);
        $(".panel-heading").html(lookfeel_settings);
        $("[name='age_verification[enable]']").closest('.form-group').hide();
        $("[name='age_verification[choose_theme]']").closest('.form-group').hide();
        $("[name='age_verification[enable_default_images]']").closest('.form-group').hide();
        $("[name='age_verification[age]']").closest('.form-group').hide();
        $("[name='age_verification[verification_method]']").closest('.form-group').hide();
        $("[name='age_verification[dob_format]']").closest('.form-group').hide();
        $("[name='age_verification[remember_visitor]']").closest('.form-group').hide();
        $("[name='age_verification[under_age_action]']").closest('.form-group').hide();
        $("[name^='age_verification_under_age_message']").parent().parent().parent().closest('.form-group').hide();
        $("[name='age_verification[underage_redirect_url]']").closest('.form-group').hide();
        $("[name='age_verification[popup_display_method]']").closest('.form-group').hide();
        $("#prestashop_category").parents('.form-group').hide();
        $("[name='age_verification[product_name]']").parents('.form-group').hide();
        $("#kb_excluded_product_holder").parents('.form-group').hide();
        $("[name='age_verification[enable_product_page]']").closest('.form-group').hide();
        $("[name='kbageverification_private_pages[]']").parents('.form-group').hide();
        $("[name^='age_verification_popup_message']").parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_popup_dob_message']").parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_yes_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_no_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_submit_button_text']").parent().parent().parent().parent().closest('.form-group').hide();
        $("[name^='age_verification_popup_additional_info_message']").parent().parent().parent().closest('.form-group').hide();
        $("#age_verification_logo_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
        $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
        $("#age_verification_background_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
        $("[name='age_verification[text_align]']").closest('.form-group').show();
        $("[name='age_verification[popup_shape]']").closest('.form-group').show();
        $("[name='age_verification[popup_background_color]']").parents('.form-group').show();
        $("[name='age_verification[popup_opacity]']").closest('.form-group').show();
        $("[name='age_verification[popup_text_color]']").parents('.form-group').show();
        if ($('[id^="age_verification[verification_method]"]').val() == '1') {
            $("[name='age_verification[popup_yes_button_color]']").parents('.form-group').show();
            $("[name='age_verification[popup_yes_button_text_color]']").parents('.form-group').show();
            $("[name='age_verification[popup_no_button_color]']").parents('.form-group').show();
            $("[name='age_verification[popup_no_button_text_color]']").parents('.form-group').show();
            $("[name='age_verification[popup_submit_button_color]']").parents('.form-group').hide();
            $("[name='age_verification[popup_submit_button_text_color]']").parents('.form-group').hide();
        } else {
            $("[name='age_verification[popup_yes_button_color]']").parents('.form-group').hide();
            $("[name='age_verification[popup_yes_button_text_color]']").parents('.form-group').hide();
            $("[name='age_verification[popup_no_button_color]']").parents('.form-group').hide();
            $("[name='age_verification[popup_no_button_text_color]']").parents('.form-group').hide();
            $("[name='age_verification[popup_submit_button_color]']").parents('.form-group').show();
            $("[name='age_verification[popup_submit_button_text_color]']").parents('.form-group').show();
        }
        $("[name='age_verification[popup_message_font_size]']").closest('.form-group').show();
        $("[name='age_verification[text_font_size]']").closest('.form-group').show();
        $("[name='age_verification[additional_info_font_size]']").closest('.form-group').show();
        $("[name='age_verification[custom_css]']").closest('.form-group').show();
        $("[name='age_verification[custom_js]']").closest('.form-group').show();
    }
    $('.list-group-item').attr('class', 'list-group-item');
    $(a).attr('class', 'list-group-item active');
}


function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            
            if(document.getElementById('theImg') === null)
            {   
                $('.col-sm-6').prepend('<div id="image_div" style ="margin-bottom: 10px;"><img id="theImg" src="#" style="height: 30%; width: 60% ; padding:20px; margin:0px auto; border: 1px solid #C7D6DB"/></div>')
                $('#theImg').attr('src', e.target.result);
            }else{
                $('#theImg').attr('src', e.target.result);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function form_validation(){
    $('.vel_error_msg').remove();
    $('.imageerr').remove();
    $('.error_field').removeClass('error_field');
    $('.velsof_error_label').hide();
        
    var general_setting_tab = 0;
    var content_tab = 0;
    var look_and_feel_tab = 0;
        
    var error = false;
    var errorMessage = '';
    
    var age_value_tag = velovalidation.checkMandatoryOnly($("input[name='age_verification[age]']"));
    var age_value_numeric = velovalidation.isNumeric($("input[name='age_verification[age]']"), false);
    if (age_value_tag != true) {
        error = true;
        $("input[name='age_verification[age]']").addClass('error_field');
        $("input[name='age_verification[age]']").after($('<p class="age_value_tag vel_error_msg"></p>'));
        $('.age_value_tag').html(age_value_tag);
        general_setting_tab = 1;
    } else if (age_value_numeric != true) {
        error = true;
        $("input[name='age_verification[age]']").addClass('error_field');
        $("input[name='age_verification[age]']").after($('<p class="age_value_numeric vel_error_msg"></p>'));
        $('.age_value_numeric').html(age_value_numeric);
        general_setting_tab = 1;
    } else if ($("input[name='age_verification[age]']").val().trim() > 100 || $("input[name='age_verification[age]']").val().trim() < 1) {
        error = true;
        $("input[name='age_verification[age]']").addClass('error_field');
        $("input[name='age_verification[age]']").after($('<p class="age_value_tag vel_error_msg"></p>'));
        $('.age_value_tag').html(value_between_msg);
        general_setting_tab = 1;
    }
    
    var remember_visitor_value_tag = velovalidation.checkMandatoryOnly($("input[name='age_verification[remember_visitor]']"));
    var remember_visitor_value_numeric = velovalidation.isNumeric($("input[name='age_verification[remember_visitor]']"), false);
    if (remember_visitor_value_tag != true) {
        error = true;
        $("input[name='age_verification[remember_visitor]']").addClass('error_field');
        $("input[name='age_verification[remember_visitor]']").after($('<p class="remember_visitor_value_tag vel_error_msg"></p>'));
        $('.remember_visitor_value_tag').html(remember_visitor_value_tag);
        general_setting_tab = 1;
    } else if (remember_visitor_value_numeric != true) {
        error = true;
        $("input[name='age_verification[remember_visitor]']").addClass('error_field');
        $("input[name='age_verification[remember_visitor]']").after($('<p class="remember_visitor_value_numeric vel_error_msg"></p>'));
        $('.remember_visitor_value_numeric').html(remember_visitor_value_numeric);
        general_setting_tab = 1;
    } else if ($("input[name='age_verification[remember_visitor]']").val().trim() <= 0) {
        error = true;
        $("input[name='age_verification[remember_visitor]']").addClass('error_field');
        $("input[name='age_verification[remember_visitor]']").after($('<p class="remember_visitor_value_tag vel_error_msg"></p>'));
        $('.remember_visitor_value_tag').html(greater_than_zero);
        general_setting_tab = 1;
    }
    
    if ($('[id^="age_verification[under_age_action]"]').val() == '1') {
//        var age_verification_under_age_message_element = $('textarea[name^="age_verification_under_age_message"]');
//        var first_err_flag_top = 0;
//        age_verification_under_age_message_element.each(function () {
//            var current_error = tinyMCE.get($(this).attr("id")).getContent().trim();
//            if (current_error == '') {
//                if (first_err_flag_top == 0) {
//                    age_verification_under_age_message_element.addClass('error_field');
//                    if (first_err_flag_top == 0) {
//                        $('<p class="vel_error_msg ">' + check_for_all_lang + '</p>').insertAfter(age_verification_under_age_message_element);
//                    }
//                }
//                first_err_flag_top = 1;
//                general_setting_tab = 1;
//                error = true;
//            }
//        });
        
        var age_verification_under_age_message_element = $("textarea[name^=age_verification_under_age_message]");
        var multi_language_yes_button_err = false;
        age_verification_under_age_message_element.each(function () {
            var current_error = velovalidation.checkMandatory($(this));
            if (current_error != true) {
                error = true;
                multi_language_yes_button_err = true;
                general_setting_tab = 1;
            }
        });

        if (multi_language_yes_button_err) {
            age_verification_under_age_message_element.addClass('error_field');
            age_verification_under_age_message_element.after('<span class="vel_error_msg">' + check_for_all_lang + '</span>');
        }
    } else {
        var redirect_url_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[underage_redirect_url]']"));
        var redirect_url_check = velovalidation.checkUrl($("input[name='age_verification[underage_redirect_url]']"));
        if (redirect_url_mand != true) {
            error = true;
            $("input[name='age_verification[underage_redirect_url]']").addClass('error_field');
            $("input[name='age_verification[underage_redirect_url]']").after($('<p class="redirect_url_mand vel_error_msg"></p>'));
            $('.redirect_url_mand').html(redirect_url_mand);
            general_setting_tab = 1;
        } else if (redirect_url_check != true) {
            error = true;
            $("input[name='age_verification[underage_redirect_url]']").addClass('error_field');
            $("input[name='age_verification[underage_redirect_url]']").after($('<p class="redirect_url_check vel_error_msg"></p>'));
            $('.redirect_url_check').html(redirect_url_check);
            general_setting_tab = 1;
        }
    }
    
    if ($('input[name="age_verification[popup_display_method]"]:checked').val() == 'selected') {
        var any_private_cms = $('select[name="kbageverification_private_pages[]"]').val();
        var any_priavte_product = $('input[name="age_verification[excluded_products_hidden]"]').val();

        if ((any_priavte_product == '') && ((any_private_cms == '') || (any_private_cms == null))) {
            var presta_cat = '';
            $('#prestashop_category').find(":input[type=checkbox]").each(function () {
                if ($(this).prop("checked") == true) {
                    presta_cat = '1';
                }
            });
            if (presta_cat == '') {
                error = true;
                general_setting_tab = 1;
                $("input[name='age_verification[popup_display_method]']").closest('.col-lg-9').append($('<span class="vel_error_msg">' + selected_pages_selection_error + ' </span>'));
            }
        }
    }
    
//    var age_verification_popup_message_element = $("textarea[name^=age_verification_popup_message]");
//    var multi_language_yes_button_err = false;
//    age_verification_popup_message_element.each(function () {
//        var current_error = velovalidation.checkMandatory($(this));
//        if (current_error != true) {
//            error = true;
//            multi_language_yes_button_err = true;
//            content_tab = 1;
//        }
//    });
//
//    if (multi_language_yes_button_err) {
//        age_verification_popup_message_element.addClass('error_field');
//        age_verification_popup_message_element.after('<span class="vel_error_msg">' + check_for_all_lang + '</span>');
//    }
//
//    var age_verification_popup_dob_message_element = $("textarea[name^=age_verification_popup_dob_message]");
//    var multi_language_yes_button_err = false;
//    age_verification_popup_dob_message_element.each(function () {
//        var current_error = velovalidation.checkMandatory($(this));
//        if (current_error != true) {
//            error = true;
//            multi_language_yes_button_err = true;
//            content_tab = 1;
//        }
//    });

    if (multi_language_yes_button_err) {
        age_verification_popup_dob_message_element.addClass('error_field');
        age_verification_popup_dob_message_element.after('<span class="vel_error_msg">' + check_for_all_lang + '</span>');
    }
    
    if ($('[id^="age_verification[verification_method]"]').val() == '1') {
        var age_verification_yes_button_text_element = $("input[name^=age_verification_yes_button_text]");
        var multi_language_yes_button_err = false;
        age_verification_yes_button_text_element.each(function () {
            var current_error = velovalidation.checkMandatory($(this));
            if (current_error != true) {
                error = true;
                multi_language_yes_button_err = true;
                content_tab = 1;
            }
        });
        
        if (multi_language_yes_button_err) {
            age_verification_yes_button_text_element.addClass('error_field');
            age_verification_yes_button_text_element.after('<span class="vel_error_msg">' + check_for_all_lang + '</span>');
        }
        
        var age_verification_no_button_text_element = $("input[name^=age_verification_no_button_text]");
        var multi_language_no_button_err = false;
        age_verification_no_button_text_element.each(function () {
            var current_error = velovalidation.checkMandatory($(this));
            if (current_error != true) {
                error = true;
                multi_language_no_button_err = true;
                content_tab = 1;
            }
        });
        
        if (multi_language_no_button_err) {
            age_verification_no_button_text_element.addClass('error_field');
            age_verification_no_button_text_element.after('<span class="vel_error_msg">' + check_for_all_lang + '</span>');
        }
    } else {
        var age_verification_submit_button_text_element = $("input[name^=age_verification_submit_button_text]");
        var multi_language_submit_button_err = false;
        age_verification_submit_button_text_element.each(function () {
            var current_error = velovalidation.checkMandatory($(this));
            if (current_error != true) {
                error = true;
                multi_language_submit_button_err = true;
                content_tab = 1;
            }
        });
        
        if (multi_language_submit_button_err) {
            age_verification_submit_button_text_element.addClass('error_field');
            age_verification_submit_button_text_element.after('<span class="vel_error_msg">' + check_for_all_lang + '</span>');
        }
    }
    
    if ($("#age_verification_logo_file").val() !== '') {
        var img_vali = velovalidation.checkImage($("#age_verification_logo_file"), 4194304, 'mb');
        if (img_vali !== true) {
            error = true;
            $('#age_verification_logo_file-name').parent('.dummyfile').after($('<p class="imageerr ageverification_image_logo_error">' + img_vali + '</p>'));
            content_tab = 1;
        }
    }

    if ($("#age_verification_window_file").val() !== '') {
        var img_vali = velovalidation.checkImage($("#age_verification_window_file"), 4194304, 'mb');
        if (img_vali !== true) {
            error = true;
            $('#age_verification_window_file-name').parent('.dummyfile').after($('<p class="imageerr ageverification_image_window_error">' + img_vali + '</p>'));
            content_tab = 1;
        }
    }

    if ($("#age_verification_background_file").val() !== '') {
        var img_vali = velovalidation.checkImage($("#age_verification_background_file"), 4194304, 'mb');
        if (img_vali !== true) {
            error = true;
            $('#age_verification_background_file-name').parent('.dummyfile').after($('<p class="imageerr ageverification_image_background_error">' + img_vali + '</p>'));
            look_and_feel_tab = 1;
        }
    }
    
    var popup_background_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_background_color]']"));
    var popup_background_color_check = velovalidation.isColor($("input[name='age_verification[popup_background_color]']"));
    if (popup_background_color_mand != true) {
        error = true;
        $("input[name='age_verification[popup_background_color]']").addClass('error_field');
        $("input[name='age_verification[popup_background_color]']").closest('.form-group').after($('<p class="popup_background_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
        $('.popup_background_color_mand').html(popup_background_color_mand);
        look_and_feel_tab = 1;
    } else if (popup_background_color_check != true) {
        error = true;
        $("input[name='age_verification[popup_background_color]']").addClass('error_field');
        $("input[name='age_verification[popup_background_color]']").closest('.form-group').after($('<p class="popup_background_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
        $('.popup_background_color_check').html(popup_background_color_check);
        look_and_feel_tab = 1;
    }
    
    var popup_opacity_value_tag = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_opacity]']"));
    var popup_opacity_value_numeric = velovalidation.checkAmount($("input[name='age_verification[popup_opacity]']"), false);
    if (popup_opacity_value_tag != true) {
        error = true;
        $("input[name='age_verification[popup_opacity]']").addClass('error_field');
        $("input[name='age_verification[popup_opacity]']").after($('<p class="popup_opacity_value_tag vel_error_msg"></p>'));
        $('.popup_opacity_value_tag').html(popup_opacity_value_tag);
        look_and_feel_tab = 1;
    } else if (popup_opacity_value_numeric != true) {
        error = true;
        $("input[name='age_verification[popup_opacity]']").addClass('error_field');
        $("input[name='age_verification[popup_opacity]']").after($('<p class="popup_opacity_value_numeric vel_error_msg"></p>'));
        $('.popup_opacity_value_numeric').html(popup_opacity_value_numeric);
        look_and_feel_tab = 1;
    }
    
    var popup_text_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_text_color]']"));
    var popup_text_color_check = velovalidation.isColor($("input[name='age_verification[popup_text_color]']"));
    if (popup_text_color_mand != true) {
        error = true;
        $("input[name='age_verification[popup_text_color]']").addClass('error_field');
        $("input[name='age_verification[popup_text_color]']").closest('.form-group').after($('<p class="popup_text_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
        $('.popup_text_color_mand').html(popup_text_color_mand);
        look_and_feel_tab = 1;
    } else if (popup_text_color_check != true) {
        error = true;
        $("input[name='age_verification[popup_text_color]']").addClass('error_field');
        $("input[name='age_verification[popup_text_color]']").closest('.form-group').after($('<p class="popup_text_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
        $('.popup_text_color_check').html(popup_text_color_check);
        look_and_feel_tab = 1;
    }
    
    if ($('[id^="age_verification[verification_method]"]').val() == '1') {
        var popup_yes_button_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_yes_button_color]']"));
        var popup_yes_button_color_check = velovalidation.isColor($("input[name='age_verification[popup_yes_button_color]']"));
        if (popup_yes_button_color_mand != true) {
            error = true;
            $("input[name='age_verification[popup_yes_button_color]']").addClass('error_field');
            $("input[name='age_verification[popup_yes_button_color]']").closest('.form-group').after($('<p class="popup_yes_button_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_yes_button_color_mand').html(popup_yes_button_color_mand);
            look_and_feel_tab = 1;
        } else if (popup_yes_button_color_check != true) {
            error = true;
            $("input[name='age_verification[popup_yes_button_color]']").addClass('error_field');
            $("input[name='age_verification[popup_yes_button_color]']").closest('.form-group').after($('<p class="popup_yes_button_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_yes_button_color_check').html(popup_yes_button_color_check);
            look_and_feel_tab = 1;
        }

        var popup_yes_button_text_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_yes_button_text_color]']"));
        var popup_yes_button_text_color_check = velovalidation.isColor($("input[name='age_verification[popup_yes_button_text_color]']"));
        if (popup_yes_button_text_color_mand != true) {
            error = true;
            $("input[name='age_verification[popup_yes_button_text_color]']").addClass('error_field');
            $("input[name='age_verification[popup_yes_button_text_color]']").closest('.form-group').after($('<p class="popup_yes_button_text_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_yes_button_text_color_mand').html(popup_yes_button_text_color_mand);
            look_and_feel_tab = 1;
        } else if (popup_yes_button_text_color_check != true) {
            error = true;
            $("input[name='age_verification[popup_yes_button_text_color]']").addClass('error_field');
            $("input[name='age_verification[popup_yes_button_text_color]']").closest('.form-group').after($('<p class="popup_yes_button_text_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_yes_button_text_color_check').html(popup_yes_button_text_color_check);
            look_and_feel_tab = 1;
        }

        var popup_no_button_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_no_button_color]']"));
        var popup_no_button_color_check = velovalidation.isColor($("input[name='age_verification[popup_no_button_color]']"));
        if (popup_no_button_color_mand != true) {
            error = true;
            $("input[name='age_verification[popup_no_button_color]']").addClass('error_field');
            $("input[name='age_verification[popup_no_button_color]']").closest('.form-group').after($('<p class="popup_no_button_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_no_button_color_mand').html(popup_no_button_color_mand);
            look_and_feel_tab = 1;
        } else if (popup_no_button_color_check != true) {
            error = true;
            $("input[name='age_verification[popup_no_button_color]']").addClass('error_field');
            $("input[name='age_verification[popup_no_button_color]']").closest('.form-group').after($('<p class="popup_no_button_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_no_button_color_check').html(popup_no_button_color_check);
            look_and_feel_tab = 1;
        }

        var popup_no_button_text_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_no_button_text_color]']"));
        var popup_no_button_text_color_check = velovalidation.isColor($("input[name='age_verification[popup_no_button_text_color]']"));
        if (popup_no_button_text_color_mand != true) {
            error = true;
            $("input[name='age_verification[popup_no_button_text_color]']").addClass('error_field');
            $("input[name='age_verification[popup_no_button_text_color]']").closest('.form-group').after($('<p class="popup_no_button_text_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_no_button_text_color_mand').html(popup_no_button_text_color_mand);
            look_and_feel_tab = 1;
        } else if (popup_no_button_text_color_check != true) {
            error = true;
            $("input[name='age_verification[popup_no_button_text_color]']").addClass('error_field');
            $("input[name='age_verification[popup_no_button_text_color]']").closest('.form-group').after($('<p class="popup_no_button_text_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_no_button_text_color_check').html(popup_no_button_text_color_check);
            look_and_feel_tab = 1;
        }
    } else {
        var popup_submit_button_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_submit_button_color]']"));
        var popup_submit_button_color_check = velovalidation.isColor($("input[name='age_verification[popup_submit_button_color]']"));
        if (popup_submit_button_color_mand != true) {
            error = true;
            $("input[name='age_verification[popup_submit_button_color]']").addClass('error_field');
            $("input[name='age_verification[popup_submit_button_color]']").closest('.form-group').after($('<p class="popup_submit_button_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_submit_button_color_mand').html(popup_submit_button_color_mand);
            look_and_feel_tab = 1;
        } else if (popup_submit_button_color_check != true) {
            error = true;
            $("input[name='age_verification[popup_submit_button_color]']").addClass('error_field');
            $("input[name='age_verification[popup_submit_button_color]']").closest('.form-group').after($('<p class="popup_submit_button_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_submit_button_color_check').html(popup_submit_button_color_check);
            look_and_feel_tab = 1;
        }

        var popup_submit_button_text_color_mand = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_submit_button_text_color]']"));
        var popup_submit_button_text_color_check = velovalidation.isColor($("input[name='age_verification[popup_submit_button_text_color]']"));
        if (popup_submit_button_text_color_mand != true) {
            error = true;
            $("input[name='age_verification[popup_submit_button_text_color]']").addClass('error_field');
            $("input[name='age_verification[popup_submit_button_text_color]']").closest('.form-group').after($('<p class="popup_submit_button_text_color_mand vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_submit_button_text_color_mand').html(popup_submit_button_text_color_mand);
            look_and_feel_tab = 1;
        } else if (popup_submit_button_text_color_check != true) {
            error = true;
            $("input[name='age_verification[popup_submit_button_text_color]']").addClass('error_field');
            $("input[name='age_verification[popup_submit_button_text_color]']").closest('.form-group').after($('<p class="popup_submit_button_text_color_check vel_error_msg" style="margin-top: -10px;"></p>'));
            $('.popup_submit_button_text_color_check').html(popup_submit_button_text_color_check);
            look_and_feel_tab = 1;
        }
    }
        
    var popup_message_font_size_value_tag = velovalidation.checkMandatoryOnly($("input[name='age_verification[popup_message_font_size]']"));
    var popup_message_font_size_value_numeric = velovalidation.isNumeric($("input[name='age_verification[popup_message_font_size]']"), false);
    if (popup_message_font_size_value_tag != true) {
        error = true;
        $("input[name='age_verification[popup_message_font_size]']").addClass('error_field');
        $("input[name='age_verification[popup_message_font_size]']").parent().after($('<p class="popup_message_font_size_value_tag vel_error_msg"></p>'));
        $('.popup_message_font_size_value_tag').html(popup_message_font_size_value_tag);
        look_and_feel_tab = 1;
    } else if (popup_message_font_size_value_numeric != true) {
        error = true;
        $("input[name='age_verification[popup_message_font_size]']").addClass('error_field');
        $("input[name='age_verification[popup_message_font_size]']").parent().after($('<p class="popup_message_font_size_value_numeric vel_error_msg"></p>'));
        $('.popup_message_font_size_value_numeric').html(popup_message_font_size_value_numeric);
        look_and_feel_tab = 1;
    } else if ($("input[name='age_verification[popup_message_font_size]']").val().trim() > 40 || $("input[name='age_verification[popup_message_font_size]']").val().trim() < 10) {
        error = true;
        $("input[name='age_verification[popup_message_font_size]']").addClass('error_field');
        $("input[name='age_verification[popup_message_font_size]']").parent().after($('<p class="popup_message_font_size_value_tag vel_error_msg"></p>'));
        $('.popup_message_font_size_value_tag').html(popup_message_font_size_value_between_msg);
        look_and_feel_tab = 1;
    }
    
    var text_font_size_value_tag = velovalidation.checkMandatoryOnly($("input[name='age_verification[text_font_size]']"));
    var text_font_size_value_numeric = velovalidation.isNumeric($("input[name='age_verification[text_font_size]']"), false);
    if (text_font_size_value_tag != true) {
        error = true;
        $("input[name='age_verification[text_font_size]']").addClass('error_field');
        $("input[name='age_verification[text_font_size]']").parent().after($('<p class="text_font_size_value_tag vel_error_msg"></p>'));
        $('.text_font_size_value_tag').html(text_font_size_value_tag);
        look_and_feel_tab = 1;
    } else if (text_font_size_value_numeric != true) {
        error = true;
        $("input[name='age_verification[text_font_size]']").addClass('error_field');
        $("input[name='age_verification[text_font_size]']").parent().after($('<p class="text_font_size_value_numeric vel_error_msg"></p>'));
        $('.text_font_size_value_numeric').html(text_font_size_value_numeric);
        look_and_feel_tab = 1;
    } else if ($("input[name='age_verification[text_font_size]']").val().trim() > 20 || $("input[name='age_verification[text_font_size]']").val().trim() < 10) {
        error = true;
        $("input[name='age_verification[text_font_size]']").addClass('error_field');
        $("input[name='age_verification[text_font_size]']").parent().after($('<p class="text_font_size_value_tag vel_error_msg"></p>'));
        $('.text_font_size_value_tag').html(text_font_size_value_between_msg);
        look_and_feel_tab = 1;
    }
    
    var additional_info_font_size_value_tag = velovalidation.checkMandatoryOnly($("input[name='age_verification[additional_info_font_size]']"));
    var additional_info_font_size_value_numeric = velovalidation.isNumeric($("input[name='age_verification[additional_info_font_size]']"), false);
    if (additional_info_font_size_value_tag != true) {
        error = true;
        $("input[name='age_verification[additional_info_font_size]']").addClass('error_field');
        $("input[name='age_verification[additional_info_font_size]']").parent().after($('<p class="additional_info_font_size_value_tag vel_error_msg"></p>'));
        $('.additional_info_font_size_value_tag').html(additional_info_font_size_value_tag);
        look_and_feel_tab = 1;
    } else if (additional_info_font_size_value_numeric != true) {
        error = true;
        $("input[name='age_verification[additional_info_font_size]']").addClass('error_field');
        $("input[name='age_verification[additional_info_font_size]']").parent().after($('<p class="additional_info_font_size_value_numeric vel_error_msg"></p>'));
        $('.additional_info_font_size_value_numeric').html(additional_info_font_size_value_numeric);
        look_and_feel_tab = 1;
    } else if ($("input[name='age_verification[additional_info_font_size]']").val().trim() > 20 || $("input[name='age_verification[additional_info_font_size]']").val().trim() < 10) {
        error = true;
        $("input[name='age_verification[additional_info_font_size]']").addClass('error_field');
        $("input[name='age_verification[additional_info_font_size]']").parent().after($('<p class="additional_info_font_size_value_tag vel_error_msg"></p>'));
        $('.additional_info_font_size_value_tag').html(additional_info_font_size_value_between_msg);
        look_and_feel_tab = 1;
    }

    var custom_css_tag = velovalidation.checkTags($("textarea[name='age_verification[custom_css]']"));
    if (custom_css_tag != true){
        error = true;
        $("textarea[name='age_verification[custom_css]']").addClass('error_field');
        $("textarea[name='age_verification[custom_css]']").after($('<p class="custom_css_tag vel_error_msg"></p>'));
        $('.custom_css_tag').html(custom_css_tag);
        look_and_feel_tab = 1;
    } else if($("textarea[name='age_verification[custom_css]']").val().trim().length > 10000) {
        error = true;
        $("textarea[name='age_verification[custom_css]']").addClass('error_field');
        $("textarea[name='age_verification[custom_css]']").after($('<p class="custom_css_length vel_error_msg"></p>'));
        $('.custom_css_length').html(custom_css_length);
        look_and_feel_tab = 1;
    }
    
    
    if (error == true) {
        if (general_setting_tab == 1) {
            $('#link-General_Settings').children('.velsof_error_label').show();
            $('#link-General_Settings').children().children('#velsof_error_icon').css('display', 'inline');
        }
        if (content_tab == true) {
            $('#link-Content').children('.velsof_error_label').show();
            $('#link-Content').children().children('#velsof_error_icon').css('display', 'inline');
        }
        if (look_and_feel_tab == 1) {
            $('#link-Look_and_Feel_Settings').children('.velsof_error_label').show();
            $('#link-Look_and_Feel_Settings').children().children('#velsof_error_icon').css('display', 'inline');
        }
        $("html, body").animate({scrollTop: 0}, "fast");
        return false;
    }
}

function showHideShopTypeSetting() {
    $("#prestashop_category").parents('.form-group').hide();
    $("[name='age_verification[product_name]']").parents('.form-group').hide();
    $("#kb_excluded_product_holder").parents('.form-group').hide();
    $("[name='age_verification[enable_product_page]']").closest('.form-group').hide();
    $("[name='kbageverification_private_pages[]']").parents('.form-group').hide();
    if ($('input[name="age_verification[popup_display_method]"]:checked').val() == 'selected') {
        $("#prestashop_category").parents('.form-group').show();
        $("[name='age_verification[product_name]']").parents('.form-group').show();
        $("#kb_excluded_product_holder").parents('.form-group').show();
        $("[name='age_verification[enable_product_page]']").closest('.form-group').show();
        $("[name='kbageverification_private_pages[]']").parents('.form-group').show();
    }
}

function removeLogoImage() {
    if (confirm(delete_image_text)) {
        if ($('.thumb-ageverification_logo_image').attr('src') == undefined) {
            $.ajax({
                url: module_path,
                data: 'ajax=true&method=deleteLogoImage',
                success: function(result) {
                    logoimageexist = 0;
                    if (result == 'No Image Found') {
                        alert(no_image_text);
                    } else {
                        alert(image_deleted_text);
                        $('#ageverification_logo_remove-button').hide();
                        $('.default-ageverification_logo_image').attr('src', default_image_path);
                    }
                }
            });
        } else {
            $('.thumb-ageverification_logo_image').remove();
            $('.default-ageverification_logo_image').show();
//            $('#velsof_exitpopup_pvtshop_background_image-name').val("");
            $('#ageverification_logo_remove-button').hide();
        }
    }
    return false;
}

function removeWindowImage() {
    if (confirm(delete_image_text)) {
        if ($('.thumb-ageverification_window_image').attr('src') == undefined) {
            $.ajax({
                url: module_path,
                data: 'ajax=true&method=deleteWindowImage',
                success: function(result) {
                    verificationwindowimageexist = 0;
                    if (result == 'No Image Found') {
                        alert(no_image_text);
                    } else {
                        alert(image_deleted_text);
                        $('#ageverification_window_remove-button').hide();
                        $('.default-ageverification_window_image').attr('src', default_image_path);
                    }
                }
            });
        } else {
            $('.thumb-ageverification_window_image').remove();
            $('.default-ageverification_window_image').show();
//            $('#velsof_exitpopup_pvtshop_background_image-name').val("");
            $('#ageverification_window_remove-button').hide();
        }
    }
    return false;
}

function removeBackgroundImage() {
    if (confirm(delete_image_text)) {
        if ($('.thumb-ageverification_background_image').attr('src') == undefined) {
            $.ajax({
                url: module_path,
                data: 'ajax=true&method=deleteBackgroundImage',
                success: function(result) {
                    verificationbackgroundimageexist = 0;
                    if (result == 'No Image Found') {
                        alert(no_image_text);
                    } else {
                        alert(image_deleted_text);
                        $('#ageverification_background_remove-button').hide();
                        $('.default-ageverification_background_image').attr('src', default_image_path);
                    }
                }
            });
        } else {
            $('.thumb-ageverification_background_image').remove();
            $('.default-ageverification_background_image').show();
//            $('#velsof_exitpopup_pvtshop_background_image-name').val("");
            $('#ageverification_background_remove-button').hide();
        }
    }
    return false;
}

//function kbChangeThemeImages() {
//    if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
//        if ($('[id^="age_verification[choose_theme]"]').val() == '1' || $('[id^="age_verification[choose_theme]"]').val() == '2' || $('[id^="age_verification[choose_theme]"]').val() == '3') {
//            $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
//        } else {
//            $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
//        }
//    } else {
//        $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
//    }
//    kbChangeDefaultImages();
//}

function kbChangeDefaultImages() {
    if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
        var logoimage_path = imagedir_path + 'theme' + $('[id^="age_verification[choose_theme]"]').val() + '/logo.png';
        var sideimage_path = imagedir_path + 'theme' + $('[id^="age_verification[choose_theme]"]').val() + '/side-img.jpg';
        $('.default-ageverification_logo_image').attr('src', logoimage_path);
        $('.default-ageverification_window_image').attr('src', sideimage_path);
        $('input[name="age_verification[logo_file]"]').closest('.form-group').hide();
        $('input[name="age_verification[verification_window_file]"]').closest('.form-group').hide();
        $('#ageverification_logo_remove-button').hide();
        $('#ageverification_window_remove-button').hide();
        $('#new-ageverification_logo_image').hide();
        $('#new-ageverification_window_image').hide();
        $('.default-ageverification_logo_image').show();
        $('.default-ageverification_window_image').show();
        ;
    } else {
        $('input[name="age_verification[logo_file]"]').closest('.form-group').show();
        $('input[name="age_verification[verification_window_file]"]').closest('.form-group').show();
        if (logoimageexist == 1) {
            $('#ageverification_logo_remove-button').show();
        } else {
            $('#ageverification_logo_remove-button').hide();
        }
        if (verificationwindowimageexist == 1) {
            $('#ageverification_window_remove-button').show();
        } else {
            $('#ageverification_window_remove-button').hide();
        }
        if ($('#new-ageverification_logo_image').length) {
            $('#new-ageverification_logo_image').show();
            $('.default-ageverification_logo_image').hide();
        } else {
            $('.default-ageverification_logo_image').attr('src', display_logo_image_path);
        }
        if ($('#new-ageverification_window_image').length) {
            $('#new-ageverification_window_image').show();
            $('.default-ageverification_window_image').hide();
        } else {
            $('.default-ageverification_window_image').attr('src', display_window_image_path);
        }
    }
    if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
        var backgroundimage_path = imagedir_path + 'theme' + $('[id^="age_verification[choose_theme]"]').val() + '/main-bg.jpg';
        $('.default-ageverification_background_image').attr('src', backgroundimage_path);
        $('#ageverification_background_remove-button').hide();
        $('input[name="age_verification[verification_background_file]"]').closest('.form-group').hide();
        $('#new-ageverification_background_image').hide();
        $('.default-ageverification_background_image').show();
    } else {
        $('input[name="age_verification[verification_background_file]"]').closest('.form-group').show();
        if (verificationbackgroundimageexist == 1) {
            $('#ageverification_background_remove-button').show();
        } else {
            $('#ageverification_background_remove-button').hide();
        }
        if ($('#new-ageverification_background_image').length) {
            $('#new-ageverification_background_image').show();
            $('.default-ageverification_background_image').hide();
        } else {
            $('.default-ageverification_background_image').attr('src', display_background_image_path);
        }
    }
    if ($('input[name="age_verification[enable_default_images]"]:checked').val() == '1') {
        if ($('[id^="age_verification[choose_theme]"]').val() == '1' || $('[id^="age_verification[choose_theme]"]').val() == '2' || $('[id^="age_verification[choose_theme]"]').val() == '3') {
            $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
        } else {
            $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').hide();
        }
    } else {
        $("#age_verification_window_file-images-thumbnails").parents('.form-group ').parents('.form-group ').show();
    }
}