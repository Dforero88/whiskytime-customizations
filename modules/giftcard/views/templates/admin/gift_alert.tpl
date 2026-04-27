{**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author FMM Modules
* @copyright 2021 FMM Modules
* @license FMM Modules
*}

<div class="cart_summary">
    <table class="table" id="orderProducts" cellspacing="0" cellpadding="0" style="background: none repeat scroll 0 0 #EAEBEC;
        border: 1px solid #CCCCCC;
        border-radius: 3px;
        box-shadow: 0 1px 2px #D1D1D1;
        color: #444444;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 12px;
        margin: 20px;
        text-shadow: 1px 1px 0 #FFFFFF;">
        <thead>
            <tr>
                <th class="center" style="background: -moz-linear-gradient(center top , #EDEDED, #EBEBEB) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-top: 1px solid #FAFAFA;
                padding: 21px 25px 22px;"><span class="title_box">{l s='Gift Card' mod='giftcard'}</span></th>
                <th class="text-right fixed-width-md" style="background: -moz-linear-gradient(center top , #EDEDED, #EBEBEB) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-top: 1px solid #FAFAFA;
                padding: 21px 25px 22px;"><span class="title_box">{l s='Coupon code' mod='giftcard'}</span></th>
                <th class="text-center fixed-width-md" style="background: -moz-linear-gradient(center top , #EDEDED, #EBEBEB) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-top: 1px solid #FAFAFA;
                padding: 21px 25px 22px;"><span class="title_box">{l s='Quantity' mod='giftcard'}</span></th>
                <th class="text-center fixed-width-md" style="background: -moz-linear-gradient(center top , #EDEDED, #EBEBEB) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-top: 1px solid #FAFAFA;
                padding: 21px 25px 22px;"><span class="title_box">{l s='Expire date' mod='giftcard'}</span></th>
            </tr>
        </thead>
        <tbody>';
            {foreach from=$rules item=rule}
            {assign var=cart_rule value=Gift::getCartsRuleById($rule, $id_lang)}
            <tr>
                <td class="center" style="background: -moz-linear-gradient(center top , #FBFBFB, #FAFAFA) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-left: 1px solid #E0E0E0;
                border-top: 1px solid #FFFFFF;
                padding: 18px;">{$cart_rule.name|escape:'htmlall':'UTF-8'}</td>
                <td class="text-right" style="background: -moz-linear-gradient(center top , #FBFBFB, #FAFAFA) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-left: 1px solid #E0E0E0;
                border-top: 1px solid #FFFFFF;
                padding: 18px;">{$cart_rule.code|escape:'htmlall':'UTF-8'}</td>
                <td class="text-right" style="background: -moz-linear-gradient(center top , #FBFBFB, #FAFAFA) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-left: 1px solid #E0E0E0;
                border-top: 1px solid #FFFFFF;
                padding: 18px;">{$cart_rule.quantity|escape:'htmlall':'UTF-8'}</td>
                <td class="text-center" style="background: -moz-linear-gradient(center top , #FBFBFB, #FAFAFA) repeat scroll 0 0 rgba(0, 0, 0, 0);
                border-bottom: 1px solid #E0E0E0;
                border-left: 1px solid #E0E0E0;
                border-top: 1px solid #FFFFFF;
                padding: 18px;">{$cart_rule.date_to|escape:'htmlall':'UTF-8'}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    <br />
</div>