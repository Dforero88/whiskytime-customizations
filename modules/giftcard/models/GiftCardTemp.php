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
class GiftCardImageTemplateModel extends ObjectModel
{
    public $id_giftcard_image_template;
    public $price;
    public $discount_code;
    public $bg_color;
    public $active;
    public $name;
    // public $tags;
    public $gc_image;
    public $template_text;

    public static $definition = [
        'table' => 'giftcard_image_template',
        'primary' => 'id_giftcard_image_template',
        'multilang' => true,
        'multishop' => true,
        'fields' => [
            'price' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'discount_code' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'bg_color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true, 'size' => 255],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 255],
            // 'tags' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'gc_image' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'template_text' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
        ],
    ];

    public static function getAllTemplates($idLang = null, $idShop = null, $activeOnly = false)
    {
        $context = Context::getContext();

        if ($idLang === null) {
            $idLang = (int) $context->language->id;
        }

        if ($idShop === null) {
            $idShop = (int) $context->shop->id;
        }

        $alias = 'a';
        $langAlias = 'b';
        $shopAlias = 's';

        $sql = new DbQuery();
        $sql->select("{$alias}.*, {$langAlias}.name, {$langAlias}.template_text");
        $sql->from(self::$definition['table'], $alias);
        $sql->leftJoin(
            self::$definition['table'] . '_lang',
            $langAlias,
            "{$alias}." . self::$definition['primary'] . " = {$langAlias}." . self::$definition['primary'] .
            " AND {$langAlias}.id_lang = " . (int) $idLang
        );

        if (Shop::isFeatureActive()) {
            $sql->leftJoin(
                self::$definition['table'] . '_shop',
                $shopAlias,
                "{$alias}." . self::$definition['primary'] . " = {$shopAlias}." . self::$definition['primary'] .
                " AND {$shopAlias}.id_shop = " . (int) $idShop
            );
        }

        if ($activeOnly) {
            $sql->where("{$alias}.active = 1");
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getTemplateByTemplateName($filename, $idLang = null, $idShop = null, $activeOnly = false)
    {
        $context = Context::getContext();

        if ($idLang === null) {
            $idLang = (int) $context->language->id;
        }

        if ($idShop === null) {
            $idShop = (int) $context->shop->id;
        }

        $alias = 'a';
        $langAlias = 'b';
        $shopAlias = 's';

        $sql = new DbQuery();
        $sql->select("{$alias}.*, {$langAlias}.name, {$langAlias}.template_text");
        $sql->from(self::$definition['table'], $alias);
        $sql->leftJoin(
            self::$definition['table'] . '_lang',
            $langAlias,
            "{$alias}." . self::$definition['primary'] . " = {$langAlias}." . self::$definition['primary'] .
            " AND {$langAlias}.id_lang = " . (int) $idLang
        );

        if (Shop::isFeatureActive()) {
            $sql->leftJoin(
                self::$definition['table'] . '_shop',
                $shopAlias,
                "{$alias}." . self::$definition['primary'] . " = {$shopAlias}." . self::$definition['primary'] .
                " AND {$shopAlias}.id_shop = " . (int) $idShop
            );
        }

        if ($activeOnly) {
            $sql->where("{$alias}.active = 1");
        }

        return Db::getInstance()->getRow($sql);
    }
}
