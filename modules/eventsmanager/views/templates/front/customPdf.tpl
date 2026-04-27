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

<div class="row">
<div class="container">
  {if $eventData}
  <table class="table table-bordered">
    <tr>
      <td style="width:150px; text-align:center;">  
        <span>
          {if $eventData.event_image} 
          {$eventData.contact_photo = $eventData.event_image}
          {else}
          {$eventData.contact_photo = 'events/blank_icon.jpg'}
          {/if}
          <img style="height:100px;" class="img-circle" src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventData.contact_photo|escape:'htmlall':'UTF-8'}" /><br>
        </span><br>
        <span>{l s='From: ' mod='eventsmanager'}{$eventData.event_start_date|date_format:"%m/%d/%Y"|escape:'html':'UTF-8'}</span><br>
        <span>{l s='To: ' mod='eventsmanager'}{$eventData.event_end_date|date_format:"%m/%d/%Y"|escape:'html':'UTF-8'}</span><br>
      </td>
      <td style="width:10px; text-align:center;border-right:2px solid black;"></td>
      {* <td width='40%'>
      </td> *}
      <td style="width:120px; text-align:center;">
        <span>       
         <span> <img src="{$logo|escape:'htmlall':'UTF-8'}"></span>
          <span>{$eventData.event_start_date|date_format:"%m/%d/%Y"|escape:'html':'UTF-8'}</span><br>
          <span>{l s='Venue' mod='eventsmanager'}</span><br>
          <span>{$eventData.event_venu|escape:'htmlall':'UTF-8'}</span><br>
        </span>
      </td>
      {* <td width='40%'></td> *}
      <td style="width:150px; text-align:center;border-right:2px dashed grey;"></td>
      <td>
        {* <img src="https://chart.googleapis.com/chart?cht=qr&chs=75x75&chl={$onumber|escape:'url':'UTF-8'}&choe=UTF-8&chld=L|0" alt="{l s='QR Code' mod='eventsmanager'}" /> *}
        {assign var="order_url" value="{$link->getModuleLink('eventsmanager', 'eventTickets', ['order_id' => $order_id, 'id_event' => $eventData.event_id])}"}
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={$order_url|escape:'url':'UTF-8'}" alt="{l s='QR Code' mod='eventsmanager'}" />
        <br><span>&nbsp;{l s=' Ticket Number: ' mod='eventsmanager'}{$onumber|escape:'htmlall':'UTF-8'}</span>
      </td>
    </tr>
    
  </table>
  {/if}
  </div>

</div>