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
class HTMLTemplateCustomPdfAdmin extends HTMLTemplate
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
        $id_event_customer = $this->custom_temp;
        $all_data = FmeCustomerModel::getAllDataByIdEventCustomer($id_event_customer);
        $array_alldata = [];
        $id_products_coll = [];
        foreach ($all_data as $valu) {
            $detail = Events::getBookingDetailsByIdEventLang($valu['event_id'], 1);
            $booking_date = $valu['date'];
            $customer_name = $valu['customer_name'];
            $customer_phone = $valu['customer_phone'];
            $id_product = $valu['id_product'];
            $quantity = $valu['quantity'];
            $reserve_seats = $valu['reserve_seats_num'];

            $this->smarty->assign([
                'booking_date' => $booking_date,
                'customer_name' => $customer_name,
                'customer_phone' => $customer_phone,
                'id_product' => $id_product,
                'quantity' => $quantity,
                'reserve_seats' => $reserve_seats,
            ]);
            $valu['event_id'] = $detail[0]['event_id'];
            $valu['sdate'] = $detail[0]['event_start_date'];
            $valu['edate'] = $detail[0]['event_end_date'];
            $valu['location'] = $detail[0]['event_venu'];
            $valu['title'] = $detail[0]['event_title'];
            $valu['description'] = $detail[0]['event_content'];
            array_push($id_products_coll, $valu['id_product']);
            $id_event = $detail[0]['event_id'];
            $id_product = Events::getBookingProductsId($id_event);
            $id_product = $valu['id_product'];
            $reserve_Seat = FmeCustomerModel::getCustomerReserveSeatsById(
                $id_event_customer
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
            $valu['image'] = $image_url;
            $valu['p_name'] = $pro->name;
            array_push($array_alldata, $valu);
        }
        $id_shop = (int) $this->shop->id;
        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $logo = _PS_IMG_DIR_ . $logo;
        }
        $currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency_name = Currency::getCurrency($currency);
        $this->smarty->assign([
            'custom_temp' => $this->custom_temp,
            'id_products_coll' => $id_products_coll,
            'order' => '',
            'id_order' => '',
            'currency_name' => $currency_name['iso_code'],
            'current_status' => '',
            'order_details' => '',
            'order_pyment' => '',
            'logo' => $logo,
            'lname' => '',
            'fname' => '',
            'email' => '',
            'array_alldata' => $array_alldata,
        ]);

        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'eventsmanager/views/templates/front/ticket_content_admin.tpl'
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
        $id_event_customer = $this->custom_temp;
        $all_data = FmeCustomerModel::getAllDataByIdEventCustomer($id_event_customer);
        $array_alldata = [];
        $id_products_coll = [];
        foreach ($all_data as $valu) {
            $detail = Events::getBookingDetailsByIdEventLang($valu['event_id'], 1);
            $valu['event_id'] = $detail[0]['event_id'];
            $valu['sdate'] = $detail[0]['event_start_date'];
            $valu['edate'] = $detail[0]['event_end_date'];
            $valu['location'] = $detail[0]['event_venu'];
            $valu['title'] = $detail[0]['event_title'];
            $valu['description'] = $detail[0]['event_content'];
            array_push($id_products_coll, $valu['id_product']);
            $reserve_Seat = FmeCustomerModel::getCustomerReserveSeatsById(
                $id_event_customer
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
            $valu['image'] = $image_url;
            $valu['p_name'] = $pro->name;
            array_push($array_alldata, $valu);
        }
        $id_shop = (int) $this->shop->id;
        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $logo = _PS_IMG_DIR_ . $logo;
        }
        $this->smarty->assign([
            'custom_temp' => $this->custom_temp,
            'order' => '',
            'order_details' => '',
            'order_pyment' => '',
            'logo' => $logo,
            'lname' => '',
            'id_order' => '',
            'fname' => '',
            'email' => '',
            'array_alldata' => $array_alldata,
        ]);

        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'eventsmanager/views/templates/front/ticket_header_admin.tpl'
        );
    }

    public function getFooter()
    {
        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'eventsmanager/views/templates/front/ticket_footer.tpl'
        );
    }
}
