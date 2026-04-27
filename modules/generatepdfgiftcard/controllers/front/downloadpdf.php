<?php
require_once _PS_MODULE_DIR_ . 'generatepdfgiftcard/generatepdfgiftcard.php';

class GeneratePDFGiftCardDownloadpdfModuleFrontController extends ModuleFrontController
{
    public $module;
    
    public function __construct()
    {
        parent::__construct();
        $this->module = Module::getInstanceByName('generatepdfgiftcard');
    }

    public function init()
    {
        parent::init();
        
        if (!$this->context->customer->isLogged()) {
            Tools::redirect($this->context->link->getPageLink('authentication', true));
        }
    }

    public function initContent()
{
    parent::initContent();

    $id_cart_rule = (int)Tools::getValue('id_cart_rule');
    $custom_message = Tools::getValue('custom_message', '');

    if (!$id_cart_rule) {
        die('Invalid gift card');
    }

    // Get gift card data
    $gift_data = $this->getGiftCardData($id_cart_rule);

    if (!$gift_data) {
        die('Gift card not found or not owned by you');
    }

    // Get template for current language
    $template = $this->module->getTemplateForLanguage($this->context->language->id);
    
    // Traiter les conditions dans le template
    $template = $this->processTemplateConditions($template, $custom_message);

    // Prepare variables
    $variables = $this->prepareVariables($gift_data, $custom_message);

    // Replace variables in template
    $html_content = $this->replaceVariables($template, $variables);

    // Generate PDF
    $this->generatePDF($html_content, isset($gift_data['code']) ? $gift_data['code'] : 'giftcard');
}

    private function getGiftCardData($id_cart_rule)
{
    $id_lang = $this->context->language->id;
    $id_customer = $this->context->customer->id;

    // REQUÊTE SIMPLIFIÉE - juste l'essentiel
    $sql = 'SELECT cr.id_cart_rule, cr.code, cr.reduction_amount, cr.reduction_percent, 
                   cr.reduction_currency, cr.date_to, cr.date_add,
                   crl.name as gift_title, 
                   gcc.id_product, gcc.id_cart
            FROM `'._DB_PREFIX_.'cart_rule` cr
            LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl 
                ON (cr.`id_cart_rule` = crl.`id_cart_rule` AND crl.`id_lang` = '.(int)$id_lang.')
            LEFT JOIN `'._DB_PREFIX_.'gift_card_customer` gcc 
                ON (cr.`id_cart_rule` = gcc.`id_cart_rule`)
            WHERE cr.`id_cart_rule` = '.(int)$id_cart_rule.'
            AND gcc.`id_customer` = '.(int)$id_customer;

    return Db::getInstance()->getRow($sql);
}

    private function prepareVariables($gift_data, $custom_message)
{
    $customer = $this->context->customer;

    // Format amount
    $amount = '0';
    if (isset($gift_data['reduction_percent']) && $gift_data['reduction_percent'] > 0) {
        $amount = $gift_data['reduction_percent'] . '%';
    } elseif (isset($gift_data['reduction_amount']) && $gift_data['reduction_amount'] > 0) {
        $currency_id = isset($gift_data['reduction_currency']) ? $gift_data['reduction_currency'] : Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = new Currency($currency_id);
        $amount = Tools::displayPrice($gift_data['reduction_amount'], $currency);
    }

    // Get gift image
    $gift_image = '';
    if (isset($gift_data['id_product']) && $gift_data['id_product']) {
        $image = Image::getCover($gift_data['id_product']);
        if ($image && isset($image['id_image'])) {
            $product = new Product($gift_data['id_product'], false, $this->context->language->id);
            $link_rewrite = $product->link_rewrite;
            $gift_image = $this->context->link->getImageLink($link_rewrite, $image['id_image'], 'medium_default');
        }
    }

    // Get shop logo
    $shop_logo = $this->context->shop->getBaseURL(true) . 'img/' . Configuration::get('PS_LOGO');

    // Format dates
    $expiry_date = isset($gift_data['date_to']) ? date('d/m/Y', strtotime($gift_data['date_to'])) : '';
    $date_purchased = isset($gift_data['date_add']) ? date('d/m/Y', strtotime($gift_data['date_add'])) : '';

    // Get order reference - NOUVELLE MÉTHODE
    $order_reference = $this->getOrderReference($gift_data, $customer->id);

    return [
        '{code}' => isset($gift_data['code']) ? $gift_data['code'] : '',
        '{amount}' => $amount,
        '{expiry_date}' => $expiry_date,
        '{gift_image}' => $gift_image,
        '{shop_logo}' => $shop_logo,
        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        '{order_reference}' => $order_reference,
        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
        '{custom_message}' => $custom_message,
        '{gift_title}' => isset($gift_data['gift_title']) ? $gift_data['gift_title'] : 'Gift Card',
        '{date_purchased}' => $date_purchased
    ];
}

    private function replaceVariables($template, $variables)
    {
        foreach ($variables as $key => $value) {
            $template = str_replace($key, $value, $template);
        }
        return $template;
    }

