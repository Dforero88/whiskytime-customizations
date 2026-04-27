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

<link rel="stylesheet" type="text/css" href="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/css/gift_card.css">
<script type="text/javascript">
var giftType    = "{$type|escape:'htmlall':'UTF-8'}";
var pid         = parseInt("{$pid|escape:'htmlall':'UTF-8'}");
</script>
{if count($values) > 0}
    <div id="gift-card-wrapper" class="card card-block" style="display:none">
        <input type="hidden" name="giftcard_type" value="{$type|escape:'htmlall':'UTF-8'}">
        {include file='./gift_radios.tpl'}
        {if $type == 'dropdown'}
            {include file='./dropdown.tpl'}
        {elseif $type == 'range'}
            {include file='./range.tpl'}
        {/if}
    </div>
{/if}
