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

<div id="range_price" class="gc-product-variants">
	<div class="product-variants-item">
		<span class="giftcard_custom_price" style="font-size:16px;margin-left:5px;">{l s='Enter Price: ' mod='giftcard'}</span>
		<input id="gift_card_price"
		class="product-price input-group form-control"
		name="giftcard_price"
		style="width: 40%;margin-left:5px;"
		onkeyup="_validatePrice($(this).val(), 'range')"
		value="{if isset($preselected_price) AND $preselected_price > 0}{$preselected_price|escape:'htmlall':'UTF-8'}{else}{$values[0]|escape:'htmlall':'UTF-8'}{/if}"/>
		{if $type == 'range'}
			<div class="amount" style="font-size:10px;margin-left:5px;">
				{l s='Enter value between' mod='giftcard'}
				{if Tools::version_compare(_PS_VERSION_, '9.0.0', '>=')}
					{* {($values[0])} {l s=' and ' mod='giftcard'} {($values[1])|escape:'htmlall':'UTF-8'} *}
					{$values[0]} {l s=' and ' mod='giftcard'} {$values[1]}
					
				{else}
					{Tools::displayPrice($values[0])|escape:'htmlall':'UTF-8'} {l s=' and ' mod='giftcard'} {Tools::displayPrice($values[1])|escape:'htmlall':'UTF-8'}
				{/if}
				<div id="price_error" class="alert alert-danger error" style="width: 98%;display:none;">
					<p style="color:#CE1F21;font-size:18px;margin-left:5px;">{l s='Invalid Price' mod='giftcard'}</p>
				</div>
				<input type="hidden" id="range_min" value="{$values[0]|escape:'htmlall':'UTF-8'}"/>
				<input type="hidden" id="range_max" value="{$values[1]|escape:'htmlall':'UTF-8'}"/>
			</div>
		{/if}
	</div>
</div>
<script>
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
</script>
