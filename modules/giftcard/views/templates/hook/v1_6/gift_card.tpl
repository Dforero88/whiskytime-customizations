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

<link rel="stylesheet" type="text/css" href="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/css/gift_card.css">
{if count($values) > 0}

<script>
var required_label = "{l s='is required' mod='giftcard' js=1}";
{if $ps_version < 1.6}
{literal}
	var button = $('#buy_block #add_to_cart input[type="submit"]');
{/literal}
{else}
{literal}
	var button = $('#buy_block #add_to_cart button[type="submit"]');
{/literal}
{/if}
{literal}

var giftType = "{/literal}{$type|escape:'htmlall':'UTF-8'}{literal}";
var redirect = "{/literal}{$link->getpageLink('order', true)|escape:'htmlall':'UTF-8'}{literal}";
var price_label = (giftType && giftType == 'range')? "{/literal}{l s='Enter Price' mod='giftcard' js=1}: {literal}" : "{/literal}{l s='Select Price' mod='giftcard' js=1}: {literal}";
var isClicked = false;
var priceValidate = true;
var giftValidate = true;
var addedFromProductPage = true;
var pid = parseInt("{/literal}{$pid|escape:'htmlall':'UTF-8'}{literal}");
var tax_label = "{/literal}{if $priceDisplay == 1}{l s='tax excl.' mod='giftcard' js=1}{else}{l s='tax incl.' mod='giftcard' js=1}{/if}{literal}";
</script>
{/literal}
{include file="./add_to_cart_script.tpl"}
<div id="gift-card-wrapper">
	<input type="hidden" name="giftcard_type" value="{$type|escape:'htmlall':'UTF-8'}">
	{include file='./gift_radios.tpl'}
	<div class="price">
		<p class="our_price_display">
			{if $type == 'dropdown'}
				<div id="dropdown_price" style="display:none">
					<select width="40%" class="form-control" name="giftcard_price" id="gift_card_price" style="font-size:24px;font-weight:bold">
						{foreach from=$prices_tax item=price key=k}
							<option value="{$values[$k]|escape:'htmlall':'UTF-8'}">
								{convertPrice price=$price}
							</option>
						{/foreach}
					</select>
				</div>
			{elseif $type == 'range'}
				<div id="range_price" style="display:none">
					<label>{l s='Enter Price' mod='giftcard'}:</label>
					<input id="gift_card_price" class="form-control" name="giftcard_price" style="text-align: center; width: 45%;"/>
					{if $type == 'range'}
					<div class="amount" style="font-size:10px">{l s='Enter value between' mod='giftcard'} {convertPrice price=$values[0]} {l s=' and ' mod='giftcard'} {convertPrice price=$values[1]}
						<div id="price_error" class="alert alert-danger error" style="display:none">
							<p  style="font-size:18px;">{l s='Invalid Price' mod='giftcard'}</p>
						</div>
						<input type="hidden" id="range_min" value="{$values[0]|escape:'htmlall':'UTF-8'}"/>
						<input type="hidden" id="range_max" value="{$values[1]|escape:'htmlall':'UTF-8'}"/>
					</div>
					{/if}
				</div>
			{/if}
		</p>
	</div>
</div>
{/if}
