<?php

namespace modules\carousels\admin;

use libraries\helper\html;
use m\core;
use m\functions;
use m\module;
use m\registry;
use m\view;
use m\config;
use modules\carousels\models\carousels_products;
use modules\carousels\models\carousels_products_items;
use modules\pages\models\pages;
use modules\carousels\models\carousel;
use modules\shop\models\shop_products;

class products extends module {

    protected $css = ['/css/overview_carousels_products.css'];
    protected $js = ['/js/onchange_update.js'];

    public function _init()
    {
        if (!empty($this->get->id)) {
            return $this->product_slider();
        }

        config::set('per_page', 1000);

        view::set('page_title', '<h1><i class="fa fa-chain"></i> *Products carousels*</h1>');
        registry::set('title', '*Products carousels*');

        registry::set('breadcrumbs', [
            '/' . config::get('admin_panel_alias') . '/carousels' => '*Carousels*',
            '/' . config::get('admin_panel_alias') . '/carousels/products' => '*Products carousels*',
        ]);

        $carousels = carousels_products::call_static()
            ->s([], ['site' => $this->site->id, [['language' => $this->language_id], ['language' => null]]], [1000])
            ->all('object');

        $items = '';

        if (!empty($carousels)) {
            foreach ($carousels as $carousel) {
                $items .= $this->view->overview_carousel_product->prepare($carousel);
            }
        }

        $pages_tree = $this->page->get_pages_tree();

        if (empty($pages_tree)) {
            $this->page->prepare_page([]);
            $pages_tree = $this->page->get_pages_tree();
        }

        $pages_arr = empty($pages_tree) ? [] : pages::options_arr_recursively($pages_tree, '');

        view::set('content', $this->view->overview_carousels_products->prepare([
            'items' => $items,
            'pages_options' => empty($pages_arr) ? '' : html::arr_to_options($pages_arr, null),
        ]));
    }

    private function product_slider()
    {
        config::set('per_page', 1000);

        $carousel = carousels_products::call_static()
            ->s([],
                [
                    'site' => $this->site->id,
                    [['language' => $this->language_id], ['language' => null]],
                    'id' => $this->get->id
                ])
            ->obj();

        view::set('page_title', '<h1><i class="fa fa-chain"></i> *Edit products of carousel* "'.$carousel->title.'"</h1>');
        registry::set('title', '*Edit products of carousel*');

        registry::set('breadcrumbs', [
            '/' . config::get('admin_panel_alias') . '/carousels' => '*Carousels*',
            '/' . config::get('admin_panel_alias') . '/carousels/products' => '*Products carousels*',
            '/' . config::get('admin_panel_alias') . '/carousels/products/carousel/id/' . $this->get->id => '*Edit products of carousel*',
        ]);

        if (empty($carousel) || empty($carousel->id)) {
            core::redirect('/' . $this->conf->admin_panel_alias . '/carousels/products');
            return false;
        }

        $carousels_items = carousels_products_items::call_static()
            ->s([], [
                'site' => strval($this->site->id),
                'carousel' => strval($this->get->id),
            ], [1000])
            ->all('object');

        $items = '';

        if (!empty($carousels_items)) {
            foreach ($carousels_items as $carousels_item) {
                $items .= $this->view->carousel_product_item->prepare($carousels_item);
            }
        }

        view::set('content', $this->view->carousel_products->prepare([
            'items' => $items,
            'carousel_id' => $this->get->id,
        ]));
    }



    public function _ajax_suggestions()
    {
        if (empty($this->post->fields) || empty($this->post->fragment)) {
            core::out(['error' => 'empty important data']);
        }

        $fields_conditions = [];
        $fields = explode(',', $this->post->fields);

        foreach ($fields as $field)
        {
            if (!empty($field)) {

                if ($field == 'id' || $field == 'code') {
                    $fields_conditions[] = [$field => $this->post->fragment];
                }
                else {
                    // TODO: set  Mongo Regular
                    $fields_conditions[] = [$field . " LIKE '%" . $this->post->fragment . "%'"];
                }
            }
        }

        $cond = [$fields_conditions];

        if (!empty($this->post->additional_conditions)) {
            foreach ($this->post->additional_conditions as $add_k => $additional_condition) {
                $cond[$add_k] = $additional_condition;
            }
        }

        switch ($this->post->model) {
            case 'products':
                $model = shop_products::call_static();
                break;
        }

        if (empty($model)) {
            core::out(['error' => 'empty model']);
        }

        $suggestions = $districts = $regions = [];

        $items = $model->s([], $cond, [20])->all('object');

        foreach ($items as $item) {

            $suggestion = [];

            if ($this->post->model == 'products') {
                $suggestion['label'] = $item->name;
            }

            $suggestion['id'] = $item->id();

            $suggestions[] = $suggestion;
        }

        if (empty($suggestions)) {
            core::out(registry::get('db_logs'));
        }
        else {
            core::out($suggestions);
        }
    }
}