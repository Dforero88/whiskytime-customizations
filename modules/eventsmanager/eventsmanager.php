<?php
/**
 * Events Manager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2017 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
if (!defined('_MYSQL_ENGINE_')) {
    define('_MYSQL_ENGINE_', 'MyISAM');
}

require_once _PS_MODULE_DIR_ . 'eventsmanager/models/Events.php';
require_once _PS_MODULE_DIR_ . 'eventsmanager/models/fmeTags.php';
require_once _PS_MODULE_DIR_ . 'eventsmanager/tools/FMEEventsTools.php';
require_once _PS_MODULE_DIR_ . 'eventsmanager/tools/EventsPaginator.php';
include_once dirname(__FILE__) . '/models/FmeSeatMapModel.php';
include_once dirname(__FILE__) . '/models/FmeCustomerModel.php';
include_once _PS_MODULE_DIR_ . 'eventsmanager/HTMLTemplateCustomPdf.php';
include_once _PS_MODULE_DIR_ . 'eventsmanager/HTMLTemplateCustomPdfAdmin.php';
class EventsManager extends Module
{
    private $tabClass = 'AdminEventsManager';
    private $tabModule = 'eventsmanager';
    private $tabName = 'Events Manager';

    public function __construct()
    {
        $this->name = 'eventsmanager';
        $this->tab = 'front_office_features';
        $this->version = '4.5.4';
        $this->author = 'FMM Modules';
        $this->displayName = $this->l('Events Manager');
        $this->description = $this->l('Display Events and also sale tickets to those events.');
        $this->bootstrap = true;
        $this->module_key = '32698ef280747ac46c837056595ea5fa';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        parent::__construct();
    }

    public function install()
    {
        $this->installConfiguration();
        if (!$this->existsTab($this->tabClass)) {
            if (!$this->addTab($this->tabClass, 0)) {
                return false;
            }
        }

        return parent::install()
        && $this->installDb()
        && $this->registerHook('displayPaymentTop')
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayLeftColumn')
        && $this->registerHook('displayEvents')
        && $this->registerHook('displayCustomerAccount')
        && $this->registerHook('ModuleRoutes')
        && $this->registerHook('actionOrderStatusUpdate')
        && $this->registerHook('displayProductAdditionalInfo')
        && $this->registerHook('displayAdminOrder')
        // && $this->registerHook('newOrder')
        && $this->registerHook('actionValidateOrder')
        && $this->registerHook('displayBackOfficeHeader')
        && Configuration::updateValue($this->name . '_INSTALL_DATE', date('Y-m-d'))
        && $this->makeDir();
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        if (!$this->removeTab($this->tabClass)) {
            return false;
        }
        if (!$this->uninstallDb()) {
            return false;
        }
        $this->renameDir();
        if (Configuration::get($this->name . '_INSTALL_DATE')) {
            Configuration::deleteByName($this->name . '_INSTALL_DATE');
        }
        if (Configuration::get($this->name . '_FME_POPUP_REMINDER')) {
            Configuration::deleteByName($this->name . '_FME_POPUP_REMINDER');
        }

        return true;
    }

    public function renameDir()
    {
        rename(_PS_IMG_DIR_ . 'eventsgallery', _PS_IMG_DIR_ . 'eventsgallery' . rand(pow(10, 3 - 1), pow(10, 3) - 1));

        return true;
    }

    public function makeDir()
    {
        mkdir(_PS_IMG_DIR_ . 'eventsgallery', 0777, true);

        return true;
    }

    protected function addTab($tabClass, $id_parent)
    {
        $tab = new Tab();
        $tab->class_name = $tabClass;
        $tab->id_parent = $id_parent;
        $tab->module = $this->tabModule;
        $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Events');
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $tab->icon = 'event';
        }
        $tab->add();
        $subtab1 = new Tab();
        $subtab1->class_name = 'AdminEvents';
        $subtab1->id_parent = Tab::getIdFromClassName($tabClass);
        $subtab1->module = $this->tabModule;
        $subtab1->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Manage Events');
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $subtab1->icon = 'event';
        }
        $subtab1->add();
        $subtab2 = new Tab();
        $subtab2->class_name = 'AdminEventsDetails';
        $subtab2->id_parent = Tab::getIdFromClassName($tabClass);
        $subtab2->module = $this->tabModule;
        $subtab2->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Events Details');
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $subtab2->icon = 'event';
        }
        $subtab2->add();
        $subtab3 = new Tab();
        $subtab3->class_name = 'AdminTags';
        $subtab3->id_parent = Tab::getIdFromClassName($tabClass);
        $subtab3->module = $this->tabModule;
        $subtab3->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Events Tags');
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $subtab3->icon = 'event';
        }
        $subtab3->add();

        return true;
    }

    private function removeTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);
        $id_tab1 = Tab::getIdFromClassName('AdminEventsDetails');
        $id_tab_2 = Tab::getIdFromClassName('AdminEvents');
        $id_tab_3 = Tab::getIdFromClassName('AdminTags');

        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
        }
        if ($id_tab1 != 0) {
            $tab = new Tab($id_tab1);
            $tab->delete();
        }
        if ($id_tab_2 != 0) {
            $tab = new Tab($id_tab_2);
            $tab->delete();
        }
        if ($id_tab_3 != 0) {
            $tab = new Tab($id_tab_3);
            $tab->delete();
        }

        return true;
    }

    public function getIdTabFromClassName($tabClass)
    {
        $sql = 'SELECT id_tab FROM ' . _DB_PREFIX_ . 'tab WHERE class_name="' . pSQL($tabClass) . '"';
        $tab = Db::getInstance()->getRow($sql);

        return (int) $tab['id_tab'];
    }

    public function existsTab($tabClass)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT id_tab AS id
        FROM `' . _DB_PREFIX_ . 'tab` t
        WHERE LOWER(t.`class_name`) = \'' . pSQL($tabClass) . '\'');
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

    private function installDb()
    {
        $sql = [];
        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events(
                    `event_id` int(11) NOT NULL auto_increment,
                    `event_start_date` varchar(250) default NULL,
                    `event_end_date` varchar(250) default NULL,
                    `event_streaming_start_time` varchar(250) default NULL,
                    `event_streaming_end_time` varchar(250) default NULL,
                    `event_image` mediumtext,
                    `event_venu` varchar(250) default NULL,
                    `longitude` varchar(250) default NULL,
                    `latitude` varchar(250) default NULL,
                    `event_thumb_image` mediumtext,
                    `event_medium_image` mediumtext,
                    `event_video` mediumtext,
                    `event_streaming` mediumtext,
                    `event_status` smallint(6) default NULL,
                    `image_status` smallint(6) default 0,
                    `pdf_status` smallint(6) default 0,
                    `contact_name` varchar(250) default NULL,
                    `contact_phone` varchar(250) default NULL,
                    `contact_fax` varchar(250) default NULL,
                    `contact_email` varchar(250) default NULL,
                    `contact_address` text,
                    `created_time` datetime default NULL,
                    `update_time` datetime default NULL,
                    `facebook_link` mediumtext,
                    `twitter_link` mediumtext,
                    `instagram_link` mediumtext,
                    `seat_selection`        tinyint(1) default \'0\',
                    PRIMARY KEY  (`event_id`)
              ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';
        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_lang(
                `event_id` int(11) NOT NULL AUTO_INCREMENT,
                `id_lang` int(11) NOT NULL,
                `event_title` varchar(250) default NULL,
                `event_permalinks` varchar(250) default NULL,
                `event_content` text,
                `event_url_prefix` varchar(250) default NULL,
                `event_page_title` varchar(250) default NULL,
                `event_meta_keywords` text,
                `event_meta_description` text,
                PRIMARY KEY(event_id, id_lang)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_products(
                event_product_id int(11) NOT NULL AUTO_INCREMENT,
                event_id int(11) NOT NULL,
                id_product int(10) unsigned NOT NULL,
                UNIQUE KEY event_product_id (event_product_id)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_seat_map(
            `id_seat_map`              int(11) NOT NULL auto_increment,
            `event_id`               int(11) NOT NULL,
            `seat_map`      longtext default NULL,
            PRIMARY KEY             (`id_seat_map`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_gallery(
                `image_id` mediumint(9) NOT NULL auto_increment,
                `image_file` mediumtext,
                `image_name` varchar(250) default NULL,
                `image_order` int(250) default NULL,
                `image_status` int(10) default NULL,
                `events_id` mediumint(9) NOT NULL,
                PRIMARY KEY  (`image_id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_shop(
                    `event_id` int(11) NOT NULL,
                    `id_shop` int(11) NOT NULL,
                    PRIMARY KEY  (`event_id`, `id_shop`),
                    KEY `id_shop` (`id_shop`)
                   ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_customer(
            `id_events_customer`              int(11) NOT NULL auto_increment,
            `event_id`               int(11) NOT NULL,
            `id_product`      int(11) NOT NULL,
            `quantity`      int(11) default NULL,
            `id_customer`      int(11) default NULL,
            `id_guest`      int(11) default NULL,
            `id_cart`      int(11) default NULL,
            `id_order`      int(11) default NULL,
            `order_status`       varchar(255) default NULL,
            `customer_name`      varchar(255) default NULL,
            `customer_phone`     varchar(255) default NULL,
            `reserve_seats`     varchar(255) default NULL,
            `reserve_seats_num`     varchar(255) default NULL,
            `date`    varchar(255) default NULL,
            `days`    varchar(255) default NULL,
            `admin_order`      int(11) default NULL,
            `admin_payment_confirm`      int(11) default NULL,
            PRIMARY KEY             (`id_events_customer`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_tags(
            `id_fme_tags`   int(11) NOT NULL AUTO_INCREMENT,
            `active`        tinyint(1),
            PRIMARY KEY(`id_fme_tags`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_tags_lang(
            `id_fme_tags`   int(11) NOT NULL,
            `id_lang`       int(11) NOT NULL,
            `title`         varchar(250) default NULL,
            `friendly_url` varchar(250) default NULL,
            `description`   text,
            PRIMARY KEY  (`id_fme_tags`, `id_lang`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_tags(
            `event_id`  int(11) NOT NULL,
            `id_fme_tags`  int(11) NOT NULL,
            PRIMARY KEY  (`event_id`, `id_fme_tags`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }

        return true;
    }

    public function uninstallDb()
    {
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events_seat_map';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events_gallery';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events_lang';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events_products';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events_customer';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_tags';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_tags_lang';
        Db::getInstance()->execute($sql);
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events_tags';
        Db::getInstance()->execute($sql);
        Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fme_events_shop');

        return true;
    }

    public function installConfiguration()
    {
        Configuration::updateValue('EVENTS_ENABLE_DISABLE', 1);
        Configuration::updateValue('EVENTS_TAGS_ENABLE_DISABLE', 1);
        Configuration::updateValue('FME_WAIT_MIN', 2);
        Configuration::updateValue('FME_REQ_PHONE', 1);
        Configuration::updateValue('EVENTS_PAGE_TITLE', 'Events');
        Configuration::updateValue('EVENTS_META_KEYWORDS', 'Events Keywords');
        Configuration::updateValue('EVENTS_META_DESCRIPTION', 'Events Description');
        Configuration::updateValue('EVENTS_SORT_BY', 0);
        Configuration::updateValue('EVENTS_SORT_ORDER', 1);
        Configuration::updateValue('EVENTS_THEME', 0);
        Configuration::updateValue('EVENTS_SHOW_MAP_HOVER_ADDRESS', 1);
        Configuration::updateValue('EVENTS_SHOW_YOUTUBE_VIDEO', 1);
        Configuration::updateValue('EVENTS_SHARING_OPTIONS', 1);
        Configuration::updateValue('EVENTS_SHOW_GALLERY', 1);
        Configuration::updateValue('EVENTS_CONF_BLOCK_SETTINGS', 1);
        Configuration::updateValue('FME_ENABLE_PRODUCT', 0);
        Configuration::updateValue('EVENTS_PER_PAGE', 5);
        Configuration::updateValue('SLIDER_WIDTH', 960);
        Configuration::updateValue('SLIDER_HEIGHT', 500);
        Configuration::updateValue('THUMBNAILS_ENABLE_DISABLE', 1);
        Configuration::updateValue('SLIDER_ARROWS', 1);
        Configuration::updateValue('PAGINATION_BUTTONS', 1);
        Configuration::updateValue('PAGINATION_BUTTONS', 1);
        Configuration::updateValue('EVENT_SHOW_LIVE_STREAMING', 0);
        Configuration::updateValue('EM_VS', 0);
        Configuration::updateValue('ORDER_INVOICE_ENABLE_DISABLE', 1);
        Configuration::updateValue('EVENTS_META_MAPKEY', '');

        return true;
    }

    public function uninstallConfiguration()
    {
        Configuration::deleteByName('EVENTS_ENABLE_DISABLE');
        Configuration::deleteByName('EVENTS_TAGS_ENABLE_DISABLE');
        Configuration::deleteByName('FME_WAIT_MIN');
        Configuration::deleteByName('FME_REQ_PHONE');
        Configuration::deleteByName('EVENTS_PAGE_TITLE');
        Configuration::deleteByName('EVENTS_META_MAPKEY');
        Configuration::deleteByName('EVENTS_META_KEYWORDS');
        Configuration::deleteByName('EVENTS_META_DESCRIPTION');
        Configuration::deleteByName('EVENTS_SORT_BY');
        Configuration::deleteByName('EVENTS_SORT_ORDER');
        Configuration::deleteByName('EVENTS_THEME');
        Configuration::deleteByName('EVENTS_SHOW_MAP_HOVER_ADDRESS');
        Configuration::deleteByName('EVENTS_SHOW_YOUTUBE_VIDEO');
        Configuration::deleteByName('EVENTS_SHARING_OPTIONS');
        Configuration::deleteByName('EVENTS_SHOW_GALLERY');
        Configuration::deleteByName('FME_ENABLE_PRODUCT');
        Configuration::deleteByName('EVENTS_CONF_BLOCK_SETTINGS');
        Configuration::deleteByName('EVENTS_PER_PAGE');
        Configuration::deleteByName('SLIDER_WIDTH');
        Configuration::deleteByName('SLIDER_HEIGHT');
        Configuration::deleteByName('THUMBNAILS_ENABLE_DISABLE');
        Configuration::deleteByName('SLIDER_ARROWS');
        Configuration::deleteByName('EM_VS');
        Configuration::deleteByName('PAGINATION_BUTTONS');
        Configuration::deleteByName('EVENT_SHOW_LIVE_STREAMING');
        Configuration::deleteByName('AUTOPLAY_SLIDER');
        Configuration::deleteByName('ORDER_INVOICE_ENABLE_DISABLE');
        $this->unregisterHook('displayPaymentTop');
        // $this->unregisterHook('newOrder');
        $this->unregisterHook('actionValidateOrder');
        $this->unregisterHook('displayProductAdditionalInfo');
        $this->unregisterHook('actionOrderStatusUpdate');

        return true;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitPquote')) {
            $languages = Language::getLanguages();
            $values = [];
            foreach ($languages as $lang) {
                $values['EVENTS_PAGE_TITLE'][$lang['id_lang']] = Tools::getValue(
                    'EVENTS_PAGE_TITLE_' . $lang['id_lang']
                );
                $values['EVENTS_META_KEYWORDS'][$lang['id_lang']] = Tools::getValue(
                    'EVENTS_META_KEYWORDS_' . $lang['id_lang']
                );
                $values['EVENTS_META_DESCRIPTION'][$lang['id_lang']] = Tools::getValue(
                    'EVENTS_META_DESCRIPTION_' . $lang['id_lang']
                );
            }
            Configuration::updateValue('EVENTS_PAGE_TITLE', $values['EVENTS_PAGE_TITLE']);
            Configuration::updateValue('EVENTS_META_KEYWORDS', $values['EVENTS_META_KEYWORDS']);
            Configuration::updateValue('EVENTS_META_DESCRIPTION', $values['EVENTS_META_DESCRIPTION']);
            Configuration::updateValue('EVENTS_ENABLE_DISABLE', Tools::getValue('EVENTS_ENABLE_DISABLE'));
            Configuration::updateValue(
                'EVENTS_TAGS_ENABLE_DISABLE',
                Tools::getValue('EVENTS_TAGS_ENABLE_DISABLE')
            );
            Configuration::updateValue(
                'EVENTS_CONF_BLOCK_SETTINGS',
                Tools::getValue('EVENTS_CONF_BLOCK_SETTINGS')
            );
            Configuration::updateValue(
                'FME_ENABLE_PRODUCT',
                Tools::getValue('FME_ENABLE_PRODUCT')
            );

            Configuration::updateValue(
                'FME_REQ_PHONE',
                Tools::getValue('FME_REQ_PHONE')
            );
            Configuration::updateValue('EVENTS_SORT_BY', Tools::getValue('EVENTS_SORT_BY'));
            Configuration::updateValue('EVENTS_SORT_ORDER', Tools::getValue('EVENTS_SORT_ORDER'));
            Configuration::updateValue('EVENTS_THEME', Tools::getValue('EVENTS_THEME'));
            Configuration::updateValue('EVENTS_PER_PAGE', Tools::getValue('EVENTS_PER_PAGE'));
            Configuration::updateValue(
                'EVENTS_SHOW_MAP_HOVER_ADDRESS',
                Tools::getValue('EVENTS_SHOW_MAP_HOVER_ADDRESS')
            );
            Configuration::updateValue('EVENTS_SHOW_YOUTUBE_VIDEO', Tools::getValue('EVENTS_SHOW_YOUTUBE_VIDEO'));
            Configuration::updateValue('EVENTS_SHARING_OPTIONS', Tools::getValue('EVENTS_SHARING_OPTIONS'));
            Configuration::updateValue('EVENTS_SHOW_GALLERY', Tools::getValue('EVENTS_SHOW_GALLERY'));
            Configuration::updateValue('SLIDER_WIDTH', Tools::getValue('SLIDER_WIDTH'));
            Configuration::updateValue('SLIDER_HEIGHT', Tools::getValue('SLIDER_HEIGHT'));
            Configuration::updateValue('THUMBNAILS_ENABLE_DISABLE', Tools::getValue('THUMBNAILS_ENABLE_DISABLE'));
            Configuration::updateValue('SLIDER_ARROWS', Tools::getValue('SLIDER_ARROWS'));
            Configuration::updateValue('PAGINATION_BUTTONS', Tools::getValue('PAGINATION_BUTTONS'));
            Configuration::updateValue('AUTOPLAY_SLIDER', Tools::getValue('AUTOPLAY_SLIDER'));
            Configuration::updateValue('EVENTS_META_MAPKEY', Tools::getValue('EVENTS_META_MAPKEY'));
            Configuration::updateValue('FME_WAIT_MIN', Tools::getValue('FME_WAIT_MIN'));
            Configuration::updateValue('EVENT_SHOW_TIMESTAMP', Tools::getValue('EVENT_SHOW_TIMESTAMP'));
            Configuration::updateValue('EVENT_SHOW_LIVE_STREAMING', Tools::getValue('EVENT_SHOW_LIVE_STREAMING'));
            Configuration::updateValue('EM_VS', Tools::getValue('EM_VS'));

            return $this->displayConfirmation($this->l('The settings have been updated.'));
        }

        return '';
    }

    public function getContent()
    {
        $this->context->smarty->assign('ps_version', _PS_VERSION_);
        $this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
        $btn_link = $this->context->link->getAdminLink('AdminEvents');
        $this->context->smarty->assign('btn_link', $btn_link);
        $this->button = $this->display(__FILE__, 'views/templates/hook/button.tpl');

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
                $this->html = $this->display(__FILE__, 'views/templates/admin/fme_popup.tpl');
            }
        }

        return $this->postProcess() . $this->html . $this->button . $this->renderForm();
    }

    public function renderForm()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=') == true) {
            $status_admin = [
                'type' => 'switch',
                'label' => $this->l('Enable Module?'),
                'name' => 'EVENTS_ENABLE_DISABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin2 = [
                'type' => 'switch',
                'label' => $this->l('Enable Left Column Block?'),
                'name' => 'EVENTS_CONF_BLOCK_SETTINGS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem2_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem2_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_product = [
                'type' => 'switch',
                'label' => $this->l('Enable SeatMap on Product page'),
                'name' => 'FME_ENABLE_PRODUCT',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmepro_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmepro_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin_phone = [
                'type' => 'switch',
                'label' => $this->l('Required Customer Phone'),
                'name' => 'FME_REQ_PHONE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem_phone_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem_phone_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin3 = [
                'type' => 'switch',
                'label' => $this->l('Show Map on hover address?'),
                'name' => 'EVENTS_SHOW_MAP_HOVER_ADDRESS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem3_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem3_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin4 = [
                'type' => 'switch',
                'label' => $this->l('Show YouTube Video on Event Detail?'),
                'name' => 'EVENTS_SHOW_YOUTUBE_VIDEO',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem4_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem4_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin5 = [
                'type' => 'switch',
                'label' => $this->l('Show Social Sharing on Event Detail?'),
                'name' => 'EVENTS_SHARING_OPTIONS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem5_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem5_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin5 = [
                'type' => 'switch',
                'label' => $this->l('Show Social Sharing on Event Detail?'),
                'name' => 'EVENTS_SHARING_OPTIONS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem5_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem5_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin6 = [
                'type' => 'switch',
                'label' => $this->l('Show Gallery Images on Event Detail?'),
                'name' => 'EVENTS_SHOW_GALLERY',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem6_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem6_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin7 = [
                'type' => 'switch',
                'label' => $this->l('Enable thumbnails on Slider?'),
                'name' => 'THUMBNAILS_ENABLE_DISABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'fmmem7_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem7_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin8 = [
                'type' => 'switch',
                'label' => $this->l('Enable Arrows on Slider?'),
                'name' => 'SLIDER_ARROWS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'fmmem8_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem8_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin9 = [
                'type' => 'switch',
                'label' => $this->l('Enable pagination on Slider?'),
                'name' => 'PAGINATION_BUTTONS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'fmmem9_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem9_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin10 = [
                'type' => 'switch',
                'label' => $this->l('Autoplay Slider?'),
                'name' => 'AUTOPLAY_SLIDER',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'fmmem10_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem10_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin11 = [
                'type' => 'switch',
                'label' => $this->l('Show Time with Event?'),
                'name' => 'EVENT_SHOW_TIMESTAMP',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem11_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem11_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin12 = [
                'type' => 'switch',
                'label' => $this->l('Enable Live Streaming'),
                'name' => 'EVENT_SHOW_LIVE_STREAMING',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem12_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem12_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $status_admin13 = [
                'type' => 'switch',
                'label' => $this->l('Enable Events Tags'),
                'name' => 'EVENTS_TAGS_ENABLE_DISABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'fmmem13_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'fmmem13_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
            $event_video_status = [
                'type' => 'switch',
                'label' => $this->l('Show full width Video (Modern Theme)'),
                'name' => 'EM_VS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'event_video_status_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'event_video_status_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];
        } else {
            $status_admin = [
                'type' => 'radio',
                'label' => $this->l('Enable Module?'),
                'name' => 'EVENTS_ENABLE_DISABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin2 = [
                'type' => 'radio',
                'label' => $this->l('Enable Left Column Block?'),
                'name' => 'EVENTS_CONF_BLOCK_SETTINGS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];

            $status_product = [
                'type' => 'radio',
                'label' => $this->l('Enable SeatMap on Product page'),
                'name' => 'FME_ENABLE_PRODUCT',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];

            $status_admin_phone = [
                'type' => 'radio',
                'label' => $this->l('Required Customer Phone'),
                'name' => 'FME_REQ_PHONE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin3 = [
                'type' => 'radio',
                'label' => $this->l('Show Map on hover address?'),
                'name' => 'EVENTS_SHOW_MAP_HOVER_ADDRESS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin4 = [
                'type' => 'radio',
                'label' => $this->l('Show YouTube Video on Event Detail?'),
                'name' => 'EVENTS_SHOW_YOUTUBE_VIDEO',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin5 = [
                'type' => 'radio',
                'label' => $this->l('Show Social Sharing on Event Detail?'),
                'name' => 'EVENTS_SHARING_OPTIONS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin6 = [
                'type' => 'radio',
                'label' => $this->l('Show Gallery Images on Event Detail?'),
                'name' => 'EVENTS_SHOW_GALLERY',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin7 = [
                'type' => 'radio',
                'label' => $this->l('Enable thumbnails on Slider?'),
                'name' => 'THUMBNAILS_ENABLE_DISABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin8 = [
                'type' => 'radio',
                'label' => $this->l('Enable Arrows on Slider?'),
                'name' => 'SLIDER_ARROWS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin9 = [
                'type' => 'radio',
                'label' => $this->l('Enable pagination on Slider?'),
                'name' => 'PAGINATION_BUTTONS',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin10 = [
                'type' => 'radio',
                'label' => $this->l('Autoplay Slider?'),
                'name' => 'AUTOPLAY_SLIDER',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'gallery',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin11 = [
                'type' => 'radio',
                'label' => $this->l('Show Time with Event?'),
                'name' => 'EVENT_SHOW_TIMESTAMP',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin12 = [
                'type' => 'radio',
                'label' => $this->l('Enable Live Streaming?'),
                'name' => 'EVENT_SHOW_LIVE_STREAMING',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
            $status_admin13 = [
                'type' => 'radio',
                'label' => $this->l('Enable Live Streaming?'),
                'name' => 'EVENTS_TAGS_ENABLE_DISABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'tab' => 'general',
                'values' => [
                    [
                        'id' => 'active13_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active13_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ];
        }

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuration Settings'),
                    'icon' => 'icon-cogs',
                ],
                'tabs' => [
                    'general' => $this->l('General Settings'),
                    'gallery' => $this->l('Gallery Settings'),
                ],
                'input' => [
                    $status_admin,
                    $status_admin2,
                    $status_product,

                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Page Title'),
                        'name' => 'EVENTS_PAGE_TITLE',
                        'desc' => $this->l('Enter Heading for the page'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('META Keywords'),
                        'name' => 'EVENTS_META_KEYWORDS',
                        'desc' => $this->l('Enter meta keywords for SEO'),
                        'tab' => 'general',

                    ],
                    [
                        'type' => 'textarea',
                        'lang' => true,
                        'label' => $this->l('META Description'),
                        'name' => 'EVENTS_META_DESCRIPTION',
                        'desc' => $this->l('Enter meta Description for SEO'),
                        'class' => 'rte',
                        'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                        'tab' => 'general',

                    ],
                    [
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Google Map API Key'),
                        'name' => 'EVENTS_META_MAPKEY',
                        'desc' => '
                        <a href="https://developers.google.com/maps/documentation/static-maps/" target="_blank">' .
                        $this->l('click here to get your MAP key..') . '</a>',
                        'col' => '7',
                        'tab' => 'general',

                    ],
                    [
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Waiting Minutes'),
                        'name' => 'FME_WAIT_MIN',
                        'col' => '7',
                        'tab' => 'general',

                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Sort By'),
                        'name' => 'EVENTS_SORT_BY',
                        'tab' => 'general',

                        'values' => [
                            [
                                'id' => 'st_date',
                                'value' => 0,
                                'label' => $this->l('Start Date'),
                            ],
                            [
                                'id' => 'end_date',
                                'value' => 1,
                                'label' => $this->l('End Date'),
                            ],
                            [
                                'id' => 'name',
                                'value' => 2,
                                'label' => $this->l('Name'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Sort Order'),
                        'name' => 'EVENTS_SORT_ORDER',
                        'tab' => 'general',

                        'values' => [
                            [
                                'id' => 'st_asc',
                                'value' => 0,
                                'label' => $this->l('Ascending'),
                            ],
                            [
                                'id' => 'end_dsc',
                                'value' => 1,
                                'label' => $this->l('Descending'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Theme'),
                        'name' => 'EVENTS_THEME',
                        'tab' => 'general',

                        'values' => [
                            [
                                'id' => 'theme_a',
                                'value' => 0,
                                'label' => $this->l('Classic'),
                            ],
                            [
                                'id' => 'theme_b',
                                'value' => 1,
                                'label' => $this->l('Modern'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Events Per Page'),
                        'name' => 'EVENTS_PER_PAGE',
                        'tab' => 'general',

                    ],
                    $status_admin3,
                    $status_admin4,
                    $status_admin5,
                    $status_admin6,
                    $status_admin11,
                    $status_admin12,
                    $status_admin13,
                    $status_admin_phone,
                    $event_video_status,

                    [
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Slider Width'),
                        'name' => 'SLIDER_WIDTH',
                        'suffix' => 'pixels',
                        'tab' => 'gallery',
                    ],
                    [
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Slider Height'),
                        'name' => 'SLIDER_HEIGHT',
                        'suffix' => 'pixels',
                        'tab' => 'gallery',

                    ],
                    $status_admin7,
                    $status_admin8,
                    $status_admin9,
                    $status_admin10,
                
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPquote';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
        '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $url = '';
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $router = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('router');
 
            $url = $router->generate('admin_module_configure_action', [
                'module_name' => $this->name,
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

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages();
        $fields = [];
        foreach ($languages as $lang) {
            $fields['EVENTS_PAGE_TITLE'][$lang['id_lang']] = Tools::getValue(
                'EVENTS_PAGE_TITLE_' . $lang['id_lang'],
                Configuration::get(
                    'EVENTS_PAGE_TITLE',
                    $lang['id_lang']
                )
            );
            $fields['EVENTS_META_KEYWORDS'][$lang['id_lang']] = Tools::getValue(
                'EVENTS_META_KEYWORDS_' . $lang['id_lang'],
                Configuration::get(
                    'EVENTS_META_KEYWORDS',
                    $lang['id_lang']
                )
            );
            $fields['EVENTS_META_DESCRIPTION'][$lang['id_lang']] = Tools::getValue(
                'EVENTS_META_DESCRIPTION_' . $lang['id_lang'],
                Configuration::get(
                    'EVENTS_META_DESCRIPTION',
                    $lang['id_lang']
                )
            );
        }

        $fields['EVENTS_ENABLE_DISABLE'] = (int) Configuration::get('EVENTS_ENABLE_DISABLE');
        $fields['EVENTS_TAGS_ENABLE_DISABLE'] = (int) Configuration::get('EVENTS_TAGS_ENABLE_DISABLE');
        $fields['FME_REQ_PHONE'] = (int) Configuration::get('FME_REQ_PHONE');
        $fields['EVENTS_CONF_BLOCK_SETTINGS'] = (int) Configuration::get('EVENTS_CONF_BLOCK_SETTINGS');
        $fields['FME_ENABLE_PRODUCT'] = (int) Configuration::get('FME_ENABLE_PRODUCT');
        $fields['EVENTS_SORT_BY'] = (int) Configuration::get('EVENTS_SORT_BY');
        $fields['EVENTS_SORT_ORDER'] = (int) Configuration::get('EVENTS_SORT_ORDER');
        $fields['EVENTS_THEME'] = (int) Configuration::get('EVENTS_THEME');
        $fields['EVENTS_PER_PAGE'] = (int) Configuration::get('EVENTS_PER_PAGE');
        $fields['EVENTS_SHOW_MAP_HOVER_ADDRESS'] = (int) Configuration::get('EVENTS_SHOW_MAP_HOVER_ADDRESS');
        $fields['EVENTS_SHOW_YOUTUBE_VIDEO'] = (int) Configuration::get('EVENTS_SHOW_YOUTUBE_VIDEO');
        $fields['EVENTS_SHARING_OPTIONS'] = (int) Configuration::get('EVENTS_SHARING_OPTIONS');
        $fields['EVENTS_SHOW_GALLERY'] = (int) Configuration::get('EVENTS_SHOW_GALLERY');
        $fields['SLIDER_WIDTH'] = (int) Configuration::get('SLIDER_WIDTH');
        $fields['SLIDER_HEIGHT'] = (int) Configuration::get('SLIDER_HEIGHT');
        $fields['THUMBNAILS_ENABLE_DISABLE'] = (int) Configuration::get('THUMBNAILS_ENABLE_DISABLE');
        $fields['SLIDER_ARROWS'] = (int) Configuration::get('SLIDER_ARROWS');
        $fields['PAGINATION_BUTTONS'] = (int) Configuration::get('PAGINATION_BUTTONS');
        $fields['AUTOPLAY_SLIDER'] = (int) Configuration::get('AUTOPLAY_SLIDER');
        $fields['EVENT_SHOW_TIMESTAMP'] = (int) Configuration::get('EVENT_SHOW_TIMESTAMP');
        $fields['EVENT_SHOW_LIVE_STREAMING'] = (int) Configuration::get('EVENT_SHOW_LIVE_STREAMING');
        $fields['EM_VS'] = (int) Configuration::get('EM_VS');
        $fields['EVENTS_META_MAPKEY'] = (string) Configuration::get('EVENTS_META_MAPKEY');
        $fields['FME_WAIT_MIN'] = (string) Configuration::get('FME_WAIT_MIN');

        return $fields;
    }

    public function hookDisplayHeader($params)
    {
        $events_theme = (int) Configuration::get('EVENTS_THEME');
        $this->context->controller->addCSS($this->_path . 'views/css/events_bootstrap.css');
        $this->context->controller->addCSS($this->_path . 'views/css/fmeevents.css');
        $this->context->controller->addCSS($this->_path . 'views/css/print.css');
        if ($events_theme > 0) {
            $this->context->controller->addCSS($this->_path . 'views/css/magento.css');
        }
        Media::addJsDef([
            'event_id' => null,
            'is_all' => Tools::getValue('ipp'),//changed
            'set_width' => (int) Configuration::get('SLIDER_WIDTH'),
            'set_height' => (int) Configuration::get('SLIDER_HEIGHT'),
            'set_arrows' => (int) Configuration::get('SLIDER_ARROWS'),
            'set_buttons' => (int) Configuration::get('PAGINATION_BUTTONS'),
            'set_thumbnailArrows' => (int) Configuration::get('THUMBNAILS_ENABLE_DISABLE'),
            'set_autoplay' => (int) Configuration::get('AUTOPLAY_SLIDER'),
            'fmm_theme' => (int) Configuration::get('EVENTS_THEME'),
            'static_map_key' => pSQL(Configuration::get('EVENTS_META_MAPKEY')),
            'show_map_hover' => Configuration::get('EVENTS_SHOW_MAP_HOVER_ADDRESS'),
            'controller' => Dispatcher::getInstance()->getController(),
            'unavailable' => $this->l('Ticket(s) not Available at the moment'),
        ]);
        $this->context->controller->addCSS($this->_path . 'views/css/slider-pro.min.css');
        $this->context->controller->addJqueryPlugin('fancybox');
        $this->context->controller->addJS($this->_path . 'views/js/jquery.sliderPro.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/sweetalert2.all.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addJS($this->_path . 'views/js/popupscript.js');
    }

    public function hookDisplayLeftColumn($params)
    {
        if (Configuration::get('EVENTS_CONF_BLOCK_SETTINGS') == 1) {
            $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
            $data = Events::getAllFrontEvents(Context::getContext()->language->id);
            $this->context->smarty->assign('leftdata', $data);
            $this->context->smarty->assign('img_dir', _PS_IMG_DIR_);
            $this->context->smarty->assign('version', _PS_VERSION_);
            $this->context->smarty->assign('ps_ver', $PS_VERSION);

            return $this->display(__FILE__, 'left-column.tpl');
        } else {
            return false;
        }
    }

    public function hookDisplayEvents($params)
    {
        if (Configuration::get('EVENTS_CONF_BLOCK_SETTINGS') == 1) {
            $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
            $data = Events::getAllFrontEvents(Context::getContext()->language->id);
            $this->context->smarty->assign('leftdata', $data);
            $this->context->smarty->assign('img_dir', _PS_IMG_DIR_);
            $this->context->smarty->assign('version', _PS_VERSION_);
            $this->context->smarty->assign('ps_ver', $PS_VERSION);

            return $this->display(__FILE__, 'homepage.tpl');
        } else {
            return false;
        }
    }

    public function hookDisplayMyAccountBlock()
    {
        return $this->hookDisplayCustomerAccount();
    }

    public function hookDisplayCustomerAccount()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            return $this->display(__FILE__, 'my-tickets.tpl');
        } else {
            return $this->display(__FILE__, 'my-tickets16.tpl');
        }
    }

    public function hookModuleRoutes()
    {
        return [
            'module-eventsmanager-events' => [
                'controller' => 'events',
                'rule' => 'events',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'eventsmanager',
                ],
            ],
            'module-eventsmanager-detail' => [
                'controller' => 'events',
                'rule' => 'events{/:event_id}-{eventslink}',
                'keywords' => [
                    'event_id' => ['regexp' => '[0-9]+', 'param' => 'event_id'],
                    'eventslink' => ['regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'eventslink'],
                    'module_action' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module_action'],
                    'module' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'],
                    'controller' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'eventsmanager',
                    'controller' => 'events',
                ],
            ],
            'module-eventsmanager-eventstag' => [
                'controller' => 'events',
                'rule' => 'events/tags{/:id_tag}-{tags}',
                'keywords' => [
                    'id_tag' => ['regexp' => '[0-9]+', 'param' => 'id_tag'],
                    'tags' => ['regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'tags'],
                    'module_action' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module_action'],
                    'module' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'],
                    'controller' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'eventsmanager',
                    'controller' => 'events',
                ],
            ],
            'module-eventsmanager-calendar' => [
                'controller' => 'events',
                'rule' => 'events{/:show}',
                'keywords' => [
                    'show' => ['regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'show'],
                    'module_action' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module_action'],
                    'module' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'],
                    'controller' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'eventsmanager',
                    'controller' => 'events',
                ],
            ],
        ];
    }

    public function hookDisplayPaymentTop()
    {
        $p_id_array = [];
        $products_ids = Events::getActiveEventProductsRecords();
        foreach ($products_ids as $key => $value) {
            $p_id_array[] = (int) $value['id_product'];
        }
        $products = Context::getContext()->cart->getProducts();
        $product_ids = [];
        foreach ($products as $product) {
            $product_ids[] = (int) $product['id_product'];
        }
        $match_products = array_intersect($p_id_array, $product_ids);
        $response_Record = [];
        $events_in_cart = [];
        foreach ($match_products as $key) {
            $all_records = Events::getRecordsByIdProduct($key);
            foreach ($all_records as $key => $value) {
                $id_event = $value['event_id'];
                $result = Events::isEnable($id_event);

                if ($result == 1) {
                    $final_key = $key;
                }
            }
            $dates = Events::getEventDate($all_records[$final_key]['event_id']);
            $start_d = $dates[0];
            $end_d = $dates[1];
            $start = date_create($start_d);
            $end = date_create($end_d);
            $total_days = date_diff($start, $end);
            $total_days = $total_days->format('%a');
            $all_records[$final_key]['days'] = $total_days;
            $response_Record[] = $all_records[$final_key];
            $events_in_cart[] = $all_records[$final_key]['event_id'];
        }
        $products_in_cart = implode(',', $match_products);
        $map_event_in_cart = 0;
        foreach ($events_in_cart as $cart_key => $cart_val) {
            $cart_key = $cart_key;
            $res = Events::isEnableSeatMap($cart_val);
            if ($res) {
                ++$map_event_in_cart;
            }
        }
        $events_in_cart = implode(',', $events_in_cart);
        $ajax_url = $this->context->link->getModuleLink(
            $this->name,
            'ajax',
            ['token' => md5(_COOKIE_KEY_)]
        );
        $blue_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/checked.png';
        $red_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/red.png';
        $green_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/green.png';
        $is_logged = $this->context->customer->logged;
        $customer_id = $this->context->customer->id;
        $id_cart = $this->context->cart->id;
        // $customer_name = $this->context->customer->firstname;
        $customer_name = trim($this->context->customer->firstname . ' ' . $this->context->customer->lastname);
        $this->context->smarty->assign('ps_version', _PS_VERSION_);
        $this->context->smarty->assign('map_event_in_cart', $map_event_in_cart);
        $this->context->smarty->assign('ajax_url', $ajax_url);
        $this->context->smarty->assign('id_customer', $customer_id);
        $this->context->smarty->assign('id_guest', $this->context->cookie->id_guest);
        $this->context->smarty->assign('match_products', $response_Record);
        $this->context->smarty->assign('products_in_cart', $products_in_cart);
        $this->context->smarty->assign('events_in_cart', $events_in_cart);
        $this->context->smarty->assign('is_logged', $is_logged);
        $this->context->smarty->assign('id_cart', $id_cart);
        $this->context->smarty->assign('blue_color', $blue_color);
        $this->context->smarty->assign('red_color', $red_color);
        $this->context->smarty->assign('green_color', $green_color);
        $this->context->smarty->assign('customer_name', $customer_name);
        $wait_min = Configuration::get('FME_WAIT_MIN');
        $this->context->smarty->assign('wait_min', $wait_min);
        $req_phone = Configuration::get('FME_REQ_PHONE');
        $this->context->smarty->assign('req_phone', $req_phone);
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            return $this->fetch('module:eventsmanager/views/templates/hook/payment_top.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/hook/payment_top.tpl');
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = Dispatcher::getInstance()->getController();
        if ($controller == 'AdminEvents' || $controller == 'AdminEventsDetails') {
            if (Tools::version_compare(_PS_VERSION_, '1.7.7.0', '<') == true) {
                $this->context->controller->addJquery();
            }
            Media::addJsDef([
                'controller' => $controller,
            ]);
            $this->context->controller->addJS($this->_path . 'views/js/sweetalert2.all.min.js');
        }
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $url = '';
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $router = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('router');
 
            $url = $router->generate('admin_module_configure_action', [
                'module_name' => $this->name,
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

    public function hookActionValidateOrder($params)
    {
        $mail = 0;
        $p_id_array = [];
        $products_ids = Events::getAllBookingProductsRecords();
        foreach ($products_ids as $key) {
            $p_id_array[] = (int) $key['id_product'];
        }
        $products = $params['cart']->getProducts(true);
        $product_ids = [];
        foreach ($products as $product) {
            $product_ids[] = (int) $product['id_product'];
        }
        $id_cart = $params['cart']->id;
        $id_customer = $params['cart']->id_customer;
        $order_state = $params['orderStatus']->id;
        $id_order = (int) $params['order']->id;
        $match_products = array_intersect($p_id_array, $product_ids);
        foreach ($match_products as $id_product) {
            $if_exist = FmeCustomerModel::ifExistCustomerCart($id_product, $id_customer, $id_cart);
            $id_customer_event = $if_exist[0]['id_events_customer'];
            $obj_booking_customer = new FmeCustomerModel($id_customer_event);
            $obj_booking_customer->order_status = $order_state;
            $obj_booking_customer->id_order = $id_order;
            $result = $obj_booking_customer->update();
            $mail = 1;
            $result = $result;
        }
        $customer_mail = $params['customer']->email;
        $customer_mail = $customer_mail;
        $firstname = $params['customer']->firstname;
        $firstname = $firstname;
    }

    public function initTokenSecure()
    {
        $now = date('Y-m-d H:i:s');
        $time_string = strtotime($now);
        $time_string = md5(_COOKIE_KEY_ . $time_string);
        Configuration::updateValue('EVENT_MANAGER__ACCESS_TOKEN', $time_string);

        return $time_string;
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        $id_product = $params['product']->id;
        $all_products = Events::getEventByIdProduct($id_product);
        $id_event = 0;
        foreach ($all_products as $key) {
            $isenable = Events::isEnable($key['event_id']);
            if ($isenable == 1) {
                $id_event = $key['event_id'];
            }
        }
        $expairy_date = Events::getExpairy($id_event);
        if (!$expairy_date) {
            $expairy_date = 0;
        }
        $today = date('Y/m/d H:i:s');
        $hide_btn = 0;
        if ($today > $expairy_date) {
            $hide_btn = 1;
        }
        $this->context->smarty->assign('hide_btn', $hide_btn);
        $products_ids = Events::getActiveBookingProductsRecords();
        $show = 0;
        foreach ($products_ids as $key) {
            if ($id_product == $key['id_product']) {
                $show = 1;
            }
        }
        $ajax_url = $this->context->link->getModuleLink(
            $this->name,
            'ajax',
            ['token' => md5(_COOKIE_KEY_)]
        );
        $match_products = Events::getRecordsByIdProduct($id_product);
        $qty = Context::getContext()->cart->getProducts(false, $id_product);
        $enable_product_map = Configuration::get('FMM_ENABLE_PRODUCT');
        $this->context->smarty->assign('enable_product_map', $enable_product_map);
        $customer_name = $this->context->customer->firstname;
        $is_logged = $this->context->customer->logged;
        $customer_id = $this->context->customer->id;
        $blue_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/checked.png';
        $red_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/red.png';
        $green_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/green.png';
        $this->context->smarty->assign(
            [
                'ps_version' => _PS_VERSION_,
                'is_logged' => $is_logged,
                'id_product' => $id_product,
                'match_products' => $match_products,
                'ajax_url' => $ajax_url,
                'blue_color' => $blue_color,
                'red_color' => $red_color,
                'id_customer' => $customer_id,
                'green_color' => $green_color,
                'qty' => $qty,
                'customer_name' => $customer_name,
            ]
        );
        $wait_min = Configuration::get('FME_WAIT_MIN');
        $this->context->smarty->assign('wait_min', $wait_min);
        $req_phone = Configuration::get('FMM_REQ_PHONE');
        $this->context->smarty->assign('req_phone', $req_phone);
        $enable_product_map = Configuration::get('FMM_ENABLE_PRODUCT');
        $this->context->smarty->assign('enable_product_map', $enable_product_map);
        $id_cart = $this->context->cart->id;
        $this->context->smarty->assign('id_guest', $this->context->cookie->id_guest);
        $this->context->smarty->assign('id_cart', $id_cart);
        $all_records = Events::getRecordsByIdProduct($id_product);
        $enable_product_map = Configuration::get('FME_ENABLE_PRODUCT');
        if ($all_records) {
            $events_in_cart = $all_records[0]['event_id'];
        } else {
            $events_in_cart = '';
        }
        $this->context->smarty->assign('events_in_cart', $events_in_cart);
        if ($show == 1 && $enable_product_map == 1) {
            $id_lang = $this->context->language->id;
            $product_info = Configuration::get('FMM_PRODUCT_PAGE_INFO', (int) $id_lang);
            $product_info = Tools::htmlentitiesUTF8($product_info);
            $this->context->smarty->assign('product_info', $product_info);
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $output = $this->fetch(
                    $this->local_path . 'views/templates/hook/product_detail_info.tpl'
                );
                $output_map = $this->fetch(
                    $this->local_path . 'views/templates/hook/product_map_17.tpl'
                );
            } else {
                $output = $this->display(
                    __FILE__,
                    'views/templates/hook/product_detail_info.tpl'
                );
                $output_map = $this->display(
                    __FILE__,
                    'views/templates/hook/product_map_16.tpl'
                );
            }

            return $output . $output_map;
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        $id_order = $params['id_order'];
        if ($id_order && Validate::isLoadedObject($order = new Order($id_order))) {
            $events_data = FmeCustomerModel::getEventsByOrder((int) $order->id, $order->id_lang);
            $this->context->smarty->assign([
                'storeOrder' => $order,
                'events_data' => $events_data,
            ]);

            return $this->display(dirname(__FILE__), 'views/templates/admin/order-events-details.tpl');
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $orderState = $params['newOrderStatus'];
        $status_id = $orderState->id;
        $order = new Order((int) $params['id_order']);
        $order_id = $order->id;
        FmeCustomerModel::updateOrderStatus($order_id, $status_id);
    }

    // public function addAdditionalTab($tabClass, $id_parent)
    // {
    //     $id_parent = $id_parent;
    //     $subtab3 = new Tab();
    //     $subtab3->class_name = 'AdminTags';
    //     $subtab3->id_parent = Tab::getIdFromClassName($tabClass);
    //     $subtab3->module = $this->tabModule;
    //     $subtab3->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Events Tags');
    //     if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
    //         $subtab3->icon = 'event';
    //     }
    //     $subtab3->add();

    //     return true;
    // }

    public function ajaxProcessFmePopupAction()
    {
        $action = Tools::getValue('fme_action');
        $result = ['success' => false, 'message' => 'Action failed'];

        if ($action == 'popupcompleted') {
            if (Configuration::get($this->name . '_INSTALL_DATE')) {
                Configuration::deleteByName($this->name . '_INSTALL_DATE', date('Y-m-d'));
            }
            if (Configuration::get($this->name . '_FME_POPUP_REMINDER')) {
                Configuration::deleteByName($this->name . '_FME_POPUP_REMINDER', date('Y-m-d'));
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
