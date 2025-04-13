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

use MpSoft\MpWaCart\Api\WhatsAppBusinessApi;
use MpSoft\MpWaCart\Entity\WaCartConversation;
use MpSoft\MpWaCart\Entity\WaCartRequest;
use MpSoft\MpWaCart\Repository\WaCartRequestRepository;

class AdminWaCartRequestsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->translator = Context::getContext()->getTranslator();
        $this->bootstrap = true;
        $this->table = 'mpwacart_request';
        $this->identifier = 'id_request';
        $this->className = 'MpSoft\\MpWaCart\\Entity\\WaCartRequest';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;
        $this->context = Context::getContext();

        // Inizializza la tabella admin con le configurazioni necessarie
        $this->initAdminTable();

        parent::__construct();
    }

    public function initAdminTable()
    {
        // Configurazione delle query SQL per il join con la tabella customer
        $this->_select = 'CONCAT(c.`firstname`, " ", c.`lastname`) as customer_name';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_where = 'AND a.`id_customer` != 0';

        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->fields_list = [
            'id_request' => [
                'title' => $this->trans('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'id_cart' => [
                'title' => $this->trans('ID Carrello'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
            ],
            'customer_name' => [
                'title' => $this->trans('Cliente'),
                'havingFilter' => true,
                'filter_key' => 'c.lastname',
            ],
            'phone_number' => [
                'title' => $this->trans('Telefono'),
                'align' => 'center',
            ],
            'total' => [
                'title' => $this->trans('Totale'),
                'align' => 'right',
                'type' => 'price',
                'currency' => true,
            ],
            'status' => [
                'title' => $this->trans('Stato'),
                'align' => 'center',
                'type' => 'select',
                'list' => [
                    'pending' => $this->trans('In attesa'),
                    'sent' => $this->trans('Inviato'),
                    'replied' => $this->trans('Risposto'),
                    'completed' => $this->trans('Completato'),
                    'cancelled' => $this->trans('Annullato'),
                ],
                'filter_key' => 'a!status',
                'badge_success' => 'completed',
                'badge_warning' => 'pending,sent',
                'badge_danger' => 'cancelled',
            ],
            'date_add' => [
                'title' => $this->trans('Data creazione'),
                'align' => 'center',
                'type' => 'datetime',
            ],
        ];
    }

    /**
     * Override initProcess to ensure joins and selects are applied
     */
    public function initProcess()
    {
        // Assicuriamoci che i join e le selezioni siano applicate
        parent::initProcess();
    }

    /**
     * Render view
     */
    public function renderView()
    {
        $id_request = (int) Tools::getValue('id_request');
        $request = new WaCartRequest($id_request);

        if (!Validate::isLoadedObject($request)) {
            $this->errors[] = $this->l('Richiesta non trovata');

            return $this->createTemplate('error.tpl')->fetch();
        }

        $cart = new Cart($request->id_cart);
        $customer = new Customer($request->id_customer);

        // Ottieni la conversazione
        $repository = new WaCartRequestRepository();
        $conversations = $repository->getConversations($id_request);

        $this->context->smarty->assign([
            'request' => $request,
            'cart' => $cart,
            'customer' => $customer,
            'products' => $cart->getProducts(),
            'conversations' => $conversations,
            'pdf_url' => $this->context->link->getBaseLink() . 'modules/mpwacart/pdf/' . basename($request->pdf_path),
            'statuses' => [
                'pending' => $this->trans('In attesa'),
                'sent' => $this->trans('Inviato'),
                'replied' => $this->trans('Risposto'),
                'completed' => $this->trans('Completato'),
                'cancelled' => $this->trans('Annullato'),
            ],
            'current_status' => $request->status,
            'whatsapp_link' => 'https://wa.me/' . str_replace(['+', ' ', '-'], '', $request->phone_number),
        ]);

        return $this->createTemplate('request_view.tpl')->fetch();
    }

    /**
     * Send message via AJAX
     */
    public function ajaxProcessSendMessage()
    {
        $id_request = (int) Tools::getValue('id_request');
        $message = Tools::getValue('message');

        $request = new WaCartRequest($id_request);

        if (!Validate::isLoadedObject($request)) {
            die(json_encode([
                'success' => false,
                'message' => $this->trans('Richiesta non trovata'),
            ]));
        }

        // Invia messaggio WhatsApp
        $apiKey = Configuration::get('MPWACART_API_KEY');
        $phoneNumberId = Configuration::get('MPWACART_PHONE_NUMBER_ID');

        $whatsappApi = new WhatsAppBusinessApi($apiKey, $phoneNumberId);
        $result = $whatsappApi->sendTextMessage($request->phone_number, $message);

        if (!$result['success']) {
            die(json_encode([
                'success' => false,
                'message' => isset($result['error']) ? $result['error'] : $this->l('Errore durante l\'invio del messaggio'),
            ]));
        }

        // Salva messaggio nel database
        $conversation = new WaCartConversation();
        $conversation->id_request = $id_request;
        $conversation->message = $message;
        $conversation->direction = 'outgoing';
        $conversation->save();

        // Aggiorna stato se necessario
        if ($request->status == 'pending' || $request->status == 'sent') {
            $request->status = 'replied';
            $request->update();
        }

        die(json_encode([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'message' => $conversation->message,
                'direction' => $conversation->direction,
                'date' => $conversation->date_add,
            ],
        ]));
    }

    /**
     * Update status via AJAX
     */
    public function ajaxProcessUpdateStatus()
    {
        $id_request = (int) Tools::getValue('id_request');
        $status = Tools::getValue('status');

        $request = new WaCartRequest($id_request);

        if (!Validate::isLoadedObject($request)) {
            die(json_encode([
                'success' => false,
                'message' => $this->trans('Richiesta non trovata'),
            ]));
        }

        $valid_statuses = ['pending', 'sent', 'replied', 'completed', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            die(json_encode([
                'success' => false,
                'message' => $this->trans('Stato non valido'),
            ]));
        }

        $request->status = $status;
        $result = $request->update();

        die(json_encode([
            'success' => $result,
            'message' => $result ? $this->trans('Stato aggiornato') : $this->trans('Errore durante l\'aggiornamento dello stato'),
        ]));
    }
}
