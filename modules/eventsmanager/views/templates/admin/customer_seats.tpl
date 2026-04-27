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
{if $seat_data}
   <div class="panel col-lg-12" id="seat_maphide">
      <div class="panel-heading">
         {l s='Seat Map' mod='eventsmanager'}
      </div>
      <span class="green_text">{l s='Not Availble' mod='eventsmanager'}</span>
         <div class="gray_div"></div>
         <span class="red_text">{l s='Booked Seats' mod='eventsmanager'}</span>
         <div class="red_div"></div>
         <input type="hidden" name="ajax_url" id="ajax_url" value="{$ajax_url|escape:'htmlall':'UTF-8'}">
         <input type="hidden" name="id_product" id="id_product" value="{$id_product|escape:'htmlall':'UTF-8'}">
         <input type="hidden" name="id_event" id="id_event" value="{$id_event|escape:'htmlall':'UTF-8'}">
   
      <table border="1" id="row_col_table2">
         {$seat_data} {* html content *}
      </table>

      <div class="form-group row col-lg-4 seat_color">
         <button style="float: left;" onclick="openBookedSelectMap();" type="button" class="button btn btn-primary">
               <span>{l s='Show Booked Seats' mod='eventsmanager'}</span>
            </button>

      </div>
   </div>

  
   <div class="table-responsive-row clearfix">
   <table id="table-fme_customer_details" class="table">
      <thead>
         <tr class="nodrag nodrop">
            <th class="">
               <span class="title_box active">
               {l s='ID' mod='eventsmanager'}
               </span>
            </th>
            <th class="">
               <span class="title_box">
               {l s='Customer Name' mod='eventsmanager'}
               </span>
            </th>
            <th class="">
               <span class="title_box">
                  {l s='Customer Phone' mod='eventsmanager'}
               </span>
            </th>
            <th class="">
               <span class="title_box">
                  {l s='Customer Seats' mod='eventsmanager'}
               </span>
            </th>
            <th class="">
               <span class="title_box">
                  {l s='Selected Days' mod='eventsmanager'}
               </span>
            </th>
         </tr>
      </thead>
      <tbody>
         {if $all_seatdata|@count > 0}
            {foreach from=$all_seatdata item=id_data}
            <tr>
               <td>{$id_data.id_events_customer|escape:'htmlall':'UTF-8'}</td>
               <td>{$id_data.customer_name|escape:'htmlall':'UTF-8'}</td>
               <td>{$id_data.customer_phone|escape:'htmlall':'UTF-8'}</td>
               <td>
                  {$id_data.reserve_seats_num|escape:'htmlall':'UTF-8'}
               </td>
               <td>
                  {if $id_data.days} 
                     <a class="trigger_fmm_pop" aa="{$id_data.id_events_customer|escape:'htmlall':'UTF-8'}"> 
                        {$id_data.days|escape:'htmlall':'UTF-8'}
                     </a> 
                  {else} 
                     {l s='All Days' mod='eventsmanager'}  
                  {/if}
               </td>
            </tr>

            <div class="hover_fmm_pk" id="hover_fmm_pk_{$id_data.id_events_customer|escape:'htmlall':'UTF-8'}">
               <span class="helper"></span>
               <div>
                  <div class="popup_close">X</div>
                  {assign var="sdates" value= Events::getEventSDate({$id_event|escape:'htmlall':'UTF-8'},{$id_data.days|escape:'htmlall':'UTF-8'})}
                  {assign var="count" value="1"}
                  
                  {foreach from=$sdates item=date}
                  <span id="day_{$count|escape:'htmlall':'UTF-8'}">
                     {$date|escape:'htmlall':'UTF-8'}<br>
                     {assign var="count" value="{$count|escape:'htmlall':'UTF-8'}"+1}
                  </span>
                  {/foreach}
                  <span style="font-size: x-large;color: #1fa11f;">
                     <p style="border: dashed;margin-top: 14px;">{$id_data.days|escape:'htmlall':'UTF-8'}</p>
                  </span>
               </div>
            </div>
            {/foreach}
         {else}
            <tr>
               <td class="list-empty" colspan="5">
                  <div class="list-empty-msg">
                     <i class="icon-warning-sign list-empty-icon"></i>
                     {l s='No records found' mod='eventsmanager'}
                  </div>
               </td>
            </tr>
         {/if}
      </tbody>
   </table>
</div>

{else}
   <div class="center" style="text-align: center;">{l s='No Record Found' mod='eventsmanager'}</div> 
{/if}
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