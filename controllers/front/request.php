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

use MpSoft\MpWaCart\Entity\WaCartRequest;
use MpSoft\MpWaCart\Service\PdfGenerator;
use MpSoft\MpWaCart\Service\NotificationService;

class MpWaCartRequestModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        
        // Esegui prima il postProcess per gestire l'invio del form
        $this->postProcess();
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $cart = $this->context->cart;
        if (!$cart->id || $cart->nbProducts() <= 0) {
            Tools::redirect('index.php?controller=cart');
        }
        
        // Ottieni i prodotti nel carrello in modo corretto
        $products = $cart->getProducts();
        $presenter = new PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter();
        $presentedCart = $presenter->present($cart);
        
        // Verifica se l'utente è loggato
        $isLoggedIn = $this->context->customer->isLogged();
        $customerInfo = [];
        
        if ($isLoggedIn) {
            $customer = $this->context->customer;
            $customerInfo = [
                'firstname' => $customer->firstname,
                'lastname' => $customer->lastname,
                'email' => $customer->email
            ];
        }
        
        // Only assign the presented cart to avoid conflicts with core templates
        $this->context->smarty->assign([
            'cart_summary' => $presentedCart,
            'products' => $products,
            'token' => Tools::getToken(false),
            'is_logged_in' => $isLoggedIn,
            'customer_info' => $customerInfo
        ]);
        
        // Preserviamo la variabile $cart globale originale per evitare conflitti
        // Salviamo temporaneamente la variabile $cart originale
        $originalCart = $this->context->smarty->getTemplateVars('cart');
        
        // Utilizziamo un nome diverso per il nostro template
        $this->context->smarty->assign('mpwacart_cart', $presentedCart);
        
        // Ripristiniamo la variabile $cart originale se esisteva
        if ($originalCart !== null) {
            $this->context->smarty->assign('cart', $originalCart);
        }
        
        // Aggiungiamo eventuali errori al template
        if (count($this->errors) > 0) {
            $this->context->smarty->assign('errors', $this->errors);
        }
        
        $this->setTemplate('module:mpwacart/views/templates/front/request_form.tpl');
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('submitWaCartRequest')) {
            $phone = Tools::getValue('phone_number');
            
            // Validazione numero di telefono
            if (!Validate::isPhoneNumber($phone)) {
                $this->errors[] = $this->module->l('Numero di telefono non valido');
                return;
            }
            
            // Verifica che sia stata accettata la privacy policy
            if (!Tools::getValue('privacy_policy')) {
                $this->errors[] = $this->module->l('Devi accettare la Privacy Policy per continuare');
                return;
            }
            
            // Verifica i campi aggiuntivi per utenti non registrati
            $isLoggedIn = $this->context->customer->isLogged();
            $customerFirstname = '';
            $customerLastname = '';
            $customerEmail = '';
            
            if (!$isLoggedIn) {
                $customerFirstname = Tools::getValue('customer_firstname');
                $customerLastname = Tools::getValue('customer_lastname');
                $customerEmail = Tools::getValue('customer_email');
                
                // Validazione dei campi
                if (empty($customerFirstname) || !Validate::isName($customerFirstname)) {
                    $this->errors[] = $this->module->l('Nome non valido');
                    return;
                }
                
                if (empty($customerLastname) || !Validate::isName($customerLastname)) {
                    $this->errors[] = $this->module->l('Cognome non valido');
                    return;
                }
                
                if (empty($customerEmail) || !Validate::isEmail($customerEmail)) {
                    $this->errors[] = $this->module->l('Email non valida');
                    return;
                }
            }
            
            try {
                $cart = $this->context->cart;
                
                // Log per debug
                PrestaShopLogger::addLog('MpWaCart: Elaborazione richiesta iniziata per carrello ID ' . $cart->id, 1);
                
                // Genera PDF
                $pdfGenerator = new PdfGenerator($this->context);
                $pdf = $pdfGenerator->generateCartPdf($cart);
                
                // Salva richiesta nel database
                $request = new WaCartRequest();
                $request->id_cart = (int)$cart->id;
                $request->id_customer = (int)$cart->id_customer;
                $request->phone_number = pSQL($phone);
                $request->total_products = (float)$cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                $request->total_shipping = (float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
                $request->total = (float)$cart->getOrderTotal(true);
                $request->status = 'pending';
                $request->pdf_path = pSQL($pdf['path']);
                
                // Salva i dati del cliente non registrato
                if (!$isLoggedIn) {
                    $request->customer_firstname = pSQL($customerFirstname);
                    $request->customer_lastname = pSQL($customerLastname);
                    $request->customer_email = pSQL($customerEmail);
                }
                
                if (!$request->save()) {
                    throw new Exception($this->module->l('Errore nel salvataggio della richiesta'));
                }
                
                PrestaShopLogger::addLog('MpWaCart: Richiesta salvata con ID ' . $request->id, 1);
                
                // Invia notifiche
                $notificationService = new NotificationService($this->context);
                
                // Se l'utente non è registrato, passa i dati del cliente al servizio di notifica
                if (!$isLoggedIn) {
                    // Creiamo un oggetto temporaneo con i dati del cliente
                    $tempCustomer = new stdClass();
                    $tempCustomer->firstname = $customerFirstname;
                    $tempCustomer->lastname = $customerLastname;
                    $tempCustomer->email = $customerEmail;
                    
                    $notificationService->notifyOwner($cart, $phone, $pdf['url'], $request->id, $tempCustomer);
                    $notificationService->notifyCustomer($cart, $phone, $request->id, $tempCustomer);
                } else {
                    $notificationService->notifyOwner($cart, $phone, $pdf['url'], $request->id);
                    $notificationService->notifyCustomer($cart, $phone, $request->id);
                }
                
                PrestaShopLogger::addLog('MpWaCart: Notifiche inviate, reindirizzamento alla pagina di conferma', 1);
                
                // Reindirizza alla pagina di conferma
                Tools::redirect($this->context->link->getModuleLink('mpwacart', 'confirmation', ['id_request' => $request->id]));
                exit; // Assicurati che l'esecuzione si fermi qui
            } catch (Exception $e) {
                // Gestione degli errori
                $this->errors[] = $this->module->l('Si è verificato un errore durante l\'elaborazione della richiesta: ') . $e->getMessage();
                PrestaShopLogger::addLog('MpWaCart error: ' . $e->getMessage(), 3);
            }
        }
    }
}
