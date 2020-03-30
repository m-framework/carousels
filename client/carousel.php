<?php

namespace modules\carousels\client;

use m\config;
use m\module;
use m\registry;
use m\view;
use modules\carousels\models;

class carousel extends module {

    public function _init()
    {
        $slides = models\carousel::call_static()
            ->s(
                [],
                [
                    'site' => registry::get('site')->id,
                    'module' => $this->module_name,
                    [['language' => $this->language_id], ['language' => null]]
                ],
                [1000],
                ($this->module_name == 'clients_carousel' ? ['RAND()'] : ['page' => 'ASC', 'sequence' => 'ASC'])
            )
            ->all('object');

        $carousel_slides = [];

        if (!isset($this->view->{$this->module_name . '_item'})) {
            return false;
        }

        if (!empty($slides)) {
            foreach ($slides as $slide) {

                if ($this->user->has_permission($this->name, $this->page->id) && isset($this->view->edit_bar)) {
                    $slide->edit_bar = $this->view->edit_bar->prepare([
                        'model' => 'carousel',
                        'module' => $this->module_name,
                        'id' => $slide->id,
                        'edit_link' => '/' . config::get('admin_panel_alias') . '/carousels/edit/' . $slide->id,
                    ]);
                }

                $carousel_slides[] = $this->view->{$this->module_name . '_item'}->prepare([
                    'id' => $slide->id,
                    'module' => $this->module_name,
                    'title' => !empty($slide->title) ?  htmlspecialchars_decode($slide->title) : '',
                    'image' => empty($slide->image_path) ? '' : $slide->image_path,
                    'text' => !empty($slide->text) ? htmlspecialchars_decode($slide->text) : '',
                    'link' => !empty($slide->link) ? $slide->link : '#',
                    'edit_bar' => empty($slide->edit_bar) ? null : $slide->edit_bar,
                ]);
            }
        }

        if (empty($carousel_slides)) {
            return false;
        }

        view::set($this->module_name, $this->view->{$this->module_name}->prepare([
            'slides' => implode('', $carousel_slides)
            ]));

        return true;
    }

    public function _ajax_update_carousel()
    {
        if (!empty($this->post->module) && $this->post->module !== $this->module_name) {
            return false;
        }

        if (!$this->user->has_permission($this->module_name, $this->page->id) || empty($this->post->id)
            || empty($this->post->model) || $this->post->model !== 'carousel') {
            return $this->ajax_arr = ['error' => 'Not fully data'];
        }

        $item = new models\carousel($this->post->id);
        $item->import($this->post);

        if ($item->save()) {
            $this->ajax_arr = ['result' => 'success'];
        }
        else {
            $this->ajax_arr = ['error' => 'Can\'t update this carousel slide'];
        }

        return true;
    }
}