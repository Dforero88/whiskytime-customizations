<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class HTMLTemplateCustomPdf extends HTMLTemplate
{
    public $custom_temp;

    public function __construct($custom_object, $smarty)
    {
        $this->custom_temp = $custom_object;
        $this->smarty = $smarty;
        $this->id_lang = Context::getContext()->language->id;
        $this->title = HTMLTemplateCustomPdf::l('Custom Title');
        $this->shop = new Shop(Context::getContext()->shop->id);
    }

    public function getContent()
    {
        $id_order = $this->custom_temp;
        $get_data = FmeCustomerModel::getAllDataByIdOrder($id_order);
        $array_alldata = [];
        $id_products_coll = [];
        foreach ($get_data as $valu) {
            $detail = Events::getBookingDetailsByIdEventLang($valu['event_id'], 1);
            $valu['event_id'] = $detail[0]['event_id'];
            $valu['sdate'] = $detail[0]['event_start_date'];
            $valu['edate'] = $detail[0]['event_end_date'];
            $valu['location'] = $detail[0]['event_venu'];
            $valu['title'] = $detail[0]['event_title'];
            $valu['description'] = $detail[0]['event_content'];
            array_push($id_products_coll, $valu['id_product']);
            $id_customer = $valu['id_customer'];
            $id_cart = $valu['id_cart'];
            $id_event = $detail[0]['event_id'];
            $id_product = Events::getBookingProductsId($id_event);
            $id_product = $valu['id_product'];
            $reserve_Seat = FmeCustomerModel::getCustomerReserveSeatsByCart(
                $id_event,
                $id_product,
                $id_cart,
                $id_customer
            );
            
            $customer_record = FmeCustomerModel::getCustomerPhoneDays(
                $id_event,
                $id_product,
                $id_cart,
                $id_customer
            );
            $pro = new Product($valu['id_product'], false, $this->id_lang);
            $temp_images = $pro->getImages((int) $this->id_lang);
            $image = '';
            $image_url = '';
            if ($temp_images) {
                $image = new Image($temp_images[0]['id_image']);
                $image_url = _PS_BASE_URL_ . _THEME_PROD_DIR_ .
                $image->getExistingImgPath() . '.jpg';
            }
            $valu['reserve_seat'] = $reserve_Seat;
            $valu['customer_phone'] = $customer_record['customer_phone'];
            $valu['days'] = $customer_record['days'];
            $valu['image'] = $image_url;
            $valu['p_name'] = $pro->name;
            array_push($array_alldata, $valu);
        }
        $order = new Order($id_order);
        $currency = new Currency($order->id_currency);
        $currency_name = $currency->name;
        $order_details = $order->getProductsDetail();
        foreach ($order_details as $key_p => $value_p) {
            $call = in_array($value_p['product_id'], $id_products_coll);
            if (!$call) {
                unset($order_details[$key_p]);
            }
        }
        $order_pyment = $order->getOrderPayments();
        $order_pyment = $order->payment;
        $customer = new Customer($order->id_customer);
        $fname = $customer->firstname;
        $lname = $customer->lastname;
        $email = $customer->email;
        $id_shop = (int) $this->shop->id;
        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $logo = _PS_IMG_DIR_ . $logo;
        }
        $order_state = new OrderState($order->current_state, $this->id_lang);
        $current_status = $order_state->name;
        $this->smarty->assign([
            'custom_temp' => $this->custom_temp,
            'id_products_coll' => $id_products_coll,
            'order' => $order,
            'id_order' => $id_order,
            'currency_name' => $currency_name,
            'current_status' => $current_status,
            'order_details' => $order_details,
            'order_pyment' => $order_pyment,
            'logo' => $logo,
            'lname' => $lname,
            'fname' => $fname,
            'email' => $email,
            'array_alldata' => $array_alldata,
        ]);

        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'eventsmanager/views/templates/front/ticket_content.tpl'
        );
    }

    public function getFilename()
    {
        return 'ticket_pdf.pdf';
    }

    public function getBulkFilename()
    {
        return 'ticket_pdf.pdf';
    }

    public function getHeader()
    {
        $id_order = $this->custom_temp;
        $get_data = FmeCustomerModel::getAllDataByIdOrder($id_order);
        $array_alldata = [];
        foreach ($get_data as $valu) {
            $detail = Events::getBookingDetailsByIdEventLang($valu['event_id'], 1);
            if (!$detail) {
                exit;
            }
            $valu['sdate'] = $detail[0]['event_start_date'];
            $valu['edate'] = $detail[0]['event_end_date'];
            $valu['location'] = $detail[0]['event_venu'];
            $valu['title'] = $detail[0]['event_title'];
            $valu['description'] = $detail[0]['event_content'];
            $pro = new Product($valu['id_product'], false, $this->id_lang);
            $temp_images = $pro->getImages((int) $this->id_lang);
            $image = '';
            $image_url = '';
            if ($temp_images) {
                $image = new Image($temp_images[0]['id_image']);
                $image_url = _PS_BASE_URL_ . _THEME_PROD_DIR_ .
                $image->getExistingImgPath() . '.jpg';
            }
            $valu['image'] = $image_url;
            $valu['p_name'] = $pro->name;
            array_push($array_alldata, $valu);
        }
        $order = new Order($id_order);
        $order_details = $order->getProductsDetail();
        $order_pyment = $order->getOrderPaymentCollection();
        $customer = new Customer($order->id_customer);
        $fname = $customer->firstname;
        $lname = $customer->lastname;
        $email = $customer->email;
        $id_shop = (int) $this->shop->id;
        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $logo = _PS_IMG_DIR_ . $logo;
        }
        $this->smarty->assign([
            'custom_temp' => $this->custom_temp,
            'order' => $order,
            'order_details' => $order_details,
            'order_pyment' => $order_pyment,
            'logo' => $logo,
            'lname' => $lname,
            'id_order' => $id_order,
            'fname' => $fname,
            'email' => $email,
            'array_alldata' => $array_alldata,
        ]);

        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'eventsmanager/views/templates/front/ticket_header.tpl'
        );
    }

    public function getFooter()
    {
        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'eventsmanager/views/templates/front/ticket_footer.tpl'
        );
    }
}
