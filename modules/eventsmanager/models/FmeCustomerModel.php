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
class FmeCustomerModel extends ObjectModel
{
    public $event_id;
    public $id_events_customer;
    public $id_product;
    public $quantity;
    public $id_customer;
    public $id_guest;
    public $id_cart;
    public $id_order;
    public $order_status;
    public $customer_name;
    public $customer_phone;
    public $reserve_seats;
    public $reserve_seats_num;
    public $date;
    public $days;
    public $admin_order;
    public $admin_payment_confirm;
    public static $definition = [
        'table' => 'fme_events_customer',
        'primary' => 'id_events_customer',
        'fields' => [
            'id_events_customer' => ['type' => self::TYPE_INT],
            'event_id' => ['type' => self::TYPE_INT],
            'id_product' => ['type' => self::TYPE_INT],
            'quantity' => ['type' => self::TYPE_INT],
            'id_customer' => ['type' => self::TYPE_INT],
            'id_guest' => ['type' => self::TYPE_INT],
            'id_cart' => ['type' => self::TYPE_INT],
            'id_order' => ['type' => self::TYPE_INT],
            'order_status' => ['type' => self::TYPE_STRING],
            'customer_name' => ['type' => self::TYPE_STRING],
            'customer_phone' => ['type' => self::TYPE_STRING],
            'reserve_seats' => ['type' => self::TYPE_STRING],
            'reserve_seats_num' => ['type' => self::TYPE_STRING],
            'date' => ['type' => self::TYPE_STRING],
            'days' => ['type' => self::TYPE_STRING],
            'admin_order' => ['type' => self::TYPE_INT],
            'admin_payment_confirm' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($event_id = null, $id_events_customer = null)
    {
        parent::__construct($event_id, $id_events_customer);
    }

    public static function getAllReserveSeats($id_event, $id_product, $wait_min)
    {
        $id_product = $id_product;
        $time = date('Y-m-d H:i:s');
        $newtimestamp = strtotime($time . '-' . $wait_min . 'minute');
        $new_time = date('Y-m-d H:i:s', $newtimestamp);
        $sql = new DbQuery();
        $sql->select('reserve_seats');
        $sql->from('fme_events_customer');
        $sql->where('event_id = ' . (int) $id_event . ' AND( date >= "' . $new_time . '" OR id_order != 0)');

        return Db::getInstance()->executeS($sql);
    }

    public static function getCustomerReserveSeats($id_event, $id_product, $id_cart, $id_customer, $wait_min)
    {
        $time = date('Y-m-d H:i:s');
        $newtimestamp = strtotime($time . '-' . $wait_min . 'minute');
        $new_time = date('Y-m-d H:i:s', $newtimestamp);
        $sql = new DbQuery();
        $sql->select('reserve_seats');
        $sql->from('fme_events_customer');

        $sql->where('event_id = ' . (int) $id_event . ' AND id_product = ' .
            (int) $id_product . ' AND id_cart = ' . (int) $id_cart .
            ' AND id_customer = ' . (int) $id_customer . ' AND date >= "' . $new_time . '"');

        return Db::getInstance()->getValue($sql);
    }

    public static function ifExistSeats($id_event, $id_product, $id_customer, $id_cart)
    {
        $id_customer = $id_customer;
        $sql = new DbQuery();
        $sql->select('reserve_seats');
        $sql->from('fme_events_customer');
        $sql->where('`event_id` = ' . (int) $id_event . ' AND id_product = ' .
            (int) $id_product . ' AND id_cart != ' . (int) $id_cart);

        return Db::getInstance()->executeS($sql);
    }

    public static function ifExistCustomer($id_event, $id_product, $id_customer, $id_cart)
    {
        $sql = new DbQuery();
        $sql->select('id_events_customer');
        $sql->from('fme_events_customer');
        $sql->where('`event_id` = ' . (int) $id_event . ' AND id_product = ' .
            (int) $id_product . ' AND id_customer = ' . (int) $id_customer .
            ' AND id_cart = ' . (int) $id_cart);
        $sql->orderBy('id_events_customer DESC LIMIT 1');

        return Db::getInstance()->executeS($sql);
    }

    public static function ifExistCustomerCart($id_product, $id_customer, $id_cart)
    {
        $sql = new DbQuery();
        $sql->select('id_events_customer');
        $sql->from('fme_events_customer');
        $sql->where('id_product = ' . (int) $id_product . ' AND id_customer = '
            . (int) $id_customer . ' AND id_cart = ' . (int) $id_cart);
        $sql->orderBy('id_events_customer DESC LIMIT 1');

        return Db::getInstance()->executeS($sql);
    }

    public static function getTicketDetailsById($id_event, $id_product)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_customer');
        $sql->where('event_id = ' . (int) $id_event .
        ' AND ( (order_status != 0 AND order_status != 999 ) ||
        (order_status = 999 AND admin_order = 1) ) AND id_product = ' .
        (int) $id_product);

        return Db::getInstance()->executeS($sql);
    }

