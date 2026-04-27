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

<link rel="stylesheet" type="text/css" href=" https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css">

{if $is_logged == true AND $match_products}
<div class="front_panel">

<h3 class="card-block" style="color: #13392a;text-align: center;">{l s='Customer Information' mod='eventsmanager'}</h3>

{foreach from=$match_products item=id_product}
  {if $ps_version < 1.7}
    {assign var="name" value=Context::getContext()->cart->getProducts("false", {$id_product.id_product|escape:'htmlall':'UTF-8'})}
  {else}
    {assign var="name" value=Context::getContext()->cart->getProductQuantity({$id_product.id_product|escape:'htmlall':'UTF-8'})}
  {/if}
  
  {assign var="isenable" value= Events::isEnable({$id_product.event_id|escape:'htmlall':'UTF-8'})}
  {assign var="isseatmap" value= Events::isEnableSeatMap({$id_product.event_id|escape:'htmlall':'UTF-8'})}
   <div class="front_panel_ticket">
   <h5 class="p_name">{Product::getProductName({$id_product.id_product|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}</h5>

   {if $ps_version < 1.7}
    <h6 style="text-align: center;">{l s='Quantity : ' mod='eventsmanager'}{$name[0]['cart_quantity']|escape:'htmlall':'UTF-8'}</h6>
   {else}
    <h6 style="text-align: center;">{l s='Quantity : ' mod='eventsmanager'}{$name['quantity']|escape:'htmlall':'UTF-8'}</h6>
   {/if}
  
   <div class="col-lg-12">
          <label class="col-form-label">{l s='Customer Name' mod='eventsmanager'}</label>
            <input type="hidden" name="ajax_url" id="ajax_url" value="{$ajax_url|escape:'htmlall':'UTF-8'}">
            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_event" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_event" value="{$id_product.event_id|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_product" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_product_{$id_product.id_product|escape:'htmlall':'UTF-8'}" value="{$id_product.id_product|escape:'htmlall':'UTF-8'}">

             <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_product" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_product" value="{$id_product.id_product|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_event_product_id" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_event_product_id" value="{$id_product.event_product_id|escape:'htmlall':'UTF-8'}">


            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_customer" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_customer" value="{$id_customer|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_guest" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_guest" value="{$id_guest|escape:'htmlall':'UTF-8'}">

            {if $ps_version < 1.7}
              <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" value="{$name[0]['cart_quantity']|escape:'htmlall':'UTF-8'}">
            {else}
              <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" value="{$name['quantity']|escape:'htmlall':'UTF-8'}">

              <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_quantity_{$id_product.id_product|escape:'htmlall':'UTF-8'}" value="{$name['quantity']|escape:'htmlall':'UTF-8'}">

            {/if}

            

            <input class="form-control" type="hidden" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_cart" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_id_cart" value="{$id_cart|escape:'htmlall':'UTF-8'}">

            <input class="form-control" type="text" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_customer_name" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_customer_name" value="{$customer_name|escape:'htmlall':'UTF-8'}">
    </div>
    <div class="col-lg-12">
          <label class="col-form-label">{l s='Customer Phone' mod='eventsmanager'}</label>
          
{* MODIFICATION SEULEMENT ICI - Récupérer le téléphone depuis l'adresse *}
{assign var="context" value=Context::getContext()}
{assign var="customer" value=$context->customer}
{assign var="customer_phone" value=""}

{if $customer->id}
    {* Chercher dans toutes les adresses du client *}
    {assign var="addresses" value=$customer->getAddresses($context->language->id)}
    {foreach from=$addresses item=addr}
        {if !empty($addr.phone_mobile)}
            {assign var="customer_phone" value=$addr.phone_mobile}
            {break}
        {elseif !empty($addr.phone)}
            {assign var="customer_phone" value=$addr.phone}
            {break}
        {/if}
    {/foreach}
{/if}

<input class="form-control" type="tel" name="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_customer_phone" id="event_info_{$id_product.event_id|escape:'htmlall':'UTF-8'}_customer_phone" value="{$customer_phone|escape:'htmlall':'UTF-8'}" required="true">
         </div>

{if $id_product.days}
<div class="col-lg-12">
  <label class="col-form-label">{l s='Confirm your presence for an Event (leave empty for all days)' mod='eventsmanager'}</label>
  <br>
  {for $count=1 to $id_product.days}
  
          
             <div class="pretty p-icon p-round p-pulse">
              
        <input type="checkbox" name="days_{$count|escape:'htmlall':'UTF-8'}_{$id_product.event_id|escape:'htmlall':'UTF-8'}[]" id="days_{$id_product.event_id|escape:'htmlall':'UTF-8'}" value="{$count|escape:'htmlall':'UTF-8'}" />
        <div class="state p-success">
            <i class="icon mdi mdi-check"></i>
            <label>{l s='Day ' mod='eventsmanager'}{$count|escape:'htmlall':'UTF-8'}</label>
        </div>
    </div>
    {/for}
</div>
{/if}
             

    <div style="text-align: center;">
    {if $isseatmap}
      {if $ps_version < 1.7}
      <button style="margin-top: 5px;" type="button" name="selectMap" id="select_seat_{$id_product.event_id|escape:'htmlall':'UTF-8'}" onclick="openSelectMapP({$id_product.event_id|escape:'htmlall':'UTF-8'},{$name[0]['cart_quantity']|escape:'htmlall':'UTF-8'}, {$id_product.id_product|escape:'htmlall':'UTF-8'}, {$id_product.event_product_id|escape:'htmlall':'UTF-8'});" class="btn btn-primary btn-xs" comp="">{l s='Select Seat' mod='eventsmanager'}</button>
      {else}
        <button style="margin-top: 5px;" type="button" name="selectMap" id="select_seat_{$id_product.event_id|escape:'htmlall':'UTF-8'}" onclick="openSelectMapP({$id_product.event_id|escape:'htmlall':'UTF-8'},{$name['quantity']|escape:'htmlall':'UTF-8'}, {$id_product.id_product|escape:'htmlall':'UTF-8'}, {$id_product.event_product_id|escape:'htmlall':'UTF-8'});" class="btn btn-primary btn-xs" comp="">{l s='Select Seat' mod='eventsmanager'}</button>
      {/if}
      
      <span id="timer_{$id_product.event_id|escape:'htmlall':'UTF-8'}"></span>
    {/if}
    </div>
   <div class="clearfix"></div>
   <br>
   </div>
{/foreach}
<input type="hidden" name="map_event_in_cart" id="map_event_in_cart" value="{$map_event_in_cart|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="products_in_cart" id="products_in_cart" value="{$products_in_cart|escape:'htmlall':'UTF-8'}">

<input type="hidden" name="events_in_cart" id="events_in_cart" value="{$events_in_cart|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="allow_order" id="allow_order" value="1" reserve="1">
<div style="text-align: center;">
<button class="btn btn-success" name="customer_info_submit" id="customer_info_submit">
        {l s='Update Details' mod='eventsmanager'}
   </button>

  <span style="color: green;display: none;" id="done_message"><i class="material-icons">done</i></span>
   </div>
<p id="error_info" class='alert alert-warning warning' style="margin-top: 10px;display: none;">{l s='Kindly Fill All Fields' mod='eventsmanager'}</p>
   <div id=result></div>
   <script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var updateBtn = document.getElementById('customer_info_submit');
        if (!updateBtn) return;

        // Vérifier tous les champs 'customer_name' et 'customer_phone' pour chaque événement
        var allFilled = true;
        var nameInputs = document.querySelectorAll('input[id*="_customer_name"]');
        var phoneInputs = document.querySelectorAll('input[id*="_customer_phone"]');

        // Vérifier que chaque champ nom ET téléphone est rempli (par index correspondant)
        for (var i = 0; i < phoneInputs.length; i++) {
            var nameValue = nameInputs[i] ? nameInputs[i].value.trim() : '';
            var phoneValue = phoneInputs[i] ? phoneInputs[i].value.trim() : '';
            
            if (!nameValue || !phoneValue) {
                allFilled = false;
                break; // On sort dès qu'un champ manque
            }
        }

        // Si tous les champs requis sont remplis, déclencher l'update automatique
        if (allFilled) {
            updateBtn.click();
        }
        // Sinon, on ne fait rien → l'utilisateur devra remplir manuellement
    }, 500);
});
</script>
</div>
{elseif $match_products}
<div class="front_panel">
  <h6 class="card-block" style="color: #13392a;">{l s='Login For Ticket Registration' mod='eventsmanager'}</h6>
</div>
{/if}
<div class="modal fade" id="openMap" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content" style="overflow: scroll;">
        <h2>{l s='Select Seat' mod='eventsmanager'}</h2>
        <div class="form-group row col-lg-12">
          <div id="show_map">
            
          </div>
          <!-- <input type="button" id="btnShowSeat" value="Show All" />

          <input type="button" id="btnShowNew" value="Show Selected Seats" />  -->   
        </div>
        <div class="clearfix"></div>
        <div class="form-group row col-lg-12">
          <button type="button" name="save_map" id="save_map" class="button btn btn-primary">
            <span>{l s='Confirm' mod='eventsmanager'}</span>
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

  <input type="hidden" name="wait_min" id="wait_min" value="{$wait_min|escape:'htmlall':'UTF-8'}">
  <input type="hidden" name="req_phone" id="req_phone" value="{$req_phone|escape:'htmlall':'UTF-8'}">
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
background-image:url("{$red_color|escape:'htmlall':'UTF-8'}") !important;          
}
#row_col_table .selectingSeat
{ 
background-image:url("{$green_color|escape:'htmlall':'UTF-8'}") !important;        
}
#row_col_table .fmm_disabled
{
background: #bdbdbd !important;
}
</style>