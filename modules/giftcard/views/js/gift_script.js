/*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2021 FMM Modules
*  @license   FMM Modules
*/

var price_elem = $('#gift-card-wrapper');
$(document).ready(function() {
    price_elem.show();
    if (typeof giftType !== 'undefined' && giftType) {
        initGiftCard(giftType)
    }
    prestashop.on('updatedProduct', function() {
        $('#add-to-cart-or-refresh .add-to-cart').removeAttr('disabled');
    });


    prestashop.on('updateProduct', function(e) {
        setTimeout(function() {
            initGiftCard(giftType);
            $('.product-additional-info').find('#gift-card-wrapper').remove();
        }, 2000);
    });

    prestashop.on('clickQuickView', function(e) {
        setTimeout(function() {
            price_elem.show();
            var _ctype = _getCtype(e.dataset.idProduct);
            if (typeof _ctype !== 'undefined' && _ctype) {
                _getGiftPrice(_ctype, parseFloat($('#gift_card_price').val()), e.dataset.idProduct);
                initLaterDate();
            }
        }, 600);
    })
    
})

$(document).on('click', 'input[name=gift_order_type]', function(event) {
    checkGiftCardOrderMethod($(this).val());
});

$(document).on('keydown, keyup', '.gc_required_fields', checkGcRequiredVals);
$(document).on('change', '.gc_required_fields', checkGcRequiredVals);

function _getGiftPrice(_ctype, _sprice, pid) {
    pid = (typeof pid === 'undefined' || !pid)? 0 : pid;
    var update_display = ((_ctype == 'fixed' && $('#gift-card-wrapper').length <= 0) || $.inArray(_ctype, ['dropdown','range']) !== -1)? true : false;
    $.post(ajax_gc , { ajax: '1', action: 'getGiftPrice', id_product: pid, card_type: _ctype, current_price: _sprice }, null, 'json')
    .then(function (resp) {
        $('.gift_card').remove();
        if ($.inArray(_ctype, ['dropdown','range']) !== -1) {
            $('#' + _ctype + '_price').remove();
        }
        $('#gift_product').html($(price_elem));

        if (update_display) {
            $('#gift-card-wrapper').append(resp.gift_prices).show();
        }
        $('.product-additional-info').find('#gift-card-wrapper').remove();
        initGiftCard(_ctype);
    });
}

function _getCtype(pid) {
    var cType = '';
    var options = {
        type        : "GET",
        cache       : false,
        async       : false,
        dataType    : "json",
        url         : ajax_gc,
        data        : {
            ajax : 1,
            action : 'getGiftType',
            id_product : pid,
        },
        success : function(resp) {
            if(resp && resp.gift_type) {
                cType = resp.gift_type;
            }
        },
    };
    $.ajax(options);
    return cType;
}

function initGiftCard(giftType) {
    //  check gift card type on product page.
    var price_elem = $('#gift-card-wrapper');
    $('.add-to-cart').hide();
    if ($('#gift_product').length <= 0) {
        $('#add-to-cart-or-refresh').prepend('<div id="gift_product"></div>');
        $('#add-to-cart-or-refresh .add-to-cart').removeAttr('disabled');
    }

    $('#gift_product').html($(price_elem));
    
    checkGiftCardOrderMethod($('input[name=gift_order_type]:checked').val());

    checkGcRequiredVals();

    if (price_elem.length) {
        if (giftType == 'range') {
            $('#gift_card_price').keydown(function(e) {
                var key = e.charCode || e.keyCode || 0;
                return (key == 8 || 
                        key == 9 ||
                        key == 46 ||
                        key == 110 ||
                        key == 190 ||
                        (key >= 35 && key <= 40) ||
                        (key >= 48 && key <= 57) ||
                        (key >= 96 && key <= 105));
            });
            _validatePrice();
        }
    }
}

function checkGiftCardOrderMethod(method) {
    if (typeof method === 'undefined') {
        $('#print-home').click();
    }

    $('.add-to-cart').hide();
    if (method == 'sendsomeone') {
        $('#giftcard_send_to_friend').show();
    } else {
        $('#giftcard_send_to_friend').hide();
    }

    checkGcRequiredVals();
}

function checkGcRequiredVals() {
    var show_add_to_cart = [];
    $('.add-to-cart').hide();
    var selected_method = $('input[name=gift_order_type]:checked').val();
    $('.add-to-cart').hide();
    if (selected_method == 'sendsomeone') {
        $('.gc_required_fields').each(function() {
            if ($.trim($(this).val())) {
                show_add_to_cart.push(true);
            } else {
                show_add_to_cart.push(false);
                $(this).attr({
                    placeholder: $(this).prev('.gc_required_label').text() + ` ${required_label}`,
                })
            }
        });
    } else {
        show_add_to_cart.push(true);
    }

    if ($.inArray(false, show_add_to_cart) === -1) {
        $('.add-to-cart').show();
    } else {
        return false;
    }

    _validatePrice();
}

function _validatePrice() {
    if (typeof giftType !== 'undefined' && giftType == 'range') {
        var val = $('#gift_card_price').val();
        var min = parseFloat($('#range_min').val());
        var max = parseFloat($('#range_max').val());
        val = (isNaN(val) || typeof val === 'undefined')? 0.00 : parseFloat(val);
        if (typeof val !== 'undefined') {
            $('.add-to-cart').show();
            $('#price_error').hide();
            $('#gift_card_price').attr('value', val);

            if (!val || (val < min) || (val > max)) {
                $('#gift_card_price').focus();
                $('#price_error').show();
                $('.add-to-cart').hide();
            }
        }
    }
}
$(document).on('click', 'a#button_share_cart', function() {
    var facebook_link = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent($(this).attr('data'));
    var messenger_link = 'https://www.facebook.com/dialog/send?display=page&link=' + encodeURIComponent($(this).attr('data'));
    var skype_link = 'https://web.skype.com/share?url=' + encodeURIComponent($(this).attr('data'));
    var twitter_link = 'https://twitter.com/home?status=' + encodeURIComponent($(this).attr('data'));
    var linkedin_link = 'https://www.linkedin.com/shareArticle?mini=true&title=&summary=&source=&url=' + encodeURIComponent($(this).attr('data'));
    var email_link = 'mailto:info@example.com?&subject=&body=' + encodeURIComponent($(this).attr('data'));
    var whatsapp_link = 'https://wa.me/?text=' + encodeURIComponent($(this).attr('data'));
    $('.share-buttons a.btn-facebook').attr('href', facebook_link);
    $('.share-buttons a.btn-messenger').attr('href', messenger_link);

    // $('.share-buttons a.btn-messenger').attr('data', $(this).attr('data'));
    $('.share-buttons a.btn-skype').attr('href', skype_link);
    $('.share-buttons a.btn-twitter').attr('href', twitter_link);
    $('.share-buttons a.btn-linkedin').attr('href', linkedin_link);
    $('.share-buttons a.btn-envelope').attr('href', email_link);
    $('.share-buttons a.btn-whatsapp').attr('href', whatsapp_link);
    });
    
    /**
    * Set for the Copy button i-e copy to clipboard
    */
   