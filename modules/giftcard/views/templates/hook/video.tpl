{**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2024 FMM Modules
*  @license   FMM Modules
*}
{if !empty($temp_videos) && is_array($temp_videos)}
    {foreach from=$temp_videos item=card}
        <div class="send_someone_temp" colspan="6">
            <div class="send_someone_form_temp">
                <form action="{$link->getModuleLink('giftcard','tempvideo')|escape:'htmlall':'UTF-8'}" method="post" name="giftcard_send_to_friend" id="from_giftcard_temp">
                    {if $video_enabled}
                        <div class="form-group template-wrapper">
                            <label class="label">{l s='Video Attachment' mod='giftcard'}</label>
                            <div class="row">
                                <div class="col-md-7">
                                    <a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-id= '' data-video-id='{$card.id_temp_video|escape:'htmlall':'UTF-8'}' data-target="#videoAttachmentModalTemp">
                                        {l s='Add Video Attachment' mod='giftcard'}
                                    </a>            </div>
                                <div id="gift-template-modal-temp"></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="mt-3 p-1 video_alert_message_temp"></div>
                        </div>
                        
                    {/if}

                <input type="hidden" name="temp_video" value="{if isset($card.id_temp_video)}{$temp_controller_display|escape:'htmlall':'UTF-8'}?id_media={$card.id_temp_video|base64_encode}&id={$card.id_customer|base64_encode}&id_guest={$card.id_guest|base64_encode}{/if}"/>
                    <input type="hidden" id="id_temp_video" name="id_temp_video" value="{$card.id_temp_video|escape:'htmlall':'UTF-8'}"/>
                    <input type="hidden" id="id_gift_product_temp" name="id_gift_product_temp" value="{$card.id_product|escape:'htmlall':'UTF-8'}"/>
                    <input type="hidden" name="giftcard_temp_name" value="{$card.video_name|escape:'htmlall':'UTF-8'}"/>
                    <input type="hidden" name="created_at" value="{$card.created_at|escape:'htmlall':'UTF-8'}"/>
                </form>
            </div>
        </div>
    {/foreach}
{else}
    <form action="{$link->getModuleLink('giftcard','tempvideo')|escape:'htmlall':'UTF-8'}" method="post" name="giftcard_send_to_friend" id="from_giftcard_temp">
        {if $video_enabled}
            <div class="form-group template-wrapper">
                <label class="label">{l s='Video Attachment' mod='giftcard'}</label>
                <div class="row">
                    <div class="col-md-7">
                        <a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-id= '' data-video-id='' data-target="#videoAttachmentModalTemp">
                            {l s='Add Video Attachment' mod='giftcard'}
                        </a>            </div>
                    <div id="gift-template-modal-temp"></div>
                </div>
                <div class="clearfix"></div>
                <div class="mt-3 p-1 video_alert_message_temp"></div>
            </div>
            
        {/if}
        <input type="hidden" name="temp_video" value="{$temp_controller_display|escape:'htmlall':'UTF-8'}"/>
        <input type="hidden" id="id_temp_video" name="id_temp_video" value=""/>
        <input type="hidden" id="id_gift_product_temp" name="id_gift_product_temp" value="{$id_product|escape:'htmlall':'UTF-8'}"/>
        </form>
{/if}

<div class="modal fade" id="videoAttachmentModalTemp" tabindex="-1" role="dialog" aria-labelledby="videoAttachmentModalLabelTemp" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="videoAttachmentModalLabelTemp">{l s='Add Video Attachment' mod='giftcard'}</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="videoAttachmentIdTemp" name="video_attachment_id_temp" value="{$temp_controller_display|escape:'htmlall':'UTF-8'}">
                <input type="hidden" id="giftCardVideoIdTemp" name="gift_card_video_id_temp">
                <div class="form-group">
                    <label>{l s='Select Video Type' mod='giftcard'}</label>
                    <select id="videoType" class="form-control">
                        <option value="upload">{l s='Upload Video' mod='giftcard'}</option>
                        <option value="embed">{l s='Embed Video Link' mod='giftcard'}</option>
                    </select>
                </div>

                <div id="uploadVideoSectionTemp">
                    <div class="form-group">
                        <label>{l s='Upload Video File' mod='giftcard'}</label>
                        <input type="file" name="video_file_temp" class="form-control" accept="video/*">
                        <small>{l s='Accepted formats: MP4, MOV.' mod='giftcard'} {if $video_limit}{l s='Video size limit is : ' mod='giftcard'} {$video_limit|escape:'htmlall':'UTF-8'} {l s='MB' mod='giftcard'}{/if}</small>
                        {if $video_expiry}
                            <br><small>{l s='Uploaded video will become Inaccessible after' mod='giftcard'} {if $id_customer}{$video_expiry|escape:'htmlall':'UTF-8'}{else}{l s='1 ' mod='giftcard'}{/if} {l s='Days' mod='giftcard'}</small>
                        {/if}  
                    </div>
                </div>

                <div id="embedVideoSectionTemp" style="display: none;">
                    <div class="form-group">
                        <label>{l s='Embed Video Link' mod='giftcard'}</label>
                        <input type="url" name="video_link_temp" class="form-control" placeholder="https://example.com">
                        <small>{l s='You can add a YouTube or Vimeo link.' mod='giftcard'}</small>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <p>{l s='This will replace the previously uploaded video' mod='giftcard'}</p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='giftcard'}</button>
                <button type="button" class="btn btn-primary saveVideoAttachmentTemp" id="saveVideoAttachmentTemp">{l s='Save' mod='giftcard'}</button>
            </div>
        </div>
    </div>
