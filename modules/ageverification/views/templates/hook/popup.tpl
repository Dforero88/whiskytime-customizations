{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Front tpl file
*}
<script>
    {$kbage_verification_values['custom_js'] nofilter} {*Variable contains css content, escape not required*}
</script>
<style>
    {$kbage_verification_values['custom_css'] nofilter} {*Variable contains css content, escape not required*}
</style>
<style>
   .agePopupTitle, .ageformGroup label, .agePopupadditionalinfo {
       text-align: {$kbage_verification_values['text_align']} !important;
   }
   .agePopupTitle, .ageformGroup label {
       color: {$kbage_verification_values['popup_text_color']} !important;
   }
   .agePopupTitle {
       font-size: {$kbage_verification_values['popup_message_font_size']}px !important;
   }
   .ageformGroup label {
       font-size: {$kbage_verification_values['text_font_size']}px !important;
   }
   .kbageSubmitbutton, .kbbirthdateSubmitbutton {
       background: {$kbage_verification_values['popup_submit_button_color']} !important;
       color: {$kbage_verification_values['popup_submit_button_text_color']} !important;
       font-size: {$kbage_verification_values['text_font_size']}px !important;
   }
   .agePopupadditionalinfo p{
       font-size: {$kbage_verification_values['additional_info_font_size']}px !important;
   }
   .ageModalleftBox {
       background-color: rgba({$popup_rgb_color['r']}, {$popup_rgb_color['g']}, {$popup_rgb_color['b']}, {$kbage_verification_values['popup_opacity']});
   }
   .kbyesbutton {
       background: {$kbage_verification_values['popup_yes_button_color']} !important;
       color: {$kbage_verification_values['popup_yes_button_text_color']} !important;
       font-size: {$kbage_verification_values['text_font_size']}px !important;
   }
   .kbnobutton {
       background: {$kbage_verification_values['popup_no_button_color']} !important;
       color: {$kbage_verification_values['popup_no_button_text_color']} !important;
       font-size: {$kbage_verification_values['text_font_size']}px !important;
   }
   .ageModalBody .date-box input, .ageModalBody .year-box input {
       color: {$kbage_verification_values['popup_text_color']} !important;
       border: 1px solid {$kbage_verification_values['popup_text_color']} !important;
       font-size: {$kbage_verification_values['text_font_size']}px !important;
   }
   .ageModalBody .date-box input::placeholder, .ageModalBody .year-box input::placeholder{
       color: {$kbage_verification_values['popup_text_color']} !important;
   }
</style>
{if $kbage_verification_values['popup_shape'] == 2 && (isset($kbverification_window_image_path) && $kbverification_window_image_path != '' && $kbage_verification_values['enable_default_images'] != 1)}
    <style>
        @media (max-width: 1000px) {
            .ageModalleftBox {
                border-radius: 15px;
            }
        }
        @media (min-width: 1000px) {
            .ageModalleftBox {
                border-radius: 15px 0px 0px 15px;
            }
            .ageModalRightBox {
                border-radius: 0px 15px 15px 0px;
            }
        }
    </style>
{elseif $kbage_verification_values['popup_shape'] == 2 && $kbverification_window_image_path == '' && $kbage_verification_values['enable_default_images'] != 1}
    <style>
    .ageModalleftBox {
    border-radius: 15px;
    }
    </style>
{elseif $kbage_verification_values['popup_shape'] == 2 && $kbage_verification_values['enable_default_images'] == 1}
    {if $kbage_verification_values['choose_theme'] == 1 || $kbage_verification_values['choose_theme'] == 2 || $kbage_verification_values['choose_theme'] == 3}
        <style>
            @media (max-width: 1000px) {
                .ageModalleftBox {
                    border-radius: 15px;
                }
            }
            @media (min-width: 1000px) {
                .ageModalleftBox {
                    border-radius: 15px 0px 0px 15px;
                }
                .ageModalRightBox {
                    border-radius: 0px 15px 15px 0px;
                }
            }
        </style>
    {else}
        <style>
            .ageModalleftBox {
                border-radius: 15px;
            }
        </style>
    {/if}
{/if}
<script>
    var kbcurrentyear = {$kbcurrentyear};
    var current_timest = '{$current_timest}';
    var kbageverification_age_value = '{$kbage_verification_values['age']}';
    {*var current_timest = '{$current_timest}';*}
    var kbageverification_cookie_value = '{$kbage_verification_values['remember_visitor']}';
    var kbageverification_under_age_action = '{$kbage_verification_values['under_age_action']}';
    var kbageverification_redirect_url = '{$kbage_verification_values['underage_redirect_url'] nofilter}';{*Variable contains a URL, escape not required*}
    var kb_invalid_year = "{l s='Entered year is not valid. Please enter a valid year.' mod='ageverification'}";
    var kb_invalid_DOB = "{l s='Entered DOB is not valid. Please enter a valid DOB.' mod='ageverification'}";
