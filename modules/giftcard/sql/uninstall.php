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

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card_video_links`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card_customer`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ordered_gift_cards`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card_shop`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card_template`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card_template_lang`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card_template_shop`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gift_card_video_temp`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'giftcard_image_template`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'giftcard_image_template_lang`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'giftcard_image_template_shop`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
