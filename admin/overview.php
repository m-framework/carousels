<?php

namespace modules\carousels\admin;

use m\module;
use m\view;
use m\i18n;
use m\config;
use modules\admin\admin\overview_data;

class overview extends carousel_by_type {

    public function _init()
    {
        $arr = [];

        foreach (['header','clients','items','products'] as $type) {

            $arr[] = $this->view->overview_item->prepare([
                'name' => i18n::get(ucfirst($type) . ' carousel'),
                'link' => '~language_prefix~/' . config::get('admin_panel_alias') . '/carousels/' . $type,
            ]);
        }

        view::set('content', $this->view->overview->prepare([
            'items' => implode('', $arr)
        ]));
    }
}
