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
class Events extends ObjectModel
{
    public $id;
    public $event_id;
    public $event_start_date;
    public $event_end_date;
    public $event_image;
    public $image_status;
    public $event_video;
    public $instagram_link;
    public $facebook_link;
    public $twitter_link;
    public $contact_name;
    public $contact_phone;
    public $contact_fax;
    public $contact_email;
    public $contact_address;
    public $event_status;
    public $created_time;
    public $update_time;
    public $event_title;
    public $event_permalinks;
    public $longitude;
    public $latitude;
    public $event_venu;
    public $event_content;
    public $event_page_title;
    public $active;
    public $pdf_status;
    public $event_meta_description;
    public $event_meta_keywords;
    public $event_thumb_image;
    public $event_streaming_start_time;
    public $event_streaming_end_time;
    public $event_streaming;
    public $seat_selection;
    public static $definition = [
        'table' => 'fme_events',
        'primary' => 'event_id',
        'multilang' => true,
        'fields' => [
            'event_start_date' => ['type' => self::TYPE_STRING, 'required' => true],
            'event_end_date' => ['type' => self::TYPE_STRING, 'required' => true],
            'event_streaming_start_time' => ['type' => self::TYPE_STRING],
            'event_streaming_end_time' => ['type' => self::TYPE_STRING],
            'event_image' => ['type' => self::TYPE_STRING],
            'event_video' => ['type' => self::TYPE_STRING],
            'facebook_link' => ['type' => self::TYPE_STRING],
            'twitter_link' => ['type' => self::TYPE_STRING],
            'instagram_link' => ['type' => self::TYPE_STRING],
            'event_streaming' => ['type' => self::TYPE_STRING],
            'contact_name' => ['type' => self::TYPE_STRING],
            'contact_phone' => ['type' => self::TYPE_STRING],
            'contact_fax' => ['type' => self::TYPE_STRING],
            'event_status' => ['type' => self::TYPE_BOOL],
            'pdf_status' => ['type' => self::TYPE_BOOL],
            'image_status' => ['type' => self::TYPE_BOOL],
            'created_time' => ['type' => self::TYPE_DATE],
            'update_time' => ['type' => self::TYPE_DATE],
            'contact_email' => ['type' => self::TYPE_STRING],
            'contact_address' => ['type' => self::TYPE_STRING],
            'event_venu' => ['type' => self::TYPE_STRING],
            'longitude' => ['type' => self::TYPE_STRING],
            'latitude' => ['type' => self::TYPE_STRING],
            'seat_selection' => ['type' => self::TYPE_STRING],
            'event_title' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isString',
                'required' => true,
            ],
            'event_permalinks' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isString',
                'required' => true,
            ],
            'event_content' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'required' => true,
            ],
            'event_page_title' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
            ],
            'event_meta_description' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
            ],
            'event_meta_keywords' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
            ],
            'event_meta_keywords' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
        $this->image_dir = _PS_IMG_DIR_ . '/events/';
    }

    public static function findAll()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'fme_events WHERE event_status = 1';
        if ($rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return ObjectModel::hydrateCollection(__CLASS__, $rows);
        }

        return [];
    }

    public function delete()
    {
        $res = Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'fme_events` WHERE `event_id` = ' . (int) $this->event_id
        );
        $res &= parent::delete();

        return $res;
    }

    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            exit(Tools::displayError());
        }
        $result = true;
        foreach ($selection as $id) {
            $this->id = (int) $id;
            $this->event_id = Events::getEvents();
            $result = $result && $this->delete();
        }

        return $result;
    }

    public function getEvents()
    {
        if (!$this->id) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `event_id` FROM ' . _DB_PREFIX_ . 'fme_events WHERE `event_id` = ' . (int) $this->id);
    }

    public function getAllEvent($id_lang)
    {
        if (!$id_lang) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance()->executeS('SELECT t.*, tl.`event_title`, tl.`event_content`,
            tl.`event_permalinks`, tl.`event_page_title`, tl.`event_meta_keywords`, tl.`event_meta_description`
            FROM ' . _DB_PREFIX_ . 'fme_events t
            LEFT JOIN ' . _DB_PREFIX_ . 'fme_events_lang tl
            ON (t.event_id = tl.event_id AND tl.id_lang = ' . (int) $id_lang . ')
            WHERE t.`event_status` = 1
            ORDER BY t.event_id'
        );
    }

    public function getAllEvents()
    {
        $id_lang = (int) $this->context->language->id;

        return Db::getInstance()->executeS('SELECT t.*, tl.`event_title`, tl.`event_content`, tl.`event_permalinks`,
            tl.`event_page_title`, tl.`event_meta_keywords`, tl.`event_meta_description`
            FROM ' . _DB_PREFIX_ . 'fme_events t
            LEFT JOIN ' . _DB_PREFIX_ . 'fme_events_lang tl
            ON (t.event_id = tl.event_id AND tl.id_lang = ' . (int) $id_lang . ')
            WHERE t.`event_status` = 1
            ORDER BY t.event_id'
        );
    }

    public static function getAllFrontEvents($id_lang, $limit = 'LIMIT 0, 10')
    {
        if (!$id_lang) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        $sort_by = (int) Configuration::get('EVENTS_SORT_BY');
        if ($sort_by == 2) {
            $sort_by = 'tl.`event_title`';
        } elseif ($sort_by <= 0) {
            $sort_by = 't.`event_start_date`';
        } elseif ($sort_by == 1) {
            $sort_by = 't.`event_end_date`';
        } else {
            $sort_by = 't.`event_id`';
        }
        $sort_order = (int) Configuration::get('EVENTS_SORT_ORDER');
        if ($sort_order > 0) {
            $sort_order = 'DESC';
        } else {
            $sort_order = 'ASC';
        }
        if ($limit == '') {
            $limit = ' LIMIT 0, 10';
        }
        $id_shop = (int) Context::getContext()->shop->id;
        $sql = 'SELECT t.*, tl.*
            FROM `' . _DB_PREFIX_ . 'fme_events` t
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_lang` tl ON (
            t.`event_id` = tl.`event_id`
            AND tl.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_shop` es ON (t.`event_id` = es.`event_id`)' . Shop::addSqlAssociation('event_title', 't');
        $sql .= ' WHERE t.`event_status` = 1 AND t.`event_end_date` >= NOW() AND es.`id_shop` = ' .
            (int) $id_shop . ' GROUP BY t.event_id ORDER BY ' .
            pSQL($sort_by) . ' ' . pSQL($sort_order) . ' ' . pSQL($limit);
        $events = Db::getInstance()->executeS($sql);
        if ($events === false) {
            return false;
        }

        return $events;
    }

    public function getEventDetails($event_id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = 'SELECT t.*, tl.`event_title`, tl.`event_content`, tl.`event_permalinks`,
        tl.`event_page_title`, tl.`event_meta_keywords`, tl.`event_meta_description`
        FROM `' . _DB_PREFIX_ . 'fme_events` t
        LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_lang` tl
        ON (t.`event_id` = tl.`event_id` AND tl.`id_lang` = ' . (int) $id_lang . ')';
        $sql .= 'WHERE t.`event_status` = 1
        AND t.event_id = ' . (int) $event_id;
        $sql .= ' GROUP BY t.event_id';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function getEventGallery($event_id)
    {
        $sql = 'SELECT tl.*
            FROM `' . _DB_PREFIX_ . 'fme_events` t
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_gallery` tl
            ON (t.`event_id` = tl.`events_id`)';
        $sql .= 'WHERE tl.events_id = ' . (int) $event_id;

        return Db::getInstance()->executeS($sql);
    }

    public function getEventProducts($event_id)
    {
        $sql = 'SELECT tl.`id_product`
            FROM `' . _DB_PREFIX_ . 'fme_events` t
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_products` tl
            ON (t.`event_id` = tl.`event_id`)
            WHERE tl.event_id = ' . (int) $event_id;

        return Db::getInstance()->executeS($sql);
    }

    public function parseString($str, $minChars)
    {
        $charCount = 0;
        $output = '';
        $token = explode(' ', $str);
        for ($i = 0, $limit = count($token); $i < $limit; ++$i) {
            if ($charCount < $minChars) {
                $output .= $token[$i] . ' ';
                $charCount += Tools::strlen($token[$i]);
            } else {
                $output .= '...';
                break;
            }
        }

        return $output;
    }

    public static function getCountEvents($id_lang = 0, $active = true, $p = false, $n = false)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        if (!$id_lang) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        $sql = 'SELECT t.*, tl.`event_title`, tl.`event_permalinks`
            FROM `' . _DB_PREFIX_ . 'fme_events` t
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_lang` tl
            ON (t.`event_id` = tl.`event_id` AND tl.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_shop` es ON (t.`event_id` = es.`event_id`)
            ' . Shop::addSqlAssociation('event_title', 't');
        if ($active) {
            $sql .= 'WHERE t.`event_status` = 1';
        }
        $sql .= '
            AND es.`id_shop` = ' . (int) $id_shop . '
            GROUP BY t.event_id
            ORDER BY t.`event_id` ASC' .
            ((int) $p ? ' LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n : '');
        $events = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($events === false) {
            return false;
        }

        return $events;
    }

    public static function getProductsAttached($id)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'fme_events_products` tp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` pl ON (tp.`id_product` = pl.`id_product`)
            WHERE `event_id` = ' . (int) $id;
        $tmp = Db::getInstance()->executeS($sql);
        $event_products = [];
        foreach ($tmp as $t) {
            $event_products[] = 'row_' . (int) $t['id_product'];
        }

        return json_encode($event_products);
    }

    public static function getEventGalleryImages($id)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'fme_events_gallery` t1
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events` t2 ON (t1.events_id = t2.event_id)
            WHERE t1.events_id = ' . (int) $id;
        $tmp = Db::getInstance()->executeS($sql);

        return $tmp;
    }

    public static function getImageFile($image_id)
    {
        $sql = 'SELECT image_file
            FROM `' . _DB_PREFIX_ . 'fme_events_gallery`
            WHERE image_id = ' . (int) $image_id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function getEventContactInfoAdmin($event_id)
    {
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'fme_events`
            WHERE event_id = ' . (int) $event_id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getEventsByDate($date)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;

        return Db::getInstance()->executeS('SELECT t.*, tl.`event_title`, tl.`event_content`, tl.`event_permalinks`,
            tl.`event_page_title`, tl.`event_meta_keywords`, tl.`event_meta_description`
            FROM ' . _DB_PREFIX_ . 'fme_events t
            LEFT JOIN ' . _DB_PREFIX_ . 'fme_events_lang tl
                ON (t.event_id = tl.event_id AND tl.id_lang = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_shop` es ON (t.`event_id` = es.`event_id`)
            WHERE t.`event_status` = 1
            AND es.`id_shop` = ' . (int) $id_shop . '
            AND CAST(t.event_start_date AS DATE) <= "' . pSQL($date) .
            '" AND CAST(t.event_end_date AS DATE) >= "' . pSQL($date) . '"
            ORDER BY t.event_id'
        );
    }

    public function toggleStatus()
    {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'fme_events
            SET event_status = !event_status
            WHERE event_id = ' . (int) $this->event_id;
        if (Db::getInstance()->Execute($sql)) {
            return true;
        }

        return false;
    }

    public function getProductId($id)
    {
        $sql = 'SELECT `id_product`
        FROM `' . _DB_PREFIX_ . 'fme_events_products`
        WHERE event_id = ' . (int) $id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getEventId()
    {
        $sql = 'SELECT `event_id`
            FROM `' . _DB_PREFIX_ . 'fme_events`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getProductName($id)
    {
        $sql = 'SELECT `name`
        FROM `' . _DB_PREFIX_ . 'product_lang`
        WHERE id_product = ' . (int) $id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getProductPrice($id)
    {
        $sql = 'SELECT `price`
        FROM `' . _DB_PREFIX_ . 'product`
        WHERE id_product = ' . (int) $id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getProductQty($id)
    {
        $sql = 'SELECT `quantity`
        FROM `' . _DB_PREFIX_ . 'product`
        WHERE id_product = ' . (int) $id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getProductImg($id)
    {
        $id_image = Product::getCover($id);
        if (!empty($id_image)) {
            $image = new Image($id_image['id_image']);
            $image_url = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';

            return $image_url;
        }
    }

    public function getProduct($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        $sql = new DbQuery();
        $sql->select('pep.id_product,p.price,pl.name,sa.quantity');
        $sql->from('fme_events_products', 'pep');
        $sql->where('pep.event_id=' . (int) $id);
        $sql->innerJoin('product_lang', 'pl', 'pep.id_product=pl.id_product');
        $sql->innerJoin('product', 'p', 'p.id_product=pep.id_product');
        $sql->where('pl.id_lang=' . (int) $id_lang);
        $sql->where('pl.id_shop=' . (int) $id_shop);
        $sql->where('sa.id_shop=' . (int) $id_shop);
        $sql->innerJoin('stock_available', 'sa', 'pep.id_product=sa.id_product');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    public function getOrder($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        $id = (int) Context::getContext()->customer->id;
        $orderStates = OrderState::getOrderStates($id_lang);
        $indexedOrderStates = [];
        foreach ($orderStates as $orderState) {
            $indexedOrderStates[$orderState['id_order_state']] = $orderState;
        }
        $sql = new DbQuery();
        $sql->select(
            'o.id_order,
            o.id_customer,
            osl.id_order_state,
            o.secure_key,
            o.reference,
            od.product_name,
            od.product_price,
            od.product_quantity,
            od.total_price_tax_incl,
            osl.name,
            o.date_add,
            od.id_order_invoice,
            sp.event_id,sp.id_product'
        );
        $sql->from('orders', 'o');
        $sql->where('o.id_customer=' . $id);
        $sql->leftJoin(
            'order_state_lang',
            'osl',
            'o.current_state=osl.id_order_state and osl.id_lang=' . $id_lang
        );
        $sql->innerJoin(
            'order_detail',
            'od',
            'o.id_order = od.id_order AND o.id_lang = ' . $id_lang . ' and o.id_shop=' . $id_shop
        );
        $sql->innerJoin('fme_events_products', 'sp', 'od.product_id=sp.id_product');
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$res) {
            return [];
        }
        foreach ($res as $key => $val) {
            $res[$key]['order_state'] = $indexedOrderStates[$val['id_order_state']]['name'];
            $res[$key]['invoice'] = $indexedOrderStates[$val['id_order_state']]['invoice'];
            $res[$key]['order_state_color'] = $indexedOrderStates[$val['id_order_state']]['color'];
        }

        return $res;
    }

    public static function getActiveEventProductsRecords()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_products', 'a');
        $sql->innerJoin('fme_events', 'b', 'a.`event_id` = b.`event_id` AND b.`event_status` = 1');

        return Db::getInstance()->executeS($sql);
    }

    public static function getRecordsByIdProduct($id_product)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_products');
        $sql->where('`id_product` = ' . (int) $id_product);

        return Db::getInstance()->executeS($sql);
    }

    public static function isEnable($id_event)
    {
        $sql = new DbQuery();
        $sql->select('event_status');
        $sql->from('fme_events');
        $sql->where('`event_id` = ' . (int) $id_event);

        return Db::getInstance()->getValue($sql);
    }

    public static function isEnableSeatMap($id_event)
    {
        $sql = new DbQuery();
        $sql->select('seat_map');
        $sql->from('fme_events_seat_map');
        $sql->where('`event_id` = ' . (int) $id_event);

        return Db::getInstance()->getValue($sql);
    }

    public static function getAllBookingProductsRecords()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_products');

        return Db::getInstance()->executeS($sql);
    }

    public static function getProductByEId($event_product_id)
    {
        $sql = new DbQuery();
        $sql->select('id_product');
        $sql->from('fme_events_products');
        $sql->where('`event_product_id` = ' . (int) $event_product_id);

        return Db::getInstance()->getValue($sql);
    }

    public static function getProductByIdP($id_product)
    {
        $sql = new DbQuery();
        $sql->select('event_id');
        $sql->from('fme_events_products');
        $sql->where('`id_product` = ' . (int) $id_product);

        return Db::getInstance()->getValue($sql);
    }

    public static function getAllBookingDetails($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events', 'a');
        $sql->leftJoin('fme_events_lang', 'pm', 'a.`event_id` = pm.`event_id`');
        $sql->leftJoin('fme_events_products', 'pp', 'a.`event_id` = pp.`event_id`');
        $sql->where('pm.`id_lang` = ' . (int) $id_lang);

        return Db::getInstance()->executeS($sql);
    }

    public static function getproductQuantity($id_product, $id_event, $id_product_event)
    {
        $sql = new DbQuery();
        $sql->select('ec.quantity');
        $sql->from('fme_events_customer', 'ec');
        $sql->where('ec.event_id = ' . (int) $id_event);
        $sql->where('ec.id_product = ' . (int)$id_product);
        $sql->where('ec.id_events_customer = ' . (int)$id_product_event);
        return Db::getInstance()->getRow($sql);
    }

    public static function getOrderState($id_state, $id_lang)
    {
        if ($id_state == 999) {
            $result = 'Admin (Backoffice)';

            return $result;
        }
        $order_state = new OrderState($id_state, $id_lang);
        $result = $order_state->name;

        return $result;
    }

    public static function getBookingDetailsByIdEventLang($id_event, $id_lang)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events', 'a');
        $sql->leftJoin(
            'fme_events_lang',
            'pm',
            'a.`event_id` = pm.`event_id` AND pm.`id_lang` =' . $id_lang
        );
        $sql->where('a.`event_id` = ' . (int) $id_event);

        return Db::getInstance()->executeS($sql);
    }

    public static function getBookingProductsId($id_event)
    {
        $sql = new DbQuery();
        $sql->select('id_product');
        $sql->from('fme_events_products');
        $sql->where('`event_id` = ' . (int) $id_event);

        return Db::getInstance()->getValue($sql);
    }

    public static function getProductNameLang(
        $id_product,
        $id_product_attribute = null, $id_lang = null
    ) {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $query = new DbQuery();
        if ($id_product_attribute) {
            $query->select(
                'IFNULL(
                CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),
                pl.name) as name'
            );
        } else {
            $query->select('DISTINCT pl.name as name');
        }
        if ($id_product_attribute) {
            $query->from('product_attribute', 'pa');
            $query->join(Shop::addSqlAssociation('product_attribute', 'pa'));
            $query->innerJoin('product_lang', 'pl', 'pl.id_product = pa.id_product AND pl.id_lang = ' .
                (int) $id_lang . Shop::addSqlRestrictionOnLang('pl'));
            $query->leftJoin(
                'product_attribute_combination',
                'pac',
                'pac.id_product_attribute = pa.id_product_attribute'
            );
            $query->leftJoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
            $query->leftJoin(
                'attribute_lang',
                'al',
                'al.id_attribute = atr.id_attribute AND al.id_lang = ' . (int) $id_lang
            );
            $query->leftJoin(
                'attribute_group_lang',
                'agl',
                'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = ' . (int) $id_lang
            );
            $query->where(
                'pa.id_product = ' . (int) $id_product . ' AND pa.id_product_attribute = ' . (int) $id_product_attribute
            );
        } else {
            $query->from('product_lang', 'pl');
            $query->where('pl.id_product = ' . (int) $id_product);
            $query->where('pl.id_lang = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl'));
        }

        return Db::getInstance()->getValue($query);
    }

    public static function getEventDate($id_event)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events');
        $sql->where('`event_id` = ' . (int) $id_event);
        $result = Db::getInstance()->executeS($sql);
        $dates = [];
        foreach ($result as $value) {
            $dates[] = $value['event_start_date'];
            $dates[] = $value['event_end_date'];
        }

        return $dates;
    }

    public static function getEventSDate($id_event, $days)
    {
        $final_dates = [];
        $days = explode(',', $days);
        $sql = new DbQuery();
        $sql->select('event_start_date');
        $sql->from('fme_events');
        $sql->where('`event_id` = ' . (int) $id_event);
        $start_d = Db::getInstance()->getValue($sql);
        $sql = new DbQuery();
        $sql->select('event_end_date');
        $sql->from('fme_events');
        $sql->where('`event_id` = ' . (int) $id_event);
        $end_d = Db::getInstance()->getValue($sql);
        $Date = Events::getDateRange($start_d, $end_d);
        foreach ($days as $value) {
            $value = $value - 1;
            if (isset($Date[$value])) {
                $final_dates[] = $Date[$value];
            }
        }

        return $final_dates;
    }

    public static function getEventEDate($id_event)
    {
        $sql = new DbQuery();
        $sql->select('event_end_date');
        $sql->from('fme_events');
        $sql->where('`event_id` = ' . (int) $id_event);
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    public static function getDateRange($start_d, $end_d, $format = 'Y-m-d')
    {
        $array_date = [];
        $date_interval = new DateInterval('P1D');
        $end_date = new DateTime($end_d);
        $end_date->add($date_interval);
        $gap = new DatePeriod(new DateTime($start_d), $date_interval, $end_date);
        foreach ($gap as $date) {
            $array_date[] = $date->format($format);
        }

        return $array_date;
    }

    public static function getEventByIdProduct($id_product)
    {
        $sql = new DbQuery();
        $sql->select('event_id');
        $sql->from('fme_events_products');
        $sql->where('`id_product` = ' . (int) $id_product);

        return Db::getInstance()->executeS($sql);
    }

    public static function getExpairy($id_event)
    {
        $sql = new DbQuery();
        $sql->select('event_end_date');
        $sql->from('fme_events');
        $sql->where('`event_id` = ' . (int) $id_event);

        return Db::getInstance()->getValue($sql);
    }

    public static function getActiveBookingProductsRecords()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_products', 'a');
        $sql->innerJoin('fme_events', 'b', 'a.`event_id` = b.`event_id` AND b.`event_status` = 1');

        return Db::getInstance()->executeS($sql);
    }
}
