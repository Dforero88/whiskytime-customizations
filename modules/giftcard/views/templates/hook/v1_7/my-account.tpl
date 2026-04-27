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

<!-- MODULE Giftcard -->
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="{$link->getModuleLink('giftcard', 'mygiftcards', ['my_gifts' => 'show'], true)|escape:'htmlall':'UTF-8'}" title="{l s='My giftcards' mod='giftcard'}">
	<span class="link-item">
		<i class="material-icons">card_giftcard</i>
		{l s='Purchased Giftcards' mod='giftcard'}
	</span>
</a>
{* 
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="{$link->getModuleLink('giftcard', 'mygiftcards', ['pending_gifts' => 'show'], true)|escape:'htmlall':'UTF-8'}" title="{l s='My giftcards' mod='giftcard'}">
	<span class="link-item">
		<i class="material-icons">credit_card</i>
		{l s='Pending Giftcards' mod='giftcard'}
	</span>
</a>
*}
<!-- END : MODULE Giftcard -->