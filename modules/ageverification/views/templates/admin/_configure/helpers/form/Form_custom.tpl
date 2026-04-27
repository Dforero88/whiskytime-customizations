{extends file='helpers/form/form.tpl'}

{block name='defaultForm'}
    <script>
        var general_settings = "{l s='General Settings' mod='ageverification'}";
        var lookfeel_settings = "{l s='Look and Feel Settings' mod='ageverification'}";
        var content_settings = "{l s='Content Settings' mod='ageverification'}";
        var path = '{$path nofilter}';{*Variable contains a URL, escape not required*}
        var module_path = "{$module_path}";{*variable contains HTML content, Can not escape this*}
        {*var myImage = '{$myImage}';*}
        {*var pathImage = '{$pathImage nofilter}';*} {*Variable contains a URL, escape not required*}
        var imagedir_path = '{$imagedir_path nofilter}'; {*Variable contains a URL, escape not required*}
        var default_lang = '{$default_lang}';
        var value_between_msg = "{l s='Field value must be from 1 to 100.' mod='ageverification'}";
        var value_between_msg2 = "{l s='Field value must be from 0 to 100.' mod='ageverification'}";
        var greater_than_zero = "{l s='Number should be greater than 0.' mod='ageverification'}";
        var custom_css_length = "{l s='Maximum 10000 charaters allowed at Custom CSS.' mod='ageverification'}";
        var custom_js_length = "{l s='Maximum 10000 charaters allowed at Custom JS.' mod='ageverification'}";
        var please_check_all_fields = "{l s='Please check for all the languages in the field.' mod='ageverification'}";
        var count_language = '{$count_language}';
        var path_fold = '{$path_fold|escape:'quotes':'UTF-8'}';
        var check_for_all_lang = "{l s='This field can not be empty.Please check for all languages.' mod='ageverification'}";
        var selected_pages_selection_error = "{l s='Please choose at least one product, category or page on which you want to show Age Verification Popup.' mod='ageverification'}";
        var popup_message_font_size_value_between_msg = "{l s='Field value must be from 10 to 40.' mod='ageverification'}";
        var text_font_size_value_between_msg = "{l s='Field value must be from 10 to 20.' mod='ageverification'}";
        var additional_info_font_size_value_between_msg = "{l s='Field value must be from 10 to 20.' mod='ageverification'}";
        var no_image_text = "{l s='No Image Found' mod='ageverification'}";
        var image_deleted_text = "{l s='Image deleted successfully' mod='ageverification'}";
        var delete_image_text = "{l s='Are you sure you want to delete?' mod='ageverification'}";
        var browser_support_text = "{l s='This browser does not support FileReader.' mod='ageverification'}";
        var logoimageexist = {$logoimageexist};
        var verificationwindowimageexist = {$verificationwindowimageexist};
        var verificationbackgroundimageexist = {$verificationbackgroundimageexist};
        var display_logo_image_path = '{$display_logo_image_path nofilter}'; {*Variable contains a URL, escape not required*}
        var display_window_image_path = '{$display_window_image_path nofilter}'; {*Variable contains a URL, escape not required*}
        var display_background_image_path = '{$display_background_image_path nofilter}'; {*Variable contains a URL, escape not required*}
        var default_image_path = "{$default_image_path|escape:'quotes':'UTF-8'}";
        

        velovalidation.setErrorLanguage({
            empty_fname: "{l s='Please enter First name.' mod='ageverification'}",
            maxchar_fname: "{l s='First name cannot be greater than #d characters.' mod='ageverification'}",
            minchar_fname: "{l s='First name cannot be less than #d characters.' mod='ageverification'}",
            empty_mname: "{l s='Please enter middle name.' mod='ageverification'}",
            maxchar_mname: "{l s='Middle name cannot be greater than #d characters.' mod='ageverification'}",
            minchar_mname: "{l s='Middle name cannot be less than #d characters.' mod='ageverification'}",
            only_alphabet: "{l s='Only alphabets are allowed.' mod='ageverification'}",
            empty_lname: "{l s='Please enter Last name.' mod='ageverification'}",
            maxchar_lname: "{l s='Last name cannot be greater than #d characters.' mod='ageverification'}",
            minchar_lname: "{l s='Last name cannot be less than #d characters.' mod='ageverification'}",
            alphanumeric: "{l s='Field should be alphanumeric.' mod='ageverification'}",
            empty_pass: "{l s='Please enter Password.' mod='ageverification'}",
            maxchar_pass: "{l s='Password cannot be greater than #d characters.' mod='ageverification'}",
            minchar_pass: "{l s='Password cannot be less than #d characters.' mod='ageverification'}",
            specialchar_pass: "{l s='Password should contain atleast 1 special character.' mod='ageverification'}",
            alphabets_pass: "{l s='Password should contain alphabets.' mod='ageverification'}",
            capital_alphabets_pass: "{l s='Password should contain atleast 1 capital letter.' mod='ageverification'}",
            small_alphabets_pass: "{l s='Password should contain atleast 1 small letter.' mod='ageverification'}",
            digit_pass: "{l s='Password should contain atleast 1 digit.' mod='ageverification'}",
            empty_field: "{l s='Field cannot be empty.' mod='ageverification'}",
            number_field: "{l s='You can enter only numbers.' mod='ageverification'}",            
            positive_number: "{l s='Number should be greater than 0.' mod='ageverification'}",
            maxchar_field: "{l s='Field cannot be greater than #d characters.' mod='ageverification'}",
            minchar_field: "{l s='Field cannot be less than #d character(s).' mod='ageverification'}",
            empty_email: "{l s='Please enter Email.' mod='ageverification'}",
            validate_email: "{l s='Please enter a valid Email.' mod='ageverification'}",
            empty_country: "{l s='Please enter country name.' mod='ageverification'}",
            maxchar_country: "{l s='Country cannot be greater than #d characters.' mod='ageverification'}",
            minchar_country: "{l s='Country cannot be less than #d characters.' mod='ageverification'}",
            empty_city: "{l s='Please enter city name.' mod='ageverification'}",
            maxchar_city: "{l s='City cannot be greater than #d characters.' mod='ageverification'}",
            minchar_city: "{l s='City cannot be less than #d characters.' mod='ageverification'}",
            empty_state: "{l s='Please enter state name.' mod='ageverification'}",
            maxchar_state: "{l s='State cannot be greater than #d characters.' mod='ageverification'}",
            minchar_state: "{l s='State cannot be less than #d characters.' mod='ageverification'}",
            empty_proname: "{l s='Please enter product name.' mod='ageverification'}",
            maxchar_proname: "{l s='Product cannot be greater than #d characters.' mod='ageverification'}",
            minchar_proname: "{l s='Product cannot be less than #d characters.' mod='ageverification'}",
            empty_catname: "{l s='Please enter category name.' mod='ageverification'}",
            maxchar_catname: "{l s='Category cannot be greater than #d characters.' mod='ageverification'}",
            minchar_catname: "{l s='Category cannot be less than #d characters.' mod='ageverification'}",
            empty_zip: "{l s='Please enter zip code.' mod='ageverification'}",
            maxchar_zip: "{l s='Zip cannot be greater than #d characters.' mod='ageverification'}",
            minchar_zip: "{l s='Zip cannot be less than #d characters.' mod='ageverification'}",
            empty_username: "{l s='Please enter Username.' mod='ageverification'}",
            maxchar_username: "{l s='Username cannot be greater than #d characters.' mod='ageverification'}",
            minchar_username: "{l s='Username cannot be less than #d characters.' mod='ageverification'}",
            invalid_date: "{l s='Invalid date format.' mod='ageverification'}",
            maxchar_sku: "{l s='SKU cannot be greater than #d characters.' mod='ageverification'}",
            minchar_sku: "{l s='SKU cannot be less than #d characters.' mod='ageverification'}",
            invalid_sku: "{l s='Invalid SKU format.' mod='ageverification'}",
            empty_sku: "{l s='Please enter SKU.' mod='ageverification'}",
            validate_range: "{l s='Number is not in the valid range. It should be betwen #d1 and #d2' mod='ageverification'}",
            empty_address: "{l s='Please enter address.' mod='ageverification'}",
            minchar_address: "{l s='Address cannot be less than #d characters.' mod='ageverification'}",
            maxchar_address: "{l s='Address cannot be greater than #d characters.' mod='ageverification'}",
            empty_company: "{l s='Please enter company name.' mod='ageverification'}",
            minchar_company: "{l s='Company name cannot be less than #d characters.' mod='ageverification'}",
            maxchar_company: "{l s='Company name cannot be greater than #d characters.' mod='ageverification'}",
            invalid_phone: "{l s='Phone number is invalid.' mod='ageverification'}",
            empty_phone: "{l s='Please enter phone number.' mod='ageverification'}",
            minchar_phone: "{l s='Phone number cannot be less than #d characters.' mod='ageverification'}",
            maxchar_phone: "{l s='Phone number cannot be greater than #d characters.' mod='ageverification'}",
            empty_brand: "{l s='Please enter brand name.' mod='ageverification'}",
            maxchar_brand: "{l s='Brand name cannot be greater than #d characters.' mod='ageverification'}",
            minchar_brand: "{l s='Brand name cannot be less than #d characters.' mod='ageverification'}",
            empty_shipment: "{l s='Please enter Shimpment.' mod='ageverification'}",
            maxchar_shipment: "{l s='Shipment cannot be greater than #d characters.' mod='ageverification'}",
            minchar_shipment: "{l s='Shipment cannot be less than #d characters.' mod='ageverification'}",
            invalid_ip: "{l s='Invalid IP format.' mod='ageverification'}",
            invalid_url: "{l s='Invalid URL format.' mod='ageverification'}",
            empty_url: "{l s='Please enter URL.' mod='ageverification'}",
            valid_amount: "{l s='Field should be numeric.' mod='ageverification'}",
            valid_decimal: "{l s='Field can have only upto two decimal values.' mod='ageverification'}",
            max_email: "{l s='Email cannot be greater than #d characters.' mod='ageverification'}",
            specialchar_zip: "{l s='Zip should not have special characters.' mod='ageverification'}",
            specialchar_sku: "{l s='SKU should not have special characters.' mod='ageverification'}",
            max_url: "{l s='URL cannot be greater than #d characters.' mod='ageverification'}",
            valid_percentage: "{l s='Percentage should be in number.' mod='ageverification'}",
            between_percentage: "{l s='Percentage should be between 0 and 100.' mod='ageverification'}",
            maxchar_size: "{l s='Size cannot be greater than #d characters.' mod='ageverification'}",
            specialchar_size: "{l s='Size should not have special characters.' mod='ageverification'}",
            specialchar_upc: "{l s='UPC should not have special characters.' mod='ageverification'}",
            maxchar_upc: "{l s='UPC cannot be greater than #d characters.' mod='ageverification'}",
            specialchar_ean: "{l s='EAN should not have special characters.' mod='ageverification'}",
            maxchar_ean: "{l s='EAN cannot be greater than #d characters.' mod='ageverification'}",
            specialchar_bar: "{l s='Barcode should not have special characters.' mod='ageverification'}",
            maxchar_bar: "{l s='Barcode cannot be greater than #d characters.' mod='ageverification'}",
            positive_amount: "{l s='Field should be positive.' mod='ageverification'}",
            maxchar_color: "{l s='Color could not be greater than #d characters.' mod='ageverification'}",
            invalid_color: "{l s='Color is not valid.' mod='ageverification'}",
            specialchar: "{l s='Special characters are not allowed.' mod='ageverification'}",
            script: "{l s='Script tags are not allowed.' mod='ageverification'}",
            style: "{l s='Style tags are not allowed.' mod='ageverification'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='ageverification'}",
            not_image: "{l s='Uploaded file is not an image.' mod='ageverification'}",
            image_size: "{l s='Uploaded file size must be less than #d.' mod='ageverification'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='ageverification'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='ageverification'}",
            invalid_separator:"{l s='Invalid comma (#d) separated values.' mod='ageverification'}",
        });
        
    </script>
    {if $version == 16}
        <div class='row'>
            <div class='productTabs col-lg-2 col-md-3'>
                <div class='list-group'>
                    {$i=1}
                    {foreach $module_tabs key=numStep item=tab}
                        <a class='list-group-item {if $tab.selected}active{/if}' id='link-{$tab.id}' onclick='change_tab(this, {$i});'>{$tab.name}
                            <label class="velsof_error_label"><img id='velsof_error_icon' class="velsof_error_tab_img"  style="display:none; position:absolute; right:10px; top:10px;" src="{$error_img_path nofilter}views/img/admin/error_icon.gif"></label>{*Variable contains a URL, escape not required*}
                        </a>
                        {$i=$i+1}
                    {/foreach}
                </div>
            </div>
            {$form nofilter} {*Variable contains html content, escape not required*}
            {$view nofilter} {*Variable contains html content, escape not required*}  
        </div>
    {else}
        <div class='productTabs col-lg-2 col-md-3 vss-pos' >
            <ul class='tab'>
                {*todo href when nojs*}
                {$i=1}
                {foreach $module_tabs key=numStep item=tab}
                    <li class='tab-row'>
                        <a class='tab-page {if $tab.selected}selected{/if}' id='link-{$tab.id}' onclick='change_tab(this, {$i});'>{$tab.name}</a>
                        {$i=$i+1}
                    </li>
                {/foreach}
            </ul>
        </div>
        {$form nofilter} {*Variable contains html content, escape not required*}  
        {$view nofilter} {*Variable contains html content, escape not required*}  
    {/if}
    <form action="{$preview_url nofilter}" method="POST" target="_blank" id="kbageverification_preview_html_form">{*Variable contains URL content, escape not required*}
        <input type="hidden" name="kbageverification_preview_html" id="kbageverification_preview_html"/>
    </form>
{/block}


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
* Admin tpl file
*}

