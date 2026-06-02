{if !empty($wtbanner.image_url)}
  <section class="wtbanner" aria-label="Whisky Time banner">
    <div class="wtbanner__media">
      <img
        src="{$wtbanner.image_url|escape:'htmlall':'UTF-8'}"
        alt="{$wtbanner.alt|escape:'htmlall':'UTF-8'}"
        fetchpriority="high"
        loading="eager"
      >
    </div>
  </section>
{/if}
