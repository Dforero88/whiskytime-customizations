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
<div class="panel col-lg-12">
   <div class="panel-heading">
      {l s='Events Details' mod='eventsmanager'}
      <input type="hidden" name="ajax_url" id="ajax_url" value="{$ajax_url|escape:'htmlall':'UTF-8'}">
   </div>
   {if $customer_data}
   <input id="search_fmmid" type="text" placeholder="Search..." style="float: right;width: 298px;">
   <div class="table-responsive-row clearfix">
      <table id="table-fme_customer_details" class="table">
         <thead>
            <tr class="nodrag nodrop">
               <th class="">
                  <span class="title_box active">
                  {l s='Order' mod='eventsmanager'}

                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                 {l s='Reference' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='Product' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='Quantity' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='Customer' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='Status' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='Admin Confirm' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='Cancel' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='PDF' mod='eventsmanager'}
                  </span>
               </th>
               <th class="">
                  <span class="title_box">
                   {l s='Delete' mod='eventsmanager'}
                  </span>
               </th>
            </tr>
         </thead>
         <tbody>

             {foreach from=$customer_data key=myId item=i}
            <tr>
                <td><a target="_blank" href="{Context::getContext()->link->getAdminLink('AdminOrders')}{* html link *}&id_order={$i.id_order|escape:'htmlall':'UTF-8'}&vieworder">{l s='View Detail' mod='eventsmanager'}</a></td>
                <td style="color: black">{FmeCustomerModel::getOrderRef($i.id_order|escape:'htmlall':'UTF-8')}</td>
                <td>{Events::getProductNameLang($i.id_product|escape:'htmlall':'UTF-8')}</td>
                <td>{$i.quantity|escape:'htmlall':'UTF-8'}</td>
                <td>{$i.customer_name|escape:'htmlall':'UTF-8'}</td>
                
                <td><span class="order_state_{$i.order_status|escape:'htmlall':'UTF-8'}">{Events::getOrderState($i.order_status|escape:'htmlall':'UTF-8', $id_lang|escape:'htmlall':'UTF-8')}</span></td>

                <td> <button id_customer="{$i.id_events_customer|escape:'htmlall':'UTF-8'}" class="btn btn-default ok_customer_data"  {if $i.admin_payment_confirm == 1} disabled="disabled" {/if}> {if $i.admin_payment_confirm == 1} <span style="color: green;"> {l s='confirmed' mod='eventsmanager'} </span>  {else} {l s='Mark as confirmed' mod='eventsmanager'} {/if}</button></td>

                <td> <button id_customer="{$i.id_events_customer|escape:'htmlall':'UTF-8'}" class="btn btn-default cancel_customer_data"><i class="icon-remove-sign"></i></button></td>
                <td>
                  {if $i.order_status == 999}
                  <a class="btn btn-default" href="{$ajax_url|escape:'htmlall':'UTF-8'}&id_product={$i.id_product|escape:'htmlall':'UTF-8'}&id_event_customer={$i.id_events_customer|escape:'htmlall':'UTF-8'}&id_order_admin=1"><i class="icon-download"></i></a>
                  {else}
                    <a class="btn btn-default" href="{$ajax_url|escape:'htmlall':'UTF-8'}&id_order={$i.id_order|escape:'htmlall':'UTF-8'}""><i class="icon-download"></i></a>
                  {/if}
                    </td>
                <td>
                    <button id_customer="{$i.id_events_customer|escape:'htmlall':'UTF-8'}" class="btn btn-default delete_customer_data"><i class="icon-trash"></i></button>
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
      </table>
   </div>
   {else}
   <div class="center" style="text-align: center;">{l s='No Record Found' mod='eventsmanager'}</div> 

   {/if}
</div>