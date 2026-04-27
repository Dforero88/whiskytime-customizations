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
class AdminGiftCardsController extends ModuleAdminController
{
    public $msg = 0;

    public $gift = [];

    public function __construct()
    {
        $this->table = 'gift_card';
        $this->className = 'Gift';
        $this->identifier = 'id_gift_card';
        $this->context = Context::getContext();
        $this->deleted = false;
        $this->bootstrap = true;

        $this->submit_action = 'SaveGift' . $this->table;

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');

        parent::__construct();

        $this->bulk_actions = ['delete' => ['text' => $this->module->l('Delete selected'), 'confirm' => $this->module->l('Delete selected items?')]];

        $this->_group = 'GROUP BY a.' . $this->identifier;
        $this->_select = 'pl.name';
        $this->_join = '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
        ON (a.`id_product` = pl.`id_product` AND pl.id_lang = ' . (int) $this->context->employee->id_lang . ')';

        $this->fields_list = [
            'id_gift_card' => [
                'title' => '#',
                'width' => 25,
            ],
            'id_product' => [
                'title' => $this->module->l('Photo'),
                'width' => 'auto',
                'callback' => 'getProductPhoto',
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ],
            'name' => [
                'title' => $this->module->l('Name'),
                'width' => 'auto',
                'filter_key' => 'pl!name',
                'havingFilter' => true,
            ],
            'qty' => [
                'title' => $this->module->l('Quantity'),
                'type' => 'int',
                'callback' => 'getProductQuantity',
            ],
            'value_type' => [
                'title' => $this->module->l('Value Type'),
            ],
            'reduction_type' => [
                'title' => $this->module->l('Discount Type'),
            ],
            'reduction_currency' => [
                'title' => $this->module->l('Discount Currency'),
                'callback' => 'getCurrencyCode',
                'search' => false,
                'orderby' => false,
            ],
            'validity_period' => [
                'title' => $this->module->l('Validity'),
                'width' => 40,
                'align' => 'center',
                'callback' => 'getValidity',
            ],
            'status' => [
                'title' => $this->module->l('Status'),
                'width' => 70,
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false,
            ],
        ];
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
    }

    public function getValidity($validity, $row)
    {
        return sprintf($this->module->l('%d %s'), $validity, Tools::ucfirst($row['validity_type']));
    }

    public function getProductQuantity($qty, $row)
    {
        if ($row && $row['id_product']) {
            return (int) StockAvailable::getQuantityAvailableByProduct($row['id_product']);
        }

        return (int) $qty;
    }

    public function getProductPhoto($id_product)
    {
        $imgPath = sprintf('%smodules/%s/views/img/no_image.png', __PS_BASE_URI__, $this->module->name);
        if ($id_product) {
            $product = new Product($id_product, false, $this->context->language->id);
            $cover = Product::getCover($product->id);
            if ($cover) {
                $path_to_image = 'p/' . Image::getImgFolderStatic($cover['id_image']) . (int) $cover['id_image'] . '.jpg';
                if (is_file(_PS_IMG_DIR_ . $path_to_image)) {
                    $imgPath = sprintf('%simg/%s', __PS_BASE_URI__, $path_to_image);
                }
            }
        }

        $this->context->smarty->assign('image_path', $imgPath);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/img.tpl');
    }

    public function getCurrencyCode($id_currecny)
    {
        if ($id_currecny) {
            $currency = new Currency($id_currecny);

            return $currency->iso_code;
        }
    }

