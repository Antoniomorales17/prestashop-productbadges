<?php


if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductBadges extends Module
{
    public function __construct()
    {
        $this->name = 'productbadges';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Antonio Morales';
        $this->need_instance = 0;
        $this->bootstrap = true; // Requisito obligatorio

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
            && $this->registerHook('header') // Para CSS/JS
            && $this->registerHook('displayProductPriceBlock'); // Para pintar en los listados
    }

    public function uninstall()
    {
        if (file_exists(dirname(__FILE__) . '/sql/uninstall.php')) {
            require_once dirname(__FILE__) . '/sql/uninstall.php';
        }

        return parent::uninstall();
    }
}