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


{capture name=path}<a href="{$link->getModuleLink('eventsmanager', 'events')|escape:'htmlall':'UTF-8'}">{l s='Events' mod='eventsmanager'}</a> | <a href="{$link->getModuleLink('eventsmanager', 'events?show=calendar')|escape:'htmlall':'UTF-8'}">{l s='Calendar' mod='eventsmanager'}</a>{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<script type="text/javascript" src="https://w.sharethis.com/button/buttons.js"></script>
<style>
.event-detail-heading.event-detail-tags {
  display: block;
  min-height: 100px;
  margin-bottom: .5rem;
}
span.event-detail-tag a {
    background-color: #0000;
    border: 2px solid #666;
    display: inline-block;
    letter-spacing: 1px;
    margin: 0 10px 10px 0;
    padding: 5px 10px;
    text-transform: uppercase;
    float: left;
}
.event-detail-heading.theme2-event-detail-tags {
display: block;
min-height: 50px;
margin-top: -1.5rem;
}
span.theme2-event-detail-tag a {
   background-color: #0000;
   /* border: 2px solid #666; */
   display: inline-block;
   letter-spacing: 1px;
   margin: -2.5% -6% 1% 7%;
   /* padding: 5px 10px; */
   text-transform: uppercase;
   float: left;
}
</style>
{if $default_theme eq 0}

{if $events_map_hover_address eq 1}
{literal}
<script type="text/javascript">
   var unavailable = "{/literal}&nbsp;{l s='Ticket(s) not Available at the moment' mod='eventsmanager'}{literal}";
   var static_map_key = "{/literal}{$EVENTS_META_MAPKEY|escape:'htmlall':'UTF-8'}{literal}";
</script>

{/literal}
{/if}
<div class="fme-bootstrap">
	<div id="ticket-unavailable" class="alert alert-danger error" style="display:none;"></div>
	<div class="eventviewDIV">
		<h3 class="event-detail-heading">{$eventData.event_title|escape:'htmlall':'UTF-8'}</h3>
      {if $enable_tags}
         <div class="event-detail-heading event-detail-tags">
            <h4>{l s='Event Tag' mod='eventsmanager'}</h4>
            {foreach from=$eventTags item=tag}
               {assign var=params value=['id_tag'=> $tag.id_fme_tags, 'tags'=> $tag.friendly_url]}
               <span class="event-detail-tag">
                     <a href="{$link->getModuleLink('eventsmanager', 'eventstag', $params)|escape:'htmlall':'UTF-8'}">{$tag.name|escape:'htmlall':'UTF-8'}</a>
               </span>
            {/foreach}
         </div>
      {/if}
   </div>
	<div class="mat_event_content col-sm-8 clearfix">
		<p style="font-size: small;color: #777;float:left;">
			<strong><i class="icon-calendar"></i> {l s='From' mod='eventsmanager'} :</strong> {$eventData.event_start_date|date_format:"%A, %B %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0} {l s='at' mod='eventsmanager'} {$eventData.event_start_date|date_format:"%H:%M:%S"|escape:'htmlall':'UTF-8'}{/if}
			<br/><strong><i class="icon-calendar"></i> {l s='To' mod='eventsmanager'} :</strong> {$eventData.event_end_date|date_format:"%A, %B %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0} {l s='at' mod='eventsmanager'} {$eventData.event_end_date|date_format:"%H:%M:%S"|escape:'htmlall':'UTF-8'}{/if}
         <br /><strong><i class="icon-map-marker"></i>   {l s='Venue' mod='eventsmanager'} : </strong>
			
         <span class="mapThis" place="{$eventData.event_venu|escape:'htmlall':'UTF-8'}" zoom="10">{$eventData.event_venu|escape:'htmlall':'UTF-8'}</span>
		</p>
	</div>
   {if $stream_on eq 1}
      {if $eventData.event_streaming_start_time le $eventData.event_streaming_end_time }
      <div class="mat_event_content col-sm-4 clearfix">
         <p style="font-size: small;color: #777;float:left;">
            <a href="{$streaming_video|escape:'htmlall':'UTF-8'}" autoplay="true" target="_blank" class="btn btn-primary">{l s='See Live Streaming' mod='eventsmanager'}</a>
            <br><strong style="line-height: 1.25em;"><i class="icon-calendar"></i> {l s='This Streaming is available from' mod='eventsmanager'} :</strong> {$eventData.event_streaming_start_time|escape:'htmlall':'UTF-8'}
            <strong>{l s= 'To' mod='eventsmanager'}:</strong> {$eventData.event_streaming_end_time|escape:'htmlall':'UTF-8'}
         </p>
      </div>
      {else}
         <p style="font-size: small;color: #777;float:left;">
            <strong><i class="icon-calendar"></i>{l s='This streaming is no longer available' mod='eventsmanager'}</strong>
         </p>
      {/if}   
   {/if}
	   <br style="clear: both;"/>
	   <!-- Ticketing -->
	   {if !empty($eproduct)}
		<div class="ticket_section">
			<h3 class="event-detail-heading">{l s='Tickets' mod='eventsmanager'}</h3>
			<table width="100%" border="0">
				<thead>
					<tr>
						<th colspan="4">&nbsp;</th>
					</tr>
				</thead>
				<body>
					<body>
						{foreach from=$eproduct item=product}

						<tr class="mat_event_single_holder">
							<td align="center"><img src="{$link->getImageLink($product->link_rewrite, $cover[$product->id].id_image, 'small_default')|escape:'htmlall':'UTF-8'}"></td>
							<td align="center">{$product->name|escape:'htmlall':'UTF-8'}</td>
							<td align="center"></strong>{convertPrice price=$product->price}</td>
							{if $product->available_for_order eq 1 AND $product->quantity gt 0}

							{if $is_seat_map}

										<td align="center">
                  </td>
                  <td align="center">
                     <a class="btn btn-primary mat_btn_book {if $version < 1.6}button{/if}" href="{$link->getProductLink($product->id)|escape:'htmlall':'UTF-8'}" >{l s='Buy Ticket' mod='eventsmanager'}</a>&nbsp;
                  </td>

						{else}

									<td align="center">
                     <div class="qty">
                    <div class="input-group bootstrap-touchspin" style="float: right;">
                       <input type="number" value="1" class="js-cart-line-product-quantity form-control qty_pro_fmm{$product->id|escape:'htmlall':'UTF-8'}" min="1" aria-label="Quantity" style="display: block;">
                       <input type="hidden" name="fmm_cartlink" id="fmm_cartlink" value="{$link->getPageLink('cart', true, NULL, "token={$static_token|escape:'htmlall':'UTF-8'}&amp;add=1")|escape:'htmlall':'UTF-8'}"">
                       <a onclick="fmmdown('{$product->id|escape:'htmlall':'UTF-8'}')" data-field-qty="qty" class="btn btn-default button-minus product_quantity_down">
							<span><i class="icon-minus"></i></span>
						</a>
						<a onclick="fmmup('{$product->id|escape:'htmlall':'UTF-8'}')" data-field-qty="qty" class="btn btn-default button-plus product_quantity_up">
							<span><i class="icon-plus"></i></span>
						</a>

                    </div>
                    </div>
                  </td>
                  <td align="center">
                     <a class="btn btn-primary mat_btn_book {if $version < 1.6}button{/if}" onclick="cart_update('{$product->id|escape:'htmlall':'UTF-8'}')" >{l s='Buy Tickets' mod='eventsmanager'}</a>&nbsp;
                  </td>
						
						{/if}

					
							{else}
								<td align="center">
									<span class="exclusive">
										<a id="unavailable" href="#ticket-unavailable" onclick="unAvaliable('{$product->name|escape:'htmlall':'UTF-8'}')">{l s='Tickets Not Available' mod='eventsmanager'}</a>
									</span>
								</td>
							{/if}
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						{/foreach}
					</body>
				</body>
			</table>
		</div>
		{/if}
		<!-- /Ticketing -->
	   
	   <!-- Main Image begins -->
			<div class="event-contents  mat_event_single_holder">
				{if $eventData.event_image} 
					{$eventData.contact_photo = $eventData.event_image}
				{else}
					{$eventData.contact_photo = 'events/blank_icon.jpg'}
				{/if}
				<span style="max-width: 100%">
					<img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventData.contact_photo|escape:'htmlall':'UTF-8'}"  style="border: 1px solid #d5d5d5;padding:3px;margin-right: 10px;" />
				</span>
				<br style="clear: both;"/>
				<div class="textEvent">
					<p>{$eventData.event_content}{*HTML Content*}</p>
				</div>
	            {if $eventData.contact_name neq ""}
	            	<div class="eventsContact_info">
			            {if $eventData.contact_name neq ""}
				            <div class="mat_event_organizer">
								<i class="icon-user"></i>
								<strong>{l s='Contact Person' mod='eventsmanager'} :</strong> {$eventData.contact_name|escape:'htmlall':'UTF-8'}
							</div>
			            {/if}
			            {if $eventData.contact_phone neq ""}
			            	<div class="mat_event_phone">
								<i class="icon-phone"></i>
			            		<strong>{l s='Phone' mod='eventsmanager'} : </strong> {$eventData.contact_phone|escape:'htmlall':'UTF-8'}
			            	</div>
			            {/if}
			            {if $eventData.contact_fax neq ""}
			            	<div class="mat_event_phone">
								<i class="icon-print"></i>
			            		<strong>{l s='Fax' mod='eventsmanager'} : </strong> {$eventData.contact_fax|escape:'htmlall':'UTF-8'}
			            	</div>
			            {/if}
			            {if $eventData.contact_email neq ""}
			            	<div class="mat_event_phone">
								<i class="icon-envelope"></i>
			            		<strong>{l s='Email' mod='eventsmanager'} :</strong> {$eventData.contact_email|escape:'htmlall':'UTF-8'}
			            	</div>
			            {/if}
			            {if $eventData.contact_address neq ""}
			            	<div class="mat_event_phone">
								<i class="icon-home"></i>
			            		<strong>{l s='Address' mod='eventsmanager'} : </strong> {$eventData.contact_address|escape:'htmlall':'UTF-8'}
			            	</div>
			            {/if}
		        	</div>

	            {/if}
			</div>
		<!-- Main Image ends -->
{if $eventGallery|@count > 0}
	<!-- Gallery section begins -->
	{if $events_show_gallery eq 1}
		{if $eventGallery neq ""}
			<h3 class="event-detail-heading">{l s='Event Gallery' mod='eventsmanager'}</h3>
			<div class="mat_event_single_holder">
				<div id="fme-events-slider" class="slider-pro">
					<div class="sp-slides">
						{section name=gallery loop=$eventGallery}
						<div class="sp-slide">
							<a class="noclass" href="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventGallery[gallery].image_file|escape:'htmlall':'UTF-8'}" rel="event_gallery"><img class="sp-image" src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventGallery[gallery].image_file|escape:'htmlall':'UTF-8'}" /></a>
						</div>
						{/section}
					</div>
					{if isset($THUMBNAILS_ENABLE_DISABLE) AND $THUMBNAILS_ENABLE_DISABLE AND $THUMBNAILS_ENABLE_DISABLE eq '1'}
					 <div class="sp-thumbnails">
					 	{section name=gallery loop=$eventGallery}
				        <div class="sp-thumbnail">
				            <img class="sp-thumbnail-image" src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventGallery[gallery].image_file|escape:'htmlall':'UTF-8'}" style="max-width:100%;min-height:80px;!important"/>
				        </div>
				        {/section}
				    </div>
				    {/if}
				</div>
			</div>
			{literal}
				<script type="text/javascript">
               var set_width = "{/literal}{$SLIDER_WIDTH|intval}{literal}";
               var set_height = "{/literal}{$SLIDER_HEIGHT|intval}{literal}";
               var set_arrows = "{/literal}{$SLIDER_ARROWS|intval}{literal}";
               var set_buttons = "{/literal}{$PAGINATION_BUTTONS|intval}{literal}";
               var set_thumbnailArrows = "{/literal}{$THUMBNAILS_ENABLE_DISABLE|intval}{literal}";
               var set_autoplay = "{/literal}{$AUTOPLAY_SLIDER|intval}{literal}";
				</script>
			{/literal}
		{/if}
	{/if}
	<!-- /Gallery section ends -->
{/if}
	<!-- Video section begins -->
	{if $events_show_youtbue_video eq 1}
		{if $videoId neq ""}
		{if $has_match eq 1}
				<p style="clear: both"></p>
				<h3 class="event-detail-heading">{l s='Event Video' mod='eventsmanager'}</h3>
				<div class="fme-event-video">
					<div class="mat_event_single_holder">
						<iframe id="events-video" class="youtube-player col-lg-12" type="text/html" src="https://www.youtube.com/embed/{$videoId[1]|escape:'htmlall':'UTF-8'}" frameborder="0" style="padding: 1%;min-width:98%;min-height:500px;">
						</iframe>
					</div>
				</div>
		{/if}
		{/if}
	{/if}
	<!-- /Video section ends -->

	<p style="clear: both;"></p>
	<!-- Social section -->
	{if $events_sharing_options eq 1}
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style ">
		<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
		<a class="addthis_button_tweet"></a>
		<a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"></a>
		<a class="addthis_counter addthis_pill_style"></a>
	</div>
	{literal}
	<script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-530bf3b2187e73b7"></script>
	{/literal}
	<!-- AddThis Button END -->
	{/if}
	<p style="clear: both;"></p>
