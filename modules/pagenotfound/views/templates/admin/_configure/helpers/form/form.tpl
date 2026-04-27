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
*  International Registered Trademark & Property of ETS-Soft
*}
{extends file="helpers/form/form.tpl"}
{block name="input_row"}
	{if isset($isConfigForm) && $isConfigForm}
		<div class="form-group ets-form-group form_{$input.name|lower|escape:'html':'UTF-8'}{if isset($input.isShow) && $input.isShow} {$input.isShow|escape:'html':'UTF-8'}{/if}{if isset($input.parent) && $input.parent} js-for_{$input.parent|lower|escape:'html':'UTF-8'}{/if}">
			{if !isset($define)}
				{assign var="define" value="0"}
			{/if}
			{if !$define && $input.name == 'exported_fields'}
				{if $define <= 0}
					{assign var="define" value="1"}
				{/if}
				<div class="form-group">
					<span class="ets_pnf_title_available">{l s='Available fields' mod='pagenotfound'}</span>
				</div>
			{/if}
			{$smarty.block.parent}
			{if isset($input.info) && $input.info}
				<div class="ets_tc_info alert alert-warning">{$input.info|escape:'html':'UTF-8'}</div>
			{/if}
			{if $input.type == 'file'}
				<div class="form-group ets_uploaded_img_wrapper">
					<label class="control-label col-lg-4 uploaded_image_label" style="font-style: italic;">&nbsp;</label>
					<div class="col-lg-8 uploaded_img_wrapper">
						<a  class="ets_fancy" href="{$input.display_img|escape:'html':'UTF-8'}"><img title="{l s='Click to see full size image' mod='pagenotfound'}" style="display: inline-block; max-width: 240px;" src="{$input.display_img|escape:'html':'UTF-8'}" /></a>
					</div>
				</div>
			{/if}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="input"}
	{if $input.type == 'search'}
		<div class="ets_pnf_search ets_pnf_search_{$input.name|escape:'html':'UTF-8'}">
			<input id="ets_specific_{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" type="hidden" name="{$input.name|escape:'html':'UTF-8'}">
			<div class="input_group">
				<input id="ets_pnf_search_{$input.name|escape:'html':'UTF-8'}" class="input_ets_pnf_search js-input_ets_pnf_search" name="ets_pnf_search_{$input.name|escape:'html':'UTF-8'}" value="" autocomplete="off" type="text" placeholder="{if isset($input.placeholder)}{$input.placeholder|escape:'html':'UTF-8'}{/if}" data-type="{if isset($input.type_search)}{$input.type_search|escape:'html':'UTF-8'}{/if}"/>
				<span class="input-group-addon">
					<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
				</span>
			</div>
			<ul class="ets-pnf-items-added ets_pnf_search_list_{$input.name|escape:'html':'UTF-8'}">
				{if $input.name == 'ETS_PNF_PRODUCTS'}
					{hook h='displayEtsPnfProductList' ids = $fields_value[$input.name]|escape:'html':'UTF-8'}
				{elseif $input.name == 'ETS_PNF_BLOGS'}
					{hook h='displayEtsPnfBlogList' ids = $fields_value[$input.name]|escape:'html':'UTF-8'}
				{elseif $input.name == 'ETS_PNF_BLOGS_FREE'}
					{hook h='displayEtsPnfBlogListFree' ids = $fields_value[$input.name]|escape:'html':'UTF-8'}
				{/if}
				<li class="ets_pnf_ac_results_loading"></li>
			</ul>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
