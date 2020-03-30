<?php

namespace modules\carousels\models;

use m\model;
use m\registry;
use modules\shop\models\shop_products;

class carousels_products_items extends model
{
    protected $_sort = ['sequence' => 'ASC'];

    protected $fields = [
        'id' => 'int',
        'site' => 'int',
        'carousel' => 'int',
        'product' => 'int',
        'active' => 'tinyint',
        'sequence' => 'int',
    ];

    public static function get_items($carousel_id)
    {
        $products_ids = [];

        $products = carousels_products_items::call_static()
            ->s(['id'], ['site' => registry::get('site')->id, 'carousel' => $carousel_id], [1000])
            ->all();

        foreach ($products as $product) {
            $products_ids[] = $product['id'];
        }

        return shop_products::call_static()
            ->s([], ['site' => registry::get('site')->id, 'id' => $products_ids], [1000])
            ->all('object');
    }

    public function _autoload_product_obj()
    {
        $this->product_obj = new shop_products($this->product);
    }

    public function _autoload_product_name()
    {
        $this->product_name = $this->product_obj->name;
    }

    public function _autoload_product_image()
    {
        $this->product_image = $this->product_obj->image;
    }

    public function _autoload_product_price()
    {
        $this->product_price = $this->product_obj->beautiful_price;
    }

    public function _autoload_product_path()
    {
        $this->product_path = $this->product_obj->path;
    }

    public function _autoload_active_checked()
    {
        $this->active_checked = empty($this->active) ? '' : 'checked';
    }

    public function _autoload_currency_name()
    {
        $this->currency_name = $this->product_obj->currency_name;
    }
}
