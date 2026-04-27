<?php
/**
 * PDF Customers List Generator
 */

require_once(_PS_ROOT_DIR_ . '/vendor/tecnickcom/tcpdf/tcpdf.php');

class PDFCustomersGenerator extends TCPDF
{
    protected $event_info;
    protected $orders;
    
    public function __construct($event_info, $orders, $orientation = 'L')
    {
        parent::__construct($orientation, 'mm', 'A4', true, 'UTF-8', false);
        
        $this->event_info = $event_info;
        $this->orders = $orders;
        
        // Set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('PrestaShop');
        $this->SetTitle('Customers List - ' . $event_info['event_title']);
        $this->SetSubject('Event Customers');
        
        // Remove header/footer
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        
        // Set margins
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 15);
        
        // Set font
        $this->SetFont('helvetica', '', 9);
        
        // Add a page
        $this->AddPage();
        
        // Generate content
        $this->generateContent();
    }
    
    protected function generateContent()
{
    // Part 1: Event Information
    $this->SetFont('helvetica', 'B', 14);
    $this->Cell(0, 10, 'Event Customers List', 0, 1, 'C');
    $this->Ln(5);
    
    $this->SetFont('helvetica', 'B', 11);
    $this->Cell(0, 7, $this->event_info['event_title'], 0, 1, 'L');
    
    $this->SetFont('helvetica', '', 10);
    $this->Cell(60, 6, 'Start Date: ' . $this->event_info['event_start_date'], 0, 0);
    $this->Cell(60, 6, 'Location: ' . $this->event_info['event_venu'], 0, 0);
    $this->Ln(10);
    
    // Part 2: Orders Table
    if (!empty($this->orders)) {
        // Calculer les totaux
        $total_qty = 0;
        $total_amount = 0;
        foreach($this->orders as $order) {
            $total_qty += $order['quantity'];
            $total_amount += $order['total'];
        }
        
        // NOUVEAUX LARGEURS DE COLONNES
        $col_widths = array(20, 25, 30, 35, 25, 20, 15, 20, 40, 20); // Payment: 25→40
        
        // EN-TÊTE AVEC COULEUR BLANC SUR FOND GRIS
        $this->SetFont('helvetica', 'B', 9);
        $this->SetFillColor(80, 80, 80); // Gris foncé
        $this->SetTextColor(255, 255, 255); // Texte blanc
        
        $header = array('Order Ref', 'Order Date', 'Customer Name', 'Email', 'Phone', 
                       'Price/Ticket', 'Qty', 'Total', 'Payment', 'Status');
        
        for($i = 0; $i < count($header); $i++) {
            $this->Cell($col_widths[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        
        // DONNÉES
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(0, 0, 0); // Retour au texte noir
        $this->SetFillColor(245, 245, 245);
        $fill = false;
        
        foreach($this->orders as $order) {
            // Alternate row color
            $fill = !$fill;
            $this->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
            
            // Format price
            $price = Tools::displayPrice($order['price_per_ticket'], 
                Context::getContext()->currency);
            $total = Tools::displayPrice($order['total'], 
                Context::getContext()->currency);
            
            // Remove currency symbol for cleaner display
            $price = preg_replace('/[^\d.,]/', '', $price);
            $total = preg_replace('/[^\d.,]/', '', $total);
            
            $this->Cell($col_widths[0], 6, $order['order_reference'], 'LR', 0, 'C', $fill);
            $this->Cell($col_widths[1], 6, substr($order['order_date'], 0, 10), 'LR', 0, 'C', $fill);
            $this->Cell($col_widths[2], 6, substr($order['customer_name'], 0, 20), 'LR', 0, 'L', $fill);
            $this->Cell($col_widths[3], 6, substr($order['customer_email'], 0, 25), 'LR', 0, 'L', $fill);
            $this->Cell($col_widths[4], 6, $order['customer_phone'], 'LR', 0, 'C', $fill);
            $this->Cell($col_widths[5], 6, $price, 'LR', 0, 'R', $fill);
            $this->Cell($col_widths[6], 6, $order['quantity'], 'LR', 0, 'C', $fill);
            $this->Cell($col_widths[7], 6, $total, 'LR', 0, 'R', $fill);
            
            // Colonne Payment élargie
            $this->Cell($col_widths[8], 6, substr($order['payment_method'], 0, 20), 'LR', 0, 'C', $fill);
            
            $this->Cell($col_widths[9], 6, substr($order['order_status'], 0, 12), 'LR', 0, 'C', $fill);
            $this->Ln();
        }
        
        // LIGNE DES TOTAUX
        $this->SetFont('helvetica', 'B', 8);
        $this->SetFillColor(200, 200, 200); // Gris moyen
        $this->SetTextColor(0, 0, 0);
        
        // Calcul largeur pour "TOTALS:"
        $total_label_width = $col_widths[0] + $col_widths[1] + $col_widths[2] + 
                            $col_widths[3] + $col_widths[4] + $col_widths[5];
        
        $this->Cell($total_label_width, 6, 'TOTALS:', 1, 0, 'R', 1);
        $this->Cell($col_widths[6], 6, $total_qty, 1, 0, 'C', 1);
        
        // Formater le montant total
        $total_formatted = Tools::displayPrice($total_amount, Context::getContext()->currency);
        $total_formatted = preg_replace('/[^\d.,]/', '', $total_formatted);
        $this->Cell($col_widths[7], 6, $total_formatted, 1, 0, 'R', 1);
        
        // Colonnes vides pour Payment et Status
        $this->Cell($col_widths[8] + $col_widths[9], 6, '', 1, 0, 'C', 1);
        
        // Closing line
        $this->Ln();
        $this->Cell(array_sum($col_widths), 0, '', 'T');
    } else {
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 10, 'No orders found for this event.', 0, 1, 'C');
    }
}
    
    public function render()
    {
        $filename = 'customers_list_' . $this->event_info['event_id'] . '_' . date('Y-m-d') . '.pdf';
        $this->Output($filename, 'D'); // 'D' for download
    }
}