<section class="wtholidays wtholidays--home" aria-label="{$wtholidays.headline|escape:'html':'UTF-8'}">
  <div class="container">
    <div class="wtholidays__card wtholidays__card--home">
      {if $wtholidays.headline}
        <h2 class="wtholidays__headline">{$wtholidays.headline|escape:'html':'UTF-8'}</h2>
      {/if}
      <p class="wtholidays__message">{$wtholidays.message|escape:'html':'UTF-8'|nl2br nofilter}</p>
    </div>
  </div>
</section>