</div>


<script>
document.getElementById('saveVideoAttachmentTemp').addEventListener('click', function() {  
    const videoType = $('#videoType').val();
    const formData = new FormData();
    let id_product = $('#id_gift_product_temp').val();
    let id_cart_rule = $('#videoAttachmentIdTemp').val();
    let id_temp_video = $('#videoAttachmentIdTemp').val();
    let existed_id_temp_video = $('#id_temp_video').val();

    const alertMessage = $('.video_alert_message_temp');
    alertMessage.hide();
    alertMessage.text('').removeClass('alert-success alert-danger');


    if (videoType === 'embed') {
        const videoLink = $('input[name="video_link_temp"]').val();
        formData.append('videolink', videoLink);
        formData.append('video_type', 'embed');
    } else if (videoType === 'upload') {
        const videoFileInput = document.querySelector('input[name="video_file_temp"]');
        const videoFile = videoFileInput.files[0];

        if (videoFile) {
            formData.append('videofile', videoFile); 
            formData.append('video_type', 'upload');
        } else {
            alert('Please select a video file to upload.');
            return;  
        }
    }

    formData.append('ajax', 1);
    formData.append('id_product', id_product);
    formData.append('id_temp_video', id_temp_video);
    formData.append('existed_id_temp_video', existed_id_temp_video);
    formData.append('action', 'saveVideoAttachmentTemp');
    var temp_controller_display = "{$temp_controller_display|escape:'htmlall':'UTF-8'}"
    $.ajax({
        url: temp_controller,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.error) {
                $('#videoAttachmentModalTemp').modal('hide');
                alertMessage.show();
                alertMessage.text(response.content).addClass('alert-danger');
                setTimeout(function(){
                    alertMessage.fadeOut(500, function() {
                        $(this).removeClass('alert-danger alert-success').hide();
                    });
                }, 3000);
            } else {
                $('#videoAttachmentModalTemp').modal('hide');
                $('input[name="id_giftcard_video"]').val(temp_controller_display+'?&id_media='+response.id_temp_video);
                alertMessage.show();
                alertMessage.text(response.content).addClass('alert-success');
                setTimeout(function(){
                    alertMessage.fadeOut(500, function() {
                        $(this).removeClass('alert-danger alert-success').hide();
                    });
                }, 3000);
            }

        },
        error: function() {
            alert('An error occurred while saving the video attachment. Please try again.');
            alertMessage.text('An error occurred while saving the video attachment. Please try again.').addClass('alert-danger');

        }
    });
});

const videoTypeSelect = document.getElementById('videoType');
if (videoTypeSelect) {
    videoTypeSelect.addEventListener('change', function () {
        const selectedType = this.value;
        const uploadSectionTemp = document.getElementById('uploadVideoSectionTemp');
        const embedSectionTemp = document.getElementById('embedVideoSectionTemp');

        if (uploadSectionTemp && embedSectionTemp) {
            if (selectedType === 'upload') {
                uploadSectionTemp.style.display = 'block';
                embedSectionTemp.style.display = 'none';
            } else {
                uploadSectionTemp.style.display = 'none';
                embedSectionTemp.style.display = 'block';
            }
        }
    });
}

const videoAttachmentModalTemp = document.getElementById('videoAttachmentModalTemp');
if (videoAttachmentModalTemp) {
    document.getElementById('videoAttachmentModalTemp').addEventListener('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const videoAttachmentId = button.data('id');
        const id_video = button.data('video-id');
        $('#giftCardVideoIdTemp').val(id_video);
        $('#videoAttachmentIdTemp').val(videoAttachmentId);
    });
}
</script>