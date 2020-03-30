<?php

namespace modules\carousels\admin;

use m\config;
use m\module;
use m\i18n;
use m\registry;
use m\view;
use m\form;
use modules\carousels\models\carousel;
use modules\pages\models\pages;
use modules\pages\models\pages_types;
use modules\pages\models\pages_types_modules;
use modules\sites\models\sites;
use modules\files\models\files;

class edit extends module {

    public function _init()
    {

        /**
         *
         */

        if (!isset($this->view->{'carousel_' . $this->name . '_form'})) {
            return false;
        }

        $item = new carousel(!empty($this->get->edit) ? $this->get->edit : null);

        if (!empty($item->id)) {
            view::set('page_title', '<h1><i class="fa fa-chain"></i> *Edit a carousel slide* ' . (empty($item->title) ? '' : '`' . $item->title . '`') . '</h1>');
            registry::set('title', i18n::get('Edit a carousel slide'));

            registry::set('breadcrumbs', [
                '/' . config::get('admin_panel_alias') . '/carousels' => '*Carousels*',
                '/' . config::get('admin_panel_alias') . '/carousels/' . str_replace('_carousel', '', $item->module) => $item->get_type_name(),
                '/' . config::get('admin_panel_alias') . '/carousels/edit/' . $item->id => '*Edit a carousel slide*',
            ]);
        }
        else {
            view::set('page_title', '<h1><i class="fa fa-chain"></i> *Add new carousel slide*</h1>');
            registry::set('title', i18n::get('Add new carousel slide'));
        }

        if (empty($item->site)) {
            $item->site = $this->site->id;
        }
        if (empty($item->language)) {
            $item->language = (string)$this->language_id;
        }
        if (empty($item->module) && !empty($this->get->add)) {
            $item->module = $this->get->add;
        }

        $pages_tree = $this->page->get_pages_tree();

        if (empty($pages_tree)) {
            $this->page->prepare_page([]);
            $pages_tree = $this->page->get_pages_tree();
        }

        $pages_arr = empty($pages_tree) ? [] : pages::options_arr_recursively($pages_tree, '');


        new form(
            $item,
            [
                'module' => [
                    'field_name' => i18n::get('Menu type'),
                    'related' => [
                        ['value' => 'header_carousel', 'name' => '*Header carousel*'],
                        ['value' => 'clients_carousel', 'name' => '*Clients carousel*'],
                        ['value' => 'items_carousel', 'name' => '*Items carousel*'],
                    ],
                    'required' => true,
                ],
                'page' => [
                    'field_name' => i18n::get('Page'),
                    'related' => $pages_arr,
                ],
                'title' => [
                    'type' => 'varchar',
                    'field_name' => i18n::get('Title'),
                ],
                'text' => [
                    'type' => 'text',
                    'field_name' => i18n::get('Text'),
                ],
                'link' => [
                    'type' => 'varchar',
                    'field_name' => i18n::get('Link'),
                ],
                'image_path' => [
                    'type' => 'file_path',
                    'field_name' => i18n::get('Slide image'),
                ],
                'site' => [
                    'type' => 'hidden',
                    'field_name' => '',
                ],
                'language' => [
                    'type' => 'hidden',
                    'field_name' => '',
                ],
            ],
            [
                'form' => $this->view->{'carousel_' . $this->name . '_form'},
                'varchar' => $this->view->edit_row_varchar,
                'text' => $this->view->edit_row_text,
                'related' => $this->view->edit_row_related,
                'hidden' => $this->view->edit_row_hidden,
                'file_path' => $this->view->edit_row_file_path,
                'saved' => $this->view->edit_row_saved,
                'error' => $this->view->edit_row_error,
            ]
        );
    }

}