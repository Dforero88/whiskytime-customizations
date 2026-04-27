/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
var etsPNFBlogFree = 'undefined' == typeof etsPNFBlogFree ? true: window.etsPNFBlogFree;
var ets_pnf = {
	closeSearch: function($inputEle, $resultEle) {
		$inputEle.val('');
		$inputEle.focus();
		$resultEle.hide();
	},
	maybeHideACResults($results, loading) {
		if (!$results.length) {
			return;
		}
		if (loading) {
			if ($results.hasClass('ets-hidden')) {
				$results.show().removeClass('ets-hidden');
			}
			return;
		}
		if ($results.children().length < 2) {
			if (!$results.hasClass('ets-hidden')) {
				$results.hide().addClass('ets-hidden');
			}
		} else {
			if ($results.hasClass('ets-hidden')) {
				$results.show().removeClass('ets-hidden');
			}
		}
	},
	addProduct: function($inputAC, data) {
		var $listEl = $('.ets_pnf_search_list_ETS_PNF_PRODUCTS');
		var $loadingEl = $('.ets_pnf_ac_results_loading', $listEl);
		if (!$listEl.length || !$loadingEl.length || !data) {
			return;
		}
		var $productIdsEl = $('#ets_specific_ETS_PNF_PRODUCTS'),
			productId = parseInt(data[0], 10);
		if (!$productIdsEl.length || !productId || $loadingEl.hasClass('ets-active')) {
			return;
		}

		$loadingEl.addClass('ets-active');
		ets_pnf.maybeHideACResults($listEl, true);
		$.ajax({
			url: ets_pnf_module_link,
			data: {
				ids: productId,
				action: 'etsPnfAddProduct'
			},
			type: 'post',
			dataType: 'json',
			success: function(json) {
				if (json) {
					var ids = $productIdsEl.val();
					$loadingEl.removeClass('ets-active').before(json.html);
					if (!ids) {
						$productIdsEl.val(productId);
					}
					else if (ids.split(',').indexOf(productId) == -1) {
						$productIdsEl.val($productIdsEl.val() + ',' + productId);
					}
					else {
						showErrorMessage(data[1].toString());
					}
					$inputAC.unautocomplete();
					ets_pnf.searchProduct();
					$inputAC.click();
					ets_pnf.maybeHideACResults($listEl);
				}
			},
			error: function(xhr, status, error) {
				$loadingEl.removeClass('ets-active');
			}
		});
	},
	removeProduct: function(productId) {
		var productId = productId || 0,
			$listEl = $('.ets_pnf_search_list_ETS_PNF_PRODUCTS'),
			$inputAC = $('#ets_pnf_search_ETS_PNF_PRODUCTS'),
			$inputValEl = $('#ets_specific_ETS_PNF_PRODUCTS');
		if (confirm(ets_pnf_msg_confirm) && $listEl.length > 0 && $listEl.find('li[data-id="' + productId + '"]').length > 0 && $inputValEl.length > 0) {
			$('li.ets_pnf_item[data-id="' + productId + '"]').remove();
			$inputValEl.val(ets_pnf.removeIds($inputValEl.val().split(','), productId));
			ets_pnf.maybeHideACResults($listEl);
			$inputAC.unautocomplete();
			ets_pnf.searchProduct();
		}
	},
	addBlog: function($inputAC, data) {
		var $listEl = !etsPNFBlogFree ? $('.ets_pnf_search_list_ETS_PNF_BLOGS') : $('.ets_pnf_search_list_ETS_PNF_BLOGS_FREE');
		var $loadingEl = $('.ets_pnf_ac_results_loading', $listEl);
		if (!$listEl.length || !$loadingEl.length || !data) {
			return;
		}
		var $postIdsEl = !etsPNFBlogFree ? $('#ets_specific_ETS_PNF_BLOGS') : $('#ets_specific_ETS_PNF_BLOGS_FREE'),
			postId = parseInt(data[0], 10);
		if (!$postIdsEl.length || !postId || $loadingEl.hasClass('ets-active')) {
			return;
		}
		$loadingEl.addClass('ets-active');
		ets_pnf.maybeHideACResults($listEl, true);

		$.ajax({
			url: ets_pnf_module_link,
			data: {
				ids: postId,
				action: !etsPNFBlogFree ? 'etsPnfAddBlog' : 'etsPnfAddBlogFree'
			},
			type: 'post',
			dataType: 'json',
			success: function(json) {
				if (json) {
					var ids = $postIdsEl.val();
					$loadingEl.removeClass('ets-active').before(json.html);
					if (!ids) {
						$postIdsEl.val(postId);
					}
					else if (ids.split(',').indexOf(postId) == -1) {
						$postIdsEl.val($postIdsEl.val() + ',' + postId);
					}
					else {
						showErrorMessage(data[1].toString());
					}
					$inputAC.unautocomplete();
					ets_pnf.searchBlog();
					$inputAC.click();
					ets_pnf.maybeHideACResults($listEl);
				}
			},
			error: function(xhr, status, error) {
				$loadingEl.removeClass('ets-active');
			}
		});
	},
	removeIds: function(parent, element) {
		var ax = -1;
		if ((ax = parent.indexOf(element)) !== -1) {
			parent.splice(ax, 1);
		} else {
			var elementStr = element.toString();
			if ((ax = parent.indexOf(elementStr)) !== -1) {
				parent.splice(ax, 1);
			}
		}
		return parent;
	},
	removeBlog: function(postId) {
		var postId = postId || 0,
			$listEl = !etsPNFBlogFree ? $('.ets_pnf_search_list_ETS_PNF_BLOGS') : $('.ets_pnf_search_list_ETS_PNF_BLOGS_FREE'),
			$inputAC = !etsPNFBlogFree ? $('#ets_pnf_search_ETS_PNF_BLOGS') : $('#ets_pnf_search_ETS_PNF_BLOGS_FREE'),
			$inputValEl = !etsPNFBlogFree ? $("#ets_specific_ETS_PNF_BLOGS") : $("#ets_specific_ETS_PNF_BLOGS_FREE");
		if (confirm(ets_pnf_msg_confirm) && $listEl.length > 0 && $listEl.find('li[data-id="' + postId + '"]').length > 0 && $inputValEl.length > 0) {
			$('li.ets_pnf_item[data-id="' + postId + '"]').remove();
			$inputValEl.val(ets_pnf.removeIds($inputValEl.val().split(','), postId));
			ets_pnf.maybeHideACResults($listEl);
			$inputAC.unautocomplete();
			ets_pnf.searchBlog();
		}
	},
	
	searchProduct: function() {
		var $inputVal = $('#ets_specific_ETS_PNF_PRODUCTS'),
			$inputAC = $('#ets_pnf_search_ETS_PNF_PRODUCTS'),
			$appendTar = $('.ets_pnf_search_list_ETS_PNF_PRODUCTS'),
			resultsClass = 'ets_pnf_products_search_ac';
		var $results = $('.' + resultsClass);
		if (!$inputAC.length || typeof ets_pnf_module_link === "undefined" || !ets_pnf_module_link) {
			return;
		}
		$inputAC.autocomplete(ets_pnf_module_link + '&type=product', {
			resultsClass: resultsClass,
			minChars: 1,
			appendTo: $appendTar,
			autoFill: true,
			max: 20,
			matchContains: true,
			mustMatch: true,
			scroll: true,
			scrollHeight: 360,
			extraParams: {
				excludeIds: $inputVal.val(),
			},
			formatItem: function(item) {
				return '<div class="ets-pnf-item-mini ets-pnf-product-mini" data-item-id="' + item[0] + '">' +
					'<img class="ets-item__img" src="' + item[3] + '" width="64"/>' +
					'<div class="ets-item__info">' +
						'<span class="ets-item__name">' + item[1] + (item[2] ? ' (' + item[2] + ')' : '') + '</span>' +
						'<span class="ets-item__price">ID: ' + item[0] + (item[4] ? ' | ' + item[4] : '') + '</span>' +
					'</div>' +
				'</div>';
			}
		}).result(function(event, data, formatted) {
			if (data == null || !data) {
				return false;
			}
			ets_pnf.addProduct($inputAC, data);
			ets_pnf.closeSearch($inputAC, $results);
		});
	},
	searchBlog: function() {
		var $inputVal = !etsPNFBlogFree ? $('#ets_specific_ETS_PNF_BLOGS') : $('#ets_specific_ETS_PNF_BLOGS_FREE'),
			$inputAC = !etsPNFBlogFree ? $('#ets_pnf_search_ETS_PNF_BLOGS') : $('#ets_pnf_search_ETS_PNF_BLOGS_FREE'),
			$appendTar = !etsPNFBlogFree ? $('.ets_pnf_search_list_ETS_PNF_BLOGS') : $('.ets_pnf_search_list_ETS_PNF_BLOGS_FREE'),
			resultsClass = !etsPNFBlogFree ? 'ets_pnf_posts_search_ac' : 'ets_pnf_posts_search_ac_free';
		var $results = $('.' + resultsClass);
		if (!$inputAC.length || typeof ets_pnf_module_link === "undefined" || !ets_pnf_module_link) {
			return;
		}
		if ($inputAC.length > 0 && typeof ets_pnf_module_link !== "undefined" && ets_pnf_module_link) {
			$inputAC.autocomplete(ets_pnf_module_link + '&type=' + (!etsPNFBlogFree ? 'blog' : 'blogFree'), {
				resultsClass: resultsClass,
				minChars: 1,
				appendTo: $appendTar,
				autoFill: true,
				max: 20,
				matchContains: true,
				mustMatch: true,
				scroll: true,
				scrollHeight: 360,
				extraParams: {
					excludeIds: $inputVal.val(),
				},
				formatItem: function(item) {
					return '<div class="ets-pnf-item-mini ets-pnf-post-mini" data-item-id="' + item[0] + '">' +
						'<img class="ets-item__img" src="' + item[2] + '" width="64"/>' +
						'<div class="ets-item__info">' +
							'<span class="ets-item__name">' + item[1] + '</span>' +
							'<span class="ets-item__id">ID: ' + item[0] + '</span>' +
						'</div>' +
					'</div>';
				}
			}).result(function(event, data, formatted) {
				if (data == null || !data) {
					return false;
				}
				ets_pnf.addBlog($inputAC, data);
				ets_pnf.closeSearch($inputAC, $results);
			});
		}
	},
	readImage: function(input, evt, rootEle = '.form_ets_pnf_image') {
		if (input.files && input.files[0]) {
			if ($(input).closest('.ets-form-group' + rootEle).find('.ets_uploaded_img_wrapper').length) {
				$(input).closest('.ets-form-group' + rootEle).find('.ets_uploaded_img_wrapper').hide();
			}
			var imgSrc = URL.createObjectURL(evt.target.files[0]);
			var $imgEl = $(input).closest('.ets-form-group' + rootEle).find('.ets_mp_level_badge_image');
			if ($imgEl.length <= 0) {
				var $imgBox = $(`<div class="form-group level_badge_image">
					<label class="control-label col-lg-4 uploaded_image_label" style="font-style: italic;">&nbsp;</label>
					<div class="col-lg-8">
						<img class="ets_mp_level_badge_image" src="" style="display: inline-block; max-width: 240px;">
					</div>
				</div>`);
				$(input).closest('.ets-form-group' + rootEle).append($imgBox);
				$imgEl = $('.ets_mp_level_badge_image', $imgBox);
				$imgEl.on('load', function() {
					URL.revokeObjectURL(imgSrc) // free memory
				});
			}
			$imgEl.attr('src', imgSrc);
		}
	}
}

