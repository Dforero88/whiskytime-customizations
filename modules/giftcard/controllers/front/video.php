<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class GiftcardVideoModuleFrontController extends ModuleFrontController
{
    public $useSSL = true;

    protected $cron_tpl = 'v1_6/cron.tpl';

    public function init()
    {
        parent::init();
        $this->context = Context::getContext();
        if (true == (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->cron_tpl = sprintf('module:%s/views/templates/front/v1_7/video_expiry.tpl', $this->module->name);
        }
    }

    public function initContent()
    {
        parent::initContent();
        $result = ['deleted' => 0, 'skipped' => 0, 'deleted_temp_video' => 0, 'skipped_temp_video' => 0];
        $action = Tools::getValue('action');

        if ($action == 'delete_video') {
            $video_deadline = (int) Configuration::get('GIFT_VIDEO_EXPIRY_DAYS', 15);

            if (!Tools::getIsset('giftcard_video_cron')) {
                $this->context->controller->errors[] = $this->module->l('Cron key not set.', 'cron');
            } elseif (Configuration::get('GIFTCARD_VIDEO_CRON_KEY') !== Tools::getValue('giftcard_video_cron')) {
                $this->context->controller->errors[] = $this->module->l('Invalid cron key.', 'cron');
            } else {
                $videos = Gift::getAllVideos();

                foreach ($videos as $video) {
                    if ($video['type'] == 'upload') {
                        $created_at = new DateTime($video['created_at']);
                        $expiration_date = $created_at->modify('+' . $video_deadline . ' days');
                        $current_date = new DateTime();
                        if ($current_date >= $expiration_date) {
                            $video_path = _PS_IMG_DIR_ . 'giftcard_videos/' . $video['video_name'];
                            // Delete file from the upload folder
                            if (file_exists($video_path)) {
                                unlink($video_path);
                            }

                            Db::getInstance()->delete('gift_card_video_links', 'id_video = ' . (int) $video['id_video']);

                            ++$result['deleted'];
                        } else {
                            ++$result['skipped'];
                        }
                    }
                }

                $temp_videos = GiftCardVideoTemp::getAllTempVideos();
                foreach ($temp_videos as $temp_video) {
                    // if ($temp_video['id_customer'] > 0) {
                    if ($temp_video['type'] == 'upload') {
                        $created_at = new DateTime($temp_video['created_at']);
                        // $expiration_date = $created_at->modify('+' . $video_deadline . ' days');
                        $expiration_date = $temp_video['id_customer'] > 0 ? $created_at->modify('+' . $video_deadline . ' days') : $created_at->modify('+1 days');
                        $current_date = new DateTime();
                        if ($current_date >= $expiration_date) {
                            $video_path = $temp_video['id_customer'] > 0 ? _PS_IMG_DIR_ . 'giftcard_videos/temp_videos/' . $temp_video['video_name'] : _PS_IMG_DIR_ . 'giftcard_videos/temp_videos/guest_videos/' . $temp_video['video_name'];
                            // Delete file from the upload folder
                            if (file_exists($video_path)) {
                                unlink($video_path);
                            }

                            Db::getInstance()->delete('gift_card_video_temp', 'id_temp_video = ' . (int) $temp_video['id_temp_video']);

                            ++$result['deleted_temp_video'];
                        } else {
                            ++$result['skipped_temp_video'];
                        }
                    }
                    if ($temp_video['id_customer'] == 0) {
                    }
                }
                $this->context->smarty->assign('result', $result);

                return $this->setTemplate($this->cron_tpl);
            }
        }
    }
}
