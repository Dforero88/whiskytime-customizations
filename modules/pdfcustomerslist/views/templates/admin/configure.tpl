{capture name=path}
    <span class="navigation-page">
        <a href="{$currentIndex|escape:'html':'UTF-8'}">
            {l s='Modules' mod='pdfcustomerslist'}
        </a>
        <i class="icon-angle-right"></i>
        <span>{l s='PDF Customers List' mod='pdfcustomerslist'}</span>
    </span>
{/capture}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-file-pdf-o"></i> {l s='Generate Customers PDF' mod='pdfcustomerslist'}
    </div>
    
    <form method="post" action="" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='Select Event' mod='pdfcustomerslist'}
            </label>
            <div class="col-lg-9">
                <select name="event_id" id="event_id" class="form-control fixed-width-xl">
                    <option value="0">{l s='-- Select an event --' mod='pdfcustomerslist'}</option>
                    {foreach from=$events item=event}
                        <option value="{$event.event_id|intval}" 
                                {if $selected_event == $event.event_id}selected="selected"{/if}>
                            {$event.event_title|escape:'html':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-lg-9 col-lg-offset-3">
                <button type="submit" name="select_event" class="btn btn-default">
                    <i class="icon-search"></i> {l s='Show Orders' mod='pdfcustomerslist'}
                </button>
                {if $selected_event > 0}
                    <button type="submit" name="generate_pdf" class="btn btn-primary">
                        <i class="icon-file-pdf-o"></i> {l s='Generate PDF' mod='pdfcustomerslist'}
                    </button>
                {/if}
            </div>
        </div>
    </form>
</div>

{if $selected_event > 0 && $event_info}
<div class="panel">
    <div class="panel-heading">
        <i class="icon-calendar"></i> {l s='Event Information' mod='pdfcustomerslist'}
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-3">
                <strong>{l s='Event Name' mod='pdfcustomerslist'}:</strong><br>
                {$event_info.event_title|escape:'html':'UTF-8'}
            </div>
            <div class="col-lg-3">
                <strong>{l s='Start Date' mod='pdfcustomerslist'}:</strong><br>
                {$event_info.event_start_date|escape:'html':'UTF-8'}
            </div>
            <div class="col-lg-3">
                <strong>{l s='Location' mod='pdfcustomerslist'}:</strong><br>
                {$event_info.event_venu|escape:'html':'UTF-8'}
            </div>
        </div>
    </div>
</div>

{if $orders_data}
<div class="panel">
    <div class="panel-heading">
        <i class="icon-shopping-cart"></i> {l s='Event Orders' mod='pdfcustomerslist'} 
        <span class="badge">{$orders_data|count}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Order Ref' mod='pdfcustomerslist'}</th>
                    <th>{l s='Order Date' mod='pdfcustomerslist'}</th>
                    <th>{l s='Customer Name' mod='pdfcustomerslist'}</th>
                    <th>{l s='Customer Email' mod='pdfcustomerslist'}</th>
                    <th>{l s='Phone' mod='pdfcustomerslist'}</th>
                    <th>{l s='Price/Ticket' mod='pdfcustomerslist'}</th>
                    <th>{l s='Qty' mod='pdfcustomerslist'}</th>
                    <th>{l s='Total' mod='pdfcustomerslist'}</th>
                    <th>{l s='Payment Method' mod='pdfcustomerslist'}</th>
                    <th>{l s='Status' mod='pdfcustomerslist'}</th>
                </tr>
            </thead>
            <tbody>
                {assign var="total_qty" value=0}
                {assign var="total_amount" value=0}
                
                {foreach from=$orders_data item=order}
                {assign var="total_qty" value=$total_qty+$order.quantity}
                {assign var="total_amount" value=$total_amount+$order.total}
                <tr>
                    <td>{$order.order_reference|escape:'html':'UTF-8'}</td>
                    <td>{$order.order_date|escape:'html':'UTF-8'}</td>
                    <td>{$order.customer_name|escape:'html':'UTF-8'}</td>
                    <td>{$order.customer_email|escape:'html':'UTF-8'}</td>
                    <td>{$order.customer_phone|escape:'html':'UTF-8'}</td>
                    <td>{displayPrice price=$order.price_per_ticket}</td>
                    <td>{$order.quantity|intval}</td>
                    <td>{displayPrice price=$order.total}</td>
                    <td>{$order.payment_method|escape:'html':'UTF-8'}</td>
                    <td>{$order.order_status|escape:'html':'UTF-8'}</td>
                </tr>
                {/foreach}
                
                {* Ligne des totaux *}
                <tr style="background-color: #f5f5f5; font-weight: bold;">
                    <td colspan="6" style="text-align: right;">
                        <strong>{l s='TOTALS:' mod='pdfcustomerslist'}</strong>
                    </td>
                    <td><strong>{$total_qty|intval}</strong></td>
                    <td><strong>{displayPrice price=$total_amount}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
{else}
<div class="alert alert-warning">
    {l s='No orders found for this event.' mod='pdfcustomerslist'}
</div>
{/if}
{/if}