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

class MpWaCartConfirmationModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $id_request = (int) Tools::getValue('id_request');
        $request = new WaCartRequest($id_request);
        
        if (!Validate::isLoadedObject($request)) {
            Tools::redirect('index.php?controller=cart');
        }
        
        $cart = new Cart($request->id_cart);
        
        // Ottieni i prodotti nel carrello in modo corretto
        $products = $cart->getProducts();
        $presenter = new PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter();
        $presentedCart = $presenter->present($cart);
        
        // Preserviamo la variabile $cart globale originale per evitare conflitti
        $originalCart = $this->context->smarty->getTemplateVars('cart');
        
        // Verifica se è attiva l'opzione per il link diretto wa.me
        $useDirectLink = (bool)Configuration::get('MPWACART_USE_DIRECT_LINK');
        $ownerPhone = Configuration::get('MPWACART_OWNER_PHONE');
        
        // Prepara i link diretti wa.me se l'opzione è attiva
        $waLinkCustomer = '';
        $waLinkOwner = '';
        
        if ($useDirectLink) {
            // Prepara il messaggio per il cliente
            $customer = new Customer($cart->id_customer);
            $customerName = $customer->firstname;
            
            $template = Configuration::get('MPWACART_MESSAGE_TEMPLATE');
            if (!$template) {
                $template = "Grazie {customer_name} per la tua richiesta di preventivo!\n\nAbbiamo ricevuto il tuo carrello con {products_count} prodotti per un totale di {total}.\n\nTi contatteremo al più presto per confermare i dettagli e fornirti un preventivo personalizzato.";
            }
            
            $customerMessage = str_replace(
                ['{customer_name}', '{products_count}', '{total}'],
                [$customerName, $cart->nbProducts(), Tools::displayPrice($cart->getOrderTotal(true))],
                $template
            );
            
            // Prepara il messaggio per il titolare
            $ownerMessage = "Nuova richiesta di preventivo!\n\n";
            $ownerMessage .= "Cliente: " . $customer->firstname . ' ' . $customer->lastname . "\n";
            $ownerMessage .= "Telefono: " . $request->phone_number . "\n";
            $ownerMessage .= "Email: " . $customer->email . "\n";
            $ownerMessage .= "Totale carrello: " . Tools::displayPrice($cart->getOrderTotal(true)) . "\n";
            $ownerMessage .= "Prodotti: " . $cart->nbProducts() . "\n";
            
            // Crea i link wa.me
            $waLinkCustomer = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $request->phone_number) . '?text=' . urlencode($customerMessage);
            $waLinkOwner = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $ownerPhone) . '?text=' . urlencode($ownerMessage);
        }
        
        $this->context->smarty->assign([
            'request' => $request,
            'cart_summary' => $presentedCart,
            'mpwacart_cart' => $presentedCart, // Utilizziamo lo stesso nome usato nel template request_form.tpl
            'products' => $products,
            'cart_total' => $cart->getOrderTotal(true),
            'customer_phone' => $request->phone_number,
            'whatsapp_link' => 'https://wa.me/' . str_replace(['+', ' ', '-'], '', $request->phone_number),
            'use_direct_link' => $useDirectLink,
            'wa_link_customer' => $waLinkCustomer,
            'wa_link_owner' => $waLinkOwner,
            'owner_phone' => $ownerPhone
        ]);
        
        // Ripristiniamo la variabile $cart originale se esisteva
        if ($originalCart !== null) {
            $this->context->smarty->assign('cart', $originalCart);
        } else {
            // Se non esisteva, assegniamo la versione presentata
            $this->context->smarty->assign('cart', $presentedCart);
        }
        
        $this->setTemplate('module:mpwacart/views/templates/front/confirmation.tpl');
    }
}
