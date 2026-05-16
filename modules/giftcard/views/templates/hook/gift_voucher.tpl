{**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2024 FMM Modules
*  @license   FMM Modules
*}
 <div id="gift_card_voucher" class="product-row row">
   <div class="col-md-4 left-column">

     <div class="gift_card mt-2" data-role="message-card">
       <div class="gift_card-header">
         <div class="gift_row">
           <div class="col-md-12">
             <h2 class="gift_card-header-title">
               {l s='Gift Card Voucher' mod='giftcard' }
             </h2>
           </div>
         </div>
       </div>
      {foreach from=$vouchers item=voucher}
       <div class="gift_card-body d-print-none">
        <p> <strong>{l s='Gift Name :' mod='giftcard' } </strong>
          {$voucher['name']|escape:'htmlall':'UTF-8'}
        </p>
        <p> <strong>{l s='Customer ID :' mod='giftcard' } </strong>
          {$voucher['id_customer']|escape:'htmlall':'UTF-8'}
        </p>
        <p> <strong>{l s='Quantity :' mod='giftcard' } </strong>
          {$voucher['quantity']|escape:'htmlall':'UTF-8'}
        </p>
        <p> <strong>{l s='Code :' mod='giftcard' } </strong>
          {$voucher['code']|escape:'htmlall':'UTF-8'}
        </p>
        <p> <strong>{l s='Partial Use :' mod='giftcard' } </strong>
         {if (isset($voucher['partial_use']) && $voucher['partial_use'] == '1')} {l s='Yes' mod='giftcard' } {else} {l s='No' mod='giftcard' }{/if}
        </p>
        <p> <strong>{l s='Free Shipping :' mod='giftcard' } </strong>
          {if (isset($voucher['free_shipping']) && $voucher['free_shipping'] == '1')} {l s='Yes' mod='giftcard' } {else} {l s='No' mod='giftcard' }{/if}
        </p>
        {if (int)$voucher['reduction_percent']}
          <p> <strong>{l s='Reduction Percent' mod='giftcard' } </strong>
           {$voucher['reduction_percent']|escape:'htmlall':'UTF-8'}%
          </p>
        {/if}
        {if (int)$voucher['reduction_amount']}
          <p> <strong>{l s='Reduction Amount' mod='giftcard' } </strong>
          {if Tools::version_compare(_PS_VERSION_, '9.0.0', '>=')}
              {* {$voucher['reduction_amount']|escape:'htmlall':'UTF-8'} *}
              {$voucher['reduction_amount']}
          {else}
              {Tools::displayPrice($voucher['reduction_amount'])|escape:'htmlall':'UTF-8'}
          {/if}
           {* {Tools::displayPrice($voucher['reduction_amount']|escape:'htmlall':'UTF-8')} *}
          </p>
        {/if}
        <p> <strong>{l s='Active :' mod='giftcard' } </strong>
          {if (isset($voucher['active']) && $voucher['active'] == '1')} {l s='Yes' mod='giftcard' } {else} {l s='No' mod='giftcard' }{/if}
        </p>
        {if $voucher['gift_type'] == 'sendsomeone'}
        <p><strong> {l s='Friend Name' mod='giftcard' }</strong>
          {$voucher['friend_name']|escape:'htmlall':'UTF-8'}
        </p>
        <p><strong> {l s='Friend Email' mod='giftcard' }</strong> 
          {$voucher['friend_email']|escape:'htmlall':'UTF-8'}
        </p>
        <p><strong> {l s='Gift Message' mod='giftcard' }</strong> 
          {$voucher['gift_message']|escape:'htmlall':'UTF-8'}
        </p>
        <p><strong> {l s='Specific Date' mod='giftcard' }</strong> 
          {$voucher['specific_date']|escape:'htmlall':'UTF-8'}
        </p>
        {/if}
        <p><a href="{$link->getAdminLink('AdminCartRules', true, [], ['id_cart_rule' => {$voucher['id_cart_rule']|escape:'htmlall':'UTF-8'}, 'updatecart_rule' => 1])|escape:'html':'UTF-8'}">{l s='View Voucher Details' mod='giftcard' }</a></p>
       </div>
      {/foreach}
     </div>

   </div>

</div>
<style type="text/css">

#gift_card_voucher .gift_card{
  position: relative;
  display: block;
  margin-bottom: .625rem;
  background-color: #fff;
  border: 1px solid #dbe6e9;
  border-radius: 5px;
  -webkit-box-shadow: 0 0 4px 0 rgba(0,0,0,.06);
  box-shadow: 0 0 4px 0 rgba(0,0,0,.06);
}
#gift_card_voucher .mt-2 {
  margin-top: .625rem !important;
}
#gift_card_voucher .gift_card-header {
  padding: .625rem;
  background-color: #fafbfc;
  border-bottom: 1px solid #dbe6e9;
}
#gift_card_voucher .gift_row{
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  margin-right: -.9375rem;
  margin-left: -.9375rem;
}
#gift_card_voucher .gift_card-header-title {
  font-weight: 600;
  line-height: 1.5rem;
  padding-left: 1rem;
}
#gift_card_voucher .gift_card-body {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  min-height: 1px;
  padding: .625rem;
  border-bottom: 1px solid #dbe6e9;
}
</style>
