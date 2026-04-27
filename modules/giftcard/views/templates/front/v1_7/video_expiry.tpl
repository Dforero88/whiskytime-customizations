{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2021 FMM Modules
*  @license   FMM Modules
*}

{extends file="page.tpl"}

{block name="page_content"}
   <div class="card card-block">
            {if isset($errors) && $errors}
               <h3>{l s='An error occurred' mod='giftcard'}:</h3>
               <ul class="alert alert-danger">
                  {foreach from=$errors item='error'}
                     <li>{$error|escape:'htmlall':'UTF-8'}.</li>
                  {/foreach}
               </ul>
            {/if}
            <hr>
            <p class="alert alert-info info">
                {l s='Deleted Giftcards Videos' mod='giftcard'}:{$result.deleted|escape:'htmlall':'UTF-8'}
                <br>
                {l s='Skipped' mod='giftcard'}:{$result.skipped|escape:'htmlall':'UTF-8'}
            </p>
            {* <p class="alert alert-info info">
                {l s='Deleted Giftcards temporary product Videos' mod='giftcard'}:{$result.deleted_temp_video|escape:'htmlall':'UTF-8'}
                <br>
                {l s='Skipped temporary videos' mod='giftcard'}:{$result.skipped_temp_video|escape:'htmlall':'UTF-8'}
            </p> *}
        </div>

{/block}