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
class GiftTemplates extends ObjectModel
{
    public $id_template;

    public $status;

    public $template_name;

    public $content;

    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => 'gift_card_template',
        'primary' => 'id_gift_card_template',
        'multilang' => true,
        'fields' => [
            'template_name' => ['type' => self::TYPE_STRING, 'validate' => 'isCatalogName'],
            'status' => ['type' => self::TYPE_INT, 'validate' => 'isBool'],
            'content' => ['type' => self::TYPE_HTML, 'lang' => true],
            'date_add' => ['type' => self::TYPE_STRING, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_STRING, 'validate' => 'isDate'],
        ],
    ];

    public function add($autodate = true, $null_values = false)
    {
        $this->template_name = Tools::toUnderscoreCase(Tools::toCamelCase($this->template_name));

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->template_name = Tools::toUnderscoreCase(Tools::toCamelCase($this->template_name));

        return parent::update($null_values);
    }

    public static function getTemplate($id_cart, $id_order, $type = 'home')
    {
        if (!$id_order || !$id_cart) {
            return [];
        } else {
            $sql = new DbQuery();
            $sql->select('ogc.*, cp.`quantity` AS cart_quantity');
            $sql->from('ordered_gift_cards', 'ogc');
            $sql->leftJoin('cart_product', 'cp', 'ogc.id_product = cp.id_product AND ogc.id_cart = cp.id_cart');
            $sql->where('ogc.has_voucher = 0');
            $sql->where('ogc.gift_type = "' . pSQL($type) . '"');
            $sql->where('ogc.id_cart = ' . (int) $id_cart);
            $sql->where('ogc.id_order = ' . (int) $id_order);

            return Db::getInstance()->executeS($sql);
        }
    }

    public static function getTemplates($active = true, $id_lang = null, $id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $sql = new DbQuery();
        $sql->select('gt.*, gtl.*');
        $sql->from(self::$definition['table'], 'gt');
        $sql->leftJoin(
            self::$definition['table'] . '_lang',
            'gtl',
            'gt.id_gift_card_template = gtl.id_gift_card_template AND gtl.id_lang = ' . (int) $id_lang
        );

        if (Shop::isFeatureActive()) {
            $sql->leftJoin(
                self::$definition['table'] . '_shop',
                'gts',
                'gt.id_gift_card_template = gts.id_gift_card_template AND gts.id_shop = ' . (int) $id_shop
            );
        }

        if ($active) {
            $sql->where('gt.status = 1');
        }

        $sql->where('gtl.id_lang = ' . (int) $id_lang);

        $templates = [];
        $templates = Db::getInstance()->executeS($sql);
        if (isset($templates)) {
            foreach ($templates as &$template) {
                $fileName = sprintf('giftcard_template_%d_%d', $template['id_gift_card_template'], $id_shop);
                $tempFile = _PS_TMP_IMG_DIR_ . $fileName;
                $actualFile = GiftTemplates::checkFile($tempFile);
                $thumb = sprintf('%smodules/giftcard/views/img/dummy.png', __PS_BASE_URI__);
                if (file_exists($actualFile)) {
                    $templateName = pathinfo($actualFile, PATHINFO_BASENAME);
                    $thumb = sprintf('%simg/tmp/%s', __PS_BASE_URI__, $templateName);
                }
                $template['thumb'] = $thumb;
            }
        }

        return $templates;
    }

    public static function checkFile($name)
    {
        // reads informations over the path
        $info = pathinfo($name);
        if (!empty($info['extension'])) {
            // if the file already contains an extension returns it
            return $name;
        }
        $filename = $info['filename'];
        $len = Tools::strlen($filename);
        // open the folder
        $dh = opendir($info['dirname']);
        if (!$dh) {
            return false;
        }
        // scan each file in the folder
        while (($file = readdir($dh)) !== false) {
            if (strncmp($file, $filename, $len) === 0) {
                if (Tools::strlen($name) > $len) {
                    // if name contains a directory part
                    $name = Tools::substr($name, 0, Tools::strlen($name) - $len) . $file;
                } else {
                    // if the name is at the path root
                    $name = $file;
                }
                closedir($dh);

                return $name;
            }
        }
        // file not found
        closedir($dh);

        return false;
    }

    public static function createTemplateTables()
    {
        $sql = [];
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

        $return = true;
        foreach ($sql as $query) {
            $return &= Db::getInstance()->execute($query);
        }

        return $return;
    }
}
