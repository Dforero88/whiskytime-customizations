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

<button name="package"
	class="btn btn-default"
	type="button"
	onclick="showGiftDetails('{$row.id_product|escape:'htmlall':'UTF-8'}', '{$row.id_order|escape:'htmlall':'UTF-8'}'); return false;"
	value="{$row.id_product|escape:'htmlall':'UTF-8'}"><i class="icon-eye"></i> {l s='Show Detail' mod='giftcard'}
</button>
