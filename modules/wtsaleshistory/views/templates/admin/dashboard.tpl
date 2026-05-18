<div class="panel wtsh-panel">
  <div class="panel-heading">
    <i class="icon icon-line-chart"></i>
    {l s='Courbes de ventes' mod='wtsaleshistory'}
  </div>

  <div class="wtsh-toolbar">
    <form method="post" action="{$wtsh_refresh_action|escape:'htmlall':'UTF-8'}" class="wtsh-refresh-form">
      <button type="submit" class="btn btn-primary" name="submitRefreshWtSalesHistory">
        <i class="icon icon-refresh"></i>
        {l s='Refresh' mod='wtsaleshistory'}
      </button>
    </form>
    <div class="wtsh-note">
      {l s='En cas de chevauchement, les données Prestashop remplacent les données legacy pour le mois concerné.' mod='wtsaleshistory'}
    </div>
  </div>

  <div class="row wtsh-summary">
    <div class="col-lg-3 col-md-6">
      <div class="wtsh-card">
        <div class="wtsh-card-label">{l s='Commandes affichées' mod='wtsaleshistory'}</div>
        <div class="wtsh-card-value">{$wtsh_summary.order_count|intval}</div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="wtsh-card">
        <div class="wtsh-card-label">{l s='Total ventes affiché' mod='wtsaleshistory'}</div>
        <div class="wtsh-card-value">{$wtsh_currency_sign|escape:'htmlall':'UTF-8'} {$wtsh_summary.total_sales|string_format:"%.2f"}</div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="wtsh-card">
        <div class="wtsh-card-label">{l s='Mois legacy chargés' mod='wtsaleshistory'}</div>
        <div class="wtsh-card-value">{$wtsh_summary.legacy_rows|intval}</div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="wtsh-card">
        <div class="wtsh-card-label">{l s='Mois Prestashop calculés' mod='wtsaleshistory'}</div>
        <div class="wtsh-card-value">{$wtsh_summary.prestashop_rows|intval}</div>
      </div>
    </div>
  </div>

  <div class="wtsh-meta">
    <span><strong>{l s='Dernier refresh :' mod='wtsaleshistory'}</strong> {$wtsh_summary.last_refresh|default:'-'|escape:'htmlall':'UTF-8'}</span>
    <span><strong>{l s='États inclus :' mod='wtsaleshistory'}</strong> {$wtsh_state_labels_text|escape:'htmlall':'UTF-8'}</span>
  </div>

  <div class="wtsh-filters">
    <div class="wtsh-filter-title">{l s='Années visibles' mod='wtsaleshistory'}</div>
    <div class="wtsh-year-list">
      {foreach from=$wtsh_years item=year}
        <label class="wtsh-year-pill">
          <input type="checkbox" class="js-wtsh-year-toggle" value="{$year|intval}" checked>
          <span>{$year|intval}</span>
        </label>
      {/foreach}
    </div>
  </div>

  <div id="wtsh-chart-root" class="wtsh-chart-root" data-chart="{$wtsh_payload_json|escape:'htmlall':'UTF-8'}">
    <div class="wtsh-chart-header">
      <div class="wtsh-axis wtsh-axis-left">{l s='Nombre de ventes' mod='wtsaleshistory'}</div>
      <div class="wtsh-axis wtsh-axis-right">{l s='Total ventes' mod='wtsaleshistory'} ({$wtsh_currency_sign|escape:'htmlall':'UTF-8'})</div>
    </div>
    <div class="wtsh-chart-body">
      <svg class="wtsh-svg" viewBox="0 0 1200 520" preserveAspectRatio="none" aria-hidden="true"></svg>
    </div>
    <div class="wtsh-legend"></div>
    <div class="wtsh-legend-note">
      <span class="wtsh-line-sample wtsh-line-sample-solid"></span> {l s='Nombre de ventes' mod='wtsaleshistory'}
      <span class="wtsh-line-sample wtsh-line-sample-dashed"></span> {l s='Total ventes' mod='wtsaleshistory'}
    </div>
  </div>
</div>
