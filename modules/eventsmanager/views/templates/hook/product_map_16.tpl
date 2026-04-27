{*
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
*}

{if $is_logged == true AND $match_products}

{foreach from=$match_products item=id_product}
  {if $ps_version < 1.7}
    {assign var="name" value=Context::getContext()->cart->getProducts("false", {$id_product.id_product|escape:'htmlall':'UTF-8'})}
  {else}
    {assign var="name" value=Context::getContext()->cart->getProductQuantity({$id_product.id_product|escape:'htmlall':'UTF-8'})}
  {/if}
  
  {assign var="isenable" value= Events::isEnable({$id_product.event_id|escape:'htmlall':'UTF-8'})}
  {assign var="isseatmap" value= Events::isEnableSeatMap({$id_product.event_id|escape:'htmlall':'UTF-8'})}
  {if $isseatmap}
  <div class="front_panel">

  
  <!-- <h3 class="card-block" style="color: #5bc9e1;text-align: center;">{l s='Customer Information' mod='eventsmanager'}</h3> -->
   <div class="">
   <!-- <h5 class="p_name">{Product::getProductName({$id_product.id_product|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}</h5> -->

   <!-- {if $ps_version < 1.7}
    <h6 style="text-align: center;">{l s='Quantity : ' mod='eventsmanager'}
    {if $name}
    {$name[0]['cart_quantity']|escape:'htmlall':'UTF-8'}</h6>
    {/if}
   {else}
    <h6 style="text-align: center;">{l s='Quantity : ' mod='eventsmanager'}{$name['quantity']|escape:'htmlall':'UTF-8'}</h6>
   {/if} -->
  
   <div class="col-lg-12">
          
            <input type="hidden" name="ajax_url" id="ajax_url" value="{$ajax_url|escape:'htmlall':'UTF-8'}">
            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_event_id" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_event_id" value="{$id_product.event_id|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_product" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_product" value="{$id_product.id_product|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_customer" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_customer" value="{$id_customer|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_guest" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_guest" value="{$id_guest|escape:'htmlall':'UTF-8'}">

            {if $ps_version < 1.7}
              <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" value="{$name[0]['cart_quantity']|escape:'htmlall':'UTF-8'}">
            {else}
              <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" value="{$name['quantity']|escape:'htmlall':'UTF-8'}">
            {/if}

            

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_cart" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_cart" value="{$id_cart|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_customer_name" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_customer_name" value="{$customer_name|escape:'htmlall':'UTF-8'}">
    </div>
    <div style="text-align: center;">
    {if $isseatmap}
      {if $ps_version < 1.7}
      <!-- {if !$name}
        <span style="color: red;"> {l s='First add product to cart' mod='eventsmanager'} </span>
      {else}

      {/if} -->
      <button style="margin-top: 5px;" type="button" name="selectMap" id="select_seat_{$id_product.event_id|escape:'htmlall':'UTF-8'}" onclick="openSelectMapPro({$id_product.event_id|escape:'htmlall':'UTF-8'},0, {$id_product.id_product|escape:'htmlall':'UTF-8'});" class="btn btn-primary btn-sm" comp="">{l s='Select Seat' mod='eventsmanager'}</button>
      {else}
        <button style="margin-top: 5px;" type="button" name="selectMap" id="select_seat_{$id_product.event_id|escape:'htmlall':'UTF-8'}" data-toggle="modal" data-target="#openMap" onclick="openSelectMapPro({$id_product.event_id|escape:'htmlall':'UTF-8'},{$name['quantity']|escape:'htmlall':'UTF-8'}, {$id_product.id_product|escape:'htmlall':'UTF-8'});" class="btn btn-primary btn-sm" comp="">{l s='Select Seat' mod='eventsmanager'}</button>
      {/if}
      
      <span id="timer_{$id_product.event_id|escape:'htmlall':'UTF-8'}"></span>
    {/if}
    </div>
   <div class="clearfix"></div>
   <br>
   </div>
   {/if}
{/foreach}
<input type="hidden" name="allow_order" id="allow_order" value="1" reserve="1">
<div style="text-align: center;">

  <span style="color: green;display: none;" id="done_message"><i class="material-icons">done</i></span>
   </div>
<p id="error_info" class='alert alert-warning warning' style="margin-top: 10px;display: none;">{l s='Kindly Fill All Fields' mod='eventsmanager'}</p>
   <div id=result></div>
</div>
{elseif $match_products}
<div class="front_panel" style="text-align: center;">
  <h6 class="card-block" style="color: #5bc9e1;">{l s='Login For Ticket Registration' mod='eventsmanager'}</h6>
</div>
{/if}
<div class="modal" id="openMap" role="dialog" style="display: none;background: #e6e6e6;">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content" style="overflow: scroll;text-align: center;">
        <h2>{l s='Select Seat' mod='eventsmanager'}</h2>
        <div class="form-group row col-lg-12">
          <div id="show_map" style="margin-left: 12px;">
            
          </div>
          <!-- <input type="button" id="btnShowSeat" value="Show All" />

          <input type="button" id="btnShowNew" value="Show Selected Seats" />  -->   
        </div>
        <div class="clearfix"></div>
        <div class="form-group row col-lg-12">
          <button type="button" name="save_map_pro" id="save_map_pro" class="button btn btn-primary">
            <span>{l s='Save & Close' mod='eventsmanager'}</span>
          </button>
            <!-- <button type="button" name="closemodal" data-dismiss="modal" id="closemodal" class="button btn btn-danger">
            <span>{l s='Close' mod='eventsmanager'}</span>
          </button> -->
        </div>
        <div class="clearfix"></div>
         <span id="update_message" >{l s='Seleced Seats are reserve for ' mod='eventsmanager'}{$wait_min|escape:'htmlall':'UTF-8'} {l s=' mints. Kindly complete your order within this time limit ' mod='eventsmanager'}</span>
        <span id="error_message" >{l s='Kindly First select ' mod='eventsmanager'}<span id="t_seats"></span>{l s=' Seat' mod='eventsmanager'}</span>

      </div>
      
    </div>
  </div>

  <input type="hidden" name="events_in_cart" id="events_in_cart" value="{$events_in_cart|escape:'htmlall':'UTF-8'}">
  <input type="hidden" name="wait_min" id="wait_min" value="{$wait_min|escape:'htmlall':'UTF-8'}">
  <input type="hidden" name="req_phone" id="req_phone" value="0">
  <input type="hidden" name="update_btn_fmm" id="update_btn_fmm" value="0">

<style type="text/css">
#row_col_table tbody tr td:hover
{
background-color:#15a1e3 !important;      
} 
#row_col_table .sello{
background:url("{$blue_color|escape:'htmlall':'UTF-8'}") center content-box !important;
}
#row_col_table .selectedSeat
{ 
  color: white;
background-image:url("{$red_color|escape:'htmlall':'UTF-8'}") !important;          
}
#row_col_table .selectingSeat
{ 
  color: white;
background-image:url("{$green_color|escape:'htmlall':'UTF-8'}") !important;        
}
#row_col_table .fmm_disabled
{
background: #bdbdbd !important;
}
</style>