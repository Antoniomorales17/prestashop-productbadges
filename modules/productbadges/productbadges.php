<?php
/**
 * Copyright Blinders Group Test
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/ProductBadge.php';

class ProductBadges extends Module
{
    public function __construct()
    {
        $this->name = 'productbadges';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Antonio Morales';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Badges');
        $this->description = $this->l('Gestiona etiquetas visuales personalizadas para los productos.');

        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => '1.7.8.11'];
    }

    public function install()
    {
        if (file_exists(dirname(__FILE__) . '/sql/install.php')) {
            require_once dirname(__FILE__) . '/sql/install.php';
        }

        return parent::install() 
            && $this->installTab()
            && $this->registerHook('header')
            && $this->registerHook('displayProductPriceBlock');
    }

    public function uninstall()
    {
        if (file_exists(dirname(__FILE__) . '/sql/uninstall.php')) {
            require_once dirname(__FILE__) . '/sql/uninstall.php';
        }

        return parent::uninstall() 
            && $this->uninstallTab();
    }

    /**
     * Crea la pestaña en el menú lateral del Back Office
     */
    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminProductBadges';
        $tab->name = [];
        
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Product Badges';
        }
        
        // Lo colgamos del menú "Catálogo"
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;
        
        return $tab->save();
    }

    /**
     * Elimina la pestaña al desinstalar (Cumple requisito de "sin pestañas huérfanas")
     */
    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminProductBadges');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }
}