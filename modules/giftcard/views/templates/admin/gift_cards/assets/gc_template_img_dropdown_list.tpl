{* /**
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules All right reserved
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category  front_office_features
*/ *}

{if !empty($preview_img_url)}
    {if $is_customization_enabled}
        <div class="col-lg-2 col-md-4" id="giftcardtemplateselected">
            <div id="giftcardtemplateselected_img" style="position: relative; background-color: {$bg_color|escape:'htmlall':'UTF-8'};">
                <div id="overlayed_price" class="overlay-text top-right" style="position: absolute; top: 0px; right: -5px; font-size: 5px;">{$price|escape:'htmlall':'UTF-8'}</div>
                <img id="gifcardcreated" width="100%" height="auto"src="{$preview_img_url|escape:'htmlall':'UTF-8'}"style="position: relative; z-index: 1;">
                <div id="overlayed_discount" class="overlay-text bottom-right" style="position: absolute; bottom: 0px; right: -5px; font-size: 5px;">{$discount_code|escape:'htmlall':'UTF-8'}</div>
                <div id="overlayed_toptext" class="overlay-text bottom-left" style="position: absolute; bottom: 0px; left: -5px; font-size: 5px;">{$template_text[$default_form_language]|escape:'htmlall':'UTF-8'}</div>
            </div>
        </div>
    {else}
        <img id="gifcardcreated" width="15%" height="auto"src="{$preview_img_url|escape:'htmlall':'UTF-8'}"style="position: relative; z-index: 1; padding: 5px;">
    {/if}
{/if}