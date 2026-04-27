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

{block name='content'}

<div class="block card card-block">
    <h3>{l s='Gift Cards' mod='giftcard'}</h3><hr>
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

            <div class="row push-down">
                <div class="filtr-container">
                    {foreach from=$giftProducts item=$gift}
                      <div class="col-xs-6 col-sm-4 col-md-3 filtr-item" data-category="{$gift.tags|escape:'htmlall':'UTF-8'}" data-sort="{$gift.name|escape:'htmlall':'UTF-8'}">
                        <a href="{$gift.link|escape:'htmlall':'UTF-8'}" title="{l s='Detail' mod='giftcard'}">
                            <img class="img-responsive img img-thumbnail" src="{$link->getImageLink($gift.link_rewrite|escape:'htmlall':'UTF-8', $gift.id_image|escape:'htmlall':'UTF-8', 'home_default')}" alt="{$gift.name|escape:'htmlall':'UTF-8'}">
                            <span class="gift-item-desc">{$gift.name|escape:'htmlall':'UTF-8'}</span>
                        </a>
                      </div>
                    {/foreach}
                </div>
            </div>
        {else}
          <div class="alert alert-info info">{l s='No Gift products available.' mod='giftcard'}</div>
        {/if}
    </div>
</div>

{/block}