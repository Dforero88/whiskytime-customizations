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

<p>

    <div class="print_home">
        <input id="print-home" type="radio" name="gift_order_type" value="home">
        <label for="print-home">{l s='Print at Home' mod='giftcard'}</label>
    </div>
    <div class="send_to_friend">
        <input id="send_someone" type="radio" name="gift_order_type" value="sendsomeone">
        <label for="send_someone">{l s='Send to Friend' mod='giftcard'}</label>
    </div>
    {if !empty($GIFTCARD_SHARE)}
        <a id="button_share_cart" href="javascript:void(0);" data="{$product_url|escape:'htmlall':'UTF-8'}" data-toggle="modal" data-target="#giftCardShareModal" class="btn btn-primary button share-button-toggle mr-1 mb-1 mt-1" title="Share Cart" rel="nofollow">
           Share 
        <i class="material-icons">share</i>
        </a>
    {/if}
    <div id="giftcard_send_to_friend" style="display: none;">
        <hr>
        <div class="form-group">
            <label class="label gc_required_label">{l s='Reciptent Full Name' mod='giftcard'}</label>
            <input class="form-control gc_required_fields" type="text" name="gift_vars[reciptent]">
            <p class="small gc_required">* {l s='required' mod='giftcard'}</p>
        </div>
        <div class="form-group">
            <label class="label gc_required_label">{l s='Reciptent Email' mod='giftcard'}</label>
            <input class="form-control gc_required_fields" type="email" name="gift_vars[email]">
            <p class="small gc_required">* {l s='required' mod='giftcard'}</p>
        </div>
        <div class="form-group">
            <div class="specific_date_check">
                <label class="label" for="specific_date_check">
                    <input id="specific_date_check" type="checkbox" name="specific_date_check" value="1">
                    {l s='Send on a specific date' mod='giftcard'}
                </label>
            </div>
            <div id="specific-date-wrapper" style="display:none;">
                <label class="label gc_required_label">{l s='Select Date' mod='giftcard'}</label>
                <input id="specific-date-selector" class="form-control specific_date_selector" type="text" name="gift_vars[specific_date]">
                <p class="small gc_required">* {l s='required' mod='giftcard'}</p>
            </div>
        </div>
        {if isset($templates) AND $templates}
            <div class="form-group template-wrapper">
                <label class="label">{l s='Email Template' mod='giftcard'}</label>
                <div class="row">
                    <div class="col-md-9">
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
        <div class="form-group">
            <label class="label">{l s='Message (optional)' mod='giftcard'}</label>
            <textarea class="form-control" name="gift_vars[message]"></textarea>
        </div>

        <div class="form-group">
            {include file='../video.tpl'}
        </div>

    </div>
</p>


<!-- Modal Structure -->
<div class="modal fade" id="giftCardShareModal" tabindex="-1" role="dialog" aria-labelledby="giftCardShareModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="giftCardShareModalLabel">{l s='Share Your Gift Card' mod='giftcard'}</h5>
            </div>
            <div class="modal-body text-center">
                <div class="share-buttons">
                    {if !empty($GIFTCARD_SHARE) && in_array('facebook', $GIFTCARD_SHARE)}
                        <a type="button" class="btn btn-default btn-facebook" target="_blank">
                            <span class="fa fa-facebook"></span>{l s='Facebook' mod='giftcard'}
                        </a>
                    {/if}
                    {if !empty($GIFTCARD_SHARE) && in_array('messenger', $GIFTCARD_SHARE)}
                        <a type="button" class="btn btn-default btn-messenger" target="_blank">
                            <span class="fa fa-facebook-messenger"></span>{l s='Messenger' mod='giftcard'}
                        </a>
                    {/if}
                    {if !empty($GIFTCARD_SHARE) && in_array('skype', $GIFTCARD_SHARE)}
                        <a type="button" class="btn btn-default btn-skype" target="_blank">
                            <span class="fa fa-skype"></span>{l s='Skype' mod='giftcard'}
                        </a>
                    {/if}
                    {if !empty($GIFTCARD_SHARE) && in_array('twitter', $GIFTCARD_SHARE)}
                        <a type="button" class="btn btn-default btn-twitter" target="_blank">
                            <span class="fa fa-twitter"></span>{l s='Twitter' mod='giftcard'}
                        </a>
                    {/if}
                    {if !empty($GIFTCARD_SHARE) && in_array('whatsapp', $GIFTCARD_SHARE)}
                        <a type="button" class="btn btn-default btn-whatsapp" target="_blank">
                            <span class="fa fa-whatsapp"></span>{l s='Whatsapp' mod='giftcard'}
                        </a>
                    {/if}
                    {if !empty($GIFTCARD_SHARE) && in_array('linkedin', $GIFTCARD_SHARE)}
                        <a type="button" class="btn btn-default btn-linkedin" target="_blank">
                            <span class="fa fa-linkedin"></span>{l s='LinkedIn' mod='giftcard'}
                        </a>
                    {/if}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='giftcard'}</button>
            </div>
        </div>
    </div>
</div>
