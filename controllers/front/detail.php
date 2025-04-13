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
use MpSoft\MpWaCart\Repository\WaCartRequestRepository;

class MpWaCartDetailModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($this->context->link->getModuleLink('mpwacart', 'detail', ['id_request' => Tools::getValue('id_request')])));
        }
        
        $id_request = (int) Tools::getValue('id_request');
        $request = new WaCartRequest($id_request);
        
        if (!Validate::isLoadedObject($request) || $request->id_customer != $this->context->customer->id) {
            Tools::redirect('index.php?controller=pagenotfound');
        }
        
        $cart = new Cart($request->id_cart);
        
        // Ottieni la conversazione
        $repository = new WaCartRequestRepository();
        $conversations = $repository->getConversations($id_request);
        
        // Aggiungi le informazioni sullo stato
        switch ($request->status) {
            case 'pending':
                $status_label = $this->module->l('In attesa');
                $status_class = 'warning';
                break;
            case 'sent':
                $status_label = $this->module->l('Inviato');
                $status_class = 'info';
                break;
            case 'replied':
                $status_label = $this->module->l('Risposto');
                $status_class = 'primary';
                break;
            case 'completed':
                $status_label = $this->module->l('Completato');
                $status_class = 'success';
                break;
            case 'cancelled':
                $status_label = $this->module->l('Annullato');
                $status_class = 'danger';
                break;
            default:
                $status_label = $request->status;
                $status_class = 'secondary';
        }
        
        $this->context->smarty->assign([
            'request' => $request,
            'cart' => $cart,
            'products' => $cart->getProducts(),
            'conversations' => $conversations,
            'status_label' => $status_label,
            'status_class' => $status_class,
            'whatsapp_link' => 'https://wa.me/' . str_replace(['+', ' ', '-'], '', $request->phone_number)
        ]);
        
        $this->setTemplate('module:mpwacart/views/templates/front/detail.tpl');
    }
}