jQuery(document).ready(function($) {
	ets_pnf.searchProduct();
	ets_pnf.searchBlog();
	ets_pnf.maybeHideACResults($('.ets_pnf_search_list_ETS_PNF_PRODUCTS'));
	ets_pnf.maybeHideACResults($('.ets_pnf_search_list_ETS_PNF_BLOGS'));
	ets_pnf.maybeHideACResults($('.ets_pnf_search_list_ETS_PNF_BLOGS_FREE'));

	$(document).on('change', 'input[type="radio"]', function() {
		var name = this.name.toLowerCase(),
			wrapperSelector = '#ets-pnf-config-form .js-for_' + name;
		if ('ets_pnf_display_blogs' == name) {
			if ('undefined' == typeof etsPNFBlogInstalled || !etsPNFBlogInstalled) {
				return;
			}
			wrapperSelector += !etsPNFBlogFree ? '.form_ets_pnf_blogs' : '.form_ets_pnf_blogs_free';
			wrapperSelector += ',#ets-pnf-config-form .form_ets_pnf_title_blogs';
		}
		var $wrapper = $(wrapperSelector);
		if ($wrapper.length) {
			var $formGroup = $wrapper.children('.form-group');
			if ($(this).val() == '1') {
				$wrapper.removeClass('ets_pnf_hide').addClass('ets_pnf_show');
				if ($formGroup.length) {
					$formGroup.removeClass('hide');
				}
			} else {
				if ($formGroup.length) {
					$wrapper.removeClass('ets_pnf_show').addClass('ets_pnf_hide');
					$formGroup.addClass('hide');
				}
			}
		}
	});

	if ($('.js-for_ets_pnf_display_blogs > .form-group.hide').length) {
		$('.js-for_ets_pnf_display_blogs > .form-group.hide').parent().addClass('ets_pnf_hide');
	}

	$(document).on('click', '.ets_pnf_item_product .ets-item__action.ets-action-remove', function() {
		if ($(this).closest('li').data('id') != '') {
			ets_pnf.removeProduct($(this).parents('li').data('id'));
		}
	});
	$(document).on('click', '.ets_pnf_item_blog .ets-item__action.ets-action-remove', function() {
		if ($(this).closest('li').data('id') != '') {
			ets_pnf.removeBlog($(this).parents('li').data('id'));
		}
	});


	$(document).on('change','input[name="ETS_PNF_IMAGE"]',function(e){
		var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
		if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) != -1) {
			ets_pnf.readImage(this, e);
		}
	});

	if ('undefined' != typeof $.fn.fancybox) {
		$('.ets_uploaded_img_wrapper .ets_fancy').fancybox();
	}
});