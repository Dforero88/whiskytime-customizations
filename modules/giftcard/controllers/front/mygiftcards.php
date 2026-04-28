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
class GiftCardMyGiftCardsModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public $html;

    public $errors = [];

    protected $my_giftcards = false;

    protected $tpl = 'v1_6/giftcards.tpl';
    protected $display_column_left;
    protected $display_column_right;

    public function __construct()
    {
        // $this->ajax = true;
        parent::__construct();
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->context = Context::getContext();
        $this->ajax = Tools::getValue('ajax');
    }

    public function init()
    {
        parent::init();
        // $this->ajax = true;
        $this->my_giftcards = (Tools::getIsset('my_gifts') || Tools::getIsset('pending_gifts')) ? true : false;
        $this->setGiftcardTemplate();
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        if ($this->my_giftcards) {
            if ($this->context->customer->isLogged()) {
                return $this->displayMyGiftCards();
            } else {
                Tools::redirect($this->context->link->getPageLink(
                    'authentication',
                    true,
                    null,
                    ['back' => urlencode($this->context->link->getModuleLink('giftcard', 'mygiftcards'))]
                ));
            }
        }

        return $this->displayGiftCards();
    }

    public function postProcess()
    {
        parent::postprocess();
        $this->html = 0;
        $video_link = '';
        $id_lang = $this->context->cookie->id_lang;
        if (Tools::isSubmit('send_giftcard')) {
            $send_video = Tools::getValue('send_video');
            $id_cart_rule = (int) Tools::getValue('id_coupen');
            $id_video = (int) Gift::getVideoIdByCardtRule($id_cart_rule);
            if (Configuration::get('GIFT_VIDEO_UPLOAD_ENABLED') && $id_video && ($send_video == 1)) {
                $id_video = base64_encode($id_video);
                $video_link = $this->context->link->getModuleLink($this->module->name, 'display', ['id_media' => $id_video], true);
            }
            $id_gift_product = (int) Tools::getValue('id_gift_product');
            $friend_name = (string) Tools::getValue('friend_name');
            $friend_email = (string) Tools::getValue('friend_email');
            $friend_message = (string) Tools::getValue('friend_message');
            $id_template = (int) Tools::getValue('email_template');
            $media_link = (int) Tools::getValue('id_giftcard_video');
            $now = (string) date('Y-m-d H:i:s');

            $mycoupon = Gift::getCartsRuleById($id_cart_rule, $id_lang);
            $old_voucher = new CartRule((int) $id_cart_rule);
            $gift_coupon = new CartRule();

            $date_from = date_create($mycoupon['date_from']);
            $date_to = date_create($mycoupon['date_to']);
            $newDate = date_diff($date_from, $date_to);
            $languages = Language::getLanguages(true);
            foreach ($languages as $lang) {
                $gift_coupon->name[$lang['id_lang']] = $mycoupon['name'];
            }

            $gift_coupon->name[$id_lang] = $mycoupon['name'];
            $gift_coupon->date_from = $now;
            $gift_coupon->date_to = date('Y-m-d H:i:s', strtotime($now . ' ' . $newDate->format('%R%a days')));
            $gift_coupon->quantity = 1;
            $gift_coupon->quantity_per_user = 1;
            $gift_coupon->free_shipping = $mycoupon['free_shipping'];
            $gift_coupon->reduction_currency = $mycoupon['reduction_currency'];
            $gift_coupon->active = $mycoupon['active'];
            $gift_coupon->date_add = $mycoupon['date_add'];
            $gift_coupon->reduction_product = $mycoupon['reduction_product'];
            $gift_coupon->code = (Configuration::get('GIFTCARD_VOUCHER_PREFIX') ? Configuration::get('GIFTCARD_VOUCHER_PREFIX') : '') . Tools::passwdGen(mt_rand(4, 14));
            $gift_coupon->minimum_amount_currency = $mycoupon['minimum_amount_currency'];
            $gift_coupon->reduction_amount = $mycoupon['reduction_amount'];
            $gift_coupon->reduction_tax = $mycoupon['reduction_tax'];
            $gift_coupon->reduction_percent = $mycoupon['reduction_percent'];
            $gift_coupon->shop_restriction = $old_voucher->shop_restriction;

            if ($gift_coupon->add()) {
                CartRule::copyConditions($id_cart_rule, $gift_coupon->id);
                $email = trim($friend_email);
                if (!Validate::isName($friend_name)) {
                    $this->context->controller->errors[] = $this->module->translations['invalid_name'];
                }
                if (empty($email)) {
                    $this->context->controller->errors[] = $this->module->translations['required_email'];
                } elseif (!Validate::isEmail($email)) {
                    $this->context->controller->errors[] = $this->module->translations['invalid_email'];
                } elseif (!Validate::isMessage($friend_message)) {
                    $this->context->controller->errors[] = $this->module->translations['invalid_message'];
                } elseif (!$this->context->controller->errors) {
                    $currency = new Currency((int) $gift_coupon->reduction_currency);

                    $sender_name = $this->context->cookie->customer_firstname . ' ' . $this->context->cookie->customer_lastname;
                    if(_PS_VERSION_ == '9.0.0'){
                        $priceFormatter = new PriceFormatter();
                        $this->smarty->assign('priceFormatter', $priceFormatter);
                        $value = $mycoupon['reduction_percent'] && $mycoupon['reduction_percent'] != '0.00' ? $mycoupon['reduction_percent'] . ' %' : $priceFormatter->format($mycoupon['reduction_amount'], $currency);

                    } else{
                        $value = $mycoupon['reduction_percent'] && $mycoupon['reduction_percent'] != '0.00' ? $mycoupon['reduction_percent'] . ' %' : Tools::displayPrice($mycoupon['reduction_amount'], $currency);
                    }

                    if (!$this->module->sendGiftCard(
                        $sender_name,
                        $friend_name,
                        $friend_email,
                        $video_link,
                        $gift_coupon->name[$id_lang],
                        $gift_coupon->code,
                        $newDate->format('%R%a days'),
                        $now,
                        $value,
                        $id_gift_product,
                        $friend_message,
                        $gift_coupon->quantity,
                        $id_template,
                        $this->context->customer->id_lang
                    )) {
                        $this->context->controller->errors[] = $this->module->translations['gift_sending_failed'];
                    } else {
                        $qty = $mycoupon['quantity'];
                        if (!empty($mycoupon['quantity'])) {
                            $qty = $qty - 1;
                        }

                        Db::getInstance()->update('cart_rule', ['quantity' => (int) $qty, 'quantity_per_user' => (int) $qty], 'id_cart_rule =' . (int) $mycoupon['id_cart_rule']);
                        Tools::redirect($this->context->link->getModuleLink(
                            $this->module->name,
                            'mygiftcards',
                            ['my_gifts' => 'show', 'msg' => (int) $this->html],
                            true
                        ));
                    }
                } else {
                    $gift_coupon->delete();
                }
            } else {
                $this->context->controller->errors[] = $this->module->translations['gift_card_failure'];
            }
            if ($this->context->controller->errors) {
                Tools::redirect($this->context->link->getModuleLink(
                    $this->module->name,
                    'mygiftcards',
                    ['my_gifts' => 'show', 'errors' => $this->context->controller->errors],
                    true
                ));
            }
        }
    }

    protected function setGiftcardTemplate()
    {
        if ($this->my_giftcards) {
            $templates = $this->module->getTemplates();
            $this->context->smarty->assign([
                'templates' => $templates,
            ]);
            if (true == Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                if (Tools::getIsset('my_gifts')) {
                    $this->tpl = 'module:' . $this->module->name . '/views/templates/front/v1_7/mygiftcards.tpl';
                } else {
                    $this->tpl = 'module:' . $this->module->name . '/views/templates/front/v1_7/pending_giftcards.tpl';
                }
            } else {
                if (Tools::getIsset('my_gifts')) {
                    $this->tpl = 'v1_6/mygiftcards.tpl';
                } else {
                    $this->tpl = 'v1_6/pending_giftcards.tpl';
                }
            }
        } else {
            if (true == Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $this->tpl = 'module:' . $this->module->name . '/views/templates/front/v1_7/giftcards.tpl';
            } else {
                $this->tpl = 'v1_6/giftcards.tpl';
            }
        }
    }

    public function displayAjaxSaveVideoAttachment()
    {
        $response = [
            'error' => false,
            'content' => '',
        ];

        if (Tools::getValue('ajax')) {
            $filename = '';
            $existing_video = '';
            $videoType = Tools::getValue('video_type');
            $cart_rule_id = Tools::getValue('id_cart_rule');
            $id_video = Tools::getValue('id_video');
            $cart_id = Context::getContext()->cart->id;
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
                        $giftcard_video_id_cart_rule = Tools::getValue('id_cart_rule');
                        $filename = 'giftcard_video_' . $giftcard_video_id_cart_rule . '.' . $video_ext;
                        $path = _PS_IMG_DIR_ . 'giftcard_videos/';

                        $destination_path = $path . $filename;
                        if (!is_dir($path)) {
                            @mkdir($path, 0777, true);
                        }

                        $file_moved = move_uploaded_file($video_file['tmp_name'], $destination_path);

                        if ($file_moved) {
                            $media_link = $base_url . 'img/giftcard_videos/' . $filename;
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
                if ($cart_rule_id && $id_video) {
                    $existing_video = Db::getInstance()->getRow('
                        SELECT id_video 
                        FROM `' . _DB_PREFIX_ . 'gift_card_video_links` 
                        WHERE id_cart_rule = ' . (int) $cart_rule_id
                    );
                }

                $data = [
                    'id_cart_rule' => (int) $cart_rule_id,
                    'video_link' => pSQL($media_link),
                    'type' => pSQL($videoType),
                    'created_at' => date('Y-m-d H:i:s'),
                    'video_name' => pSQL($filename),
                ];
                $encoded_video_id = base64_encode($existing_video['id_video']);

                if ($existing_video) {
                    // Update existing entry
                    $encoded_video_id = base64_encode($existing_video['id_video']);
                    $decode = base64_decode('MTI%3D');
                    $result = Db::getInstance()->update('gift_card_video_links', $data, 'id_video = ' . (int) $existing_video['id_video']);
                    if ($result) {
                        $response['content'] = $this->module->l($videoType === 'upload' ? 'Video uploaded successfully' : 'Embedded video link saved successfully');
                        $response['id_video'] = $existing_video ? $encoded_video_id : base64_encode(Db::getInstance()->Insert_ID()); // Get the video ID
                    } else {
                        $response['error'] = true;
                        $response['content'] = $this->module->l('Failed to save video information.');
                    }
                } else {
                    // Insert new entry
                    $result = Db::getInstance()->insert('gift_card_video_links', $data);
                    if ($result) {
                        $response['content'] = $this->module->l($videoType === 'upload' ? 'Video uploaded successfully' : 'Embedded video link saved successfully');
                        $response['id_video'] = $existing_video ? $encoded_video_id : base64_encode(Db::getInstance()->Insert_ID());
                    } else {
                        $response['error'] = true;
                        $response['content'] = $this->module->l('Failed to save video information.');
                    }
                }

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

    public function displayMyGiftCards()
    {
        $model = new Gift();
        $id_lang = $this->context->cookie->id_lang;
        if (Tools::getValue('errors')) {
            $this->context->controller->errors = Tools::getValue('errors');
        }

        $id_customer = (int) $this->context->cookie->id_customer;
        $id_lang = (int) $this->context->cookie->id_lang;
        $coupen = $model->getVoucherByCustomerId($id_customer, true, $id_lang);

        foreach ($coupen as &$coupon) {
            $link = new Link();
            $product_url = $link->getProductLink($coupon['id_product']);
            $coupon['link'] = $product_url;
        }

        Media::addJsDef([
            'controller_js' => $this->context->link->getModuleLink($this->module->name, 'mygiftcards', [], true),
            'controller_display' => $this->context->link->getModuleLink($this->module->name, 'display', [], true),
        ]);

        $GIFTCARD_SHARE = Configuration::get('GIFTCARD_SHARE') ? explode(',', Configuration::get('GIFTCARD_SHARE')) : [];
        $this->context->smarty->assign([
            'id_customer' => $id_customer,
            'coupens' => $coupen,
            'video_expiry' => Configuration::get('GIFT_VIDEO_EXPIRY_DAYS'),
            'video_limit' => Configuration::get('GIFT_VIDEO_SIZE_LIMIT'),
            'video_enabled' => Configuration::get('GIFT_VIDEO_UPLOAD_ENABLED'),
            'errors' => $this->context->controller->errors,
            'msg' => $this->html,
            'ps_version' => _PS_VERSION_,
            'GIFTCARD_SHARE' => $GIFTCARD_SHARE,
            'controller_js' => $this->context->link->getModuleLink($this->module->name, 'mygiftcards', ['ajax' => true], true),
            'controller_display' => $this->context->link->getModuleLink($this->module->name, 'display', [], true),
            'pending_cards' => $model->getPendingGiftCards($id_customer, $id_lang),
        ]);

        $this->setTemplate($this->tpl);
    }

    public function displayGiftCards()
    {
        $productTags = [];
        $giftProducts = [];
        $idLang = $this->context->cookie->id_lang;
        $giftCategory = new Category(Configuration::get('GIFT_CARD_CATEGORY'));
        $products = $giftCategory->getProducts($idLang, 1, 100);
        if (!empty($products)) {
            foreach ($products as $key => &$product) {
                if(_PS_VERSION_ == '9.0.0'){
                    $product['category'] = Category::getLinkRewrite((int) $product['id_category_default'], (int) $idLang);
                    $product['category_name'] = Db::getInstance()->getValue('SELECT name FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_shop = ' . (int) $this->context->shop->id . ' AND id_lang = ' . (int) $idLang . ' AND id_category = ' . (int) $product['id_category_default']);
                    $product['link'] = $this->context->link->getProductLink((int) $product['id_product'], $product['link_rewrite'], $product['category'], $product['ean13']);
                }
                $_in_gift_table = (int) Gift::lookForGiftTable($product['id_product']);
                if (Gift::isInOrderCart($product['id_product']) && $_in_gift_table <= 0) {
                    unset($products[$key]);
                } else {
                    $giftProducts[$key] = $product;
                    $giftProducts[$key]['giftcard_range'] = $this->getGiftCardRangeDisplay((int) $product['id_product']);
                    $tags = Tag::getProductTags((int) $product['id_product']);
                    $giftProducts[$key]['id_image'] = Gift::getId_image($product['id_product']);
                    $giftProducts[$key]['tags'] = ($tags && count($tags) > 0) ? array_shift($tags) : [];
                    $productTags[] = $giftProducts[$key]['tags'];
                    $giftProducts[$key]['tags'] = (count($giftProducts[$key]['tags']) > 0) ? implode(',', $giftProducts[$key]['tags']) : '';
                                    }
            }
        }

        $filterTags = [];
        if (!empty($productTags)) {
            foreach ($productTags as $ptags) {
                if ($ptags) {
                    foreach ($ptags as $tag) {
                        $filterTags[$tag] = $tag;
                    }
                }
            }
        }
        $category = new Category(Configuration::get('GIFT_CARD_CATEGORY'), $this->context->language->id);
        $this->context->smarty->assign([
            'category_name' => $category->name,
            'giftcard_category_description' => $category->description,
            'giftcard_category_image' => $category->id_image,
            'giftcard_category_link' => $this->context->link->getCategoryLink($category),
            'giftcard_category_link_rewrite' => $category->link_rewrite,
        ]);
        $this->context->smarty->assign([
            'filterTags' => $filterTags,
            'giftProducts' => $giftProducts,
        ]);
        $this->setTemplate($this->tpl);
    }

    protected function getGiftCardRangeDisplay($idProduct)
    {
        $giftCard = Gift::getCardValue((int) $idProduct);
        if (empty($giftCard['card_value'])) {
            return '';
        }

        $rawValues = array_filter(array_map('trim', explode(',', $giftCard['card_value'])), 'strlen');
        if (empty($rawValues)) {
            return '';
        }

        $numericValues = array_map('floatval', $rawValues);
        sort($numericValues, SORT_NUMERIC);

        $currencyIsoCode = $this->context->currency->iso_code;
        $locale = method_exists($this->context, 'getCurrentLocale') ? $this->context->getCurrentLocale() : null;

        $formatPrice = function ($value) use ($locale, $currencyIsoCode) {
            if ($locale) {
                return $locale->formatPrice($value, $currencyIsoCode);
            }

            return Tools::displayPrice($value, $this->context->currency);
        };

        $minFormatted = $formatPrice($numericValues[0]);

        if (count($numericValues) === 1) {
            return $minFormatted;
        }

        $maxFormatted = $formatPrice($numericValues[count($numericValues) - 1]);
        $minAmountOnly = preg_replace('/\s*' . preg_quote($currencyIsoCode, '/') . '$/u', '', $minFormatted);

        return trim($minAmountOnly) . ' - ' . $maxFormatted;
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
            $sender = (!empty($this->context->customer) && $this->context->customer->id) ? sprintf('%s %s', $this->context->customer->firstname, $this->context->customer->lastname) : $this->module->l('Your Name');
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
