{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<div class="block_newsletter col-lg-12 col-md-12 col-sm-12">
  <div class="row">
 
  <div class="newsletter-left col-md-5 col-xs-12 col-lg-5">
  	<span class="newsletter-icon"></span>
  	<div class="newsletter-title">
		{l s='Subscribe For The Newsletter' d='Shop.Theme.Global'}
	</div>
  	{if $conditions}
		<!--<span class="newsletter-desc">{$conditions}</span>-->
		<span class="newsletter-desc">{l s='You may unsubscribe at any moment. For that purpose, please find our contact info in the legal notice.' d='Shop.Theme.Global'}</span>
	  {/if}
  </div>
	
    <div class="col-md-7 col-xs-12 col-lg-7 newsletter-right">
      <form action="{$urls.pages.index}#footer" method="post">
        <div class="row">
          <div class="col-xs-12">
		  <div class="block_newsletter_inner">                       
              <input
                name="email"
                type="text"
                value="{$value}"
                placeholder="{l s='Enter Your Email Address...' d='Shop.Forms.Labels'}"
              >          
			<input
              class="btn btn-primary float-xs-right hidden-lg-down"
              name="submitNewsletter"
              type="submit"
              value="{l s='Subscribe' d='Shop.Theme.Actions'}"
            >
			<i class="fa fa-paper-plane-o" aria-hidden="true"></i>
            <input
              class="btn btn-primary float-xs-right hidden-xl-up"
              name="submitNewsletter"
              type="submit"
              value="{l s='OK' d='Shop.Theme.Actions'}"
            >
            <input type="hidden" name="action" value="0">
            <div class="clearfix"></div>
			</div>
          </div>
          <div class="col-xs-12">
              {if $msg}
                <p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
                  {$msg}
                </p>
              {/if}
              {if isset($id_module)}
                {hook h='displayGDPRConsent' id_module=$id_module}
              {/if}
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
