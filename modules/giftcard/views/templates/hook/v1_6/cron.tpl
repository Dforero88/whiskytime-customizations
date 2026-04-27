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

{if isset($result) AND $result}
<div class="box">
   <p class="alert alert-info info">
        {if $request == 1}
            {l s='Flushed Giftcards' mod='giftcard'}:{$result.deleted|escape:'htmlall':'UTF-8'}
            <br>
            {l s='Skipped' mod='giftcard'}:{$result.skip|escape:'htmlall':'UTF-8'}
        {elseif $request == 2}
            {l s='Cron job performed successfully.' mod='giftcard'}
        {/if}
    </p>
    <hr>
</div>
{/if}
{include file="$tpl_dir./errors.tpl"}

