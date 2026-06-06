<section class="wtholidays wtholidays--home" aria-label="{$wtholidays.headline|escape:'html':'UTF-8'}">
  <div class="container">
    <div class="wtholidays__card wtholidays__card--home">
      {if $wtholidays.headline}
        <h2 class="title_blog title_block wtholidays__headline">{$wtholidays.headline|escape:'html':'UTF-8'}</h2>
      {/if}
      <p class="wthome__intro wtholidays__message wtholidays__message--home">{$wtholidays.message|escape:'html':'UTF-8'|nl2br nofilter}</p>
    </div>
  </div>
</section>