    public function initPageHeaderToolbar()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            if (empty($this->display)) {
                $this->page_header_toolbar_btn['new'] = [
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->module->l('Add Gift Card'),
                    'icon' => 'process-icon-new',
                ];
            }
            parent::initPageHeaderToolbar();
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('SaveGift' . $this->table)) {
            return $this->addGiftCard();
        } elseif (Tools::isSubmit('status' . $this->table)) {
            $id_gift_card = (int) Tools::getValue('id_gift_card');
            if (!Validate::isLoadedObject($giftCard = new Gift($id_gift_card))) {
                $this->errors[] = $this->module->l('Gift card not found.');
            } else {
                if (!$giftCard->id_product || !Validate::isLoadedObject($giftProduct = new Product($giftCard->id_product))) {
                    $this->errors[] = $this->module->l('Gift card product not found.');
                } else {
                    $giftCard->status = !$giftCard->status;
                    $giftProduct->active = $giftCard->status;
                    if ($giftCard->update()) {
                        $giftProduct->update();

                        return $this->confirmations[] = $this->module->l('Status updated successfully.');
                    }

                    return $this->errors[] = $this->module->l('Gift card product not found.');
                }
            }
        }
        parent::postProcess();
    }

    public function renderView()
    {
        parent::renderView();
        $id_product = Gift::getIdProductFromCard((int) Tools::getValue('id_gift_card'));
        if ($id_product && Validate::isLoadedObject($product = new Product((int) $id_product))) {
            if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $productLink = $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => $product->id]);
            } else {
                $productLink = $this->context->link->getAdminLink('AdminProducts') . '&updateproduct&id_product=' . (int) $product->id;
            }
            Tools::redirectAdmin($productLink);
        } else {
            $this->errors[] = $this->module->l('Gift Product not found in catalog.');
        }

        return '';
    }

    public function renderList()
    {
        $this->context->smarty->assign([
            'edit_category_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsCategory'),
            'add_new_giftcard' => $this->context->link->getAdminLink('AdminGiftCards'),
            'add_img_template_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsImageTemplate'),
            'giftcard_settings' => $this->context->link->getAdminLink('AdminGiftSettings'),
            'gift_templates' => $this->context->link->getAdminLink('AdminGiftTemplates'),
            'ordered_giftcards' => $this->context->link->getAdminLink('AdminOrderedGiftcards'),
        ]);
        return parent::renderList();
    }

    public function renderForm()
    {
        $this->loadObject(true);
        $defaultCurrencyObject = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex . '&token=' . $this->token;
        }

        if (Tools::getValue('id_gift_card')) {
            $gift = new Gift(Tools::getValue('id_gift_card'));
            $this->gift = Gift::getGiftCard($gift->id_product, $gift->id, $this->context->language->id);
        }

        $radio = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        $currencies = [];
        foreach (Currency::getCurrenciesByIdShop($this->context->shop->id) as $currency) {
            $currencies[] = [
                'id_option' => $currency['id_currency'],
                'name' => $currency['name'] . (($currency['sign']) ? sprintf(' (%s)', $currency['sign']) : sprintf(' (%s)', $currency['iso_code'])),
            ];
        }
        $this->fields_form = [
            'tabs' => [
                'product' => $this->module->l('Gift Product'),
                'voucher' => $this->module->l('Gift Voucher'),
            ],
            'legend' => [
                'title' => (Tools::getValue('id_gift_card')) ? $this->module->l('Edit Gift Product') : $this->module->l('Add Gift Product'),
                'icon' => 'icon-conf',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_gift_card',
                    'tab' => 'product',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_product',
                    'tab' => 'product',
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Gift Card Name'),
                    'desc' => $this->module->l('Your Product name will be considered as your Gift card name.'),
                    'name' => 'card_name',
                    'lang' => true,
                    'tab' => 'product',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->module->l('Gift Card Description'),
                    'name' => 'product_description',
                    'lang' => true,
                    'class' => 'rte autoload_rte',
                    'tab' => 'product',
                ],
                [
                    'type' => 'tags',
                    'label' => $this->module->l('Gift Card Tags'),
                    'desc' => $this->module->l('Use a comma to create separate tags. E.g.: birthday, christmas, friendship..'),
                    'name' => 'tags',
                    'lang' => true,
                    'id' => 'gift_card_tags',
                    'tab' => 'product',
                ],
                [
                    'type' => $radio,
                    'is_bool' => true,
                    'label' => $this->module->l('Image/Template'),
                    'name' => 'gcp_image_type',
                    'values' => [
                        [
                            'id' => 'template',
                            'value' => 'template',
                            'label' => $this->module->l('Template'),
                            // 'checked' => true,
                        ],
                        [
                            'id' => 'image',
                            'value' => 'image',
                            'label' => $this->module->l('Image'),
                        ],
                    ],
                    'tab' => 'product',
                ],
                [
                    'type' => 'giftimage_template',
                    'name' => 'giftimage_template',
                    'label' => $this->module->l('Template Giftcard'),
                    'tab' => 'product',
                    'form_group_class' => 'template-image-wrapper', // Class for JS toggling
                ],
                [
                    'type' => 'giftimage',
                    'name' => 'giftimage',
                    'label' => $this->module->l('Gift Card Image Upload'),
                    'tab' => 'product',
                    'form_group_class' => 'upload-image-wrapper', // Class for JS toggling
                ],
                [
                    'type' => 'radio',
                    'name' => 'value_type',
                    'label' => $this->module->l('Card Type'),
                    'col' => 4,
                    'values' => [
                        ['id' => 'dropdown', 'value' => 'dropdown', 'label' => $this->module->l('Drop Down')],
                        ['id' => 'fixed', 'value' => 'fixed', 'label' => $this->module->l('Fixed Price')],
                        ['id' => 'range', 'value' => 'range', 'label' => $this->module->l('Range Type')],
                    ],
                    'tab' => 'product',
                ],
                [
                    'type' => 'card_value',
                    'name' => 'card_value',
                    'label' => $this->module->l('Card Value'),
                    'prefix' => $defaultCurrencyObject->iso_code,
                    'tab' => 'product',
                ],
                [
                    'type' => 'text',
                    'name' => 'qty',
                    'label' => $this->module->l('Quantity'),
                    'col' => 2,
                    'tab' => 'product',
                ],
                [
                    'type' => 'tax_rules_group',
                    'name' => 'id_tax_rules_group',
                    'label' => $this->module->l('Tax Rule'),
                    'tab' => 'product',
                ],
                [
                    'type' => $radio,
                    'class' => 't',
                    'is_bool' => true,
                    'label' => $this->module->l('Status:'),
                    'name' => 'status',
                    'values' => [
                        [
                            'id' => 'status_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled'),
                        ],
                        [
                            'id' => 'status_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled'),
                        ],
                    ],
                    'tab' => 'product',
                ],
                [
                    'type' => 'radio',
                    'name' => 'apply_discount',
                    'label' => $this->module->l('Discount Type'),
                    'col' => 4,
                    'values' => [
                        ['id' => 'apply_discount_percent', 'value' => 'percent', 'label' => $this->module->l('Percent (%)')],
                        ['id' => 'apply_discount_amount', 'value' => 'amount', 'label' => $this->module->l('Amount') . '<b> ' . $this->module->l('(card values will bes used)') . '</b>'],
                    ],
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'discount_value',
                    'name' => 'discount_value',
                    'label' => $this->module->l('Value'),
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'select',
                    'col' => 4,
                    'label' => $this->module->l('Voucher Tax'),
                    'name' => 'reduction_tax',
                    'id' => 'voucher_reduction_tax',
                    'options' => [
                        'query' => [
                            ['id_option' => 0, 'name' => $this->module->l('Tax excluded')],
                            ['id_option' => 1, 'name' => $this->module->l('Tax included')],
                        ],
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'radio',
                    'name' => 'apply_discount_to',
                    'label' => $this->module->l('Apply Discount To'),
                    'col' => 4,
                    'values' => [
                        ['id' => 'apply_discount_to_order', 'value' => 'order', 'label' => $this->module->l('Order (without shipping)')],
                        ['id' => 'apply_discount_to_product', 'value' => 'specific', 'label' => $this->module->l('Specific Product')],
                    ],
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'product_search',
                    'name' => 'reductionProductFilter',
                    'hint' => $this->module->l('Discount will be applied to specific product only.'),
                    'label' => $this->module->l('Product'),
                    'cols' => 6,
                    'id' => 'reductionProductFilter',
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'select',
                    'col' => 3,
                    'label' => $this->module->l('Discount Currency'),
                    'name' => 'reduction_currency',
                    'options' => [
                        'query' => $currencies,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Coupon code Length'),
                    'desc' => $this->module->l('Minimum length is 4 and maximum length is 20.'),
                    'hint' => $this->module->l('Specified length of code will be generated, default length is 14.'),
                    'name' => 'length',
                    'col' => 2,
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'radio',
                    'name' => 'vcode_type',
                    'label' => $this->module->l('Coupon code type'),
                    'hint' => $this->module->l('Coupon code type(i.e Numeric or Alphanumeric)'),
                    'col' => 4,
                    'values' => [
                        ['id' => 'vcode_type_num', 'value' => 'NUMERIC', 'label' => $this->module->l('Numeric (i.e 12345)')],
                        ['id' => 'vcode_type_alphanum', 'value' => 'ALPHANUMERIC', 'label' => $this->module->l('Alphanumeric  (i.e ABC123)')],
                    ],
                    'tab' => 'voucher',
                ],
                [
                    'col' => 2,
                    'type' => 'text',
                    'name' => 'validity_period',
                    'required' => true,
                    'label' => $this->module->l('Validity Period'),
                    'desc' => $this->module->l('enter validity period.'),
                    'tab' => 'voucher',
                ],
                [
                    'type' => 'select',
                    'name' => 'validity_type',
                    'label' => $this->module->l('Validity Type'),
                    'desc' => $this->module->l('Select validity either in days or months.'),
                    'options' => [
                        'query' => [
                            ['id_option' => 'days', 'name' => $this->module->l('Days')],
                            ['id_option' => 'months', 'name' => $this->module->l('Months')],
                        ],
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                    'tab' => 'voucher',
                ],
                [
                    'type' => $radio,
                    'class' => 't',
                    'is_bool' => true,
                    'label' => $this->module->l('Free Shipping:'),
                    'name' => 'free_shipping',
                    'desc' => $this->module->l('Offer free shipping on order'),
                    'values' => [
                        [
                            'id' => 'free_shipping_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled'),
                        ],
                        [
                            'id' => 'free_shipping_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled'),
                        ],
                    ],
                    'tab' => 'voucher',
                ],
                [
                    'type' => $radio,
                    'class' => 't',
                    'is_bool' => true,
                    'label' => $this->module->l('Partial Use:'),
                    'name' => 'partial_use',
                    'desc' => $this->module->l('If you allow partial use, a new voucher will be created with the remainder.Only applicable if the voucher value is greater than the cart total.'),
                    'values' => [
                        [
                            'id' => 'partial_use_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled'),
                        ],
                        [
                            'id' => 'partial_use_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled'),
                        ],
                    ],
                    'tab' => 'voucher',
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
                'class' => 'btn button btn-default pull-right',
            ],
        ];

        $this->toolbar_title = (Tools::getValue('id_gift_card')) ? $this->module->l('Edit Card') : $this->module->l('Add Gift Card');
        $product = null;
        $languages = Language::getLanguages();

        if ($this->gift) {
            $product = new Product($this->gift['id_product'], true);
            $tags = [];
            foreach ($languages as $lang) {
                $tags[$lang['id_lang']] = '';
            }

            if ($product) {
                $cover = Product::getCover($product->id);

                $this->fields_value['card_name'] = $product->name;
                $this->fields_value['product_description'] = $product->description;
                $this->fields_value['qty'] = StockAvailable::getQuantityAvailableByProduct($product->id);

                $product = (array) $product;
                $product['id_cover'] = $cover['id_image'];

                if (Tag::getProductTags($this->gift['id_product'])) {
                    foreach (Tag::getProductTags($this->gift['id_product']) as $id_lang => $tag) {
                        $tags[$id_lang] = $tag ? implode(',', $tag) : '';
                    }
                }
            }

            $this->fields_value['selected_gc_template'] = $this->getTemplateImages();
            $this->fields_value['tags'] = $tags;
            $this->fields_value['status'] = (bool) $this->gift['status'];
            $this->fields_value['gcp_image_type'] = $this->gift['gcp_image_type'];
            $this->fields_value['value_type'] = $this->gift['value_type'];
            $this->fields_value['apply_discount'] = $this->gift['reduction_type'];
            $this->fields_value['vcode_type'] = $this->gift['vcode_type'];
            $this->fields_value['apply_discount_to'] = ($this->gift['id_discount_product'] > 0) ? 'specific' : 'order';
        } else {
            $this->fields_value['status'] = true;
            $this->fields_value['value_type'] = 'fixed';
            $this->fields_value['gcp_image_type'] = 'image';
            $this->fields_value['apply_discount'] = 'amount';
            $this->fields_value['vcode_type'] = 'ALPHANUMERIC';
            $this->fields_value['apply_discount_to'] = 'order';
        }

        $this->context->smarty->assign('current_lang', $this->context->language->id);
        $this->context->smarty->assign('giftcard_templates_array', $this->getTemplateImages());
        $this->context->smarty->assign('languages', $languages);
        $this->context->smarty->assign('module', $this->module);

        $iso_tiny_mce = $this->context->language->iso_code;
        $iso_tiny_mce = (file_exists(_PS_JS_DIR_ . 'tiny_mce/langs/' . $iso_tiny_mce . '.js') ? $iso_tiny_mce : 'en');

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->module->l('Shop association'),
                'name' => 'checkBoxShopAsso',
                'tab' => 'product',
            ];
        }
        $this->context->smarty->assign([
            'iso_tiny_mce' => $iso_tiny_mce,
            'card' => $this->gift,
            'giftcard_image_type' => 'template',
            // 'base_uri' => _PS_BASE_URL_SSL_ . _PS_IMG_ . 'giftcard_templates/',
            'base_uri' => Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . 'img/giftcard_templates/',
            'currentIndex' => self::$currentIndex,
            'currentToken' => $this->token,
            'id_lang' => $this->context->language->id,
            'id_currency' => $this->context->cookie->id_currency,
            'msg' => $this->msg,
            'link' => $this->context->link,
            'token' => Tools::getAdminTokenLite('AdminProducts'),
            'current_id_tab' => (int) $this->context->controller->id,
            'version' => _PS_VERSION_,
            'product' => $product,
            'tax_exclude_taxe_option' => Tax::excludeTaxeOption(),
            'currencies' => Currency::getCurrencies(false, true, true),
            'tax_rules_groups' => TaxRulesGroup::getTaxRulesGroups(true),
            'ad' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
            'default_currency' => Configuration::get('PS_CURRENCY_DEFAULT'),
            'default_currency_object' => $defaultCurrencyObject,
        ]);

        $this->context->smarty->assign([
            'edit_category_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsCategory'),
            'add_new_giftcard' => $this->context->link->getAdminLink('AdminGiftCards'),
            'add_img_template_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsImageTemplate'),
            'giftcard_settings' => $this->context->link->getAdminLink('AdminGiftSettings'),
            'gift_templates' => $this->context->link->getAdminLink('AdminGiftTemplates'),
            'ordered_giftcards' => $this->context->link->getAdminLink('AdminOrderedGiftcards'),
            'ajaxGc' => $this->context->link->getModuleLink(
                $this->module->name,
                'ajax',
                ['ajax' => 1],
                true,
                $this->context->cookie->id_lang,
                $this->context->shop->id
            ),
        ]);

        return parent::renderForm();
    }

    protected function getTemplateImages()
    {
        $obj = new Gift((int) Tools::getValue('id_gift_card'));
        $price = $obj->card_value ?: Tools::getValue('card_value');
        $templates = [];
        $templateDir = _PS_IMG_DIR_ . 'giftcard_templates/';

        $all_template_images = GiftCardImageTemplateModel::getAllTemplates($this->context->language->id, $this->context->shop->id, true);
        $template_text = [];
        foreach (Language::getLanguages() as &$lang) {
            $template_text[$lang['id_lang']] = $this->module->l('prestashop');
        }
        foreach ($all_template_images as $template_image) {
            if (in_array(pathinfo($template_image['gc_image'], PATHINFO_EXTENSION), ['png'])) {
                $this->context->smarty->assign([
                    // 'preview_img_url' => Media::getMediaPath(_PS_BASE_URL_SSL_ . _PS_IMG_ . '/giftcard_templates/' . $template_image['gc_image']),
                    'preview_img_url' => Media::getMediaPath(Tools::getShopDomainSsl(true) . _PS_IMG_ . '/giftcard_templates/' . $template_image['gc_image']),
                    'price' => $price ?: $template_image['price'],
                    'discount_code' => $template_image['discount_code'],
                    'is_customization_enabled' => Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED'),
                    'template_text' => $template_text,
                    'bg_color' => $template_image['bg_color'],
                    'default_form_language' => $this->context->language->id,
                    'id_giftcard_image_template' => $this->identifier,
                ]);

                $templates[] = [
                    'id_option' => $template_image['gc_image'],
                    'id_gc_img_temp' => $template_image['id_giftcard_image_template'],
                    // 'img_url' => Media::getMediaPath(_PS_BASE_URL_SSL_ . _PS_IMG_ . '/giftcard_templates/' . $template_image['gc_image']),
                    'img_url' => Media::getMediaPath(Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . 'img/giftcard_templates/' . $template_image['gc_image']),
                    'image' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'giftcard/views/templates/admin/gift_cards/assets/gc_template_img_dropdown_list.tpl'),
                ];

                // $image_url = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'giftcard/views/templates/admin/gift_cards_image_template/assets/gc_template_img_list.tpl');
                // $templates[] = $image_url;
            }
        }

        return $templates;
    }

    public function addGiftCard()
    {
        $action = 'add';
        $id_gift_card = (int) Tools::getValue('id_gift_card');
        $id_product = (int) Tools::getValue('id_product');

        if ($id_gift_card) {
            $action = 'update';
            $giftCard = new Gift($id_gift_card);
            $product = new Product($id_product, true);
        } else {
            $giftCard = new Gift();
            $product = new Product();
        }

        $now = date('Y-m-d H:i:s');
        $id_lang = $this->context->cookie->id_lang;
        $tags = [];
        $card_name = Tools::getValue('card_name');
        $length = (int) Tools::getValue('length');
        $vcode_type = (string) Tools::getValue('vcode_type');
        $card_qty = (int) Tools::getValue('qty');
        $status = (int) Tools::getValue('status');
        $free_ship = (int) Tools::getValue('free_shipping');
        $partial_use = (int) Tools::getValue('partial_use');
        $value_type = (string) Tools::getValue('value_type');
        $card_value = Tools::getValue('card_value');
        $gcp_image_type = (string) Tools::getValue('gcp_image_type');
        $red_type = (string) Tools::getValue('apply_discount');
        $reduction_product = (int) Tools::getValue('reduction_product');
        $apply_discount_to = (string) Tools::getValue('apply_discount_to');
        $min = (int) Tools::getValue('min');
        $max = (int) Tools::getValue('max');
        $min_percent = (float) Tools::getValue('min_percent');
        $max_percent = (float) Tools::getValue('max_percent');
        $id_attribute = 0;
        $reduction_tax = 0;
        $img_name = (string) $_FILES['giftimage']['name'];
        $reduction_currency = (int) Tools::getValue('reduction_currency');
        $price = 0.0;
        $id_tax_rules_group = (int) Tools::getValue('id_tax_rules_group');
        $validity_period = (int) Tools::getValue('validity_period');
        $validity_type = Tools::getValue('validity_type');
        if ($value_type == 'fixed') {
            $card_value = (float) $card_value;
            $price = (float) $card_value;
            $product->show_price = true;
        } else {
            $product->show_price = false;
        }
        if ($value_type == 'range') {
            $card_value = $min . ',' . $max;
        }
        $languages = Language::getLanguages(false);
        if (empty(Tools::getValue('card_name_' . Configuration::get('PS_LANG_DEFAULT')))) {
            $this->errors[] = $this->module->l('You must enter gift card name for default language.');
        }
        if (Tools::getValue('card_name_' . Configuration::get('PS_LANG_DEFAULT')) && !Validate::isCatalogName(Tools::getValue('card_name_' . Configuration::get('PS_LANG_DEFAULT')))) {
            $this->errors[] = $this->module->l('Gift card name is invalid for default language.');
        }

        foreach ($languages as $language) {
            if (Tools::getValue('card_name_' . $language['id_lang']) && !Validate::isCatalogName(Tools::getValue('card_name_' . $language['id_lang']))) {
                $this->errors[] = $this->module->l('Invalid card name in ') . $language['name'];
            } else {
                $product->name[$language['id_lang']] = (string) Tools::getValue('card_name_' . $language['id_lang']);
                $product->link_rewrite[$language['id_lang']] = Tools::str2url((string) Tools::getValue('card_name_' . $language['id_lang']));
            }

            if (!Validate::isCleanHtml(Tools::getValue('product_description_' . $language['id_lang']))) {
                $this->errors[] = $this->module->l('Invalid description in ') . $language['name'];
            } else {
                $product->description[$language['id_lang']] = (string) Tools::getValue('product_description_' . $language['id_lang']);
            }

            if ($value = Tools::getValue('tags_' . $language['id_lang'])) {
                if (!Validate::isTagsList($value)) {
                    $this->errors[] = sprintf(
                        $this->module->l('The tags list (%s) is invalid.'),
                        $language['name']
                    );
                } else {
                    $tags[(int) $language['id_lang']] = $value;
                }
            }
        }

        $product->quantity = $card_qty;
        $product->active = $status;
        $product->date_add = $now;
        $product->is_virtual = true;
        $product->price = $price;
        $product->show_price = ($value_type == 'fixed') ? true : false;
        $product->id_category_default = (Configuration::get('GIFT_CARD_CATEGORY')) ? (int) Configuration::get('GIFT_CARD_CATEGORY') : (int) Configuration::get('PS_HOME_CATEGORY');
        $product->redirect_type = '404';
        $product->id_tax_rules_group = $id_tax_rules_group;

        $categories = new Category($product->id_category_default, $id_lang);
        $product->category = $categories->link_rewrite;
        $product->show_price = ('fixed' == $value_type) ? true : false;

        $reduction_amount = $card_value;
        if ($red_type == 'amount') {
            $reduction_tax = (int) Tools::getValue('reduction_tax');
        } elseif ($red_type == 'percent') {
            if ($value_type == 'range') {
                $reduction_amount = $min_percent . ',' . $max_percent;
            } elseif ($value_type == 'dropdown') {
                $val1 = explode(',', $card_value);
                $val2 = explode(',', Tools::getValue('reduction_percent_dropdown'));
                $val1 = count($val1);
                $val2 = count($val2);
                if ($val1 != $val2) {
                    $this->errors[] = $this->module->l('No.of values of card price does not match No of values discount type.');
                } else {
                    $reduction_amount = Tools::getValue('reduction_percent_dropdown');
                }
            } elseif ($value_type == 'fixed') {
                $reduction_amount = (int) Tools::getValue('reduction_percent_fixed');
                $product->show_price = true;
            }

            $reduction_tax = 0;
        }
        if (empty($length)) {
            $length = 14;
        }
        $giftCard->id_product = $id_product;
        $giftCard->validity_period = $validity_period;
        $giftCard->validity_type = $validity_type;
        $giftCard->id_discount_product = $reduction_product;
        $giftCard->id_attribute = $id_attribute;
        $giftCard->card_name = $card_name;
        $giftCard->gcp_image_type = $gcp_image_type;
        $giftCard->qty = $card_qty;
        $giftCard->status = $status;
        $giftCard->length = $length;
        $giftCard->free_shipping = (int) $free_ship;
        $giftCard->partial_use = (int) $partial_use;
        $giftCard->value_type = $value_type;
        $giftCard->card_value = $card_value;
        $giftCard->vcode_type = $vcode_type;
        $giftCard->reduction_type = $red_type;
        $giftCard->reduction_amount = $reduction_amount;
        $giftCard->reduction_currency = $reduction_currency;
        $giftCard->reduction_tax = $reduction_tax;

        if (empty($card_qty) || $card_qty < 1 || !Validate::isInt($card_qty)) {
            $this->errors[] = $this->module->l('Invalid Card quantity');
        }
        if ($length < 4 || $length > 30 || !Validate::isInt($length)) {
            $this->errors[] = $this->module->l('Invalid Code length');
        }
        if (($red_type == 'amount' || $red_type == 'percent') && empty($reduction_amount)) {
            $this->errors[] = $this->module->l('Invalid discount amount/percentage');
        }
        if (($red_type == 'amount' || $red_type == 'percent') && ($apply_discount_to == 'specific' && empty($reduction_product))) {
            $this->errors[] = $this->module->l('Please specificy a discount product');
        }
        if (($value_type == 'dropdown' || $value_type == 'fixed' || $value_type == 'fixed') && empty($card_value)) {
            $this->errors[] = $this->module->l('Invalid Gift card price');
        }
        if (($value_type == 'range') && (empty($min) || empty($max) || $min < 1 || $max < 1 || $min >= $max)) {
            $this->errors[] = $this->module->l('Invalid Gift card price');
        }
        if (($value_type == 'range' && $red_type == 'percent') && (empty($min_percent) || empty($max_percent) || $min_percent < 1 || $max_percent < 1 || $min_percent >= $max_percent)) {
            $this->errors[] = $this->module->l('Invalid range discount percent');
        }

        if (!$validity_period || !Validate::isInt($validity_period)) {
            $this->errors[] = $this->module->l('Validatity period is required/invalid.');
        }

        if (!count($this->errors)) {
            if ($action == 'update') {
                if (!empty($img_name) && $img_name != null) {
                    $this->setImage($id_product, $product->link_rewrite);
                } elseif (Tools::getValue('gc_selected_template')) {
                    $this->setImage($id_product, $product->link_rewrite);
                }

                Db::getInstance()->delete('product_shop', 'id_product = 0');
                if ($tags) {
                    if (Tag::deleteTagsForProduct((int) $product->id)) {
                        $this->updateTags($tags, $languages, $product->id);
                    }
                }
                if ($product->update() && $giftCard->update()) {
                    if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                        $shops = Tools::getValue('checkBoxShopAsso_gift_card');
                        if (!empty($shops)) {
                            foreach ($shops as $shop) {
                                StockAvailable::setQuantity($product->id, $id_attribute, $card_qty, (int) $shop, false);
                            }
                        }
                    } else {
                        StockAvailable::setQuantity($product->id, $id_attribute, $card_qty);
                    }
                    $this->confirmations[] = $this->module->l('Gift card updated successfully.');
                }
            } else {
                if ($product->add()) {
                    $giftCard->id_product = (int) $product->id;
                    $giftCard->save();
                    $product->updateCategories([Configuration::get('PS_HOME_CATEGORY'), Configuration::get('GIFT_CARD_CATEGORY')]);
                    $this->setImage($product->id, $product->link_rewrite);
                    if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                        $shops = Tools::getValue('checkBoxShopAsso_gift_card');
                        if (!empty($shops)) {
                            foreach ($shops as $shop) {
                                StockAvailable::setQuantity($product->id, $id_attribute, $card_qty, (int) $shop, false);
                            }
                        }
                    } else {
                        StockAvailable::setQuantity($product->id, $id_attribute, $card_qty);
                    }
                    $this->updateTags($tags, $languages, $product->id);
                    Hook::exec('actionProductUpdate', ['id_product' => (int) $product->id, 'product' => $product]);
                    $this->confirmations[] = $this->module->l('Gift card added successfully.');
                }
            }
            if (Shop::isFeatureActive()) {
                Gift::removeAssocShops($product->id);
                if ($giftCard->id) {
                    Db::getInstance()->delete('gift_card_shop', 'id_gift_card = ' . (int) $giftCard->id);
                }
                if ($shops = Tools::getValue('checkBoxShopAsso_gift_card')) {
                    foreach ($shops as $shop) {
                        Gift::updateGiftShops(
                            $product->id,
                            (int) $shop,
                            $product->id_category_default,
                            $product->id_tax_rules_group,
                            $product->active,
                            $price
                        );
                        if ($giftCard->id) {
                            Db::getInstance()->insert(
                                'gift_card_shop',
                                [
                                    'id_gift_card' => (int) $giftCard->id,
                                    'id_shop' => (int) $shop,
                                ]
                            );
                        }
                    }
                }
            }
        } else {
            $this->display = 'edit';

            return false;
        }
    }

    public function setImage($id_product, $legend)
    {
        $image = new Image();
        $image->id_product = (int) $id_product;
        $image->position = Image::getHighestPosition($id_product) + 1;
        Image::deleteCover((int) $id_product);
        $image->cover = true;

        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $image->legend[$language['id_lang']] = $legend[$language['id_lang']];
        }

        $image->id_image = $image->id;
        $image->add();
        $tmp_name = tempnam(_PS_PRODUCT_IMG_DIR_ , 'PS'); 
        if (Tools::getValue('gcp_image_type') == 'template') {
            $generated_giftcard_image_path = Tools::getValue('generated_giftcard_image_path');
            $parsed_url = parse_url($generated_giftcard_image_path, PHP_URL_PATH);
            $clean_path = str_replace(Configuration::get('PS_SHOP_NAME') . '/', '', $parsed_url);
            $local_path = _PS_ROOT_DIR_ . $clean_path;
            if (file_exists($local_path)) {
                if (copy($local_path, $tmp_name)) {
                    unlink($local_path);
                }
            }

        } elseif ($_FILES['giftimage']['size'] >= 0) {
            move_uploaded_file($_FILES['giftimage']['tmp_name'], $tmp_name);
        }

        $new_path = $image->getPathForCreation();
        ImageManager::resize($tmp_name, $new_path . '.' . $image->image_format);
        $images_types = ImageType::getImagesTypes('products');
        foreach ($images_types as $imageType) {
            ImageManager::resize($tmp_name, $new_path . '-' . stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format);
        }
    }

    public function ajaxProcess()
    {
        if (Tools::isSubmit('reductionProductFilter')) {
            $products = Product::searchByName($this->context->language->id, trim(Tools::getValue('q')));
            exit(json_encode($products));
        }
    }

    protected function updateTags($tags, $languages, $id_product)
    {
        $success = true;
        if ($tags) {
            foreach ($languages as $language) {
                $tags = isset($tags[$language['id_lang']]) ? explode(',', $tags[$language['id_lang']]) : [];
                if ($tags) {
                    foreach ($tags as $tag) {
                        if ($tag) {
                            $success &= Tag::addTags($language['id_lang'], (int) $id_product, $tag);
                        }
                    }
                }
            }

            return $success;
        }

        return false;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addjQueryPlugin([
            'date',
            'tagify',
        ]);
        $this->addJqueryUI([
            'ui.slider',
            'ui.datepicker',
        ]);

        $this->addJS([
            _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            _PS_JS_DIR_ . 'admin/product.js',
        ]);

        $this->addJS([_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js']);
        $this->addCSS([_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css']);
        $this->addCSS([_PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.css']);
        $this->addJS([_PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js']);
        return true;
    }
}
