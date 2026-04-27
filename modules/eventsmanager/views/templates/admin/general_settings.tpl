{*
* PrivateShop
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FME Modules
*  @copyright 2021 FME Modules All right reserved
*  @license   FME Modules
*  @category  FMM Modules
*  @package   PrivateShop
*}
{literal}
    <style type="text/css">
    {if $version < '1.7.0.0' }
        .chosen-container {display : contents!important;}
    {/if}
    .frame_styled { display: inline-block; padding: 1.5% !important; border: 1px solid #C7D6DB; background: #F5F8F9; text-align: center;
    margin-right: 2%; border-radius: 4px; position: relative; cursor: pointer; max-height: 90px; overflow: hidden}
    .active_frame { background: #c5f7ca; border-color: #72C279}
    .frame_styled input[type="radio"] { height: 90px; left: 0; position: absolute; top: -10px; width: 100%; opacity: 0; z-index: 99;}
    .pvt_icon { text-align: center; display: block; clear: both;}
    .pvt_icon::before { display: inline-block;
    font-family: FontAwesome; color: #2EACCE;
    font-size: 32px;
    font-size-adjust: none;
    font-stretch: normal;
    font-style: normal;
    font-weight: normal;
    line-height: 1;
    text-rendering: auto;}
    .ac_ico::before {content: "\f1fb";}
    .ai_ico::before {content: "\f03e";}
    .ayv_ico::before {content: "\f16a";}
    .ws_ico::before {content: "\f023";}
    .os_ico::before {content: "\f13e";}
    .mod_theme_ico::before {content: "\f0d0";}
    .def_theme_ico::before {content: "\f0c5";}
    </style>
{/literal}

<div class="panel col-lg-10" style="margin-top: -5px;">
   <div class="panel" id="fieldset_0">
      <div class="panel-heading">
         <img src="../img/admin/add.gif" alt="FME Events">{l s='FME Events' mod='eventsmanager'}
      </div>
      <div class="form-wrapper">

        <div class="form-group">
          <label class="control-label col-lg-3">
            {l s='Status:' mod='eventsmanager'}
            </label>
           <span class="switch prestashop-switch fixed-width-lg col-lg-9 col-xs-10">
                <input type="radio" name="event_status" id="event_status_on" value="1" {if isset($event_status) AND $event_status == 1}checked="checked"{/if}/>
                <label class="t" for="event_status_on">
                    {if $version < 1.6}
                        <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='eventsmanager'}" title="{l s='Enabled' mod='eventsmanager'}" />
                    {else}
                        {l s='Yes' mod='eventsmanager'}
                    {/if}
                </label>
                <input type="radio" name="event_status" id="event_status_off" value="0" {if isset($event_status) AND $event_status == 0}checked="checked"{/if}/>
                <label class="t" for="event_status_off">
                    {if $version < 1.6}
                        <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='eventsmanager'}" title="{l s='Disabled' mod='eventsmanager'}" />
                    {else}
                        {l s='No' mod='eventsmanager'}
                    {/if}
                </label>
                <a class="slide-button btn"></a>
            </span>
        </div>

