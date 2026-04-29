{if $popupimage_file}
<div id="popupimage-overlay" hidden>
  <div id="popupimage-box">
    <button type="button" id="popupimage-close" aria-label="Close popup">&times;</button>
    {if $popupimage_text}
      <div id="popupimage-content">
        {$popupimage_text nofilter}
      </div>
    {/if}
    <img src="{$popupimage_file}" alt="Popup" />
  </div>
</div>
{/if}
