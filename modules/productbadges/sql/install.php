<?php
/**
 * Copyright Blinders Group Test
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

// 1. Tabla principal de etiquetas
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'productbadges` (
    `id_productbadges` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `color_bg` VARCHAR(7) NOT NULL DEFAULT "#000000",
    `color_text` VARCHAR(7) NOT NULL DEFAULT "#FFFFFF",
    `position` VARCHAR(10) NOT NULL DEFAULT "left",
    `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT "1",
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_productbadges`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

// 2. Tabla multilenguaje de etiquetas
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'productbadges_lang` (
    `id_productbadges` INT(11) UNSIGNED NOT NULL,
    `id_lang` INT(11) UNSIGNED NOT NULL,
    `text` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_productbadges`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

// 3. Tabla relacional Muchos a Muchos con Productos
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'productbadges_product` (
    `id_productbadges` INT(11) UNSIGNED NOT NULL,
    `id_product` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_productbadges`, `id_product`),
    KEY `id_product` (`id_product`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

// Ejecutar todas las consultas de forma segura
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}