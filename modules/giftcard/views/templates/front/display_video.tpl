{**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by Solver Web Tech.
*
*  @author    Solver Web Tech <solverwebtech@gmail.com>
*  @copyright Solver Web Tech 2023
*  @license   Single domain
*}
{extends file="page.tpl"}
{block name="page_content"}
   <div class="col-md-12" style="text-align: center;">
   {if !empty($media) && $media_exist == true}
      {if strpos($media['video_link'], 'youtube.com') !== false || strpos($media['video_link'], 'vimeo.com') !== false}
         <iframe width="100%" height="500" src="{$media['video_link']|escape:'htmlall':'UTF-8'}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      {else}
         <video controls style="margin: auto; float: none; width: 100%;">
            <source src="{$media['video_link']|escape:'htmlall':'UTF-8'}" type="video/mp4">
            <source src="{$media['video_link']|escape:'htmlall':'UTF-8'}" type="video/ogg">
            <source src="{$media['video_link']|escape:'htmlall':'UTF-8'}" type="video/webm">
            {l s='Your browser does not support the video tag.' mod='giftcard'}
         </video>
      {/if}
      <br><br>
   {else}
      <p class="alert alert-warning" style="width: fit-content; margin: auto;">
         <strong>{l s='Meia has expired' mod='giftcard'}</strong>
      </p>
   {/if}
   </div>
{/block}
