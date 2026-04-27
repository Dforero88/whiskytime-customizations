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

<div>
    <div class="print_home">
        <input id="print-home" type="radio" name="gift_order_type" value="home">
        <label for="print-home">{l s='To Myself' mod='giftcard'}</label>
    </div>
    <div class="send_to_friend">
        <input id="send_someone" type="radio" name="gift_order_type" value="sendsomeone">
        <label for="send_someone">{l s='Send to Friend' mod='giftcard'}</label>
    </div>

    <div id="giftcard_send_to_friend" style="display: none;">
        <hr>
        <p>
            <label class="required gc_required_label">{l s='Reciptent Full Name' mod='giftcard'}</label>
            <input class="form-control gc_required_fields" type="text" name="gift_vars[reciptent]">
            <span class="small gc_required">* {l s='required' mod='giftcard'}</span>
        </p>
        <p>
            <label class="required gc_required_label">{l s='Reciptent Email' mod='giftcard'}</label>
            <input class="form-control gc_required_fields" type="text" name="gift_vars[email]">
            <span class="small gc_required">* {l s='required' mod='giftcard'}</span>
        </p>
        <p class="form-group">
            <div class="specific_date_check">
                <label for="specific_date_check">
                    <input id="specific_date_check" type="checkbox" name="specific_date_check" value="1">
                    {l s='Send on a specific date' mod='giftcard'}
                </label>
            </div>
            <div id="specific-date-wrapper" style="display:none;">
                <label class="gc_required_label">{l s='Select Date' mod='giftcard'}</label>
                <input id="specific-date-selector" class="form-control specific_date_selector" type="text" name="gift_vars[specific_date]">
                <p class="small gc_required">* {l s='required' mod='giftcard'}</p>
            </div>
        </p>
        {if isset($templates) AND $templates}
            <div class="form-group template-wrapper">
                <label>{l s='Email Template' mod='giftcard'}</label>
                <div class="row">
                    <div class="col-md-8">
                        <select id="templateDropdown" class="form-control" name="email_template">
                            {foreach from=$templates item=template}
                                <option
                                value="{$template.id_gift_card_template|escape:'htmlall':'UTF-8'}"
                                data-imagesrc="{$template.thumb|escape:'htmlall':'UTF-8'}"
                                data-content="{$template.content|base64_encode|escape:'htmlall':'UTF-8'}">
                                {$template.template_name|escape:'htmlall':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a id="template-preview" class="btn btn-info" href="javascript:void(0);">{l s='Preview' mod='giftcard'}</a>
                    </div>
                    <div id="gift-template-modal"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        {/if}
        <p>
            <label>{l s='Message (optional)' mod='giftcard'}</label>
            <textarea class="form-control" name="gift_vars[message]"></textarea>
        </p>

        <div class="form-group">
            {include file='../video.tpl'}
        </div>
    </div>
</div>
