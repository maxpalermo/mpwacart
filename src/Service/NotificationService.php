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

use MpSoft\MpWaCart\Api\WhatsAppBusinessApi;
use MpSoft\MpWaCart\Entity\WaCartConversation;

class NotificationService
{
    /**
     * @var \Context
     */
    private $context;

    /**
     * @var WhatsAppBusinessApi
     */
    private $whatsappApi;

    /**
     * @var bool
     */
    private $testMode;
    
    /**
     * @var bool
     */
    private $useDirectLink;

    /**
     * Constructor
     *
     * @param \Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
        
        // Verifica se la modalità test è attiva
        $this->testMode = (bool)\Configuration::get('MPWACART_TEST_MODE');
        
        // Verifica se usare il link diretto wa.me
        $this->useDirectLink = (bool)\Configuration::get('MPWACART_USE_DIRECT_LINK');
        
        // Inizializza l'API WhatsApp solo se non usiamo il link diretto
        if (!$this->useDirectLink) {
            $apiKey = \Configuration::get('MPWACART_API_KEY');
            $phoneNumberId = \Configuration::get('MPWACART_PHONE_NUMBER_ID');
            $this->whatsappApi = new WhatsAppBusinessApi($apiKey, $phoneNumberId);
        }
        
        // Se siamo in modalità test, registra nei log
        if ($this->testMode) {
            \PrestaShopLogger::addLog('MpWaCart in modalità TEST - Le notifiche WhatsApp saranno simulate', 1);
        }
        
        // Se usiamo il link diretto, registra nei log
        if ($this->useDirectLink) {
            \PrestaShopLogger::addLog('MpWaCart utilizza link diretto wa.me - Non verrà utilizzata l\'API WhatsApp Business', 1);
        }
    }

    /**
     * Send a notification to the owner
     *
     * @param \Cart $cart
     * @param string $customerPhone
     * @param string $pdfUrl
     * @param int $id_request
     * @param object|null $guestCustomer Dati del cliente non registrato (opzionale)
     * @return bool
     */
    public function notifyOwner($cart, $customerPhone, $pdfUrl, $id_request, $guestCustomer = null)
    {
        $ownerPhone = \Configuration::get('MPWACART_OWNER_PHONE');
        if (!$ownerPhone) {
            return false;
        }

        // Se abbiamo i dati del cliente non registrato, li utilizziamo
        if ($guestCustomer !== null) {
            $customerName = $guestCustomer->firstname . ' ' . $guestCustomer->lastname;
            $customerEmail = $guestCustomer->email;
        } else {
            // Altrimenti prendiamo i dati dal cliente registrato
            $customer = new \Customer($cart->id_customer);
            $customerName = $customer->firstname . ' ' . $customer->lastname;
            $customerEmail = $customer->email;
        }
        
        // Messaggio al titolare
        $message = "Nuova richiesta di preventivo!\n\n";
        $message .= "Cliente: " . $customerName . "\n";
        $message .= "Telefono: " . $customerPhone . "\n";
        $message .= "Email: " . $customerEmail . "\n";
        $message .= "Totale carrello: " . \Tools::displayPrice($cart->getOrderTotal(true)) . "\n";
        $message .= "Prodotti: " . $cart->nbProducts() . "\n\n";
        $message .= "Il PDF con i dettagli è allegato a questo messaggio.";
        
        // Salva il messaggio nel database
        $conversation = new WaCartConversation();
        $conversation->id_request = $id_request;
        $conversation->message = $message;
        $conversation->direction = 'outgoing';
        $conversation->save();
        
        if ($this->testMode) {
            // MODALITÀ TEST: Simula l'invio del messaggio e registra nei log
            \PrestaShopLogger::addLog(
                'TEST WhatsApp - Messaggio al titolare: ' . $ownerPhone . '\n' . $message, 
                1, // Info level
                null,
                'MpWaCart',
                $id_request
            );
            
            // Simula l'invio del PDF
            \PrestaShopLogger::addLog(
                'TEST WhatsApp - Invio PDF al titolare: ' . $ownerPhone . '\n' . 
                'URL: ' . $pdfUrl . '\n' . 
                'Nome: Preventivo Carrello #' . $cart->id, 
                1,
                null,
                'MpWaCart',
                $id_request
            );
            
            return true;
        } else if ($this->useDirectLink) {
            // MODALITÀ LINK DIRETTO: Genera un link wa.me ma non lo invia automaticamente
            // Registra nei log per riferimento
            $encodedMessage = urlencode($message);
            $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $ownerPhone) . "?text=" . $encodedMessage;
            
            \PrestaShopLogger::addLog(
                'Link diretto WhatsApp generato per il titolare: ' . $waLink, 
                1, // Info level
                null,
                'MpWaCart',
                $id_request
            );
            
            // In questa modalità non possiamo inviare automaticamente il PDF
            \PrestaShopLogger::addLog(
                'Nota: Con il link diretto non è possibile inviare automaticamente il PDF. ' . 
                'URL del PDF: ' . $pdfUrl, 
                1,
                null,
                'MpWaCart',
                $id_request
            );
            
            return true;
        } else {
            // MODALITÀ API: Invia il messaggio tramite l'API WhatsApp
            $result = $this->whatsappApi->sendTextMessage($ownerPhone, $message);
            
            if ($result['success']) {
                // Invia il PDF
                $pdfResult = $this->whatsappApi->sendDocumentMessage(
                    $ownerPhone,
                    $pdfUrl,
                    "Preventivo Carrello #" . $cart->id
                );
                
                return $pdfResult['success'];
            }
            
            return false;
        }
    }

    /**
     * Send a notification to the customer
     *
     * @param \Cart $cart
     * @param string $customerPhone
     * @param int $id_request
     * @param object|null $guestCustomer Dati del cliente non registrato (opzionale)
     * @return bool
     */
    public function notifyCustomer($cart, $customerPhone, $id_request, $guestCustomer = null)
    {
        // Se abbiamo i dati del cliente non registrato, li utilizziamo
        if ($guestCustomer !== null) {
            $customerName = $guestCustomer->firstname;
        } else {
            // Altrimenti prendiamo i dati dal cliente registrato
            $customer = new \Customer($cart->id_customer);
            $customerName = $customer->firstname;
        }
        
        // Messaggio al cliente
        $template = \Configuration::get('MPWACART_MESSAGE_TEMPLATE');
        if (!$template) {
            $template = "Grazie {customer_name} per la tua richiesta di preventivo!\n\nAbbiamo ricevuto il tuo carrello con {products_count} prodotti per un totale di {total}.\n\nTi contatteremo al più presto per confermare i dettagli e fornirti un preventivo personalizzato.";
        }
        
        // Sostituisci le variabili nel template
        $message = str_replace(
            ['{customer_name}', '{products_count}', '{total}'],
            [$customerName, $cart->nbProducts(), \Tools::displayPrice($cart->getOrderTotal(true))],
            $template
        );
        
        // Salva il messaggio nel database
        $conversation = new WaCartConversation();
        $conversation->id_request = $id_request;
        $conversation->message = $message;
        $conversation->direction = 'outgoing';
        $conversation->save();
        
        if ($this->testMode) {
            // MODALITÀ TEST: Simula l'invio del messaggio e registra nei log
            \PrestaShopLogger::addLog(
                'TEST WhatsApp - Messaggio al cliente: ' . $customerPhone . '\n' . $message, 
                1, // Info level
                null,
                'MpWaCart',
                $id_request
            );
            
            return true;
        } else if ($this->useDirectLink) {
            // MODALITÀ LINK DIRETTO: Genera un link wa.me ma non lo invia automaticamente
            $encodedMessage = urlencode($message);
            $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $customerPhone) . "?text=" . $encodedMessage;
            
            \PrestaShopLogger::addLog(
                'Link diretto WhatsApp generato per il cliente: ' . $waLink, 
                1, // Info level
                null,
                'MpWaCart',
                $id_request
            );
            
            return true;
        } else {
            // MODALITÀ API: Invia il messaggio tramite l'API WhatsApp
            $result = $this->whatsappApi->sendTextMessage($customerPhone, $message);
            return $result['success'];
        }
    }
}
