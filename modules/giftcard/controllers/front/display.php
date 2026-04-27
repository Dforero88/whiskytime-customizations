<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by Solver Web Tech.
 *
 *  @author    Solver Web Tech <solverwebtech@gmail.com>
 *  @copyright Solver Web Tech 2023
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class GIftCardDisplayModuleFrontController extends ModuleFrontController
{
    public $ref_credit = false;

    public function initContent()
    {
        parent::initContent();
        $ajax = Tools::getValue('ajax');

        $media_id = Tools::getValue('id_media');
        $media_id = base64_decode($media_id);
        $media_exist = false;
        if (!$ajax) {
            if (empty($media_id)) {
                Tools::redirectLink($this->context->link->getPageLink('index', true));
            } else {
                $media = Gift::getMediaById($media_id);
                if (is_array($media)) {
                    $media_exist = $this->existMedia($media);
                }
                $this->context->smarty->assign(
                    [
                        'media' => $media,
                        'media_exist' => $media_exist,
                    ]
                );

                return $this->setTemplate('module:giftcard/views/templates/front/display_video.tpl');
            }
        }
    }

    public function renoveOldMedia($media)
    {
        if (file_exists(_PS_IMG_DIR_ . 'swt_ordermedia/' . $media['media_name'])) {
            if (is_file(_PS_IMG_DIR_ . 'swt_ordermedia/' . $media['media_name'])) {
                unlink(_PS_IMG_DIR_ . 'swt_ordermedia/' . $media['media_name']);
            }
        }
    }

    public function existMedia($media)
    {
        $video_path = _PS_IMG_DIR_ . 'giftcard_videos/' . $media['video_name'];

        return file_exists($video_path);
    }
}
