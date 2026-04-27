{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="aeicategorytabs" class="tabs products_block clearfix"> 	
<div class="container">
	<div class="ax-title">
		<h2 class="h1 ax-product-title">{l s='Latest Products' mod='aei_categoryslider'}</h2>
	</div>
	<ul id="aeicategory-tabs" class="nav nav-tabs clearfix">
		{$count=0}
		{foreach from=$aeicategorysliderinfos item=aeicategorysliderinfo}
			<li class="nav-item">
				<a href="#tab_{$aeicategorysliderinfo.id}" data-toggle="tab" class="nav-link {if $count == 0}active{/if}">{$aeicategorysliderinfo.name}</a>
			</li>
			{$count= $count+1}
		{/foreach}
	</ul>

	<div class="tab-content">
		{$tabcount=0}
		{foreach from=$aeicategorysliderinfos item=aeicategorysliderinfo}
			<div id="tab_{$aeicategorysliderinfo.id}" class="tab-pane {if $tabcount == 0}active{/if}">
				{if isset($aeicategorysliderinfo.product) && $aeicategorysliderinfo.product}

					{assign var='sliderFor' value=5}
					{assign var='productCount' value=count($aeicategorysliderinfo.product)}
					
					{if isset($aeicategorysliderinfo.cate_id) && $aeicategorysliderinfo.cate_id}
                        {if $aeicategorysliderinfo.id == $aeicategorysliderinfo.cate_id.id_category}
                            <div class="categoryimage">
                                <img src="{$image_url}/{$aeicategorysliderinfo.cate_id.image}" alt="" class="category_img"/>
                            </div>
                        {/if}
                    {/if}
					
					<div class="products row">
						{if $slider == 1 && $productCount >= $sliderFor}
							<div class="product-carousel">
							<ul id="aeicategory{$aeicategorysliderinfo.id}-slider" class="aeicategoryproduct-slider" data-catid="{$aeicategorysliderinfo.id}">
						{else}
							<ul id="aeicategory{$aeicategorysliderinfo.id}" class="product_list grid" data-catid="{$aeicategorysliderinfo.id}">
						{/if}
						
							{foreach from=$aeicategorysliderinfo.product item='product'}
								<li class="{if $slider == 1 && $productCount >= $sliderFor}item{else}product_item col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-3{/if}">
								{include file="catalog/_partials/miniatures/product.tpl" product=$product}
								</li>
							{/foreach}
						</ul>
						
						<div id="aeicategoryproduct-arrows{$aeicategorysliderinfo.id}" class="aeicategoryproduct-arrows arrows"></div>
						
						{if $slider == 1 && $productCount >= $sliderFor}
	  					</div>
	  					{/if}						
					</div>
				{else}
					<div class="alert alert-info">{l s='No Products in current tab at this time.' mod='aei_categoryslider'}</div>
				{/if}
			</div> 
		{$tabcount= $tabcount+1}
		{/foreach}
	</div> 
	</div> 
</div>