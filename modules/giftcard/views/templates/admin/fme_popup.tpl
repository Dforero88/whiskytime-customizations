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
<div class="overlay main_popup" id="fme_marketing_pop_up">
  <div class="popup modal"> 
    <input type='hidden' id="fme-popup-admin_url" value="{$admin_url|escape:'htmlall':'UTF-8'}">
    <input type='hidden' id="fme-popup-showpopup" value="{$showpopup|escape:'htmlall':'UTF-8'}">
    <div class='popup-4'>
        <div class="header"> 
          <div class="logo">
            <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/logo.png" width="auto" height="auto" />
          </div>
          <div class="cnt">
            <h3 class="popuptitle">{l s='Are you satisfied with our module?' mod='giftcard'}</h3>
            <p class="text">{l s='It helps us improve and serve better' mod='giftcard'}</p>
            <button class="popupclose button-close">
              <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/close.png" alt="close" />
            </button>
          </div>
        </div>
        <div class="cta emoji_cta">
          <a href="https://forms.gle/LLwHK6QBDmVmz38A9" target='_blank' data-action="popupcompleted" class="review_cta button-happy btn-popup-action"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/sad.png" alt="sad">{l s='Sad' mod='giftcard'}</a>

          <a href="#" class="review_cta button-sad"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/happy.png" alt="happy"> {l s='Happy' mod='giftcard'}</a>
        </div>
      </div>
      <div class='popup-3' style="display:none;">
              <div class="header">
                  <div class="logo">
                      <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/logo.png" width="auto" height="auto"/>
                  </div>
                  <div class="cnt">
                      <h3 class="popuptitle">{l s='Enjoying Our Module? We would Love Your Feedback!' mod='giftcard'}</h3>
                      <p class="text">{l s='Your opinion matters! If you are satisfied with our module, please take a moment to leave a review. It helps us improve and serve you better.' mod='giftcard'}</p>
                      <button class="popupclose button-close"> <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/close.png" alt="close"> </button>
                  </div>
               </div>
              <ul class="cta full_row">
                  <li class="full">
                      <a href="#" class="review_cta button-review">{l s='Leave a Review' mod='giftcard'}</a>
                  </li>
                  <li class="half">
                      <a href="#" data-action="setreminder" class="review_cta button-reminder btn-popup-action">{l s='Remind Later' mod='giftcard'}</a>
                  </li>
                  <li class="half">
                      <a href="#" data-action="popupcompleted" class="review_cta button-close btn-popup-action">{l s='No Thanks' mod='giftcard'}</a>
                  </li>
              </ul>
      </div>
      <div class="popup-2" style="display:none;">
          <div class="header">
              <div class="logo">
                  <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/logo.png" width="auto" height="auto"/>
              </div>
              <div class="cnt">
                  <h3 class="popuptitle">{l s='Where would you like to leave a review?' mod='giftcard'}</h3>
                  <p class="text">{l s='Choose where would you prefer to leave your feedback.' mod='giftcard'}</p>
                  <button class="popupclose button-close"> <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/close.png" alt="close"> </button>
              </div>
          </div>
          <ul class="cta full_row">
              <li>
                  <a href="https://addons.prestashop.com/en/ratings.php"  target='_blank' data-action="popupcompleted"  class="review_cta blue button-prestashop btn-popup-action">{l s='Review us on PrestaShop Addons' mod='giftcard'}</a>
              </li>
              <li>
                  <a href="#" data-action="popupcompleted" class="review_cta green button-fme ">{l s='Review us on FME Modules' mod='giftcard'}</a>
              </li>
          </ul>
      </div>
      <div class="popup-1" style="display:none;">
        <div class="header">
        <div class="logo">
            <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/logo.png" width="auto" height="auto" alt="FME Logo"/>
        </div>
        <div class="cnt">
            <h3 class="popuptitle">{l s='Thank You for Choosing FME' mod='giftcard'}</h3>
            <p class="text">{l s='We appreciate your interest in leaving a review on our website. Your feedback helps us continue to improve and deliver high-quality modules. Please log in to your account on the FME Modules to share your experience.' mod='giftcard'}</p>
            <button class="popupclose button-close btn-popup-action" data-action="popupcompleted">
                <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/giftcard/views/img/fme_popup/close.png" alt="{l s='Close' mod='giftcard'}">
            </button>
        </div>
  </div>
      </div>
  </div>
</div>
<style>
* {
    box-sizing: border-box;
}

#fme_marketing_pop_up {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.6); /* Change color and opacity */
    width: 100%;
    z-index: 1000 !important;
    display: none;
  }

