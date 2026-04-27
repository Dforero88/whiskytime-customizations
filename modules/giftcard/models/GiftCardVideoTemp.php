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
class GiftCardVideoTemp extends ObjectModel
{
    public $id_temp_video;

    public $id_customer;

    public $id_guest;

    public $id_product;

    public $video_link;

    public $video_name;

    public $type;

    public $created_at;

    public static $definition = [
        'table' => 'gift_card_video_temp',
        'primary' => 'id_temp_video',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_guest' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'video_link' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'required' => true],
            'video_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 50],
            'created_at' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public function getTempVideoById($id_customer, $id_guest = null)
    {
        if (!$id_customer) {
            if (!$id_guest) {
                return false;
            }
        }
        $temp_video = Db::getInstance()->executeS('
            SELECT gcvp.*
            FROM `' . _DB_PREFIX_ . 'gift_card_video_temp` gcvp
            WHERE gcvp.id_customer = ' . (int) $id_customer . '
            AND gcvp.id_guest = ' . (int) $id_guest);

        return $temp_video;
    }

    public static function getTempMediaById($id_video, $id_customer, $id_guest)
    {
        $sql = '
            SELECT * 
            FROM ' . _DB_PREFIX_ . 'gift_card_video_temp 
            WHERE id_temp_video = ' . (int) $id_video . '
            AND id_customer = ' . (int) $id_customer . '
            AND id_guest = ' . (int) $id_guest;

        return Db::getInstance()->getRow($sql);
    }

    public static function getAllTempVideos()
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gift_card_video_temp`');
    }
}
