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

<script type="text/javascript" src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}themes/core.js"></script>

<div class="calendar_outerdiv">
{$controls nofilter}{*HTML Content*}
{$drwaClaendar nofilter}{*HTML Content*}

<div style="clear:both; height:50px;"></div>
</div>
{/block}