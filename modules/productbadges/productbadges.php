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
            && $this->registerHook('displayProductPriceBlock')
            && Configuration::updateValue('PRODUCTBADGES_GLOBAL_ACTIVE', 1)
            && Configuration::updateValue('PRODUCTBADGES_SHOW_LIST', 1)
            && Configuration::updateValue('PRODUCTBADGES_SHOW_PRODUCT', 1)
            && Configuration::updateValue('PRODUCTBADGES_MAX_BADGES', 3);
    }

    public function uninstall()
    {
        if (file_exists(dirname(__FILE__) . '/sql/uninstall.php')) {
            require_once dirname(__FILE__) . '/sql/uninstall.php';
        }

        return parent::uninstall() 
            && $this->uninstallTab()
            && Configuration::deleteByName('PRODUCTBADGES_GLOBAL_ACTIVE')
            && Configuration::deleteByName('PRODUCTBADGES_SHOW_LIST')
            && Configuration::deleteByName('PRODUCTBADGES_SHOW_PRODUCT')
            && Configuration::deleteByName('PRODUCTBADGES_MAX_BADGES');
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminProductBadges';
        $tab->name = [];
        
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Product Badges';
        }
        
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;
        
        return $tab->save();
    }

    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminProductBadges');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    /**
     * Controlador de la página de configuración del módulo
     */
    public function getContent()
    {
        $output = '';

        // Si el formulario ha sido enviado, procesamos los datos
        if (Tools::isSubmit('submitProductBadgesConfig')) {
            $globalActive = (int)Tools::getValue('PRODUCTBADGES_GLOBAL_ACTIVE');
            $showList = (int)Tools::getValue('PRODUCTBADGES_SHOW_LIST');
            $showProduct = (int)Tools::getValue('PRODUCTBADGES_SHOW_PRODUCT');
            $maxBadges = (int)Tools::getValue('PRODUCTBADGES_MAX_BADGES');

            // Validación server-side requerida por la prueba
            if ($maxBadges < 1) {
                $output .= $this->displayError($this->l('El número máximo de etiquetas debe ser mayor que 0.'));
            } else {
                Configuration::updateValue('PRODUCTBADGES_GLOBAL_ACTIVE', $globalActive);
                Configuration::updateValue('PRODUCTBADGES_SHOW_LIST', $showList);
                Configuration::updateValue('PRODUCTBADGES_SHOW_PRODUCT', $showProduct);
                Configuration::updateValue('PRODUCTBADGES_MAX_BADGES', $maxBadges);
                
                $output .= $this->displayConfirmation($this->l('Configuración actualizada correctamente.'));
            }
        }

        return $output . $this->renderConfigForm();
    }

    /**
     * Genera el formulario usando la API de PrestaShop
     */
    protected function renderConfigForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitProductBadgesConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuración General de Etiquetas'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activar módulo globalmente'),
                        'name' => 'PRODUCTBADGES_GLOBAL_ACTIVE',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Sí', [], 'Admin.Global')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Mostrar en listados de productos'),
                        'name' => 'PRODUCTBADGES_SHOW_LIST',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'list_on', 'value' => 1, 'label' => $this->trans('Sí', [], 'Admin.Global')],
                            ['id' => 'list_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Mostrar en ficha de producto'),
                        'name' => 'PRODUCTBADGES_SHOW_PRODUCT',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'prod_on', 'value' => 1, 'label' => $this->trans('Sí', [], 'Admin.Global')],
                            ['id' => 'prod_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Número máximo de badges visibles por producto'),
                        'name' => 'PRODUCTBADGES_MAX_BADGES',
                        'class' => 'fixed-width-xs',
                        'required' => true,
                        'desc' => $this->l('Si un producto tiene más etiquetas asignadas que este número, solo se mostrarán las primeras.')
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Guardar', [], 'Admin.Actions'),
                ],
            ],
        ];

        return $helper->generateForm([$form]);
    }

    /**
     * Recupera los valores actuales de la base de datos para rellenar el formulario
     */
    protected function getConfigFormValues()
    {
        return [
            'PRODUCTBADGES_GLOBAL_ACTIVE' => Configuration::get('PRODUCTBADGES_GLOBAL_ACTIVE', 1),
            'PRODUCTBADGES_SHOW_LIST' => Configuration::get('PRODUCTBADGES_SHOW_LIST', 1),
            'PRODUCTBADGES_SHOW_PRODUCT' => Configuration::get('PRODUCTBADGES_SHOW_PRODUCT', 1),
            'PRODUCTBADGES_MAX_BADGES' => Configuration::get('PRODUCTBADGES_MAX_BADGES', 3),
        ];
    }
}