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

{$html_content = Tools::htmlentitiesDecodeUTF8($product_info)}
{$html_content nofilter}{* html content *}
<input type="hidden" id="hide_btn" name="hide_btn" value="{$hide_btn|escape:'htmlall':'UTF-8'}">
