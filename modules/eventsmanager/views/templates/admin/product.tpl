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
$(document).ready(function() {
    var sectionsCount = 0;
    //add new section
    $('body').on('click', '.addsection', function() {
        sectionsCount++;
            var newName = this.id + sectionsCount;
            //update for label
            $(this).prev().attr('for', newName);
            //update id
            this.id = newName;
    $("#sections").append('<div class="panel section" id="fieldset_0"><input type="hidden" id="p_id" name="" ><div class="panel-heading"><img src="../img/admin/add.gif" alt="FME Events">{l s='Add Event Product' mod='eventsmanager'}</div><div class="form-wrapper forms"> <div class="alert alert-info" role="alert"><p class="alert-text">{l s='Add Aditional Information (Tax, SEO, Shipping etc) about this Product from Catalog->Products' mod='eventsmanager'} <a class="alert-link" target="_blank">{l s='Edit Product' mod='eventsmanager'}</a></p></div> <div class="form-group"><label class="control-label col-lg-3 required">{l s='Product Name:' mod='eventsmanager'}</label><div class="col-lg-9 col-xs-10" style="display: inline-flex;"><input type="text" required="" id="p_name" name="p_name[]" value="" ></div></div> <div class="form-group"> <label class="control-label col-lg-3 required"> {l s='Product Quantity:' mod='eventsmanager'} </label> <div class="col-lg-9 col-xs-10" style="display: inline-flex;"> <input type="text" required="" id="p_quantity" name="p_quantity[]" class="" value="" > </div> </div> <div class="form-group"> <label class="control-label col-lg-3"> {l s='Product Image:' mod='eventsmanager'} </label> <div class="col-lg-9 col-xs-10" style="display: inline-flex;"> <input type="file" id="p_img" name="p_img[]" class="" value="" > </div> </div> <div class="form-group"> <label class="control-label col-lg-3 required"> {l s='Product Price:' mod='eventsmanager'} </label> <div class="col-lg-9 col-xs-10" style="display: inline-flex;"> <div class="input-group prefix"> <span class="input-group-addon">{$currency|escape:'htmlall':'UTF-8'}</span> <input type="text" required="" id="p_price" name="p_price[]" class="" value="" > </div> </div> </div> </div> </div>');
        return false;
    });
});

</script>

{if ! isset($seat_selection) OR ($seat_selection != 1 AND isset($seat_selection) )}

<div class="panel col-lg-10" style="margin-top: -5px;" id="sections">
    <div class="form-group"> 
        <a class="btn btn-primary button addsection pull-right"><strong>{l s='Add More Products' mod='eventsmanager'}</strong></a>
    </div>
{if !empty($productData)}
        {foreach from=$productData item=data}
        <div class="panel" id="fieldset_0">
            <div class="panel-heading">
                <img src="../img/admin/add.gif" alt="FME Events">{l s='Add Event Product' mod='eventsmanager'}
                <a class="material-icons pull-right" style="color:red;" href="javascript:;" onclick="Delete_this_data({$data.id_product|escape:'htmlall':'UTF-8'});" id="remove">delete</a>
            </div>
            <div class="form-wrapper forms">
                <div class="alert alert-info" role="alert"><p class="alert-text">{l s='Add Aditional Information (Tax, SEO, Shipping etc) about this Product from Catalog->Products' mod='eventsmanager'} <a class="alert-link" target="_blank">{l s='Edit Product' mod='eventsmanager'}</a></p>
                </div>
                <input type="hidden" id="count" name="totals" value="">
                <input type="hidden" id="p_id" name="p_id[]" value="{if !empty($data.id_product)}{$data.id_product|escape:'htmlall':'UTF-8'}{/if}" >
                <div class="form-group">
                    <label class="control-label col-lg-3 required">
                    {l s='Product Name:' mod='eventsmanager'}
                    </label>
                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    <input type="text" required="" id="p_name" name="p_name[]" value="{if !empty($data.name)}{$data.name|escape:'htmlall':'UTF-8'}{/if}" >
                </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">
                    {l s='Product Quantity:' mod='eventsmanager'}
                    </label>
                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    <input type="text"  required="" id="p_quantity" name="p_quantity[]" class="" value="{if !empty($data.quantity)}{$data.quantity|escape:'htmlall':'UTF-8'}{/if}" >
                </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">
                    {l s='Product Image:' mod='eventsmanager'}
                    </label>

                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    {if isset($productsCover[$data.id_product])}<img src="{$productsCover[$data.id_product]|escape:'htmlall':'UTF-8'}" width="250" height="250"> {/if}
                    <input type="file" id="p_img" name="p_img[]" class="" value="" >
                </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">
                    {l s='Product Price:' mod='eventsmanager'}
                    </label>
                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    <div class="input-group prefix">                           
                    <span class="input-group-addon">{$currency|escape:'htmlall':'UTF-8'}</span>
                    <input type="text" required="" id="p_price" name="p_price[]" class="" value="{if !empty($data.price)}{$data.price|escape:'htmlall':'UTF-8'}{/if}" >
                    </div>
                </div>
                
                </div>
                
                </div>
        </div>
         
        {* <div class="form-group"> 
            <a class="btn btn-default button addsection"><strong>{l s='Add More Products' mod='eventsmanager'}</strong></a>
        </div> *}
      {/foreach}
    
