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

$sql = [];

// Aggiungi i campi per i clienti non registrati
$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'mpwacart_request` 
    ADD COLUMN `customer_firstname` varchar(255) DEFAULT NULL,
    ADD COLUMN `customer_lastname` varchar(255) DEFAULT NULL,
    ADD COLUMN `customer_email` varchar(255) DEFAULT NULL;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

return true;
