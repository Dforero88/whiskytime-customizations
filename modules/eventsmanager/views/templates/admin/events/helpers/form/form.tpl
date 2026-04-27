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
 * @copyright Copyright 2017 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   eventsmanager
*}

{extends file="helpers/form/form.tpl"}    
{block name="other_fieldsets"}

<script type="text/javascript" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}js/jquery/plugins/jquery.colorpicker.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#fieldset_0').hide();

   //$('#currentFormTab').val('general');
   displayPrivateTab('general');
    
    $("#shows").click(function(){
        $('#eventsmanager_link_general').removeClass('selected');
        $('#eventsmanager_link_invoice').addClass('selected');
        $(".pdf").show();
    });

})
function displayPrivateTab(tab)
{
    $('.private_tab').hide();
    $('.private_tab_page').removeClass('selected');
    $('#eventsmanager_' + tab).show();
    $('#eventsmanager_link_' + tab).addClass('selected');
    $('#currentFormTab').val(tab);
    scroll(0,0);
}
{if isset($event_id)}    
    function Delete_this_image(image_id)
    {
    	if(confirm("Are you sure to delete this image?"))
    	{
    		var dataString = "image_id=" + image_id +"&action=delete_this_image";
    		$.ajax({
    			type: "POST",  
    			url: "index.php/?controller=AdminEvents&event_id={$event_id|escape:'htmlall':'UTF-8'}&updateevents&token={$token|escape:'htmlall':'UTF-8'}",  
    			data: dataString, 
    			success: function(response)
    			{
    				location.reload();
    			}
    		});
    	}
    	return false;
    }
    function Delete_this_data(p_id)
    {
        if(confirm("Are you sure to delete this product?"))
        {
            var dataString = "p_id=" + p_id +"&action=delete_this_data";
            $.ajax({  
                type: "POST",  
                url: "index.php/?controller=AdminEvents&id_event_product={$event_id|escape:'htmlall':'UTF-8'}&updateevents&token={$token|escape:'htmlall':'UTF-8'}",  
                data: dataString, 
                success: function(response)
                {  
                    location.reload();
                }
            });
        }
        return false;
    }
{/if}
document.addEventListener("DOMContentLoaded", function() {
    var manageEventsSelected = {$manage_events_link|json_encode};
    if (manageEventsSelected) {
        document.getElementById('event-manager-tabs').style.display = 'block';
    }
});
</script>
<input type="hidden" name="display_none" id="display_none" value="{if isset($display_none)} {$display_none|escape:'htmlall':'UTF-8'} {/if}">
<div class="">
    <div class="toolbarBox pageTitle">
    </div>
    <div class="col-lg-2 " id="private-shop">
        <div class="productTabs">
            <ul class="tab" id="event-manager-tabs">
                <li class="tab-row">
                    <a class="private_tab_page selected" id="eventsmanager_link_general" href="javascript:displayPrivateTab('general');">{l s='General Information' mod='eventsmanager'}</a>
                </li>
                <li class="tab-row">
                    <a class="private_tab_page" id="eventsmanager_link_contact" href="javascript:displayPrivateTab('contact');">{l s='Contact Detail' mod='eventsmanager'}</a>
                </li>
                <li class="tab-row">
                    <a class="private_tab_page" id="eventsmanager_link_gallery" href="javascript:displayPrivateTab('gallery');">{l s='Event Gallery ' mod='eventsmanager'}</a>
                </li>
               <li class="tab-row">
                   <a class="private_tab_page" id="eventsmanager_link_product" href="javascript:displayPrivateTab('product');">{l s='Event Tickets' mod='eventsmanager'}</a>
               </li>
               <li class="tab-row" {if isset($display_none)} {if $display_none == 1 } style="display: none;" {/if} {/if}>
                   <a class="private_tab_page" id="eventsmanager_link_seatmap" href="javascript:displayPrivateTab('seatmap');">{l s='Seat Map' mod='eventsmanager'}</a>
               </li>
               <!-- <li class="tab-row">
                   <a class="private_tab_page" id="eventsmanager_link_invoice" href="javascript:displayPrivateTab('invoice');">{l s='Ticket Setting' mod='eventsmanager'}</a>
               </li> -->
            </ul>
        </div>
    </div>
    <!-- Tab Content -->
    <form action="" name="eventsmanager_form" id="eventsmanager_form" method="post" enctype="multipart/form-data" class="col-lg-10 panel form-horizontal" {if $version < 1.6}style="margin-left: 145px;"{/if}>
        <input type="hidden" id="currentFormTab" name="currentFormTab" value="general" />
        <div id="eventsmanager_general" class="private_tab tab-pane">
            <div class="separation"></div>
            {include file="../../../general_settings.tpl"}
        </div>
        <div id="eventsmanager_contact" class="private_tab tab-pane" style="display:none;">
            <div class="separation"></div>
            {include file="../../../contact.tpl"}
        </div>
        <div id="eventsmanager_gallery" class="private_tab tab-pane" style="display:none;">
            <div class="separation"></div>
            {include file="../../../gallery.tpl"}
        </div>
        <div id="eventsmanager_product" class="private_tab tab-pane" style="display:none;">
            <div class="separation"></div>
            {include file="../../../product.tpl"}
        </div>
        <div id="eventsmanager_seatmap" class="private_tab tab-pane" style="display:none;">
            <div class="separation"></div>
            {include file="../../../seatmap.tpl"}
        </div>
        <div id="eventsmanager_invoice" class="private_tab tab-pane" style="display:none;">
            <div class="separation"></div>
            {include file="../../../invoice.tpl"}
        </div>

        <div class="separation"></div>

        <!-- {if $version >= 1.6}
            <div class="panel-footer">
                <button class="btn btn-default pull-right" name="saveConfiguration" type="submit">
                    <i class="process-icon-save"></i>
                    {l s='Save' mod='eventsmanager'}
                </button>
            </div>
        {else}
            <div style="text-align:center">
                <input type="submit" value="{l s='Save' mod='eventsmanager'}" class="button" name="saveConfiguration"/>
            </div>
        {/if} -->

    </form>
     
   <div class="clearfix"></div>

