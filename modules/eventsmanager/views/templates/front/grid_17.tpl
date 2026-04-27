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
{if $default_theme eq 0}

<div class="compojoom-bootstrap">
<h3 class="event-detail-heading events_list_title">{$meta_title|escape:'htmlall':'UTF-8'}</h3>
<div id="mat_events" class="col-md-9 col-lg-12">
  
    {if $nbEvents > 0}

    {foreach from=$events item=_event name=dataevent}

    <div class="row">
    <div class="mat_event_single_holder col-sm-12 mat_event_single_even">
      <div class="mat_event_single_holder_inner">
        <div class="row">
          <div class="mat_event_image_holder col-sm-2" style="max-height: 169px;{if $version < 1.6} width: 35%; float: left;{/if}">
            <div class="mat_event_image_holder_inner">
              {assign var=params value=['event_id'=> $_event.event_id, 'eventslink'=> $_event.event_permalinks]}
              {assign var=event_link value= $link->getModuleLink('eventsmanager', 'detail', $params)|escape:'htmlall':'UTF-8'}
              {if $_event.event_image} 
                {$_event.contact_photo = $_event.event_thumb_image}
              {else}
                {$_event.contact_photo = 'events/blank_icon.jpg'}
              {/if}
              <a href="{$event_link}{* HTML CONTENT *}"><img class="mat_event_img img-thumbnail" src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$_event.contact_photo|escape:'htmlall':'UTF-8'}" border="0" /></a>
            </div>
          </div>
          <div class="mat_event_content col-sm-10 clearfix" {if $version < 1.6} style="float: right; width: 65%;"{/if}>
            <h3 ><a href="{$event_link}{* HTML CONTENT *}">{if !empty($_event.event_title)}{$_event.event_title|escape:'htmlall':'UTF-8'}{/if}</a></h3>
            <div class="mat_event_top_informations" {if $version < 1.6}style="border-bottom:1px solid #f1f1f1;"{/if}>
              {if !empty($_event.contact_name)}<span class="mat_event_organizer">
                <i class="icon-user"></i>
                {if isset($_event.contact_name)}<strong>{l s='Contact Person' mod='eventsmanager'}: </strong>{$_event.contact_name|escape:'htmlall':'UTF-8'}{/if}
              </span>{/if}
              {if $version < 1.6}<br/>{/if}
              {if !empty($_event.contact_email)}<span class="mat_event_email">
                <i class="icon-envelope"></i>
                {if isset($_event.contact_email)}<strong>{l s='Email' mod='eventsmanager'}: </strong>{$_event.contact_email|escape:'htmlall':'UTF-8'}{/if}
              </span>{/if}
              {if $version < 1.6}<br/>{/if}
              {if !empty($_event.contact_phone)}<span class="mat_event_phone">
                <i class="icon-phone"></i>
                {if isset($_event.contact_phone)}<strong>{l s='Phone' mod='eventsmanager'}: </strong>{$_event.contact_phone|escape:'htmlall':'UTF-8'}{/if}
              </span>{/if}
            </div>
            <div class="mat_event_date">
              <i class="icon-calendar"></i>
              {l s='From' mod='eventsmanager'} : <strong>{$_event.event_start_date|date_format:"%A, %B %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0} {l s='at' mod='eventsmanager'} {$_event.event_start_date|date_format:"%H:%M:%S"|escape:'htmlall':'UTF-8'}{/if}</strong>
            </div>
            <div class="mat_event_date">
              <i class="icon-calendar"></i>
              {l s='To' mod='eventsmanager'} : <strong>{$_event.event_end_date|date_format:"%A, %B %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0} {l s='at' mod='eventsmanager'} {$_event.event_end_date|date_format:"%H:%M:%S"|escape:'htmlall':'UTF-8'}{/if}</strong>
            </div>
            <div class="mat_event_location">
              <i class="icon-flag"></i>
              {l s='Venu' mod='eventsmanager'} : <strong>{$_event.event_venu|escape:'htmlall':'UTF-8'}</strong>
            </div>
            
            <div class="mat_event_buttons">
              <a class="btn btn-primary mat_btn_details {if $version < 1.6}button{/if}" href="{$event_link}{* HTML CONTENT *}">{l s='Event details' mod='eventsmanager'}</a>   
            </div>
          </div>
          <div style="clear:both;"></div>       
        </div>
      </div>
         </div>
     </div>
    {/foreach}
    {if $pages_nb > 1}
      {$display_pages nofilter}{*HTML Content*}
    {/if}
    <div style="clear:both; margin-top:10px;"></div>
    {if $ps_ver >= 1}
    <div class="mat_event_buttons">
      <a class="btn btn-primary mat_btn_details" href="{$calender_link|escape:'htmlall':'UTF-8'}">{l s='Event Calendar' mod='eventsmanager'}</a>    
    </div>
    {/if}
    {/if}
</div>
{literal}
<script type="text/javascript">
function trigThisLoc(e)
{
  alert(e);
}
</script>
{/literal}
</div>

{else}
<div id="wrapper">
  <div class="event_manager">
    <div class="inner_container">
      <div class="page_title">
        <h1>{$meta_title|escape:'htmlall':'UTF-8'}</h1>
         {if $enable_tags}
          <div style="border-left:none;" class="event-detail-heading event-detail-tags">
            <h4>{l s='Filters' mod='eventsmanager'}</h4>
            {foreach from=$tags item=tag}
              {assign var=params value=['id_tag'=> $tag.id_fme_tags, 'tags'=> $tag.friendly_url]}
              {$tag_link = $link->getModuleLink('eventsmanager', 'eventstag', $params)|escape:'htmlall':'UTF-8'}
              <span class="event-detail-tag">
                <a {if !empty($selected_event) AND $selected_event.id_fme_tags eq $tag.id_fme_tags} style="border:2px solid green;" {/if} href="{$tag_link}{if isset($show_value)}?show={$show_value|escape:'htmlall':'UTF-8'}{/if}">{$tag.name|escape:'htmlall':'UTF-8'}</a>
              </span>
            {/foreach}
            {if !empty($selected_event) AND $selected_event}
              <span class="event-detail-tag" style="float: right;">
                  <a href="{$events_page_link|escape:'htmlall':'UTF-8'}{if isset($show_value)}?show={$show_value|escape:'htmlall':'UTF-8'}{/if}">
                    <i class="material-icons" style="font-size: 20px;">
                      clear
                    </i>
                    {$selected_event.name|escape:'htmlall':'UTF-8'}
                  </a>
              </span>
            {/if}
          </div>
      {/if}
      </div>
       <div class="toolbar clearfix">
        <div class="modes">
        <div class="calendar"> <a href="{$calender_link|escape:'htmlall':'UTF-8'}"><img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}modules/eventsmanager/views/img/images/calendar_icon.png" alt=""> {l s='Calendar' mod='eventsmanager'} </a> </div>

        <div class="view_mode"> {l s='View Mode' mod='eventsmanager'}<a href="{$grid_link|escape:'htmlall':'UTF-8'}"><img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}modules/eventsmanager/views/img/images/grid_view.png" alt=""></a> <a href="{$list_link|escape:'htmlall':'UTF-8'}"><img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}modules/eventsmanager/views/img/images/list_view.png" alt=""></a> </div>

        
        <div class="sort_by total_events"> <p>{l s='Total Events: ' mod='eventsmanager'}{$nbEvents|escape:'htmlall':'UTF-8'} </p>  </div>

      </div>

        <div class="sort_by"><label class="sorter-label" for="sorter">{l s='Sort By ' mod='eventsmanager'}</label>
          <select id="sorter" data-role="sorter" class="sorter-options">
            
              <option onclick="fmmSortDate();" id="sortdate" value="event_end_date">
                 {l s='Date' mod='eventsmanager'}            
              </option>
              <option onclick="fmmSortName();" id="sortname" value="event_name">
                {l s='Event Name' mod='eventsmanager'}            
              </option>
          </select>
        </div>
      </div>
      <div class="product_grid">
         <ul id="gridli" class="fmm-sort-events">
          {if $nbEvents > 0}

          {foreach from=$events item=_event name=dataevent}
          {if $isTagPage AND $enable_tags}
            {if ($_event.event_id|in_array:$filtered_events)}
                <li class="grid fmm-sorted-items" data-ex="{$_event.event_title|escape:'htmlall':'UTF-8'}" data-event-date="{$_event.event_start_date|escape:'htmlall':'UTF-8'}">
                  {assign var=params value=['event_id'=> $_event.event_id, 'eventslink'=> $_event.event_permalinks]}
                  {assign var=event_link value= $link->getModuleLink('eventsmanager', 'detail', $params)|escape:'htmlall':'UTF-8'}
                      {if $_event.event_image} 
                        {$_event.contact_photo = $_event.event_image}
                      {else}
                        {$_event.contact_photo = 'events/blank_icon.jpg'}
                      {/if}

                    <div class="event_block">
                      <div class="event_title">
                        <h2><a href="{$event_link}{* HTML CONTENT *}">{if !empty($_event.event_title)}{$_event.event_title|escape:'htmlall':'UTF-8'}{/if}</a></h2>
                      </div>
                      <div class="event_thumbnail">  <a href="{$event_link}{* HTML CONTENT *}"><img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$_event.contact_photo|escape:'htmlall':'UTF-8'}" alt=""></a> </div>
                      <div class="event_detail">

                        <p><span>{l s='From: ' mod='eventsmanager'}</span> {$_event.event_start_date|date_format:"%b %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0} {$_event.event_start_date|date_format:"g:i:s A"|escape:'htmlall':'UTF-8'}{/if}<br>
                          <span>{l s='To: ' mod='eventsmanager'}</span> {$_event.event_end_date|date_format:"%b %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0}  {$_event.event_end_date|date_format:"g:i:s A"|escape:'htmlall':'UTF-8'}{/if}<br>
                          <span>{l s='Venue: ' mod='eventsmanager'}</span> {$_event.event_venu|escape:'htmlall':'UTF-8'}
                          </p>

                          
                          <div class="learn_more">
                            <a href="{$event_link}{* HTML CONTENT *}">{l s='Learn More ..' mod='eventsmanager'}</a> 
                          </div>
                          
                      </div>
                    </div>

                  </li>
                {/if}
              {else}
                <li class="grid" data-ex="{$_event.event_title|escape:'htmlall':'UTF-8'}" data-event-date="{$_event.event_start_date|escape:'htmlall':'UTF-8'}">
                {assign var=params value=['event_id'=> $_event.event_id, 'eventslink'=> $_event.event_permalinks]}
                {assign var=event_link value= $link->getModuleLink('eventsmanager', 'detail', $params)|escape:'htmlall':'UTF-8'}
                {if $_event.event_image} 
                  {$_event.contact_photo = $_event.event_image}
                {else}
                  {$_event.contact_photo = 'events/blank_icon.jpg'}
                {/if}

              <div class="event_block">
                <div class="event_title">
                  <h2><a href="{$event_link}{* HTML CONTENT *}">{if !empty($_event.event_title)}{$_event.event_title|escape:'htmlall':'UTF-8'}{/if}</a></h2>
                </div>
                <div class="event_thumbnail">  <a href="{$event_link}{* HTML CONTENT *}"><img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$_event.contact_photo|escape:'htmlall':'UTF-8'}" alt=""></a> </div>
                <div class="event_detail">
                  <p><span>{l s='From: ' mod='eventsmanager'}</span> {$_event.event_start_date|date_format:"%b %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0} {$_event.event_start_date|date_format:"g:i:s A"|escape:'htmlall':'UTF-8'}{/if}<br>
                    <span>{l s='To: ' mod='eventsmanager'}</span> {$_event.event_end_date|date_format:"%b %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0}  {$_event.event_end_date|date_format:"g:i:s A"|escape:'htmlall':'UTF-8'}{/if}<br>
                    <span>{l s='Venue: ' mod='eventsmanager'}</span> {$_event.event_venu|escape:'htmlall':'UTF-8'}
                    </p>
                    <div class="learn_more">
                      <a href="{$event_link}{* HTML CONTENT *}">{l s='Learn More ..' mod='eventsmanager'}</a> 
                    </div>
                    
                </div>
              </div>

          </li>  
              
              
            {/if}  

         {/foreach}
        {if $pages_nb > 1}
            {$display_pages nofilter}{*HTML Content*}
        {/if}
          {/if}
        </ul>

      </div>
    </div>
  </div>
</div>


{/if}
{literal}
<style>
.event-detail-heading.event-detail-tags {
  display: block;
  min-height: 87px;
  margin-bottom: .5rem;
}
span.event-detail-tag a {
    background-color: #0000;
    border: 2px solid #666;
    display: inline-block;
    letter-spacing: 1px;
    margin: 0 10px 10px 0;
    padding: 5px 10px;
    /* text-transform: uppercase; */
    float: left;
}
</style>
{/literal}
{/block}