{assign var=has_mobile_filters value=(!empty($listing.rendered_facets) || (isset($page.page_name) && $page.page_name == 'category'))}
<div id="js-product-list-top" class="row products-selection">
  <div class="col-md-5 hidden-sm-down total-products">
  <!--<i class="material-icons show_grid">&#xE8F0;</i>
  <i class="material-icons show_list">&#xE8EF;</i>-->
  <span class="show_grid"></span>
  <span class="show_list"></span>
    {if $listing.pagination.total_items > 1}
      <p>{l s='There are %product_count% products.' d='Shop.Theme.Catalog' sprintf=['%product_count%' => $listing.pagination.total_items]}</p>
    {else if $listing.pagination.total_items > 0}
      <p>{l s='There is 1 product.' d='Shop.Theme.Catalog'}</p>
    {/if}
  </div>
  <div class="col-md-7">
    <div class="sort-by-row">

      {block name='sort_by'}
        {include file='catalog/_partials/sort-orders.tpl' sort_orders=$listing.sort_orders}
      {/block}

      {if $has_mobile_filters}
        <div class="col-sm-3 col-xs-4 hidden-md-up filter-button">
          <button id="search_filter_toggler" class="btn btn-secondary">
            {l s='Filter' d='Shop.Theme.Actions'}
          </button>
        </div>
      {/if}
    </div>
  </div>
  <div class="col-sm-12 col-md-5 hidden-md-up text-sm-center showing">
    {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=[
    '%from%' => $listing.pagination.items_shown_from ,
    '%to%' => $listing.pagination.items_shown_to,
    '%total%' => $listing.pagination.total_items
    ]}
  </div>
</div>
