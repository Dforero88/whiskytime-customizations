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
class AdminEventsController extends ModuleAdminController
{
    public function __construct()
    {
        $os = PHP_OS;
        switch ($os) {
            case 'Linux':
                define('SEPARATOR', '/');
                break;
            case 'Windows':
                define('SEPARATOR', '\\');
                break;
            default:
                define('SEPARATOR', '/');
                break;
        }
        $this->table = 'fme_events';
        $this->className = 'Events';
        $this->identifier = 'event_id';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;
        parent::__construct();
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected'),
                'confirm' => $this->module->l('Delete selected items?'),
            ],
        ];
        $this->context = Context::getContext();
        $this->fields_list = [
            'event_id' => [
                'title' => $this->module->l('ID'),
                'width' => 25,
            ],
            'event_title' => [
                'title' => $this->module->l('Event Title'),
            ],
            'event_venu' => [
                'title' => $this->module->l('Event Venu'),
            ],
            'event_start_date' => [
                'title' => $this->module->l('Event Start Date'),
                'type' => 'text',
            ],
            'event_end_date' => [
                'title' => $this->module->l('Event End Date'),
                'type' => 'text',
            ],
            'event_status' => [
                'title' => $this->module->l('Enabled'),
                'active' => 'event_status',
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false,
            ],
        ];
        $url = '';
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $router = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('router');
 
            $url = $router->generate('admin_module_configure_action', [
                'module_name' => $this->module->name,
            ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $url = $this->context->link->getAdminLink('AdminModules', true);
        }
        $this->context->smarty->assign([
            'version' => _PS_VERSION_,
            'configure_link' => $url,
            'manage_events_link' => $this->context->link->getAdminLink('AdminEvents'),
            'events_details_link' => $this->context->link->getAdminLink('AdminEventsDetails'),
            'events_tags_link' => $this->context->link->getAdminLink('AdminTags'),
        ]);
    }

    public function init()
    {
        parent::init();
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $event_start_date = Tools::getValue('event_start_date');
            $event_end_date = Tools::getValue('event_end_date');
            if ($event_start_date > $event_end_date) {
                $this->errors[] = $this->module->l('Event End Date Must be Greater then Start Date');
            }
            if (isset($_FILES['event_image']) && $_FILES['event_image']['name']) {
                $check = getimagesize($_FILES['event_image']['tmp_name']);
                if ($check == false) {
                    $this->errors[] = $this->module->l('Event Image not Valid');
                }
            }
            $file_ary = $this->reArrayFiles($_FILES['galleryimages']);
            $file_ary = array_filter($file_ary);
            if ($file_ary && !empty($file_ary[0]['name'])) {
                $ObjFile = new ImageManager();
                $ObjFile = $ObjFile;
                foreach ($file_ary as $file) {
                    $check = getimagesize($file['tmp_name']);
                    if ($check == false) {
                        $this->errors[] = $this->module->l('Gallery Files are not Images');
                    }
                }
            }
        }
        Shop::addTableAssociation($this->table, [
            'type' => 'shop',
        ]);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ .
            'fme_events_shop` sa ON (a.`event_id` = sa.`event_id` AND sa.id_shop = ' .
            (int) $this->context->shop->id . ') ';
        }
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = ' . (int) Context::getContext()->shop->id;
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $lists = parent::renderList();

        return $lists;
    }

    public function renderForm()
    {
        $tags = EventTags::getAllTagNames((int) Context::getContext()->cookie->id_lang);
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->module->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }
        if (!($events = $this->loadObject(true))) {
            return;
        }
        $this->fields_form['submit'] = [
            'title' => $this->module->l('Saverr'),
            'class' => 'hide',
        ];
        if(isset($events->event_thumb_image)){
            $image = '<img src="' . _PS_IMG_ . $events->event_thumb_image .
            '" width="75" height="75" alt="' . $events->event_thumb_image . '" />';
            $this->fields_value = [
                'image' => $image ? $image : false,
                'size' => $image ? filesize(_PS_IMG_DIR_ . $events->event_thumb_image) / 1000 : true,
        ];
        } else {
            $image = '';
        }
        // $image = '<img src="' . _PS_IMG_ . $events->event_thumb_image .
        // '" width="75" height="75" alt="' . $events->event_thumb_image . '" />';
        // $this->fields_value = [
        //     'image' => $image ? $image : false,
        //     'size' => $image ? filesize(_PS_IMG_DIR_ . $events->event_thumb_image) / 1000 : true,
        // ];
        $id = (int) Tools::getValue('event_id');
        $is_enable_seat = Events::isEnableSeatMap($id);
        // $image = '<img src="' . _PS_IMG_ . $events->event_thumb_image . '" width="75" height="75" alt="' .
        // $events->event_thumb_image . '" />';
        if(!empty($events->event_image)){
            $events->event_image = '<img src="' . _PS_IMG_ . $events->event_image .
        '" width="250" height="250" alt="' . $events->event_image . '" />';
        } else{
            $events->event_image = '';
        }
        $obj = new Events();
        $product_id = $obj->getProductId($id);
        $productsCover = [];
        $productData = '';
        $productData = $obj->getProduct($id);
        foreach ($productData as $data) {
            $productsCover[$data['id_product']] = $obj->getProductImg($data['id_product']);
        }
        $eventTags = EventTags::getEventTags($id, (int) Context::getContext()->cookie->id_lang);
        $selectedTags = [];
        foreach ($eventTags as $tag) {
            $selectedTags[] = $tag['id_fme_tags'];
        }
        if ($id != 0) {
            $this->context->smarty->assign('selectedPIDS', $obj->getProductsAttached($id));
            $this->context->smarty->assign('event_id', $id);
            $this->context->smarty->assign('eventGalleryImages', $obj->getEventGalleryImages($id));
            $this->context->smarty->assign('image_status', $events->image_status);
            if ($is_enable_seat) {
                $this->context->smarty->assign('display_none', '1');
            }
            $this->context->smarty->assign('seat_selection', $events->seat_selection);
            $this->context->smarty->assign('pdf_status', $events->pdf_status);
            $this->context->smarty->assign('contact_name', $events->contact_name);
            $this->context->smarty->assign('contact_phone', $events->contact_phone);
            $this->context->smarty->assign('contact_fax', $events->contact_fax);
            $this->context->smarty->assign('event_venu', $events->event_venu);
            $this->context->smarty->assign('longitude', $events->longitude);
            $this->context->smarty->assign('latitude', $events->latitude);
            $this->context->smarty->assign('productData', $productData);
            $this->context->smarty->assign('product_id', $product_id);
            $this->context->smarty->assign('productsCover', $productsCover);
            $this->context->smarty->assign('languages', $this->context->controller->getLanguages());
            $this->context->smarty->assign('event_permalinks', $events->event_permalinks);
            $this->context->smarty->assign('event_title', $events->event_title);
            $this->context->smarty->assign('event_streaming_start_time', $events->event_streaming_start_time);
            $this->context->smarty->assign('event_streaming_end_time', $events->event_streaming_end_time);
            $this->context->smarty->assign('event_streaming', $events->event_streaming);
            $this->context->smarty->assign('event_start_date', $events->event_start_date);
            $this->context->smarty->assign('event_end_date', $events->event_end_date);
            $this->context->smarty->assign('event_page_title', $events->event_page_title);
            $this->context->smarty->assign('event_meta_keywords', $events->event_meta_keywords);
            $this->context->smarty->assign('event_meta_description', $events->event_meta_description);
            $this->context->smarty->assign('event_content', $events->event_content);
            $this->context->smarty->assign('event_status', $events->event_status);
            $this->context->smarty->assign('event_video', $events->event_video);
            $this->context->smarty->assign('facebook_link', $events->facebook_link);
            $this->context->smarty->assign('twitter_link', $events->twitter_link);
            $this->context->smarty->assign('instagram_link', $events->instagram_link);
            $this->context->smarty->assign('event_image', $events->event_image);
            $this->context->smarty->assign('contact_email', $events->contact_email);
            $this->context->smarty->assign('contact_address', $events->contact_address);
            $this->context->smarty->assign('image', $image);
            $this->context->smarty->assign('selectedTags', $selectedTags);
        }
        $currency = Currency::getDefaultCurrency()->sign;
        $default_currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $iso_code = $default_currency->iso_code;
        $this->context->smarty->assign('mode', $this->display);
        $this->context->smarty->assign('iso_code', $iso_code);
        $this->context->smarty->assign('currency', $currency);
        $this->context->smarty->assign('default_currency', Configuration::get('PS_CURRENCY_DEFAULT'));
        $Obj = new Events();
        $this->context->smarty->assign('token', Tools::getValue('token'));
        $this->context->smarty->assign('Obj', $Obj);
        $this->context->smarty->assign(
            'libpath',
            $this->context->link->getAdminLink('AdminEvents') . '&action=getproducts'
        );
        $this->context->smarty->assign('tags', $tags);

        return parent::renderForm();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $iso_tiny_mce = $this->context->language->iso_code;
        $iso_tiny_mce = file_exists(_PS_JS_DIR_ . 'tiny_mce/langs/' . $iso_tiny_mce . '.js') ? $iso_tiny_mce : 'en';
        $this->context->smarty->assign('iso_tiny_mce', $iso_tiny_mce);
        $controller = Dispatcher::getInstance()->getController();
        Media::addJsDef([
            'iso_tiny_micy' => $iso_tiny_mce,
            'css_content' => '',
            'base_url' => '',
            'controller' => $controller,
        ]);
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/jquery-ui-1.9.2.custom.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/fmeevents.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/fme_page.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/fme_table_jui.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/ColVis.css', 'screen');
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/jquery.accordion.js', 'screen');
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/jquery.cookie.min.js', 'screen');
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        $this->addJS(
            [
                _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            ]
        );
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/jquery.dataTables.js', 'screen');
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/ColVis.js', 'screen');
        if (Tools::version_compare(_PS_VERSION_, '1.7.8.0', '>=') == true) {
            $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/admin_ps1780_plus.js');
        } else {
            $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/admin.js');
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        $action = Tools::getValue('action');
        if ($action == 'getproducts') {
            $this->triggerAjaxProductsList();
        }
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $vedio = Tools::getValue('event_video');
            $update_event = Tools::getValue('display_none');
            $id_lang = $this->context->language->id;
            $event_title = Tools::getValue('event_title_' . $id_lang);
            $event_permalinks = Tools::getValue('event_permalinks_' . $id_lang);
            $event_content = Tools::getValue('event_content_' . $id_lang);
            $event_start_date = Tools::getValue('event_start_date');
            $event_end_date = Tools::getValue('event_end_date');
            $event_selected_tags = Tools::getValue('event_tags');
            $rx = '~
            ^(?:https?://)?                           # Optional protocol
            (?:www[.])?                              # Optional sub-domain
            (?:youtube[.]com/watch[?]v=|youtu[.]be/) # Mandatory domain name (w/ query string in .com)
            ([^&]{11})                               # Video id of 11 characters as capture group 1
                ~x';
            $p_name = '';
            $p_quantity = null;
            $p_price = null;
            $has_match = preg_match($rx, $vedio, $matches);
            $has_match = $has_match;
            $p_nameArr = Tools::getValue('p_name');
            $seat_selection = Tools::getValue('seat_selection');
            $p_quantityArr = Tools::getValue('p_quantity');
            $p_priceArr = Tools::getValue('p_price');
            $p_ids = Tools::getValue('p_id');
            $id = Tools::getValue('event_id');
            $new_seat_selection = 0;
            $is_enable_seat_m = Events::isEnableSeatMap($id);
            if ($seat_selection == 1 && $is_enable_seat_m != 1) {
                $new_seat_selection = 1;
                if (isset($_FILES['p_img']) && !empty($_FILES['p_img']['tmp_name'])) {
                    $img = $_FILES['p_img']['tmp_name'];
                }
            }
            if ($seat_selection != 1 && $update_event != 1) {
                if (isset($_FILES['p_img']) && !empty($_FILES['p_img']['tmp_name'])) {
                    $img = $_FILES['p_img']['tmp_name'];
                }
            }
            if ($seat_selection != 1 && $update_event == 1) {
                if (isset($_FILES['p_img']) && !empty($_FILES['p_img']['tmp_name'])) {
                    $img = $_FILES['p_img']['tmp_name'];
                }
            }

            EventTags::addEventTags($id, $event_selected_tags);
            $parray = [];
            if (!empty($p_nameArr)) {
                for ($i = 0; $i < count($p_nameArr); $i = $i + 1) {
                    $p_id = '';
                    if (!empty($p_nameArr[$i])) {
                        $p_name = $p_nameArr[$i];
                        $p_quantity = (int) $p_quantityArr[$i];
                        $p_price = (float) $p_priceArr[$i];
                        if (!empty($p_ids)) {
                            if (!empty($p_ids[$i])) {
                                $p_id = (int) $p_ids[$i];
                            }
                        }
                        $p_img = $img[$i];
                        if (!$p_name) {
                            $p_name = 'Event Product';
                        }
                        if (!$p_quantity) {
                            $p_quantity = 0;
                        }
                        if (!$p_price) {
                            $p_price = 0;
                        }
                        if ($event_title
                            && $event_permalinks
                            && $event_content
                            && $event_start_date && $event_end_date) {
                            if ($p_id) {
                                $object = new Product($p_id);
                            } else {
                                $object = new Product();
                            }
                            $id_lang = $this->context->cookie->id_lang;
                            $object->price = $p_price;
                            $object->id_tax_rules_group = Tools::getValue('id_tax_rules_group') ?
                            (int) Tools::getValue('id_tax_rules_group') : '0';
                            $object->id_manufacturer = Tools::getValue('id_manufacturer') ?
                            (int) Tools::getValue('id_manufacturer') : '0';
                            $object->id_supplier = Tools::getValue('id_supplier') ?
                            (int) Tools::getValue('id_supplier') : '0';
                            $object->quantity = $p_quantity;
                            $object->minimal_quantity = Tools::getValue('minimal_quantity') ?
                            (int) Tools::getValue('minimal_quantity') : '1';
                            $object->additional_shipping_cost = Tools::getValue('additional_shipping_cost') ?
                            (int) Tools::getValue('additional_shipping_cost') : '0';
                            $object->wholesale_price = Tools::getValue('wholesale_price') ?
                            (int) Tools::getValue('wholesale_price') : '0';
                            $object->ecotax = Tools::getValue('ecotax') ? (int) Tools::getValue('ecotax') : '0';
                            $object->width = Tools::getValue('width') ? (int) Tools::getValue('width') : '0';
                            $object->height = Tools::getValue('height') ? (int) Tools::getValue('height') : '0';
                            $object->depth = Tools::getValue('depth') ? (int) Tools::getValue('depth') : '0';
                            $object->weight = Tools::getValue('weight') ? (int) Tools::getValue('weight') : '0';
                            $object->out_of_stock = Tools::getValue('out_of_stock') ?
                            (int) Tools::getValue('out_of_stock') : '0';
                            $object->active = Tools::getValue('active') ? (int) Tools::getValue('active') : '1';
                            $object->id_category_default = Tools::getValue('id_category_default') ?
                            (int) Tools::getValue('id_category_default') : '2';
                            $object->available_for_order = Tools::getValue('available_for_order') ?
                            (int) Tools::getValue('available_for_order') : '1';
                            $object->show_price = Tools::getValue('show_price') ?
                            (int) Tools::getValue('show_price') : '1';
                            $object->on_sale = Tools::getValue('on_sale') ?
                            (int) Tools::getValue('on_sale') : '0';
                            $object->online_only = Tools::getValue('online_only') ?
                            (int) Tools::getValue('online_only') : '1';
                            $object->meta_keywords = $p_name;
                            $languages = Language::getLanguages(false);
                            foreach ($languages as $language) {
                                $object->name[$language['id_lang']] = $p_name;
                                $object->link_rewrite[$language['id_lang']] = Tools::str2url($p_name);
                            }
                            $object->visibility = 'none';
                            $categories = new Category($object->id_category_default, $id_lang);
                            $object->category = $categories->link_rewrite;
                            $object->addToCategories(2);
                            /* fme updated 2024-10-21 (known issue) */
                            $randomNumber = rand(0, 100);
                            $object->reference = 'events_product_' . $randomNumber . $randomNumber;
                            $object->ean13 = '1234567890123';
                            $object->isbn = '0-000-00000-0';
                            $object->upc = '0';
                            $object->mpn = '0-000-00000-0';
                            /* END, fme updated 2024-10-21 (known issue) */
                            if (!empty($p_id)) {
                                StockAvailable::setQuantity($object->id, 0, $p_quantity);
                                Product::updateIsVirtual((int) $object->id, true);
                                $object->update();
                            } else {
                                // $object->save();
                                if ($object->save()) {
                                    Product::updateIsVirtual((int) $object->id, true);
                                    $object->addToCategories(2);
                                    array_push($parray, $object->id);
                                    $object->update($object->id);
                                    Db::getInstance()->execute('
                                        DELETE FROM `' . _DB_PREFIX_ . 'fme_events_products`
                                        WHERE `id_product` = ' . (int) $p_id);
                                    $event_pids = $object->id;
                                    Db::getInstance()->execute('
                                        INSERT INTO `' . _DB_PREFIX_ . 'fme_events_products` (event_id, id_product)
                                        VALUES ("' . (int) $id . '", "' . (int) $event_pids . '")');
                                }
                            }
                            if (!empty($p_img)) {
                                $image = new Image();
                                $id_product = $object->id;
                                $legend = $object->link_rewrite;
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
                                $tmp_name = '';
                                if(version_compare(_PS_VERSION_,'9.0.0', '>=')) {
                                    $tmp_name = tempnam(_PS_PRODUCT_IMG_DIR_, 'PS');
                                } else {
                                    $tmp_name = tempnam(_PS_PROD_IMG_DIR_, 'PS');
                                }
                                // $tmp_name = tempnam(_PS_PROD_IMG_DIR_, 'PS');
                                move_uploaded_file($p_img, $tmp_name);
                                StockAvailable::setQuantity($object->id, 0, $p_quantity);
                                $new_path = $image->getPathForCreation();
                                ImageManager::resize($tmp_name, $new_path . '.' . $image->image_format);
                                $images_types = ImageType::getImagesTypes('products');
                                foreach ($images_types as $imageType) {
                                    ImageManager::resize(
                                        $tmp_name,
                                        $new_path . '-' . version_compare(_PS_VERSION_,'9.0.0', '>=') ? stripslashes($imageType['name']) : Tools::stripslashes($imageType['name']) . '.' .
                                        $image->image_format,
                                        $imageType['width'],
                                        $imageType['height'],
                                        $image->image_format
                                    );
                                }
                            } else {
                                StockAvailable::setQuantity($object->id, 0, $p_quantity);
                            }
                        }
                    }
                }
            }
            if ($new_seat_selection == 1) {
                $seatobj = new FmeSeatMapModel();
                $seatobj->event_id = $id;
                $hidden_seats_table = Tools::getValue('hidden_seats_table');
                $seatobj->seat_map = $hidden_seats_table;
                $seatobj->save();
            }
            if ($_FILES['event_image'] && $_FILES['event_image']['name']) {
                $ObjFile = new ImageManager();
                @mkdir(_PS_IMG_DIR_ . 'events/tmp', 0777, true);
                @mkdir(_PS_IMG_DIR_ . 'events/' . $id, 0777, true);
                $check = getimagesize($_FILES['event_image']['tmp_name']);
                if ($check != false) {
                    $imgRealName = $_FILES['event_image']['name'];
                    $imgRealName = preg_replace('/[^a-zA-Z0-9_.]/', '', $imgRealName);
                    $img_name = $imgRealName;
                    $realImgArray = explode('.', $imgRealName);
                    $realImgOnlyName = $realImgArray[0];
                    $realImgExt = $realImgArray[1];
                    move_uploaded_file(
                        $_FILES['event_image']['tmp_name'],
                        _PS_IMG_DIR_ . 'events/' . $id . '/' . $imgRealName
                    );
                    $ObjFile->resize(
                        _PS_IMG_DIR_ . 'events/' . $id . '/' . $img_name,
                        _PS_IMG_DIR_ . 'events/' . $id . '/' . $realImgOnlyName . '_thumb.' . $realImgExt,
                        150,
                        150
                    );
                    $imgThumbPath = 'events/' . $id . '/' . $realImgOnlyName . '_thumb.' . $realImgExt;
                    $imgPath = 'events/' . $id . '/' . $imgRealName;
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fme_events`
                        SET `event_image` = "' . pSQL($imgPath) . '",
                        `event_thumb_image` = "' . pSQL($imgThumbPath) . '"
                        WHERE `event_id` = ' . (int) $id);
                }
            }
            $file_ary = $this->reArrayFiles($_FILES['galleryimages']);
            $file_ary = array_filter($file_ary);
            if ($file_ary && !empty($file_ary[0]['name'])) {
                $ObjFile = new ImageManager();
                foreach ($file_ary as $file) {
                    $temp_check = getimagesize($file['tmp_name']);
                    if ($temp_check != false) {
                        @mkdir(_PS_IMG_DIR_ . 'eventsgallery/' . $id, 0777, true);
                        $imgRealName = $file['name'];
                        $realImgArray = explode('.', $imgRealName);
                        $realImgOnlyName = $realImgArray[0];
                        $realImgExt = $realImgArray[1];
                        move_uploaded_file(
                            $file['tmp_name'],
                            _PS_IMG_DIR_ . 'eventsgallery/' . $id . '/' . $imgRealName
                        );
                        $ObjFile->resize(
                            _PS_IMG_DIR_ . 'eventsgallery/' . $id . '/' . $file['name'],
                            _PS_IMG_DIR_ . 'eventsgallery/' . $id . '/' . $realImgOnlyName . '_thumb.' . $realImgExt,
                            150,
                            150
                        );
                        $imgThumbPath = 'eventsgallery/' . $id . '/' . $realImgOnlyName . '_thumb.' . $realImgExt;
                        $imgPath = 'eventsgallery/' . $id . '/' . $imgRealName;
                        if ($imgRealName) {
                            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ .
                                'fme_events_gallery` (image_file, events_id)
                            VALUES ("' . pSQL($imgPath) . '", "' . (int) $id . '")');
                        }
                    }
                }
            }
        } elseif (Tools::getValue('action') == 'delete_this_data') {
            $id = Tools::getValue('p_id');
            if (!empty($id)) {
                $obj = new Product($id);
                $obj->delete($id);
                Db::getInstance()->execute('
                    DELETE FROM `' . _DB_PREFIX_ . 'fme_events_products`
                    WHERE `id_product` = ' . (int) $id);
            }
        } elseif (trim(Tools::getValue('action')) == 'delete_this_image') {
            $id = (int) Tools::getValue('image_id');
            if (!empty($id)) {
                $imageFile = Events::getImageFile($id);
                $dirPath = _PS_IMG_DIR_ . $imageFile['image_file'];
                $this->delDirectoryRecord($dirPath);
                Db::getInstance()->execute('
                    DELETE FROM `' . _DB_PREFIX_ . 'fme_events_gallery`
                    WHERE `image_id` = ' . (int) $id);
                echo '1';
                exit;
            } else {
                echo '0';
                exit;
            }
        } elseif ((int) Tools::getValue('deleteImage') == 1) {
            $id = (int) Tools::getValue('event_id');
            $dirPath = _PS_IMG_DIR_ . 'events' . SEPARATOR . $id . SEPARATOR;
            $this->deleteDirectory($dirPath);
            Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'fme_events`
                SET `event_image` = "",
                `event_thumb_image` = ""
                WHERE `event_id` = ' . (int) $id);
        }
        if (Tools::isSubmit('event_status' . $this->table)) {
            $event = new Events((int) Tools::getvalue('event_id'));
            $event->toggleStatus();
        }
    }

    public function initProcess()
    {
        if (Tools::getValue('addevents')
            || Tools::getValue('updateevents')
            || Tools::isSubmit('submitBulkdeleteevents')) {
            $this->table = 'fme_events';
            $this->className = 'Events';
            $this->identifier = 'event_id';
            $this->deleted = false;
        }
        parent::initProcess();
    }

    protected function afterImageUpload()
    {
        $res = true;
        if (($event_id = (int) Tools::getValue('event_id'))
            && isset($_FILES)
            && count($_FILES)
            && file_exists(_PS_IMG_DIR_ . '/events/' . $event_id . '.jpg')) {
            $images_types = ImageType::getImagesTypes('events');
            foreach ($images_types as $image_type) {
                $res &= ImageManager::resize(
                    _PS_IMG_DIR_ . '/events/' . $event_id . '.jpg',
                    _PS_IMG_DIR_ . '/events/' . $event_id . '-' . version_compare(_PS_VERSION_,'9.0.0', '>=') ? stripslashes($imageType['name']) : Tools::stripslashes($image_type['name']) . '.jpg',
                    (int) $image_type['width'],
                    (int) $image_type['height']
                );
            }
        }
        if (!$res) {
            $this->errors[] = Tools::displayError('Unable to resize one or more pictures');
        }

        return $res;
    }

    protected function deleteDirectory($dirPath)
    {
        if (is_dir($dirPath)) {
            $objects = scandir($dirPath);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dirPath . '/' . $object) == 'dir') {
                        $this->delDirectoryRecord($dirPath . '/' . $object);
                    } else {
                        unlink($dirPath . '/' . $object);
                    }
                }
                reset($objects);
                rmdir($dirPath);
            }
        }
    }

    protected function delDirectoryRecord($filename)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function reArrayFiles(&$file_post)
    {
        $file_ary = [];
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i = 0; $i < $file_count; $i = $i + 1) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

    public function triggerAjaxProductsList()
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $aColumns = [
            'id_product',
            'id_image',
            'name',
            'price',
            'categories',
            'status',
        ];
        $filterColumns = [
            'p.id_product',
            'pl.name',
            'p.price',
        ];
        $sIndexColumn = 'id_product';
        $sTable = _DB_PREFIX_ . 'product';
        $iDisplayStart = (int) Tools::getValue('iDisplayStart');
        $i_display_length = (int) Tools::getValue('iDisplayLength');
        $sLimit = 'LIMIT 0, 10';
        if (!empty($iDisplayStart) && $iDisplayStart != '-1') {
            $sLimit = 'LIMIT ' . (int) $iDisplayStart . ', ' . (int) $i_display_length;
        }

        $sOrder = '';
        $iSortCol_0 = Tools::getValue('iSortCol_0');
        if (!empty($iSortCol_0)) {
            $iSortingCols = Tools::getValue('iSortCol_0');
            $sOrder = 'ORDER BY  ';
            for ($i = 0; $i < (int) $iSortingCols; $i = $i + 1) {
                $iSortCol_i = (int) Tools::getValue('iSortCol_' . $i);
                $bSortable_i = Tools::getValue('iSortCol_' . $iSortCol_i);
                if ($bSortable_i == 'true') {
                    $sSortDir_i = Tools::getValue('sSortDir_' . $i);
                    $sOrder .= $aColumns[$iSortCol_i] . ' ' . ($sSortDir_i === 'asc' ? 'asc' : 'desc') . ', ';
                }
            }

            $sOrder = substr_replace($sOrder, '', -2);
            if ($sOrder == 'ORDER BY') {
                $sOrder = '';
            }
        }

        $sWhere = '';
        $sSearch = Tools::getValue('sSearch');
        $filterColumns_count = count($filterColumns);
        if (!empty($sSearch) && $sSearch != '') {
            $sWhere = 'AND (';
            for ($i = 0; $i < $filterColumns_count; $i = $i + 1) {
                $bSearchable_i = Tools::getValue('bSearchable_' . $i);
                if (!empty($bSearchable_i) && $bSearchable_i == 'true') {
                    $sWhere .= $filterColumns[$i] . ' LIKE "%' . $sSearch . '%" OR ';
                }
            }

            $sWhere = substr_replace($sWhere, '', -3);
            $sWhere .= ')';
        }

        for ($i = 0; $i < $filterColumns_count; $i = $i + 1) {
            $bSearchable_i = Tools::getValue('bSearchable_' . $i);
            $sSearch_i = Tools::getValue('sSearch_' . $i);
            if (!empty($bSearchable_i) && $bSearchable_i == 'true' && $sSearch_i != '') {
                if ($sWhere == '') {
                    $sWhere = ' AND ';
                } else {
                    $sWhere .= ' AND ';
                }
                $sWhere .= $filterColumns[$i] . ' LIKE "%' . $sSearch_i . '%"';
            }
        }

        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');

        $sQuery = 'SELECT SQL_CALC_FOUND_ROWS image.id_image,pl.id_lang,p.id_product, IF(p.active=1,"Yes","No")
        as status, pl.name , GROUP_CONCAT(DISTINCT(cl.name) SEPARATOR ",") as
        categories, p.price, p.id_tax_rules_group, p.wholesale_price, p.reference,
        p.supplier_reference, p.id_supplier, p.id_manufacturer, p.upc, p.ecotax,
        p.weight, p.quantity, pl.description_short,     pl.description,
        pl.meta_title, pl.meta_keywords, pl.meta_description, pl.link_rewrite,
        pl.available_now, pl.available_later, p.available_for_order, p.date_add,
        p.show_price, p.online_only, p.condition, p.id_shop_default
    FROM ' . pSQL($sTable) . ' p
    LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (p.id_product = pl.id_product)
    LEFT JOIN ' . _DB_PREFIX_ . 'image image ON (p.id_product = image.id_product)
    LEFT JOIN ' . _DB_PREFIX_ . 'category_product cp ON (p.id_product = cp.id_product)
    LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cp.id_category = cl.id_category)
    LEFT JOIN ' . _DB_PREFIX_ . 'category c ON (cp.id_category = c.id_category)
    LEFT JOIN ' . _DB_PREFIX_ . 'product_tag pt ON (p.id_product = pt.id_product)
    WHERE pl.id_lang = ' . (int) $id_lang_default . '
    AND cl.id_lang = ' . (int) $id_lang_default . '
    AND p.id_shop_default = ' . (int) $id_shop . '
    AND c.id_shop_default = ' . (int) $id_shop . '
    ' . $sWhere . '
    GROUP BY p.id_product
    ' . $sOrder . '
    ' . $sLimit;
        $rResult = Db::getInstance()->executeS($sQuery);
        $sQuery = 'SELECT FOUND_ROWS() as ttl';
        $rResultFilterTotal = Db::getInstance()->executeS($sQuery);
        if (false === $rResultFilterTotal) {
            echo $this->errors[] = Tools::displayError('Bad SQL query');
        }
        $iFilteredTotal = $rResultFilterTotal[0]['ttl'];
        $sQuery = 'SELECT COUNT(`' . pSQL($sIndexColumn) . '`) as total FROM  ' . pSQL($sTable);
        $rResultTotal = Db::getInstance()->executeS($sQuery);
        $iTotal = $rResultTotal[0]['total'];
        $sEcho = Tools::getValue('sEcho');
        $output = [
            'sEcho' => (int) $sEcho,
            'iTotalRecords' => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData' => [],
        ];
        foreach ($rResult as $aRow) {
            $row = [];
            $row['DT_RowId'] = 'row_' . $aRow['id_product'];
            $row['DT_RowClass'] = 'grade' . $aRow['status'];
            $row['DT_CheckVal'] = 'pids_' . $aRow['id_product'];
            $cover = Product::getCover($aRow['id_product']);
            $image_url = '';
            if (!empty($cover) && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $image_url = Context::getContext()->link->getImageLink(
                    $aRow['link_rewrite'],
                    $cover['id_image'],
                    ImageType::getFormattedName('small')
                );
            } elseif (!empty($cover)) {
                $image_url = Context::getContext()->link->getImageLink(
                    $aRow['link_rewrite'],
                    $cover['id_image'],
                    $imageType = ImageType::getByName('small')
                );
            }
            $row['DT_IMGPath'] = $image_url;
            $aColumns_count = count($aColumns);
            for ($i = 0; $i < $aColumns_count; $i = $i + 1) {
                if ($aColumns[$i] == 'version') {
                    $row[] = ($aRow[$aColumns[$i]] == '0') ? '-' : $aRow[$aColumns[$i]];
                } elseif ($aColumns[$i] != ' ') {
                    $row[] = $aRow[$aColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        exit;
    }
}
