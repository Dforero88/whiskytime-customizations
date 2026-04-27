{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<script type="text/javascript">
var token_request= '{$token|escape:'html':'UTF-8'}';
var ets_pnf_module_link = "{$module_link nofilter}";
var ets_pnf_msg_confirm = "{l s='Do you want to delete this item?' mod='pagenotfound' js=1}";
var Click_to_copy_text = "{l s='Click to copy' mod='pagenotfound' js=1}";
var Copied_text = "{l s='Copied' mod='pagenotfound' js=1}";
</script>
{if $pagenotfound_error_message} 
    {$pagenotfound_error_message nofilter}
{/if}
<div class="bootstrap{if isset($is_ps16) && $is_ps16} is_ps16{else} is_ps17{/if}">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="blog_center_content col-lg-12">
                    {$pagenotfound_body_html nofilter}
                    {* <div id="exported_list_fields">
                        <div class="ets-form-group ets-custom-payment-tab-exported_fields" >
                            <label>{l s='Selected fields' mod='pagenotfound'}</label>
                            <ul id="list_fields">
                                {if isset($list_fields) && $list_fields}
                                    {foreach from=$list_fields item='field'}
                                        <li id="fields-{$field.val|escape:'html':'UTF-8'}" class="exported_fields_{$field.class|escape:'html':'UTF-8'}">{$field.name|escape:'html':'UTF-8'}</li>
                                    {/foreach}
                                {/if}
                            </ul>
                        </div>
                    </div> *}
                </div>
            </div>
        </div>
    </div>
</div>
{Module::getInstanceByName('pagenotfound')->displayIframe() nofilter}