<section class="wtholidays wthome wtholidays--home" aria-label="{$wtholidays.headline|escape:'html':'UTF-8'}">
  <div class="container">
    <article class="wthome__welcome wtholidays__home-block" style="background:#fff;border:0;border-radius:0;box-shadow:none;">
      <div class="wthome__welcome-copy" style="background:#fff;min-height:0;padding:0;">
        {if $wtholidays.headline}
          <h2 class="title_blog title_block wthome__title wtholidays__headline" style="font-family:'Great Vibes',cursive;color:#000;font-size:48px;font-weight:400;line-height:1.05;letter-spacing:normal;text-transform:none;margin:0 0 14px;padding:0;display:inline-block;">{$wtholidays.headline|escape:'html':'UTF-8'}</h2>
        {/if}
        <p class="wthome__intro wtholidays__message wtholidays__message--home" style="margin:0;color:#63574c;font-size:15px;line-height:1.75;">{$wtholidays.message|escape:'html':'UTF-8'|nl2br nofilter}</p>
      </div>
    </article>
  </div>
</section>
