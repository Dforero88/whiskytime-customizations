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

<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<!-- Addresses -->
	<tr>
		<td colspan="12">

		<table id="addresses-tab" cellspacing="0" cellpadding="0">
			<tr>
				<td width="40%"><span class="bold"> </span><br/><br/>
					
					<img src="{$logo|escape:'htmlall':'UTF-8'}">
				</td>
				<td width="40%"><br/><br/>
							
				</td>
				{assign var="order_url" value="{$link->getModuleLink('eventsmanager', 'eventTickets', ['order_id' => $order_id, 'id_event' => $event_id])}"}
				<td width="20%">
					<img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={$order_url|escape:'url':'UTF-8'}" alt="{l s='QR Code' mod='eventsmanager'}" />
					<br/><br/>

				</td>
			</tr>
		</table>

		</td>
	</tr>
</table>
