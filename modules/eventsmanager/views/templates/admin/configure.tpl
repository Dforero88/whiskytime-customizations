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
<div id='events_details_form'>

   {include file='module:eventsmanager/views/templates/admin/menu.tpl'}
</div>
<div class="col-lg-10"> 

   <input type="hidden" name="ajax_url" id="ajax_url" value="{$ajax_url|escape:'htmlall':'UTF-8'}">
   <div class="panel col-lg-12">
      <div class="panel-heading">
         {l s='Events Details' mod='eventsmanager'}
      </div>
      
      <div class="table-responsive-row clearfix">
         <table id="table-fme_events_details" class="table">
            <thead>
               <tr class="nodrag nodrop">
                  <th class="">
                     <span class="title_box active">
                     {l s='ID' mod='eventsmanager'}
                     </span>
                  </th>
                  <th class="">
                     <span class="title_box">
                     {l s='Event' mod='eventsmanager'}
                     </span>
                  </th>
                  <th class="">
                     <span class="title_box">
                     {l s='Start' mod='eventsmanager'}
                     </span>
                  </th>
                  <th class="">
                     <span class="title_box">
                     {l s='End' mod='eventsmanager'}
                     </span>
                  </th>
                  <th class="">
                     <span class="title_box">
                     {l s='Seat Map' mod='eventsmanager'}
                     </span>
                  </th>
                  <th class="">
                     <span class="title_box">
                     {l s='Status' mod='eventsmanager'}
                     </span>
                  </th>
                  <th class="center">
                     <span class="title_box">
                     {l s='View' mod='eventsmanager'}
                     </span>
                  </th>
                  <th></th>
               </tr>
            </thead>
            {if $all_booking_data}
            <tbody>
               {foreach from=$all_booking_data key=myId item=i}
               <tr>
                  <td>{$i.event_id|escape:'htmlall':'UTF-8'}-{$i.id_product|escape:'htmlall':'UTF-8'}</td>
                  <td>{$i.event_title|escape:'htmlall':'UTF-8'}</td>
                  <td>{$i.event_start_date|escape:'htmlall':'UTF-8'}</td>
                  <td>{$i.event_end_date|escape:'htmlall':'UTF-8'}</td>

                  <td>{if $i.seat_selection == '1'}
                     <i class="material-icons action-enabled" style="color: green;">check</i>
                     {else}
                     <i class="material-icons action-disabled" style="color: red;">clear</i>
                     {/if} 
                  </td>

                  <td>{if $i.event_status == '1'}
                     <i class="material-icons action-enabled" style="color: green;">check</i>
                     {else}
                     <i class="material-icons action-disabled" style="color: red;">clear</i>
                     {/if} 
                  </td>

                  <td style="text-align: center;">
                     <button type="button" id="event_{$i.event_id|escape:'htmlall':'UTF-8'}" onclick="window.location.href = '{$customers_path}{* html content *}&event_id={$i.event_id|escape:'htmlall':'UTF-8'}&id_product={$i.id_product|escape:'htmlall':'UTF-8'}';" class="btn btn-info btn-sm">{l s='Order Detail' mod='eventsmanager'}</button>
                     {if $i.seat_selection == '1'}
                     <button type="button" id="event_{$i.event_id|escape:'htmlall':'UTF-8'}" onclick="window.location.href = '{$seatmap_path}{* html content *}&event_id={$i.event_id|escape:'htmlall':'UTF-8'}&id_product={$i.id_product|escape:'htmlall':'UTF-8'}';" class="btn btn-success btn-sm">{l s='Seat Map' mod='eventsmanager'}</button>
                     <button style="float: left;" onclick="reserveBookedSelectMap({$i.event_id|escape:'htmlall':'UTF-8'},{$i.id_product|escape:'htmlall':'UTF-8'}, {$i.event_product_id|escape:'htmlall':'UTF-8'}, {if isset($i.quantity)}{$i.quantity|escape:'htmlall':'UTF-8'}{/if});" type="button" class="button btn btn-primary">
                     <span>{l s='Reservation' mod='eventsmanager'}</span>
                     </button>
                     {/if}
                     

                  </td>

                  
               </tr>
               {/foreach}

               <!-- <tr>
                  <td class="list-empty" colspan="7">
                     <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        No records found
                     </div>
                  </td>
               </tr> -->
            </tbody>
            {else}
            <tbody>
         <tr>
            <td class="list-empty" colspan="7">
               <div class="list-empty-msg">
               <i class="icon-warning-sign list-empty-icon"></i>
               {l s=' No records found' mod='eventsmanager'}
               </div>
            </td>
         </tr>
         </tbody>
            {/if}
         </table>
      </div>
      
   </div>

   <div class="modal fade" id="openMap" role="dialog">
      <div class="modal-dialog">
      
         <!-- Modal content-->
         <div class="modal-content" style="overflow: scroll;">
         <h2>{l s='Select Seat' mod='eventsmanager'}</h2>
         <div class="form-group row col-lg-12">
            <div id="show_map" style="margin-left: 5px;margin-right: 5px;">
               
            </div>
            <!-- <input type="button" id="btnShowSeat" value="Show All" />

            <input type="button" id="btnShowNew" value="Show Selected Seats" />  -->   
         </div>
         <div class="clearfix"></div>
         <div class="form-group row col-lg-12" style="margin-left: 1px;">
            <div class="col-lg-4">
            <label class="col-form-label required">{l s='Customer Name' mod='eventsmanager'}</label>
            <input class="form-control" type="text" name="event_info_customer_name" id="event_info_customer_name" >
            </div>
            <div class="col-lg-4">
            <label class="col-form-label required">{l s='Email' mod='eventsmanager'}</label>
            <input class="form-control" type="text" name="event_info_customer_email" id="event_info_customer_email" >
            </div>
            <div class="col-lg-4">
            <label class="col-form-label required">{l s='Phone' mod='eventsmanager'}</label>
            <input class="form-control" type="text" name="event_info_customer_phone" id="event_info_customer_phone" >
            </div>
            <div class="col-lg-4" style="margin-top: 20px;">
            <button type="button" name="save_map" id="save_map" onclick="save_map()" class="button btn btn-primary">
               <span>{l s='Save' mod='eventsmanager'}</span>
            </button>
            </div>
               <!-- <button type="button" name="closemodal" data-dismiss="modal" id="closemodal" class="button btn btn-danger">
               <span>{l s='Close' mod='eventsmanager'}</span>
            </button> -->
         </div>
         <div class="clearfix"></div>
            <span id="update_message" style="display: none;color: #6bad24;margin-left: 11px;">{l s='Updated Successfully' mod='eventsmanager'}</span>
            <span id="error_message" style="display: none;color: red;margin-left: 11px;">{l s='Kindly select seat first' mod='eventsmanager'}</span>
            <span id="fill_message" style="display: none;color: red;margin-left: 11px;">{l s='Seats are Already Reserved' mod='eventsmanager'}</span>
            <span id="req_message" style="display: none;color: red;margin-left: 11px;">{l s='Kindly Fill All Fields' mod='eventsmanager'}</span>

         </div>
         
      </div>
   </div>
</div>
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
