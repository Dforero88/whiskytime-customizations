{*
 * Events Manager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2021 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   eventsmanager
*}
{literal}
  <style>
    .mce-flow-layout-item.mce-last {
      display:none!important;
    }
  </style>
{/literal}
<div class="panel col-lg-10" style="margin-top: -5px;">
   <div class="panel" id="fieldset_0">
      <div class="panel-heading">
          <img src="../img/admin/add.gif" alt="FME Events">{l s='Add Contact Detail' mod='eventsmanager'}
      </div>
      <div class="form-wrapper">


         <div class="form-group">
            <label class="control-label col-lg-3">
           <label>{l s='Contact Person' mod='eventsmanager'}: </label>
            </label>
            <div class="col-lg-9">
            <input type="text" size="60" class="" value="{if !empty($contact_name)}{$contact_name|escape:'htmlall':'UTF-8'}{/if}" id="contact_name" name="contact_name" required="required">
              
            </div>
         </div>

         <div class="form-group">
            <label class="control-label col-lg-3">
            <label>{l s='Phone' mod='eventsmanager'}: </label>
            </label>
            <div class="col-lg-9">
            <input type="text" size="60" class="" value="{if !empty($contact_phone)}{$contact_phone|escape:'htmlall':'UTF-8'}{/if}" id="contact_phone" name="contact_phone">
              
            </div>
         </div>

         <div class="form-group">
            <label class="control-label col-lg-3">
            <label>{l s='Fax' mod='eventsmanager'}: </label>
            </label>
            <div class="col-lg-9">
            <input type="text" size="60" class="" value="{if !empty($contact_fax)}{$contact_fax|escape:'htmlall':'UTF-8'}{/if}" id="contact_fax" name="contact_fax">
              
            </div>
         </div>

         <div class="form-group">
            <label class="control-label col-lg-3">
             <label>{l s='Email' mod='eventsmanager'}: </label>
            </label>
            <div class="col-lg-9">
           <input type="text" size="60" class="" value="{if !empty($contact_email)}{$contact_email|escape:'htmlall':'UTF-8'}{/if}" id="contact_email" name="contact_email">
              
            </div>
         </div>

         <div class="form-group">
            <label class="control-label col-lg-3">
             <label>{l s='Address' mod='eventsmanager'}: </label>
            </label>
            <div class="col-lg-9">
           <textarea class="autoload_rte rte default-editor" id="contact_address" name="contact_address" rows="10" cols="93">{if !empty($contact_address)}{$contact_address|escape:'htmlall':'UTF-8'}{/if}</textarea>
              
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel-footer">
    {if isset($event_id)}
      <button id="fme_events_form_submit_btn" class="btn btn-default pull-right" name="submitAddfme_events" value="Save" type="submit"><i class="process-icon-save"></i>{l s='Update' mod='eventsmanager'} </button>
      {else}
       <a class="btn btn-default pull-right btn-lg" id="eventsmanager_link_gallery" href="javascript:displayPrivateTab('gallery');">{l s='NEXT ' mod='eventsmanager'}<i class="icon-circle-arrow-right"></i></a>
    {/if}
   </div>
</div>

