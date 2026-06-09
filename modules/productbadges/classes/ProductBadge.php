<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductBadge extends ObjectModel
{
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';

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
            'color_bg'   => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true, 'size' => 7],
            'color_text' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true, 'size' => 7],
            'position'   => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 10],
            'active'     => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'date_add'   => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd'   => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'text'       => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255],
        ],
    ];

    public function validateFields($die = true, $error_return = false)
    {
        if (!in_array($this->position, [self::POSITION_LEFT, self::POSITION_RIGHT], true)) {
            $message = Tools::displayError('Invalid badge position.');
            if ($die) {
                die($message);
            }

            return $error_return ? $message : false;
        }

        return parent::validateFields($die, $error_return);
    }

    public static function getAllBadges($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('pb.*, pbl.text');
        $sql->from('productbadges', 'pb');
        $sql->leftJoin('productbadges_lang', 'pbl', 'pb.id_productbadges = pbl.id_productbadges AND pbl.id_lang = ' . (int)$id_lang);
        $sql->where('pb.active = 1');
        
        return Db::getInstance()->executeS($sql);
    }

    public static function getProductBadgesIds($id_product)
    {
        $sql = new DbQuery();
        $sql->select('id_productbadges');
        $sql->from('productbadges_product');
        $sql->where('id_product = ' . (int)$id_product);
        
        $result = Db::getInstance()->executeS($sql);
        $ids = [];
        if ($result) {
            foreach ($result as $row) {
                $ids[] = $row['id_productbadges'];
            }
        }
        return $ids;
    }

    public static function updateProductBadges($id_product, $badges)
    {
        Db::getInstance()->delete('productbadges_product', 'id_product = ' . (int)$id_product);
        
        if (!empty($badges) && is_array($badges)) {
            $insert = [];
            $badges = array_unique(array_map('intval', $badges));

            foreach ($badges as $id_badge) {
                if ($id_badge <= 0) {
                    continue;
                }

                $insert[] = [
                    'id_productbadges' => $id_badge,
                    'id_product' => (int)$id_product,
                ];
            }

            if (!empty($insert)) {
                Db::getInstance()->insert('productbadges_product', $insert);
            }
        }
    }

    public static function getBadgesByProduct($id_product, $id_lang, $limit = 3)
    {
        $sql = new DbQuery();
        $sql->select('pb.*, pbl.text');
        $sql->from('productbadges', 'pb');
        $sql->leftJoin('productbadges_lang', 'pbl', 'pb.id_productbadges = pbl.id_productbadges AND pbl.id_lang = ' . (int)$id_lang);
        $sql->innerJoin('productbadges_product', 'pbp', 'pb.id_productbadges = pbp.id_productbadges');
        $sql->where('pbp.id_product = ' . (int)$id_product);
        $sql->where('pb.active = 1');
        $sql->limit((int)$limit);
        
        return Db::getInstance()->executeS($sql);
    }
}
