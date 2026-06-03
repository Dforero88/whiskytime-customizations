{if !empty($wtbanner.image_url)}
  <section class="wtbanner" aria-label="Whisky Time banner" style="width:100vw;max-width:100vw;margin:0 0 2rem 0;margin-left:calc(50% - 50vw);">
    <div class="wtbanner__media" style="display:block;width:100vw;max-width:100vw;aspect-ratio:5 / 1;overflow:hidden;">
      <img
        src="{$wtbanner.image_url|escape:'htmlall':'UTF-8'}"
        alt="{$wtbanner.alt|escape:'htmlall':'UTF-8'}"
        fetchpriority="high"
        loading="eager"
        style="display:block;width:100vw;min-width:100vw;max-width:none;height:100%;min-height:100%;object-fit:cover;object-position:center center;"
      >
    </div>
  </section>
{/if}
