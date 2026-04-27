{*
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

<button class="btn btn-default preview-content">
    <i class="icon icon-eye"></i> {l s='Preview' mod='giftcard'}
</button>
<div id="modal"></div>

<script type="text/javascript">
var preview_label = "{l s='Template Preview' mod='giftcard' js=1}";

// create special config
var rte_mail_config = {
    selector: ".rte",
    apply_source_formatting : false,
    plugins: "preview align colorpicker link image filemanager table media placeholder advlist code table autoresize",
    toolbar1: "code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect",
    toolbar2: "preview | receiver | sender | gift_image | voucher_name | vcode | discount | quantity | period | message | shop_name",
    setup: function (editor) {
        each({
			receiver: '{literal}{rec_name}{/literal}',
			sender: '{literal}{sender}{/literal}',
			gift_image: '{literal}{gift_image}{/literal}',
			voucher_name: '{literal}{giftcard_name}{/literal}',
			vcode: '{literal}{vcode}{/literal}',
			discount: '{literal}{value}{/literal}',
            quantity: '{literal}{quantity}{/literal}',
            period: '{literal}{expire_date}{/literal}',
            message: '{literal}{message}{/literal}',
            shop_name: '{literal}{shop_name}{/literal}'
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

$(document).on('click', '.preview-content', function(e){
    e.preventDefault();

    var content = tinyMCE.activeEditor.getContent();
    var modal = $('#modal').iziModal({
        width: '70%',
        title: preview_label,
        headerColor: '#363A41',
        navigateArrows: false,
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight' // TransitionOut will be applied if you have any open modal.
    });
    modal.iziModal('setContent', content);
    modal.iziModal('open');

})

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
</script>