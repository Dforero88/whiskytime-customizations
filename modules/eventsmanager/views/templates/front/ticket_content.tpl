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

{assign var=color_header value="#F0F0F0"}
{assign var=color_border value="#000000"}
{assign var=color_border_lighter value="#CCCCCC"}
{assign var=color_line_even value="#FFFFFF"}
{assign var=color_line_odd value="#F9F9F9"}
{assign var=font_size_text value="9pt"}
{assign var=font_size_header value="9pt"}
{assign var=font_size_product value="9pt"}
{assign var=height_header value="20px"}
{assign var=table_padding value="4px"}

<style>
	table, th, td {
		margin: 0!important;
		padding: 0!important;
		vertical-align: middle;
		font-size: {$font_size_text|escape:'htmlall':'UTF-8'};
		white-space: nowrap;
	}

	table.product {
		border: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
		border-collapse: collapse;
	}

	table#addresses-tab tr td {
		font-size: large;
	}

	table#summary-tab {
		padding: {$table_padding|escape:'htmlall':'UTF-8'};
		border: 1pt solid {$color_border|escape:'htmlall':'UTF-8'};
	}

	table#payment-tab {
		padding: {$table_padding|escape:'htmlall':'UTF-8'};
		border: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
	}

	th.product {
		border-bottom: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
	}

	tr.discount th.header {
		border-top: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
	}

	tr.product td {
		border-bottom: 1px solid {$color_border_lighter|escape:'htmlall':'UTF-8'};
	}

	tr.color_line_even {
		background-color: {$color_line_even|escape:'htmlall':'UTF-8'};
	}

	tr.color_line_odd {
		background-color: {$color_line_odd|escape:'htmlall':'UTF-8'};
	}

	tr.customization_data td {
	}

	td.product {
		vertical-align: middle;
		font-size: {$font_size_product|escape:'htmlall':'UTF-8'};
	}

	th.header {
		font-size: {$font_size_header|escape:'htmlall':'UTF-8'};
		height: {$height_header|escape:'htmlall':'UTF-8'};
		background-color: {$color_header|escape:'htmlall':'UTF-8'};
		vertical-align: middle;
		text-align: center;
		font-weight: bold;
	}

	th.header-right {
		font-size: {$font_size_header|escape:'htmlall':'UTF-8'};
		height: {$height_header|escape:'htmlall':'UTF-8'};
		background-color: {$color_header|escape:'htmlall':'UTF-8'};
		vertical-align: middle;
		text-align: right;
		font-weight: bold;
	}

	th.payment {
		background-color: {$color_header|escape:'htmlall':'UTF-8'};
		vertical-align: middle;
		font-weight: bold;
	}

	tr.separator td {
		border-top: 1px solid #000000;
	}

	.left {
		text-align: left;
	}

	.fright {
		float: right;
	}

	.right {
		text-align: right;
	}

	.center {
		text-align: center;
	}

	.bold {
		font-weight: bold;
	}

	.border {
		border: 1px solid black;
	}

	.no_top_border {
		border-top:hidden;
		border-bottom:1px solid black;
		border-left:1px solid black;
		border-right:1px solid black;
	}

	.grey {
		background-color: {$color_header|escape:'htmlall':'UTF-8'};

	}

	/* This is used for the border size */
	.white {
		background-color: #FFFFFF;
	}

	.big,
	tr.big td{
		font-size: 110%;
	}
	.small {
		font-size:small;
	}
</style>

