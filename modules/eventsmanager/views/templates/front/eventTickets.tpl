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

{extends file='page.tpl'}
{block name="page_content"}
<div class="fmm_block" style="overflow-x:auto;background: white;">
<h4 class="pdf_list_fmm" style="text-align: center;">{l s='Tickets Details' mod='eventsmanager'}</h4>
<table class="table table-hover" style="font-size: smaller;">
  <thead>
    <tr>
      <th scope="col">{l s='Name' mod='eventsmanager'}</th>
      <th scope="col">{l s='Quantity' mod='eventsmanager'}</th>
      <th scope="col">{l s='Ticket Price' mod='eventsmanager'}</th>
      <th scope="col">{l s='Order Status' mod='eventsmanager'}</th>
      <th scope="col">{l s='Event Start Date' mod='eventsmanager'}</th>
      <th scope="col">{l s='Event End Date' mod='eventsmanager'}</th>
      <th scope="col">{l s='Order Invoice' mod='eventsmanager'}</th>
      <th scope="col">{l s='Ticket PDF' mod='eventsmanager'}</th>
      <th scope="col">{l s='Google Map' mod='eventsmanager'}</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$alldata item=data}
     <tr>
          <td>{Events::getProductName($data.id_product|escape:'htmlall':'UTF-8')}</td>
          <td>{$data.quantity|escape:'htmlall':'UTF-8'}</td>
           <td>{$currency|escape:'html':'UTF-8'}{Product::getPriceStatic($data.id_product)|string_format:"%.2f"|escape:'html':'UTF-8'}</td>
          <td>
            {if $data.order_status == 0}
                <span class="order_state_red">{l s='Imperfection' mod='eventsmanager'}</span>
            {else}
                <span class="order_state_{$data.order_status|escape:'htmlall':'UTF-8'}">{Events::getOrderState($data.order_status|escape:'htmlall':'UTF-8', $id_lang|escape:'htmlall':'UTF-8')}</span>
            {/if}
            
          </td>
          <td>{$data.event_start_date|escape:'htmlall':'UTF-8'}</td>
          <td>{$data.event_end_date|escape:'htmlall':'UTF-8'}</td>
          <td>
            {if $data.order_status == 0 || $data.order_status == 1}
                
            {else}
                <a style="margin-top: -5px;" class="btn btn-default" href="{$link->getPageLink('pdf-invoice')}{* html content *}&id_order={$data.id_order|escape:'htmlall':'UTF-8'}">
                <i class="material-icons">move_to_inbox</i>
                </a>
            {/if}
            
            </td>
          <td>
            {if $data.order_status != 6 && $data.quantity >= 0}
                <span>{l s='' mod='eventsmanager'}</span>
            {elseif $data.order_status == 6 && $data.quantity <= 0}
                <span>{l s='' mod='eventsmanager'}</span>
            {else}
                <a style="margin-top: -5px;" class="btn btn-default" href="{$ajax_url|escape:'htmlall':'UTF-8'}&id_order={$data.id_order|escape:'htmlall':'UTF-8'}&id_event={$data.event_id|escape:'htmlall':'UTF-8'}"><i class="material-icons">cloud_download</i></a>
            {/if}
          </td>
          <td>
          {if $data.latitude AND $data.longitude}
            <button class="btn btn-outline-info btn-sm" href="#googleMap" id="show_mapp" onclick="myMap({$data.latitude|escape:'htmlall':'UTF-8'}, {$data.longitude|escape:'htmlall':'UTF-8'});">{l s='Show Map' mod='eventsmanager'}</button>
          {else}
            <!-- {l s='Nill' mod='eventsmanager'} -->
          {/if}
        </td>
        </tr>
{/foreach}
</tbody>
</table>
</div>
<input type="hidden" name="api_key" id="api_key" value="{$api_key|escape:'htmlall':'UTF-8'}">

<script src="https://maps.googleapis.com/maps/api/js?key={$api_key|escape:'htmlall':'UTF-8'}&callback=myMap"></script>


<div class="modal fade1" id="openMapgoogle" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content" style="overflow: scroll;">
        
            <div id="googleMap" style="width:100%;height:400px;"></div>

      </div>
      
    </div>
</div> 
{/block}

{block name='page_footer'}
  {block name='my_account_links'}
    {include file='customer/_partials/my-account-links.tpl'}
  {/block}
{/block}

<!-- AIzaSyDZy9an_zg5rzHz_yawWdXlMhj6b-PSdd0 -->