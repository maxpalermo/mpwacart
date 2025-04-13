<?php

use MpSoft\MpWaCart\Entity\WaCartRequest;

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
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use MpSoft\MpWaCart\Install\InstallMenu;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class MpWaCart extends Module implements WidgetInterface
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'mpwacart';
        $this->tab = 'front_office_features';
        $this->version = '1.1.3';
        $this->author = 'Massimiliano Palermo';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('WhatsApp Cart');
        $this->description = $this->l('Invia il carrello via WhatsApp per richiedere un preventivo');
        $this->confirmUninstall = $this->l('Sei sicuro di voler disinstallare questo modulo?');

        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];
    }

    /**
     * Install the module
     *
     * @return bool
     */
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';

        // Installa il tab di amministrazione
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminWaCartRequests';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Richieste WhatsApp';
        }
        // Utilizzo dell'approccio consigliato per PrestaShop 8
        $tabRepository = $this->get('prestashop.core.admin.tab.repository');
        $tabId = $tabRepository->findOneIdByClassName('AdminParentOrders');
        $tab->id_parent = (int) $tabId;
        $tab->module = $this->name;
        $tab->add();

        // Imposta la versione iniziale del modulo
        Configuration::updateValue('MPWACART_VERSION', $this->version);

        $installMenu = new InstallMenu($this);

        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayShoppingCartFooter')
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayAdminOrder')
            && $installMenu->installMenu(
                'AdminWaCartRequests',
                'Richieste WhatsApp',
                'AdminParentOrders',
                '',
                'admin_wa_cart_requests',
                'Richieste WhatsApp',
                'Modules',
                null,
                true,
                true
            );
    }

    /**
     * Esegue gli aggiornamenti del modulo quando viene aggiornata la versione
     *
     * @return array Risultato dell'aggiornamento
     */
    public function runUpgradeModule()
    {
        $result = [];
        $oldVersion = Configuration::get('MPWACART_VERSION');

        if (!$oldVersion) {
            // Se non esiste la versione precedente, imposta la versione attuale
            Configuration::updateValue('MPWACART_VERSION', $this->version);
            $result[] = $this->displayConfirmation($this->l('Modulo inizializzato alla versione ') . $this->version);

            return $result;
        }

        if (version_compare($oldVersion, '1.1.0', '<')) {
            // Esegui l'aggiornamento alla versione 1.1.0
            if (!include (dirname(__FILE__) . '/sql/update_1.1.0.php')) {
                $result[] = $this->displayError($this->l('Errore durante l\'aggiornamento alla versione 1.1.0'));

                return $result;
            }
            $result[] = $this->displayConfirmation($this->l('Aggiornamento alla versione 1.1.0 completato con successo'));
        }

        // Aggiorna la versione salvata
        Configuration::updateValue('MPWACART_VERSION', $this->version);

        return $result;
    }

    /**
     * Uninstall the module
     *
     * @return bool
     */
    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';

        // Rimuovi il tab di amministrazione
        $installMenu = new InstallMenu($this);
        $installMenu->uninstallMenu('AdminWaCartRequests');

        // Rimuovi le configurazioni
        Configuration::deleteByName('MPWACART_API_KEY');
        Configuration::deleteByName('MPWACART_PHONE_NUMBER_ID');
        Configuration::deleteByName('MPWACART_OWNER_PHONE');
        Configuration::deleteByName('MPWACART_MESSAGE_TEMPLATE');
        Configuration::deleteByName('MPWACART_TEST_MODE');
        Configuration::deleteByName('MPWACART_HIDE_CHECKOUT');
        Configuration::deleteByName('MPWACART_USE_DIRECT_LINK');
        Configuration::deleteByName('MPWACART_VERSION');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $whatsappApiKey = Tools::getValue('MPWACART_API_KEY');
            $whatsappPhoneNumberId = Tools::getValue('MPWACART_PHONE_NUMBER_ID');
            $ownerPhone = Tools::getValue('MPWACART_OWNER_PHONE');
            $messageTemplate = Tools::getValue('MPWACART_MESSAGE_TEMPLATE');
            $testMode = (int) Tools::getValue('MPWACART_TEST_MODE');
            $hideCheckoutButton = (int) Tools::getValue('MPWACART_HIDE_CHECKOUT');
            $useDirectLink = (int) Tools::getValue('MPWACART_USE_DIRECT_LINK');

            Configuration::updateValue('MPWACART_API_KEY', $whatsappApiKey);
            Configuration::updateValue('MPWACART_PHONE_NUMBER_ID', $whatsappPhoneNumberId);
            Configuration::updateValue('MPWACART_OWNER_PHONE', $ownerPhone);
            Configuration::updateValue('MPWACART_MESSAGE_TEMPLATE', $messageTemplate);
            Configuration::updateValue('MPWACART_TEST_MODE', $testMode);
            Configuration::updateValue('MPWACART_HIDE_CHECKOUT', $hideCheckoutButton);
            Configuration::updateValue('MPWACART_USE_DIRECT_LINK', $useDirectLink);

            $output .= $this->displayConfirmation($this->l('Impostazioni aggiornate con successo'));
        }

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Impostazioni'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('WhatsApp Business API Key'),
                        'name' => 'MPWACART_API_KEY',
                        'desc' => $this->l('Inserisci la tua API Key di WhatsApp Business'),
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('WhatsApp Phone Number ID'),
                        'name' => 'MPWACART_PHONE_NUMBER_ID',
                        'desc' => $this->l('Inserisci l\'ID del numero di telefono WhatsApp Business'),
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Numero di telefono del titolare'),
                        'name' => 'MPWACART_OWNER_PHONE',
                        'desc' => $this->l('Inserisci il numero di telefono del titolare che riceverà le notifiche (formato internazionale, es. +393401234567)'),
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Template del messaggio'),
                        'name' => 'MPWACART_MESSAGE_TEMPLATE',
                        'desc' => $this->l('Template del messaggio inviato al cliente. Puoi usare le variabili: {customer_name}, {total}, {products_count}'),
                        'required' => true,
                        'cols' => 40,
                        'rows' => 10,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Modalità test'),
                        'name' => 'MPWACART_TEST_MODE',
                        'desc' => $this->l('Attiva la modalità test per simulare l\'invio dei messaggi WhatsApp senza utilizzare l\'API. I messaggi verranno registrati nei log di PrestaShop.'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Sì'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Nascondi pulsante checkout'),
                        'name' => 'MPWACART_HIDE_CHECKOUT',
                        'desc' => $this->l('Nascondi il pulsante "Procedi con il checkout" nella pagina del carrello quando è presente il pulsante WhatsApp.'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Sì'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Usa link diretto WhatsApp'),
                        'name' => 'MPWACART_USE_DIRECT_LINK',
                        'desc' => $this->l('Utilizza il link diretto wa.me invece dell\'API WhatsApp Business. Questa opzione non richiede un account business.'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Sì'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Salva'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'MPWACART_API_KEY' => Configuration::get('MPWACART_API_KEY'),
            'MPWACART_PHONE_NUMBER_ID' => Configuration::get('MPWACART_PHONE_NUMBER_ID'),
            'MPWACART_OWNER_PHONE' => Configuration::get('MPWACART_OWNER_PHONE'),
            'MPWACART_MESSAGE_TEMPLATE' => Configuration::get('MPWACART_MESSAGE_TEMPLATE') ?: 'Grazie {customer_name} per la tua richiesta di preventivo! Abbiamo ricevuto il tuo carrello con {products_count} prodotti per un totale di {total}. Ti contatteremo al più presto per confermare i dettagli.',
            'MPWACART_TEST_MODE' => (int) Configuration::get('MPWACART_TEST_MODE'),
            'MPWACART_HIDE_CHECKOUT' => (int) Configuration::get('MPWACART_HIDE_CHECKOUT'),
            'MPWACART_USE_DIRECT_LINK' => (int) Configuration::get('MPWACART_USE_DIRECT_LINK'),
        ];
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookActionFrontControllerSetMedia()
    {
        $controller = $this->context->controller->php_self;

        if ($controller == 'cart' || $controller == 'order') {
            $this->context->controller->registerStylesheet(
                'mpwacart-style',
                'modules/' . $this->name . '/views/css/front.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                ]
            );

            $this->context->controller->registerJavascript(
                'mpwacart-script',
                'modules/' . $this->name . '/views/js/front.js',
                [
                    'position' => 'bottom',
                    'priority' => 200,
                ]
            );
        }
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookDisplayShoppingCartFooter()
    {
        // Verifica che il carrello esista e contenga prodotti
        if (!isset($this->context->cart) || !$this->context->cart->nbProducts()) {
            return;
        }

        // Salva temporaneamente le variabili Smarty che potrebbero entrare in conflitto
        $originalProduct = $this->context->smarty->getTemplateVars('product');

        $this->context->smarty->assign([
            'mpwacart_request_url' => $this->context->link->getModuleLink('mpwacart', 'request'),
            'cart_products_count' => $this->context->cart->nbProducts(),
            'cart_total' => $this->context->cart->getOrderTotal(true),
            'hide_checkout_button' => (bool) Configuration::get('MPWACART_HIDE_CHECKOUT'),
        ]);

        $output = $this->display(__FILE__, 'views/templates/hook/cart_footer.tpl');

        // Ripristina le variabili originali
        if ($originalProduct !== null) {
            $this->context->smarty->assign('product', $originalProduct);
        }

        return $output;
    }

    public function hookDisplayCustomerAccount()
    {
        return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
    }

    public function hookDisplayAdminOrder($params)
    {
        $id_order = $params['id_order'];
        $order = new Order($id_order);

        // Verifica se esiste una richiesta per questo carrello
        $id_cart = $order->id_cart;
        $requests = WaCartRequest::getByCartId($id_cart);

        if (!$requests) {
            return '';
        }

        $this->context->smarty->assign([
            'requests' => $requests,
            'mpwacart_admin_link' => $this->context->link->getAdminLink('AdminWaCartRequests'),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/admin_order.tpl');
    }

    public function renderWidget($hookName, array $configuration)
    {
        if ($hookName == 'displayShoppingCartFooter') {
            return $this->hookDisplayShoppingCartFooter();
        }

        return '';
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        if ($hookName == 'displayShoppingCartFooter') {
            return [
                'mpwacart_request_url' => $this->context->link->getModuleLink('mpwacart', 'request'),
                'cart_products_count' => $this->context->cart->nbProducts(),
                'cart_total' => $this->context->cart->getOrderTotal(true),
                'hide_checkout_button' => (bool) Configuration::get('MPWACART_HIDE_CHECKOUT'),
            ];
        }

        return [];
    }
}
