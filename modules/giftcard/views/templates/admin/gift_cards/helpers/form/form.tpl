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

{extends file="helpers/form/form.tpl"}
{block name="fieldset"}
	{include file='../../../menu.tpl'}
	<div class = "col-lg-10">
	 {$smarty.block.parent}
	</div>
{/block}

{block name="input"}
    {if $input.type == 'giftimage_template'}
        <div id="gc_template-options_div" class="col-lg-6 custom-dropdown">
            <button type="button" id="gc_dropdown-btn"  class="btn btn-default dropdown-toggle">
                <span id="gc_selected-template-text">{l s='Select a Template' mod='giftcard'}</span>
                <span class="caret"></span>
            </button>
            <ul id="gc_template-options" class="dropdown-menu dropdown-list" style="width: max-content;">
                {foreach from=$giftcard_templates_array item=giftcard_template}
                    <li class="dropdown-item" style="cursor: pointer;" data-value="{$giftcard_template.id_option|escape:'htmlall':'UTF-8'}" data-image="{$giftcard_template.img_url}">{$giftcard_template.image}</li>  {* HTML CONTENT*}
                {/foreach}
            </ul>
            <input type="hidden" name="gc_selected_template" id="gc_selected_template_input" value="">
            <input type="hidden" name="id_gc_selected_template" id="id_gc_selected_template_value" value="">
        </div>
        <div class="clearfix"></div>
        <div id="preview" style="{if isset($product) AND isset($product.id_cover) AND $product.id_cover}display: block;{else}display:none;{/if}">
                <img id="image-thumb" src="{if isset($product) AND isset($product.id_cover) AND $product.id_cover}{$link->getImageLink($product.link_rewrite[$id_lang], $product.id_cover, 'home_default')|escape:'htmlall':'UTF-8'}{/if}" class="img img-thumbnail" width="300">
        </div>
        {* <button type='button' id="save_image_button" class="btn btn-primary">Save Image</button> *}
    {elseif $input.type == 'giftimage'}
        <div class="col-lg-6 giftimage_upload">
            <input id="gift-image" class="btn btn-default" type="file" name="giftimage" value=""/>
            <p class="preference_description help-block hint-block" style="padding-top:3px;">{l s='Format: JPG, GIF, PNG. Filesize: 8.00 MB max.' mod='giftcard'}</p>
        </div>
        <div class="clearfix"></div>
        <div id="preview" class="form-group" style="{if isset($product) AND isset($product.id_cover) AND $product.id_cover}display: block;{else}display:none;{/if}">
            <div class="col-lg-12">
                <img id="image-thumb" src="{if isset($product) AND isset($product.id_cover) AND $product.id_cover}{$link->getImageLink($product.link_rewrite[$id_lang], $product.id_cover, 'home_default')|escape:'htmlall':'UTF-8'}{/if}" class="img img-thumbnail" width="300">
            </div>
        </div>
    {elseif $input.type == 'card_value'}
        <div class="col-lg-9">
            <div id="card_val" class="input-group" style="{if !isset($card) OR (isset($card) AND isset($card.value_type) AND !in_array($card.value_type, ['dropdown', 'fixed']))}display: none;{/if}">
                <span class="input-group-addon">{$default_currency_object->iso_code|escape:'htmlall':'UTF-8'}</span>
                <input type="text" name="card_value" {if $card != null AND isset($card.card_value) AND ($card.value_type == 'dropdown' OR $card.value_type == 'fixed')}value="{$card.card_value|escape:'htmlall':'UTF-8'}"{/if}/>
            </div>
            <div id="dropdown_div" class="form-group margin-form "{if $card != null AND isset($card.value_type) AND $card.value_type == 'dropdown'}style="display:block"{/if}style="display:none">
                <p class="preference_description help-block hint-block" style="padding-top:3px;">{l s='Example: 10,50,100,200 (use comma (,) as a separater to make your dropdown list.)' mod='giftcard'}</p>
            </div>
            <div id="fixed_div" class="form-group margin-form " {if $card == null OR (isset($card.value_type) AND $card.value_type == 'fixed')}style="display:block"{/if}style="display:none">
                <p class="preference_description help-block hint-block" style="padding-top:3px;">{l s='Example: 100 (enter single numeric value.)' mod='giftcard'}</p>
            </div>
            <div id="range_div" class="form-group margin-form " {if $card != null AND !empty($card.card_value) AND $card.value_type == 'range'}style="display:inline-flex;margin-top:-5px;"{/if} style="display:none;">
                {if $card != null AND !empty($card.card_value) AND $card.value_type == 'range'}
                    {assign var=vals value=","|explode:$card.card_value}
                {/if}
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon">{$default_currency_object->iso_code|escape:'htmlall':'UTF-8'}</span>
                        <input type="text" id="range_val" name="min" {if $card != null AND !empty($card.card_value) AND $card.value_type == 'range'}value="{$vals[0]|escape:'htmlall':'UTF-8'}"{/if}/>
                        <span class="input-group-addon">{l s='Min' mod='giftcard'}</span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon">{$default_currency_object->iso_code|escape:'htmlall':'UTF-8'}</span>
                        <input type="text" id="range_val" name="max"{if $card != null AND !empty($card.card_value) AND $card.value_type == 'range'}value="{$vals[1]|escape:'htmlall':'UTF-8'}"{/if}/>
                        <span class="input-group-addon">{l s='Max' mod='giftcard'}</span>
                    </div>
                </div>
                <p class="preference_description help-block hint-block" style="padding-top:-5px;">{l s='Select min and max values for your gift card.' mod='giftcard'}</p>
            </div>
        </div>
    {elseif $input.type == 'tax_rules_group'}
        <div class="col-lg-4">
            <select name="id_tax_rules_group" id="id_tax_rules_group" {if $tax_exclude_taxe_option}disabled="disabled"{/if} >
                <option value="0">{l s='No Tax' mod='giftcard'}</option>
                {foreach from=$tax_rules_groups item=tax_rules_group}
                    <option value="{$tax_rules_group.id_tax_rules_group|escape:'htmlall':'UTF-8'}" {if isset($product) AND $product AND isset($product.id_tax_rules_group) AND $product.id_tax_rules_group == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
                        {$tax_rules_group['name']|htmlentitiesUTF8|escape:'htmlall':'UTF-8'}
                    </option>
                {/foreach}
            </select>
        </div>
    {elseif $input.type == 'discount_value'}
        <div id="apply_discount_percent_div">
            <div class="form-group margin-form">
                <!-- pecentage value for fixed price -->
                <div id="percent_fixed" class="col-lg-6" style="display:none;">
                    <div class="input-group col-lg-6">
                        <span class="input-group-addon">{l s='%' mod='giftcard'}</span>
                        <input type="text" name="reduction_percent_fixed" id="reduction_percent" value="{if $card != null AND isset($card.reduction_amount) AND $card.reduction_type == 'percent'}{$card.reduction_amount|escape:'htmlall':'UTF-8'}{/if}">  
                    </div>
                </div>

                <!-- percentage values for dropdown list -->
                <div  id="percent_dropdown" class="col-lg-6" style="display:none;">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='%' mod='giftcard'}</span>
                        <input type="text" name="reduction_percent_dropdown" {if $card != null AND isset($card.reduction_amount) AND $card.reduction_type == 'percent'}value="{$card.reduction_amount|escape:'htmlall':'UTF-8'}"{else}value=""{/if}>
                    </div>
                        <p class="preference_description help-block hint-block" style="padding-top:3px;">{l s='Example: 5,10,15,20 (use comma separater. Th percentage will be applied respectively.)' mod='giftcard'}</p>
                </div>

                <!-- percentage values for rage type -->
                <div id="percent_range" class="input-group"  style="display:none;">
                    {if $card != null AND !empty($card.reduction_amount) AND $card.value_type == 'range' AND $card.reduction_type == 'percent'}
                        {assign var=per value=","|explode:$card.reduction_amount}
                    {/if}

                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-addon">{l s='%' mod='giftcard'}</span>
                            <input type="text" name="min_percent" {if $card != null AND !empty($card.reduction_amount) AND $card.value_type == 'range' AND $card.reduction_type == 'percent'}value="{if !empty($per) AND $per}{$per[0]|escape:'htmlall':'UTF-8'}{/if}"{/if}/><span class="input-group-addon">{l s='Min' mod='giftcard'}</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-addon">{l s='%' mod='giftcard'}</span>
                            <input type="text" name="max_percent"{if $card != null AND !empty($card.reduction_amount) AND $card.value_type == 'range' AND $card.reduction_type == 'percent'}value="{if !empty($per) AND $per}{$per[1]|escape:'htmlall':'UTF-8'}{/if}"{/if}/><span class="input-group-addon">{l s='Max' mod='giftcard'}</span>
                        </div>
                    </div>
                    <p class="preference_description help-block hint-block" style="padding-top:-5px;">{l s='Select min and max percentage value.' mod='giftcard'}</p>
                </div>
            </div>
        </div>
    {elseif $input.type == 'product_search'}
        <div id="apply_discount_to_div" {if $card != null AND isset($card.reduction_type) AND ($card.reduction_type == 'amount' OR $card.reduction_type == 'percent')}style="display:block;"{/if}style="display:none;">
            <div id="apply_discount_to_product_div">
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="text" style="width:400px" {if $card != null AND isset($card.discount_product)}value="{$card.discount_product|escape:'htmlall':'UTF-8'}"{/if} name="reductionProductFilter" id="reductionProductFilter" autocomplete="off" class="ac_input">
                        <span class="input-group-addon"><i class="icon-search"></i></span>
                    </div>
                    <input type="hidden" name="reduction_product" id="reduction_product" {if $card != null AND isset($card.id_discount_product)}value="{$card.id_discount_product|escape:'htmlall':'UTF-8'}"{/if}>
                    <input id="spy" type="hidden" value="{$link->getPageLink('search')|escape:'htmlall':'UTF-8'}" />
                    <input id="lang_spy" type="hidden" value="{$id_lang|escape:'htmlall':'UTF-8'}" />
                    <p class="preference_description help-block hint-block" style="padding-top:3px;">{l s='(Begin typing the first letters of the product name, then select the product from the drop-down list.)' mod='giftcard'}</p>
                </div>
            </div>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}

