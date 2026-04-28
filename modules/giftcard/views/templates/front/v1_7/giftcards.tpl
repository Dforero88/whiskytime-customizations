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
    <style>
        #products.giftcards-catalog .giftcard-miniature button,
        #products.giftcards-catalog .giftcard-miniature .wishlist-button,
        #products.giftcards-catalog .giftcard-miniature .wishlist-button-add,
        #products.giftcards-catalog .giftcard-miniature .material-icons {
            display: none !important;
        }
    </style>
    {* <h3>{l s='Gift Cards' mod='giftcard'}</h3><hr> *}
    <div id="js-product-list-header">
        <div class="block-category card card-block">
            {* <h1 class="h1">{l s='Gift Cards' mod='giftcard'}</h1> *}
            <h1 class="h1">{$category_name}</h1>
            {if $giftcard_category_description || $giftcard_category_image}
                <div class="block-category-inner">
                    {if $giftcard_category_description}
                        <div id="category-description" class="text-muted">
                            {$giftcard_category_description nofilter}
                        </div>
                    {/if}
                    {if $giftcard_category_image}
                        <div class="category-cover">
                            <picture>
                                <img src="{$link->getCatImageLink($giftcard_category_link_rewrite, $giftcard_category_image, 'category_default')}" 
                                    alt="{l s='Gift Card Category Image' mod='giftcard'}" 
                                    loading="lazy" width="141" height="180">
                            </picture>
                        </div>
                    {/if}
                </div>
            {/if}
        </div>
    </div>
    
    <hr>
    <div class="gift_products_wrapper col-lg-12">
        {if count($giftProducts) > 0}
            <div class="sort-search">
                <!-- Shuffle & Sort Controls -->
                <div class="row" style="float: left;">
                    <h6>{l s='Sort' mod='giftcard'}:</h6>
                    <select class="sortandshuffle form-control">
                        <option class="fltr-controls shuffle-btn" data-shuffle value="0">{l s='Shuffle' mod='giftcard'}</option>
                        <option class="fltr-controls sort-btn active" data-sortAsc value="1">{l s='Asc' mod='giftcard'}</option>
                        <option class="fltr-controls sort-btn" data-sortDesc value="2">{l s='Desc' mod='giftcard'}</option>
                    </select>
                </div>
                <!-- Search -->
                <div id="gift-product-search" class="row gift-search-row">
                    {l s='Search' mod='giftcard'}:
                    <input type="text" class="fltr-controls filtr-search form-control" name="filtr-search" data-search>
                </div>
            </div><div class="clearfix"></div>

            <div class="row">
                <h6>{l s='Filters Tags' mod='giftcard'}</h6>
                <div class="form-group">
                    <ul class="gift-filter tags">
                      <li class="tag fltr-controls active" data-filter="all">{l s='All' mod='giftcard'}</li>
                        {if count($filterTags) > 0}
                            {foreach from=$filterTags item=filter}
                            <li class="tag fltr-controls" data-filter="{$filter|escape:'htmlall':'UTF-8'}">{$filter|escape:'htmlall':'UTF-8'}</li>
                            {/foreach}
                        {/if}
                    </ul>
                </div>
            </div><hr>

            <section id="products" class="giftcards-catalog push-down">
                <div id="js-product-list">
                    <div class="products row filtr-container">
                        {foreach from=$giftProducts item=$gift}
                            <article
                                class="product-miniature js-product-miniature filtr-item giftcard-miniature"
                                data-id-product="{$gift.id_product|intval}"
                                data-category="{$gift.tags|escape:'htmlall':'UTF-8'}"
                                data-sort="{$gift.name|escape:'htmlall':'UTF-8'}"
                            >
                                <div class="thumbnail-container">
                                    <div class="product-image-block">
                                        <a href="{$gift.link|escape:'htmlall':'UTF-8'}" class="thumbnail product-thumbnail" title="{l s='Detail' mod='giftcard'}">
                                            <span class="main_image">
                                                <img
                                                    src="{$link->getImageLink($gift.link_rewrite|escape:'htmlall':'UTF-8', $gift.id_image|escape:'htmlall':'UTF-8', 'home_default')}"
                                                    alt="{$gift.name|escape:'htmlall':'UTF-8'}"
                                                    loading="lazy"
                                                >
                                            </span>
                                        </a>
                                    </div>

                                    <div class="product-description">
                                        <h2 class="h3 product-title">
                                            <a href="{$gift.link|escape:'htmlall':'UTF-8'}" title="{l s='Detail' mod='giftcard'}">
                                                {$gift.name|truncate:30:'...'}
                                            </a>
                                        </h2>
                                        {if !empty($gift.giftcard_range)}
                                            <div class="product-price-and-shipping">
                                                <span class="price">{$gift.giftcard_range|escape:'htmlall':'UTF-8'}</span>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </article>
                        {/foreach}
                    </div>
                </div>
            </section>
        {else}
          <div class="alert alert-info info">{l s='No Gift products available.' mod='giftcard'}</div>
        {/if}
    </div>
</div>

{/block}
