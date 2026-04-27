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
class AdminOrderedGiftcardsController extends ModuleAdminController
{
    public $max_file_size;
    public $max_image_size;

    protected $_category;
    /**
     * @var string name of the tab to display
     */
    protected $tab_display;
    protected $tab_display_module;

    /**
     * The order in the array decides the order in the list of tab. If an element's value is a number, it will be preloaded.
     * The tabs are preloaded from the smallest to the highest number.
     *
     * @var array product tabs
     */
    protected $available_tabs = [];

    protected $default_tab = 'Informations';

    protected $available_tabs_lang = [];

    protected $position_identifier = 'id_product';

    protected $submitted_tabs;

    protected $id_current_category;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->context = Context::getContext();
        $this->_defaultOrderBy = 'o.id_order';
        $this->_defaultOrderWay = 'DESC';

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->module->l('Delete selected items?'),
            ],
        ];
        if (!Tools::getValue('id_product')) {
            $this->multishop_context_group = false;
        }

        $statuses_array = [];
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->imageType = 'jpg';
        $this->_defaultOrderBy = $this->identifier;
        $this->max_file_size = (int) (Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->allow_export = true;

        // @since 1.5 : translations for tabs
        $this->available_tabs_lang = [
            'Informations' => $this->module->l('Information'),
            'Pack' => $this->module->l('Pack'),
            'VirtualProduct' => $this->module->l('Virtual Product'),
            'Prices' => $this->module->l('Prices'),
            'Seo' => $this->module->l('SEO'),
            'Images' => $this->module->l('Images'),
            'Associations' => $this->module->l('Associations'),
            'Shipping' => $this->module->l('Shipping'),
            'Combinations' => $this->module->l('Combinations'),
            'Features' => $this->module->l('Features'),
            'Customization' => $this->module->l('Customization'),
            'Attachments' => $this->module->l('Attachments'),
            'Quantities' => $this->module->l('Quantities'),
            'Suppliers' => $this->module->l('Suppliers'),
            'Warehouses' => $this->module->l('Warehouses'),
        ];

        $this->available_tabs = ['Quantities' => 6, 'Warehouses' => 14];
        if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP) {
            $this->available_tabs = array_merge($this->available_tabs, [
                'Informations' => 0,
                'Pack' => 7,
                'VirtualProduct' => 8,
                'Prices' => 1,
                'Seo' => 2,
                'Associations' => 3,
                'Images' => 9,
                'Shipping' => 4,
                'Combinations' => 5,
                'Features' => 10,
                'Customization' => 11,
                'Attachments' => 12,
                'Suppliers' => 13,
            ]);
        }

        // Sort the tabs that need to be preloaded by their priority number
        asort($this->available_tabs, SORT_NUMERIC);

        /* Adding tab if modules are hooked */
        $modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                $this->available_tabs['Module' . Tools::ucfirst($m['module'])] = 23;
                $this->available_tabs_lang['Module' . Tools::ucfirst($m['module'])] = Module::getModuleName($m['module']);
            }
        }

        if (Tools::getValue('reset_filter_category')) {
            $this->context->cookie->id_category_products_filter = false;
        }
        if (Shop::isFeatureActive() && $this->context->cookie->id_category_products_filter) {
            $category = new Category((int) $this->context->cookie->id_category_products_filter);
            if (!$category->inShop()) {
                $this->context->cookie->id_category_products_filter = false;
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
            }
        }
        /* Join categories table */
        if ($id_category = (int) Tools::getValue('productFilter_cl!name')) {
            $this->_category = new Category((int) $id_category);
            $_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
        } else {
            if ($id_category = (int) Tools::getValue('id_category')) {
                $this->id_current_category = $id_category;
                $this->context->cookie->id_category_products_filter = $id_category;
            } elseif ($id_category = $this->context->cookie->id_category_products_filter) {
                $this->id_current_category = $id_category;
            }
            if ($this->id_current_category) {
                $this->_category = new Category((int) $this->id_current_category);
            } else {
                $this->_category = new Category();
            }
        }

        $join_category = false;
        if (Validate::isLoadedObject($this->_category) && empty($this->_filter)) {
            $join_category = true;
        }

        if (true === Tools::version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
            $this->_join .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = a.`id_product`)
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
            ' . StockAvailable::addSqlShopRestriction(null, null, 'sav') . ') ';

            $alias = 'sa';
            $alias_image = 'image_shop';

            $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int) $this->context->shop->id : 'a.id_shop_default';
            $this->_join .= ' JOIN `' . _DB_PREFIX_ . 'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = ' . $id_shop . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (' . $alias . '.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = ' . $id_shop . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'shop` shop ON (shop.id_shop = ' . $id_shop . ') 
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 AND image_shop.id_shop = ' . $id_shop . ')';

            $this->_select .= 'shop.name as shopname, a.id_shop_default, ';
            $this->_select .= 'MAX(' . $alias_image . '.id_image) id_image, cl.name `name_category`, ' . $alias . '.`price`, 0 AS price_final, sav.`quantity` as sav_quantity, ' . $alias . '.`active`, IF(sav.`quantity`<=0, 1, 0 ) badge_danger';
        } else {
            $this->_join .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
            ' . StockAvailable::addSqlShopRestriction(null, null, 'sav') . ') ';

            $alias = 'sa';
            $alias_image = 'image_shop';

            $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int) $this->context->shop->id : 'a.id_shop_default';
            $this->_join .= ' JOIN `' . _DB_PREFIX_ . 'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = ' . $id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (' . $alias . '.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = ' . $id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'shop` shop ON (shop.id_shop = ' . $id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = a.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = ' . $id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = image_shop.`id_image`)
                LEFT JOIN `' . _DB_PREFIX_ . 'product_download` pd ON (pd.`id_product` = a.`id_product` AND pd.`active` = 1)';

            $this->_select .= 'shop.`name` AS `shopname`, a.`id_shop_default`, ';
            $this->_select .= $alias_image . '.`id_image` AS `id_image`, cl.`name` AS `name_category`, ' . $alias . '.`price`, 0 AS `price_final`, a.`is_virtual`, pd.`nb_downloadable`, sav.`quantity` AS `sav_quantity`, ' . $alias . '.`active`, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`';
        }
        // custom box query
        $this->_select .= ', o.*, o.reference AS order_reference, od.unit_price_tax_incl as price,
        o.date_add AS order_date_add,
        osl.`name` AS `osname`,
        os.`color`,
        od.product_quantity AS ordered_quantity,
        cb.has_voucher, cb.specific_date,';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'ordered_gift_cards` cb ON (a.`id_product` = cb.`id_product` AND cb.id_order > 0)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (cb.`id_order` = o.`id_order`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (cb.`id_product` = od.`product_id` AND cb.id_order = od.id_order)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = o.`current_state`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
        ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_where .= 'AND a.`id_product` IN (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'ordered_gift_cards` WHERE id_order > 0)';

        if ($join_category) {
            $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = ' . (int) $this->_category->id . ') ';
            $this->_select .= ' , cp.`position`, ';
        }

        $this->_use_found_rows = false;

        $this->fields_list = [];
        $this->fields_list['id_product'] = [
            'title' => $this->module->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
        ];

        $this->fields_list['order_reference'] = [
            'title' => $this->module->l('Order Reference'),
            'align' => 'left',
            'filter_key' => 'o!reference',
        ];

        $this->fields_list['id_image'] = [
            'title' => $this->module->l('Image'),
            'align' => 'center',
            'callback' => 'getProductImage',
            'orderby' => false,
            'filter' => false,
            'search' => false,
        ];

        $this->fields_list['name'] = [
            'title' => $this->module->l('Product'),
            'filter_key' => 'b!name',
        ];

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = [
                'title' => $this->module->l('Default shop'),
                'filter_key' => 'shop!name',
            ];
        }

        $this->fields_list['unit_price_tax_incl'] = [
            'title' => $this->module->l('Price'),
            'type' => 'price',
            'align' => 'text-right',
            'filter_key' => 'od!unit_price_tax_incl',
        ];

        $this->fields_list['ordered_quantity'] = [
            'title' => $this->module->l('Qty'),
            'type' => 'int',
            'align' => 'text-right',
            'filter_key' => 'od!ordered_quantity',
            'orderby' => true,
        ];

        $this->fields_list['osname'] = [
            'title' => $this->module->l('Status'),
            'type' => 'select',
            'color' => 'color',
            'list' => $statuses_array,
            'filter_key' => 'os!id_order_state',
            'filter_type' => 'int',
            'order_key' => 'osname',
        ];

        $this->fields_list['payment'] = [
            'title' => $this->module->l('Payment'),
        ];

        $this->fields_list['order_date_add'] = [
            'title' => $this->module->l('Ordered Date'),
            'filter_key' => 'o!date_add',
            'align' => 'text-center',
            'type' => 'datetime',
            'class' => 'fixed-width-sm',
            'orderby' => false,
        ];

        $this->fields_list['reference'] = [
            'title' => '',
            'align' => 'text-center',
            'callback' => 'showItemsIcon',
            'orderby' => false,
            'search' => false,
        ];

         $this->context->smarty->assign([
            'edit_category_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsCategory'),
            'add_new_giftcard' => $this->context->link->getAdminLink('AdminGiftCards'),
            'add_img_template_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsImageTemplate'),
            'giftcard_settings' => $this->context->link->getAdminLink('AdminGiftSettings'),
            'gift_templates' => $this->context->link->getAdminLink('AdminGiftTemplates'),
            'ordered_giftcards' => $this->context->link->getAdminLink('AdminOrderedGiftcards'),
        ]);
    }

    public function showItemsIcon($id, $tr)
    {
        $customizationData = Product::getAllCustomizedDatas($tr['id_cart']);
        if (!empty($customizationData)) {
            $this->context->smarty->assign([
                'row' => $tr,
            ]);

            return $this->createTemplate('item_icon.tpl')->fetch();
        }
    }

    public function getProductImage($id, $row)
    {
        $id_product = (int) $row['id_product'];
        $imgPath = sprintf('%smodules/%s/views/img/no_image.png', __PS_BASE_URI__, $this->module->name);
        if ($id_product) {
            $product = new Product($id_product, false, $this->context->language->id);
            $cover = Product::getCover($product->id);
            if (!empty($cover)) {
                $path_to_image = 'p/' . Image::getImgFolderStatic($cover['id_image']) . (int) $cover['id_image'] . '.jpg';
                if (is_file(_PS_IMG_DIR_ . $path_to_image)) {
                    $imgPath = sprintf('%simg/%s', __PS_BASE_URI__, $path_to_image);
                }
            }
        }
        $this->context->smarty->assign('image_path', $imgPath);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/img.tpl');
    }

    public function renderKpis()
    {
        return false;
    }

    public function renderList()
    {
        $this->addRowAction('vieworder');
        $stock_management = Configuration::get('PS_STOCK_MANAGEMENT');
        $this->tpl_list_vars['ps_new'] = ((true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) ? 1 : 0);
        $this->tpl_list_vars['stock_management'] = $stock_management;
        $this->tpl_list_vars['multishop_active'] = Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
        $this->tpl_list_vars['current_date'] = date('Y-m-d');
        $this->tpl_list_vars['sendtosomeone_action'] = $this->context->link->getAdminLink('AdminOrderedGiftcards');
        $this->list_no_link = true;

        return parent::renderList();
    }

    public function displayViewOrderLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('list_action_view_order.tpl');
        if (!array_key_exists('ViewOrder', self::$cache_lang)) {
            self::$cache_lang['ViewOrder'] = $this->module->l('Order Detail');
        }

        $token = Tools::getAdminTokenLite('AdminOrders');
        $id = Gift::getOrderIdByProduct($id);
        $tpl->assign([
            'href' => $this->context->link->getAdminLink('AdminOrders', true, [], ['id_order' => $id, 'vieworder' => 1]),
            'action' => self::$cache_lang['ViewOrder'],
        ]);

        return $tpl->fetch();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function setMedia($newTheme = false)
    {
        parent::setMedia($newTheme);
        $this->addJs($this->module->getPathUri() . 'views/js/giftcard_admin.js');
    }

    public function processSendtosomeone()
    {
        $id_cart = Tools::getValue('id_cart');
        if ($id_cart && Validate::isLoadedObject($cart = new Cart((int) $id_cart))) {
            $this->module->generateVoucher($cart->id, 'sendsomeone');
            Tools::redirectAdmin(sprintf(
                '%s&conf=10',
                $this->context->link->getAdminLink('AdminOrderedGiftcards')
            ));
        }
        return true;
    }
}
