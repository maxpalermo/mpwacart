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

class WaCartConversation extends \ObjectModel
{
    /**
     * @var int
     */
    public $id_conversation;

    /**
     * @var int
     */
    public $id_request;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $direction;

    /**
     * @var string
     */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'mpwacart_conversation',
        'primary' => 'id_conversation',
        'fields' => [
            'id_request' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'message' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'direction' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * Get conversations by request ID
     *
     * @param int $id_request
     * @return array
     */
    public static function getByRequestId($id_request)
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

        $conversations = [];
        foreach ($result as $row) {
            $conversation = new WaCartConversation();
            $conversation->hydrate($row);
            $conversations[] = $conversation;
        }

        return $conversations;
    }
}
