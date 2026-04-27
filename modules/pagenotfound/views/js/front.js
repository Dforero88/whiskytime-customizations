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
var ets_pnf = {
	initSlider: function () {
		var $slider = $('.ets-pnf-list.js_ets-pnf-slider');
		$slider.each(function() {
			var $_slider = $(this);
			if (!$_slider.hasClass('slick-slider')) {
				$_slider.slick({
					slidesToShow: 4,
					slidesToScroll: 1,
					arrows: true,
					responsive: [
						{
							breakpoint: 1200,
							settings: {
								slidesToShow: 4
							}
						},
						{
							breakpoint: 992,
							settings: {
								slidesToShow: 3
							}
						},
						{
							breakpoint: 768,
							settings: {
								slidesToShow: 2
							}
						},
						{
							breakpoint: 576,
							settings: {
								slidesToShow: 1
							}
						}
					]
				});

				$_slider.on('setPosition', function() {
					var _slider = this,
						$_img = $('img', $_slider),
						_imgHeigt = 0;
					$_img.each(function() {
						if (_imgHeigt < $_img.height()) {
							_imgHeigt = $_img.height();
						}
						_slider.style.setProperty('--pnf-slider-img-height', _imgHeigt + 'px');
					});
				});
			}
		});
	}
}

$(document).ready(function() {
	ets_pnf.initSlider();
});
