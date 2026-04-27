<?php
/**
 * PDF Customers List Module
 * 
 * @author YourName
 * @version 1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PDFCustomersList extends Module
{
    public function __construct()
    {
        $this->name = 'pdfcustomerslist';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'David Forero';
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('PDF Customers List');
        $this->description = $this->l('Generate PDF customer lists for events');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() 
            && $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

        public function getContent()
        {
            $output = '';
            
            // Process form submission
            if (Tools::isSubmit('generate_pdf')) {
                $event_id = (int)Tools::getValue('event_id');
                if ($event_id > 0) {
                    $this->generatePDF($event_id);
                }
            }
            
            // Display configuration form
            $this->context->smarty->assign(array(
                'events' => $this->getActiveEvents(),
                'selected_event' => Tools::getValue('event_id', 0),
                'orders_data' => $this->getEventOrders(Tools::getValue('event_id', 0)),
                'event_info' => $this->getEventInfo(Tools::getValue('event_id', 0)),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules'), // AJOUT IMPORTANT
            ));
            
            return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
        }

public function getActiveEvents()
{
    $sql = 'SELECT e.event_id, el.event_title, e.event_start_date
            FROM ' . _DB_PREFIX_ . 'fme_events e
            INNER JOIN ' . _DB_PREFIX_ . 'fme_events_lang el 
                ON e.event_id = el.event_id 
                AND el.id_lang = ' . (int)$this->context->language->id . '
            WHERE e.event_status = 1
            ORDER BY e.event_start_date DESC';
    
    $events = Db::getInstance()->executeS($sql);
    
    // Formater les résultats pour ajouter la date au titre
    foreach ($events as &$event) {
        // Extraire seulement la date (sans l'heure)
        $date_only = date('Y-m-d', strtotime($event['event_start_date']));
        // Créer le nouveau format : Date // Titre
        $event['event_title'] = $date_only . ' // ' . $event['event_title'];
    }
    
    return $events;
}

public function getEventInfo($event_id)
{
    if (!$event_id) {
        return array();
    }
    
    $sql = 'SELECT e.event_id, el.event_title, e.event_start_date, 
                   e.event_end_date, e.event_venu,
                   (SELECT SUM(ec.quantity) 
                    FROM ' . _DB_PREFIX_ . 'fme_events_customer ec
                    INNER JOIN ' . _DB_PREFIX_ . 'fme_events_products ep 
                        ON ep.id_product = ec.id_product
                    WHERE ep.event_id = e.event_id) as tickets_sold
            FROM ' . _DB_PREFIX_ . 'fme_events e
            INNER JOIN ' . _DB_PREFIX_ . 'fme_events_lang el 
                ON e.event_id = el.event_id 
                AND el.id_lang = ' . (int)$this->context->language->id . '
            WHERE e.event_id = ' . (int)$event_id;
    
    return Db::getInstance()->getRow($sql);
}

        public function getEventOrders($event_id)
    {
        if (!$event_id) {
            return array();
        }
        
        $sql = 'SELECT DISTINCT
                    o.reference as order_reference,
                    o.date_add as order_date,
                    CONCAT(c.firstname, " ", c.lastname) as customer_name,
                    c.email as customer_email,
                    ec.customer_phone,
                    od.unit_price_tax_excl as price_per_ticket,
                    od.product_quantity as quantity,
                    (od.unit_price_tax_excl * od.product_quantity) as total,
                    op.payment_method,
                    osl.name as order_status
                FROM ' . _DB_PREFIX_ . 'fme_events_products ep
                INNER JOIN ' . _DB_PREFIX_ . 'order_detail od 
                    ON od.product_id = ep.id_product
                INNER JOIN ' . _DB_PREFIX_ . 'orders o 
                    ON o.id_order = od.id_order
                INNER JOIN ' . _DB_PREFIX_ . 'customer c 
                    ON c.id_customer = o.id_customer
                INNER JOIN ' . _DB_PREFIX_ . 'fme_events_customer ec
                    ON ec.id_order = o.id_order 
                    AND ec.id_product = od.product_id
                LEFT JOIN ' . _DB_PREFIX_ . 'order_payment op 
                    ON op.order_reference = o.reference
                INNER JOIN ' . _DB_PREFIX_ . 'order_state_lang osl 
                    ON osl.id_order_state = o.current_state 
                    AND osl.id_lang = ' . (int)$this->context->language->id . '
                WHERE ep.event_id = ' . (int)$event_id . '
                ORDER BY o.date_add DESC';
        
        return Db::getInstance()->executeS($sql);
    }

private function generatePDF($event_id)
{
    require_once(_PS_MODULE_DIR_ . $this->name . '/pdf/PDFCustomersGenerator.php');
    
    $event_info = $this->getEventInfo($event_id);
    $orders = $this->getEventOrders($event_id);
    
    $pdf = new PDFCustomersGenerator($event_info, $orders, 'L'); // 'L' pour paysage
    $pdf->render();
    exit;
}

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }
    }
}