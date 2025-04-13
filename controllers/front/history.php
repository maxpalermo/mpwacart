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

use MpSoft\MpWaCart\Repository\WaCartRequestRepository;

class MpWaCartHistoryModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($this->context->link->getModuleLink('mpwacart', 'history')));
        }
        
        $repository = new WaCartRequestRepository();
        $page = (int) Tools::getValue('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $requests = $repository->getByCustomerId($this->context->customer->id, $limit, $offset);
        $total = $repository->countByCustomerId($this->context->customer->id);
        
        $pagination = [
            'total_items' => $total,
            'items_shown_from' => ($total > 0) ? (($page - 1) * $limit + 1) : 0,
            'items_shown_to' => ($total > ($page * $limit)) ? ($page * $limit) : $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
        ];
        
        // Aggiungi le informazioni sullo stato per ogni richiesta
        foreach ($requests as &$request) {
            switch ($request['status']) {
                case 'pending':
                    $request['status_label'] = $this->module->l('In attesa');
                    $request['status_class'] = 'warning';
                    break;
                case 'sent':
                    $request['status_label'] = $this->module->l('Inviato');
                    $request['status_class'] = 'info';
                    break;
                case 'replied':
                    $request['status_label'] = $this->module->l('Risposto');
                    $request['status_class'] = 'primary';
                    break;
                case 'completed':
                    $request['status_label'] = $this->module->l('Completato');
                    $request['status_class'] = 'success';
                    break;
                case 'cancelled':
                    $request['status_label'] = $this->module->l('Annullato');
                    $request['status_class'] = 'danger';
                    break;
                default:
                    $request['status_label'] = $request['status'];
                    $request['status_class'] = 'secondary';
            }
        }
        
        $this->context->smarty->assign([
            'requests' => $requests,
            'pagination' => $pagination,
            'pages_nb' => $pagination['pages'],
            'page' => $page,
            'prev_page' => ($page > 1) ? $page - 1 : false,
            'next_page' => ($page < $pagination['pages']) ? $page + 1 : false,
        ]);
        
        $this->setTemplate('module:mpwacart/views/templates/front/history.tpl');
    }
}
