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

<div id="dropdown_price" class="gc-product-variants">
	<div class="product-variants-item" style="margin-left:5px;">
		<span class="control-label">{l s='Select Price' mod='giftcard'} : </span>
		<select width="40%" class="product-price form-control" name="giftcard_price" id="gift_card_price" style="font-weight:bold;width:45%">

			{foreach from=$prices_tax item=price key=k}
				<option value="{$values[$k]|escape:'htmlall':'UTF-8'}" {if isset($preselected_price) AND $preselected_price AND $preselected_price == $values[$k]}selected="selected"{/if}>
					{$price|escape:'htmlall':'UTF-8'}
				</option>
			{/foreach}
		</select>
	</div>
</div>