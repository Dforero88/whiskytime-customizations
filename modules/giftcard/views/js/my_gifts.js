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

$(function() {
    if (isGiftListingPage) {
        var f = $('.filtr-container').filterizr({ controlsSelector: '.fltr-controls' });
        window.filterizr = f;

        $('.gift-filter li').click(function() {
            $('.gift-filter li').removeClass('active');
            $(this).addClass('active');
        });
        //Shuffle control
        $('.shuffle-btn').click(function() {
        $('.sort-btn').removeClass('active');
        });
        //Sort controls
        $('.sort-btn').click(function() {
        $('.sort-btn').removeClass('active');
        $(this).addClass('active');
        });
    }

});

$(document).on('click', '.show-form', function(e){
	$(this).closest('.my-gift-voucher').next('.form-row').toggleClass('invisible_row');
});


$(document).on('click', '.preview_template', function(e) {

    var templateVars = [];
    var id_cart = $(this).attr('data-cart'),
        id_product = $(this).attr('data-pid'),
        directParent = $(this).parents('.send_someone'),
        elderSibling = $(`#gift-card-${id_cart}-${id_product}`)
        imageSrc = $.trim(elderSibling.find('.card_image').attr('img-src'))
        content = directParent.find('select[name=email_template] option:selected').attr('data-content');

    templateVars['{quantity}'] = 1;
    templateVars['{vcode}'] = 'XXXXX-XXXXX';
    templateVars['{shop_logo}'] = shop_logo;
    templateVars['{expire_date}'] = expiry_date;
    templateVars['{shop_name}'] = shop_name;
    templateVars['{shop_url}'] = shop_url;
    templateVars['{sender}'] = sender;
    templateVars['{rec_name}'] = $.trim(directParent.find('input[name=friend_name]').val());
    templateVars['{message}'] = $.trim(directParent.find('textarea[name=friend_message]').val());
    var videoUrl = $.trim(directParent.find('input[name=id_giftcard_video]').val());
    templateVars['{video_link}'] = videoUrl ? `<p>${video_paragraph}<a href="${videoUrl}" target="_blank">${video_link_title}</a><p>` : '';
    templateVars['{value}'] = $.trim(elderSibling.find('.card_price').text());
    templateVars['{giftcard_name}'] = $.trim(elderSibling.find('.card_name').text());
    templateVars['{gift_image}'] = '<img src= ' + imageSrc + '>';

    var variables = [], values = [];
    for (var item in templateVars) {
        variables.push(item)
        values.push(templateVars[item]);
    }

    // var cleanContent = strReplace(atob(content), variables, values );
    var decodedContent = b64DecodeUnicode(content);
    var cleanContent = strReplace(decodedContent, variables, values);
    displayPreview(cleanContent);
});

function displayPreview(content) {
    var modal = $('#gift-template-modal-account').iziModal({
        width: '70%',
        title: preview_label,
        headerColor: '#363A41',
        navigateArrows: false,
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight' 
    });
    modal.iziModal('setContent', content);
    modal.iziModal('open');
}

function strReplace( str, findArray, replaceArray ) {
    var i, regex = [], map = {};
    for( i=0; i<findArray.length; i++ ){
      regex.push( findArray[i].replace(/([-[\]{}()*+?.\\^$|#,])/g,'\\$1') );
      map[findArray[i]] = replaceArray[i];
    }
    regex = regex.join('|');
    str = str.replace( new RegExp( regex, 'g' ), function(matched){
      return map[matched];
    });
    return str;
}

function b64DecodeUnicode(str) {
  try {
    return decodeURIComponent(
      atob(str)
        .split('')
        .map(function (c) {
          return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        })
        .join('')
    );
  } catch (e) {
    console.error('Invalid base64 or non-UTF8 characters:', e);
    return '';
  }
}

