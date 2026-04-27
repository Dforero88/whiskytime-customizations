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
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card`(
        `id_gift_card`          INT(10) unsigned NOT NULL auto_increment,
        `id_product`            INT(10) unsigned NOT NULL,
        `id_discount_product`   INT(10) unsigned NOT NULL,
        `id_attribute`          INT(10)  unsigned NOT NULL,
        `card_name`             TEXT,
        `gcp_image_type`        VARCHAR(255) NOT NULL DEFAULT "image",
        `qty`                   INT(10),
        `status`                INT(2),
        `length`                INT(10),
        `free_shipping`         INT(2),
        `partial_use`           INT(2),
        `from`                  DATE,
        `to`                    DATE,
        `value_type`            TEXT,
        `card_value`            TEXT,
        `vcode_type`            TEXT,
        `reduction_type`        TEXT,
        `reduction_amount`      TEXT,
        `reduction_currency`    INT(10),
        `reduction_tax`         INT(2),
        `validity_period`       INT(10) NOT NULL DEFAULT 0,
        `validity_type`         VARCHAR(64) NOT NULL DEFAULT \'days\',
        PRIMARY KEY             (`id_gift_card`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_customer`(
        `id_cart_rule`          INT(10) unsigned NOT NULL,
        `id_customer`           INT(10) unsigned NOT NULL,
        `id_cart`               INT(10) unsigned,
        `id_order`              INT(10) unsigned,
        `id_product`            INT(10) unsigned,
        `link_rewrite`          TEXT,
        `id_image`              INT(10) unsigned,
        `expiry_alert`          TINYINT(2) NOT NULL DEFAULT 0,
        PRIMARY KEY             (`id_cart_rule`, `id_customer`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ordered_gift_cards`(
        `id_cart`               INT(10) unsigned NOT NULL,
        `id_order`              INT(10) unsigned NOT NULL,
        `id_gift_card_template` INT(10) unsigned NOT NULL DEFAULT 0,
        `id_product`            INT(10),
        `id_customer`           INT(10) DEFAULT 0,
        `reference_product`     INT(10),
        `has_voucher`           TINYINT(2) DEFAULT 0,
        `gift_type`             VARCHAR(20) NOT NULL DEFAULT \'home\',
        `friend_name`           VARCHAR(100),
        `friend_email`          VARCHAR(100),
        `gift_message`          TEXT,
        `specific_date`         DATE,
        PRIMARY KEY             (`id_cart`, `id_product`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_shop`(
        `id_gift_card`          INT(10) unsigned NOT NULL,
        `id_shop`               INT(10) unsigned NOT NULL,
        PRIMARY KEY             (`id_gift_card`, `id_shop`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_template`(
        `id_gift_card_template`         INT(10) unsigned NOT NULL auto_increment,
        `template_name`                 VARCHAR(250),
        `status`                        INT(2),
        `date_add`                      DATE,
        `date_upd`                      DATE,
        PRIMARY KEY                     (`id_gift_card_template`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_template_lang`(
        `id_gift_card_template`         INT(10) unsigned NOT NULL,
        `id_lang`                       INT(10) unsigned NOT NULL,
        `content`                       TEXT,
        PRIMARY KEY                     (`id_gift_card_template`, `id_lang`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_template_shop`(
        `id_gift_card_template`         INT(10) unsigned NOT NULL,
        `id_shop`                       INT(10) unsigned NOT NULL,
        PRIMARY KEY                     (`id_gift_card_template`, `id_shop`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gift_card_video_links` (
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

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
