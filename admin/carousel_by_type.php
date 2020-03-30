<?php

namespace modules\carousels\admin;

use m\module;
use m\view;
use m\config;
use modules\pages\models\pages;
use modules\carousels\models\carousel;

class carousel_by_type extends module {

    protected static $module = 'carousel';

    public function _init()
    {
        config::set('per_page', 1000);

        $items = carousel::call_static()->s([], [
            'site' => $this->site->id,
            'module' => static::$module,
            [['language' => $this->language_id], ['language' => null]]
        ], [1000])->all('object');

        $arr = [];

        if (!empty($items)) {
            foreach ($items as $item) {

                $page = empty($item->page) ? '' : new pages($item->page);

                $arr[] = $this->view->{'overview_carousel_by_type_item'}->prepare([
                    'id' => $item->id,
                    'title' => $item->title,
                    'link' => $item->link,
                    'page_name' => !empty($page->name) ? $page->name : '',
                    'page_path' => !empty($page->address) ? $page->get_path() : '',
                    'module' => static::$module,
                    'image' => $item->image_path,
                ]);
            }
        }

        view::set('content', $this->view->overview_carousel_by_type->prepare([
            'items' => implode("\n", $arr),
            'module_name' => static::$module,
        ]));
    }
}