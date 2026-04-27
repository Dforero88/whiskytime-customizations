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
<h1>{$cdate}{*HTML Content*}</h1>
{$controls}{*HTML Content*}
{$drwaClaendar}{*HTML Content*}

<div style="clear:both; height:50px;"></div>
