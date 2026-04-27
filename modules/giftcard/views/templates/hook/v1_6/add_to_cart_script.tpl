{**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2021 FMM Modules
*  @license   FMM Modules
*}

{literal}
<script type="text/javascript">
$(document).ready(function()
{
    var addToCartEvent = true;
    initGiftCard(giftType);
});

$(document).on('keydown, keyup', '.gc_required_fields', checkGcRequiredVals);
$(document).on('click', 'input[name=gift_order_type]', function(event) {
    checkGiftCardOrderMethod($(this).val());
});

function initGiftCard(giftType) {
    $('#add_to_cart').hide();
    $('#buy_block').prepend('<div id="gift_product" class="box-info-product box"></div>');

    var price_elem = $('#gift-card-wrapper');
    $('#gift_product').html(price_elem);
    if (price_elem.length) {
        if (giftType == 'dropdown') {
            $('#add_to_cart').show();
            price_elem.append(
                $('#dropdown_price').show()
            );
        } else if (giftType == 'fixed') {
            $('.product-prices').show();
            $('#add_to_cart').show();
        } else if (giftType == 'range') {
            price_elem.append(
                $('#range_price').show()
            );
            if ($('input[name=giftcard_price').val()) {
                $('input[name=giftcard_price').val()
                _validatePrice($('input[name=giftcard_price').val(), giftType);
            }
        }
        //$('#gift_card_price').before('<span class="giftcard_custom_price" style="font-size:16px;">'+ price_label +'</span>');
    }

    $('#old_price, #reduction_percent, #reduction_amount').hide();
    _triggerAddToCart(giftType);
    checkGiftCardOrderMethod($('input[name=gift_order_type]:checked').val());
    checkGcRequiredVals();
}

$(document).on('keydown', '#gift_card_price', function(e) {
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

$(document).on('keyup', '#gift_card_price', function(e) {
    _validatePrice($(this).val(), 'range');
});

function _validatePrice(val, giftType) {
    checkGcRequiredVals();

    var priceValidate = true;
    val = parseFloat(val);
    $('#add_to_cart').show();
    $('#price_error').hide();
    $('#gift_card_price').attr('value', val);
    if (isNaN(val) && giftType != 'fixed') {
        $('#gift_card_price').val('');
        $('#price_error').show();
        $('#add_to_cart').hide();
        $('#gift_card_price').focus();
        priceValidate = false;
    }

    if (giftType == 'range') {
        $('#add_to_cart').show();
        var min = parseFloat($('#range_min').val());
        var max = parseFloat($('#range_max').val());
        if (!val || (val < min) || (val > max)) {
            $('#gift_card_price').focus();
            $('#price_error').show();
            $('#add_to_cart').hide();
            priceValidate = false;
        }
    }

    return priceValidate;
}

function _triggerAddToCart(cardType)
{
    button.on('click', function(event) {
        $('#gift-receiver-errors').remove();
        event.stopImmediatePropagation();
        event.preventDefault();
        var giftPrice = 0;
        if (cardType == 'range') {
            giftPrice = $('input[name=giftcard_price').val();
        } else if (giftType == 'dropdown') {
            giftPrice = $('select#gift_card_price option:selected').val();
        }
        priceValidate = _validatePrice(giftPrice, cardType);
        if (priceValidate && giftValidate) {
            var quantity = $('#quantity_wanted').val();
            var requestData = {
                type        : "POST",
                headers     : { "cache-control": "no-cache" },
                cache       : false,
                async       : false,
                dataType    : "json",
                url         : baseUri + '?rand=' + new Date().getTime() + '&' + $('form#buy_block').serialize(),
                data        : 'controller=cart&add=1&ajax=true&qty='+ ((quantity && quantity != null) ? quantity : 1)
                            + '&id_product='+ pid
                            + '&token='+ token
                            + '&ipa=0'
                            + '&allow_refresh=1&giftcard_price='+ giftPrice,
                success: function(jsonData, textStatus, jqXHR)
                {
                    if (jsonData && jsonData.errors) {
                        $('#giftcard_send_to_friend').prepend('<p id="gift-receiver-errors" class="alert alert-danger error">' + jsonData.errors + '</p>');
                    } else {
                        ajaxCart.updateCartEverywhere(jsonData);
                        var idProduct = pid;
                        var idCombination =$('#idCombination').val();
                        var callerElement = null;
                        var quantity = $('#quantity_wanted').val();
                        var whishlist = null;

                        if (whishlist && !jsonData.errors)
                            WishlistAddProductCart(whishlist[0], idProduct, idCombination, whishlist[1]);

                        // add the picture to the cart
                        var $element = $(callerElement).parent().parent().find('a.product_image img,a.product_img_link img');
                        if (!$element.length)
                            $element = $('#bigpic');
                        var $picture = $element.clone();
                        var pictureOffsetOriginal = $element.offset();

                        if ($picture.size() || $picture.length)
                            $picture.css({'position': 'absolute', 'top': pictureOffsetOriginal.top, 'left': pictureOffsetOriginal.left});

                        var pictureOffset = $picture.offset();
                        if ($('#cart_block, .cart_block').offset.top && $('#cart_block, .cart_block').offset.left)
                            var cartBlockOffset = $('#cart_block, .cart_block').offset();
                        else
                            var cartBlockOffset = $('#shopping_cart').offset();

                        // Check if the block cart is activated for the animation
                        if (cartBlockOffset != undefined && ($picture.size() || $picture.length))
                        {
                            $picture.appendTo('body');
                            $picture.css({ 'position': 'absolute', 'top': $picture.css('top'), 'left': $picture.css('left'), 'z-index': 4242 })
                            .animate({ 'width': $element.attr('width')*0.66, 'height': $element.attr('height')*0.66, 'opacity': 0.2, 'top': cartBlockOffset.top + 30, 'left': cartBlockOffset.left + 15 }, 1000)
                            .fadeOut(100, function() {
                                ajaxCart.updateCartInformation(jsonData, addedFromProductPage);
                                $(this).remove();
                            });
                        }
                        else
                            ajaxCart.updateCartInformation(jsonData, addedFromProductPage);
                        //window.location = redirect;
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
                    if (addedFromProductPage)
                        $('body#product p#add_to_cart input').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');
                    else
                        $(callerElement).removeAttr('disabled');
                }
            };
            $.ajax(requestData);
        }
        return false;
    });
}

function checkGiftCardOrderMethod(method) {

    if (typeof method === 'undefined') {
        $('#print-home').click();
    }

    $('#add_to_cart').hide();
    if (method == 'sendsomeone') {
        $('#giftcard_send_to_friend').show();
    } else {
        $('#giftcard_send_to_friend').hide();
    }

    checkGcRequiredVals();
}

function checkGcRequiredVals() {
    var show_add_to_cart = false;
    var selected_method = $('input[name=gift_order_type]:checked').val();
    $('#add_to_cart').hide();
    if (selected_method == 'sendsomeone') {
        $('.gc_required_fields').each(function() {
            if ($.trim($(this).val())) {
                show_add_to_cart = true;
            } else {
                show_add_to_cart = false;
                $(this).attr({
                    placeholder: $(this).prev('.gc_required_label').text() + ` ${required_label}`,
                })
            }
        });
    } else {
        show_add_to_cart = true;
    }
    if (show_add_to_cart) {
        $('#add_to_cart').show();
    }
}
</script>
{/literal}