{else}
        <div class="panel" id="fieldset_0">
            <div class="panel-heading">
                <img src="../img/admin/add.gif" alt="FME Events">{l s='Add Event Product' mod='eventsmanager'}
            </div>
                <input type="hidden" id="p_id" name="" >
            <div class="form-wrapper forms">
                <div class="alert alert-info" role="alert"><p class="alert-text">{l s='Add Aditional Information (Tax, SEO, Shipping etc) about this Product from Catalog->Products' mod='eventsmanager'} <a class="alert-link" target="_blank">{l s='Edit Product' mod='eventsmanager'}</a></p>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3 required">
                    {l s='Product Name:' mod='eventsmanager'}
                    </label>
                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    <input type="text" required="" id="p_name" name="p_name[]" value="" >
                </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">
                    {l s='Product Quantity:' mod='eventsmanager'}
                    </label>
                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    <input type="text"  required="" id="p_quantity" name="p_quantity[]" class="" value="" >
                </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">
                    {l s='Product Image:' mod='eventsmanager'}
                    </label>

                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    
                    <input type="file" id="p_img" name="p_img[]" class="" value="" >
                </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">
                    {l s='Product Price:' mod='eventsmanager'}
                    </label>
                    <div class="col-lg-9 col-xs-10" style="display: inline-flex;">
                    <div class="input-group prefix">                           
                    <span class="input-group-addon">{$currency|escape:'htmlall':'UTF-8'}</span>
                    <input type="text"  required="" id="p_price" name="p_price[]" class="" value="" >
                    </div>
                </div>
                
                </div>
                
                </div>
        </div>
        {/if}
         <div class="panel-footer" style="margin: 0px;">
            {if isset($event_id)}
      <button id="fme_events_form_submit_btn" class="btn btn-default pull-right" name="submitAddfme_events" value="Save" type="submit"><i class="process-icon-save"></i>{l s='Update' mod='eventsmanager'} </button>
      {else}

    
    <a class="btn btn-default pull-right btn-lg" id="eventsmanager_link_seatmap" href="javascript:displayPrivateTab('seatmap');">{l s='NEXT ' mod='eventsmanager'}<i class="icon-circle-arrow-right"></i></a>
    {/if}
   </div>
    </div>
{else}
    <div class="panel col-lg-8" style="margin-top: -5px;" id="sections">
        <div class="panel" id="fieldset_0">
        <div class="panel-heading">
                <img src="../img/admin/add.gif" alt="FME Events">{l s='List Of Attach Products' mod='eventsmanager'}
        </div>
        {if !empty($productData)}
        <ul id="list_pro">
            {foreach from=$productData item=data}
                    <li id="row_6" class="media">
                        <div class="media-body media-middle">
                            <span class="label" style="color: #8a1212;font-size: 16px;">
                                 {l s='( ID:' mod='eventsmanager'}{if !empty($data.id_product)}{$data.id_product|escape:'htmlall':'UTF-8'}{/if}
                                  {l s=')' mod='eventsmanager'}

                                  {if !empty($data.name)}{$data.name|escape:'htmlall':'UTF-8'}{/if}
                            </span>
                            <span>
                                {if isset($productsCover[$data.id_product])}<img src="{$productsCover[$data.id_product]|escape:'htmlall':'UTF-8'}" width="33" height="33"> {/if}
                            </span>
                        </div>
                    </li>
            {/foreach}
        </ul>
        {/if}
        </div>
    </div>
{/if}