    public static function getOrderRef($id_order)
    {
        $order = new Order($id_order);
        $order_ref = $order->reference;

        return $order_ref;
    }

    public static function getAllSeats($id_event, $id_product)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_customer');
        $sql->where('event_id = ' . (int) $id_event . ' AND id_product = ' .
            (int) $id_product . ' AND id_order != 0');

        return Db::getInstance()->executeS($sql);
    }

    public static function getAdminAllReserveSeats($id_event, $id_product)
    {
        $sql = new DbQuery();
        $sql->select('reserve_seats');
        $sql->from('fme_events_customer');
        $sql->where('event_id = ' . (int) $id_event . ' AND id_product = ' . (int) $id_product . ' AND id_order != 0');

        return Db::getInstance()->executeS($sql);
    }

    public static function getAllDataByIdCustomer($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_customer', 'a');
        $sql->leftJoin('fme_events', 'pm', 'a.`event_id` = pm.`event_id`');
        $sql->where('id_customer = ' . (int) $id_customer);

        return Db::getInstance()->executeS($sql);
    }

    public static function updateOrderStatus($id_order, $status)
    {
        return Db::getInstance()->update(
            'fme_events_customer',
            ['order_status' => $status],
            'id_order = ' . (int) $id_order
        );
    }

    public static function getAllDataByIdOrder($id_order)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_customer');
        $sql->where('id_order = ' . (int) $id_order);

        return Db::getInstance()->executeS($sql);
    }

    public static function getCustomerReserveSeatsByCart($id_event, $id_product, $id_cart, $id_customer)
    {
        $sql = new DbQuery();
        $sql->select('reserve_seats_num');
        $sql->from('fme_events_customer');
        $sql->where('event_id = ' . (int) $id_event . ' AND id_product = ' .
            (int) $id_product . ' AND id_cart = ' . (int) $id_cart . ' AND id_customer = ' . (int) $id_customer);

        return Db::getInstance()->getValue($sql);
    }

    public static function getCustomerPhoneDays($id_event, $id_product, $id_cart, $id_customer)
    {
        $sql = new DbQuery();
        $sql->select('customer_phone, days');
        $sql->from('fme_events_customer');
        $sql->where('event_id = ' . (int) $id_event . ' AND id_product = ' .
            (int) $id_product . ' AND id_cart = ' . (int) $id_cart . ' AND id_customer = ' . (int) $id_customer);

        return Db::getInstance()->getRow($sql);
    }

    public static function getEventsByOrder($id_order, $id_lang = null)
    {
        if (!$id_order) {
            return false;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_customer');
        $sql->where('id_order = ' . (int) $id_order);

        $events = Db::getInstance()->executeS($sql);
        // if (isset($events)
        //     && $events
        //     && isset($events['id_store'])
        //     && $events['id_store']
        //     && Validate::isLoadedObject($store = new Store($events['id_store'], $id_lang))) {
        //     $id_address = self::getStoreAddressId($store->id);
        //     if ($id_address && Validate::isLoadedObject($address = new Address($id_address))) {
        //         $events['store_name'] = $store->name;
        //         $st_address = AddressFormat::generateAddress($address, [], ' ', ' ');
        //         $st_address = str_replace($store->name, '', $st_address);
        //         $st_address = str_replace($address->firstname . ' ' . $address->lastname, '', $st_address);
        //         $events['store_address'] = ltrim($st_address);
        //     }
        // }

        return $events;
    }

    public static function getAllDataByIdEventCustomer($id_events_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fme_events_customer');
        $sql->where('id_events_customer = ' . (int) $id_events_customer);

        return Db::getInstance()->executeS($sql);
    }

    public static function getCustomerReserveSeatsById($id_events_customer)
    {
        $sql = new DbQuery();
        $sql->select('reserve_seats_num');
        $sql->from('fme_events_customer');
        $sql->where('id_events_customer = ' . (int) $id_events_customer);

        return Db::getInstance()->getValue($sql);
    }
}
