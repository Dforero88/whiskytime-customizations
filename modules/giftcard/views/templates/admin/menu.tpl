{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2022 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{$ps_version = 1.7}
{$module_path = 'dummy'}
{* {include file='./script.tpl'} *}
<div class="bootstrap col-lg-2">
    <div class="sidebar-fme-menu">
        <div class="fmm_giftcard-menu" id="giftcard-menu">
            <ul class="tab">
                <li class="tab-row" >
                    <a class="tab-page" data-tab = "giftcard_add_category_giftcard_tab" id="giftcard_add_category_giftcard" href="{$edit_category_giftcard|escape:'htmlall':'UTF-8'}" title="{l s='Settings' mod='giftcard'}">
                    {if $ps_version < 1.6}<img src="../img/admin/translation.gif">{else}<i class="material-icons">category</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Edit Gif Card Category' mod='giftcard'}</span>
                    </a>   
                </li>
                <li class="tab-row">
                    <a class="tab-page" data-tab = "giftcard_add_giftcard_tab" id="giftcard_add_giftcard"   href="{$add_new_giftcard|escape:'htmlall':'UTF-8'}" title="{l s='Add New Giftcard' mod='giftcard'}">
                    {if $ps_version < 1.6}<img src="../img/admin/translation.gif">{else}<i class="material-icons">redeem</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Add Gift Cards' mod='giftcard'}</span>
                    </a>
                </li>
                <li class="tab-row">
                    <a class="tab-page" data-tab = "giftcard_add_image_template_tab" id="giftcard_add_image_template"   href="{$add_img_template_giftcard|escape:'htmlall':'UTF-8'}" title="{l s='Gift Cards Image Template' mod='giftcard'}">
                    {if $ps_version < 1.6}<img src="../img/admin/translation.gif">{else}<i class="material-icons">collections</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Gift Cards Image Template' mod='giftcard'}</span>
                    </a>
                </li>
                <li class="tab-row" >
                    <a class="tab-page" data-tab = "giftcard_email_templates_tab" id="giftcard_emil_templates" href="{$gift_templates|escape:'htmlall':'UTF-8'}" title="{l s='Gift Templates' mod='giftcard'}">
                    {if $ps_version < 1.6}<img src="../img/admin/copy_files.gif">{else}<i class="material-icons">email</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Email Templates' mod='giftcard'}</span>
                    </a>  
                </li>
                <li class="tab-row" >
                    <a class="tab-page" data-tab = "giftcard_ordered_giftcards_tab" id="giftcard_ordered_giftcards" href="{$ordered_giftcards|escape:'htmlall':'UTF-8'}" title="{l s='Ordered Gift Cards' mod='giftcard'}">
                    {if $ps_version < 1.6}<img src="../img/admin/products.gif">{else}<i class="material-icons">list_alt</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Ordered Gift Cards' mod='giftcard'}</span><span class="fa fa-angle-right" style="float: right"></span>
                    </a>   
                </li>
                <li class="tab-row" >
                    <a class="tab-page" data-tab = "giftcard_giftcard_setting_tab" id="giftcard_giftcard_setting" href="{$giftcard_settings|escape:'htmlall':'UTF-8'}" title="{l s='Settings' mod='giftcard'}">
                    {if $ps_version < 1.6}<img src="../img/admin/picture.gif">{else}<i class="material-icons">settings</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Settings' mod='giftcard'}</span><span class="fa fa-angle-double-right" style="float: right"></span>
                    </a>   
                </li>
            </ul>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<script>
// $(document).ready(function () {
//     const currentUrl = window.location.href;

//     const tabMap = {
//         'giftcard_add_category_giftcard_tab': 'AdminGiftCardsCategory',
//         'giftcard_add_giftcard_tab': 'AdminGiftCards',
//         'giftcard_add_image_template_tab': 'AdminGiftCardsImageTemplate',
//         'giftcard_email_templates_tab': 'AdminGiftTemplates',
//         'giftcard_ordered_giftcards_tab': 'AdminOrderedGiftcards',
//         'giftcard_giftcard_setting_tab': 'AdminModules',
//     };
//     $.each(tabMap, function(tab, value) {
//         console.log(tab, value);
//         if (currentUrl.includes(value)) {
//             $('[data-tab="' + tab + '"]').addClass('selected');
//             return false;
//         }
//     });
// });

$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const controller = urlParams.get('controller');

    const tabMap = {
        'giftcard_add_category_giftcard_tab': 'AdminGiftCardsCategory',
        'giftcard_add_giftcard_tab': 'AdminGiftCards',
        'giftcard_add_image_template_tab': 'AdminGiftCardsImageTemplate',
        'giftcard_email_templates_tab': 'AdminGiftTemplates',
        'giftcard_ordered_giftcards_tab': 'AdminOrderedGiftcards',
        'giftcard_giftcard_setting_tab': 'AdminModules',
    };
    $.each(tabMap, function(tab, expectedController) {
        if (controller === expectedController) {
            $('[data-tab="' + tab + '"]').addClass('selected');
            return false;
        }
    });
});
</script>
