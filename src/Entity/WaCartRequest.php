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

namespace MpSoft\MpWaCart\Entity;

class WaCartRequest extends \ObjectModel
{
    /**
     * @var int
     */
    public $id_request;

    /**
     * @var int
     */
    public $id_cart;

    /**
     * @var int
     */
    public $id_customer;

    /**
     * @var string
     */
    public $phone_number;

    /**
     * @var float
     */
    public $total_products;

    /**
     * @var float
     */
    public $total_shipping;

    /**
     * @var float
     */
    public $total;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $pdf_path;
    
    /**
     * @var string
     */
    public $customer_firstname;
    
    /**
     * @var string
     */
    public $customer_lastname;
    
    /**
     * @var string
     */
    public $customer_email;

    /**
     * @var string
     */
    public $date_add;

    /**
     * @var string
     */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'mpwacart_request',
        'primary' => 'id_request',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'phone_number' => ['type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'required' => true, 'size' => 20],
            'total_products' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'total_shipping' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'total' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'status' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'pdf_path' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'customer_firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 255],
            'customer_lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 255],
            'customer_email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * Get requests by cart ID
     *
     * @param int $id_cart
     * @return array
     */
    public static function getByCartId($id_cart)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('mpwacart_request');
        $query->where('id_cart = ' . (int) $id_cart);
        $query->orderBy('date_add DESC');

        $result = \Db::getInstance()->executeS($query);
        if (!$result) {
            return [];
        }

        $requests = [];
        foreach ($result as $row) {
            $request = new WaCartRequest();
            $request->hydrate($row);
            $requests[] = $request;
        }

        return $requests;
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        $customer = new \Customer($this->id_customer);
        if (!\Validate::isLoadedObject($customer)) {
            return '';
        }

        return $customer->firstname . ' ' . $customer->lastname;
    }

    /**
     * Get conversations
     *
     * @return array
     */
    public function getConversations()
    {
        return WaCartConversation::getByRequestId($this->id);
    }
}
