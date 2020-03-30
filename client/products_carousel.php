<?php

namespace modules\carousels\client;

use m\i18n;
use m\module;
use m\registry;
use m\view;
use modules\carousels\models\carousels_products;
use modules\carousels\models\carousels_products_items;
use modules\shop\models\shop_categories;
use modules\shop\models\shop_products;

class products_carousel extends module {

    protected $cache = false;
    protected $cache_per_page = true;

    protected $css = [
        '/../../shop/client/css/product.css',
        '/css/products-carousel.css',
        '/css/clients_carousel.css',
    ];

    public static $_name = '*Products carousel*';

    public function _init()
    {
        $products_carousels = carousels_products::call_static()
            ->s(
                [],
                [
                    'site' => registry::get('site')->id,
//                    'module' => $this->module_name,
                    [['language' => $this->language_id], ['language' => null]],
                    'active' => 1,
                ],
                [1000]
            )
            ->all('object');

        if (!empty($products_carousels)) {
            foreach ($products_carousels as $products_carousel) {

                $products_carousel_items = carousels_products_items::call_static()
                    ->s([], [
                        'site' => registry::get('site')->id,
                        'active' => 1,
                        'carousel' => $products_carousel->id,
                        ], [1000])
                    ->all('object');

                $items = '';

                $products_ids = [];
                $categories_ids = [];

                if (!empty($products_carousel_items)) {
                    foreach ($products_carousel_items as $products_carousel_item) {
                        $products_ids[] = $products_carousel_item->product;
                    }

                    $products = shop_products::call_static()
                        ->s([], ['id' => $products_ids], [100])
                        ->all('object');

                    if (!empty($products)) {

                        foreach ($products as $product) {
                            $categories_ids[$product->category] = $product->category;
                        }

                        $categories = shop_categories::call_static()
                            ->s([], ['id' => array_values($categories_ids)], [100])
                            ->all('object');

                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                $path = $category->path;
                            }
                        }
                    }

                    foreach ($products_carousel_items as $products_carousel_item) {
                        if (empty($products[$products_carousel_item->product])) {
                            continue;
                        }

                        $product = $products[$products_carousel_item->product];

                        $product->category_object = $categories[$product->category];

                        $items .= i18n::lang_replace($this->view->products_carousel_item->prepare($product));
                    }
                }

                $view_output_name = empty($products_carousel->view_code) ? 'products_carousel'
                    : $products_carousel->view_code;

                if (empty($items)) {
                    continue;
                }

                view::set($view_output_name, $this->view->products_carousel->prepare([
                    'slides' => $items,
                    'title' => $products_carousel->title,
                    'view_code' => $products_carousel->view_code,
                ]));
                //
            }
        }



        return true;
    }
}