    private function generatePDF($html_content, $filename)
    {
        // Clean filename
        $clean_filename = Tools::str2url($filename) . '.pdf';
        
        // Use PrestaShop's TCPDF
        if (file_exists(_PS_ROOT_DIR_ . '/vendor/tecnickcom/tcpdf/tcpdf.php')) {
            require_once _PS_ROOT_DIR_ . '/vendor/tecnickcom/tcpdf/tcpdf.php';
        } else {
            require_once _PS_ROOT_DIR_ . '/tools/tcpdf/tcpdf.php';
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('GeneratePDFGiftCard Module');
        $pdf->SetAuthor(Configuration::get('PS_SHOP_NAME'));
        $pdf->SetTitle('Gift Card ' . $filename);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 11);
        
        // Write HTML content
        $pdf->writeHTML($html_content, true, false, true, false, '');
        
        // Close and output PDF
        $pdf->Output($clean_filename, 'D');
        
        exit;
    }
private function processTemplateConditions($template, $custom_message)
    {
    // Traitement spécifique pour {if $custom_message}
    if (strpos($template, '{if $custom_message}') !== false) {
        if (empty($custom_message)) {
            // Supprime tout le bloc conditionnel
            $template = preg_replace('/\{if \$custom_message\}.*?\{\/if\}/s', '', $template);
        } else {
            // Enlève seulement les tags {if} et {/if}, garde le contenu
            $template = preg_replace('/\{if \$custom_message\}/', '', $template);
            $template = preg_replace('/\{\/if\}/', '', $template);
        }
    }
    
    return $template;
    }
    
    private function getOrderReference($gift_data, $id_customer)
{
    if (!isset($gift_data['id_cart_rule'])) {
        return 'N/A';
    }
    
    $id_cart_rule = $gift_data['id_cart_rule'];
    
    // DEBUG: Voir ce qu'on a
    error_log('=== ORDER REF DEBUG ===');
    error_log('Cart Rule ID: ' . $id_cart_rule);
    error_log('Customer ID: ' . $id_customer);
    error_log('Has id_cart: ' . (isset($gift_data['id_cart']) ? $gift_data['id_cart'] : 'NO'));
    
    // ESSAI 1: Via la table gift_card_customer -> id_cart -> orders
    if (isset($gift_data['id_cart']) && $gift_data['id_cart']) {
        $sql = 'SELECT o.reference 
                FROM `'._DB_PREFIX_.'orders` o
                WHERE o.id_cart = ' . (int)$gift_data['id_cart'] . '
                AND o.id_customer = ' . (int)$id_customer;
        
        error_log('SQL 1 (via id_cart): ' . $sql);
        $result = Db::getInstance()->getValue($sql);
        
        if ($result) {
            error_log('Found via id_cart: ' . $result);
            return $result;
        }
    }
    
    // ESSAI 2: Via order_cart_rule (table standard PrestaShop)
    $sql2 = 'SELECT o.reference 
             FROM `'._DB_PREFIX_.'orders` o
             INNER JOIN `'._DB_PREFIX_.'order_cart_rule` ocr 
                ON o.id_order = ocr.id_order
             WHERE ocr.id_cart_rule = ' . (int)$id_cart_rule . '
             AND o.id_customer = ' . (int)$id_customer;
    
    error_log('SQL 2 (via order_cart_rule): ' . $sql2);
    $result2 = Db::getInstance()->getValue($sql2);
    
    if ($result2) {
        error_log('Found via order_cart_rule: ' . $result2);
        return $result2;
    }
    
    // ESSAI 3: Recherche large dans order_cart_rule sans filtre client
    $sql3 = 'SELECT o.reference 
             FROM `'._DB_PREFIX_.'orders` o
             INNER JOIN `'._DB_PREFIX_.'order_cart_rule` ocr 
                ON o.id_order = ocr.id_order
             WHERE ocr.id_cart_rule = ' . (int)$id_cart_rule . '
             LIMIT 1';
    
    error_log('SQL 3 (wide search): ' . $sql3);
    $result3 = Db::getInstance()->getValue($sql3);
    
    if ($result3) {
        error_log('Found via wide search: ' . $result3);
        return $result3;
    }
    
    // ESSAI 4: Via le produit si disponible
    if (isset($gift_data['id_product']) && $gift_data['id_product']) {
        $sql4 = 'SELECT o.reference 
                 FROM `'._DB_PREFIX_.'orders` o
                 INNER JOIN `'._DB_PREFIX_.'order_detail` od 
                    ON o.id_order = od.id_order
                 WHERE od.product_id = ' . (int)$gift_data['id_product'] . '
                 AND o.id_customer = ' . (int)$id_customer . '
                 ORDER BY o.date_add DESC 
                 LIMIT 1';
        
        error_log('SQL 4 (via product): ' . $sql4);
        $result4 = Db::getInstance()->getValue($sql4);
        
        if ($result4) {
            error_log('Found via product: ' . $result4);
            return $result4;
        }
    }
    
    error_log('No order reference found');
    return 'N/A';
}
}