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
<li class="lnk_giftcard">
	<a href="{$link->getModuleLink('giftcard', 'mygiftcards', ['my_gifts' => 'show'], true)|escape:'htmlall':'UTF-8'}" title="{l s='Purchased giftcards' mod='giftcard'}">
		{if $ps_version < 1.6}
		<img src="{$module_template_dir|escape:'htmlall':'UTF-8'}views/img/gift.png" alt="{l s='Purchased Gift Cards' mod='giftcard'}" class="icon" />
		{/if}
		<span>{l s='Purchased Giftcards' mod='giftcard'}</span>
		{if $ps_version >= 1.6}
		<i class="icon-gift"></i>
		{/if}
	</a>
</li>

<li class="lnk_giftcard">
	<a href="{$link->getModuleLink('giftcard', 'mygiftcards', ['pending_gifts' => 'show'], true)|escape:'htmlall':'UTF-8'}" title="{l s='Pending giftcards' mod='giftcard'}">
		{if $ps_version < 1.6}
		<img src="{$module_template_dir|escape:'htmlall':'UTF-8'}views/img/gift.png" alt="{l s='Pending Gift Cards' mod='giftcard'}" class="icon" />
		{/if}
		<span>{l s='Pending Giftcards' mod='giftcard'}</span>
		{if $ps_version >= 1.6}
		<i class="icon-gift"></i>
		{/if}
	</a>
</li>
<!-- END : MODULE Giftcard -->