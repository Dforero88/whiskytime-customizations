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

<div class="block card card-block">
    {if isset($smarty.get.msg) AND $smarty.get.msg == 1}
        <div class="success conf alert alert-success">
            <p>{l s='Your gift card has been sent successfully.' mod='giftcard'}</p>
        </div>
    {/if}


    <h4 class="block title_block giftcards_title">{l s='My Gift Cards' mod='giftcard'}</h4>
    <div id="block-giftcard" class="block-center table-responsive">
        {if count($coupens) > 0}

        <table class="table table-bordered {if $ps_version < 1.6}std{/if}" id="order-list">
                <thead>
                    <tr>
                        <th class="first_item">{l s='Card' mod='giftcard'}</th>
                        <th class="item">{l s='Code' mod='giftcard'}</th>
                        <th class="item">{l s='Qty' mod='giftcard'}</th>
                        <th class="item">{l s='Value' mod='giftcard'}</th>
                        <th class="item">{l s='Expire Date' mod='giftcard'}</th>
                        {* <th class="item">&nbsp;</th>
                        *}
                        <th class="item">{l s='Actions' mod='giftcard'}</th>

                    </tr>
                </thead>
            <tbody class="giftcard-list">
            
                {foreach from=$coupens item=card}
                        <tr id="gift-card-{$card.id_cart|escape:'htmlall':'UTF-8'}-{$card.id_product|escape:'htmlall':'UTF-8'}" class="my-gift-voucher" style="text-align:center">
                            <td>
                                <center>
                                    <span class="card_image" img-src="{$link->getImageLink($card.link_rewrite, $card.id_image, 'home_default')|escape:'htmlall':'UTF-8'}">
                                        <img style="height:50px;" src="{$link->getImageLink($card.link_rewrite, $card.id_image, 'small_default')|escape:'htmlall':'UTF-8'}">
                                    </span>
                                    <p class="card_name">{$card.name|escape:'htmlall':'UTF-8'}<p>
                                </center>
                            </td>
                            <td>
                                {$card.code|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>
                                <span class="card_qty">{$card.quantity|escape:'htmlall':'UTF-8'}</span>
                            </td>
                            <td>
                                <span class="card_price">
                                    {if isset($card.reduction_percent) AND $card.reduction_percent != 0}
                                        {$card.reduction_percent|escape:'htmlall':'UTF-8'}{l s='%' mod='giftcard'}
                                    {elseif isset($card.reduction_amount) AND $card.reduction_amount != 0}
                                        {if _PS_VERSION_ == '9.0.0'}
                                            {* {$card.reduction_amount|escape:'htmlall':'UTF-8'} *}
                                            {$card.reduction_amount}
                                            
                                        {else}
                                            {Tools::displayPrice($card.reduction_amount)|escape:'htmlall':'UTF-8'}
                                        {/if}
                                    {else}
                                        {l s='0' mod='giftcard'}
                                    {/if}
                                </span>
                            </td>
                            <td>
                                <span class="card_date">{$card.date_to|escape:'htmlall':'UTF-8'}</span>
                            </td>
                            {*
                            <td class="send_someone">
                                <div class='action_buttons'>
                                    <a href="javascript:;" class="show-form btn btn-primary">
                                        {if $ps_version <= 1.6}<i class="icon-chevron-down"></i>{else}<i class="material-icons">keyboard_arrow_down</i>{/if}
                                        {l s='Send to someone' mod='giftcard'}
                                    </a>
                                </div>
                            </td>
                            *}
                            <td class="download_pdf">
                                {hook h='displayMyGiftCardsButtons' id_cart_rule=$card.id_cart_rule}
                            </td>
                        </tr>
                        <tr class="form-row invisible_row">
                            <td class="send_someone" colspan="6">
                                <div class="send_someone_form">
                                    <form action="{$link->getModuleLink('giftcard','mygiftcards')|escape:'htmlall':'UTF-8'}" method="post" name="giftcard_send_to_friend" id="from_giftcard">
                                        <div class="form-group col-lg-12">
                                            <label class="control-label col-lg-3">{l s='To' mod='giftcard'}</label>
                                            <div class="col-lg-7">
                                                <input class="form-control" type="text" name="friend_name" required placeholder="John Doe" />
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <label class="control-label col-lg-3">{l s='Email' mod='giftcard'}</label>
                                            <div class="col-lg-7">
                                                <input class="form-control" type="text" name="friend_email" required placeholder="demo@demo.com" />
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        {if isset($templates) AND $templates}
                                            <div class="form-group col-lg-12">
                                                <label class="control-label col-lg-3">{l s='Email Template' mod='giftcard'}</label>
                                                <div class="col-lg-5">
                                                    <select class="email_template form-control" name="email_template">
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
                                                <div class="col-md-2">
                                                    <a class="btn btn-info preview_template"
                                                    href="javascript:void(0);"
                                                    data-cart="{$card.id_cart|escape:'htmlall':'UTF-8'}"
                                                    data-pid="{$card.id_product|escape:'htmlall':'UTF-8'}">
                                                        {l s='Preview' mod='giftcard'}
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        {/if}
                                        <div class="form-group col-lg-12">
                                            <label class="control-label col-lg-3">{l s='Message' mod='giftcard'}</label>
                                            <div class="col-lg-7">
                                                <textarea class="form-control" name="friend_message"/></textarea>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        {if $video_enabled}
                                            <div class="form-group col-lg-12">
                                                <label class="control-label col-lg-3">{l s='Video Attachment' mod='giftcard'}</label>
                                                <div class="col-lg-7">
                                                    <a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-id= '{$card.id_cart_rule|escape:'htmlall':'UTF-8'}' data-video-id='{$card.id_video|escape:'htmlall':'UTF-8'}' data-target="#videoAttachmentModal">
                                                        {l s='Add Video Attachment' mod='giftcard'}
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group col-lg-12" style='display:{if isset($card.id_video) && $card.id_video}block{else}none{/if} '>
                                                    <label class="control-label col-lg-3"></label>
                                                    <div class="col-lg-7">
                                                        <label>
                                                            <input type="checkbox" name="send_video" {if isset($card.id_video) && $card.id_video}checked{/if} value="{if isset($card.id_video) && $card.id_video}1{else}0{/if}" />
                                                                {l s='Video for this giftcards has been uploaded. Do you want to send it?' mod='giftcard'} 
                                                        </label>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="mt-3 p-1 video_alert_message"></div>
                                            </div>
                                            
                                        {/if}
                                        <div class="form-group col-lg-10">
                                            <input class="button btn btn-primary float-xs-right" type="submit" name="send_giftcard" value="{l s='Send' mod='giftcard'}"/>
                                            <div class="clearfix"></div>
                                        </div>
                                       <input type="hidden" name="id_giftcard_video" value="{if isset($card.id_video)}{$controller_display|escape:'htmlall':'UTF-8'}?id_media={$card.id_video|base64_encode}{/if}"/>
                                        <input type="hidden" name="id_gift_product" value="{$card.id_product|escape:'htmlall':'UTF-8'}"/>
                                        <input type="hidden" name="giftcard_name" value="{$card.name|escape:'htmlall':'UTF-8'}"/>
                                        <input type="hidden" name="vcode" value="{$card.code|escape:'htmlall':'UTF-8'}"/>
                                        <input type="hidden" name="id_coupen" value="{$card.id_cart_rule|escape:'htmlall':'UTF-8'}"/>
                                        <input type="hidden" name="expire_date" value="{$card.date_to|escape:'htmlall':'UTF-8'}"/>
                                    </form>
                                </div>
                            </td>
                        </tr>
                {/foreach}
            </tbody>
        </table>
        <div id="gift-template-modal-account"></div>
        {else}
            <div class="alert alert-warning warning">
                <center>{l s='You did not purchased any Gift card yet.' mod='giftcard'}</center>
            </div>
        {/if}
    </div>

    
{block name='page_footer'}
  {block name='my_account_links'}
    {include file='customer/_partials/my-account-links.tpl'}
  {/block}
{/block}
</div>

<div class="modal fade" id="videoAttachmentModal" tabindex="-1" role="dialog" aria-labelledby="videoAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="videoAttachmentModalLabel">{l s='Add Video Attachment' mod='giftcard'}</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="videoAttachmentId" name="video_attachment_id">
                <input type="hidden" id="giftCardVideoId" name="gift_card_video_id">
                <div class="form-group">
                    <label>{l s='Select Video Type' mod='giftcard'}</label>
                    <select id="videoType" class="form-control">
                        <option value="upload">{l s='Upload Video' mod='giftcard'}</option>
                        <option value="embed">{l s='Embed Video Link' mod='giftcard'}</option>
                    </select>
                </div>

                <div id="uploadVideoSection">
                    <div class="form-group">
                        <label>{l s='Upload Video File' mod='giftcard'}</label>
                        <input type="file" name="video_file" class="form-control" accept="video/*">
                        <small>{l s='Accepted formats: MP4, MOV.' mod='giftcard'} {if $video_limit}{l s='Video size limit is : ' mod='giftcard'} {$video_limit|escape:'htmlall':'UTF-8'} {l s='MB' mod='giftcard'}{/if}</small>
                        {if $video_expiry}
                            <br><small>{l s='Uploaded video will become Inaccessible after' mod='giftcard'} {$video_expiry|escape:'htmlall':'UTF-8'} {l s='Days' mod='giftcard'}</small>
                        {/if}  
                    </div>
                </div>

                <div id="embedVideoSection" style="display: none;">
                    <div class="form-group">
                        <label>{l s='Embed Video Link' mod='giftcard'}</label>
                        <input type="url" name="video_link" class="form-control" placeholder="https://example.com">
                        <small>{l s='You can add a YouTube or Vimeo link.' mod='giftcard'}</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='giftcard'}</button>
                <button type="button" class="btn btn-primary saveVideoAttachment" id="saveVideoAttachment">{l s='Save' mod='giftcard'}</button>
            </div>
        </div>
    </div>
</div>

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
