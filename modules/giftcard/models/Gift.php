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
class Gift extends ObjectModel
{
    public $id_product;

    public $id_discount_product;

    public $id_attribute;

    public $gcp_image_type;

    public $card_name;

    public $qty;

    public $status;

    public $length;

    public $free_shipping;

    public $partial_use;

    public $validity_period;

    public $validity_type;

    public $from;

    public $to;

    public $value_type;

    public $card_value;

    public $vcode_type;

    public $reduction_type;

    public $reduction_amount;

    public $reduction_currency;

    public $reduction_tax;

    public static $definition = [
        'table' => 'gift_card',
        'primary' => 'id_gift_card',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_discount_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'card_name' => ['type' => self::TYPE_STRING],
            'gcp_image_type' => ['type' => self::TYPE_STRING],
            'qty' => ['type' => self::TYPE_INT],
            'status' => ['type' => self::TYPE_INT],
            'length' => ['type' => self::TYPE_INT],
            'free_shipping' => ['type' => self::TYPE_INT],
            'partial_use' => ['type' => self::TYPE_INT],
            'validity_period' => ['type' => self::TYPE_INT],
            'validity_type' => ['type' => self::TYPE_STRING],
            'value_type' => ['type' => self::TYPE_STRING],
            'card_value' => ['type' => self::TYPE_STRING],
            'vcode_type' => ['type' => self::TYPE_STRING],
            'reduction_type' => ['type' => self::TYPE_STRING],
            'reduction_amount' => ['type' => self::TYPE_STRING],
            'reduction_currency' => ['type' => self::TYPE_INT],
            'reduction_tax' => ['type' => self::TYPE_INT],
        ],
    ];

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            return true;
        }

        return false;
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            return true;
        }

        return false;
    }

    public function delete()
    {
        if (Validate::isLoadedObject($giftProduct = new Product($this->id_product))) {
            $giftProduct->delete();
        }
        if (parent::delete()) {
            return true;
        }

        return false;
    }

    public static function isExists($id_product)
    {
        if (!$id_product) {
            return false;
        }

        return (bool) Db::getInstance()->getValue('SELECT `id_product`
            FROM `' . _DB_PREFIX_ . 'product` WHERE id_product = ' . (int) $id_product);
    }

    public static function hasBaseProduct($id_product, $return_value = false)
    {
        if (!$id_product) {
            return false;
        }

        $sql = 'SELECT op.`reference_product`
            FROM `' . _DB_PREFIX_ . 'ordered_gift_cards` op
            LEFT JOIN `' . _DB_PREFIX_ . 'gift_card` gc ON (op.reference_product = gc.id_product)
            WHERE op.id_product = ' . (int) $id_product;

        if ($return_value) {
            return (int) Db::getInstance()->getValue($sql);
        } else {
            return (bool) Db::getInstance()->getValue($sql);
        }
    }

    public static function disableChildProduct($id_product)
    {
        if (!$id_product) {
            return false;
        } else {
            return (bool) Db::getInstance()->update(
                'product',
                ['active' => 0],
                'id_product = ' . (int) $id_product
            );
        }
    }

    public static function isInOrderCart($id_product)
    {
        if (!$id_product) {
            return false;
        }

        return (bool) Db::getInstance()->getRow('SELECT *
            FROM `' . _DB_PREFIX_ . 'ordered_gift_cards`
            WHERE id_product = ' . (int) $id_product);
    }

    public static function getGiftCardType($id_product)
    {
        if (!$id_product) {
            return false;
        }

        return (string) Db::getInstance()->getValue('SELECT `value_type`
            FROM `' . _DB_PREFIX_ . 'gift_card`
            WHERE id_product = ' . (int) $id_product);
    }

    public function insertGiftCard($id_product, $card_name, $qty, $to, $from, $status, $length, $card_value, $value_type, $free_shipping, $id_discount_product, $reduction_type, $reduction_amount, $reduction_tax, $id_attribute, $reduction_currency, $vcode_type = 'ALPHANUMERIC' ) {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'gift_card` (
        `id_product`,
        `card_name`,
        `qty`,
        `from`,
        `to`,
        `status`,
        `length`,
        `card_value`,
        `value_type`,
        `free_shipping`,
        `id_discount_product`,
        `reduction_type`,
        `reduction_amount`,
        `reduction_tax`,
        `id_attribute`,
        `reduction_currency`,
        `vcode_type`
        )
        VALUES('
        . (int) $id_product . ',
        "' . pSQL($card_name) . '",
        ' . (int) $qty . ',
        "' . pSQL($from) . '",
        "' . pSQL($to) . '",
        ' . (int) $status . ',
        ' . (int) $length . ',
        "' . pSQL($card_value) . '",
        "' . pSQL($value_type) . '",
        ' . (int) $free_shipping . ',
        ' . (int) $id_discount_product . ',
        "' . pSQL($reduction_type) . '",
        "' . pSQL($reduction_amount) . '",
        ' . (int) $reduction_tax . ',
        ' . (int) $id_attribute . ',
        ' . (int) $reduction_currency . ',
        "' . pSQL($vcode_type) . '"
        )';

        if (Db::getInstance()->execute($sql)) {
            return Db::getInstance()->Insert_ID();
        }
    }

    public static function insertCustomer( $id_cart_rule, $id_cart, $id_order, $id_product, $id_customer, $link_rewrite, $id_image) {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'gift_card_customer` (
        `id_cart_rule`,
        `id_cart`,
        `id_order`,
        `id_product`,
        `id_customer`,
        `link_rewrite`,
        `id_image`
        )
        VALUES(
        ' . (int) $id_cart_rule . ',
        ' . (int) $id_cart . ',
        ' . (int) $id_order . ',
        ' . (int) $id_product . ',
        ' . (int) $id_customer . ',
        "' . pSQL($link_rewrite) . '",
        ' . (int) $id_image . '
        )';

        if (Db::getInstance()->execute($sql)) {
            return Db::getInstance()->Insert_ID();
        }
    }

    public static function orderGC($data)
    {
        if (!Db::getInstance()->insert('ordered_gift_cards', $data, false, false, Db::ON_DUPLICATE_KEY)) {
            return false;
        }

        return true;
    }

    public static function updateCartGC($id_cart, $id_order, $id_product, $id_customer)
    {
        if (!$id_cart || !$id_product) {
            return false;
        } else {
            return (bool) Db::getInstance()->update(
                'ordered_gift_cards',
                ['id_order' => (int) $id_order, 'id_customer' => (int) $id_customer],
                'id_cart = ' . (int) $id_cart . ' AND id_product = ' . (int) $id_product
            );
        }
    }

    public function updateGiftCard($id_gift_card, $id_product, $card_name, $qty, $to, $from, $status, $length, $card_value, $value_type, $free_shipping, $id_discount_product, $reduction_type, $reduction_amount, $reduction_tax, $reduction_currency, $vcode_type) {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'gift_card`
            SET `card_name`         = "' . pSQL($card_name) . '",
            `qty`                   = ' . (int) $qty . ',
            `from`                  = "' . pSQL($from) . '",
            `to`                    = "' . pSQL($to) . '",
            `status`                = ' . (int) $status . ',
            `length`                = ' . (int) $length . ',
            `vcode_type`            = "' . pSQL($vcode_type) . '",
            `card_value`            = "' . pSQL($card_value) . '",
            `value_type`            = "' . pSQL($value_type) . '",
            `free_shipping`         = ' . (int) $free_shipping . ',
            `id_discount_product`   = "' . (int) $id_discount_product . '",
            `reduction_type`        = "' . pSQL($reduction_type) . '",
            `reduction_amount`      = "' . pSQL($reduction_amount) . '",
            `reduction_tax`         = ' . (int) $reduction_tax . ',
            `reduction_currency`    = ' . (int) $reduction_currency . '
            WHERE `id_gift_card`    = ' . (int) $id_gift_card . '
            AND `id_product`        = ' . (int) $id_product;

        if (Db::getInstance()->execute($sql)) {
            return true;
        }

        return false;
    }

    public function updateProductQty($id_product, $qty)
    {
        return (bool) Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'stock_available`
            SET `quantity` =' . (int) $qty . '
            WHERE id_product = ' . (int) $id_product);
    }

    public function setProductPrice($id_product, $price, $name, $id_lang)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'product`
            SET `price` =' . (float) $price . '
            WHERE id_product = ' . (int) $id_product;

        $qry = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
            SET `name` = "' . pSQL($name) . '"
            WHERE id_product = ' . (int) $id_product . '
            AND id_lang = ' . (int) $id_lang;

        if (Db::getInstance()->execute($sql) && Db::getInstance()->execute($qry)) {
            return true;
        }
    }

    public function setCategory($id_product)
    {
        $pos = 0;
        $id_category = 2;
        $pos = (int) Gift::getPosition($id_category);
        ++$pos;
        $sql = 'INSERT INTO`' . _DB_PREFIX_ . 'category_product`(`id_category`, `id_product`, `position`)
        VALUES(' . (int) $id_category . ', ' . (int) $id_product . ', ' . (int) $pos . ')';

        if (Db::getInstance()->execute($sql)) {
            return Db::getInstance()->Insert_ID();
        }
    }

    public function setVoucherQty($id_cart_rule, $qty)
    {
        return (bool) Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'cart_rule`
            SET `quantity` =' . (int) $qty . '
            WHERE id_cart_rule = ' . (int) $id_cart_rule);
    }

    public function getPosition($id_category)
    {
        return (int) Db::getInstance()->getValue('SELECT MAX(position) AS pos
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE id_category = ' . (int) $id_category);
    }

    public static function getId_image($id_product)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_image`
            FROM `' . _DB_PREFIX_ . 'image` WHERE cover = 1
            AND id_product = ' . (int) $id_product);
    }

    public function getIdCartRule($vcode)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_cart_rule`
            FROM `' . _DB_PREFIX_ . 'cart_rule`
            WHERE code = "' . pSQL($vcode) . '"');
    }

    public static function getProductDetail($id_product, $id_cart, $id_order, $id_customer)
    {
        return Db::getInstance()->getRow('SELECT gc.*, ogc.*, p.`price`
            FROM `' . _DB_PREFIX_ . 'ordered_gift_cards` ogc
            LEFT JOIN `' . _DB_PREFIX_ . 'gift_card` gc
                ON (gc.id_product = ogc.reference_product)
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p
                ON (ogc.id_product = p.id_product)
            WHERE ogc.id_product = ' . (int) $id_product . '
            AND ogc.id_cart = ' . (int) $id_cart . '
            AND ogc.id_order = ' . (int) $id_order . '
            AND ogc.id_customer = ' . (int) $id_customer);
    }

    public function getAllVouchers($has_voucher = false, $id_lang = null, $id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        // Retrieve only expired vouchers (assuming 'date_to' is the expiration field)
        return Db::getInstance()->executeS('SELECT cr.*,
            pl.`name`, pl.`link_rewrite`,
            gcc.`id_customer`, gcc.`id_product`, gcc.`expiry_alert`,
            image_shop.`id_image` AS id_image, ogc.*
            FROM `' . _DB_PREFIX_ . 'cart_rule` cr
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl
                ON (cr.`id_cart_rule` = crl.id_cart_rule AND crl.id_lang = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'gift_card_customer` gcc
                ON (cr.`id_cart_rule` = gcc.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'ordered_gift_cards` ogc
                ON (ogc.`id_cart` = gcc.id_cart AND gcc.id_product = ogc.id_product)
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (gcc.`id_product` = i.`id_product`)
            ' . Shop::addSqlAssociation('image', 'i', true, 'image_shop.`cover` = 1') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (gcc.`id_product` = pl.`id_product` AND pl.id_lang = ' . (int) $id_lang . ' AND pl.id_shop = ' . (int) $id_shop . ')
            WHERE ogc.gift_type = "home"
            AND cr.quantity > 0
            AND ogc.has_voucher = ' . (int) $has_voucher); // Condition to filter expired vouchers
    }

    public static function getAllVideos()
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gift_card_video_links`');
    }

    public static function updateExpiryAlert($id_cart_rule)
    {
        // Prepare the SQL query
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'gift_card_customer` 
                SET `expiry_alert` = 1 
                WHERE `id_cart_rule` = ' . (int) $id_cart_rule;

        return Db::getInstance()->execute($sql);
    }

    public function getVoucherByCustomerId($id_customer, $has_voucher = false, $id_lang = null, $id_shop = null)
    {
        if (!$id_customer) {
            return false;
        }

        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        // Fetch the vouchers
        $vouchers = Db::getInstance()->executeS('
            SELECT cr.*, pl.`name`, pl.`link_rewrite`,
                gcc.`id_customer`, gcc.`id_product`,
                image_shop.`id_image` AS id_image, ogc.*
            FROM `' . _DB_PREFIX_ . 'cart_rule` cr
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl
                ON (cr.`id_cart_rule` = crl.id_cart_rule AND crl.id_lang = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'gift_card_customer` gcc
                ON (cr.`id_cart_rule` = gcc.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'ordered_gift_cards` ogc
                ON (ogc.`id_cart` = gcc.id_cart AND gcc.id_product = ogc.id_product)
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i
                ON (gcc.`id_product` = i.`id_product`)
            ' . Shop::addSqlAssociation('image', 'i', true, 'image_shop.`cover` = 1') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (gcc.`id_product` = pl.`id_product` AND pl.id_lang = ' . (int) $id_lang . ' AND pl.id_shop = ' . (int) $id_shop . ')
            WHERE (ogc.gift_type = "home" OR ogc.gift_type = "sendsomeone")
            AND cr.quantity > 0
            AND ogc.has_voucher = ' . (int) $has_voucher . '
            AND gcc.id_customer = ' . (int) $id_customer
        );

        // Check if video upload is enabled in configuration
        $is_video_enabled = Configuration::get('GIFT_VIDEO_UPLOAD_ENABLED');

        // If video is enabled, fetch id_video for each voucher
        if ($is_video_enabled && !empty($vouchers)) {
            $voucher_ids = array_column($vouchers, 'id_cart_rule');
            $id_videos = Db::getInstance()->executeS('
                SELECT id_cart_rule, id_video
                FROM `' . _DB_PREFIX_ . 'gift_card_video_links`
                WHERE id_cart_rule IN (' . implode(',', array_map('intval', $voucher_ids)) . ')
            ');

            // Create an associative array for quick lookup
            $video_lookup = [];
            foreach ($id_videos as $video) {
                $video_lookup[$video['id_cart_rule']] = $video['id_video'];
            }

            // Add id_video to each voucher
            foreach ($vouchers as &$voucher) {
                $voucher['id_video'] = isset($video_lookup[$voucher['id_cart_rule']]) ? $video_lookup[$voucher['id_cart_rule']] : null;
            }
        }

        return $vouchers;
    }

    public function getVoucherByOrderId($id_order, $has_voucher = false, $id_lang = null, $id_shop = null)
    {
        if (!$id_order) {
            return false;
        }

        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS('SELECT cr.*,
            pl.`name`, pl.`link_rewrite`,
            gcc.`id_customer`, gcc.`id_product`,
            image_shop.`id_image` AS id_image, ogc.*
            FROM `' . _DB_PREFIX_ . 'cart_rule` cr
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl
                ON (cr.`id_cart_rule` = crl.id_cart_rule AND crl.id_lang = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'gift_card_customer` gcc
                ON (cr.`id_cart_rule` = gcc.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'ordered_gift_cards` ogc
                ON (ogc.`id_cart` = gcc.id_cart AND gcc.id_product = ogc.id_product)
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (gcc.`id_product` = i.`id_product`)
            ' . Shop::addSqlAssociation('image', 'i', true, 'image_shop.`cover` = 1') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (gcc.`id_product` = pl.`id_product` AND pl.id_lang = ' . (int) $id_lang . ' AND pl.id_shop = ' . (int) $id_shop . ')
            WHERE ogc.gift_type = "home"
            AND cr.quantity > 0
            AND ogc.has_voucher = ' . (int) $has_voucher . '
            AND gcc.id_order = ' . (int) $id_order);
    }

    public static function getAllCards($id_lang)
    {
        $row = Db::getInstance()->executeS('SELECT *
            FROM `' . _DB_PREFIX_ . 'gift_card` gc
            INNER JOIN `' . _DB_PREFIX_ . 'product` p
            ON (gc.id_product = p.id_product)');

        if ($row) {
            foreach ($row as &$result) {
                $product = new Product($result['id_product'], true, (int) $id_lang);
                $currency = new Currency($result['reduction_currency']);
                $image = Product::getCover($result['id_product']);
                $result['id_image'] = $image['id_image'];
                $result['link_rewrite'] = $product->link_rewrite;
                $result['iso_code'] = $currency->iso_code;
                $result['giftcard_product'] = (array) $product;
            }
        }

        return $row;
    }

    public static function getGiftCard($id_product, $id_gift_card, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $result = Db::getInstance()->getRow('SELECT *
            FROM `' . _DB_PREFIX_ . 'gift_card`
            Where id_product = ' . (int) $id_product . '
            AND id_gift_card = ' . (int) $id_gift_card);

        if (isset($result)) {
            $result['discount_product'] = Product::getProductName($result['id_discount_product'], null, (int) $id_lang);
        }

        return $result;
    }

    public function deleteCard($id_gift_card, $id_product)
    {
        return (bool) Db::getInstance()->execute('DELETE gc.*, p.*
            FROM `' . _DB_PREFIX_ . 'gift_card` gc
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p
                ON gc.id_product = p.id_product
            WHERE gc.id_product =' . (int) $id_product . '
            AND gc.id_gift_card =' . (int) $id_gift_card);
    }

    public static function deleteByProduct($id_product)
    {
        if (!$id_product) {
            return false;
        }

        return (bool) Db::getInstance()->Execute('DELETE gc.*, gcs.*, gco.*
            FROM `' . _DB_PREFIX_ . 'gift_card` gc
            LEFT JOIN `' . _DB_PREFIX_ . 'gift_card_shop` gcs ON (gc.id_gift_card = gcs.id_gift_card)
            LEFT JOIN `' . _DB_PREFIX_ . 'ordered_gift_cards` gco ON (gc.id_product = gco.id_product)
            WHERE gc.id_product = ' . (int) $id_product);
    }

    public static function updateGiftCardField($field_name, $id_product, $value)
    {
        if (empty($field_name) || !$id_product) {
            return false;
        }

        return (bool) Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'gift_card`
            SET `' . $field_name . '` = ' . (int) $value . ' WHERE id_product = ' . (int) $id_product);
    }

    public static function hideGiftProductPrice($id_product, $show_price = false)
    {
        if (!$id_product) {
            return false;
        }

        return (bool) (Db::getInstance()->update(
            'product',
            ['show_price' => (bool) $show_price],
            'id_product = ' . (int) $id_product
        )
            && Db::getInstance()->update(
                'product_shop',
                ['show_price' => (bool) $show_price],
                'id_product = ' . (int) $id_product
            ));
    }

    public static function getCardValue($id_product)
    {
        return Db::getInstance()->getRow('SELECT card_value, value_type
            FROM `' . _DB_PREFIX_ . 'gift_card`
            Where id_product = ' . (int) $id_product . '
            ORDER BY id_product');
    }

    public static function getCartsRuleById($id_cart_rule, $id_lang)
    {
        return Db::getInstance()->getRow('SELECT cr.*, crl.*
            FROM ' . _DB_PREFIX_ . 'cart_rule cr
            LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl
            ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ' .
            (int) $id_lang . ')
            WHERE cr.id_cart_rule = ' . (int) $id_cart_rule);
    }

    public static function getMediaById($id_video)
    {
        return Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'gift_card_video_links WHERE id_video = ' . (int) $id_video);
    }

    public static function getCustomerById($id_customer)
    {
        return Db::getInstance()->executeS('SELECT `email`, `firstname`, `lastname`
            FROM `' . _DB_PREFIX_ . 'customer`
            WHERE id_customer = ' . (int) $id_customer);
    }

    public static function getOrderIdsByCartId($id_cart)
    {
        return (int) Db::getInstance()->getValue('SELECT id_order
            FROM ' . _DB_PREFIX_ . 'orders WHERE `id_cart` = ' . (int) $id_cart);
    }

    public static function getOrderedGiftCards($id_customer)
    {
        return Db::getInstance()->ExecuteS('SELECT DISTINCT(id_cart)
            FROM ' . _DB_PREFIX_ . 'ordered_gift_cards
            WHERE id_order NOT IN (
                SELECT id_order
                FROM ' . _DB_PREFIX_ . 'gift_card_customer
                WHERE id_customer = ' . (int) $id_customer . ')');
    }

    public static function sendAlert($rules, $id_customer)
    {
        if (isset($rules) && $id_customer) {
            $id_lang = Context::getContext()->language->id;
            $module = Module::getInstanceByName('giftcard');
            if (Customer::customerIdExistsStatic($id_customer)) {
                $customer = Gift::getCustomerById($id_customer);
            }

            $html = '';
            if (!empty($customer)) {
                Context::getContext()->smarty->assign([
                    'rules' => $rules,
                    'id_lang' => $id_lang,
                ]);
                $html .= Context::getContext()->smarty->fetch($module->getLocalPath() . 'views/templates/admin/gift_alert.tpl');

                $customer = array_shift($customer);
                $template_vars = [
                    '{email}' => $customer['email'],
                    '{lname}' => $customer['lastname'],
                    '{fname}' => $customer['firstname'],
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{detail}' => $html,
                ];

                $result = Mail::Send(
                    (int) $id_lang,
                    'my_giftcards',
                    Mail::l('Your Purchased Gift cards', (int) $id_lang),
                    $template_vars,
                    $customer['email'],
                    null,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_ . 'giftcard/mails/',
                    false
                );

                return $result;
            }
        }
    }

    public static function removeAssocShops($id_product)
    {
        if (!$id_product) {
            return false;
        }

        return (bool) Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'product_shop`
            WHERE id_product = ' . (int) $id_product);
    }

    public static function updateGiftShops( $id_product, $id_shop, $id_category_default = 2, $id_tax_rules_group = 0, $active = 1, $price = 0.0) {
        return (bool) Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'product_shop`(
            `id_product`,
            `id_shop`,
            `id_category_default`,
            `id_tax_rules_group`,
            `redirect_type`,
            `active`,
            `price`,
            `date_add`
            )
            VALUES(
            ' . (int) $id_product . ',
            ' . (int) $id_shop . ',
            ' . (int) $id_category_default . ',
            ' . (int) $id_tax_rules_group . ',
            "404",
            ' . (int) $active . ',
            ' . (float) $price . ',
            NOW())
        ');
    }

    public static function getShopsByProduct($id_product)
    {
        $row = Db::getInstance()->executeS('SELECT `id_shop`
            FROM `' . _DB_PREFIX_ . 'product_shop`
            WHERE `id_product` = ' . (int) $id_product . '
            GROUP BY `id_shop`');

        $result = [];
        if ($row) {
            foreach ($row as $res) {
                $result[] = $res['id_shop'];
            }
        }

        return $result;
    }

    public static function getOrderStateHistory($id_order)
    {
        $result = Db::getInstance()->ExecuteS('SELECT `id_order_state`
            FROM `' . _DB_PREFIX_ . 'order_history`
            WHERE `id_order` = ' . (int) $id_order);

        $final = [];
        if ($result) {
            foreach ($result as $res) {
                $final[] = (int) $res['id_order_state'];
            }
        }

        return $final;
    }

    public static function restrictVoucherToShop($id_cart_rule, $id_shop)
    {
        $row = ['id_cart_rule' => (int) $id_cart_rule, 'id_shop' => (int) $id_shop];

        return Db::getInstance()->insert('cart_rule_shop', $row, false, true, Db::INSERT_IGNORE);
    }

    public static function getIdProductFromCard($id_gift_card)
    {
        if (!$id_gift_card) {
            return false;
        } else {
            return (int) Db::getInstance()->getValue('SELECT id_product from `' . _DB_PREFIX_ . 'gift_card`
            WHERE id_gift_card = ' . (int) $id_gift_card);
        }
    }

    public static function addGiftMetaLabel($id_product, $label, $languages = [], $type = Product::CUSTOMIZE_TEXTFIELD)
    {
        if (!$id_product) {
            return false;
        }

        if (!isset($languages) || !$languages) {
            $languages = Language::getLanguages();
        }

        // Label insertion
        if (!Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'customization_field` (`id_product`, `type`, `required`)
            VALUES (' . (int) $id_product . ', ' . (int) $type . ', 0)')
            || !$id_customization_field = (int) Db::getInstance()->Insert_ID()) {
            return false;
        }

        // Multilingual label name creation
        $values = '';
        foreach ($languages as $language) {
            foreach (Shop::getContextListShopID() as $id_shop) {
                $values .= '(' . (int) $id_customization_field . ', ' . (int) $language['id_lang'] . ', ' . (int) $id_shop . ',"' . pSQL($label) . '"), ';
            }
        }
        $values = rtrim($values, ', ');
        if (!Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'customization_field_lang`
            (`id_customization_field`, `id_lang`, `id_shop`, `name`) VALUES ' . $values)) {
            return false;
        }
        // Set cache of feature detachable to true
        Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', '1');

        return $id_customization_field;
    }

    public static function addGiftCustomization($id_cart, $id_product, $index, $field, $quantity = 0, $type = Product::CUSTOMIZE_TEXTFIELD, $id_product_attribute = 0 ) {
        $exising_customization = Db::getInstance()->executeS('
            SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.id_cart = ' . (int) $id_cart . '
            AND cu.id_product = ' . (int) $id_product . '
            AND in_cart = 1');

        if ($exising_customization) {
            // If the customization field is alreay filled, delete it
            foreach ($exising_customization as $customization) {
                if ($customization['type'] == $type && $customization['index'] == $index) {
                    Db::getInstance()->execute('
                        DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
                        WHERE id_customization = ' . (int) $customization['id_customization'] . '
                        AND type = ' . (int) $customization['type'] . '
                        AND `index` = ' . (int) $customization['index']);
                    if ($type == Product::CUSTOMIZE_FILE) {
                        @unlink(_PS_UPLOAD_DIR_ . $customization['value']);
                        @unlink(_PS_UPLOAD_DIR_ . $customization['value'] . '_small');
                    }
                    break;
                }
            }
            $id_customization = $exising_customization[0]['id_customization'];
        } else {
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`, `in_cart`)
                VALUES (' . (int) $id_cart . ', ' . (int) $id_product . ', ' . (int) $id_product_attribute . ', ' . (int) $quantity . ', 1)'
            );
            $id_customization = Db::getInstance()->Insert_ID();
        }

        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'customized_data` (`id_customization`, `type`, `index`, `value`)
            VALUES (' . (int) $id_customization . ', ' . (int) $type . ', ' . (int) $index . ', \'' . pSQL($field) . '\')';

        if (!Db::getInstance()->execute($query)) {
            return false;
        }

        return $id_customization;
    }

    public static function removeCartGP($id_cart, $id_product)
    {
        if (!$id_cart || !$id_product) {
            return false;
        } else {
            $where = 'id_cart = ' . (int) $id_cart . ' AND id_product = ' . (int) $id_product;
            Db::getInstance()->delete('ordered_gift_cards', $where);
            /* if (Db::getInstance()->delete('ordered_gift_cards', $where)) {
                if (Validate::isLoadedObject($gp = new Product((int) $id_product))) {
                    $gp->delete();

                    return true;
                }
            }

            return false; */
        }
    }

    public static function getOrderedGiftProducts($id_cart, $where = null)
    {
        if (!$id_cart) {
            return false;
        }

        return Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'ordered_gift_cards`
            WHERE id_cart = ' . (int) $id_cart . (($where) ? $where : ''));
    }

    public static function setVoucherFlag($id_cart, $id_order, $id_product, $value = true)
    {
        if (!$id_cart || !$id_order || !$id_product) {
            return false;
        } else {
            $where = 'id_cart = ' . (int) $id_cart . ' AND id_order = ' . (int) $id_order . ' AND id_product = ' . (int) $id_product;

            return (bool) Db::getInstance()->update(
                'ordered_gift_cards',
                ['has_voucher' => (int) $value],
                $where
            );
        }
    }

    public static function getAbandonedGifts($hours = 24)
    {
        return Db::getInstance()->ExecuteS('SELECT og.*, c.`id_cart`, HOUR(SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(NOW(), c.`date_add`)))) AS hours
            FROM ' . _DB_PREFIX_ . 'ordered_gift_cards og
            LEFT JOIN ' . _DB_PREFIX_ . 'cart c ON (og.id_cart = c.id_cart)
            WHERE HOUR(SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(NOW(), c.`date_add`)))) >= ' . (int) $hours . '
            ORDER BY c.date_add ASC');
    }

    public function getPendingGiftCards($id_customer, $id_lang = null, $id_shop = null)
    {
        if (!$id_customer) {
            return false;
        }

        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS('SELECT  ogc.*,
            od.`product_quantity`, od.`product_id` AS `id_product`, od.`product_price`,
            pl.`name`, pl.`link_rewrite`,
            image_shop.`id_image` AS id_image
            FROM `' . _DB_PREFIX_ . 'order_detail` od
            LEFT JOIN `' . _DB_PREFIX_ . 'ordered_gift_cards` ogc
                ON (od.`id_order` = ogc.`id_order` AND ogc.`id_product` = od.`product_id`)
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i
                ON (ogc.`id_product` = i.`id_product`)
            ' . Shop::addSqlAssociation('image', 'i', true, 'image_shop.`cover` = 1') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (ogc.`id_product` = pl.`id_product` AND pl.id_lang = ' . (int) $id_lang . ' AND pl.`id_shop` = ' . (int) $id_shop . ')
            WHERE ogc.gift_type = "home" OR ogc.specific_date IS NOT NULL
            AND ogc.has_voucher = 0
            AND ogc.id_customer = ' . (int) $id_customer);
    }

    /**
     * remove decimal point to convert value into whole number i.e 123.56 -> 123456.
     *
     * @param float $value
     *
     * @return int
     */
    public static function parseFloatValue($value)
    {
        if (strpos($value, '.') !== false) {
            return (int) implode('', explode('.', $value));
        }

        return $value;
    }

    public static function addGcColumn()
    {
        return (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . self::$definition['table'] . '`
            ADD `partial_use` TINYINT(2) NOT NULL DEFAULT 0,
            ADD `validity_period` INT(10) NOT NULL DEFAULT 0,
            ADD `validity_type` VARCHAR(64) NOT NULL DEFAULT \'days\'');
    }

    public static function addGiftCustomerColumn()
    {
        return (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ordered_gift_cards`
        ADD `expiry_alert` TINYINT(2) NOT NULL DEFAULT 0');
    }

    public static function addGcOrderColumn()
    {
        return (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ordered_gift_cards`
            ADD `specific_date` DATE NULL DEFAULT NULL');
    }

    public static function addGcOrderColumnTemplate()
    {
        return (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ordered_gift_cards`
            ADD `id_gift_card_template` INT(10) unsigned NOT NULL DEFAULT 0');
    }

    public static function columnExist($column_name, $table)
    {
        $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
            WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . $table . '"');
        if (isset($columns)) {
            foreach ($columns as $column) {
                if ($column['COLUMN_NAME'] == $column_name) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getParentId($id_product)
    {
        if (!$id_product) {
            return false;
        }

        return (int) Db::getInstance()->getValue('SELECT `id_parent`
            FROM `' . _DB_PREFIX_ . 'ordered_gift_cards`
            WHERE id_product = ' . (int) $id_product);
    }

    public static function getOrderIdByProduct($id_product)
    {
        if (!$id_product) {
            return false;
        }

        return (int) Db::getInstance()->getValue('SELECT `id_order`
            FROM `' . _DB_PREFIX_ . 'ordered_gift_cards`
            WHERE id_product = ' . (int) $id_product);
    }

    public static function getVideoIdByCardtRule($id_cart_rule)
    {
        if (!$id_cart_rule) {
            return false;
        }

        return (int) Db::getInstance()->getValue('SELECT `id_video`
            FROM `' . _DB_PREFIX_ . 'gift_card_video_links`
            WHERE id_cart_rule = ' . (int) $id_cart_rule);
    }

    public static function lookForGiftTable($id)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_gift_card`
        FROM ' . _DB_PREFIX_ . 'gift_card
        WHERE `id_product` = ' . (int) $id);
    }

    public static function getGiftVoucherProducts($id_cart, $id_order, $type = 'home')
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
            if ('sendsomeone' == $type) {
                $sql->where('ogc.specific_date IS NOT NULL');
                $sql->where('NOW() >= ogc.specific_date');
            }

            return Db::getInstance()->executeS($sql);
        }
    }

    public static function getLaterDateGiftCards()
    {
        $approval_states = Configuration::get('GIFT_APPROVAL_STATUS');
        if (empty(trim($approval_states))) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('ogc.*, cp.`quantity` AS cart_quantity');
        $sql->from('ordered_gift_cards', 'ogc');
        $sql->leftJoin('cart_product', 'cp', 'ogc.id_product = cp.id_product AND ogc.id_cart = cp.id_cart');
        $sql->where('ogc.id_order IN (SELECT id_order FROM ' . _DB_PREFIX_ . 'order_history WHERE id_order_state IN (' . pSQL($approval_states) . ') )');
        $sql->where('ogc.specific_date IS NOT NULL');
        $sql->where('NOW() >= ogc.specific_date');
        $sql->where('ogc.has_voucher = 0');
        $sql->where('ogc.gift_type = "sendsomeone"');

        return Db::getInstance()->executeS($sql);
    }

    public static function getGiftCardIds()
    {
        $result = Db::getInstance()->executeS('SELECT DISTINCT(id_product) FROM `' . _DB_PREFIX_ . 'gift_card`
            UNION SELECT DISTINCT(id_product) FROM `' . _DB_PREFIX_ . 'gift_card_customer`
            UNION SELECT DISTINCT(id_product) FROM `' . _DB_PREFIX_ . 'ordered_gift_cards`
            ORDER BY id_product ASC
        ');

        $ids = [];
        if (isset($result)) {
            foreach ($result as $res) {
                $ids[] = (int) $res['id_product'];
            }
        }

        return $ids;
    }

    public static function sendGiftCardExpiringAlert($coupon)
    {
        $customer = new Customer($coupon['id_customer']); // Load the customer object

        // Now you can access customer details, for example:
        $customer_email = $customer->email;
        $customer_firstname = $customer->firstname;
        $customer_lastname = $customer->lastname;
        $id_lang = (int) Context::getContext()->language->id;
        $module = Module::getInstanceByName('giftcard');
        $html = '';
        Context::getContext()->smarty->assign([
            'coupon' => $coupon,
            'id_lang' => $id_lang,
        ]);

        if (Validate::isEmail($customer_email) && Customer::customerExists($customer_email)) {
            $logo = '';
            if (false !== Configuration::get('PS_LOGO_MAIL')
            && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, Context::getContext()->shop->id))
            ) {
                $logo = __PS_BASE_URI__ . '/img/' . Configuration::get('PS_LOGO_MAIL', null, null, Context::getContext()->shop->id);
            } elseif (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, Context::getContext()->shop->id))) {
                $logo = __PS_BASE_URI__ . '/img/' . Configuration::get('PS_LOGO', null, null, Context::getContext()->shop->id);
            }
            $template_vars = [
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{coupon_code}' => $coupon['code'],
                '{fname}' => $customer_firstname,
                '{lname}' => $customer_lastname,
                '{coupon_name}' => $coupon['name'],
                '{shop_logo}' => $logo,
                '{expiry_date}' => $coupon['date_to'],
            ];

            // Send email notification
            Mail::Send(
                (int) $id_lang,
                'expiry_alert',
                Mail::l('Your Gift Coupon is Expiring', (int) $id_lang), // Dynamic subject
                $template_vars,
                $customer_email,
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_ . 'giftcard/mails/',
                false,
                Context::getContext()->shop->id
            );
        }
    }
}