</div>

<!-- if end of theme A -->


{else}

<div id="wrapper">
<div class="event_manager">
   <div class="inner_container">
   	<div class="page_title">
        <h3 class="event-detail-heading">{$eventData.event_title|escape:'htmlall':'UTF-8'}</h3>
         {if $enable_tags}
            <div style="border-left:none" class="event-detail-heading theme2-event-detail-tags">
               <h4>{l s='Tags:' mod='eventsmanager'}</h4>
               {foreach from=$eventTags item=tag name=tag}
                  {assign var=params value=['id_tag'=> $tag.id_fme_tags, 'tags'=> $tag.friendly_url]}
                  <span class="theme2-event-detail-tag">
                        <a href="{$link->getModuleLink('eventsmanager', 'eventstag', $params)|escape:'htmlall':'UTF-8'}">{$tag.name|escape:'htmlall':'UTF-8'}{if $smarty.foreach.tag.last}{else},{/if}</a>
                  </span>
               {/foreach}
            </div>
         {/if}
      </div>


       <div class="container">
         {if $eventData.event_image} 
         {$eventData.contact_photo = $eventData.event_image}
         {else}
         {$eventData.contact_photo = 'events/blank_icon.jpg'}
         {/if}
         <div class="detail_thumbnail"> <img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventData.contact_photo|escape:'htmlall':'UTF-8'}" alt=""> </div>
         <div class="detail_left_col clearfix">
            <div class="event_info">
               <h2>{l s='Event Information' mod='eventsmanager'}</h2>
               <div class="event-info-field clearfix">
                  <div class="event-info-label">
                     <label>{l s='Time' mod='eventsmanager'}</label>
                  </div>
                  <div class="event-info-content" style="font-size: 13px;">
                     {$eventData.event_start_date|date_format:"%b %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0} {$eventData.event_start_date|date_format:"g:i:s A"|escape:'htmlall':'UTF-8'}{/if}
                     {l s='-' mod='eventsmanager'}
                     {$eventData.event_end_date|date_format:"%b %e, %Y"|escape:'htmlall':'UTF-8'}{if $events_timestamp > 0}  {$eventData.event_end_date|date_format:"g:i:s A"|escape:'htmlall':'UTF-8'}{/if}
                  </div>
               </div>
               {if $eventData.event_venu neq ""}
               <div class="event-info-field clearfix">
                  <div class="event-info-label">
                     <label>{l s='Location' mod='eventsmanager'}</label>
                  </div>
                  <div class="event-info-content"><span class="mapThis" place="{$eventData.event_venu|escape:'htmlall':'UTF-8'}" zoom="10">{$eventData.event_venu|escape:'htmlall':'UTF-8'}</span>
                  </div>
               </div>
               {/if}
               {if $stream_on eq 1}
                  {if $eventData.event_streaming_start_time le $eventData.event_streaming_end_time }
                  <div class="mat_event_content col-sm-12 clearfix">
                     <p style="font-size: small;color: #777;">
                        <a style="margin-left:20%;" href="{$streaming_video|escape:'htmlall':'UTF-8'}" autoplay="true" target="_blank" class="btn btn-primary">{l s='See Live Streaming' mod='eventsmanager'}</a>
                        <br><strong style="margin-left:82px;"><i class="icon-calendar"></i> {l s='This Streaming is available from' mod='eventsmanager'} :</strong> {$eventData.event_streaming_start_time|escape:'htmlall':'UTF-8'}
                        <strong style="margin-left:82px;">{l s= 'To' mod='eventsmanager'}:</strong> {$eventData.event_streaming_end_time|escape:'htmlall':'UTF-8'}
                     </p>
                  </div>
                  {else}
                     <p style="font-size: small;color: #777;float:left;">
                        <strong style="margin-left:155px;"><i class="icon-calendar"></i>{l s='This streaming is no longer available' mod='eventsmanager'}</strong>
                     </p>
                  {/if}   
               {/if}
               {if $eventData.contact_name neq ""}
               <div class="event-info-field clearfix">
                  <div class="event-info-label">
                     <label>{l s='Contact Person' mod='eventsmanager'}</label>
                  </div>
                  <div class="event-info-content">{$eventData.contact_name|escape:'htmlall':'UTF-8'}</div>
               </div>
               {/if}
               {if $eventData.contact_phone neq ""}
               <div class="event-info-field clearfix">
                  <div class="event-info-label">
                     <label>{l s='Phone' mod='eventsmanager'} :</label>
                  </div>
                  <div class="event-info-content">{$eventData.contact_phone|escape:'htmlall':'UTF-8'}</div>
               </div>
               {/if}
               {if $eventData.contact_fax neq ""}
               <div class="event-info-field clearfix">
                  <div class="event-info-label">
                     <label>{l s='Fax' mod='eventsmanager'} :</label>
                  </div>
                  <div class="event-info-content">{$eventData.contact_fax|escape:'htmlall':'UTF-8'}</div>
               </div>
               {/if}
               {if $eventData.contact_email neq ""}
               <div class="event-info-field clearfix">
                  <div class="event-info-label">
                     <label>{l s='Email' mod='eventsmanager'}:</label>
                  </div>
                  <div class="event-info-content">{$eventData.contact_email|escape:'htmlall':'UTF-8'}</div>
               </div>
               {/if}
               {if $eventData.contact_address neq ""}
               <div class="event-info-field clearfix">
                  <div class="event-info-label">
                     <label>{l s='Address' mod='eventsmanager'} :</label>
                  </div>
                  <div class="event-info-content">
                     <span class="addressThis" place="{$eventData.contact_address|escape:'htmlall':'UTF-8'}" zoom="10">
                     {$eventData.contact_address|escape:'htmlall':'UTF-8'}
                     </span>
                  </div>
               </div>
               {/if}
           
            </div>
             <div class="buy_tickets"> <a id="buy_ticketslink" href="#">{l s='Buy Tickets' mod='eventsmanager'}</a> </div>
         </div>
      </div>


	
    <div class="container">
         <div class="content">
            <p>{$eventData.event_content nofilter}{*HTML Content*}</p>
         </div>
      </div>

      {if !empty($eproduct)}
      <div class="ticket_section">
         <h3 class="event-detail-heading">{l s='Tickets' mod='eventsmanager'}</h3>
         <table width="100%" border="0">
            <thead>
               <tr>
                  <th colspan="4">&nbsp;</th>
               </tr>
            </thead>
            <body>
               <body>
                  {foreach from=$eproduct item=product}
                  <tr class="mat_event_single_holder">
                     <td align="center"><img src="{$link->getImageLink($product->link_rewrite, $cover[$product->id].id_image, 'small_default')|escape:'htmlall':'UTF-8'}"></td>
                     <td align="center">{$product->name|escape:'htmlall':'UTF-8'}</td>
                     <td align="center"><strong>{$product->price|escape:'htmlall':'UTF-8'}</strong></td>
                     {if $product->available_for_order eq 1 AND $product->quantity gt 0}
                     <td align="center">
                     <div class="qty">
                    <div class="input-group bootstrap-touchspin" style="float: right;">
                       <input type="number" value="1" class="js-cart-line-product-quantity form-control qty_pro_fmm{$product->id|escape:'htmlall':'UTF-8'}" min="1" aria-label="Quantity" style="display: block;">
                       <input type="hidden" name="fmm_cartlink" id="fmm_cartlink" value="{$link->getPageLink('cart', true, NULL, "token={$static_token|escape:'htmlall':'UTF-8'}&amp;add=1")|escape:'htmlall':'UTF-8'}"">
                       <a onclick="fmmdown('{$product->id|escape:'htmlall':'UTF-8'}')" data-field-qty="qty" class="btn btn-default button-minus product_quantity_down">
							<span><i class="icon-minus"></i></span>
						</a>
						<a onclick="fmmup('{$product->id|escape:'htmlall':'UTF-8'}')" data-field-qty="qty" class="btn btn-default button-plus product_quantity_up">
							<span><i class="icon-plus"></i></span>
						</a>

                    </div>
                    </div>
                  </td>
                  <td align="center">
                     <a class="btn btn-primary mat_btn_book {if $version < 1.6}button{/if}" onclick="cart_update('{$product->id|escape:'htmlall':'UTF-8'}')" >{l s='Buy Tickets' mod='eventsmanager'}</a>&nbsp;
                  </td>
                     {else}
                     <td align="center">
                        <span class="exclusive">
                        <a id="unavailable" href="#ticket-unavailable" onclick="unAvaliable('{$product->name|escape:'htmlall':'UTF-8'})">{l s='Tickets Not Available' mod='eventsmanager'}</a>
                        </span>
                     </td>
                     {/if}
                  </tr>
                  <tr>
                     <td colspan="4">&nbsp;</td>
                  </tr>
                  {/foreach}
            </body>
            </body>
         </table>
      </div>
    {/if}

      
      {if $eventGallery|@count > 0}
   <!-- Gallery section begins -->
   {if $events_show_gallery eq 1}
   {if $eventGallery neq ""}
   <h3 class="event-detail-heading2">{l s='Event Gallery' mod='eventsmanager'}</h3>
   <div class="mat_event_single_holder">
      <div id="fme-events-slider" class="slider-pro">
         <div class="sp-slides">
            {section name=gallery loop=$eventGallery}
            <div class="sp-slide">
               <a class="noclass" href="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventGallery[gallery].image_file|escape:'htmlall':'UTF-8'}" rel="event_gallery"><img class="sp-image" src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventGallery[gallery].image_file|escape:'htmlall':'UTF-8'}" data-image-large-src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventGallery[gallery].image_file|escape:'htmlall':'UTF-8'}" /></a>
            </div>
            {/section}
         </div>
         {if isset($THUMBNAILS_ENABLE_DISABLE) AND $THUMBNAILS_ENABLE_DISABLE AND $THUMBNAILS_ENABLE_DISABLE eq '1'}
         <div class="sp-thumbnails">
            {section name=gallery loop=$eventGallery}
            <div class="sp-thumbnail">
               <img class="sp-thumbnail-image" src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}img/{$eventGallery[gallery].image_file|escape:'htmlall':'UTF-8'}" style="max-width:100%;min-height:80px;!important"/>
            </div>
            {/section}
         </div>
         {/if}
      </div>
   </div>
   {literal}
      <script type="text/javascript">
         var set_width = "{/literal}{$SLIDER_WIDTH|intval}{literal}";
         var set_height = "{/literal}{$SLIDER_HEIGHT|intval}{literal}";
         var set_arrows = "{/literal}{$SLIDER_ARROWS|intval}{literal}";
         var set_buttons = "{/literal}{$PAGINATION_BUTTONS|intval}{literal}";
         var set_thumbnailArrows = "{/literal}{$THUMBNAILS_ENABLE_DISABLE|intval}{literal}";
         var set_autoplay = "{/literal}{$AUTOPLAY_SLIDER|intval}{literal}";
      
      </script>
   {/literal}
   {/if}
   {/if}
   {/if}
   <!-- /Gallery section ends -->
      {if $event_video_status eq 0}
         <div class="container">
         <div class="events_venue">
            <ul>
               {if $events_show_youtbue_video eq 1}
               {if $videoId neq ""}
               {if $has_match eq 1}
               <li>
                  
                  <h3 class="event-detail-heading2">{l s='Event Video' mod='eventsmanager'}</h3>
                  <div class="video">
                     <iframe id="events-video" class="youtube-player col-lg-12" type="text/html" src="https://www.youtube.com/embed/{$videoId[1]|escape:'htmlall':'UTF-8'}" frameborder="0">
                     </iframe>
                  </div>
               </li>
               {/if}
               {/if}
               {/if}
               <li>
                  
                  <br /> <br /> <br />
                  <div class="map" id="map_canvas" style="width:100%; height:190px">
               </li>
            </ul>
            </div>
         </div>
      {else}
         <p style="clear: both"></p>
         {if $events_show_youtbue_video eq 1}
            {if $videoId neq ""}
            {if $has_match eq 1}
               <h3 class="event-detail-heading">{l s='Event Video' mod='eventsmanager'}</h3>
               <div class="fme-event-video">
                  <div class="mat_event_single_holder">
                     <iframe id="events-video" class="youtube-player col-lg-12" type="text/html" src="https://www.youtube.com/embed/{$videoId[1]|escape:'htmlall':'UTF-8'}" frameborder="0" style="padding: 1%;min-width:98%;min-height:500px;">
                     </iframe>
                  </div>
               </div>
            {/if}
            {/if}
         {/if}
         <div class="map" id="map_canvas" style="width:100%; height:290px">
      {/if}
      <!-- Video section begins -->

      <p style="clear: both;"></p>
   <!-- Social section -->
   {if $events_sharing_options eq 1}
   <!-- AddThis Button BEGIN -->
   <div class="addthis_toolbox addthis_default_style ">
      <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
      <a class="addthis_button_tweet"></a>
      <a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"></a>
      <a class="addthis_counter addthis_pill_style"></a>
   </div>
   {literal}
   <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>

   <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-530bf3b2187e73b7"></script>
   {/literal}
   <!-- AddThis Button END -->
   {/if}
   <p style="clear: both;"></p>
   {literal}
      <script src="https://maps.googleapis.com/maps/api/js?key={/literal}{$EVENTS_META_MAPKEY|escape:'htmlall':'UTF-8'}{literal}&calbback=initializeMap" async defer></script>
   {/literal}
   </div>
   </div>
</div>

{/if}