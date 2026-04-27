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
include_once dirname(__FILE__) . '/models/Gift.php';
include_once dirname(__FILE__) . '/models/GiftTemplates.php';
include_once dirname(__FILE__) . '/models/GiftCardTemp.php';
include_once dirname(__FILE__) . '/models/GiftCardVideoTemp.php';

// use PrestaShop\PrestaShop\Core\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class GiftCard extends Module
{
    public $msg = 0;

    public $translations = [];

    public $field_labels = [];

    public $tab_class = 'GiftCard';

    public $tab_module = 'giftcard';

    public $tpl_version = 'v1_6';

    public $tpl = '';

    public $html;

    public $author_address;

    protected $gcHooks = [
        'displayheader',
        'actionValidateOrder',
        'ModuleRoutes',
        'displayGiftCards',
        'displayBackOfficeHeader',
        'actionProductDelete',
        'ActionObjectDeleteAfter',
        'actionProductUpdate',
        'displayProductButtons',
        'displayMyAccountBlock',
        'displayCustomerAccount',
        'actionOrderStatusPostUpdate',
        'ActionOrderStatusUpdate',
        'ActionGiftCardDeleteFromCart',
        'displayProductListReviews',
        'actionAdminProductsListingFieldsModifier',
        'displayAdminOrder',
        'actionProductGridQueryBuilderModifier',
        // 'displayTop',
    ];

    public function __construct()
    {
        $this->name = 'giftcard';
        $this->tab = 'front_office_features';
        $this->version = '4.0.2';
        $this->author = 'FMM Modules';
        $this->bootstrap = true;
        $this->module_key = '26c0ea03bb9df50375ba49227d63e4d7';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';

        parent::__construct();

        if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->tpl_version = 'v1_7';
        }

        $this->displayName = $this->l('Gift Cards');
        $this->description = $this->l('This module allows you to create gift cards in your shop. Customers can order them and send as a gift to anyone.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

        $this->translations = [
            'video_paragraph' => $this->l('You recieved a video: '),
            'video_link_title' => $this->l('Watch Video'),
            'home' => $this->l('To Myself'),
            'sendsomeone' => $this->l('Send to a Friend'),
            'invalid_name' => $this->l('Inavlid recipient name'),
            'required_email' => $this->l('E-mail address required'),
            'invalid_email' => $this->l('Invalid e-mail address'),
            'invalid_message' => $this->l('Invalid Message content'),
            'gift_sending_failed' => $this->l('Gift card sending failed'),
            'gift_card_failure' => $this->l('Cannot sent gift card at this moment, something went wrong'),
            'specific_date' => $this->l('Sending Date'),
        ];
        $this->field_labels = [
            'type' => $this->l('Gift Type'),
            'reciptent' => $this->l('Name'),
            'email' => $this->l('Email'),
            'message' => $this->l('Message'),
            'specific_date' => $this->l('Sending Date'),
        ];
    }

    public function install()
    {
        parent::install();
        if (!$this->createGiftCategory()) {
            return false;
        }
        
        if (!$this->existsTab($this->tab_class)) {
            if (!$this->addTab()) {
                return false;
            }
        }

        if (!$this->installTopLink()) {
            return false;
        }
        
        Configuration::updateValue('GIFT_VIDEO_SIZE_LIMIT', 2);
        Configuration::updateValue('GIFT_VIDEO_EXPIRY_DAYS', 15);
        Configuration::updateValue('GIFT_EXPIRY_MAIL_TIME', 24);
        Configuration::updateValue($this->name . '_INSTALL_DATE', date('Y-m-d'));
        include dirname(__FILE__) . '/sql/install.php';
        $this->createGiftcardVideosFolder();
        $this->addtemplates();
         if (!$this->addtemplates()){
             return false;
         }
         $this->moveFiles();
        if ($this->registerHook($this->gcHooks)
            && Configuration::updateValue('GIFTCARD_VOUCHER_PREFIX', 'GiftCard_')
            && Configuration::updateValue('GIFT_CATEGORY_SHOW_MENU', 1)
            && Configuration::updateValue('GIFT_APPROVAL_STATUS', 2)
            && Configuration::updateValue('GIFTCARD_CRON_HOURS', 24)
            && Configuration::updateValue('GIFT_CARD_CUSTOMIZATION_ENABLED', 1)
            && Configuration::updateValue('GIFTCARD_CRON_KEY', Tools::passwdGen(32))
            && Configuration::updateValue('GIFTCARD_VIDEO_CRON_KEY', Tools::passwdGen(32))
            && Configuration::updateValue('GIFTCARD_EXPIRY_CRON_KEY', Tools::passwdGen(32))) {
            if (true === Tools::version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
                @copy(_PS_MODULE_DIR_ . 'giftcard/views/img/GiftCard.gif', _PS_MODULE_DIR_ . 'giftcard/GiftCard.gif');
            }

            return true;
        }

        return false;
    }

    private function createGiftcardVideosFolder()
    {
        $path = _PS_IMG_DIR_ . 'giftcard_videos/';

        if (!is_dir($path)) {
            if (@mkdir($path, 0777, true)) {
                return true;
            } else {
                return false;
            }
        }

        return true; // Directory already exists
    }

    public function uninstall()
    {
        if (!$this->removeTab()) {
            return false;
        }
        if (!$this->delTopLink()) {
            return false;
        }
        Configuration::deleteByName('GIFT_VIDEO_SIZE_LIMIT');
        Configuration::deleteByName('GIFT_VIDEO_EXPIRY_DAYS');
        Configuration::deleteByName('GIFT_EXPIRY_MAIL_TIME');
        if (Configuration::get($this->name . '_INSTALL_DATE')) {
            Configuration::deleteByName($this->name . '_INSTALL_DATE');
        }
        if (Configuration::get($this->name . '_FME_POPUP_REMINDER')) {
            Configuration::deleteByName($this->name . '_FME_POPUP_REMINDER');
        }
        include dirname(__FILE__) . '/sql/uninstall.php';

        if(!$this->delFiles()){
            return false;
        }
        if (parent::uninstall()
            && $this->unregisterHook('displayheader')
            && $this->unregisterHook('actionValidateOrder')
            && $this->unregisterHook('displayBackOfficeHeader')
            && $this->unregisterHook('actionProductDelete')
            && $this->unregisterHook('ActionObjectDeleteAfter')
            && $this->unregisterHook('actionProductUpdate')
            && $this->unregisterHook('displayMyAccountBlock')
            && $this->unregisterHook('displayCustomerAccount')
            && $this->unregisterHook('displayProductButtons')
            && $this->unregisterHook('actionOrderStatusPostUpdate')
            && $this->unregisterHook('ActionOrderStatusUpdate')
            && $this->unregisterHook('ActionGiftCardDeleteFromCart')
            && $this->unregisterHook('displayProductListReviews')
            && $this->removeGiftCategory()

            && Configuration::deleteByName('GIFTCARD_VOUCHER_PREFIX')
            && Configuration::deleteByName('GC_TOPLINK_ID')
            && Configuration::deleteByName('GIFT_CARD_CUSTOMIZATION_ENABLED')
            && Configuration::deleteByName('GIFTCARD_EXPIRY_CRON_KEY')
            && Configuration::deleteByName('GIFT_CATEGORY_SHOW_MENU')
            && Configuration::deleteByName('GIFTCARD_VIDEO_CRON_KEY')
            && Configuration::deleteByName('GIFT_APPROVAL_STATUS')
            && Configuration::deleteByName('GIFTCARD_CRON_HOURS')
            && Configuration::deleteByName('GIFTCARD_CRON_KEY')) {
            if (true === Tools::version_compare(_PS_VERSION_, '1.6.0.0', '<')
                && is_file(_PS_MODULE_DIR_ . 'giftcard/GiftCard.gif')) {
                @unlink(_PS_MODULE_DIR_ . 'giftcard/GiftCard.gif');
            }

            return true;
        }

        return false;
    }

    public function addTab()
    {
        $return = true;
        $tab = new Tab();
        $tab->id_parent = 0;
        $tab->module = $this->name;
        $tab->class_name = $this->tab_class;
        $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->displayName;
        if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $tab->icon = 'card_giftcard';
        }

        if ($tab->add()) {
            $subtab1 = new Tab();
            $subtab1->id_parent = $tab->id;
            $subtab1->module = $this->name;
            $subtab1->class_name = 'AdminGiftCardsCategory';
            $subtab1->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Gift Card Category');
            if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $subtab1->icon = 'settings';
            }
            $return &= $subtab1->add();

            $subtab2 = new Tab();
            $subtab2->id_parent = $tab->id;
            $subtab2->module = $this->name;
            $subtab2->class_name = 'AdminGiftCards';
            $subtab2->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Gift Cards');
            if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $subtab2->icon = 'redeem';
            }
            $return &= $subtab2->add();

            $subtab3 = new Tab();
            $subtab3->id_parent = $tab->id;
            $subtab3->module = $this->name;
            $subtab3->class_name = 'AdminGiftCardsImageTemplate';
            $subtab3->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Gift Cards Image Template');
            if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $subtab3->icon = 'redeem';
            }
            $return &= $subtab3->add();

            $subtab4 = new Tab();
            $subtab4->id_parent = $tab->id;
            $subtab4->module = $this->name;
            $subtab4->class_name = 'AdminGiftTemplates';
            $subtab4->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Email Templates');
            if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $subtab4->icon = 'settings';
            }
            $return &= $subtab4->add();

            $subtab5 = new Tab();
            $subtab5->id_parent = $tab->id;
            $subtab5->module = $this->name;
            $subtab5->class_name = 'AdminOrderedGiftcards';
            $subtab5->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Ordered Giftcards');
            if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $subtab5->icon = 'add_shopping_cart';
            }
            $return &= $subtab5->add();

            $subtab6 = new Tab();
            $subtab6->id_parent = $tab->id;
            $subtab6->module = $this->name;
            $subtab6->class_name = 'AdminGiftSettings';
            $subtab6->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Settings');
            if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $subtab6->icon = 'settings';
            }
            $return &= $subtab6->add();
        }

        return $return;
    }

    public function removeTab()
    {
        $result = true;
        $tabs = [
            'AdminGiftCardsCategory',
            'AdminGiftSettings',
            'AdminOrderedGiftcards',
            'AdminGiftTemplates',
            'AdminGiftCards',
            'AdminGiftCardsImageTemplate',
            $this->tab_class
        ];

        foreach ($tabs as $tabClasss) {
            if (Validate::isLoadedObject($tab = Tab::getInstanceFromClassName($tabClasss))) {
                $result &= $tab->delete();
            }
        }

        return $result;
    }

    private function existsTab($tab_class)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_tab AS id
            FROM `' . _DB_PREFIX_ . 'tab` WHERE LOWER(`class_name`) = \'' . pSQL($tab_class) . '\'');
        if (count($result) == 0) {
            return false;
        }

        return true;
    }

    public function installTopLink()
    {
        $id_shop = (int) $this->context->shop->id;
        $id_shop_group = (int) $this->context->shop->id_shop_group;
        $id_lang = $this->context->language->id;
        $shops = Shop::getShops();
        $languages = Language::getLanguages(false);
        $id_category = (int) Configuration::get('GIFT_CARD_CATEGORY');
        $category = new Category($id_category, $this->context->language->id);
        $link_giftcard = $this->context->link->getModuleLink(
            $this->name,
            'mygiftcards',
            [],
            true,
            $id_lang,
            $id_shop
        );

        if (!empty($shops)) {
            // for multishop-multilang
            foreach ($shops as $shop) {
                Db::getInstance()->insert('linksmenutop', ['id_shop' => (int) $shop['id_shop']]);
                $id_linksmenutop = Db::getInstance()->Insert_ID();
                if ($languages) {
                    foreach ($languages as $language) {
                        $link_giftcard = $this->context->link->getModuleLink(
                            $this->name,
                            'mygiftcards',
                            [],
                            true,
                            $language['id_lang'],
                            $shop['id_shop']
                        );
                        Db::getInstance()->insert('linksmenutop_lang', [
                            'id_linksmenutop' => (int) $id_linksmenutop,
                            'id_lang' => (int) $language['id_lang'],
                            'id_shop' => (int) $shop['id_shop'],
                            'label' => pSQL($category->name),
                            'link' => pSQL($link_giftcard),
                        ]);
                    }
                    Configuration::updateValue('GC_TOPLINK_ID', $id_linksmenutop, false, $shop['id_shop_group'], $shop['id_shop']);
                    $current_links = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $shop['id_shop_group'], $shop['id_shop']);
                    $string_links = $current_links . ',CAT' . Configuration::get('GIFT_CARD_CATEGORY');
                    Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $string_links, false, $shop['id_shop_group'], $shop['id_shop']);
                } else {
                    Db::getInstance()->insert('linksmenutop_lang', [
                        'id_linksmenutop' => (int) $id_linksmenutop,
                        'id_lang' => (int) $id_lang,
                        'id_shop' => (int) $id_shop,
                        'label' => pSQL($category->name),
                        'link' => pSQL($link_giftcard),
                    ]);
                    Configuration::updateValue('GC_TOPLINK_ID', $id_linksmenutop, false, $id_shop_group, $id_shop);
                    $current_links = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $id_shop_group, $id_shop);
                    $string_links = $current_links . ',CAT' . Configuration::get('GIFT_CARD_CATEGORY');
                    Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $string_links, false, $id_shop_group, $id_shop);
                }
            }
        } else {
            Db::getInstance()->insert('linksmenutop', ['id_shop' => (int) $this->context->shop->id]);
            $id_linksmenutop = Db::getInstance()->Insert_ID();
            if ($languages) {
                foreach ($languages as $language) {
                    $link_giftcard = $this->context->link->getModuleLink(
                        $this->name,
                        'mygiftcards',
                        [],
                        true,
                        $language['id_lang'],
                        $this->context->shop->id
                    );
                    Db::getInstance()->insert(
                        'linksmenutop_lang',
                        [
                            'id_linksmenutop' => (int) $id_linksmenutop,
                            'id_lang' => (int) $language['id_lang'],
                            'id_shop' => (int) $this->context->shop->id,
                            'label' => pSQL($category->name),
                            'link' => pSQL($link_giftcard)
                        ]
                    );
                }
                Configuration::updateValue('GC_TOPLINK_ID', $id_linksmenutop, false, $id_shop_group, $id_shop);
                $current_links = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $id_shop_group, $id_shop);
                $string_links = $current_links . ',CAT' . Configuration::get('GIFT_CARD_CATEGORY');
                Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $string_links, false, $id_shop_group, $id_shop);
            } else {
                $link_giftcard = $this->context->link->getModuleLink('giftcard', 'mygiftcards', [], true, $id_lang, $id_shop);
                Db::getInstance()->insert(
                    'linksmenutop_lang',
                    [
                        'id_linksmenutop' => (int) $id_linksmenutop,
                        'id_lang' => (int) $id_lang,
                        'id_shop' => (int) $id_shop,
                        'label' => pSQL($category->name),
                        'link' => pSQL($link_giftcard)
                    ]
                );
                Configuration::updateValue('GC_TOPLINK_ID', $id_linksmenutop, false, $id_shop_group, $id_shop);
                $current_links = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $id_shop_group, $id_shop);
                $string_links = $current_links . ',CAT' . Configuration::get('GIFT_CARD_CATEGORY');
                Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $string_links, false, $id_shop_group, $id_shop);
            }
        }

        return true;
    }

    private function delTopLink()
    {
        $GC_TOPLINK_ID = Configuration::get('GC_TOPLINK_ID', null, $this->context->shop->id_shop_group, $this->context->shop->id);
        $current_links = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $this->context->shop->id_shop_group, $this->context->shop->id);
        $string_links = str_replace(',CAT' . Configuration::get('GIFT_CARD_CATEGORY'), '', $current_links);
        Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $string_links, false, $this->context->shop->id_shop_group, $this->context->shop->id);

        Db::getInstance()->delete('linksmenutop', 'id_linksmenutop = ' . (int) $GC_TOPLINK_ID);
        Db::getInstance()->delete('linksmenutop_lang', 'id_linksmenutop = ' . (int) $GC_TOPLINK_ID);

        return true;
    }

    public function addtemplates()
    {
        $zipFile = _PS_MODULE_DIR_ . 'giftcard/upgrade/assets/GiftCardTemplates.zip';
        $extractTo = _PS_IMG_DIR_ . 'giftcard_templates/';
        if (!is_dir($extractTo)) {
            mkdir($extractTo, 0755, true);
        }
        $htaccessContent = '
        # Apache 2.4
        <IfModule mod_authz_core.c>
            Require all granted
        </IfModule>

        # Apache 2.2
        <IfModule !mod_authz_core.c>
            Order allow,deny
            Allow from all
        </IfModule>

        RewriteEngine On
        RewriteRule ^giftcard/(.*)$ - [L]
        ';

        $htaccessFile = $extractTo . '.htaccess';

        if (!file_put_contents($htaccessFile, $htaccessContent)) {
            return false;
        }


        if (file_exists($zipFile)) {
            $zip = new ZipArchive();
            if ($zip->open($zipFile) === true) {
                $zip->extractTo($extractTo);
                $zip->close();
            }
            //$zip = new ZipArchive();
            // if ($zip->open($zipFile) === true) {
            //     for ($i = 0; $i < $zip->numFiles; $i++) {
            //         $entry = $zip->getNameIndex($i);

            //         // Skip directories
            //         if (substr($entry, -1) === '/') {
            //             continue;
            //         }

            //         $parts = explode('/', $entry);
            //         if (count($parts) > 1) {
            //             array_shift($parts);
            //         }
            //         $relativePath = implode('/', $parts);
            //         $targetPath = $extractTo . $relativePath;

            //         $targetDir = dirname($targetPath);
            //         if (!is_dir($targetDir)) {
            //             mkdir($targetDir, 0755, true);
            //         }

            //         copy("zip://{$zipFile}#{$entry}", $targetPath);
            //     }
            //     $zip->close();
            // }
        }

        $files = scandir($extractTo);
        $oldFilePath = $extractTo . 'GiftCardTemplates/';
        foreach ($files as $file) {
            if($file == 'GiftCardTemplates' && is_dir($extractTo)){
                $templatesFiles = array_filter(scandir($oldFilePath), function ($template) {
                    return !in_array($template, ['.', '..']) && 
                        in_array(pathinfo($template, PATHINFO_EXTENSION), ['jpg', 'png', 'jpeg', 'gif', 'svg']);
                });
                foreach ($templatesFiles as $template) {
                    if (in_array(pathinfo($template, PATHINFO_EXTENSION), ['jpg', 'png', 'jpeg', 'gif', 'svg'])) {
                        
                        $query = 'INSERT INTO ' . _DB_PREFIX_ . 'giftcard_image_template 
                            (`gc_image`, `price`, `discount_code`, `bg_color`, `active`) 
                            VALUES ("", "100", "xxxxxxxxxx", "#8bcdd1", 1)';
                        Db::getInstance()->execute($query);
                        $insertedId = Db::getInstance()->Insert_ID();

                        $filename = 'giftcard_template_' . (int) $insertedId . '.png';
                        $newFilePath = $extractTo . $filename;
                        if (rename($oldFilePath. $template, $newFilePath)) {
                            $updateQuery = 'UPDATE ' . _DB_PREFIX_ . 'giftcard_image_template 
                                    SET gc_image = "' . pSQL($filename) . '" 
                                    WHERE id_giftcard_image_template = ' . (int) $insertedId;
                            Db::getInstance()->execute($updateQuery);
                        }

                        $languages = Language::getLanguages(false);
                        foreach ($languages as $lang) {
                            $id_lang = (int) $lang['id_lang'];

                            $template_text = 'Prestashop';

                            $queryLang = 'INSERT INTO ' . _DB_PREFIX_ . 'giftcard_image_template_lang 
                                (`id_giftcard_image_template`, `id_lang`, `name`, `template_text`) 
                                VALUES (' . (int) $insertedId . ', ' . $id_lang . ', 
                                "' . pSQL($filename) . '", "' . pSQL($template_text) . '")';
                            
                            Db::getInstance()->execute($queryLang);
                        }
                    }
                }
                if(empty(array_filter(scandir($oldFilePath), function ($template) {
                    return !in_array($template, ['.', '..']) && 
                        in_array(pathinfo($template, PATHINFO_EXTENSION), ['jpg', 'png', 'jpeg', 'gif', 'svg']);
                }))){
                    if (is_dir($oldFilePath)) {
                        $files = scandir($oldFilePath);
                        foreach ($files as $file) {
                            if ($file !== '.' && $file !== '..') {
                                $filePath = $oldFilePath . $file;
                                if (is_file($filePath)) {
                                    unlink($filePath);
                                }
                            }
                        }
                        rmdir($oldFilePath);
                    }
                }
            }
        }
        return true;
    }
     public function moveFiles()
    {
        if (true === Tools::version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            if(!is_dir(_PS_OVERRIDE_DIR_ . 'controllers')) {
                @mkdir(_PS_OVERRIDE_DIR_ . 'controllers');
            } 
            if (!is_dir(_PS_OVERRIDE_DIR_ . 'controllers/front')) {
                @mkdir(_PS_OVERRIDE_DIR_ . 'controllers/front');
            }
        }
        if (Tools::version_compare(_PS_VERSION_, '9.0.0', '>=') == true) {
            Tools::copy(_PS_MODULE_DIR_ . 'giftcard/includes/version_9/CartController.php', _PS_OVERRIDE_DIR_ . 'controllers/front/CartController.php');
            if (file_exists(_PS_CACHE_DIR_ . 'class_index.php')) {
                rename(_PS_CACHE_DIR_ . 'class_index.php', _PS_CACHE_DIR_ . 'class_index' . rand(pow(10, 3 - 1), pow(10, 3) - 1) . '.php');
            }
        } else {
            Tools::copy(_PS_MODULE_DIR_ . 'giftcard/includes/all_versions/CartController.php', _PS_OVERRIDE_DIR_ . 'controllers/front/CartController.php');
            if (file_exists(_PS_CACHE_DIR_ . 'class_index.php')) {
                rename(_PS_CACHE_DIR_ . 'class_index.php', _PS_CACHE_DIR_ . 'class_index' . rand(pow(10, 3 - 1), pow(10, 3) - 1) . '.php');
            }
        }

        return true;
    }

    public function delFiles()
    {
        unlink(_PS_OVERRIDE_DIR_ . 'controllers/front/CartController.php');
        if (file_exists(_PS_CACHE_DIR_ . 'class_index.php')) {
            rename(_PS_CACHE_DIR_ . 'class_index.php', _PS_CACHE_DIR_ . 'class_index' . rand(pow(10, 3 - 1), pow(10, 3) - 1) . '.php');
        }

        return true;
    }
    public function getContent()
    {
        if(!$this->registerHook('actionProductGridQueryBuilderModifier')){
            $this->registerHook('actionProductGridQueryBuilderModifier');
        }
        if (true === (bool) Configuration::get('PS_DISABLE_OVERRIDES')) {
            $this->context->controller->warnings[] = $this->l('Your overrides are disabled. Please enable overrides.');
        }

        $this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
        $current_index = $this->context->link->getAdminLink('AdminModules', false);
        $current_token = Tools::getAdminTokenLite('AdminModules');
        $action_url = $current_index . '&configure=' . $this->name . '&token=' . $current_token . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        if (Tools::isSubmit('updateConfiguration')) {
            $GIFT_VIDEO_UPLOAD_ENABLED = Tools::getValue('GIFT_VIDEO_UPLOAD_ENABLED');
            $GIFT_CARD_CUSTOMIZATION_ENABLED = Tools::getValue('GIFT_CARD_CUSTOMIZATION_ENABLED');
            $GIFT_ALERT_EXPIRED = Tools::getValue('GIFT_ALERT_EXPIRED');
            $GIFT_EXPIRY_MAIL_TIME = Tools::getValue('GIFT_EXPIRY_MAIL_TIME');
            $GIFT_VIDEO_SIZE_LIMIT = Tools::getValue('GIFT_VIDEO_SIZE_LIMIT');
            $GIFT_VIDEO_EXPIRY_DAYS = Tools::getValue('GIFT_VIDEO_EXPIRY_DAYS');
            if ($GIFT_ALERT_EXPIRED && empty($GIFT_EXPIRY_MAIL_TIME)) {
                $GIFT_EXPIRY_MAIL_TIME = 24;
            }
            if ($GIFT_VIDEO_UPLOAD_ENABLED && empty($GIFT_VIDEO_EXPIRY_DAYS)) {
                $GIFT_VIDEO_EXPIRY_DAYS = 15;
            }
            if (Tools::getValue('GIFTCARD_VOUCHER_PREFIX') && !Validate::isTablePrefix(Tools::getValue('GIFTCARD_VOUCHER_PREFIX'))) {
                $this->context->controller->errors[] = $this->l('Invalid gift voucher code prefix.');
            } elseif (!Tools::getValue('GIFTCARD_CRON_HOURS') || !Validate::isUnsignedInt(Tools::getValue('GIFTCARD_CRON_HOURS'))) {
                $this->context->controller->errors[] = $this->l('Invalid hour(s) value.');
            } elseif ($GIFT_ALERT_EXPIRED && !Validate::isUnsignedInt($GIFT_EXPIRY_MAIL_TIME)) {
                $this->context->controller->errors[] = $this->l('Invalid expiry hour(s) value.');
            } elseif ($GIFT_VIDEO_EXPIRY_DAYS && !Validate::isUnsignedInt($GIFT_VIDEO_EXPIRY_DAYS)) {
                $this->context->controller->errors[] = $this->l('Invalid expiry days value.');
            } elseif ($GIFT_VIDEO_EXPIRY_DAYS && !Validate::isUnsignedInt($GIFT_VIDEO_SIZE_LIMIT)) {
                $this->context->controller->errors[] = $this->l('Invalid size value limit.');
            } else {
                $approval_states = (Tools::getValue('approval_states')) ? implode(',', Tools::getValue('approval_states')) : '';
                $social_share_options = (Tools::getValue('GIFTCARD_SHARE')) ? implode(',', Tools::getValue('GIFTCARD_SHARE')) : '';
                Configuration::updateValue('GIFT_VIDEO_UPLOAD_ENABLED', pSQL($GIFT_VIDEO_UPLOAD_ENABLED));
                Configuration::updateValue('GIFT_CARD_CUSTOMIZATION_ENABLED', $GIFT_CARD_CUSTOMIZATION_ENABLED);
                Configuration::updateValue('GIFT_VIDEO_SIZE_LIMIT', pSQL($GIFT_VIDEO_SIZE_LIMIT));
                Configuration::updateValue('GIFT_VIDEO_EXPIRY_DAYS', pSQL($GIFT_VIDEO_EXPIRY_DAYS));
                Configuration::updateValue('GIFT_ALERT_EXPIRED', pSQL(Tools::getValue('GIFT_ALERT_EXPIRED')));
                Configuration::updateValue('GIFT_EXPIRY_MAIL_TIME', pSQL($GIFT_EXPIRY_MAIL_TIME));
                Configuration::updateValue('GIFT_APPROVAL_STATUS', $approval_states);
                Configuration::updateValue('GIFTCARD_SHARE', $social_share_options);
                Configuration::updateValue('GIFTCARD_VOUCHER_PREFIX', pSQL(Tools::getValue('GIFTCARD_VOUCHER_PREFIX')));
                Configuration::updateValue('GIFTCARD_CRON_HOURS', (int) Tools::getValue('GIFTCARD_CRON_HOURS'));
                if (!Configuration::get('GIFTCARD_CRON_KEY')) {
                    Configuration::updateValue('GIFTCARD_CRON_KEY', Tools::passwdGen(32));
                }
                $this->context->controller->confirmations[] = $this->l('Settings successfully updated');
            }
        }

        $approval_states = (Configuration::get('GIFT_APPROVAL_STATUS') ? explode(',', Configuration::get('GIFT_APPROVAL_STATUS')) : '');
        $social_share_options = (Configuration::get('GIFTCARD_SHARE') ? explode(',', Configuration::get('GIFTCARD_SHARE')) : []);
        $this->context->smarty->assign([
            'states' => OrderState::getOrderStates($this->context->employee->id_lang),
            'ps_version' => _PS_VERSION_,
            'approval_states' => $approval_states,
            'GIFTCARD_SHARE' => $social_share_options,
            'action_url' => $action_url,
            'GIFT_VIDEO_UPLOAD_ENABLED' => Configuration::get('GIFT_VIDEO_UPLOAD_ENABLED'),
            'GIFT_CARD_CUSTOMIZATION_ENABLED' => Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED'),
            'GIFT_VIDEO_SIZE_LIMIT' => Configuration::get('GIFT_VIDEO_SIZE_LIMIT'),
            'GIFT_VIDEO_EXPIRY_DAYS' => Configuration::get('GIFT_VIDEO_EXPIRY_DAYS'),
            'GIFTCARD_VOUCHER_PREFIX' => Configuration::get('GIFTCARD_VOUCHER_PREFIX'),
            'GIFT_ALERT_EXPIRED' => Configuration::get('GIFT_ALERT_EXPIRED'),
            'GIFT_EXPIRY_MAIL_TIME' => Configuration::get('GIFT_EXPIRY_MAIL_TIME'),
            'GIFTCARD_CRON_HOURS' => Configuration::get('GIFTCARD_CRON_HOURS'),
            'gifts_controller' => $this->context->link->getModuleLink($this->name, 'mygiftcards', [], true),
            'gifts_hook' => "{hook h='displayGiftCards'}",
            'giftcard_cron' => Context::getContext()->link->getModuleLink(
                'giftcard',
                'cron',
                [
                    'action' => 'flush_carts',
                    'giftcard_cron_key' => Configuration::get('GIFTCARD_CRON_KEY'),
                ],
                true
            ),
            'giftcard_expiry_cron' => Context::getContext()->link->getModuleLink(
                'giftcard',
                'expiry',
                [
                    'action' => 'send_notification',
                    'giftcard_expiry_cron_key' => Configuration::get('GIFTCARD_EXPIRY_CRON_KEY'),
                ],
                true
            ),
            'giftcard_video_cron' => Context::getContext()->link->getModuleLink(
                'giftcard',
                'video',
                [
                    'action' => 'delete_video',
                    'giftcard_video_cron' => Configuration::get('GIFTCARD_VIDEO_CRON_KEY'),
                ],
                true
            ),
            'giftcard_sendtosomeone' => $this->context->link->getModuleLink(
                $this->name,
                'cron',
                [
                    'action' => 'sendtosomeone_later',
                    'giftcard_cron_key' => Configuration::get('GIFTCARD_CRON_KEY'),
                ],
                true
            ),
        ]);
        $this->context->smarty->assign([
            'edit_category_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsCategory'),
            'add_new_giftcard' => $this->context->link->getAdminLink('AdminGiftCards'),
            'add_img_template_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsImageTemplate'),
            'giftcard_settings' => $this->context->link->getAdminLink('AdminGiftSettings'),
            'gift_templates' => $this->context->link->getAdminLink('AdminGiftTemplates'),
            'ordered_giftcards' => $this->context->link->getAdminLink('AdminOrderedGiftcards'),
        ]);
        $installDate = Configuration::get($this->name . '_INSTALL_DATE');
        $reminderEnabled = Configuration::get($this->name . '_FME_POPUP_REMINDER');
        if ($installDate || Tools::getValue('force_popper')) {
            $installTimestamp = strtotime($installDate);
            $currentTimestamp = time();
            $daysSinceInstall = ($currentTimestamp - $installTimestamp) / (60 * 60 * 24);
            $popupInterval = $reminderEnabled ? 30 : 7;
            if (($daysSinceInstall >= $popupInterval) || Tools::getValue('force_popper')) {
                $current_index = $this->context->link->getAdminLink('AdminModules', false);
                $current_token = Tools::getAdminTokenLite('AdminModules');
                $admin_url = $current_index . '&configure=' . $this->name . '&token=' . $current_token . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

                $this->context->smarty->assign(['admin_url' => $admin_url,
                    'showpopup' => true]);
                $this->html .= $this->display(__FILE__, 'views/templates/admin/fme_popup.tpl');
            }
        }

        return $this->html . $this->display($this->_path, 'views/templates/admin/config.tpl');
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (true === Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }
        $this->context->controller->addCSS($this->_path . 'views/css/back.css');

        $this->context->smarty->assign([
            'edit_category_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsCategory'),
            'add_new_giftcard' => $this->context->link->getAdminLink('AdminGiftCards'),
            'add_img_template_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsImageTemplate'),
            'giftcard_settings' => $this->context->link->getAdminLink('AdminGiftSettings'),
            'gift_templates' => $this->context->link->getAdminLink('AdminGiftTemplates'),
            'ordered_giftcards' => $this->context->link->getAdminLink('AdminOrderedGiftcards'),
            'ajaxGc' => $this->context->link->getModuleLink(
                $this->name,
                'ajax',
                ['ajax' => 1],
                true,
                $this->context->cookie->id_lang,
                $this->context->shop->id
            ),
        ]);
    }

    public function hookModuleRoutes()
    {
        return [
            'module-giftcard-mygiftcards' => [
                'controller' => 'mygiftcards',
                'rule' => 'gift-cards',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'giftcard',
                ],
            ],
            'module-giftcard-ajax' => [
                'controller' => 'ajax',
                'rule' => 'process-giftcards',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'giftcard',
                ],
            ],
            'module-giftcard-cron' => [
                'controller' => 'cron',
                'rule' => 'giftcard-cron',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'giftcard',
                ],
            ],
        ];
    }

    public function hookDisplayHeader()
    {
        $id_product = (int) Tools::getValue('id_product');
        $controller = Dispatcher::getInstance()->getController();
        $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $this->context->smarty->assign([
            'base_dir' => Tools::getShopDomainSsl(true) . __PS_BASE_URI__,
            'base_dir_ssl' => Tools::getShopDomainSsl(true) . __PS_BASE_URI__,
            'force_ssl' => $force_ssl,
        ]);

        $ajaxGc = $this->context->link->getModuleLink(
            $this->name,
            'ajax',
            ['ajax' => 1],
            true,
            $this->context->cookie->id_lang,
            $this->context->shop->id
        );

        if ($controller === 'category' && Tools::getValue('id_category') && Tools::getValue('id_category') == Configuration::get('GIFT_CARD_CATEGORY')) {
            Tools::redirect($this->context->link->getModuleLink(
                $this->name,
                'mygiftcards',
                [],
                true,
                $this->context->cookie->id_lang,
                $this->context->shop->id
            ));
        }

        $logo = '';
        $image = '';
        $productName = '';
        $id_lang = (int) Context::getContext()->language->id;
        if ($controller === 'product' && $id_product && Gift::isExists($id_product)) {
            if (Validate::isLoadedObject($product = new Product((int) $id_product, false, $id_lang))) {
                $id_image = Gift::getId_image($product->id);
                $type = (true === Tools::version_compare(_PS_VERSION_, '1.7', '<')) ? ImageType::getFormatedName('large') : ImageType::getFormattedName('large');  // used for older version compatibility
                $this->context->smarty->assign(['ps_img' => $this->context->link->getImageLink($product->link_rewrite, $id_image, $type)]);
                $image = $this->display(__FILE__, 'views/templates/hook/img.tpl');
                $productName = $product->name;
            }
            if (false !== Configuration::get('PS_LOGO_MAIL')
                && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $this->context->shop->id))
            ) {
                $logo = __PS_BASE_URI__ . '/img/' . Configuration::get('PS_LOGO_MAIL', null, null, $this->context->shop->id);
            } elseif (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $this->context->shop->id))) {
                $logo = __PS_BASE_URI__ . '/img/' . Configuration::get('PS_LOGO', null, null, $this->context->shop->id);
            }
            $this->context->smarty->assign([
                'customizationFields' => [],
            ]);
        }

        $tempVars = [
            '{value}' => 'XXX',
            '{shop_logo}' => $logo,
            '{gift_image}' => $image,
            '{vcode}' => 'XXXXX-XXXXX',
            '{giftcard_name}' => $productName,
            '{expire_date}' => sprintf('X %s', $this->l('days')),
            '{shop_name}' => Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $this->context->shop->id)),
            '{shop_url}' => $this->context->link->getPageLink('index', true, $id_lang, null, false, $this->context->shop->id),
            '{sender}' => (!empty($this->context->customer)) ? sprintf('%s %s', $this->context->customer->firstname, $this->context->customer->lastname) : $this->l('Your Name'),
            '{rec_name}' => '',
            '{message}' => '',
            '{quantity}' => 1,
        ];

         $translations = [
            'messengerCopy' => $this->trans('Link copied! You will be redirected to Messenger. Please paste the link to share GiftCard product.', [], 'Modules.giftcard'),
            'Fail' => $this->trans('Failed to copy the link.', [], 'Modules.giftcard'),
            'facebookCopy' => $this->trans('Link copied! You will be redirected to Facebook. Please paste the link to share GiftCard product.', [], 'Modules.giftcard'),
            'skypeCopy' => $this->trans('Link copied! You will be redirected to Skype. Please paste the link to share GiftCard Product.', [], 'Modules.giftcard'),
            'twitterCopy' => $this->trans('Link copied! You will be redirected to Twitter. Please paste the link to share GiftCard product.', [], 'Modules.giftcard'),
            'linkedinCopy' => $this->trans('Link copied! You will be redirected to Linkedin. Please paste the link to share GiftCard product.', [], 'Modules.giftcard'),
            'envelopeCopy' => $this->trans('Link copied! You will be redirected to Gmail. Please paste the link to share GiftCard product.', [], 'Modules.giftcard'),
            'whatsappCopy' => $this->trans('Link copied! You will be redirected to Whatsapp. Please paste the link to share GiftCard product.', [], 'Modules.giftcard'),


        ];

        $this->context->smarty->assign(['ps_version' => _PS_VERSION_]);
        Media::addJsDef([
            'ajax_gc' => $ajaxGc,
            'template_vars' => $tempVars,
            'required_label' => $this->l('is required'),
            'preview_label' => $this->l('Template Preview'),
            'video_paragraph' => $this->translations['video_paragraph'],
            'video_link_title' => $this->translations['video_link_title'],
            'giftType' => Gift::getGiftCardType($id_product),
            'front_controller' => $this->context->link->getModuleLink($this->name, 'mygiftcards', ['ajax' => true], true),
            'temp_controller' => $this->context->link->getModuleLink($this->name, 'tempvideo', [], true),
            'dynamicTranslations' => $translations,
            'select_template_label' => $this->l('Select a Template'),
            'dateOptions' => [
                'noCalendar' => false,
                'minDate' => 'today',
                'dateFormat' => 'Y-m-d',
                'monthSelectorType' => 'static',
                'locale' => $this->context->language->iso_code,
            ],
        ]);

        $this->context->controller->addCSS([
            $this->_path . 'views/css/gift_card.css',
            $this->_path . 'views/css/front.css',
            $this->_path . 'views/css/gift_products.css',
            $this->_path . 'views/css/flatpickr/flatpickr.css',
            $this->_path . 'views/css/flatpickr/material_blue.css',
            $this->_path . 'views/css/iziModal.min.css',
        ]);

        // Charger flatpickr seulement sur les pages où il est nécessaire
        $controller = Dispatcher::getInstance()->getController();
        $needsFlatpickr = false;

        // Pages qui nécessitent flatpickr
        if ($controller === 'product' && $id_product && Gift::isExists($id_product)) {
            $needsFlatpickr = true;
        } elseif ($controller === 'mygiftcards') {
            $needsFlatpickr = true;
        } elseif (Tools::getValue('module') === $this->name) {
            $needsFlatpickr = true;
        }

        if ($needsFlatpickr) {
            $this->context->controller->addJs([
                $this->_path . 'views/js/flatpickr/flatpickr.js',
                $this->_path . 'views/js/flatpickr//l10n/' . $this->context->language->iso_code . '.js',
                $this->_path . 'views/js/specific_date.js',
                $this->_path . 'views/js/iziModal.min.js',
            ]);
        } else {
            // Charger seulement les JS essentiels
            $this->context->controller->addJs([
                $this->_path . 'views/js/iziModal.min.js',
            ]);
        }
        if ($controller == 'mygiftcards') {
            $this->context->controller->addJs($this->_path . 'views/js/front.js');
        }

        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->context->controller->registerJavascript(
                'modules-giftcard',
                'modules/' . $this->name . '/views/js/gift_script.js',
                ['position' => 'bottom']
            );
        }
    }

    public function hookDisplayCustomerAccount()
    {
        $this->context->smarty->assign('ps_version', _PS_VERSION_);

        // return $this->display(__FILE__, $this->tpl_version . '/my-account.tpl');
        return $this->display(__FILE__, 'views/templates/hook/' . $this->tpl_version . '/my-account.tpl');
    }

    public function hookDisplayGiftCards()
    {
        if (true == Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->tpl = 'module:' . $this->name . '/views/templates/front/v1_7/giftcards.tpl';
        } else {
            $this->tpl = 'v1_6/giftcards.tpl';
        }

        $productTags = [];
        $giftProducts = [];
        $idLang = $this->context->cookie->id_lang;
        $giftCategory = new Category(Configuration::get('GIFT_CARD_CATEGORY'));
        $products = $giftCategory->getProducts($idLang, 1, 100);
        if (!empty($products)) {
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
        if (!empty($productTags)) {
            foreach ($productTags as $ptags) {
                if (!empty($ptags)) {
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

        $controller = Dispatcher::getInstance()->getController();
        if($controller === 'product') {
            return $this->display(__FILE__, '/views/templates/hook/giftcards_hook_product.tpl');
        } else {
            return $this->display(__FILE__, '/views/templates/front/v1_7/giftcards_hook.tpl');
        }
        return $this->display(__FILE__, '/views/templates/front/v1_7/giftcards_hook.tpl');
    }

    public function hookDisplayMyAccountBlock($params)
    {
        return $this->hookDisplayCustomerAccount();
    }

    public function hookDisplayProductListReviews($params)
    {
        if (!empty($params['product']['id_product'])) {
            if (true === Tools::version_compare(_PS_VERSION_, '1.7', '<') && Gift::isExists($params['product']['id_product'])) {
                $this->context->smarty->assign('id_gift_product', (int) $params['product']['id_product']);

                return $this->display(__FILE__, 'views/templates/hook/v1_6/giftcard_script.tpl');
            }
        }
    }

    public function hookdisplayProductButtons()
    {
        $action = '';
        $card_type = '';
        $preselected_price = '';
        $product = [];
        if ($id_product = (int) Tools::getValue('id_product')) {
            $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
        }
        if (Tools::getIsset('action') && Tools::getValue('action') == 'getGiftPrice') {
            $action = 'getGiftPrice';
            $card_type = (string) Tools::getValue('card_type');
            $preselected_price = (float) Tools::getValue('current_price');
        }

        $vals = Gift::getCardValue($product->id);
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $this->context->smarty->assign([
                'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
                'base_dir_ssl' => Tools::getShopDomainSsl(true) . __PS_BASE_URI__,
                'force_ssl' => $force_ssl,
            ]
            );
        }

        if (!empty($vals) && Gift::isExists($id_product)) {
            $price_display = (int) Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer);
            $priceses = explode(',', $vals['card_value']);
            $prices_tax_incl = [];
            $prices_tax_excl = [];

            foreach ($priceses as $price) {
                if ((!$price_display || $price_display == 2) && (int) $product->id_tax_rules_group > 0) {
                    $prices_tax_incl[] = Tools::convertPriceFull($this->calculateGiftPrice($price, $id_product, true), null, $this->context->currency);
                } else {
                    $prices_tax_excl[] = Tools::convertPriceFull($this->calculateGiftPrice($price, $id_product, false), null, $this->context->currency);
                }
            }
            $_value = ((!Configuration::get('PS_TAX') && $product->id_tax_rules_group > 0) ? $prices_tax_incl : $prices_tax_excl);
            if (empty($_value)) {
                $_value = $prices_tax_incl;
            }
            $_prices_tax = ((!$price_display && $product->id_tax_rules_group > 0) ? $prices_tax_incl : $prices_tax_excl);
            if (empty($_prices_tax)) {
                $_prices_tax = $prices_tax_incl;
            }

            $currency = $this->context->currency;
            $price = 0;
            if(_PS_VERSION_ == '9.0.0'){
                $priceFormatter = new PriceFormatter();
                $price = array_map(function ($price) use ($priceFormatter, $currency) {
                    return $priceFormatter->format((float) $price, $currency);
                }, $_prices_tax);                   
            } else{
                $price = array_map(function ($price) use ($currency) {
                        return (string) Tools::displayPrice((float) $price, $currency, $this->context->currency);
                    }, $_prices_tax);
            }
            // $price = array_map(function ($price) use ($currency) {
            //     return Tools::displayPrice((float) $price, $currency, false);
            // }, $_prices_tax);
            $templates = $this->getTemplates();
            $link = new Link();
            $product_url = $link->getProductLink($product->id);
            $GIFTCARD_SHARE = Configuration::get('GIFTCARD_SHARE') ? explode(',', Configuration::get('GIFTCARD_SHARE')) : [];

            $this->giftcardVideoModel();

            $this->context->smarty->assign([
                'values' => $_value,
                'id_product' => Tools::getValue('id_product'),
                'prices_tax' => $price,
                'product_url' => $product_url,
                'GIFTCARD_SHARE' => $GIFTCARD_SHARE,
                'type' => $vals['value_type'],
                'pid' => $product->id,
                'ps_version' => _PS_VERSION_,
                'tax_enabled' => Configuration::get('PS_TAX'),
                'product_tax' => (int) $product->id_tax_rules_group,
                'display_tax_label' => 1,
                'priceDisplay' => (int) $price_display,
                'id_module' => $this->id,
                'preselected_price' => $preselected_price,
                'templates' => $templates,
            ]);

            if (!empty($action) && $action == 'getGiftPrice' && !empty($card_type) && $card_type != 'fixed') {
                return $this->display(__FILE__, 'views/templates/hook/' . $this->tpl_version . '/' . $card_type . '.tpl');
            } else {
                return $this->display(__FILE__, 'views/templates/hook/' . $this->tpl_version . '/gift_card.tpl');
            }
        }
    }

    private function giftcardVideoModel()
    {
        $id_product = Tools::getValue('id_product');
        $model = new GiftCardVideoTemp();
        $id_lang = $this->context->cookie->id_lang;
        if (Tools::getValue('errors')) {
            $this->context->controller->errors = Tools::getValue('errors');
        }

        $id_customer = (int) $this->context->cookie->id_customer;
        $id_guest = (int) $this->context->cookie->id_guest;
        $id_lang = (int) $this->context->cookie->id_lang;
        $temp_videos = $model->getTempVideoById($id_customer, $id_guest);

        if (!empty($temp_videos) && is_array($temp_videos)) {
            foreach ($temp_videos as &$coupon) {
                $link = new Link();
                $product_url = $link->getProductLink($coupon['id_product']);
                $coupon['link'] = $product_url;
            }
        }

        $this->context->smarty->assign([
            'id_customer' => $id_customer,
            'temp_videos' => $temp_videos,
            'video_expiry' => Configuration::get('GIFT_VIDEO_EXPIRY_DAYS'),
            'video_limit' => Configuration::get('GIFT_VIDEO_SIZE_LIMIT'),
            'video_enabled' => Configuration::get('GIFT_VIDEO_UPLOAD_ENABLED'),
            'errors' => $this->context->controller->errors,
            'ps_version' => _PS_VERSION_,
            'temp_controller_display' => $this->context->link->getModuleLink($this->name, 'tempdisplay', [], true),
            'temp_controller_js' => $this->context->link->getModuleLink($this->name, 'tempvideo', ['ajax' => true], true),
        ]);
    }

    public function hookActionGiftCardDeleteFromCart($params)
    {
        if (!empty($params['id_product'])) {
            if (Gift::hasBaseProduct($params['id_product']) && in_array(Gift::getGiftCardType($params['id_product']), ['', 'dropdown', 'range'])) {
                Gift::removeCartGP($params['id_cart'], $params['id_product']);
            }
        }
    }

    public function hookActionProductDelete()
    {
        $id_product = (int) Tools::getValue('id_product');
        if ($id_product && Gift::isExists($id_product)) {
            Gift::deleteByProduct($id_product);
        }
    }

    public function hookActionObjectDeleteAfter($params)
    {
        $giftProduct = (!empty($params) && !empty($params['object']) ? $params['object'] : null);
        if (!empty($giftProduct) && $giftProduct instanceof Product) {
            if (Gift::isExists($giftProduct->id)) {
                Gift::deleteByProduct($giftProduct->id);
            }
        }
    }

    public function hookActionProductUpdate($params)
    {
        // $id_product = (int) ($params && $params['object'] && $params['object']->id) ? $params['object']->id : Tools::getValue('id_product');
        $id_product = 0;

        if (isset($params['object']) && Validate::isLoadedObject($params['object'])) {
            $id_product = (int) $params['object']->id;
        } else {
            $id_product = (int) Tools::getValue('id_product');
        }
        if (Validate::isLoadedObject($product = new Product($id_product)) && Gift::isExists($id_product)) {
            Gift::updateGiftCardField('qty', $id_product, $product->quantity);
            Gift::updateGiftCardField('status', $id_product, $product->active);
            if (in_array(Gift::getGiftCardType($product->id), ['dropdown', 'range'])) {
                Gift::hideGiftProductPrice($product->id);
            }
        }
    }

    public function hookActionValidateOrder($params)
    {
        $cart = $params['cart'];
        $id_cart = $cart->id;
        $id_order = $params['order']->id;
        $products = $cart->getProducts();

        if (!empty($products)) {
            foreach ($products as $product) {
                if($product['id_category_default'] == Configuration::get('GIFT_CARD_CATEGORY')){
                    if ((int) $product['id_product'] && Validate::isLoadedObject($giftProduct = new Product((int) $product['id_product']))) {
                        $reference_product = Gift::hasBaseProduct($giftProduct->id, true);
                        if ($reference_product) {
                            Gift::updateCartGC($id_cart, $id_order, $giftProduct->id, $cart->id_customer);
                            // disable child product
                            if (Gift::getGiftCardType($giftProduct->id) != 'fixed') {
                                StockAvailable::setQuantity($giftProduct->id, 0, $product['cart_quantity']);
                            }
                            $giftProduct->active = (Gift::isExists($giftProduct->id) && Gift::getGiftCardType($giftProduct->id) == 'fixed') ? true : false;
                            $giftProduct->update();

                            $deltaQty = (int) StockAvailable::getQuantityAvailableByProduct($reference_product) - (int) $product['cart_quantity'];
                            if (Gift::getGiftCardType($giftProduct->id) != 'fixed') {
                                StockAvailable::setQuantity($reference_product, 0, $deltaQty);
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $id_order = (int) $params['id_order'];
        $id_cart = (int) Order::getCartIdStatic($id_order);
        $id_order_state = $params['newOrderStatus']->id;

        $giftOrdered = Gift::getOrderedGiftProducts($id_cart, ' AND has_voucher = 0');
        $approval_states = (Configuration::get('GIFT_APPROVAL_STATUS') ? explode(',', Configuration::get('GIFT_APPROVAL_STATUS')) : []);
        if (!empty($approval_states) && in_array($id_order_state, $approval_states) && count($giftOrdered) > 0) {
            foreach (['home', 'sendsomeone'] as $type) {
                $this->generateVoucher($id_cart, $type);
            }
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $id_order = (int) $params['id_order'];
        $id_cart = (int) Order::getCartIdStatic($id_order);
        $id_order_state = $params['newOrderStatus']->id;

        $giftOrdered = Gift::getOrderedGiftProducts($id_cart, ' AND has_voucher = 0');
        $approval_states = (Configuration::get('GIFT_APPROVAL_STATUS') ? explode(',', Configuration::get('GIFT_APPROVAL_STATUS')) : []);
        if (!empty($approval_states) && in_array($id_order_state, $approval_states) && count($giftOrdered) > 0) {
            foreach (['home', 'sendsomeone'] as $type) {
                $this->generateVoucher($id_cart, $type);
            }
        }
    }

    public function hookActionAdminProductsListingFieldsModifier($list)
    {
        if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            if ($list['sql_where']) {
                if (!empty(pSQL(implode(',', Gift::getGiftCardIds())))) {
                    $list['sql_where'][] = 'p.id_product NOT IN (' . pSQL(implode(',', Gift::getGiftCardIds())) . ')';
                }
            }
        } else {
            if (!empty(pSQL(implode(',', Gift::getGiftCardIds())))) {
                $list['where'] = 'AND p.id_product NOT IN (' . pSQL(implode(',', Gift::getGiftCardIds())) . ')';
            }
        }
    }

     public function hookActionProductGridQueryBuilderModifier($list)
    {
        // if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $giftCardCategoryId = (int) Configuration::get('GIFT_CARD_CATEGORY');
            $qb = $list['search_query_builder'];

            $qb->leftJoin(
                'p',
                _DB_PREFIX_ . 'category',
                'c',
                'c.id_category = p.id_category_default'
            );
             $qb->andWhere('p.reference NOT LIKE :excludedReference')
                ->setParameter('excludedReference', 'GIFT_PRODUCT_%');
    }

    public function generateVoucher($id_cart, $type = 'home')
    {
        $id_order = Gift::getOrderIdsByCartId($id_cart);
        $order = new Order((int) $id_order);
        $customer = new Customer((int) $order->id_customer);
        $cart_rules = [];
        $id_lang = Context::getContext()->language->id;

        $all_products = Gift::getGiftVoucherProducts($id_cart, $order->id, $type);
        $languages = Language::getLanguages(true);
        if ($all_products) {
            foreach ($all_products as $product) {
                if (Gift::hasBaseProduct((int) $product['id_product']) && Validate::isLoadedObject($giftProduct = new Product((int) $product['id_product'], true))) {
                    $prod_detail = Gift::getProductDetail($giftProduct->id, $id_cart, $id_order, $customer->id);
                    $voucher = new CartRule();
                    $vcode = (Configuration::get('GIFTCARD_VOUCHER_PREFIX') ? Configuration::get('GIFTCARD_VOUCHER_PREFIX') : '') . Tools::passwdGen($prod_detail['length'], $prod_detail['vcode_type']);

                    foreach ($languages as $lang) {
                        $voucher->name[$lang['id_lang']] = $giftProduct->name[$lang['id_lang']];
                    }
                    $now = date('Y-m-d H:i:s');
                    $voucher->date_from = $now;
                    $voucher->date_to = date('Y-m-d H:i:s', strtotime(sprintf('+ %d %s', $prod_detail['validity_period'], $prod_detail['validity_type']), strtotime($now)));
                    $voucher->quantity = $product['cart_quantity'];
                    $voucher->quantity_per_user = $product['cart_quantity'];
                    $voucher->free_shipping = $prod_detail['free_shipping'];
                    $voucher->partial_use = $prod_detail['partial_use'];
                    $voucher->reduction_currency = $prod_detail['reduction_currency'];
                    $voucher->active = $prod_detail['status'];
                    $voucher->reduction_product = $prod_detail['id_discount_product'];
                    $voucher->code = $vcode;
                    $voucher->minimum_amount_currency = $prod_detail['reduction_currency'];

                    $prod_detail['price'] = Product::getPriceStatic((int) $giftProduct->id, true, 0, 2);
                    $cal = 0.00;
                    if ($prod_detail['reduction_type'] == 'amount') {
                        $voucher->reduction_amount = (float) $prod_detail['price'];
                        $voucher->reduction_tax = (int) $prod_detail['reduction_tax'];
                        $voucher->reduction_percent = 0;
                    } elseif ($prod_detail['reduction_type'] == 'percent') {
                        if ($prod_detail['value_type'] == 'range') {
                            $val = explode(',', $prod_detail['card_value']);
                            $pri = explode(',', $prod_detail['reduction_amount']);
                            $cal = (float) ((float) $pri[0] / (float) $val[0]) * (float) $prod_detail['price'];
                        } elseif ($prod_detail['value_type'] == 'dropdown') {
                            $val = explode(',', $prod_detail['card_value']);
                            $pri = explode(',', $prod_detail['reduction_amount']);
                            foreach ($val as $k => $v) {
                                $value = (float) $v;
                                if ($value == $prod_detail['price']) {
                                    $cal = (float) $pri[$k];
                                    break;
                                }
                            }
                        } else {
                            $cal = (float) $prod_detail['reduction_amount'];
                        }

                        $voucher->reduction_percent = $cal;
                        $voucher->reduction_amount = 0;
                        $voucher->reduction_tax = 0;

                        $voucher->shop_restriction = (Shop::isFeatureActive()) ? 1 : 0;
                    }

                    if ($voucher->add()) {
                        $id_cart_rule = $voucher->id;
                        if (Shop::isFeatureActive()) {
                            Db::getInstance()->delete('cart_rule_shop', '`id_cart_rule` = ' . (int) $id_cart_rule);
                            $product_shops = Gift::getShopsByProduct($giftProduct->id);
                            foreach ($product_shops as $id_shop) {
                                Gift::restrictVoucherToShop($id_cart_rule, $id_shop);
                            }
                        }
                        $id_image = Gift::getId_image($giftProduct->id);
                        Gift::insertCustomer($id_cart_rule, $id_cart, $id_order, $giftProduct->id, $customer->id, $giftProduct->link_rewrite[$id_lang], $id_image);
                        array_push($cart_rules, $id_cart_rule);

                        $currency = new Currency((int) $voucher->reduction_currency);
                        $price = 0;
                        if(_PS_VERSION_ == '9.0.0'){
                            $priceFormatter = new \PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
                            $price = $priceFormatter->format($voucher->reduction_amount, $currency);                        
                        } else{
                            $price = Tools::displayPrice($voucher->reduction_amount, $currency);
                        }
                        Gift::setVoucherFlag($id_cart, $id_order, $giftProduct->id);
                        if ($type == 'sendsomeone') {
                            $newDate = date_diff(date_create($voucher->date_from), date_create($voucher->date_to));
                            $value = ($voucher->reduction_percent && $voucher->reduction_percent != '0.00') ? $voucher->reduction_percent . ' %' : $price;
                            $this->sendGiftCard(
                                $customer->firstname . ' ' . $customer->lastname,
                                $product['friend_name'],
                                $product['friend_email'],
                                '',
                                $voucher->name[$order->id_lang],
                                $vcode,
                                $newDate->format('%R%a days'),
                                date('Y-m-d H:i:s'),
                                $value,
                                $giftProduct->id,
                                $product['gift_message'],
                                $voucher->quantity,
                                $product['id_gift_card_template'],
                                $order->id_lang
                            );
                        }
                    }
                } else {
                    continue;
                }
            }
            if ($cart_rules && $type == 'home') {
                Gift::sendAlert($cart_rules, $customer->id);
            }
        }
    }

    protected function calculateGiftPrice($price, $id_product, $use_tax = true, $use_group_reduction = false, $use_reduc = false, $decimals = 2)
    {
        $product = new Product((int) $id_product, true, (int) $this->context->language->id);
        $id_customer = (int) $this->context->customer->id;

        // Initializations
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int) $this->context->customer->id);
        }

        if (!$id_group) {
            $id_group = (int) Group::getCurrent()->id;
        }
        // Tax
        $id_address = $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $address = Address::initialize($id_address, true);
        $id_shop = (int) $this->context->shop->id;
        $id_currency = (int) $this->context->currency->id;
        $id_country = (int) $address->id_country;

        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int) $id_product, $this->context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        // Add Tax
        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }

        // Eco Tax
        $ecotax = '';
        if (!$product->ecotax) {
            if ($id_currency) {
                $ecotax = Tools::convertPrice($product->ecotax, $id_currency);
            }
            if ($use_tax) {
                // reinit the tax manager for ecotax handling
                $tax_manager = TaxManagerFactory::getManager($address, (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID'));
            }
            $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
            $price += $ecotax_tax_calculator->addTaxes($ecotax);
        }

        // Reduction
        $specific_price = SpecificPrice::getSpecificPrice(
            (int) $id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            1
        );

        $specific_price_reduction = 0;
        if ($specific_price && $use_reduc) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];
                if (!$specific_price['id_currency']) {
                    $reduction_amount = Tools::convertPrice($reduction_amount, $id_currency);
                }

                $specific_price_reduction = $reduction_amount;

                // Adjust taxes if required
                if (!$use_tax && $specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->removeTaxes($specific_price_reduction);
                }
                if ($use_tax && !$specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->addTaxes($specific_price_reduction);
                }
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
        }

        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }

        // Group reduction
        if ($use_group_reduction) {
            $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
            if ($reduction_from_category !== false) {
                $group_reduction = $price * (float) $reduction_from_category;
            } else {
                $group_reduction = (($reduc = Group::getReductionByIdGroup($id_group)) != 0) ? ($price * $reduc / 100) : 0;
            }
            $price -= $group_reduction;
        }

        $price = Tools::ps_round($price, $decimals);
        if ($price < 0) {
            $price = 0;
        }

        return $price;
    }

    private function setPrice($id_product, $new_price)
    {
        if (!empty($id_product) && !empty($new_price) && $new_price != 0) {
            $product = new Product($id_product);
            $product->price = $new_price;
            $product->update(true);
        }
    }

    protected function createGiftCategory()
    {
        if (!Configuration::get('GIFT_CARD_CATEGORY')) {
            $languages = Language::getLanguages(false);
            $gift_category = new Category();
            $gift_category->active = true;
            $gift_category->id_parent = Configuration::get('PS_HOME_CATEGORY');
            foreach ($languages as $lang) {
                $gift_category->name[$lang['id_lang']] = $this->l('Gift Cards');
                $gift_category->link_rewrite[$lang['id_lang']] = Tools::str2url($gift_category->name[$lang['id_lang']]);
            }
            if ($gift_category->add()) {
                Configuration::updateValue('GIFT_CARD_CATEGORY', $gift_category->id);

                return true;
            } else {
                return false;
            }
        }
    }

    protected function removeGiftCategory()
    {
        if (Configuration::get('GIFT_CARD_CATEGORY')) {
            $gift_category = new Category(Configuration::get('GIFT_CARD_CATEGORY'));
            if ($gift_category->delete()) {
                Configuration::deleteByName('GIFT_CARD_CATEGORY');

                return true;
            } else {
                return false;
            }
        }
    }

    public function processDuplicate($old_id_product, $price = 0.0)
    {
        if (Validate::isLoadedObject($product = new Product((int) $old_id_product))) {
            $id_product_old = $product->id;
            if (empty($product->price) && Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
                foreach ($shops as $shop) {
                    if ($product->isAssociatedToShop($shop['id_shop'])) {
                        $product_price = new Product($id_product_old, false, null, $shop['id_shop']);
                        $product->price = $product_price->price;
                    }
                }
            }

            // exclude tax price if tax is enabled - tax incl/ecxl option compatibility
            $priceDisplay = (int) Product::getTaxCalculationMethod((int) Context::getContext()->cart->id_customer);
            $tax = (!$priceDisplay || $priceDisplay == 2) ? true : false;
            $taxRate = Gift::parseFloatValue(Tax::getProductTaxRate($old_id_product));
            if ($tax && $taxRate) {
                $price = Tools::ps_round($price / (1 + ((float) $taxRate / 100)), 3);
            }

            unset($product->id);
            unset($product->id_product);
            $product->indexed = 0;
            $product->active = 1;
            $product->customizable = 1;
            $product->visibility = 'none';
            $product->reference = 'GIFT_PRODUCT_' . $product->reference;
            $product->price = (float) $price;
            if ($product->add()
                && Category::duplicateProductCategories($id_product_old, $product->id)
                && Product::duplicateSuppliers($id_product_old, $product->id)
                && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
                && GroupReduction::duplicateReduction($id_product_old, $product->id)
                && Product::duplicateAccessories($id_product_old, $product->id)
                && Product::duplicateFeatures($id_product_old, $product->id)
                && Pack::duplicate($id_product_old, $product->id)
                // && Product::duplicateCustomizationFields($id_product_old, $product->id)
                && Product::duplicateTags($id_product_old, $product->id)
                && Product::duplicateDownload($id_product_old, $product->id)) {
                if ($product->hasAttributes()) {
                    Product::updateDefaultAttribute($product->id);
                } else {
                    Product::duplicateSpecificPrices($id_product_old, $product->id);
                }

                if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                    $this->context->controller->errors[] = $this->l('An error occurred while copying images.');
                } else {
                    Hook::exec('actionProductAdd', ['id_product' => (int) $product->id, 'product' => $product]);
                    if (in_array($product->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $product->id);
                    }
                    // set stock equals to parent
                    StockAvailable::setQuantity($product->id, 0, StockAvailable::getStockAvailableIdByProductId($old_id_product));

                    return $product->id;
                }
            } else {
                $this->context->controller->errors[] = $this->l('An error occurred while creating an object.');
            }
        }
    }

    public function getTemplates()
    {
        $templates = [];
        $templates = GiftTemplates::getTemplates();

        $mail_template = sprintf('%smails/%s/gift.html', $this->local_path, $this->context->language->iso_code);
        array_unshift(
            $templates,
            [
                'id_gift_card_template' => 0,
                'template_name' => 'default',
                'thumb' => sprintf('%smodules/giftcard/views/img/giftcard_template_default.png', __PS_BASE_URI__),
                'id_lang' => $this->context->language->id,
                'content' => Tools::file_get_contents((is_file($mail_template)) ? $mail_template : sprintf('%smails/en/gift.html', $this->local_path)),
            ]
        );

        return $templates;
    }

    public function sendGiftCard($sender, $receiverName, $receiverEmail, $video_link, $giftcard_name, $vcode, $expire_date, $date, $value, $id_product, $message = '', $qty = 1, $id_template = 0, $id_lang = null) {
        $image = '';
        $id_lang = (!$id_lang) ? (int) Context::getContext()->language->id : $id_lang;
        $id_shop = (int) Context::getContext()->shop->id;
        if (Validate::isLoadedObject($product = new Product((int) $id_product, false, $id_lang))) {
            $id_image = Gift::getId_image($product->id);
            $type = (true === Tools::version_compare(_PS_VERSION_, '1.7', '<')) ? ImageType::getFormatedName('large') : ImageType::getFormattedName('large');  // used for older version compatibility
            $this->context->smarty->assign(['ps_img' => $this->context->link->getImageLink($product->link_rewrite, $id_image, $type)]);
            $image = $this->display(__FILE__, 'views/templates/hook/img.tpl');
        }
        $templateVars = [
            '{gift_image}' => $image,
            '{sender}' => $sender,
            '{rec_name}' => $receiverName,
            '{giftcard_name}' => $giftcard_name,
            '{vcode}' => $vcode,
            '{expire_date}' => $expire_date,
            '{date}' => $date,
            '{value}' => $value,
            '{message}' => $message,
            '{video_link}' => '',
            '{quantity}' => $qty,
            '{shop_link}' => _PS_BASE_URL_ . __PS_BASE_URI__,
            '{shop_name}' => Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)),
        ];
        if ($id_template && Validate::isLoadedObject($giftTemplate = new GiftTemplates($id_template, $id_lang))) {
            $templateVars['{video_link}'] = $video_link;
            $templateVars['{gift_content_html}'] = str_replace(
                array_keys($templateVars),
                array_values($templateVars),
                $giftTemplate->content
            );

            $templateVars['{gift_content_txt}'] = Tools::safeOutput(str_replace(
                array_keys($templateVars),
                array_values($templateVars),
                $giftTemplate->content
            ));
            $template = 'custom_template';
        } else {
            $template = 'gift';
            $this->context->smarty->assign([
                'video_url' => $video_link,
            ]);
            if (Configuration::get('GIFT_VIDEO_UPLOAD_ENABLED', false)) {
                $templateVars['{video_link}'] = $video_link ? $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'giftcard/views/templates/front/video_link.tpl') : '';
            }
        }

        if (!empty($receiverEmail)) {
            $res = Mail::Send(
                (int) $id_lang,
                $template,
                Mail::l('You received a Gift Card', (int) $id_lang),
                $templateVars,
                $receiverEmail,
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_ . 'giftcard/mails/',
                $id_shop
            );

            return $res;
        }

        return false;
    }

    public function alterNewCols()
    {
        $return = true;
        if (!Gift::columnExist('partial_use', 'gift_card')
            && !Gift::columnExist('validity_period', 'gift_card')
            && !Gift::columnExist('validity_type', 'gift_card')) {
            $return &= Gift::addGcColumn();
        }

        if (!Gift::columnExist('specific_date', 'id_gift_card_template')) {
            $return &= Gift::addGcOrderColumnTemplate();
        }

        if (!Gift::columnExist('specific_date', 'ordered_gift_cards')) {
            $return &= Gift::addGcOrderColumn();
        }
        $return &= GiftTemplates::createTemplateTables();

        return $return;
    }

    public function alterNewColumns()
    {
        $return = true;
        if (!Gift::columnExist('expiry_alert', 'gift_card_customer')) {
            $return &= Gift::addGiftCustomerColumn();
        }

        return $return;
    }

    public function hookDisplayAdminOrder($param)
    {
        $id_order = $param['id_order'];
        $model = new Gift();
        $id_lang = $this->context->cookie->id_lang;
        $vouchers = $model->getVoucherByOrderId($id_order, true, $id_lang);
        if (!empty($vouchers) && is_array($vouchers)) {
            $this->context->smarty->assign('vouchers', $vouchers);

            return $this->display(__FILE__, 'views/templates/hook/gift_voucher.tpl');
        }
    }

    public function ajaxProcessFmePopupAction()
    {
        $action = Tools::getValue('fme_action');
        $result = ['success' => false, 'message' => 'Action failed'];

        if ($action == 'popupcompleted') {
            if (Configuration::get($this->name . '_INSTALL_DATE')) {
                Configuration::deleteByName($this->name . '_INSTALL_DATE');
            }
            if (Configuration::get($this->name . '_FME_POPUP_REMINDER')) {
                Configuration::deleteByName($this->name . '_FME_POPUP_REMINDER');
            }

            $result = ['success' => true, 'message' => 'Popup completed and configuration deleted'];
        } elseif ($action == 'setreminder') {
            Configuration::updateValue($this->name . '_INSTALL_DATE', date('Y-m-d'));
            Configuration::updateValue($this->name . '_FME_POPUP_REMINDER', true);

            $result = ['success' => true, 'message' => 'Popup completed and configuration deleted'];
        }

        exit(json_encode($result));
    }
}