</div>
<br></br>
<div class="clearfix"></div>
{literal}
<style type="text/css">
/*== PS 1.6 ==*/
 #private-shop ul.tab { list-style:none; padding:0; margin:0}

 #private-shop ul.tab li a {background-color: white;border: 1px solid #DDDDDD;display: block;margin-bottom: -1px;padding: 10px 15px;}
 #private-shop ul.tab li a { display:block; color:#555555; text-decoration:none}
 #private-shop ul.tab li a.selected { color:#fff; background:#00AFF0}

 #eventsmanager_toolbar { clear:both; padding-top:20px; overflow:hidden}

 #eventsmanager_toolbar .pageTitle { min-height:90px}

 #eventsmanager_toolbar ul { list-style:none; float:right}

 #eventsmanager_toolbar ul li { display:inline-block; margin-right:10px}

 #eventsmanager_toolbar ul li .toolbar_btn {background-color: white;border: 1px solid #CCCCCC;color: #555555;-moz-user-select: none;background-image: none;border-radius: 3px 3px 3px 3px;cursor: pointer;display: inline-block;font-size: 12px;font-weight: normal;line-height: 1.42857;margin-bottom: 0;padding: 8px 8px;text-align: center;vertical-align: middle;white-space: nowrap; }

 #eventsmanager_toolbar ul li .toolbar_btn:hover { background-color:#00AFF0 !important; color:#fff;}

 #eventsmanager_form .language_flags { display:none}
 form#eventsmanager_form {
    background-color: #ebedf4;
    border: 1px solid #ccced7;
    /*min-height: 404px;*/
    padding: 5px 10px 10px;
}
</style>

{/literal}
{/block}
