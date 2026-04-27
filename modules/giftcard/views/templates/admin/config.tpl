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

{include file='./menu.tpl'}
<div class = "col-lg-10">

    <div class="panel">
        <div class="clearfix"></div>
        <form class="form-horizontal" method="POST" action="{$action_url|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
        <fieldset>
            <legend class="panel-heading"><i class="icon-cogs"></i> {l s='General Settings' mod='giftcard'}</legend>
            <div class="form-group">
                <label class="col-lg-4 control-label">{l s='Gift Voucher Code Prefix' mod='giftcard'}</label>
                <div class="col-lg-5">
                    <input type="text" name="GIFTCARD_VOUCHER_PREFIX" value="{if isset($GIFTCARD_VOUCHER_PREFIX)}{$GIFTCARD_VOUCHER_PREFIX|escape:'htmlall':'UTF-8'}{/if}">
                    <p class="help-block hint-block margin-form">{l s='Prefix will be used in gift voucher code.(space and special characters are not allowed. use underscore only.)' mod='giftcard'}</p>
                </div>
            </div>
        
        <div class="clearfix"></div>
        <div class="form-group">
            <label class="control-label col-lg-4">
                <span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='select order status for gift cards.' mod='giftcard'}">{l s='Approval status for gift cards' mod='giftcard'}</span>
            </label>
            <div class="col-lg-8">
                <div class="{if $ps_version >= 1.6}row{/if}">
                    <div class="col-lg-8">
                        <table class="table table-bordered well">
                            <thead>
                                <tr>
                                    <th class="fixed-width-xs">
                                        <span class="title_box">
                                            <input type="checkbox" onclick="checkDelBoxes(this.form, 'approval_states[]', this.checked)" id="checkme" name="checkme">
                                        </span>
                                    </th>
                                    <th>
                                        <span class="title_box">{l s='Order Status' mod='giftcard'}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            {if isset($states)}
                                {foreach from=$states item=state}
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" id="affiliate_groups_{$state.id_order_state|escape:'htmlall':'UTF-8'}" class="approval_states" name="approval_states[]" {if isset($approval_states) AND in_array($state.id_order_state, $approval_states)}checked="checked"{/if}>
                                    </td>
                                    <td>
                                        <label for="affiliate_groups_{$state.id_order_state|escape:'htmlall':'UTF-8'}">{$state.name|escape:'htmlall':'UTF-8'}</label>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                            </tbody>
                        </table>
                        <p class="help-block hint-block margin-form">{l s='Gift cards will be accessible to customer(s) after validating specified selected order states.' mod='giftcard'}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <label class="control-label col-lg-4">
                <span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" >{l s='Share on social media' mod='giftcard'}</span>
            </label>
            <div class="col-lg-8">
                <div class="{if $ps_version >= 1.6}row{/if}">
                    <div class="col-lg-8">
                        <table class="table table-bordered well">
                            <thead>
                                <tr>
                                    <th class="fixed-width-xs">
                                        <span class="title_box">
                                            <input type="checkbox" onclick="checkDelBoxes(this.form, 'GIFTCARD_SHARE[]', this.checked)" id="checkme" name="checkme">
                                        </span>
                                    </th>
                                    <th>
                                        <span class="title_box">{l s='Order Status' mod='giftcard'}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="checkbox" value="facebook"  class="approval_states" name="GIFTCARD_SHARE[]" {if isset($GIFTCARD_SHARE) && in_array('facebook',$GIFTCARD_SHARE)}checked{/if}>
                                </td>
                                <td>
                                    <label for="SAVEANDSHARECART_SHARE_facebook">{l s='Facebook' mod='giftcard'}</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" value="messenger"  class="approval_states" name="GIFTCARD_SHARE[]" {if isset($GIFTCARD_SHARE) && in_array('messenger',$GIFTCARD_SHARE)}checked{/if}>
                                </td>
                                <td>
                                    <label for="SAVEANDSHARECART_SHARE_messenger">{l s='Mesenger' mod='giftcard'}</label>
                                </td>
                
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" value="skype"  class="approval_states" name="GIFTCARD_SHARE[]" {if isset($GIFTCARD_SHARE) && in_array('skype',$GIFTCARD_SHARE)}checked{/if}>
                                </td>
                                <td>
                                    <label for="SAVEANDSHARECART_SHARE_skype">{l s='Skype' mod='giftcard'}</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" value="twitter"  class="approval_states" name="GIFTCARD_SHARE[]" {if isset($GIFTCARD_SHARE) && in_array('twitter',$GIFTCARD_SHARE)}checked{/if}>
                                </td>
                                <td>
                                    <label for="SAVEANDSHARECART_SHARE_twitter">{l s='Twitter' mod='giftcard'}</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" value="whatsapp"  class="approval_states" name="GIFTCARD_SHARE[]" {if isset($GIFTCARD_SHARE) && in_array('whatsapp',$GIFTCARD_SHARE)}checked{/if}>
                                </td>
                                <td>
                                    <label for="SAVEANDSHARECART_SHARE_whatsapp">{l s='Whatsapp' mod='giftcard'}</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" value="linkedin"  class="approval_states" name="GIFTCARD_SHARE[]" {if isset($GIFTCARD_SHARE) && in_array('linkedin',$GIFTCARD_SHARE)}checked{/if}>
                                </td>
                                <td>
                                    <label for="SAVEANDSHARECART_SHARE_linkedin">{l s='LinkedIn' mod='giftcard'}</label>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p class="help-block hint-block margin-form">{l s='Select social Media options to share gift card, Leave empty to disable.' mod='giftcard'}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="form-group">
            <label class="col-lg-4 control-label">{l s='Enable Gift Card Video Upload' mod='giftcard'}</label>
            <div class="col-lg-5 ">
            
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="GIFT_VIDEO_UPLOAD_ENABLED" id="GIFT_VIDEO_UPLOAD_ENABLED_on" value="1" {if isset($GIFT_VIDEO_UPLOAD_ENABLED) && $GIFT_VIDEO_UPLOAD_ENABLED == 1}checked="checked"{/if} />
                    <label class="t" for="GIFT_VIDEO_UPLOAD_ENABLED_on">
                        {if $ps_version < 1.6}
                            <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled" />
                        {else}
                            {l s='Enabled' mod='giftcard'}
                        {/if}
                    </label>
                    <input type="radio" name="GIFT_VIDEO_UPLOAD_ENABLED" id="GIFT_VIDEO_UPLOAD_ENABLED_off" value="0" {if isset($GIFT_VIDEO_UPLOAD_ENABLED) && $GIFT_VIDEO_UPLOAD_ENABLED == 0}checked="checked"{/if} />
                    <label class="t" for="GIFT_VIDEO_UPLOAD_ENABLED_off">
                        {if $ps_version < 1.6}
                            <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled" />
                        {else}
                            {l s='Disabled' mod='giftcard'}
                        {/if}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
                <p class="help-block hint-block margin-form">{l s='If enabled add' mod='giftcard'} {'{video_link}'} {l s='in the custom email template to include video link in mail.' mod='giftcard'}"</p> {* html content *}
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{l s='Max Video Size Limit (MB)' mod='giftcard'}</label>
            <div class="col-lg-3">
                <div class="input-group">
                    <input type="text" name="GIFT_VIDEO_SIZE_LIMIT" value="{if isset($GIFT_VIDEO_SIZE_LIMIT) }{$GIFT_VIDEO_SIZE_LIMIT|escape:'htmlall':'UTF-8'}{/if}" class="form-control">
                    <span class="input-group-addon">{l s='MB' mod='giftcard'}</span>
                </div>
                <p class="help-block hint-block margin-form">{l s='Enter the maximum file size limit for the uploaded video in MB.' mod='giftcard'}</p>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{l s='Video Expiry (Days)' mod='giftcard'}</label>
            <div class="col-lg-3">
                <div class="input-group">
                    <input type="text" name="GIFT_VIDEO_EXPIRY_DAYS" value="{if isset($GIFT_VIDEO_EXPIRY_DAYS) }{$GIFT_VIDEO_EXPIRY_DAYS|escape:'htmlall':'UTF-8'}{/if}" class="form-control">
                    <span class="input-group-addon">{l s='days' mod='giftcard'}</span>
                </div>
                <p class="help-block hint-block margin-form">{l s='Enter the number of days after which the video will expire, leave empty or add 0 fod a default value of 15 days.' mod='giftcard'}</p>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{l s='Remove abandoned giftcards from last "X" hours' mod='giftcard'}</label>
            <div class="col-lg-3">
                    <div class="input-group">
                        <input type="text" name="GIFTCARD_CRON_HOURS" value="{if isset($GIFTCARD_CRON_HOURS) AND $GIFTCARD_CRON_HOURS}{$GIFTCARD_CRON_HOURS|escape:'htmlall':'UTF-8'}{/if}">
                        <span class="input-group-addon">{l s='hours' mod='giftcard'}</span>
                    </div>
                <p class="help-block hint-block margin-form">{l s='gift cards that have been added to cart but not yet ordered for specific "X" hours, will be marked as abandoned gift cards.' mod='giftcard'}</p>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="form-group">
        <label class="col-lg-4 control-label">{l s='Enable Customization' mod='giftcard'}</label>
        <div class="col-lg-5 ">
        
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="GIFT_CARD_CUSTOMIZATION_ENABLED" id="GIFT_CARD_CUSTOMIZATION_ENABLED_on" value="1" {if isset($GIFT_CARD_CUSTOMIZATION_ENABLED) && $GIFT_CARD_CUSTOMIZATION_ENABLED == 1}checked="checked"{/if} />
                <label class="t" for="GIFT_CARD_CUSTOMIZATION_ENABLED_on">
                    {if $ps_version < 1.6}
                        <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled" />
                    {else}
                        {l s='Enabled' mod='giftcard'}
                    {/if}
                </label>
                <input type="radio" name="GIFT_CARD_CUSTOMIZATION_ENABLED" id="GIFT_CARD_CUSTOMIZATION_ENABLED_off" value="0" {if isset($GIFT_CARD_CUSTOMIZATION_ENABLED) && $GIFT_CARD_CUSTOMIZATION_ENABLED == 0}checked="checked"{/if} />
                <label class="t" for="GIFT_CARD_CUSTOMIZATION_ENABLED_off">
                    {if $ps_version < 1.6}
                        <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled" />
                    {else}
                        {l s='Disabled' mod='giftcard'}
                    {/if}
                </label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block hint-block margin-form">{l s='It will aloow you to customize the templates' mod='giftcard'}</p> {* html content *}
        </div>
    </div>


        <div class="clearfix"></div><br/>
        <div class="form-group">
            <label class="col-lg-4">&nbsp;</label>
            <div class="col-lg-8">
                <p class="alert alert-warning warning">{l s='Define settings and place this URL in crontab or call it manually daily to send scheduled gift cards (Send to Someone) on specified dates (Mandatory).' mod='giftcard'}</p>
                <strong><code>{$giftcard_sendtosomeone|escape:'htmlall':'UTF-8'}</code></strong>
            </div>
        </div>

        <div class="clearfix"></div><br/>
        <div class="form-group">
            <label class="col-lg-4">&nbsp;</label>
            <div class="col-lg-8">
                <p class="alert alert-info info">{l s='Define settings and place this URL in crontab or call it manually daily to flush abandoned gift cards.' mod='giftcard'}</p>
                <strong><code>{$giftcard_cron|escape:'htmlall':'UTF-8'}</code></strong>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{l s='Send voucher expiry mail before a specified interval.' mod='giftcard'}</label>
            <div class="col-lg-5">

            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="GIFT_ALERT_EXPIRED" id="GIFT_ALERT_EXPIRED_on" value="1" {if isset($GIFT_ALERT_EXPIRED) && $GIFT_ALERT_EXPIRED == 1}checked="checked"{/if} />
                <label class="t" for="GIFT_ALERT_EXPIRED_on">
                    {if $ps_version < 1.6}
                        <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled" />
                    {else}
                        {l s='Enabled' mod='giftcard'}
                    {/if}
                </label>
                <input type="radio" name="GIFT_ALERT_EXPIRED" id="GIFT_ALERT_EXPIRED_off" value="0" {if isset($GIFT_ALERT_EXPIRED) && $GIFT_ALERT_EXPIRED == 0}checked="checked"{/if} />
                <label class="t" for="GIFT_ALERT_EXPIRED_off">
                    {if $ps_version < 1.6}
                        <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled" />
                    {else}
                        {l s='Disabled' mod='giftcard'}
                    {/if}
                </label>
                <a class="slide-button btn"></a>
            </span>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{l s='Mail Time' mod='giftcard'}</label>
            <div class="col-lg-3">
                <div class="input-group">
                    <input type="text" name="GIFT_EXPIRY_MAIL_TIME" value="{if isset($GIFT_EXPIRY_MAIL_TIME) }{$GIFT_EXPIRY_MAIL_TIME|escape:'htmlall':'UTF-8'}{/if}" class="form-control">
                    <span class="input-group-addon">{l s='hours' mod='giftcard'}</span>
                </div>
                <p class="help-block hint-block margin-form">{l s='Enter the time in hours for sending the email. Leave empty to set default time of 24 hours' mod='giftcard'}</p>
            </div>
        </div>

        <!-- New Fields End Here -->
        <div class="clearfix"></div><br/>
        <div class="form-group">
            <label class="col-lg-4">&nbsp;</label>
            <div class="col-lg-8">
                <p class="alert alert-info info">{l s='Define settings and place this URL in crontab or call it manually daily to send gift cards expiry email.' mod='giftcard'}</p>
                <strong><code>{$giftcard_expiry_cron|escape:'htmlall':'UTF-8'}</code></strong>
            </div>
        </div>

        <div class="clearfix"></div><br/>
        <div class="form-group">
            <label class="col-lg-4">&nbsp;</label>
            <div class="col-lg-8">
                <p class="alert alert-info info">{l s='Define settings and place this URL in crontab or call it manually daily to delete expired videos and the videos uploaded from product page.' mod='giftcard'}</p>
                <strong><code>{$giftcard_video_cron|escape:'htmlall':'UTF-8'}</code></strong>
            </div>
        </div>

        <div class="clearfix"></div><br/>
        <div class="form-group">
            <label class="col-lg-4">&nbsp;</label>
            <div class="col-lg-8">
                <p class="alert alert-info info">{l s='Use below link to add a menu in your top navigation.' mod='giftcard'}</p>
                <strong><i>{$gifts_controller|escape:'htmlall':'UTF-8'}</i></strong>
            </div>
        </div>

        <div class="clearfix"></div><br/>
        <div class="form-group">
            <label class="col-lg-4">&nbsp;</label>
            <div class="col-lg-8">
                <p class="alert alert-info info">{l s='Add the bellow code in theme to display giftcards at your desired location.' mod='giftcard'}</p>
                <strong><i>{$gifts_hook|escape:'htmlall':'UTF-8'}</i></strong>
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default button pull-right" type="submit" name="updateConfiguration"><span><i class="process-icon-save"></i></span> {l s='Save' mod='giftcard'}</button>
        </div>

        </fieldset>
        </form>

    </div>

</div> 
<div class="clearfix"></div> 