{/block}

{block name="script"}
var id_lang = parseInt("{$id_lang|intval|escape:'htmlall':'UTF-8'}");
$('#gift-image').on('change', function(){
    readURL(this);
});
function readURL(input) {
    $('#product-image').remove();
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#preview').show();
            $('#image-thumb').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

if ($("input:radio[name=value_type]").is(":checked")) {
    var radio = $("input[type='radio'][name='value_type']:checked").val();
    switch(radio) {
        case 'dropdown' :
            $("#percent_range").hide();
            $("#percent_fixed").hide();
            $("#percent_dropdown").show();
            break;

        case 'fixed' :
            $("#percent_dropdown").hide();
            $("#percent_range").hide();
            $("#percent_fixed").show();
                break;

        case 'range' :
            $("#percent_dropdown").hide();
            $("#percent_fixed").hide();
            $("#percent_range").show();
                break;
    }
}

//** Show/Hide value type options 
$('#dropdown').click(function () {
    $("#dropdown_div").show();
    $("#card_val").show();
    $('input[name=card_value').show();
    $("#range_div").hide();
    $("#fixed_div").hide();
    // hiding percentage fields
    $("#percent_range").hide();
    $("#percent_fixed").hide();
    $("#percent_dropdown").show();
});

$('#fixed').click(function () {
    $("#fixed_div").show();
    $("#card_val").show();
    $('input[name=card_value').show();
    $("#range_div").hide();
    $("#dropdown_div").hide();
    // hiding percentage fields
    $("#percent_dropdown").hide();
    $("#percent_range").hide();
    $("#percent_fixed").show();
});

$('#range').click(function () {
    $("#dropdown_div").hide();
    $("#range_div").show();
    $("#card_val").hide();
    $('input[name=card_value').hide();
    $("#fixed_div").hide();
// hiding percentage fields
    $("#percent_dropdown").hide();
    $("#percent_fixed").hide();
    $("#percent_range").show();

});

//** Show/Hide Discount options
if ($("#apply_discount_percent").is(":checked")) {
    $('#apply_discount_percent_div').closest('.form-group').show();
    $("#apply_discount_to_div").show();
} else {
    $('#apply_discount_percent_div').closest('.form-group').hide();
    $("#apply_discount_to_div").show();
}

$(document).on('click', '#apply_discount_percent', function () {
    $('#apply_discount_percent_div').closest('.form-group').show();
    $('#voucher_reduction_tax').closest('.form-group').hide();
});
$(document).on('click', '#apply_discount_amount', function () {
    $('#apply_discount_percent_div').closest('.form-group').hide();
    $('#voucher_reduction_tax').closest('.form-group').show();
});

/*
$(document).on('click', '#apply_discount_off', function () {
    $("#apply_discount_percent_div").hide();
    $("#apply_discount_amount_div").hide();
    $("#apply_discount_to_div").hide();
});
*/

//** Hide/Show selection product
if ($("#apply_discount_to_product").is(":checked")) {
    $("#apply_discount_to_product_div").closest('.form-group').show();
} else {
    $("#apply_discount_to_product_div").closest('.form-group').hide();
}
$('#apply_discount_to_product').click(function () {
    $("#apply_discount_to_product_div").closest('.form-group').show();
});
$('#apply_discount_to_order').click(function () {
    $("#apply_discount_to_product_div").closest('.form-group').hide();
});

//autocomplete search
var options = {
    minChars: 3,
    max: 10,
    width: 500,
    selectFirst: false,
    scroll: false,
    dataType: 'json',
    formatItem: function(data, i, max, value, term) {
        return value;
    },
    parse: function(data) {
        var mytab = new Array();
        for (var i = 0; i < data.length; i++) {
            mytab[mytab.length] = { data: data[i], value: data[i].id + ' - ' + data[i].name };
        }
        return mytab;
    },
    extraParams: {
        ajaxSearch: 1,
        token: token,
        id_lang: id_lang,
        controller: 'AdminCreateGift',
        reductionProductFilter: 1
    }
};

$("#reductionProductFilter").autocomplete('ajax_products_list.php', options)
.result(function(event, data, formatted) {
    if ( data.id.length > 0 && data.name.length > 0 ) {
        $("#reductionProductFilter").val(data.name);
        $("#selected_prod").val(data.id);
        $("#reduction_product").val(data.id);
        $("#reduction_product").trigger('change');
    }
});

$('input[name="gcp_image_type"]').change(function() {
        var selectedOption = $('input[name="gcp_image_type"]:checked').val();
        if (selectedOption == 'template') {
            $('.template-image-wrapper').css("display", "block");
            $('.upload-image-wrapper').css("display", "none");
        } else{
            $('.template-image-wrapper').css("display", "none");
            $('.upload-image-wrapper').css("display", "block");
        }

    });

    $('input[name="gcp_image_type"]:checked').trigger('change');

    var dropdownList = document.getElementById("gc_template-options");
    var dropdownListDiv = document.getElementById("gc_template-options_div");
    var selectedText = document.getElementById("gc_selected-template-text");
    var hiddenInput = document.getElementById("gc_selected_template_input");
    var selectedImg = document.getElementById("image-thumb");
    document.querySelectorAll(".dropdown-item").forEach(function (item) {
        item.addEventListener("click", function () {
            var value = this.getAttribute("data-value");
            var image = this.getAttribute("data-image");

            selectedText.textContent = value;
            {* selectedImg.src = image;
            selectedImg.style.display = "inline-block"; *}
            hiddenInput.value = image;
            document.getElementById('id_gc_selected_template_value').value = value;

             dropdownList.classList.remove("show"); 
        });
    });
    document.getElementById("gc_dropdown-btn").addEventListener("click", function () {
        dropdownList.style.display = (dropdownList.style.display === "grid") ? "none" : "grid";
    });
    document.getElementById("gc_template-options").addEventListener("click", function () {
        dropdownList.style.display = (dropdownList.style.display === "grid") ? "none" : "grid";
    });


    $('.template-image-wrapper .col-lg-8.col-lg-offset-3').removeClass('col-lg-8 col-lg-offset-3');

    $('#gift_card_form_submit_btn').on('click', function (e) {
        e.preventDefault();
        let selectedCardValueType = null;
        let cardValue = null;

        // Check which input type is visible
        if ($('#fixed_div').is(':visible')) {
            selectedCardValueType = 'fixed';
            cardValue = $('input[name="card_value"]').val();
        } else if ($('#dropdown_div').is(':visible')) {
            selectedCardValueType = 'dropdown';
            cardValue = $('input[name="card_value"]').val(); // comma-separated
        } else if ($('#range_div').is(':visible')) {
            selectedCardValueType = 'range';
            const min = $('input[name="min"]').val();
            const max = $('input[name="max"]').val();
            cardValue = min + " - " + max ;
        }

        var filename = $('#id_gc_selected_template_value').val();
        var selectedOption = $('input[name="gcp_image_type"]:checked').val();
            if(selectedOption === 'template' && filename != ''){
                $.ajax({
                    url: '{$ajaxGc}&action=saveImage',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        filename: filename,
                        card_value_type: selectedCardValueType,
                        card_value: cardValue,
                    },
                    traditional: true,
                    success: function(response) {
                        if (response.status === 'success') {
                            console.log(response);
                            $('#product').after(response.input);
                            $('#gift_card_form').submit();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while saving the image.');
                    }
                });
            } else{
                $('#gift_card_form').submit();
            }
    });
{/block}