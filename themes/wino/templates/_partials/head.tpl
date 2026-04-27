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
{block name='head_charset'}
  <meta charset="utf-8">
{/block}
{block name='head_ie_compatibility'}
  <meta http-equiv="x-ua-compatible" content="ie=edge">
{/block}

{block name='head_seo'}
  <title>{block name='head_seo_title'}{$page.meta.title}{/block}</title>
  <meta name="description" content="{block name='head_seo_description'}{$page.meta.description}{/block}">
  <meta name="keywords" content="{block name='head_seo_keywords'}{$page.meta.keywords}{/block}">
  {if $page.meta.robots !== 'index'}
    <meta name="robots" content="{$page.meta.robots}">
  {/if}
  {if $page.canonical}
    <link rel="canonical" href="{$page.canonical}">
  {/if}
  {block name='head_hreflang'}
      {foreach from=$urls.alternative_langs item=pageUrl key=code}
            <link rel="alternate" href="{$pageUrl}" hreflang="{$code}">
      {/foreach}
  {/block}
{/block}

{block name='head_viewport'}
  <meta name="viewport" content="width=device-width, initial-scale=1">
{/block}

{* --- ANTI-FOUC CSS - À AJOUTER ICI --- *}
<style>
/* ANTI-FOUC - CSS CRITIQUE */

body:not(.loaded) #header,
body:not(.loaded) #content,
body:not(.loaded) #footer {
    opacity: 0 !important;
    visibility: hidden !important;
}

body.loaded .loadingdiv {
    display: none !important;
}

body.loaded #header,
body.loaded #content,
body.loaded #footer {
    opacity: 1 !important;
    visibility: visible !important;
    transition: opacity 0.3s ease-in-out !important;
}
</style>
{* --- FIN ANTI-FOUC --- *}

{block name='head_icons'}
  <link rel="icon" type="image/vnd.microsoft.icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
  <link rel="shortcut icon" type="image/x-icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
{/block}

{literal}
<style>
@font-face {
  font-family: 'Material Icons';
  src: url('/themes/wino/assets/css/570eb83859dc23dd0eec423a49e147fe.woff2') format('woff2');
  font-display: swap;
}
</style>

<!-- 2. Préchargement -->
<link rel="preload" href="/themes/wino/assets/css/570eb83859dc23dd0eec423a49e147fe.woff2" as="font" type="font/woff2" crossorigin>

{/literal}

{literal}
<script>
// Fallback pour les navigateurs qui ne supportent pas onload sur link
document.addEventListener('DOMContentLoaded', function() {
    var links = document.querySelectorAll('link[rel="stylesheet"][media="print"]');
    links.forEach(function(link) {
        link.onload = function() {
            this.media = 'all';
        };
        // Si déjà chargé, déclencher manuellement
        if (link.sheet) {
            link.media = 'all';
        }
    });
});
</script>
{/literal}

<link rel="preload" href="https://whiskytimeshop.ch/modules/aei_imageslider/views/img/9f6a3f8c0ac0c9e57011984fdb793f343479b7b7_a-photograph-of-a-weathered-wooden-table_zsvzls74QpauBj4hbeYpzw_ipZp3f15QEeLuvicz4L_cg.webp" as="image" fetchpriority="high">

<link rel="preload" href="/themes/wino/assets/css/local-fonts.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

{block name='stylesheets'}
  {include file="_partials/stylesheets.tpl" stylesheets=$stylesheets}
{/block}


{block name='javascript_head'}
  {include file="_partials/javascript.tpl" javascript=$javascript.head vars=$js_custom_vars}
{/block}

{block name='hook_header'}
  {$HOOK_HEADER nofilter}
{/block}

{block name='hook_extra'}{/block}

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17463477269"></script>
{literal}
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'AW-17463477269');
</script>
{/literal}