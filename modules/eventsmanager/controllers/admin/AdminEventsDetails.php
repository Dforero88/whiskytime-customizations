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
class AdminEventsDetailsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'fme_events';
        $this->className = 'Events';
        $this->identifier = 'event_id';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;
        parent::__construct();
        $this->context = Context::getContext();
        $url = '';
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $router = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('router');
 
            $url = $router->generate('admin_module_configure_action', [
                'module_name' => $this->module->name,
            ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $url = $this->context->link->getAdminLink('AdminModules', true);
        }
        $this->context->smarty->assign([
            'version' => _PS_VERSION_,
            'configure_link' => $url,
            'manage_events_link' => $this->context->link->getAdminLink('AdminEvents'),
            'events_details_link' => $this->context->link->getAdminLink('AdminEventsDetails'),
            'events_tags_link' => $this->context->link->getAdminLink('AdminTags'),
        ]);
    }

    public function initContent()
    {
        parent::initContent();
        $this->setVariables();
        $this->setMedia();
        $blue_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/checked.png';
        $red_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/red.png';
        $green_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/green.png';
        $this->context->smarty->assign('blue_color', $blue_color);
        $this->context->smarty->assign('red_color', $red_color);
        $this->context->smarty->assign('green_color', $green_color);
        $this->context->smarty->assign('ps_version', _PS_VERSION_);
        $this->content .=
        $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'eventsmanager/views/templates/admin/configure.tpl');
        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $iso_tiny_mce = $this->context->language->iso_code;
        $iso_tiny_mce = file_exists(_PS_JS_DIR_ . 'tiny_mce/langs/' . $iso_tiny_mce . '.js') ? $iso_tiny_mce : 'en';
        $this->context->smarty->assign('iso_tiny_mce', $iso_tiny_mce);
        $controller = Dispatcher::getInstance()->getController();
        Media::addJsDef([
            'iso_tiny_micy' => $iso_tiny_mce,
            'css_content' => '',
            'base_url' => '',
            'controller' => $controller,
        ]);
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/jquery-ui-1.9.2.custom.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/fmeevents.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/fme_page.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/fme_table_jui.css', 'screen');
        $this->addCSS(__PS_BASE_URI__ . 'modules/eventsmanager/views/css/ColVis.css', 'screen');
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/jquery.accordion.js', 'screen');
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/jquery.cookie.min.js', 'screen');
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        $this->addJS(
            [
                _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            ]
        );
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/jquery.dataTables.js', 'screen');
        $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/ColVis.js', 'screen');
        if (Tools::version_compare(_PS_VERSION_, '1.7.8.0', '>=') == true) {
            $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/admin_ps1780_plus.js');
        } else {
            $this->addJS(__PS_BASE_URI__ . 'modules/eventsmanager/views/js/admin.js');
        }
    }

    public function setVariables()
    {
        $id_lang = $this->context->language->id;
        $this->context->smarty->assign('ps_version', _PS_VERSION_);
        $all_booking_data = Events::getAllBookingDetails($id_lang);
        foreach($all_booking_data as $index => $booking_data){
            $quantity = Events::getproductQuantity($booking_data['id_product'],$booking_data['event_id'], $booking_data['event_product_id']);
            if (isset($quantity['quantity'])) {
                $booking_data['quantity'] = $quantity['quantity'];
                $all_booking_data[$index] = $booking_data;
            }

        }
        $this->context->smarty->assign(
            'seatmap_path',
            $this->context->link->getAdminLink('AdminEventsDetails') . '&action=getseatmap'
        );
        $this->context->smarty->assign(
            'customers_path',
            $this->context->link->getAdminLink('AdminEventsDetails') . '&action=getcustomers'
        );
        $ajax_url = $this->context->link->getModuleLink(
            'eventsmanager',
            'ajax',
            ['token' => $this->module->initTokenSecure()]
        );
        $this->context->smarty->assign(
            [
                'id_lang' => $id_lang,
                'ajax_url' => $ajax_url . '&id_event=' . $booking_data['event_id'],
                'all_booking_data' => $all_booking_data,
            ]
        );
    }

    public function postProcess()
    {
        parent::postProcess();
        $action = Tools::getValue('action');
        if ($action == 'getseatmap') {
            $this->setVariables();
            $id_event = (int) Tools::getValue('event_id');
            $id_product = (int) Tools::getValue('id_product');
            $seat_data = FmeSeatMapModel::getBookingSeatsMapById($id_event);
            $getAllSeats = FmeCustomerModel::getAllSeats($id_event, $id_product);
            if ($seat_data) {
                $seat_data = $seat_data[0]['seat_map'];
            }
            foreach($getAllSeats as $key=>$seats){
                $quantity = Events::getproductQuantity($seats['id_product'],$seats['event_id'], $seats['id_events_customer']);
                $seats['quantity'] = $quantity['quantity'];
            }
            $this->context->smarty->assign(
                [
                    'seat_data' => $seat_data,
                    'id_event' => $id_event,
                    'id_product' => $id_product,
                    'all_seatdata' => $getAllSeats,
                ]
            );
            $blue_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/checked.png';
            $red_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/red.png';
            $green_color = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/eventsmanager/views/img/green.png';
            $this->context->smarty->assign('blue_color', $blue_color);
            $this->context->smarty->assign('red_color', $red_color);
            $this->context->smarty->assign('green_color', $green_color);

            $this->context->smarty->assign('ps_version', _PS_VERSION_);
            $this->content =
            $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'eventsmanager/views/templates/admin/customer_seats.tpl');
            $this->context->smarty->assign([
                'content' => $this->content,
            ]);
        } elseif ($action == 'getcustomers') {
            $this->setVariables();
            $id_event = (int) Tools::getValue('event_id');
            $id_product = (int) Tools::getValue('id_product');
            $this->context->smarty->assign('ps_version', _PS_VERSION_);
            $customer_data = FmeCustomerModel::getTicketDetailsById($id_event, $id_product);
            $this->context->smarty->assign(
                [
                    'customer_data' => $customer_data,
                ]
            );
            $this->content .=
            $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'eventsmanager/views/templates/admin/customer_details.tpl');
            $this->context->smarty->assign([
                'content' => $this->content,
            ]);
        }
    }
}
