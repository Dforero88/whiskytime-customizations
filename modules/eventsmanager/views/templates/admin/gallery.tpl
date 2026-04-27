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

<script type="text/javascript">
    var arrow = '<img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/eventsmanager/views/img/arrow.png" style="padding-top: 10px;">';
    $(function () {
       //clone file upload box
       $('#add-more-files').click(function(i)
       {
        $('.sno').remove();
          var cloned = $(this).parent().prev().clone();
          cloned.val(null);
          $(cloned).insertBefore($(this).parent());
          $(this).parent().prev().before($('<span class="sno" style="float:left;">'+ arrow +'</span>'));
       });
    });
    </script> 


 <div class="panel col-lg-10" style="margin-top: -5px;">
   <div class="panel" id="fieldset_0">
      <div class="panel-heading">
         <img src="../img/admin/add.gif" alt="FME Events">{l s='Add Gallery Images' mod='eventsmanager'}
      </div>

      <fieldset id="middle2">
     
        {if isset($eventGalleryImages)}
        <table cellpadding="0" cellspacing="0" border="0" width="40%">
        {section name=agent loop=$eventGalleryImages}
        <tr id="{$eventGalleryImages[agent].image_id|escape:'htmlall':'UTF-8'}" style="border: 1px solid rgb(241, 241, 241);">
            <td><img alt="Event Gallery" src="../img/{$eventGalleryImages[agent].image_file|escape:'htmlall':'UTF-8'}" width="100" height="100"></td>
            <td>&nbsp;</td>
            <td><a class="btn btn-default button" href="javascript:;" onclick="Delete_this_image({$eventGalleryImages[agent].image_id|escape:'htmlall':'UTF-8'});">{l s='Delete' mod='eventsmanager'} &nbsp;<i class="icon-trash" title="{l s='Delete' mod='eventsmanager'}"></i></a></td>
        </tr>
        <tr><td height="10">&nbsp;</td></tr>
        {/section}
        </table>
        {/if}
        
        <ul>
            <label>{l s='Image:' mod='eventsmanager'}</label>
            <li style="list-style-type:none; margin-top:5px;padding-left:2%;"> <input style="border: 1px solid;border-radius: 6px;" name="galleryimages[]" type="file"/></li>
            <li style="list-style-type:none"><br /><a class="btn btn-default button" href="javascript:;" id="add-more-files"><strong>{l s='Add More Images' mod='eventsmanager'}</strong></a></li>
        </ul>
        <div class="clear"></div>

    </fieldset>

    </div>
     <div class="panel-footer">
      {if isset($event_id)}
      <button id="fme_events_form_submit_btn" class="btn btn-default pull-right" name="submitAddfme_events" value="Save" type="submit"><i class="process-icon-save"></i>{l s='Update' mod='eventsmanager'} </button>
      {else}
      <a class="btn btn-default pull-right btn-lg" id="eventsmanager_link_product" href="javascript:displayPrivateTab('product');">{l s='NEXT ' mod='eventsmanager'}<i class="icon-circle-arrow-right"></i></a>
    {/if}
   
 </div>
   
   <!-- <div class="panel-footer">
    <button id="fme_events_form_submit_btn" class="btn btn-default pull-right" name="submitAddfme_events" value="Save" type="submit"><i class="process-icon-save"></i>{l s='Save' mod='eventsmanager'} </button></div> -->
</div>

 