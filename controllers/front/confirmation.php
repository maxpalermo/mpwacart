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
            
            // Determina se utilizzare web.whatsapp.com o wa.me in base al dispositivo
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $isMobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4));
            
            // Crea i link WhatsApp
            if ($isMobile) {
                // Per dispositivi mobili, usa wa.me che apre l'app WhatsApp
                $waLinkCustomer = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $request->phone_number) . '?text=' . urlencode($customerMessage);
                $waLinkOwner = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $ownerPhone) . '?text=' . urlencode($ownerMessage);
            } else {
                // Per desktop, usa direttamente web.whatsapp.com
                $waLinkCustomer = 'https://web.whatsapp.com/send?phone=' . preg_replace('/[^0-9]/', '', $request->phone_number) . '&text=' . urlencode($customerMessage);
                $waLinkOwner = 'https://web.whatsapp.com/send?phone=' . preg_replace('/[^0-9]/', '', $ownerPhone) . '&text=' . urlencode($ownerMessage);
            }
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
