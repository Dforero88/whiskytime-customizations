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
<script type="text/javascript">
var tab_module = "{*$tab_module|escape:'htmlall':'UTF-8'*}";
</script>
<div class="bootstrap col-lg-2">
    <div class="custom-tab-eventmanager">
        <div class="eventmanager-page-head-tabs" id="eventmanager_head_tabs">
            <ul class="tab">
                <li class="tab-row" data-tab="configuration">
                    <a href="{$configure_link|escape:'htmlall':'UTF-8'}" class="config">
                        <i class="material-icons">build</i> 
                        {l s='Configuration' mod='eventsmanager'}
                    </a>
                </li>
                <li class="tab-row" data-tab="event_manage">
                    <a href="{$manage_events_link|escape:'htmlall':'UTF-8'}" class="event_manage">
                        <i class="material-icons">event</i> 
                        {l s='Manage Events' mod='eventsmanager'}
                    </a>
                </li>
                <li class="tab-row" data-tab="event_details">
                    <a href="{$events_details_link|escape:'htmlall':'UTF-8'}" class="event_details">
                        <i class="material-icons">event</i> 
                        {l s='Events Details' mod='eventsmanager'}
                    </a>
                </li>
                <li class="tab-row" data-tab="event_tags">
                    <a href="{$events_tags_link|escape:'htmlall':'UTF-8'}" class="event_tags">
                        <i class="material-icons">event</i> 
                        {l s='Events Tags' mod='eventsmanager'}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    const version = '{$version|escape:'htmlall':'UTF-8'}';

    if(version == '9.0.0'){
        const currentURL = window.location.href;

        const tabMap = {
            'configuration': 'configure/eventsmanager',
            'event_details': 'AdminEventsDetails',
            'event_manage': 'AdminEvents',
            'event_tags': 'AdminTags',
        };

        $.each(tabMap, function(tab, matchValue) {

            if (
            currentURL.includes('controller=' + matchValue) || 
            currentURL.includes(matchValue) // handles 'configure=eventsmanager'
            ) {
            $('[data-tab="' + tab + '"]').addClass('active');
            return false;
            }
        });
    } else{
        const urlParams = new URLSearchParams(window.location.search);
        const controller = urlParams.get('controller');
        
        const tabMap = {
            'configuration': 'AdminModules',
            'event_details': 'AdminEventsDetails',
            'event_manage': 'AdminEvents',
            'event_tags': 'AdminTags',
        };
        $.each(tabMap, function(tab, expectedController) {
                        console.log(controller, expectedController);

            if (controller === expectedController) {
                $('[data-tab="' + tab + '"]').addClass('selected');
                return false;
            }
        });
    }
});

</script>
