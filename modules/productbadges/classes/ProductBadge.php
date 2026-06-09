<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductBadge extends ObjectModel
{
    public $id_productbadges;
    public $color_bg;
    public $color_text;
    public $position;
    public $active;
    public $date_add;
    public $date_upd;
    
    // Campo multilenguaje
    public $text;

    public static $definition = [
        'table' => 'productbadges',
        'primary' => 'id_productbadges',
        'multilang' => true,
        'fields' => [
            // Campos estándar
            'color_bg'   => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true, 'size' => 7],
            'color_text' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true, 'size' => 7],
            'position'   => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 10],
            'active'     => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'date_add'   => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd'   => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            // Campos multilenguaje
            'text'       => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255],
        ],
    ];
}