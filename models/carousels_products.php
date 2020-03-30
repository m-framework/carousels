<?php

namespace modules\carousels\models;

use m\model;
use m\registry;
use modules\pages\models\pages;

class carousels_products extends model
{
    protected $_sort = ['sequence' => 'ASC'];

    protected $fields = [
        'id' => 'int',
        'site' => 'int',
        'page' => 'int',
        'module' => 'varchar',
        'language' => 'int',
        'title' => 'varchar',
        'view_code' => 'varchar',
        'active' => 'tinyint',
        'sequence' => 'int',
    ];

    public function _before_destroy()
    {
        $carousels_products = carousels_products_items::call_static()
            ->s([], ['site' => registry::get('site')->id, 'carousel' => $this->id], [1000])
            ->all('object');

        if (!empty($carousels_products)) {
            foreach ($carousels_products as $carousels_product) {
                $carousels_product->destroy();
            }
        }

        return true;
    }

    public function _autoload_page_obj()
    {
        $this->page_obj = new pages($this->page);
    }

    public function _autoload_page_name()
    {
        $this->page_name = $this->page_obj->name;
    }

    public function _autoload_page_path()
    {
        $this->page_path = $this->page_obj->path;
    }

    public function get_products()
    {
        return empty($this->id) ? [] : carousels_products_items::get_items($this->id);
    }

    public function _autoload_active_checked()
    {
        $this->active_checked = empty($this->active) ? '' : 'checked';
    }
}
