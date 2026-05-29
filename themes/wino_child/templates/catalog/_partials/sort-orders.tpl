{assign var=has_mobile_filters value=(!empty($listing.rendered_facets) || (isset($page.page_name) && $page.page_name == 'category'))}
<span class="col-sm-3 col-md-3 hidden-sm-down sort-by">{l s='Sort by:' d='Shop.Theme.Global'}</span>
<div class="{if $has_mobile_filters}col-sm-9 col-xs-8{else}col-sm-12 col-xs-12{/if} col-md-9 products-sort-order dropdown">
  <button
    class="btn-unstyle select-title"
    rel="nofollow"
    data-toggle="dropdown"
    aria-haspopup="true"
    aria-expanded="false">
    {if isset($listing.sort_selected)}{$listing.sort_selected}{else}{l s='Select' d='Shop.Theme.Actions'}{/if}
    <i class="material-icons float-xs-right">&#xE5C5;</i>
  </button>
  <div class="dropdown-menu">
    {foreach from=$listing.sort_orders item=sort_order}
      <a
        rel="nofollow"
        href="{$sort_order.url}"
        class="select-list {['current' => $sort_order.current, 'js-search-link' => true]|classnames}"
      >
        {$sort_order.label}
      </a>
    {/foreach}
  </div>
</div>
