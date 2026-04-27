{**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author FMM Modules
* @copyright 2021 FMM Modules
* @license FMM Modules
*}
{extends file="helpers/form/form.tpl"}
{block name="fieldset"}
	{include file='../../../menu.tpl'}
	<div class = "col-lg-10">
	 {$smarty.block.parent}
	</div>
{/block}
{block name="input"}
	{if $input.tab == 'gc_customization'}
		<div class="separation"></div>
			<div class="col-lg-5 col-md-4" id="giftcardtemplateselect">
				<div id="giftcardtemplateselect_img" style="position: relative; background-color: {$bg_color|escape:'htmlall':'UTF-8'};">
					<div id="overlay_price" class="overlay-text top-right">{$price|escape:'htmlall':'UTF-8'}</div>
					<img id="gifcardcreate" width="100%" height="auto"
					src="{$preview_img_url|escape:'htmlall':'UTF-8'}"
					style="position: relative; z-index: 1;">
					<div id="overlay_discount" class="overlay-text bottom-right">{$discount_code|escape:'htmlall':'UTF-8'}</div>
					<div id="overlay_toptext" class="overlay-text bottom-left">{$template_text[$default_form_language]|escape:'htmlall':'UTF-8'}</div>
				</div>
			</div>

			<div id="template_customization_div" class="col-lg-7 col-md-8">
				<h3>{l s='Data Variable' mod='giftcard'}</h3>
				<div class="form-group">
					<label class="control-label col-lg-3" for="price">
						<span class="label-tooltip" data-toggle="tooltip"
							title="{l s='0 to not display price in template' mod='giftcard'}">
							{l s='Price' mod='giftcard'}
						</span>
					</label>
					<div class="col-lg-9">
						<input type="text" name="price" id="price"
							class="custom_field" value="{$price|escape:'htmlall':'UTF-8'}" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3" for="discount_code">
						{l s='Discount code' mod='giftcard'}
					</label>
					<div class="col-lg-9">
						<input type="text" name="discount_code" id="discount_code"
							class="custom_field" value="{$discount_code|escape:'htmlall':'UTF-8'}" />
					</div>
				</div>

				<h3>{l s='Customizable Text' mod='giftcard'}</h3>

				{foreach from=$languages item=language}
					<div class="form-group translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $default_form_language}style="display:none"{/if}>
						<label class="control-label col-lg-3" for="template_text_{$language.id_lang|escape:'htmlall':'UTF-8'}">
						{l s='Top Text' mod='giftcard'}
						</label>
						<div class="col-lg-9">
							<div class="row">
								<div class="col-lg-9">
									<input type="text" name="template_text_{$language.id_lang|escape:'htmlall':'UTF-8'}"
										id="template_text_{$language.id_lang|escape:'htmlall':'UTF-8'}" value="{$template_text[$language.id_lang]|escape:'htmlall':'UTF-8'}" />
								</div>
								<div class="col-lg-2">
									<button type="button" class="btn btn-default dropdown-toggle"
											data-toggle="dropdown">{$language.iso_code|escape:'htmlall':'UTF-8'}
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach from=$languages item=lang}
											<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});">
												{$lang.name|escape:'htmlall':'UTF-8'} ({$lang.iso_code|escape:'htmlall':'UTF-8'})
											</a></li>
										{/foreach}
									</ul>
								</div>
							</div>
						</div>
					</div>
				{/foreach}

				<h3>{l s='Customizable color' mod='giftcard'}</h3>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Background Color' mod='giftcard'}</label>
					<div class="col-lg-9">
						<div class="col-lg-8">
							<div class="row">
								<div class="input-group">
									<input type="text" name="bg_color" id="color_3"
										class="color mColorPickerInput mColorPicker"
										value="{$bg_color|escape:'htmlall':'UTF-8'}"
										style="background-color: {$bg_color|escape:'htmlall':'UTF-8'}; color: white;" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	{else}
		{$smarty.block.parent}
	{/if} 

{/block}

{block name="script"}
	$(document).ready(function () {
		$('.mColorPickerInput').mColorPicker({
			{* color: '#8bcdd1', *}
			{* preview: true *}
		});
		$('div[data-tab-id="gc_customization"] .col-lg-offset-3')
			.removeClass('col-lg-offset-3');

		const priceInput = $('#price');
		const discountInput = $('#discount_code');

		if (priceInput.length) {
			priceInput.on('input', function () {
				$('#overlay_price').text($(this).val());
			});
		}

		if (discountInput.length) {
			discountInput.on('input', function () {
				$('#overlay_discount').text($(this).val());
			});
		}

		const updateTopTextOverlay = function () {
			const visibleInput = $('input[name^="template_text_"]:visible');
			$('#overlay_toptext').text(visibleInput.val());
		};

		updateTopTextOverlay();

		$(document).on('input', 'input[name^="template_text_"]', function () {
			updateTopTextOverlay();
		});

		$(document).on('click', '.translatable-field .dropdown-menu a', function () {
			setTimeout(updateTopTextOverlay, 100);
		});
		
		$('.mColorPickerInput').on('change', function () {
			let color = $(this).val();

			// Convert RGB to HEX if needed
			if (color.startsWith("rgb")) {
				color = rgb2hex(color);
				$(this).val(color); // Update input field to hex
			}

			$('#giftcardtemplateselect_img').css('background-color', color);
		});
		function rgb2hex(rgb) {
			var result = rgb.match(/\d+/g);
			if (!result) return rgb;
			return "#" + result.map(function(x) {
				let hex = parseInt(x).toString(16);
				return hex.length === 1 ? "0" + hex : hex;
			}).join('');
		}

		{* $('#save_image_button').on('click', function () {
			html2canvas(document.getElementById('giftcardtemplateselect_img')).then(function (canvas) {
				const imageUrl = canvas.toDataURL('image/png');
				const giftcardId = {$id_giftcard_image_template};

				$.ajax({
					url: '{$ajaxGc}&action=saveImage',
					type: 'POST',
					data: {
						image_data: imageUrl,
						filename: 'giftcard_template_' + giftcardId + '.png'
					},
					success: function(response) {
						console.log('Image saved successfully!', response);
					},
					error: function() {
						alert('An error occurred while saving the image.');
					}
				});
			});
		}); *}
	});
{/block}