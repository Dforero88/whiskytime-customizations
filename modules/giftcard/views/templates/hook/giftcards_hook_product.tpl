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

    <br />
<div class="block card card-block">
    <h3>{l s='Gift Cards' mod='giftcard'}</h3><hr>
    <div class="gift_products_wrapper col-lg-12">
        {if count($giftProducts) > 0}
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