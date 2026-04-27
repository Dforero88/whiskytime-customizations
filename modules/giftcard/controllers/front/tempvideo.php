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
class GiftCardTempVideoModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public $html;

    public $errors = [];

    protected $display_column_left;
    protected $display_column_right;

    protected $my_giftcards = false;

    protected $tpl = 'v1_6/giftcards.tpl';

    public function __construct()
    {
        // $this->ajax = true;
        parent::__construct();
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->context = Context::getContext();
        $this->ajax = Tools::getValue('ajax');
    }

    protected function setGiftcardTemplate()
    {
        if (true == Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->tpl = 'module:' . $this->module->name . '/views/templates/front/v1_7/giftcards.tpl';
        } else {
            $this->tpl = 'v1_6/giftcards.tpl';
        }
    }

    public function displayAjaxSaveVideoAttachmentTemp()
    {
        $response = [
            'error' => false,
            'content' => '',
        ];
        $id_guest = (int) $this->context->cookie->id_guest;
        $filename = '';
        if (Tools::getValue('ajax')) {
            $existing_video = '';
            $videoType = Tools::getValue('video_type');
            // $cart_rule_id = Tools::getValue('id_cart_rule');
            $id_temp_video = Tools::getValue('existed_id_temp_video');
            // $cart_id = Context::getContext()->cart->id;
            $media_key = base_convert(preg_replace('/[^0-9]/', '', microtime(false)), 10, 36);
            $media_link = '';
            $media_name = '';
            $file_moved = false;
            $video_size_limit = Configuration::get('GIFT_VIDEO_SIZE_LIMIT');
            $base_url = Context::getContext()->shop->getBaseURL(false);
            if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
                $base_url = Context::getContext()->shop->getBaseURL(true);
            }

            if ($videoType === 'embed') {
                $media_link = Tools::getValue('videolink');
                $media_name = 'Embedded Video';
            } elseif ($videoType === 'upload' && isset($_FILES['videofile'])) {
                $video_file = $_FILES['videofile'];
                if ($video_file['name']) {
                    $video_ext = pathinfo(Tools::strtolower($video_file['name']), PATHINFO_EXTENSION);
                    $allowed_formats = ['mp4', 'ogg', 'webm', 'mov', 'h264', 'hevc'];
                    if ($video_size_limit) {
                        $video_size_limit_bytes = $video_size_limit * (1024 ** 2);
                        if ($video_file['size'] > $video_size_limit_bytes) {
                            $response = [
                                'error' => true,
                                'content' => $this->module->l('The uploaded video exceeds the size limit of ' . $video_size_limit . ' MB.'),
                            ];
                            ob_end_clean();
                            header('Content-Type: application/json');
                            exit(json_encode($response));
                        }
                    }

                    if (in_array($video_ext, $allowed_formats)) {
                        // $giftcard_video_id_cart_rule = Tools::getValue('id_cart_rule');
                        $id_customer = (int) $this->context->cookie->id_customer;
                        $id_guest = (int) $this->context->cookie->id_guest;
                        $filename = 'giftcard_video_' . $id_guest . '.' . $video_ext;

                        $path = $id_customer ? _PS_IMG_DIR_ . 'giftcard_videos/temp_videos/' : _PS_IMG_DIR_ . 'giftcard_videos/temp_videos/guest_videos/';

                        $destination_path = $path . $filename;
                        if (!is_dir($path)) {
                            @mkdir($path, 0777, true);
                        }

                        $file_moved = move_uploaded_file($video_file['tmp_name'], $destination_path);

                        if ($file_moved) {
                            $media_link = $id_customer ? $base_url . 'img/giftcard_videos/temp_videos/' . $filename : $base_url . 'img/giftcard_videos/temp_videos/guest_videos/' . $filename;
                            $media_name = $filename;
                        } else {
                            $response['error'] = true;
                            $response['content'] = $this->module->l('Error moving uploaded video file.');
                        }
                    } else {
                        $response = [
                            'error' => true,
                            'content' => $this->module->l('Only these files are allowed') . ' : ' . implode(', ', $allowed_formats),
                        ];
                        ob_end_clean();
                        header('Content-Type: application/json');
                        exit(json_encode($response));
                    }
                }
            }

            if (!$response['error']) {
                $existing_video_temp = Db::getInstance()->getRow('
                    SELECT * 
                    FROM `' . _DB_PREFIX_ . 'gift_card_video_temp` 
                    WHERE id_temp_video = ' . (int) $id_temp_video
                );

                $temp_data = [
                    'id_customer' => (int) Context::getContext()->customer->id,
                    'id_guest' => (int) $id_guest,
                    'id_product' => (int) Tools::getValue('id_product'),
                    'video_link' => pSQL($media_link),
                    'video_name' => pSQL($filename),
                    'type' => pSQL($videoType),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $encoded_video_id = $existing_video_temp ? base64_encode($existing_video_temp['id_temp_video']) : '';
                if (!empty($existing_video_temp) && !empty($temp_data)) {
                    $encoded_video_id = base64_encode($existing_video_temp['id_temp_video']);
                    $decode = base64_decode('MTI%3D');
                    $result = Db::getInstance()->update('gift_card_video_temp', $temp_data, 'id_temp_video = ' . (int) $existing_video_temp['id_temp_video']);

                    if ($result) {
                        $response['content'] = $this->module->l($videoType === 'upload' ? 'Video uploaded successfully' : 'Embedded video link saved successfully');
                        $response['id_temp_video'] = $existing_video_temp ? $encoded_video_id : base64_encode(Db::getInstance()->Insert_ID()); // Get the video ID
                    } else {
                        $response['error'] = true;
                        $response['content'] = $this->module->l('Failed to save video information.');
                    }
                } else {
                    $result = Db::getInstance()->insert('gift_card_video_temp', $temp_data);
                    // }

                    if ($result) {
                        $response['content'] = $this->module->l($videoType === 'upload' ? 'Video uploaded successfully' : 'Embedded video link saved successfully');
                        $response['id_temp_video'] = $existing_video_temp ? $encoded_video_id : base64_encode(Db::getInstance()->Insert_ID());
                    } else {
                        $response['error'] = true;
                        $response['content'] = $this->module->l('Failed to save video information.');
                    }
                }

                Media::addJsDef([
                    'temp_controller_js' => $this->context->link->getModuleLink($this->module->name, 'tempvideo', [], true),
                    'controller_display' => $this->context->link->getModuleLink($this->module->name, 'display', [], true),
                ]);

                $this->context->smarty->assign([
                    'id_customer' => (int) Context::getContext()->customer->id,
                    'temp_controller_js' => $this->context->link->getModuleLink($this->module->name, 'tempvideo', ['ajax' => true], true),
                    'controller_display' => $this->context->link->getModuleLink($this->module->name, 'display', [], true),
                ]);
                if (!$result) {
                    $response['error'] = true;
                }
            }
        } else {
            $response['error'] = true;
            $response['content'] = $this->module->l('Invalid request');
        }

        ob_end_clean();
        header('Content-Type: application/json');
        exit(json_encode($response));
    }

    public function displayTempGiftCards()
    {
        $productTags = [];
        $giftProducts = [];
        $idLang = $this->context->cookie->id_lang;
        $giftCategory = new Category(Configuration::get('GIFT_CARD_CATEGORY'));
        $products = $giftCategory->getProducts($idLang, 1, 100);
        if ($products) {
            foreach ($products as $key => $product) {
                $_in_gift_table = (int) Gift::lookForGiftTable($product['id_product']);
                if (Gift::isInOrderCart($product['id_product']) && $_in_gift_table <= 0) {
                    unset($products[$key]);
                } else {
                    $giftProducts[$key] = $product;
                    $tags = Tag::getProductTags((int) $product['id_product']);
                    $giftProducts[$key]['id_image'] = Gift::getId_image($product['id_product']);
                    $giftProducts[$key]['tags'] = ($tags && count($tags) > 0) ? array_shift($tags) : [];
                    $productTags[] = $giftProducts[$key]['tags'];
                    $giftProducts[$key]['tags'] = (count($giftProducts[$key]['tags']) > 0) ? implode(',', $giftProducts[$key]['tags']) : '';
                }
            }
        }

        $filterTags = [];
        if ($productTags) {
            foreach ($productTags as $ptags) {
                if ($ptags) {
                    foreach ($ptags as $tag) {
                        $filterTags[$tag] = $tag;
                    }
                }
            }
        }
        $this->context->smarty->assign([
            'filterTags' => $filterTags,
            'giftProducts' => $giftProducts,
        ]);
        $this->setTemplate($this->tpl);
    }

    public function setMedia($newTheme = false)
    {
        parent::setMedia();
        Media::addJsDef([
            'isGiftListingPage' => !$this->my_giftcards,
        ]);
        $this->addCSS($this->module->getPathUri() . 'views/css/gift_card.css');

        if (false === $this->my_giftcards) {
            $this->addCSS($this->module->getPathUri() . 'views/css/gift_products.css');
            $this->addJs($this->module->getPathUri() . 'views/js/jquery.filterizr.min.js');
        } else {
            $logo = '';
            $shopUrl = $this->context->link->getPageLink('index', true, $this->context->language->id, null, false, $this->context->shop->id);
            $sopName = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $this->context->shop->id));
            $sender = ($this->context->customer && $this->context->customer->id) ? sprintf('%s %s', $this->context->customer->firstname, $this->context->customer->lastname) : $this->module->l('Your Name');
            if (false !== Configuration::get('PS_LOGO_MAIL')
                && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $this->context->shop->id))
            ) {
                $logo = __PS_BASE_URI__ . '/img/' . Configuration::get('PS_LOGO_MAIL', null, null, $this->context->shop->id);
            } elseif (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $this->context->shop->id))) {
                $logo = __PS_BASE_URI__ . '/img/' . Configuration::get('PS_LOGO', null, null, $this->context->shop->id);
            }
            Media::addJsDef([
                'shop_logo' => $logo,
                'sender' => $sender,
                'shop_url' => $sopName,
                'shop_name' => $shopUrl,
                'expiry_date' => sprintf('X %s', $this->module->l('days')),
                'preview_label' => $this->module->l('Template Preview'),
            ]);

            $this->addCss($this->module->getPathUri() . 'views/css/iziModal.min.css');
            $this->addJs([
                $this->module->getPathUri() . 'views/js/jquery.ddslick.min.js',
                $this->module->getPathUri() . 'views/js/jquery.filterizr.min.js',
                $this->module->getPathUri() . 'views/js/iziModal.min.js',
            ]);
        }
        Media::addJsDef([
            'video_paragraph' => $this->module->translations['video_paragraph'],
            'video_link_title' => $this->module->translations['video_link_title'],
        ]);
        $this->addJs($this->module->getPathUri() . 'views/js/my_gifts.js');
        return true;
    }
}
