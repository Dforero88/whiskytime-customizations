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

{extends file=$layout}

{block name='content'}

<div class="block card card-block">

<h4 class="block title_block giftcards_title">{l s='Pending Gift Cards' mod='giftcard'}</h4>
	<div id="block-pending-giftcard" class="block-center table-responsive">
		{if count($pending_cards) > 0}
			<table class="table table-bordered {if $ps_version < 1.6}std{/if}" id="order-list">
					<thead>
						<tr>
							<th class="first_item">{l s='Card' mod='giftcard'}</th>
							<th class="item">{l s='Qty' mod='giftcard'}</th>
							<th class="item">{l s='Value' mod='giftcard'}</th>
							<th class="item">{l s='Status' mod='giftcard'}</th>
						</tr>
					</thead>
				<tbody>
					{foreach from=$pending_cards item=card}
							<tr class="my-gift-voucher" style="text-align:center">
								<td>
									<center><span><img style="height:50px;" alt="" src="{$link->getImageLink($card.link_rewrite, $card.id_image, 'small_default')|escape:'htmlall':'UTF-8'}"></span>
									<p>{$card.name|escape:'htmlall':'UTF-8'}<p></center>
								</td>
								<td>
									{$card.product_quantity|escape:'htmlall':'UTF-8'}
								</td>
								<td>
									{if isset($card.product_price) AND $card.product_price != 0}
										{if Tools::version_compare(_PS_VERSION_, '9.0.0', '>=')}
                                            {* {$card.product_price|escape:'htmlall':'UTF-8'} *}
											{$card.product_price}
                                            
                                        {else}
                                            {Tools::displayPrice($card.product_price)|escape:'htmlall':'UTF-8'}
                                        {/if}
										{* {Tools::displayPrice($card.product_price)|escape:'htmlall':'UTF-8'} *}
									{else}
										{l s='0' mod='giftcard'}
									{/if}
								</td>
								<td>
									<span class="btn btn-warning button">{l s='Pending' mod='giftcard'}</span>
								</td>
							</tr>
					{/foreach}
				</tbody>
			</table>
			{else}
			<div class="alert alert-success conf">
				<center>{l s='You do not have any pending gift cards.' mod='giftcard'}</center>
			</div>
		{/if}
	</div>

{block name='page_footer'}
  {block name='my_account_links'}
    {include file='customer/_partials/my-account-links.tpl'}
  {/block}
{/block}
</div>

{/block}
