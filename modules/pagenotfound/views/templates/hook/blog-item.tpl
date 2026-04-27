{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if isset($blogs) && $blogs}
	{foreach from=$blogs item='blog'}
		<li class="ets_pnf_item ets_pnf_item_blog" data-id="{$blog.id_post|intval}">
			<div class="ets-pnf-item-mini ets-pnf-post-mini">
				<a class="ets-item__link" href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}" itemprop="url" target="_blank">
					<img class="ets-item__img" src="{$blog.image|escape:'quotes':'UTF-8'}" alt="{$blog.title|truncate:20:'...':true|escape:'html':'UTF-8'}" width="64"/>
					<div class="ets-item__info">
						<div class="ets-item__name">{$blog.title|truncate:80:'...':true|escape:'html':'UTF-8'}</div>
						<div class="ets-item__id">{l s='ID' mod='pagenotfound'}: {$blog.id_post|intval}</div>
					</div>
				</a>
				<div class="ets-item__action ets-action-remove" title="{l s='Delete' mod='pagenotfound'}">
					<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 1376v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm-544-992h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
				</div>
			</div>
		</li>
	{/foreach}
{/if}