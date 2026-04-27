{**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2024 FMM Modules
*  @license   FMM Modules
*}

<div class="panel" id="fmm_promo_panel">
  <div class="col-lg-3">
    <ul>
      <li><a href="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/readme_en.pdf" target="_blank" title="Need Help">{if Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') > 0}<i class="material-icons">&#xE887;</i>{else}<i class="icon-question-circle"></i>{/if}Help?</a></li>
      <li class="color_red"><a href="https://addons.prestashop.com/contact-form.php?id_product=18139" target="_blank" title="Need Support">{if Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') > 0}<i class="material-icons">&#xE0C6;</i>{else}<i class="icon-comments"></i>{/if}Support</a></li>
      <li class="color_blue"><a href="https://addons.prestashop.com/en/wishlist-gift-card/18139-gift-card-let-users-send-gift-certificates-vouchers.html" target="_blank" title="Need Details">{if Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') > 0}<i class="material-icons">&#xE8F4;</i>{else}<i class="icon-eye"></i>{/if}Details</a></li>
      <li class="color_orange"><a href="http://addons.prestashop.com/en/ratings.php" target="_blank" title="Rate us 5 stars">{if Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') > 0}<i class="material-icons">&#xE8D0;</i>{else}<i class="icon-gratipay"></i>{/if}Rate Us</a></li>
    </ul>
  </div>
  <div class="col-lg-7 fmm_promo_modules">
    <ol>
      <li class="fmm_promo_maylike">Modules you may like...</li>
      <li><a href="https://addons.prestashop.com/en/url-redirects/16633-pretty-urls-remove-ids-numbers-for-seo-friendly-url.html" target="_blank" title="Pretty URLs"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/pretty-seo-friendly-urls.png" alt="" /><i>Pretty URLs Module</i></a></li>
      <li><a href="https://addons.prestashop.com/en/registration-ordering-process/18819-pre-order-waiting-list-notification.html" target="_blank" title="Pre-Order"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/pre-order-notification-waiting-list-prestashop.jpg" alt="" /><i>Pre-order Module</i></a></li>
      <li><a href="https://addons.prestashop.com/en/private-sales-flash-sales/18884-flash-sales-pro-discount-with-countdown-timer.html" target="_blank" title="Flash Sales"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/flash-sale-pro.png" alt="" /><i>Flash Sales Module</i></a></li>
    </ol>
  </div>
  <div class="col-lg-2 fmm_promo_basecamp">
    <a href="https://addons.prestashop.com/en/139_fme-modules" title="FME Modules" target="_blank">
      <i>See all modules</i>
      <img src="https://www.fmemodules.com/img/prestashop-modules-and-addons-logo-1456381524.jpg" />
    </a>
  </div>
</div>
{literal}
<style type="text/css">
.panel:after { content: "."; clear: both; width: 100%; visibility: hidden; height: 0px; display: block;}
#fmm_promo_panel ul { padding: 0; margin: 0; list-style: none; font-size: 12px; color: #6ab233;}
#fmm_promo_panel ul a { text-decoration: none; color: #6ab233;}
#fmm_promo_panel ul a:hover,
#fmm_promo_panel ul a:focus,
#fmm_promo_panel ul a:active { text-decoration: none; color: #515151 !important;}
#fmm_promo_panel ul li { list-style: none; display: inline-block; width: 23%; margin-right: 1%; text-align: center;}
#fmm_promo_panel ul li i { display: block; clear: both; font-size: 42px;}
#fmm_promo_panel ul li.color_red,
#fmm_promo_panel ul li.color_red a { color: red;}
#fmm_promo_panel ul li.color_blue,
#fmm_promo_panel ul li.color_blue a { color: #4169E1;}
#fmm_promo_panel ul li.color_orange,
#fmm_promo_panel ul li.color_orange a { color: #fbbb22;}
.fmm_promo_modules ol { padding: 0; margin: 0; list-style: none; font-size: 11px; color: #6ab233; display: inline-block;}
.fmm_promo_modules ol li { list-style: none; display: inline-block; width: 22%; margin-right: 1%; text-align: center; vertical-align: middle}
.fmm_promo_modules ol li.fmm_promo_maylike { width: 20%;font-size: 12px; color: #6ab233; text-transform: uppercase;padding: 0; margin: 0 3% 0 0;
font-weight: bold;}
.fmm_promo_modules ol li a {display: block; line-height: 18px;text-decoration: none; color: #6ab233;}
.fmm_promo_modules ol li a img { display: inline-block; padding-right: 1%; max-width: 49%; vertical-align: middle; width: 49%;}
.fmm_promo_modules ol li i { font-style: normal; display: inline-block; vertical-align: middle; width: 50%;}
.fmm_promo_basecamp { text-align: center;}
.fmm_promo_basecamp a { text-decoration: none;color:#6ab233;}
.fmm_promo_basecamp a:hover,
.fmm_promo_basecamp a:active,
.fmm_promo_basecamp a:focus { text-decoration: none; color:#515151 !important; }
.fmm_promo_basecamp i { font-style: normal; text-transform: uppercase; font-weight: 700; padding: 4px;
border: 1px solid #6ab233; border-radius: 4px; display: block; clear: both; margin-bottom: 5px; }
.fmm_promo_basecamp a:hover i {color:#515151 !important; border-color:#515151 !important; }
.fmm_promo_basecamp img { max-width: 100%; width: 150px;}
@media (min-width: 200px) and (max-width: 1190px) {
.fmm_promo_modules,
.fmm_promo_basecamp { padding-top: 15px;}
}
@media (min-width: 200px) and (max-width:430px) {
.fmm_promo_modules { display: none}
}
</style>{/literal}