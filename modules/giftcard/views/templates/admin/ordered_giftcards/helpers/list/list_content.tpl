{**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2021 FMM Modules
*  @license   FMM Modules
*}

{capture name='tr_count'}{counter name='tr_count'}{/capture}
<tbody>
{if count($list)}

{foreach $list AS $index => $tr}
    <tr{if $position_identifier} id="tr_{$position_group_identifier|escape:'htmlall':'UTF-8'}_{$tr.$identifier|escape:'htmlall':'UTF-8'}_{if isset($tr.position['position'])}{$tr.position['position']|escape:'htmlall':'UTF-8'}{else}0{/if}"{/if} class="{if isset($tr.class)}{$tr.class|escape:'htmlall':'UTF-8'}{/if} {if $tr@iteration is odd by 1}odd{/if}"{if isset($tr.color) && $color_on_bg} style="background-color: {$tr.color|escape:'htmlall':'UTF-8'}"{/if} >
        {if $bulk_actions && $has_bulk_actions}
            <td class="row-selector text-center">
                {if isset($list_skip_actions.delete)}
                    {if !in_array($tr.$identifier, $list_skip_actions.delete)}
                    <input type="checkbox" name="{$list_id|escape:'htmlall':'UTF-8'}Box[]" value="{$tr.$identifier|escape:'htmlall':'UTF-8'}"{if isset($checked_boxes) && is_array($checked_boxes) && in_array({$tr.$identifier|escape:'htmlall':'UTF-8'}, $checked_boxes)} checked="checked"{/if} class="noborder" />
                {/if}
                
                {else}
                    <input type="checkbox" name="{$list_id|escape:'htmlall':'UTF-8'}Box[]" value="{$tr.$identifier|escape:'htmlall':'UTF-8'}"{if isset($checked_boxes) && is_array($checked_boxes) && in_array({$tr.$identifier|escape:'htmlall':'UTF-8'}, $checked_boxes)} checked="checked"{/if} class="noborder" />
                {/if}
            </td>
        {/if}
        {foreach $fields_display AS $key => $params}
            {block name="open_td"}
                <td
                    {if isset($params.position)}
                        id="td_{if !empty($position_group_identifier)}{$position_group_identifier|escape:'htmlall':'UTF-8'}{else}0{/if}_{$tr.$identifier|escape:'htmlall':'UTF-8'}{if $smarty.capture.tr_count > 1}_{($smarty.capture.tr_count - 1)|intval}{/if}"
                    {/if}
                    class="{strip}{if !$no_link}pointer{/if}
                    {if isset($params.position) && $order_by == 'position'  && $order_way != 'DESC'} dragHandle{/if}
                    {if isset($params.class)} {$params.class|escape:'htmlall':'UTF-8'}{/if}
                    {if isset($params.align)} {$params.align|escape:'htmlall':'UTF-8'}{/if}{/strip}"
                    {if (!isset($params.position) && !$no_link && !isset($params.remove_onclick))}
                        onclick="document.location = '{$current_index|addslashes|escape:'htmlall':'UTF-8'}&amp;{$identifier|escape:'htmlall':'UTF-8'}={$tr.$identifier|escape:'htmlall':'UTF-8'}{if $view}&amp;view{else}&amp;update{/if}{$table|escape:'htmlall':'UTF-8'}{if $page > 1}&amp;page={$page|intval}{/if}&amp;token={$token|escape:'htmlall':'UTF-8'}'">
                    {else}
                    >
                {/if}
            {/block}
            {block name="td_content"}
                {if isset($params.prefix)}{$params.prefix|escape:'htmlall':'UTF-8'}{/if}
                {if isset($params.badge_success) && $params.badge_success && isset($tr.badge_success) && $tr.badge_success == $params.badge_success}<span class="badge badge-success">{/if}
                {if isset($params.badge_warning) && $params.badge_warning && isset($tr.badge_warning) && $tr.badge_warning == $params.badge_warning}<span class="badge badge-warning">{/if}
                {if isset($params.badge_danger) && $params.badge_danger && isset($tr.badge_danger) && $tr.badge_danger == $params.badge_danger}<span class="badge badge-danger">{/if}
                {if isset($params.color) && isset($tr[$params.color])}
                    <span class="label color_field" style="background-color:{$tr[$params.color]|escape:'htmlall':'UTF-8'};color:{if Tools::getBrightness($tr[$params.color]) < 128}white{else}#383838{/if}">
                {/if}
                {if isset($tr.$key)}
                    {if isset($params.active)}
                        {$tr.$key nofilter}
                    {elseif isset($params.callback)}
                        {if isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
                            <span title="{$tr.$key|escape:'htmlall':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'}</span>
                        {else}
                            {$tr.$key nofilter}
                        {/if}
                    {elseif isset($params.activeVisu)}
                        {if $tr.$key}
                            <i class="icon-check-ok"></i> {l s='Enabled' mod='giftcard'}
                        {else}
                            <i class="icon-remove"></i> {l s='Disabled' mod='giftcard'}
                        {/if}
                    {elseif isset($params.position)}
                        {if !$filters_has_value && $order_by == 'position' && $order_way != 'DESC'}
                            <div class="dragGroup">
                                <div class="positions">
                                    {$tr.$key.position|escape:'htmlall':'UTF-8' + 1}
                                </div>
                            </div>
                        {else}
                            {$tr.$key.position|escape:'htmlall':'UTF-8' + 1}
                        {/if}
                    {elseif isset($params.image)}
                        {$tr.$key nofilter}
                    {elseif isset($params.icon)}
                        {if is_array($tr[$key])}
                            {if isset($tr[$key]['class'])}
                                <i class="{$tr[$key]['class']|escape:'htmlall':'UTF-8'}"></i>
                            {else}
                                <img src="../img/admin/{$tr[$key]['src']|escape:'htmlall':'UTF-8'}" alt="{$tr[$key]['alt']|escape:'htmlall':'UTF-8'}" title="{$tr[$key]['alt']|escape:'htmlall':'UTF-8'}" />
                            {/if}
                        {/if}
                    {elseif isset($params.type) && $params.type == 'price'}
                        {if isset($tr.id_currency)}
                            {displayPrice price=$tr.$key currency=$tr.id_currency}
                        {else}
                            {displayPrice price=$tr.$key}
                        {/if}
                    {elseif isset($params.float)}
                        {$tr.$key|escape:'htmlall':'UTF-8'}
                    {elseif isset($params.type) && $params.type == 'date'}
                        {dateFormat date=$tr.$key full=0}
                    {elseif isset($params.type) && $params.type == 'datetime'}
                        {dateFormat date=$tr.$key full=1}
                    {elseif isset($params.type) && $params.type == 'decimal'}
                        {number_format($tr.$key|escape:'htmlall':'UTF-8', 2)}
                    {elseif isset($params.type) && $params.type == 'percent'}
                        {$tr.$key|escape:'htmlall':'UTF-8'} {l s='%' mod='giftcard'}
                    {elseif isset($params.type) && $params.type == 'bool'}
            {if $tr.$key == 1}
              {l s='Yes' mod='giftcard'}
            {elseif $tr.$key == 0 && $tr.$key != ''}
              {l s='No' mod='giftcard'}
            {/if}
                    {* If type is 'editable', an input is created *}
                    {elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
                        <input type="text" name="{$key|escape:'htmlall':'UTF-8'}_{$tr.id|escape:'htmlall':'UTF-8'}" value="{$tr.$key|escape:'htmlall':'UTF-8'}" class="{$key|escape:'htmlall':'UTF-8'}" />
                    {elseif $key == 'color'}
                        {if !is_array($tr.$key)}
                        <div style="background-color: {$tr.$key|escape:'htmlall':'UTF-8'};" class="attributes-color-container"></div>
                        {else} {*TEXTURE*}
                        <img src="{$tr.$key.texture|escape:'htmlall':'UTF-8'}" alt="{$tr.name|escape:'htmlall':'UTF-8'}" class="attributes-color-container" />
                        {/if}
                    {elseif isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
                        <span title="{$tr.$key|escape:'htmlall':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        {$tr.$key|escape:'htmlall':'UTF-8'}
                    {/if}
                {else}
                    {block name="default_field"}--{/block}
                {/if}
                {if isset($params.suffix)}{$params.suffix|escape:'htmlall':'UTF-8'}{/if}
                {if isset($params.color) && isset($tr.color)}
                    </span>
                {/if}
                {if isset($params.badge_danger) && $params.badge_danger && isset($tr.badge_danger) && $tr.badge_danger == $params.badge_danger}</span>{/if}
                {if isset($params.badge_warning) && $params.badge_warning && isset($tr.badge_warning) && $tr.badge_warning == $params.badge_warning}</span>{/if}
                {if isset($params.badge_success) && $params.badge_success && isset($tr.badge_success) && $tr.badge_success == $params.badge_success}</span>{/if}
            {/block}
            {block name="close_td"}
                </td>
            {/block}
        {/foreach}

    {if $multishop_active && $shop_link_type}
        <td title="{$tr.shop_name|escape:'htmlall':'UTF-8'}">
            {if isset($tr.shop_short_name)}
                {$tr.shop_short_name|escape:'htmlall':'UTF-8'}
            {else}
                {$tr.shop_name|escape:'htmlall':'UTF-8'}
            {/if}
        </td>
    {/if}

    {if $has_actions}
        <td class="text-right">
            {assign var='compiled_actions' value=array()}
            {foreach $actions AS $key => $action}
                {if isset($tr.$action)}
                    {if $key == 0}
                        {assign var='action' value=$action}
                    {/if}
                    {if $action == 'delete' && $actions|@count > 2}
                        {$compiled_actions[] = 'divider'}
                    {/if}
                    {$compiled_actions[] = $tr.$action}
                {/if}
            {/foreach}
            {if $compiled_actions|count > 0}
                {if $compiled_actions|count > 1}<div class="btn-group-action">{/if}
                <div class="btn-group pull-right">
                    {$compiled_actions[0] nofilter}
                    {if $compiled_actions|count > 1}
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-caret-down"></i>&nbsp;
                    </button>
                        <ul class="dropdown-menu">
                        {foreach $compiled_actions AS $key => $action}
                            {if $key != 0}
                            <li{if $action == 'divider' && $compiled_actions|count > 3} class="divider"{/if}>
                                {if $action != 'divider'}{$action nofilter}{/if}
                            </li>
                            {/if}
                        {/foreach}
                        </ul>
                    {/if}
                </div>
                {if $compiled_actions|count > 1}</div>{/if}
            {/if}
        </td>
    {/if}
    </tr>

    <!-- giftcard data -->
    {if $tr.id_cart}
        {assign var=customizedDatas value=Product::getAllCustomizedDatas($tr.id_cart)}
        {if isset($customizedDatas) AND $customizedDatas}
            <tr id="gift-card-{$tr.$identifier|escape:'htmlall':'UTF-8'}-{$tr.id_order|escape:'htmlall':'UTF-8'}" style="width: 100%; display:none;">
                <td colspan="12" style="width:100%">
                    {foreach $customizedDatas as $customizedDatasInner}
                        {foreach $customizedDatasInner as $customizationPerAddress}
                            {foreach $customizationPerAddress as $customizationId => $customization}
                                {foreach $customization as $custData}
                                    <table class="table">
                                        {foreach $custData.datas as $type => $datas}
                                            {if ($type == Product::CUSTOMIZE_TEXTFIELD)}
                                                <tr>
                                                    {foreach from=$datas item=data}
                                                        <th class="center">
                                                            {if $data.name}
                                                                {$data.name|escape:'htmlall':'UTF-8'}
                                                            {else}
                                                                {l s='Text #%s' sprintf=[$data@iteration] mod='giftcard'}
                                                            {/if}
                                                        </th>
                                                    {/foreach}
                                                    {if isset($tr.specific_date) AND ($tr.specific_date) AND (strtotime($current_date) ge strtotime($tr.specific_date))}
                                                        <th class="center">
                                                            {l s='Action' mod='giftcard'}
                                                        </th>
                                                    {/if}
                                                </tr>
                                                <tr>
                                                    {assign var="width" value=100 / count($datas)}
                                                    {foreach from=$datas item=data}
                                                        <td class="center" width="{$width|escape:'htmlall':'UTF-8'}%">
                                                            {$data.value|escape:'htmlall':'UTF-8'}
                                                        </td>
                                                    {/foreach}
                                                    {if isset($tr.specific_date) AND ($tr.specific_date) AND (strtotime($current_date) ge strtotime($tr.specific_date))}
                                                        <td class="center">
                                                            {if $tr.has_voucher eq 0}
                                                                <a href="{$sendtosomeone_action|escape:'htmlall':'UTF-8'}&action=sendtosomeone&id_cart={$tr.id_cart|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
                                                                    <i class="icon-envelope"></i> {l s='Send Giftcard Manually' mod='giftcard'}
                                                                </a>
                                                            {else}
                                                                <span class="btn btn-success disabled"> {l s='Giftcard Sent' mod='giftcard'} <span>
                                                            {/if}
                                                        </td>
                                                    {/if}
                                                </tr>
                                            {/if}
                                        {/foreach}
                                    </table>
                                {/foreach}
                            {/foreach}
                        {/foreach}
                    {/foreach}
                </td>
            </tr>
        {/if}
    {/if}
{/foreach}
{else}
    <tr>
        <td class="list-empty" colspan="{count($fields_display)+1}">  {* no filter *}
            <div class="list-empty-msg">
                <i class="icon-warning-sign list-empty-icon"></i>
                {l s='No records found' mod='giftcard'}
            </div>
        </td>
{/if}
</tbody>

