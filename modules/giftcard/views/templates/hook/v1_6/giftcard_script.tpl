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

<div id="gift_product_{$id_gift_product|escape:'htmlall':'UTF-8'}"></div>
<script type="text/javascript">
$('document').ready( function() {
    var giftProductContainer = "#gift_product_{$id_gift_product|escape:'htmlall':'UTF-8'}";
    $(giftProductContainer).closest('.product-container').find('.content_price').remove();
    $(giftProductContainer).closest('.product-container').find('.button-container').remove();
    $(giftProductContainer).closest('.product-container').find('.quick-view').remove();
});
</script>