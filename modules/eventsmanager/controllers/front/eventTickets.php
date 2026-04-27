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
if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) {
    require_once _PS_TOOL_DIR_ . 'tcpdf/config/lang/eng.php';
    require_once _PS_TOOL_DIR_ . 'tcpdf/tcpdf.php';
}

class EventsManagerEventTicketsModuleFrontController extends ModuleFrontController
{
    protected $myEventTicket = false;

    public function __construct()
    {
        parent::__construct();
        $this->display_column_right = false;
        $this->display_column_left = false;
    }

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        if (!$this->context->customer->logged) {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $force_ssl =
            (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $this->context->smarty->assign(
                [
                    'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
                    'base_dir_ssl' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
                    'force_ssl' => $force_ssl,
                ]
            );
        }
        $obj = new Events();
        $order_id = (int) Tools::getValue('order_id');
        $event_id = (int) Tools::getValue('id_event');
        $_PS_BASE_URL_ = Tools::getCurrentUrlProtocolPrefix() . Tools::getShopDomain() . __PS_BASE_URI__;
        $pdfLink = $_PS_BASE_URL_ . 'modules/eventsmanager/pdf/pl_pdf.pdf';
        $order = new Order($order_id);
        $eventData = $obj->getEventDetails($event_id);
        $address_invoice = new Address((int) $order->id_address_invoice);
        $address_delivery = new Address((int) $order->id_address_delivery);
        $address_invoice = AddressFormat::generateAddress($address_invoice);
        $address_delivery = AddressFormat::generateAddress($address_delivery);
        // $logo = $_PS_BASE_URL_ . 'img/logo.jpg';
        $shop = new Shop(Context::getContext()->shop->id);
        $id_shop = (int) $shop->id;
         $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $logo = _PS_IMG_DIR_ . $logo;
        }
        $plink = $order->reference;
        $date = $order->date_add;
        $onumber = $order_id;
        $slip = $order->delivery_number;
        $weight = $order->getTotalWeight() . ' ' . Configuration::get('PS_WEIGHT_UNIT');
        $id_customer = (int) $this->context->cookie->id_customer;
        $datas = $obj->getAllEvent($this->context->language->id);
        $pdf_status = '';
        foreach ($datas as $ve) {
            $pdf_status = $ve['pdf_status'];
        }
        $api_key = Configuration::get('EVENTS_META_MAPKEY');
        $ajax_url = $this->context->link->getModuleLink(
            'eventsmanager',
            'ajax',
            ['token' => md5(_COOKIE_KEY_)]
        );
        $data = $obj->getOrder($id_customer);
        $this->context->smarty->assign('ajax_url', $ajax_url);
        $this->context->smarty->assign('api_key', $api_key);
        $this->context->smarty->assign('plink', $plink);
        $this->context->smarty->assign('pdf_status', (int) $pdf_status);
        $this->context->smarty->assign('pdfLink', $pdfLink);
        $this->context->smarty->assign('order_id', $order_id);
        $this->context->smarty->assign('event_id', $event_id);
        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('eventData', $eventData);
        $this->context->smarty->assign('onumber', $onumber);
        $this->context->smarty->assign('address_invoice', $address_invoice);
        $this->context->smarty->assign('address_delivery', $address_delivery);
        $this->context->smarty->assign('logo', $logo);
        $this->context->smarty->assign('date', $date);
        $this->context->smarty->assign('weight', $weight);
        $this->context->smarty->assign('slip', $slip);
        $this->context->smarty->assign('data', $data);
        $this->context->smarty->assign('id_lang', $this->context->language->id);
        $this->context->smarty->assign('_PS_BASE_URL_', $_PS_BASE_URL_);
        $customPdf = $this->generateLabelPDF();
        if ($order_id == true) {
            Tools::redirect($pdfLink);
        }
        $currency = Currency::getDefaultCurrency()->sign;
        $this->context->smarty->assign('customPdf', $customPdf);
        $this->context->smarty->assign('currency', $currency);
        $alldata = FmeCustomerModel::getAllDataByIdCustomer($id_customer);
        $this->context->smarty->assign('alldata', $alldata);
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $this->setTemplate('module:eventsmanager/views/templates/front/eventTickets.tpl');
        } else {
            $this->setTemplate('eventTickets_16.tpl');
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }

    public function generateLabelPDF()
    {
        $print_layout_view =
        $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'eventsmanager/views/templates/front/customPdf.tpl');
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->writeHTML($print_layout_view, true, false, true, false, '');
        $pdf->Output(_PS_MODULE_DIR_ . 'eventsmanager/pdf/pl_pdf.pdf', 'F');
    }
}
