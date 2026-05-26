{*
 * Theme override for ETS blog listing page.
 * Keeps module rendering logic intact and only adapts the page wrapper.
*}
{extends file="page.tpl"}

{block name="content"}
  <div class="wtblog-page">
    <div class="wtblog-page__layout">
      <aside id="left-column" class="col-xs-12 col-sm-4 col-md-3 wtblog-page__sidebar">
        <div class="ets_blog_sidebar wtblog-page__sidebar-inner">
          {$blog_left_sidebar nofilter}
        </div>
      </aside>
      <div id="content-wrapper" class="left-column col-xs-12 col-sm-8 col-md-9 wtblog-page__content">
        <div class="wtblog-page__content-inner">
          {$blog_content nofilter}
        </div>
      </div>
    </div>
  </div>
{/block}
