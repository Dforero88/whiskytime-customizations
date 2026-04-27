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
function upgrade_module_4_0_0($module)
{
    if (!$module->installTopLink()) {
        return false;
    }
    if(!$module->registerHook('actionProductGridQueryBuilderModifier')){
        $module->registerHook('actionProductGridQueryBuilderModifier');
    }
    if (!Configuration::hasKey('GIFT_CATEGORY_SHOW_MENU')) {
        Configuration::updateValue('GIFT_CATEGORY_SHOW_MENU', 1);
    }
    if (!Configuration::hasKey('GIFT_CARD_CUSTOMIZATION_ENABLED')) {
        Configuration::updateValue('GIFT_CARD_CUSTOMIZATION_ENABLED', 1);
    }

    $return = true;
    $subtabs = [
        'GiftCard',
        'AdminGiftCards',
        'AdminGiftTemplates',
        'AdminOrderedGiftcards',
        'AdminGiftSettings',
    ];

    foreach ($subtabs as $class_name) {
        $id_tab = Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $return &= $tab->delete();
        }
    }
    $tab = new Tab();
    $tab->id_parent = 0;
    $tab->module = $module->name;
    $tab->class_name = $module->tab_class;
    $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->displayName;
    if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
        $tab->icon = 'card_giftcard';
    }

    if ($tab->add()) {
        $subtab1 = new Tab();
        $subtab1->id_parent = $tab->id;
        $subtab1->module = $module->name;
        $subtab1->class_name = 'AdminGiftCardsCategory';
        $subtab1->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Gift Card Category');
        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $subtab1->icon = 'settings';
        }
        $return &= $subtab1->add();

        $subtab2 = new Tab();
        $subtab2->id_parent = $tab->id;
        $subtab2->module = $module->name;
        $subtab2->class_name = 'AdminGiftCards';
        $subtab2->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Gift Cards');
        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $subtab2->icon = 'redeem';
        }
        $return &= $subtab2->add();

        $subtab3 = new Tab();
        $subtab3->id_parent = $tab->id;
        $subtab3->module = $module->name;
        $subtab3->class_name = 'AdminGiftCardsImageTemplate';
        $subtab3->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Gift Cards Image Template');
        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $subtab3->icon = 'redeem';
        }
        $return &= $subtab3->add();

        $subtab4 = new Tab();
        $subtab4->id_parent = $tab->id;
        $subtab4->module = $module->name;
        $subtab4->class_name = 'AdminGiftTemplates';
        $subtab4->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Email Templates');
        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $subtab4->icon = 'settings';
        }
        $return &= $subtab4->add();

        $subtab5 = new Tab();
        $subtab5->id_parent = $tab->id;
        $subtab5->module = $module->name;
        $subtab5->class_name = 'AdminOrderedGiftcards';
        $subtab5->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Ordered Giftcards');
        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $subtab5->icon = 'add_shopping_cart';
        }
        $return &= $subtab5->add();

        $subtab6 = new Tab();
        $subtab6->id_parent = $tab->id;
        $subtab6->module = $module->name;
        $subtab6->class_name = 'AdminGiftSettings';
        $subtab6->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Settings');
        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $subtab6->icon = 'settings';
        }
        $return &= $subtab6->add();
    }

    $sql = [];

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_video_temp` (
        `id_temp_video` INT(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_customer` INT(10) unsigned NOT NULL,
        `id_guest` INT(10) unsigned DEFAULT NULL,
        `id_product` INT(10) unsigned NOT NULL,
        `video_link` TEXT NOT NULL,
        `video_name` VARCHAR(255) NOT NULL,
        `type`                  VARCHAR(50) NOT NULL,  -- Specify the type of video link (e.g., "embedded" or "file")
        `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- Time when the video link was added
        PRIMARY KEY             (`id_temp_video`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'giftcard_image_template` (
            `id_giftcard_image_template`              INT(10) unsigned NOT NULL AUTO_INCREMENT,
            `gc_image` VARCHAR(255) NOT NULL,
            `price` INT(10) NOT NULL DEFAULT 0,
            `discount_code` VARCHAR(255) NOT NULL DEFAULT "",
            `bg_color` VARCHAR(255) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 0,
            -- `preview` TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY             (`id_giftcard_image_template`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'giftcard_image_template_lang` (
            `id_giftcard_image_template` INT(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_lang` INT(11) UNSIGNED NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            -- `tags` VARCHAR(255) NOT NULL,
            `template_text` VARCHAR(255) NOT NULL,
            PRIMARY KEY             (`id_giftcard_image_template`, `id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'giftcard_image_template_shop`(
        `id_giftcard_image_template`         INT(10) unsigned NOT NULL,
        `id_shop`                       INT(10) unsigned NOT NULL,
        PRIMARY KEY                     (`id_giftcard_image_template`, `id_shop`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'gift_card`
        ADD `gcp_image_type` VARCHAR(255) NOT NULL DEFAULT "image" AFTER `card_name`';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) == false) {
            return false;
        }
    }

     if(!$module->addtemplates()){
        return false;
    }
    
    return $return;
}
