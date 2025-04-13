<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Massimiliano Palermo <maxx.palermo@gmail.com>
 * @copyright Since 2023 Massimiliano Palermo
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace MpSoft\MpWaCart\Service;

class PdfGenerator
{
    /**
     * @var \Context
     */
    private $context;

    /**
     * Constructor
     *
     * @param \Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Generate a PDF for a cart
     *
     * @param \Cart $cart
     * @return array
     */
    public function generateCartPdf($cart)
    {
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        // Impostazioni PDF
        $pdf->SetCreator('MpWaCart');
        $pdf->SetAuthor(\Configuration::get('PS_SHOP_NAME'));
        $pdf->SetTitle('Preventivo Carrello #' . $cart->id);
        
        // Rimuovi header/footer predefiniti
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Aggiungi pagina
        $pdf->AddPage();
        
        // Logo
        $logo_path = _PS_IMG_DIR_ . \Configuration::get('PS_LOGO');
        if (file_exists($logo_path)) {
            $pdf->Image($logo_path, 10, 10, 50);
        }
        
        // Intestazione
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 20, 'Preventivo Carrello #' . $cart->id, 0, 1, 'R');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Data: ' . date('d/m/Y H:i'), 0, 1, 'R');
        
        // Informazioni cliente
        $customer = new \Customer($cart->id_customer);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Informazioni Cliente', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Nome: ' . $customer->firstname . ' ' . $customer->lastname, 0, 1, 'L');
        $pdf->Cell(0, 6, 'Email: ' . $customer->email, 0, 1, 'L');
        
        // Prodotti nel carrello
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Prodotti nel Carrello', 0, 1, 'L');
        
        // Intestazione tabella
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(80, 8, 'Prodotto', 1, 0, 'L', true);
        $pdf->Cell(30, 8, 'Riferimento', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Quantità', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Prezzo Unitario', 1, 0, 'R', true);
        $pdf->Cell(30, 8, 'Totale', 1, 1, 'R', true);
        
        // Prodotti
        $pdf->SetFont('helvetica', '', 9);
        $products = $cart->getProducts();
        foreach ($products as $product) {
            $pdf->Cell(80, 7, $product['name'], 1, 0, 'L');
            $pdf->Cell(30, 7, $product['reference'], 1, 0, 'C');
            $pdf->Cell(20, 7, $product['cart_quantity'], 1, 0, 'C');
            $pdf->Cell(30, 7, \Tools::displayPrice($product['price_wt']), 1, 0, 'R');
            $pdf->Cell(30, 7, \Tools::displayPrice($product['total_wt']), 1, 1, 'R');
        }
        
        // Totali
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        
        $summary = $cart->getSummaryDetails();
        $pdf->Cell(130, 7, '', 0, 0, 'L');
        $pdf->Cell(30, 7, 'Totale Prodotti:', 0, 0, 'R');
        $pdf->Cell(30, 7, \Tools::displayPrice($summary['total_products_wt']), 0, 1, 'R');
        
        $pdf->Cell(130, 7, '', 0, 0, 'L');
        $pdf->Cell(30, 7, 'Spedizione:', 0, 0, 'R');
        $pdf->Cell(30, 7, \Tools::displayPrice($summary['total_shipping']), 0, 1, 'R');
        
        if ($summary['total_discounts'] > 0) {
            $pdf->Cell(130, 7, '', 0, 0, 'L');
            $pdf->Cell(30, 7, 'Sconti:', 0, 0, 'R');
            $pdf->Cell(30, 7, '-' . \Tools::displayPrice($summary['total_discounts']), 0, 1, 'R');
        }
        
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(130, 10, '', 0, 0, 'L');
        $pdf->Cell(30, 10, 'TOTALE:', 0, 0, 'R');
        $pdf->Cell(30, 10, \Tools::displayPrice($summary['total_price']), 0, 1, 'R');
        
        // Note
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 9);
        $pdf->MultiCell(0, 5, 'Questo preventivo è valido per 7 giorni dalla data di emissione. Per confermare l\'ordine, rispondere al messaggio WhatsApp.', 0, 'L');
        
        // Salva il PDF
        $filename = 'preventivo_' . $cart->id . '_' . date('YmdHis') . '.pdf';
        $pdf_dir = _PS_MODULE_DIR_ . 'mpwacart/pdf/';
        
        if (!is_dir($pdf_dir)) {
            mkdir($pdf_dir, 0777, true);
        }
        
        $pdf_path = $pdf_dir . $filename;
        $pdf->Output($pdf_path, 'F');
        
        return [
            'path' => $pdf_path,
            'filename' => $filename,
            'url' => $this->context->link->getBaseLink() . 'modules/mpwacart/pdf/' . $filename
        ];
    }
}
