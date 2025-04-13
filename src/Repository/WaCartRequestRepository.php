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

namespace MpSoft\MpWaCart\Repository;

use MpSoft\MpWaCart\Entity\WaCartRequest;
use MpSoft\MpWaCart\Entity\WaCartConversation;

class WaCartRequestRepository
{
    /**
     * Get all requests
     *
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @param string $orderWay
     * @return array
     */
    public function getAll($limit = 10, $offset = 0, $orderBy = 'date_add', $orderWay = 'DESC')
    {
        $query = new \DbQuery();
        $query->select('r.*, CONCAT(c.firstname, " ", c.lastname) as customer_name');
        $query->from('mpwacart_request', 'r');
        $query->leftJoin('customer', 'c', 'c.id_customer = r.id_customer');
        $query->orderBy('r.' . $orderBy . ' ' . $orderWay);
        $query->limit($limit, $offset);

        $result = \Db::getInstance()->executeS($query);
        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * Count all requests
     *
     * @return int
     */
    public function countAll()
    {
        $query = new \DbQuery();
        $query->select('COUNT(*)');
        $query->from('mpwacart_request');

        return (int) \Db::getInstance()->getValue($query);
    }

    /**
     * Get request by ID
     *
     * @param int $id_request
     * @return array|false
     */
    public function getById($id_request)
    {
        $query = new \DbQuery();
        $query->select('r.*, CONCAT(c.firstname, " ", c.lastname) as customer_name');
        $query->from('mpwacart_request', 'r');
        $query->leftJoin('customer', 'c', 'c.id_customer = r.id_customer');
        $query->where('r.id_request = ' . (int) $id_request);

        return \Db::getInstance()->getRow($query);
    }

    /**
     * Get conversations by request ID
     *
     * @param int $id_request
     * @return array
     */
    public function getConversations($id_request)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('mpwacart_conversation');
        $query->where('id_request = ' . (int) $id_request);
        $query->orderBy('date_add ASC');

        $result = \Db::getInstance()->executeS($query);
        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * Get requests by customer ID
     *
     * @param int $id_customer
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getByCustomerId($id_customer, $limit = 10, $offset = 0)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('mpwacart_request');
        $query->where('id_customer = ' . (int) $id_customer);
        $query->orderBy('date_add DESC');
        $query->limit($limit, $offset);

        $result = \Db::getInstance()->executeS($query);
        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * Get requests by status
     *
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getByStatus($status, $limit = 10, $offset = 0)
    {
        $query = new \DbQuery();
        $query->select('r.*, CONCAT(c.firstname, " ", c.lastname) as customer_name');
        $query->from('mpwacart_request', 'r');
        $query->leftJoin('customer', 'c', 'c.id_customer = r.id_customer');
        $query->where('r.status = "' . pSQL($status) . '"');
        $query->orderBy('r.date_add DESC');
        $query->limit($limit, $offset);

        $result = \Db::getInstance()->executeS($query);
        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * Count requests by status
     *
     * @param string $status
     * @return int
     */
    public function countByStatus($status)
    {
        $query = new \DbQuery();
        $query->select('COUNT(*)');
        $query->from('mpwacart_request');
        $query->where('status = "' . pSQL($status) . '"');

        return (int) \Db::getInstance()->getValue($query);
    }
}
