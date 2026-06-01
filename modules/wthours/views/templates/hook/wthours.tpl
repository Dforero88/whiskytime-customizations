<section class="wthours">
  <div class="container">
    <div class="wthours__inner">
      <div class="wthours__panel wthours__copy" id="wthours-hours">
        <h2 class="title_blog title_block wthours__title">{$wthours.title|escape:'html':'UTF-8'}</h2>
        <div class="wthours__text">{$wthours.body|nl2br nofilter}</div>
      </div>
      <div class="wthours__panel wthours__location" id="wthours-address">
        <h2 class="title_blog title_block wthours__title">{$wthours.address_label|escape:'html':'UTF-8'}</h2>
        <div class="wthours__address">
          <span class="wthours__address-line">{$wthours.address_line_1|escape:'html':'UTF-8'}</span>
          <span class="wthours__address-line">{$wthours.address_line_2|escape:'html':'UTF-8'}</span>
        </div>
      </div>
      <div class="wthours__panel wthours__contact" id="wthours-contact">
        <h2 class="title_blog title_block wthours__title">{$wthours.contact_label|escape:'html':'UTF-8'}</h2>
        <div class="wthours__contact-items">
          <a class="wthours__contact-item" href="tel:+41217917002" aria-label="{$wthours.phone|escape:'html':'UTF-8'}">
            <i class="material-icons" aria-hidden="true">phone</i>
            <span>{$wthours.phone|escape:'html':'UTF-8'}</span>
          </a>
          <a class="wthours__contact-item" href="mailto:{$wthours.email|escape:'html':'UTF-8'}" aria-label="{$wthours.email|escape:'html':'UTF-8'}">
            <i class="material-icons" aria-hidden="true">mail</i>
            <span>{$wthours.email|escape:'html':'UTF-8'}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