</script>
{if $kbage_verification_values['enable_default_images'] == 1}
    <div class="ageModalBackdrop" style="display: block; background-image: url({$av_image_path nofilter}ageverification/views/img/admin/uploads/theme{$kbage_verification_values['choose_theme']}/main-bg.jpg);"></div>{*Variable contains a URL, escape not required*}
{elseif isset($kbverification_background_image_path) && $kbverification_background_image_path != ''}
    <div class="ageModalBackdrop" style="display: block; background-image: url({$kbverification_background_image_path nofilter});"></div>{*Variable contains a URL, escape not required*}
{else}
    <div class="ageModalBackdrop" style="display: block; background: rgba(0, 0, 0, 0.7);"></div>
{/if}
<div class="ageModal popupfade active">
    <div class="ageModaldialogue">
        <div class="ageModalContainer">
            <div class="ageModalBody">
                <div class="kb-age-error-message">{$kbage_verification_values['age_verification_under_age_message'][$kblang_id]}</div>
                <div class="ageModalleftBox">
                    <div class="ageModalleftBoxContent">
                        <div class="brandLogo">
                            {if $kbage_verification_values['enable_default_images'] == 1}
                                <img src="{$av_image_path nofilter}ageverification/views/img/admin/uploads/theme{$kbage_verification_values['choose_theme']}/logo.png" class="brandImage"/>{*Variable contains a URL, escape not required*}
                            {else}
                                <img src="{$kblogo_image_path nofilter}" class="brandImage"/>{*Variable contains a URL, escape not required*}
                            {/if}
                        </div>
                        <h5 class="agePopupTitle">{$kbage_verification_values['age_verification_popup_message'][$kblang_id]}</h5>
                        {if $kbage_verification_values['verification_method'] == 1}
                            <div class="ageformGroup">
                                <label>{$kbage_verification_values['age_verification_popup_dob_message'][$kblang_id]}</label>
                            </div>
                            <div class="jsx-2149158085 eaav-item-allow-buttons-container">
                                <button type="button" class="kbyesnobutton kbyesbutton" onclick='kbAgeYesButtonClick();'>{$kbage_verification_values['age_verification_yes_button_text'][$kblang_id]}</button>
                                <button type="button" class="kbyesnobutton kbnobutton" onclick='kbAgeNoButtonClick();'>{$kbage_verification_values['age_verification_no_button_text'][$kblang_id]}</button>
                            </div>
                            <small class='agePopupadditionalinfo'>{$kbage_verification_popup_additional_info_message nofilter}</small>{*Variable contains HTML, escape not required*}
                        {elseif $kbage_verification_values['verification_method'] == 2}
                            {*<form>*}
                                <div class="ageformGroup">
                                    <label>{$kbage_verification_values['age_verification_popup_dob_message'][$kblang_id]}</label>
                                    <div class="year-box">
                                        <input type="number" class="ageformControl" name="kbageverification_year_of_birth" id="kbageverification_year_of_birth" placeholder="{l s='YYYY' mod='ageverification'}" max="9999" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    </div>
                                </div>
                                <button class="btn btn-submit kbageSubmitbutton" onclick='kbVerifyYearOfBirth();'>{$kbage_verification_values['age_verification_submit_button_text'][$kblang_id]}</button>
                                <small class='agePopupadditionalinfo'>{$kbage_verification_popup_additional_info_message nofilter}</small>{*Variable contains HTML, escape not required*}
                            {*</form>*}
                        {else}
                            {*<form>*}
                                <div class="ageformGroup">
                                    <label>{$kbage_verification_values['age_verification_popup_dob_message'][$kblang_id]}</label>
                                    <div class="date-box">
                                        {if $kbage_verification_values['dob_format'] == 1} 
                                            <input type="number" id="kbageverification_dateofbirth_date" placeholder="{l s='DD' mod='ageverification'}" maxlength="2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            <input type="number" id="kbageverification_dateofbirth_month" placeholder="{l s='MM' mod='ageverification'}" maxlength="2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                        {else}
                                            <input type="number" id="kbageverification_dateofbirth_month" placeholder="{l s='MM' mod='ageverification'}" maxlength="2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            <input type="number" id="kbageverification_dateofbirth_date" placeholder="{l s='DD' mod='ageverification'}" maxlength="2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                        {/if}
                                        <input type="number" id="kbageverification_dateofbirth_year" placeholder="{l s='YYYY' mod='ageverification'}" max="9999" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    </div>
                                </div>
                                <button class="btn btn-submit kbbirthdateSubmitbutton" onclick='kbVerifyDateOfBirth();'>{$kbage_verification_values['age_verification_submit_button_text'][$kblang_id]}</button>
                                <small class='agePopupadditionalinfo'>{$kbage_verification_popup_additional_info_message nofilter}</small>{*Variable contains HTML, escape not required*}
                            {*</form>*}
                        {/if}
                    </div>
                </div>
                {*{if $kbage_verification_values['choose_theme'] == 1 || $kbage_verification_values['choose_theme'] == 2 || $kbage_verification_values['choose_theme'] == 3}*}
                {if $kbage_verification_values['enable_default_images'] == 1}
                    {if $kbage_verification_values['choose_theme'] == 1 || $kbage_verification_values['choose_theme'] == 2 || $kbage_verification_values['choose_theme'] == 3}
                        <div class="ageModalRightBox" style="background-image: url({$av_image_path nofilter}ageverification/views/img/admin/uploads/theme{$kbage_verification_values['choose_theme']}/side-img.jpg);">{*Variable contains a URL, escape not required*}
                        </div>
                    {/if}
                {else if isset($kbverification_window_image_path) && $kbverification_window_image_path != ''}
                    <div class="ageModalRightBox" style="background-image: url({$kbverification_window_image_path nofilter});">{*Variable contains a URL, escape not required*}
                    </div>
                {/if}
                {*{/if}*}
            </div>
        </div>
    </div>	
</div>