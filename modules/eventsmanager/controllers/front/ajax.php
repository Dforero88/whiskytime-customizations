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
class EventsManagerAjaxModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->name = 'eventsmanager';
        parent::__construct();
        $this->context = Context::getContext();
        $this->display_column_left = false;
    }

    public function init()
    {
        parent::init();
        $this->ajax = (bool) Tools::getValue('ajax');
    }

    public function displayAjaxGetSeatMap()
    {
        $id_event = (int) Tools::getValue('id_event');
        $id_product = (int) Tools::getValue('id_product');
        $id_cart = (int) Tools::getValue('id_cart');
        $wait_min = (int) Tools::getValue('wait_min');
        $id_customer = (int) Tools::getValue('id_customer');
        $result = FmeSeatMapModel::getBookingSeatsMapById($id_event);
        if ($result) {
            $result = $result[0]['seat_map'];
        }
        $reserve_seats = FmeCustomerModel::getAllReserveSeats($id_event, $id_product, $wait_min);
        $joinArray = [];
        foreach ($reserve_seats as $key => $value) {
            $key = $key;
            array_push($joinArray, $value['reserve_seats']);
        }
        $reserve = implode(',', $joinArray);
        $customer_seats = FmeCustomerModel::getCustomerReserveSeats(
            $id_event,
            $id_product,
            $id_cart,
            $id_customer,
            $wait_min
        );
        if (!$customer_seats) {
            $customer_seats = null;
        }
        $result = json_encode([$result, $reserve, $customer_seats]);
        if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode($result);
                exit;
            } else {
                $this->ajaxDie($result);
            }
        // $this->ajaxDie($result);
    }

    public function displayAjaxUpdateCustomerTicketSeat()
    {
        $token = Tools::getValue('token');
        $key = md5(_COOKIE_KEY_);
        if ($token == $key) {
            $id_event = Tools::getValue('id_event');
            $id_product = Tools::getValue('id_product');
            $id_customer = Tools::getValue('id_customer');
            $id_cart = Tools::getValue('id_cart');
            $reserve_seat = Tools::getValue('reserve_seat');
            $reserve_seat_num = Tools::getValue('reserve_seat_num');
            $event_product_id = Tools::getValue('event_product_id');
            if ($event_product_id) {
                $id_product = Events::getProductByEId($event_product_id);
            }
            $seat_array = explode(',', $reserve_seat);
            $if_exist_seats = FmeCustomerModel::ifExistSeats($id_event, $id_product, $id_customer, $id_cart);
            foreach ($if_exist_seats as $key => $value) {
                $key = $key;
                $explode_array = explode(',', $value['reserve_seats']);
                foreach ($explode_array as $key => $value) {
                    $check = in_array($value, $seat_array);
                    if ($check) {
                        if(_PS_VERSION_ >= '9.0.0') {
                            echo json_encode(0);
                            exit;
                        } else {
                            $this->ajaxDie(0);
                        }
                        // $this->ajaxDie(0);
                    }
                }
            }
            $if_exist = FmeCustomerModel::ifExistCustomer($id_event, $id_product, $id_customer, $id_cart);
            if ($if_exist) {
                $id_customer_event = $if_exist[0]['id_events_customer'];
                $obj_customer_ev = new FmeCustomerModel($id_customer_event);
                $obj_customer_ev->reserve_seats = $reserve_seat;
                $obj_customer_ev->reserve_seats_num = $reserve_seat_num;
                $obj_customer_ev->date = date('Y-m-d H:i:s');
                $result = $obj_customer_ev->update();
            } else {
                $obj_customer_ev = new FmeCustomerModel();
                $obj_customer_ev->event_id = $id_event;
                $obj_customer_ev->id_product = $id_product;
                $obj_customer_ev->id_customer = $id_customer;
                $obj_customer_ev->id_cart = $id_cart;
                $obj_customer_ev->reserve_seats = $reserve_seat;
                $obj_customer_ev->reserve_seats_num = $reserve_seat_num;
                $obj_customer_ev->date = date('Y-m-d H:i:s');
                $result = $obj_customer_ev->save();
            }
            // $this->ajaxDie($result);
            if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode($result);
                exit;
            } else {
                $this->ajaxDie($result);
            }
        } else {
            if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode($result);
                exit;
            } else {
                $this->ajaxDie($result);
            }
            // $this->ajaxDie('invalid Token');
        }
    }

    public function displayAjaxUpdateCustomerTicket()
    {
        $token = Tools::getValue('token');
        $key = md5(_COOKIE_KEY_);
        if ($token == $key) {
            $array_data = Tools::getValue('array_data');
            $save_products = [];
            foreach ($array_data as $key => $value) {
                $key = $key;
                $obj_customer = new FmeCustomerModel();
                $obj_customer->event_id = $value[0];
                $obj_customer->id_product = $value[1];
                $save_products[] = $value[1];
                $obj_customer->quantity = $value[4];
                $obj_customer->id_customer = $value[2];
                $id_customer = $value[2];
                $obj_customer->id_guest = $value[3];
                $obj_customer->id_cart = $value[5];
                $id_cart = $value[5];
                $id_guest = $value[3];
                $obj_customer->id_order = '';
                $obj_customer->order_status = 0;
                $products_in_cart = $value[8];
                $obj_customer->customer_name = $value[6];
                $obj_customer->customer_phone = $value[7];
                $days = '';
                if (isset($value[9])) {
                    $days = implode(',', $value[9]);
                }
                $obj_customer->days = $days;
                $if_exist = FmeCustomerModel::ifExistCustomer(
                    $obj_customer->event_id,
                    $obj_customer->id_product,
                    $obj_customer->id_customer,
                    $obj_customer->id_cart
                );
                if ($if_exist) {
                    $id_customer_event = $if_exist[0]['id_events_customer'];
                    $obj_customer_ev = new FmeCustomerModel($id_customer_event);
                    $obj_customer_ev->event_id = $value[0];
                    $obj_customer_ev->id_product = $value[1];
                    $obj_customer_ev->quantity = $value[4];
                    $obj_customer_ev->id_customer = $value[2];
                    $obj_customer_ev->id_guest = $value[3];
                    $obj_customer_ev->id_cart = $value[5];
                    $obj_customer_ev->id_order = '';
                    $obj_customer_ev->order_status = 0;
                    $days = '';
                    if (isset($value[9])) {
                        $days = implode(',', $value[9]);
                    }
                    $obj_customer_ev->days = $days;
                    $obj_customer_ev->customer_name = $value[6];
                    $obj_customer_ev->customer_phone = $value[7];
                    $result = $obj_customer_ev->update();
                } else {
                    $result = $obj_customer->save();
                }
            }
            $products_in_cart = explode(',', $products_in_cart);
            $diff = array_diff($products_in_cart, $save_products);
            foreach ($diff as $key => $value_p) {
                $id_product = (int) $value_p;
                $id_event = Events::getProductByIdP($id_product);
                $qty = Context::getContext()->cart->getProducts(false, $id_product);
                $qty = (int) $qty[0]['cart_quantity'];
                $customer_obj = new FmeCustomerModel();
                $customer_obj->event_id = $id_event;
                $customer_obj->id_product = $id_product;
                $customer_obj->quantity = $qty;
                $customer_obj->id_customer = $id_customer;
                $customer_obj->id_guest = $id_guest;
                $customer_obj->id_cart = $id_cart;
                $customer_obj->id_order = '';
                $customer_obj->order_status = 0;
                $if_exist = FmeCustomerModel::ifExistCustomer(
                    $customer_obj->event_id,
                    $customer_obj->id_product,
                    $customer_obj->id_customer,
                    $customer_obj->id_cart
                );
                if ($if_exist) {
                    $id_customer_event = $if_exist[0]['id_events_customer'];
                    $customer_obj_ev = new FmeCustomerModel($id_customer_event);
                    $customer_obj_ev->event_id = $id_event;
                    $customer_obj_ev->id_product = $id_product;
                    $customer_obj_ev->quantity = $qty;
                    $customer_obj_ev->id_customer = $id_customer;
                    $customer_obj_ev->id_guest = $id_guest;
                    $customer_obj_ev->id_cart = $id_cart;
                    $customer_obj_ev->id_order = '';
                    $customer_obj_ev->order_status = 0;
                    $result = $customer_obj_ev->update();
                } else {
                    $result = $customer_obj->save();
                }
            }
            if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode($result);
                exit;
            } else {
                $this->ajaxDie($result);
            }
            // $this->ajaxDie($result);
        } else {
            if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode($result);
                exit;
            } else {
                $this->ajaxDie($result);
            }
            // $this->ajaxDie('invalid token');
        }
    }

    public function displayAjaxGetAdminSeatMap()
    {
        $id_event = (int) Tools::getValue('id_event');
        $id_product = (int) Tools::getValue('id_product');
        $result = FmeSeatMapModel::getBookingSeatsMapById($id_event);
        if ($result) {
            $result = $result[0]['seat_map'];
        }
        $reserve_seats = FmeCustomerModel::getAdminAllReserveSeats($id_event, $id_product);
        $joinArray = [];
        foreach ($reserve_seats as $key => $value) {
            $key = $key;
            array_push($joinArray, $value['reserve_seats']);
        }
        $reserve = implode(',', $joinArray);
        $result = json_encode([$result, $reserve]);
        if(_PS_VERSION_ >= '9.0.0') {
            echo $result;
            exit;
        } else {
            $this->ajaxDie($result);
        }
        // $this->ajaxDie($result);
    }

    public function displayAjaxCancelCustomerRecord()
    {
        $id_record = (int) Tools::getValue('id');
        $customer_data = new FmeCustomerModel($id_record);
        $back_qty = $customer_data->quantity;
        $id_pro = $customer_data->id_product;
        $product_qty = StockAvailable::getQuantityAvailableByProduct((int) $id_pro);
        $qty = $product_qty + $back_qty;
        StockAvailable::setQuantity((int) $id_pro, 0, (int) $qty);
        $customer_data->reserve_seats = 0;
        $customer_data->reserve_seats_num = 0;
        $customer_data->quantity = 0;
        $customer_data->order_status = 6;
        $result_customer_data = $customer_data->update();
        $result = json_encode($result_customer_data);
        if(_PS_VERSION_ >= '9.0.0') {
                echo $result;
                exit;
            } else {
                $this->ajaxDie($result);
            }
        // $this->ajaxDie($result);
    }

    public function displayAjaxOkCustomerRecord()
    {
        $id_record = (int) Tools::getValue('id');
        $customer_data = new FmeCustomerModel($id_record);
        $customer_data->admin_payment_confirm = 1;
        $result_customer_data = $customer_data->update();
        $result = json_encode($result_customer_data);
        if(_PS_VERSION_ >= '9.0.0') {
                echo $result;
                exit;
            } else {
                $this->ajaxDie($result);
            }
        // $this->ajaxDie($result);
    }

    public function displayAjaxDeleteCustomerRecord()
    {
        $id_record = (int) Tools::getValue('id');
        $customer_data = new FmeCustomerModel($id_record);
        $back_qty = $customer_data->quantity;
        $id_pro = $customer_data->id_product;
        $product_qty = StockAvailable::getQuantityAvailableByProduct((int) $id_pro);
        $qty = $product_qty + $back_qty;
        StockAvailable::setQuantity((int) $id_pro, 0, (int) $qty);
        $result_customer_data = $customer_data->delete();
        $result = json_encode($result_customer_data);
        if(_PS_VERSION_ >= '9.0.0') {
            echo $result;
            exit;
        } else {
            $this->ajaxDie($result);
        }
        // $this->ajaxDie($result);
    }

    public function postProcess()
    {
        $token = Tools::getValue('token');
        $key = Configuration::get('EVENT_MANAGER__ACCESS_TOKEN');
        if (Tools::getValue('id_order')) {
            $id_order = (int) Tools::getValue('id_order');
            $order = new Order($id_order);
            if (($this->context->customer->isLogged() && $this->userCanViewOrder($order)) || ($key && $token == $key)) {
                $this->context->smarty->assign('order_id', $id_order);
                $this->context->smarty->assign('event_id', Tools::getValue('id_event'));
                $this->context->smarty->assign('link', $this->context->link);
                $pdf = new PDF($id_order, 'CustomPdf', Context::getContext()->smarty);
                $pdf->render();
                exit;
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit('Access Denied: You do not have permission to access this file.');
            }
        }
        if (Tools::getValue('id_order_admin')) {
            if ($key && $token == $key) {
                $id_event_customer = (int) Tools::getValue('id_event_customer');
                $this->context->smarty->assign('order_id', $id_order);
                $this->context->smarty->assign('event_id', Tools::getValue('id_event'));
                $this->context->smarty->assign('link', $this->context->link);
                $pdf = new PDF($id_event_customer, 'CustomPdfAdmin', Context::getContext()->smarty);
                $pdf->render();
                exit;
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit('Access Denied: You do not have permission to access this file.');
            }
        }
    }

    protected function userCanViewOrder($order)
    {
        return $this->context->customer->id == $order->id_customer;
    }

    public function displayAjaxGetSeatMapAdmin()
    {
        $id_event = (int) Tools::getValue('id_event');
        $id_product = (int) Tools::getValue('id_product');
        $id_cart = 0;
        $wait_min = 2;
        $id_customer = 0;
        $result = FmeSeatMapModel::getBookingSeatsMapById($id_event);
        if ($result) {
            $result = $result[0]['seat_map'];
        }
        $reserve_seats = FmeCustomerModel::getAllReserveSeats($id_event, $id_product, $wait_min);
        $joinArray = [];
        foreach ($reserve_seats as $key => $value) {
            $key = $key;
            array_push($joinArray, $value['reserve_seats']);
        }
        $reserve = implode(',', $joinArray);
        $customer_seats = FmeCustomerModel::getCustomerReserveSeats(
            $id_event,
            $id_product,
            $id_cart,
            $id_customer,
            $wait_min
        );
        if (!$customer_seats) {
            $customer_seats = null;
        }
        $result = json_encode([$result, $reserve, $customer_seats]);
        if(_PS_VERSION_ >= '9.0.0') {
            echo $result;
            exit;
        } else {
            $this->ajaxDie($result);
        }
        // $this->ajaxDie($result);
    }

    public function displayAjaxUpdateCustomerTicketSeatAdmin()
    {
        $id_event = Tools::getValue('id_event');
        $id_product = Tools::getValue('id_product');
        $id_customer = Tools::getValue('id_customer');
        $id_cart = Tools::getValue('id_cart');
        $customer_phone = Tools::getValue('customer_phone');
        $customer_email = Tools::getValue('customer_email');
        $customer_name = Tools::getValue('customer_name');
        $reserve_seat = Tools::getValue('reserve_seat');
        $reserve_seat_num = Tools::getValue('reserve_seat_num');
        $event_product_id = Tools::getValue('event_product_id');
        $qty = Tools::getValue('sqty');
        if ($event_product_id) {
            $id_product = Events::getProductByEId($event_product_id);
        }
        $seat_array = explode(',', $reserve_seat);
        $if_exist_seats = FmeCustomerModel::getAllReserveSeats($id_event, $id_product, 2);
        foreach ($if_exist_seats as $key => $value) {
            $explode_array = explode(',', $value['reserve_seats']);
            foreach ($explode_array as $key => $value) {
                $check = in_array($value, $seat_array);
                if ($check) {
                    if(_PS_VERSION_ >= '9.0.0') {
                        echo json_encode(0);
                        exit;
                    } else {
                        $this->ajaxDie(0);
                    }
                    // $this->ajaxDie(0);
                }
            }
        }
        $this->shop = new Shop(Context::getContext()->shop->id);
        $id_shop = (int) $this->shop->id;
        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $logo = _PS_IMG_DIR_ . $logo;
        }
        $id_lang = (int) Context::getContext()->language->id;
        $id_curr = (int) Context::getContext()->currency->id;
        $currency = new Currency($id_curr);
        $currency_name = $currency->name;
        $detail = Events::getBookingDetailsByIdEventLang($id_event, $id_lang);
        $event_start_date = $detail[0]['event_start_date'];
        $event_end_date = $detail[0]['event_end_date'];
        $event_venu = $detail[0]['event_venu'];
        $event_title = $detail[0]['event_title'];
        $event_content = $detail[0]['event_content'];
        $product_detail = new Product($id_product, false, $id_lang);
        $p_name = $product_detail->name;
        $p_price = $product_detail->price;
        $total_p = (int) $p_price * (int) $qty;
        $date = date('l jS \of F Y h:i:s A');
        $templateVars = [
            '{id_event}' => $id_event,
            '{customer_phone}' => $customer_phone,
            '{p_name}' => $p_name,
            '{p_price}' => $p_price,
            '{customer_name}' => $customer_name,
            '{customer_email}' => $customer_email,
            '{reserve_seat}' => $reserve_seat,
            '{reserve_seat_num}' => $reserve_seat_num,
            '{date}' => $date,
            '{qty}' => $qty,
            '{logo}' => $logo,
            '{event_start_date}' => $event_start_date,
            '{event_end_date}' => $event_end_date,
            '{event_venu}' => $event_venu,
            '{event_title}' => $event_title,
            '{currency_name}' => $currency_name,
            '{event_content}' => $event_content,
            '{total_p}' => $total_p,
            '{shop_link}' => _PS_BASE_URL_ . __PS_BASE_URI__,
        ];
        if (!empty($customer_email)) {
            Mail::Send(
                (int) $id_lang,
                'ticket',
                Mail::l('You received ticket information', (int) $id_lang),
                $templateVars,
                $customer_email,
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_ . 'eventsmanager/mails/',
                false
            );
        }
        $obj_customer_ev = new FmeCustomerModel($event_product_id, $id_event);
        $obj_customer_ev->event_id = $id_event;
        $obj_customer_ev->id_product = $id_product;
        $obj_customer_ev->id_customer = $id_customer;
        $obj_customer_ev->id_cart = $id_cart;
        $obj_customer_ev->quantity = $qty;
        $obj_customer_ev->admin_order = 1;
        $obj_customer_ev->customer_phone = $customer_phone;
        $obj_customer_ev->customer_name = $customer_name;
        $obj_customer_ev->reserve_seats = $reserve_seat;
        $obj_customer_ev->reserve_seats_num = $reserve_seat_num;
        $obj_customer_ev->date = date('Y-m-d H:i:s');
        $result = $obj_customer_ev->update();
        if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode($result);
                exit;
            } else {
                $this->ajaxDie($result);
            }
        // $this->ajaxDie($result);
    }

    public function displayAjaxUpdateCustomerTicketSeatCart()
    {
        $token = Tools::getValue('token');
        $key = md5(_COOKIE_KEY_);
        if ($token == $key) {
            $id_event = Tools::getValue('id_event');
            $id_product = Tools::getValue('id_product');
            $id_customer = Tools::getValue('id_customer');
            $id_cart = Tools::getValue('id_cart');
            $reserve_seat = Tools::getValue('reserve_seat');
            $reserve_seat_num = Tools::getValue('reserve_seat_num');
            $event_product_id = Tools::getValue('event_product_id');
            if ($event_product_id) {
                $id_product = Events::getProductByEId($event_product_id);
            }
            if (!$id_cart) {
                $id_cart = $this->context->cart->id;
            }
            $seat_array = explode(',', $reserve_seat);
            $if_exist_seats = FmeCustomerModel::ifExistSeats($id_event, $id_product, $id_customer, $id_cart);
            foreach ($if_exist_seats as $key => $value) {
                $key = $key;
                $explode_array = explode(',', $value['reserve_seats']);
                foreach ($explode_array as $key => $value) {
                    $check = in_array($value, $seat_array);
                    if ($check) {
                        if(_PS_VERSION_ >= '9.0.0') {
                            echo json_encode(0);
                        } else {
                            $this->ajaxDie(0);
                        }
                        // $this->ajaxDie(0);
                        exit;
                    }
                }
            }

            $operator = 'up';
            $operator = $operator;
            $id_custom = false;
            $id_add_dliv = 0;
            $this->context->cart->deleteProduct($id_product, 0, $id_custom, $id_add_dliv);
            $operator = 'up';
            $id_cus = false;
            $id_add_dli = 0;
            $sqty = (int) Tools::getValue('sqty');

            if (!$this->context->cart->id) {
                $this->context->cart->add();
                $this->context->cookie->id_cart = $this->context->cart->id;
            }
            
            $cart_result = $this->context->cart->updateQty(
                (int) $sqty,
                (int) $id_product,
                0,
                $id_cus,
                $operator,
                (int) $id_add_dli
            );

            if ($cart_result != true) {
                if(_PS_VERSION_ >= '9.0.0') {
                    echo json_encode(0);
                } else {
                    $this->ajaxDie(0);
                }
                // $this->ajaxDie(0);
                exit;
            }
            $if_exist = FmeCustomerModel::ifExistCustomer($id_event, $id_product, $id_customer, $id_cart);
            if ($if_exist) {
                $id_customer_event = $if_exist[0]['id_events_customer'];
                $obj_customer_ev = new FmeCustomerModel($id_customer_event);
                $obj_customer_ev->reserve_seats = $reserve_seat;
                $obj_customer_ev->reserve_seats_num = $reserve_seat_num;
                $obj_customer_ev->date = date('Y-m-d H:i:s');
                $result = $obj_customer_ev->update();
            } else {
                $obj_customer_ev = new FmeCustomerModel();
                $obj_customer_ev->event_id = $id_event;
                $obj_customer_ev->id_product = $id_product;
                $obj_customer_ev->id_customer = $id_customer;
                $obj_customer_ev->id_cart = $id_cart;
                $obj_customer_ev->reserve_seats = $reserve_seat;
                $obj_customer_ev->reserve_seats_num = $reserve_seat_num;
                $obj_customer_ev->date = date('Y-m-d H:i:s');
                $result = $obj_customer_ev->save();
            }
            $customer = new Customer($id_customer);
            $this->context->customer = $customer;
            $this->context->cart->id_customer = $id_customer;
            $this->context->cart->secure_key = $customer->secure_key;
            $this->context->cart = new Cart($id_cart);
            if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode($result);
                exit;
            } else {
                $this->ajaxDie($result);
            }
            // $this->ajaxDie($result);
        } else {
            if(_PS_VERSION_ >= '9.0.0') {
                echo json_encode('invalid Token');
                exit;
            } else {
                $this->ajaxDie('invalid Token');
            }
            // $this->ajaxDie('invalid Token');
        }
    }
}
