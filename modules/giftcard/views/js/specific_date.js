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

$(document).ready(function() {
    if ($('#specific_date_check').prop('checked')) {
        $('#specific-date-wrapper').show();
        $('#specific-date-selector').addClass('gc_required_fields');
        checkGcRequiredVals();
    }

    initLaterDate();
})

$(document).on('click', '#specific_date_check', function(e){
    $('#specific-date-wrapper').hide();
    $('#specific-date-selector').removeClass('gc_required_fields');
    if ($(this).prop('checked')) {
        $('#specific-date-wrapper').show();
        $('#specific-date-selector').addClass('gc_required_fields');
    }
    checkGcRequiredVals();
});


$(document).on('click', '#template-preview', function(e) {
    //e.preventDefault();
    var ddData =$(this).parents('.template-wrapper');
    directParent = $('.send_someone_temp'),


// console.log(imageSrc = $.trim(elderSibling.find('.card_image').attr('img-src')));
    template_vars['{rec_name}'] = $.trim($('input[name="gift_vars[reciptent]"]').val());
    template_vars['{message}'] = $.trim($('textarea[name="gift_vars[message]"]').val());
    template_vars['{quantity}'] = parseInt($.trim($('#quantity_wanted').val()));
    // template_vars['{video_link}'] = '';
    var videoUrl = $.trim(directParent.find('input[name=temp_video]').val());
    template_vars['{video_link}'] = videoUrl ? `<p>${video_paragraph}<a href="${videoUrl}" target="_blank">${video_link_title}</a><p>` : '';

    // console.log(template_vars["{gift_image}"]);

    var variables = [], values = [];
    for (var item in template_vars) {
        variables.push(item)
        values.push(template_vars[item]);
    }
    // var cleanContent = strReplace(atob(ddData.find('select[name=email_template] option:selected').attr('data-content')), variables, values);
    var encodedContent = ddData.find('select[name=email_template] option:selected').attr('data-content');
    var decodedContent = b64DecodeUnicode(encodedContent);
    var cleanContent = strReplace(decodedContent, variables, values);
    displayPreview(cleanContent);
});

function initLaterDate() {
    flatpickr($('#specific-date-selector'), dateOptions);
}

function displayPreview(content) {
    var modal = $('#gift-template-modal').iziModal({
        width: '70%',
        title: preview_label,
        headerColor: '#363A41',
        navigateArrows: false,
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight' 
    });
    modal.iziModal('setContent', content);
    modal.iziModal('open', { zindex: 99999 });
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
  return decodeURIComponent(atob(str).split('').map(function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));
}
