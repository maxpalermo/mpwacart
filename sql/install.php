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

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mpwacart_request` (
    `id_request` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_cart` int(10) unsigned NOT NULL,
    `id_customer` int(10) unsigned NOT NULL,
    `phone_number` varchar(20) NOT NULL,
    `total_products` decimal(20,6) NOT NULL DEFAULT "0.000000",
    `total_shipping` decimal(20,6) NOT NULL DEFAULT "0.000000",
    `total` decimal(20,6) NOT NULL DEFAULT "0.000000",
    `status` enum("pending","sent","replied","completed","cancelled") NOT NULL DEFAULT "pending",
    `pdf_path` varchar(255) DEFAULT NULL,
    `customer_firstname` varchar(255) DEFAULT NULL,
    `customer_lastname` varchar(255) DEFAULT NULL,
    `customer_email` varchar(255) DEFAULT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_request`),
    KEY `id_cart` (`id_cart`),
    KEY `id_customer` (`id_customer`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mpwacart_conversation` (
    `id_conversation` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_request` int(10) unsigned NOT NULL,
    `message` text NOT NULL,
    `direction` enum("incoming","outgoing") NOT NULL,
    `date_add` datetime NOT NULL,
    PRIMARY KEY (`id_conversation`),
    KEY `id_request` (`id_request`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
