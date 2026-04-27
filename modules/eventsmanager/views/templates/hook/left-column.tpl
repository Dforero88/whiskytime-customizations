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

<!-- Block Topics module -->
<div id="events_block_left" class="block blockevents{if $ps_ver >= 1} fmm_ps_17{/if}">
	<h4 class="title_block text-uppercase"><a href="{$link->getModuleLink('eventsmanager', 'events')|escape:'htmlall':'UTF-8'}" title="{l s='Events' mod='eventsmanager'}">{l s='Events' mod='eventsmanager'}</a></h4>
	<div class="block_content" style="margin-top:-10px">
{if $leftdata}
	<div class="event_item" style="margin-top:12px;overflow:hidden;">
		<div id="fme-events-list" class="slider-pro">
			<div class="sp-slides">
			{foreach from=$leftdata item=_event name=dataevent}
			{assign var=params value=['event_id'=> $_event.event_id, 'eventslink'=> $_event.event_permalinks]}
            {assign var=event_link value= $link->getModuleLink('eventsmanager', 'detail', $params)|escape:'htmlall':'UTF-8'}
			<div class="sp-slide">
				<div class="event-content" style="width:100%;">
					<div style="width: 100%;text-align: center;">
						<a href="{$event_link}{* html content *}" title="{$_event.event_title|escape:'htmlall':'UTF-8'}"><img style="max-width: 100%;" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}img/{$_event.event_image|escape:'htmlall':'UTF-8'}"></a>
					</div>
						<strong style="margin-top:10px; margin-bottom:10px;"><a href="{$event_link}{* html content *}" title="{$_event.event_title|escape:'htmlall':'UTF-8'}">{$_event.event_title|escape:'htmlall':'UTF-8'}</a></strong>
						<p>{$_event.event_meta_description|truncate:100:"..."|escape:'htmlall':'UTF-8'}</p>
						<span>
							<a class="btn btn-default" style="color:#00BFFF;float: right;" href="{$event_link}{* html content *}" title="{$_event.event_title|escape:'htmlall':'UTF-8'}">{l s='Read More..' mod='eventsmanager'}</a>
						</span><br/><br/>
					<div style="background-color:#f0f0f0; padding-top:5px; padding-left:10px;padding-right:10px;padding-bottom:6px; margin-bottom:5%;">
						{$_event.event_start_date|date_format:"%B %e, %Y"|escape:'htmlall':'UTF-8'}&nbsp;
						<span>{l s='To' mod='eventsmanager'}</span>&nbsp;{$_event.event_end_date|date_format:" %B %e, %Y"|escape:'htmlall':'UTF-8'}
						<br />
						<div style="text-align:left">{$_event.event_venu|escape:'htmlall':'UTF-8'}</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			{/foreach}
		</div>
		<div class="clearfix"></div>
	</div>
		<!-- View All -->
		<div class="view_all">
			<a class="btn btn-primary mat_btn_book {if $version < 1.6}button{/if}" style="{if $version > 1.6}float:right;{/if}margin-top:10px;" href="{$link->getModuleLink('eventsmanager', 'events')|escape:'htmlall':'UTF-8'}" title="{l s='Events' mod='eventsmanager'}">
				<span >{l s='View All Events' mod='eventsmanager'}</span>
			</a>
		</div>
{literal}
<script type="text/javascript">
var nbr = "{/literal}{count($leftdata)|intval}{literal}"
</script>
{/literal}
	</div>    
{else}
	<p>{l s='No Events' mod='eventsmanager'}</p>
{/if}
	</div>
</div>
<div class="clearfix"></div>
<!-- /Block Topics module -->
