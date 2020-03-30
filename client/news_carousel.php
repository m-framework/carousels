<?php

namespace modules\carousels\client;

use m\config;
use m\core;
use m\i18n;
use m\module;
use m\registry;
use m\view;
use modules\carousels\models\carousels_products;
use modules\carousels\models\carousels_products_items;
use modules\pages\models\pages;

class news_carousel extends module {

    protected $cache = false;
    protected $cache_per_page = true;

    protected $css = [
        '/css/news-carousel.css',
    ];

    public static $_name = '*News carousel*';

    public function _init()
    {
        if (!isset($this->view->{$this->module_name . '_item'}) || !isset($this->view->{$this->module_name})) {
            return false;
        }

        $news_alias = config::get('news_alias') ? config::get('news_alias') : 'news';

        $news_page = pages::call_static()->s([], ['address' => '/' . $news_alias])->obj();

        if (empty($news_page->id) && !empty($this->site->news_page)) {
            $news_page = new pages($this->site->news_page);
        }

        $pages_ids = [];

        if (!empty($news_page->id)) {
            $child_pages = pages::call_static()->s([], ['parent' => $news_page->id])->all('object');

            if (!empty($child_pages)) {
                $pages_ids = array_keys($child_pages);
            }
        }

        $pages_ids[] = $news_page->id;

        $cond = [
            'site' => $this->site->id,
            'page' => $pages_ids,
            'alias' => ['not' => '/' . $news_alias],
            'published' => 1,
            'language' => $this->language_id,
        ];

        $news = \modules\articles\models\news::call_static()->s([], $cond, [10])->all('object');

        if (empty($news)) {
            return false;
        }

        $carousel_slides = [];
        foreach ($news as $news_item) {
            $carousel_slides[] = $this->view->{$this->module_name . '_item'}->prepare($news_item);
        }

        if (empty($carousel_slides)) {
            return false;
        }

        return view::set($this->module_name, $this->view->{$this->module_name}->prepare([
            'slides' => implode('', $carousel_slides)
        ]));
    }
}