<div class="panel">
  <h3>WT Traduction</h3>
  <p>Descriptions et récapitulatifs FR à traduire en anglais. Les textes anglais existants sont conservés.</p>
  {if $wt_can_edit}
  <form method="post" action="{$wt_url|escape:'html':'UTF-8'}">
    <label for="wt-deepl-key">Clé DeepL {if $wt_configured}(déjà configurée){/if}</label>
    <input id="wt-deepl-key" name="deepl_key" type="password" autocomplete="new-password" placeholder="Laisser vide pour conserver la clé actuelle">
    <button class="btn btn-default" name="saveDeepL" type="submit">Enregistrer la clé</button>
  </form>
  {/if}
</div>
<div class="panel" id="wt-translation" data-url="{$wt_url|escape:'html':'UTF-8'}">
  <h3>{$wt_rows|count} produit(s) à traduire</h3>
  <div id="wt-error" class="alert alert-danger" hidden></div>
  <div id="wt-progress" class="alert alert-info" hidden></div>
  {if $wt_rows && $wt_can_edit}
    <button id="wt-translate" type="button" class="btn btn-primary" {if !$wt_configured}disabled{/if}>Traduire tous les produits</button>
  {/if}
  <table class="table">
    <thead><tr><th>ID</th><th>Référence</th><th>Produit</th><th>Champs à traduire</th></tr></thead>
    <tbody>
    {foreach $wt_rows as $row}
      <tr data-product="{$row.id_product|intval}">
        <td>{$row.id_product|intval}</td>
        <td>{$row.reference|escape:'html':'UTF-8'}</td>
        <td><a href="{$row.edit_url|escape:'html':'UTF-8'}">{$row.name|escape:'html':'UTF-8'}</a></td>
        <td>{if $row.missing_description}Description{/if}{if $row.missing_description && $row.missing_short} et {/if}{if $row.missing_short}Récapitulatif{/if}</td>
      </tr>
    {foreachelse}
      <tr><td colspan="4">Aucune traduction manquante.</td></tr>
    {/foreach}
    </tbody>
  </table>
</div>
