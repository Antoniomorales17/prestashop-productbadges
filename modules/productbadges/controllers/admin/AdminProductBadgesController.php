<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminProductBadgesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'productbadges';
        $this->className = 'ProductBadge';
        $this->identifier = 'id_productbadges';
        $this->lang = true;

        parent::__construct();

        $this->fields_list = [
            'id_productbadges' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'text' => [
                'title' => $this->l('Texto de la Etiqueta')
            ],
            'color_bg' => [
                'title' => $this->l('Color de Fondo'),
                'type' => 'color'
            ],
            'color_text' => [
                'title' => $this->l('Color de Texto'),
                'type' => 'color'
            ],
            'position' => [
                'title' => $this->l('Posición')
            ],
            'active' => [
                'title' => $this->trans('Activo', [], 'Admin.Global'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center'
            ]
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Configuración de la Etiqueta'),
                'icon' => 'icon-tag'
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Texto'),
                    'name' => 'text',
                    'lang' => true, // Aquí estaba el despiste corregido
                    'required' => true,
                    'desc' => $this->l('Texto que aparecerá en la etiqueta (ej. NUEVO, OFERTA)')
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Color de Fondo'),
                    'name' => 'color_bg',
                    'required' => true
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Color del Texto'),
                    'name' => 'color_text',
                    'required' => true
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Posición'),
                    'name' => 'position',
                    'required' => true,
                    'options' => [
                        'query' => [
                            ['id' => 'left', 'name' => $this->l('Superior Izquierda')],
                            ['id' => 'right', 'name' => $this->l('Superior Derecha')]
                        ],
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Activo', [], 'Admin.Global'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Sí', [], 'Admin.Global')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')]
                    ]
                ]
            ],
            'submit' => [
                'title' => $this->trans('Guardar', [], 'Admin.Actions')
            ]
        ];

        return parent::renderForm();
    }
}