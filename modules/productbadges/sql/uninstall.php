<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

// Borramos las tres tablas creadas en la instalación
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'productbadges_product`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'productbadges_lang`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'productbadges`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

return true;
