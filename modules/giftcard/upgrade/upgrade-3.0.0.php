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
function upgrade_module_3_0_0($module)
{
    $result = true;
    $module->alterNewColumns();
    $module->unregisterHook('header');
    $module->unregisterHook('newOrder');
    $module->registerHook('displayheader');
    $module->registerHook('actionValidateOrder');
    Configuration::updateValue('GIFT_VIDEO_SIZE_LIMIT', 2);
    Configuration::updateValue('GIFT_VIDEO_EXPIRY_DAYS', 15);
    Configuration::updateValue('GIFT_EXPIRY_MAIL_TIME', 24);
    Configuration::updateValue($module->name . '_INSTALL_DATE', date('Y-m-d'));
    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_video_links` (
        `id_video`              INT(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_cart_rule`          INT(10) unsigned NOT NULL,
        `video_link`            TEXT NOT NULL,
        `video_name`            VARCHAR(255) NOT NULL,  -- Name of the video file
        `type`                  VARCHAR(50) NOT NULL,  -- Specify the type of video link (e.g., "embedded" or "file")
        `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- Time when the video link was added
        PRIMARY KEY             (`id_video`),
        KEY `id_cart_rule`      (`id_cart_rule`),
        FOREIGN KEY (`id_cart_rule`) 
                REFERENCES `' . _DB_PREFIX_ . 'gift_card_customer`(`id_cart_rule`) 
                ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
    Db::getInstance()->execute($sql);

    return $result;
}
