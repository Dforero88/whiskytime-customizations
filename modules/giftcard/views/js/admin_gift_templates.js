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

var rte_mail_config = {
    selector: ".rte",
    plugins: "preview align colorpicker link image filemanager table media placeholder advlist code table autoresize",
    toolbar1: "code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect",
    toolbar2: "preview | receiver | sender | gift_image | voucher_name | vcode | discount | quantity | period | message | shop_name | shop_url | shop_logo",
    setup: function (editor) {
        each({
			receiver: '{rec_name}',
			sender: '{sender}',
			gift_image: '{gift_image}',
			voucher_name: '{giftcard_name}',
			vcode: '{vcode}',
			discount: '{value}',
            quantity: '{quantity}',
            period: '{expire_date}',
            message: '{message}',
            shop_name: '{shop_name}',
            shop_url: '{shop_url}',
            shop_logo: '{shop_logo}'
		}, function(text, name) {
			editor.addButton(name, {
				text: text,
                classes: 'btn btn-default button',
				onclick: function() {
					editor.insertContent(text);
				}
			});
		})
    }
};

tinySetup(rte_mail_config);

$(document).on('change', '#thumb', function() {
    readURL(this);
});

function readURL(input) {
    previewElem();
    var fileTypes = ['jpg', 'jpeg', 'png', 'gif'];  //acceptable file types
    if (input.files && input.files[0]) {
        var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
            fileSize = (input.files[0].size / 1048576).toFixed(3);
            isValid = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types

        if (!isValid) {
            showErrorMessage(format_error);
            input.value = '';
            $('#thumb-images-thumbnails').hide();
            $('#thumb-images-thumbnails').find('.img-thumbnail').attr('src', '');
        } else if (fileSize > max_upload_limit) {
            showErrorMessage(size_error);
            input.value = '';
            $('#thumb-images-thumbnails').hide();
            $('#thumb-images-thumbnails').find('.img-thumbnail').attr('src', '');
        } else {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#thumb-images-thumbnails').show();
                $('#thumb-images-thumbnails').find('.img-thumbnail').attr({'src': e.target.result, 'width': '98px'});
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
}

function each(o, cb, s) {
    var n, l;

    if (!o) {
        return 0;
    }

    s = s || o;

    if (o.length !== undefined) {
        // Indexed arrays, needed for Safari
        for (n=0, l = o.length; n < l; n++) {
            if (cb.call(s, o[n], n, o) === false) {
                return 0;
            }
        }
    } else {
        // Hashtables
        for (n in o) {
            if (o.hasOwnProperty(n)) {
                if (cb.call(s, o[n], n, o) === false) {
                    return 0;
                }
            }
        }
    }

    return 1;
}

function previewElem() {
    if (!$('#thumb-images-thumbnails').length) {
        $('#thumb-name').closest('.form-group').before(`<div id="thumb-images-thumbnails" class="form-group">
            <div class="col-lg-12">
                <img id="template-thumb" src="" class="img img-thumbnail" width="98">
            </div>
        </div>`);
    }
}