<table width="100%" id="header" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<tr>
		<td width="50%">
		</td>
		<td width="50%" class="right">
		</td>
	</tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<!-- Addresses -->
	
	<tr>
		<td colspan="12">

			<table id="summary-tab" width="100%">
				<tr>
					<th class="header small" valign="middle">{l s='Customer Name'  mod='eventsmanager'}</th>
					<th class="header small" valign="middle">{l s='Customer Email'  mod='eventsmanager'}</th>
					<th class="header small" valign="middle">{l s='Order Reference'  mod='eventsmanager'}</th>
					<th class="header small" valign="middle">{l s='Order Date'  mod='eventsmanager'}</th>
				</tr>
				<tr>
					<td class="center small white">{$fname|escape:'htmlall':'UTF-8'} {$lname|escape:'htmlall':'UTF-8'}</td>
					<td class="center small white">{$email|escape:'htmlall':'UTF-8'}</td>
					<td class="center small white">{$order->getUniqReference()}</td>
					<td class="center small white">{dateFormat date=$order->date_add|escape:'htmlall':'UTF-8' full=0}</td>
				</tr>
			</table>


		</td>
	</tr>

	<tr>
		<td colspan="12" height="20">&nbsp;</td>
	</tr>

	<!-- Products -->
	<tr>
		<td colspan="12">

		<table class="product" width="100%" cellpadding="4" cellspacing="0">

			<thead>
				<tr>
					<th class="product header small" width="50%">{l s='Tickets'  mod='eventsmanager'}</th>
					<th class="product header small" width="10%">{l s='Currency'  mod='eventsmanager'}</th>
					<th class="product header small" width="10%">{l s='Per Ticket'  mod='eventsmanager'}</th>
					<th class="product header small" width="10%">{l s='Qty'  mod='eventsmanager'}</th>
					<th class="product header small" width="20%">{l s='Total'  mod='eventsmanager'}</th>
				</tr>
			</thead>

			<tbody>
				<!-- PRODUCTS -->
				{foreach $order_details as $order_detail}
					{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
					<tr class="product {$bgcolor_class|escape:'htmlall':'UTF-8'}">
						
						<td class="product left">
							{$order_detail.product_name|escape:'htmlall':'UTF-8'}
						</td>
						<td class="product center">
							{$currency_name|escape:'htmlall':'UTF-8'}
						</td>
						<td class="product center">
							{number_format($order_detail.product_price|escape:'htmlall':'UTF-8', 2)}
						</td>
						
						<td class="product center">
							{$order_detail.product_quantity|escape:'htmlall':'UTF-8'}
						</td>
						<td class="product center">
							{number_format($order_detail.total_price_tax_incl|escape:'htmlall':'UTF-8', 2)}
						</td>

					</tr>
				{/foreach}
				<!-- END PRODUCTS -->
			</tbody>

		</table>

		</td>
	</tr>

	<tr>
		<td colspan="12" height="20">&nbsp;</td>
	</tr>
	{foreach from=$array_alldata item=data}
	<tr>
		<td colspan="7" class="left">

			<table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
				<tr>
					
					<td class="payment center small grey bold" width="44%">{$data.title|escape:'htmlall':'UTF-8'}{l s=' Seats'  mod='eventsmanager'}</td>
					<td class="payment left white" width="56%">
						<table width="100%" border="0">
							
								<tr>
									<td class="center large" style="color: green;">{$data.reserve_seat|escape:'htmlall':'UTF-8'}</td>
								</tr>
							
						</table>
					</td>
					
				</tr>
			</table>

		</td>
		<td colspan="5">&nbsp;</td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="7" class="left">

			<table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td class="payment center small grey bold" width="44%">{l s='Payment Method'  mod='eventsmanager'}</td>
					<td class="payment left white" width="56%">
						<table width="100%" border="0">
								<tr>
									<td class="small center">{$order_pyment|escape:'htmlall':'UTF-8'}</td>
								</tr>
						</table>
					</td>
				</tr>
			</table>
			<table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td class="payment center small grey bold" width="44%">{l s='Order Status'  mod='eventsmanager'}</td>
					<td class="payment left white" width="56%">
						<table width="100%" border="0">
							<tr>
									<td class="center small">{$current_status|escape:'htmlall':'UTF-8'}</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

		</td>
		<td colspan="5">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="12" height="20">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="12">
		{foreach from=$array_alldata item=data}
		<table id="addresses-tab" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="12">

					<table id="summary-tab" width="100%">
						<tr>
							<th class="header small" valign="middle">{l s='Event'  mod='eventsmanager'}</th>
							<th class="header small" valign="middle">{l s='Start Date'  mod='eventsmanager'}</th>
							<th class="header small" valign="middle">{l s='End Date'  mod='eventsmanager'}</th>
							<th class="header small" valign="middle">{l s='Location'  mod='eventsmanager'}</th>
						</tr>
						<tr>
							<td class="center small white">{$data.title|escape:'htmlall':'UTF-8'}</td>
							<td class="center small white">{$data.sdate|escape:'htmlall':'UTF-8'}</td>
							<td class="center small white">{$data.edate|escape:'htmlall':'UTF-8'}</td>
							<td class="center small white">{$data.location|escape:'htmlall':'UTF-8'}</td>
							
						</tr>
					</table>
				</td>
			</tr>
			<br/>
			<tr>
				<td width="30%"><span class="bold">{$data.title|escape:'htmlall':'UTF-8'}</span><br/>
					<img src="{$data.image|escape:'htmlall':'UTF-8'}">
				</td>
				<td width="5%"></td>
				<td width="65%">
					<span class="bold">{l s='Details' mod='eventsmanager'}</span>
					<br/>
					<div class="small">
						{$data.description}{* HTML CONTENT *}
					</div>
				</td>
			</tr>
		</table>
		<br/>
		<br/>
		{/foreach}

		</td>
	</tr>
</table>