#fme_marketing_pop_up  .popup {
    position: absolute;
    z-index: 1000;
    overflow: hidden;
    background-color: #ffffff;
    padding: 0px 0;
    box-sizing: border-box;
    width: calc(100% - 35px);
    text-align: center;
    
    left: 50%;
    top: 50%;
    border-radius: 16px;

    transform: translate(-50%, -50%);
    max-width: 380px;
    height: fit-content;
}


#fme_marketing_pop_up  .popup .header .popupclose {
    position: absolute !important;
    top: 15px  !important;
    right: 15px  !important;
    background: transparent  !important;
    border: 0  !important;
    cursor: pointer  !important;
}

#fme_marketing_pop_up  .popup .header .logo {
  padding-top:20px !important; text-align: center !important; 
}
#fme_marketing_pop_up  .popup .header .cnt{
  padding:20px 30px; box-sizing: border-box;
}
#fme_marketing_pop_up .popup .header .popuptitle {
    text-align: center !important;
    font-size: 19px !important;
    line-height: 24px !important;
    font-weight: 600 !important;
    color: #000000;
    margin: 0px !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, serif !important;
    border-bottom: none !important;
}


#fme_marketing_pop_up  .popup .header .text {
    margin: 0px;
    padding-top:10px;
    font-size: 15px;
    font-weight: 400;
    line-height: 23px;
    color: rgba(0, 0, 0, 0.6);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, serif !important;

}


#fme_marketing_pop_up  .popup .footer {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 20px;
}
#fme_marketing_pop_up  .popup .cta.full_row{
  display: flex; flex-wrap: wrap;}
  #fme_marketing_pop_up   .popup .cta{
  list-style: none; margin: 0px; padding:0px 0px;}
  #fme_marketing_pop_up   .popup .cta li{
  width: 100%; padding:12px 20px; box-sizing: border-box; border-top:1px solid #eaeaea;}
  #fme_marketing_pop_up   .popup .cta li.half{
  width: 50%;}
  #fme_marketing_pop_up   .popup .cta li:last-child.half{
  border-left: 1px solid #eaeaea;}
  #fme_marketing_pop_up  .popup .cta .review_cta{
  display: block; text-align: center; text-decoration: none; transition: .3s ease-in-out; font-size: 16px; line-height: 24px; color: rgba(0, 0, 0, 0.6); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, serif !important;}
  #fme_marketing_pop_up   .popup .cta .review_cta:hover{
  color:#3673b2;}
  #fme_marketing_pop_up   .popup .cta .review_cta.green{
  color:#6ab233;}
  #fme_marketing_pop_up  .popup .cta .review_cta.blue{
  color:#3673b2;}


  #fme_marketing_pop_up   .popup .cta.emoji_cta{
  padding:0px 30px 20px; display: flex; justify-content: space-between; gap:12px;}
  #fme_marketing_pop_up  .popup .cta.emoji_cta .review_cta{
  flex:1; display: flex; align-items: center; justify-content: center; gap:10px; text-decoration: none; font-size: 16px; line-height: 24px; padding:6px 15px; color: rgba(0, 0, 0, 0.6); font-family:'Segoe UI', Tahoma, Geneva, Verdana, serif !important; border:1px solid #eaeaea; border-radius: 5px; transition: .3s ease-in;}
  #fme_marketing_pop_up   .popup .cta.emoji_cta .review_cta:hover{
  border-color: #3673b2;}

</style>
<script>
 $(document).ready(function () {
  const showPopup = $('#fme-popup-showpopup').val();
if (showPopup == 1) {
  $(' #fme_marketing_pop_up ').fadeIn();
  $('.popup.modal').show();
}
    $('.popup-4 .button-sad').on('click', function (event) {
        event.preventDefault(); // Prevent the default anchor behavior
        $('.popup-4').hide();
        $('.popup-2').hide();
        $('.popup-1').hide();
        $('.popup-3').show();
    });
    $('.popup-3 .button-review').on('click', function (event) {
        event.preventDefault();

        $('.popup-4').hide();
        $('.popup-1').hide();
        $('.popup-3').hide();
        $('.popup-2').show();
    });

    $('.popup-2 .button-fme').on('click', function (event) {
        event.preventDefault();

        $('.popup-4').hide();
        $('.popup-1').show();
        $('.popup-3').hide();
        $('.popup-2').hide();
    });
    
    $(' #fme_marketing_pop_up  .popupclose').on('click', function () {
        $('#fme_marketing_pop_up ').fadeOut()
    });
    $('.popup .btn-popup-action').on('click', function (event) {

        var adminUrl = $('.popup .fme-popup-admin_url').val();
        var fme_action = $(this).data('action');
        $('#fme_marketing_pop_up ').fadeOut();
        $.ajax({
            url: adminUrl,
            type: 'POST',
            data: { action: "fmepopupaction",
              fme_action: fme_action,
              ajax: 1
            },
            success: function (response) {
            },
            error: function () {
            }
        });
    });
});
</script>