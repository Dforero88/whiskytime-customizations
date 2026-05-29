<section class="wthome">
  <div class="container">
    <div class="wthome__welcome">
      <div class="wthome__welcome-copy">
        <h2 class="title_blog title_block wthome__title">{$wthome_welcome.title|escape:'html':'UTF-8'}</h2>
        <p>{$wthome_welcome.body|escape:'html':'UTF-8'}</p>
      </div>
    </div>

    {if !$wthome_event.empty}
      <div class="row wthome__feature">
        <div class="col-lg-6 col-md-6 wthome__feature-copy">
          <article class="wthome__panel">
            <h2 class="title_blog title_block wthome__title">{$wthome_event.title|escape:'html':'UTF-8'}</h2>
            <p class="wthome__intro">{$wthome_event.description|escape:'html':'UTF-8'}</p>
          </article>
        </div>

        <div class="col-lg-6 col-md-6 wthome__feature-card">
          <article class="product-miniature js-product-miniature wthome-product-card wthome-product-card--event">
            <div class="thumbnail-container">
              <a class="wthome-product-card__overlay-link" href="{$wthome_event.link|escape:'html':'UTF-8'}" aria-label="{$wthome_event.name|escape:'html':'UTF-8'}"></a>
              {if $wthome_event.image}
                <div class="product-image-block">
                  <div class="thumbnail product-thumbnail">
                    <img src="{$wthome_event.image|escape:'html':'UTF-8'}" alt="{$wthome_event.name|escape:'html':'UTF-8'}" loading="lazy">
                  </div>
                </div>
              {/if}

              <div class="product-description">
                <div class="wthome-product-card__meta">
                  <span class="wthome-product-card__date">{$wthome_event.date|date_format:"%A %e %B %Y"|capitalize:true|escape:'html':'UTF-8'}</span>
                  {if $wthome_event.price !== null}
                    <span class="wthome-product-card__price">{$wthome_event.price|escape:'html':'UTF-8'}</span>
                  {/if}
                </div>
                <h3 class="h3 product-title">
                  <span>{$wthome_event.name|escape:'html':'UTF-8'}</span>
                </h3>
              </div>
            </div>
          </article>
        </div>
      </div>
    {/if}

    <div class="row wthome__feature wthome__feature--reverse">
      <div class="col-lg-6 col-md-6 wthome__feature-card">
        {if $wthome_giftcard.empty}
          <div class="wthome__item-card wthome__item-card--empty">
            <p class="wthome__empty">{$wthome_giftcard.empty_message|escape:'html':'UTF-8'}</p>
            <a class="wthome__button wthome__button--secondary" href="{$wthome_giftcard.all_link|escape:'html':'UTF-8'}">{$wthome_giftcard.all_label|escape:'html':'UTF-8'}</a>
          </div>
        {else}
          <div class="wthome__giftcards-list">
            {foreach from=$wthome_giftcard.items item=giftItem}
              <article class="product-miniature js-product-miniature wthome-product-card wthome-product-card--gift">
                <div class="thumbnail-container">
                  <a class="wthome-product-card__overlay-link" href="{$giftItem.link|escape:'html':'UTF-8'}" aria-label="{$giftItem.name|escape:'html':'UTF-8'}"></a>
                  {if $giftItem.image}
                    <div class="product-image-block">
                      <div class="thumbnail product-thumbnail">
                        <img src="{$giftItem.image|escape:'html':'UTF-8'}" alt="{$giftItem.name|escape:'html':'UTF-8'}" loading="lazy">
                      </div>
                    </div>
                  {/if}

                  <div class="product-description">
                    {if $giftItem.range}
                      <div class="wthome-product-card__meta">
                        <span class="wthome-product-card__price">{$giftItem.range|escape:'html':'UTF-8'}</span>
                      </div>
                    {/if}
                    <h3 class="h3 product-title">
                      <span>{$giftItem.name|escape:'html':'UTF-8'}</span>
                    </h3>
                    {if $giftItem.range}
                      <p class="wthome__gift-note">{l s='Montants disponibles à choix.' mod='wthome'}</p>
                    {/if}
                  </div>
                </div>
              </article>
            {/foreach}
          </div>
        {/if}
      </div>

      <div class="col-lg-6 col-md-6 wthome__feature-copy">
        <article class="wthome__panel">
          <h2 class="title_blog title_block wthome__title">{$wthome_giftcard.title|escape:'html':'UTF-8'}</h2>
          <p class="wthome__intro">{$wthome_giftcard.description|escape:'html':'UTF-8'}</p>
        </article>
      </div>
    </div>
  </div>
</section>
