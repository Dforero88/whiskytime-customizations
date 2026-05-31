<section class="wthours" id="wthours">
  <div class="container">
    <div class="wthours__inner">
      <div class="wthours__copy" id="wthours-hours">
        <h2 class="title_blog title_block wthours__title">{$wthours.title|escape:'html':'UTF-8'}</h2>
        <div class="wthours__text">{$wthours.body|nl2br nofilter}</div>
      </div>
      <div class="wthours__location" id="wthours-address">
        <h2 class="title_blog title_block wthours__title">{$wthours.address_label|escape:'html':'UTF-8'}</h2>
        <div class="wthours__address">
          <span class="wthours__address-line">{$wthours.address_line_1|escape:'html':'UTF-8'}</span>
          <span class="wthours__address-line">{$wthours.address_line_2|escape:'html':'UTF-8'}</span>
        </div>
      </div>
    </div>
  </div>
</section>
