/*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2021 FMM Modules
*  @license   FMM Modules
*/
// document.getElementById('videoType').addEventListener('change', function() {
//     var selectedType = this.value;
//     var uploadSection = document.getElementById('uploadVideoSection');
//     var embedSection = document.getElementById('embedVideoSection');

//     if (selectedType === 'upload') {
//         uploadSection.style.display = 'block';
//         embedSection.style.display = 'none';
//     } else {
//         uploadSection.style.display = 'none';
//         embedSection.style.display = 'block';
//     }
// });


// $('#videoAttachmentModal').on('show.bs.modal', function (event) {
//     const button = $(event.relatedTarget);
//     const videoAttachmentId = button.data('id');
//     const id_video = button.data('video-id');
//     $('#giftCardVideoId').val(id_video);
//     $('#videoAttachmentId').val(videoAttachmentId); 

// });

// document.getElementById('saveVideoAttachment').addEventListener('click', function() {  
//     const videoType = $('#videoType').val();
//     const formData = new FormData();
//     let id_cart_rule = $('#videoAttachmentId').val();
//     let id_video = $('videoAttachmentId').val();

//     const alertMessage = $('.video_alert_message');
//     const videoCheckboxContainer = $('#videoCheckboxContainer');
//     const sendVideoCheckbox = $('#sendVideoCheckbox');
//     alertMessage.hide();
//     alertMessage.text('').removeClass('alert-success alert-danger');


//     if (videoType === 'embed') {
//         const videoLink = $('input[name="video_link"]').val();
//         formData.append('videolink', videoLink);
//         formData.append('video_type', 'embed');
//     } else if (videoType === 'upload') {
//         const videoFileInput = document.querySelector('input[name="video_file"]');
//         const videoFile = videoFileInput.files[0];

//         if (videoFile) {
//             formData.append('videofile', videoFile); 
//             formData.append('video_type', 'upload');
//         } else {
//             alert('Please select a video file to upload.');
//             return;  
//         }
//     }

//     formData.append('ajax', 1);
//     formData.append('id_video', id_video);
//     formData.append('id_cart_rule', id_cart_rule);
//     formData.append('action', 'saveVideoAttachment');
//     $.ajax({
//         url: front_controller,
//         type: 'POST',
//         data: formData,
//         processData: false,
//         contentType: false,
//         dataType: 'json',
//         success: function(response) {
//             if(response.error) {
//                 $('#videoAttachmentModal').modal('hide');
//                 alertMessage.show();
//                 alertMessage.text(response.content).addClass('alert-danger');
//                 setTimeout(function(){
//                     alertMessage.fadeOut(500, function() {
//                         $(this).removeClass('alert-danger alert-success').hide();
//                     });
//                 }, 3000);
//             } else {
//                 $('#videoAttachmentModal').modal('hide');
//                 $('input[name="id_giftcard_video"]').val(controller_display+'?&id_media='+response.id_video);
//                 alertMessage.show();
//                 alertMessage.text(response.content).addClass('alert-success');
//                 videoCheckboxContainer.show();
//                 sendVideoCheckbox.prop('checked', true);
//                 sendVideoCheckbox.val(1);
//                 setTimeout(function(){
//                     alertMessage.fadeOut(500, function() {
//                         $(this).removeClass('alert-danger alert-success').hide();
//                     });
//                 }, 3000);
//             }

//         },
//         error: function() {
//             alert('An error occurred while saving the video attachment. Please try again.');
//             alertMessage.text('An error occurred while saving the video attachment. Please try again.').addClass('alert-danger');

//         }
//     });
// });


const videoTypeSelect = document.getElementById('videoType');
if (videoTypeSelect) {
    videoTypeSelect.addEventListener('change', function () {
        const selectedType = this.value;
        const uploadSection = document.getElementById('uploadVideoSection');
        const embedSection = document.getElementById('embedVideoSection');

        if (uploadSection && embedSection) {
            if (selectedType === 'upload') {
                uploadSection.style.display = 'block';
                embedSection.style.display = 'none';
            } else {
                uploadSection.style.display = 'none';
                embedSection.style.display = 'block';
            }
        }
    });
}

const videoAttachmentModal = document.getElementById('videoAttachmentModal');
if (videoAttachmentModal) {
    $('#videoAttachmentModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const videoAttachmentId = button.data('id');
        const id_video = button.data('video-id');
        $('#giftCardVideoId').val(id_video);
        $('#videoAttachmentId').val(videoAttachmentId);
    });
}

const saveVideoButton = document.getElementById('saveVideoAttachment');
if (saveVideoButton) {
    saveVideoButton.addEventListener('click', function () {
        const videoType = $('#videoType').val();
        const formData = new FormData();
        const id_cart_rule = $('#videoAttachmentId').val();
        const id_video = $('#giftCardVideoId').val(); // FIXED selector here

        const alertMessage = $('.video_alert_message');
        const videoCheckboxContainer = $('#videoCheckboxContainer');
        const sendVideoCheckbox = $('#sendVideoCheckbox');

        // if (!alertMessage.length || !videoCheckboxContainer.length || !sendVideoCheckbox.length) {
        //     return; // Abort if essential elements are missing
        // }

        alertMessage.hide().text('').removeClass('alert-success alert-danger');

        if (videoType === 'embed') {
            const videoLink = $('input[name="video_link"]').val();
            formData.append('videolink', videoLink);
            formData.append('video_type', 'embed');
        } else if (videoType === 'upload') {
            const videoFileInput = document.querySelector('input[name="video_file"]');
            const videoFile = videoFileInput ? videoFileInput.files[0] : null;

            if (videoFile) {
                formData.append('videofile', videoFile);
                formData.append('video_type', 'upload');
            } else {
                alert('Please select a video file to upload.');
                return;
            }
        }

        formData.append('ajax', 1);
        formData.append('id_video', id_video);
        formData.append('id_cart_rule', id_cart_rule);
        formData.append('action', 'saveVideoAttachment');

        $.ajax({
            url: front_controller,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                $('#videoAttachmentModal').modal('hide');

                if (response.error) {
                    alertMessage.text(response.content).addClass('alert-danger').show();
                } else {
                    $('input[name="id_giftcard_video"]').val(controller_display + '?&id_media=' + response.id_video);
                    alertMessage.text(response.content).addClass('alert-success').show();
                    videoCheckboxContainer.show();
                    sendVideoCheckbox.prop('checked', true).val(1);
                }

                setTimeout(function () {
                    alertMessage.fadeOut(500, function () {
                        $(this).removeClass('alert-danger alert-success').hide();
                    });
                }, 3000);
            },
            error: function () {
                alertMessage.text('An error occurred while saving the video attachment. Please try again.')
                    .addClass('alert-danger')
                    .show();
            }
        });
    });
}