{foreach from=$languages item=language}
{if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
  {/if}

         <div class="form-group">
            <label class="control-label col-lg-3 required">
            {l s='Event Title:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
               <input type="text" id="event_title_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="event_title_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="" value="{if !empty($event_title)}{$event_title[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" size="60" required="required">
            

            {if $languages|count > 1}
          
              <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown" >
                  {$language.iso_code|escape:'htmlall':'UTF-8'}
                  <span class="caret"></span>
                  
              </button>
                <ul class="dropdown-menu">
                  {foreach from=$languages item=lang}
                  <li><a class="currentLang" data-id="{$lang.id_lang|escape:'htmlall':'UTF-8'}" href="javascript:hideOtherLanguage({$lang.id_lang|escape:'javascript'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
                  {/foreach}
              </ul>

            {/if}

            </div>
        
        

         </div>

         

        

        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Meta Title:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
               <input type="text" id="event_page_title_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="event_page_title_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="" value="{if !empty($event_page_title)}{$event_page_title[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" size="60" >

               {if $languages|count > 1}
          
              <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                  {$language.iso_code|escape:'htmlall':'UTF-8'}
                  <span class="caret"></span>
                  
              </button>
                <ul class="dropdown-menu">
                  {foreach from=$languages item=lang}
                  <li><a class="currentLang" data-id="{$lang.id_lang|escape:'htmlall':'UTF-8'}" href="javascript:hideOtherLanguage({$lang.id_lang|escape:'javascript'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
                  {/foreach}
              </ul>

            {/if}


            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Permalinks:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
               <input type="text" id="event_permalinks_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="event_permalinks_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="" value="{if !empty($event_permalinks)}{$event_permalinks[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" size="60" >

               {if $languages|count > 1}
          
              <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                  {$language.iso_code|escape:'htmlall':'UTF-8'}
                  <span class="caret"></span>
              </button>
                <ul class="dropdown-menu">
                  {foreach from=$languages item=lang}
                  <li><a class="currentLang" data-id="{$lang.id_lang|escape:'htmlall':'UTF-8'}" href="javascript:hideOtherLanguage({$lang.id_lang|escape:'javascript'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
                  {/foreach}
              </ul>

            {/if}


            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Meta Description:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
               <textarea rows="4" cols="50" class="fr-view autoload_rte rte default-editor" id="event_meta_description_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="event_meta_description_{$language.id_lang|escape:'htmlall':'UTF-8'}">
                {if !empty($event_meta_description)}{$event_meta_description[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}
               </textarea>

               {if $languages|count > 1}
          
              <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown" style="height: 33px;">
                  {$language.iso_code|escape:'htmlall':'UTF-8'}
                  <span class="caret"></span>
                  
              </button>
                <ul class="dropdown-menu">
                  {foreach from=$languages item=lang}
                  <li><a class="currentLang" data-id="{$lang.id_lang|escape:'htmlall':'UTF-8'}" href="javascript:hideOtherLanguage({$lang.id_lang|escape:'javascript'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
                  {/foreach}
              </ul>

            {/if}

            </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-3 required">
           <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="Invalid characters: <>;=#{}">
            {l s='Event Content:' mod='eventsmanager'}
            </span>
          </label>

            <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
               <textarea rows="7" class="autoload_rte rte default-editor" cols="40" id="event_content_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="event_content_{$language.id_lang|escape:'htmlall':'UTF-8'}">
                {if !empty($event_content)}{$event_content[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}
               </textarea>
               {if $languages|count > 1}
          
              <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown" style="height: 33px;">
                  {$language.iso_code|escape:'htmlall':'UTF-8'}
                  <span class="caret"></span>
                  
              </button>
                <ul class="dropdown-menu">
                  {foreach from=$languages item=lang}
                  <li><a class="currentLang" data-id="{$lang.id_lang|escape:'htmlall':'UTF-8'}" href="javascript:hideOtherLanguage({$lang.id_lang|escape:'javascript'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
                  {/foreach}
              </ul>

            {/if}

            </div>
        </div>

<div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Meta Keywords:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
               <input type="text" id="event_meta_keywords_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="event_meta_keywords_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="" value="{if !empty($event_meta_keywords)}{$event_meta_keywords[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" size="60" >

               {if $languages|count > 1}
          
              <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                  {$language.iso_code|escape:'htmlall':'UTF-8'}
                  <span class="caret"></span>
                  
              </button>
                <ul class="dropdown-menu">
                  {foreach from=$languages item=lang}
                  <li><a class="currentLang" data-id="{$lang.id_lang|escape:'htmlall':'UTF-8'}" href="javascript:hideOtherLanguage({$lang.id_lang|escape:'javascript'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
                  {/foreach}
              </ul>

            {/if}


            </div>
        </div>
        {if $languages|count > 1}
      </div>
  {/if}

{/foreach}
       <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='Tags:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9">
                <select name="event_tags[]" class="chosen fixed-width-xl" id="event_tags[]" multiple="multiple" style="display: none;">
                    {foreach from=$tags item=tag}
                        <option value="{$tag.id_fme_tags|escape:'htmlall':'UTF-8'}" {if !empty($selectedTags)}{if $tag.id_fme_tags|in_array:$selectedTags}selected{/if}{/if}>
                              {$tag.name|escape:'htmlall':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Youtube Video URL:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text" id="event_video" name="event_video" class="" value="{if !empty($event_video)}{$event_video|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>

         <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Facebook Link:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text" placeholder="https://www.facebook.com/page_name" id="facebook_link" name="facebook_link" class="" value="{if !empty($facebook_link)}{$facebook_link|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Instagram Link:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text" placeholder="https://www.instagram.com/user_name" id="instagram_link" name="instagram_link" class="" value="{if !empty($instagram_link)}{$instagram_link|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Twitter Link:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text"  placeholder="https://twitter.com/user_name" id="twitter_link" name="twitter_link" class="" value="{if !empty($twitter_link)}{$twitter_link|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>

         <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Event Streaming Start Time:' mod='eventsmanager'}
            </label>
             <div class="input-group col-lg-6">
                      <input id="event_streaming_start_time" type="text" name="event_streaming_start_time" value="{if !empty($event_streaming_start_time)}{$event_streaming_start_time|escape:'htmlall':'UTF-8'}{/if}">
                      <span class="input-group-addon">
                        <i class="icon-calendar-empty"></i>
                      </span>
                    </div>
         </div>

         <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Event Streaming End Time:' mod='eventsmanager'}
            </label>
             <div class="input-group col-lg-6">
                      <input id="event_streaming_end_time" type="text" name="event_streaming_end_time" value="{if !empty($event_streaming_end_time)}{$event_streaming_end_time|escape:'htmlall':'UTF-8'}{/if}">
                      <span class="input-group-addon">
                        <i class="icon-calendar-empty"></i>
                      </span>
                    </div>
         </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Live Streaming Url' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text" id="event_streaming" name="event_streaming" class="" value="{if !empty($event_streaming)}{$event_streaming|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>
      <!--   <div class="form-group">
          <label class="control-label col-lg-3">
            {l s='Vedeo Full Width:' mod='eventsmanager'}
            </label>
             
            
           <span class="switch prestashop-switch fixed-width-lg col-lg-9 col-xs-10">
                <input type="radio" name="event_video_status" id="event_video_status_on" value="1" {if isset($event_video_status) AND $event_video_status == 1}checked="checked"{/if}/>
                <label class="t" for="event_video_status_on">
                    {if $version < 1.6}
                        <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='eventsmanager'}" title="{l s='Enabled' mod='eventsmanager'}" />
                    {else}
                        {l s='Yes' mod='eventsmanager'}
                    {/if}
                </label>
                <input type="radio" name="event_video_status" id="event_video_status_off" value="0" {if isset($event_video_status) AND $event_video_status == 0}checked="checked"{/if}/>
                <label class="t" for="event_video_status_off">
                    {if $version < 1.6}
                        <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='eventsmanager'}" title="{l s='Disabled' mod='eventsmanager'}" />
                    {else}
                        {l s='No' mod='eventsmanager'}
                    {/if}
                </label>
                <a class="slide-button btn"></a>
            </span>
        </div> -->

  
  

        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Venue:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text" id="event_venu" name="event_venu" class="" value="{if !empty($event_venu)}{$event_venu|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Longitude: e.g. -77.0364' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text" id="longitude" name="longitude" class="" value="{if !empty($longitude)}{$longitude|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Latitude: e.g. 38.8951' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
               <input type="text" id="latitude" name="latitude" class="" value="{if !empty($latitude)}{$latitude|escape:'htmlall':'UTF-8'}{/if}" size="60">
            </div>
        </div>


        <div class="form-group">
            <label class="control-label col-lg-3">
            {l s='Image:' mod='eventsmanager'}
            </label>
            <div class="col-lg-9 col-xs-10">
            <p class="alert alert-info">{l s='The file must be image.' mod='eventsmanager'}</p>
               
            {if !empty($event_image)}{$event_image}{*HTML content*}{/if}
              <input id="event_image" type="file" name="event_image" style=" border: 2px solid gray; color: gray;background-color: white;padding: 8px 20px;border-radius: 8px;font-size: 15px;font-weight: bold;margin-top: 10px;">
            </div>
        </div>


        <div class="form-group">
            <label class="control-label col-lg-3 required">
            {l s='Event Start Date:' mod='eventsmanager'}
            </label>
             <div class="input-group col-lg-4">
                      <input id="event_start_date" type="text" data-hex="true" name="event_start_date" value="{if !empty($event_start_date)}{$event_start_date|escape:'htmlall':'UTF-8'}{/if}" required="true">
                      <span class="input-group-addon">
                        <i class="icon-calendar-empty"></i>
                      </span>
                    </div>
         </div>


          <div class="form-group">
            <label class="control-label col-lg-3 required">
            {l s='Event End Date:' mod='eventsmanager'}
            </label>

             <div class="input-group col-lg-4">
                      <input id="event_end_date" type="text" data-hex="true" name="event_end_date"  value="{if !empty($event_end_date)}{$event_end_date|escape:'htmlall':'UTF-8'}{/if}" required="true">
                      <span class="input-group-addon">
                        <i class="icon-calendar-empty"></i>
                      </span>
                    </div>
         </div>
      </div>
   </div>
    <div class="panel-footer">
      {if isset($event_id)}
      <button id="fme_events_form_submit_btn" class="btn btn-default pull-right" name="submitAddfme_events" value="Save" type="submit"><i class="process-icon-save"></i>{l s='Update' mod='eventsmanager'} </button>
      {else}
      <a class="btn btn-default pull-right btn-lg" id="eventsmanager_link_contact" href="javascript:displayPrivateTab('contact');">{l s='NEXT ' mod='eventsmanager'}<i class="icon-circle-arrow-right"></i></a>
    {/if}
      

 </div>
    <!-- <div class="panel-footer">
    <button id="fme_events_form_submit_btn" class="btn btn-default pull-right" name="submitAddfme_events" value="Save" type="submit"><i class="process-icon-save"></i>{l s='Save' mod='eventsmanager'} </button></div> -->
</div>


<script type="text/javascript">
    css_content ="{$smarty.const.__PS_BASE_URI__}{* html content *}themes/{$smarty.const._THEME_NAME_}{* html content *}/css/global.css";
    base_url = "{$smarty.const.__PS_BASE_URI__}{* html content *}";
</script>
