{*
*
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domain
*}

<div class="panel box card">
    <h2 class="panel-heading card-header"><i class="icon-truck"></i> <i class="material-icons">store</i> {l s='Events Order History' mod='eventsmanager'}</h2>
    <table class="table table-striped table-borderless">
        <thead>
            <tr>
                <th style="width: 45%;">{l s='Field' mod='eventsmanager'}</th>
                <th style="width: 55%;">{l s='Details' mod='eventsmanager'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $events_data as $event_data}
                <tr>
                    <td><strong>{l s='Customer Phone' mod='eventsmanager'}</strong></td>
                    <td>{$event_data.customer_phone|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr>
                    <td><strong>{l s='Days' mod='eventsmanager'}</strong></td>
                    <td>{$event_data.days|escape:'htmlall':'UTF-8'}</td>
                </tr>
                {if isset($event_data.reserve_seats) AND $event_data.reserve_seats}
                    <tr>
                        <td><strong>{l s='Total Reserved Seats' mod='eventsmanager'}</strong></td>
                        <td>
                            {assign var="reserve_seats" value=$event_data.reserve_seats|escape:'htmlall':'UTF-8'}
                            {assign var="seat_count" value=','|explode:$reserve_seats|@count}
                            {$seat_count} {l s='(Seat Numbers: ' mod='eventsmanager'} {$reserve_seats} {l s=')' mod='eventsmanager'}
                        </td>
                    </tr>
                {/if}
            {/foreach}
        </tbody>
    </table>
</div>