<?php

namespace modules\carousels\models;

use m\config;
use m\model;
use m\registry;
use modules\files\models\files;

class carousel extends model
{
    public $_table = 'carousel';
    protected $_sort = ['page' => 'ASC', 'sequence' => 'ASC'];

    protected $fields = [
        'id' => 'int',
        'module' => 'varchar',
        'site' => 'int',
        'page' => 'int',
        'language' => 'int',
        'title' => 'varchar',
        'text' => 'text',
        'link' => 'varchar',
        'image_path' => 'varchar',
        'sequence' => 'int',
    ];

    public function get_file()
    {
        if (empty($this->id))
            return false;

        return files::call_static()->s(['*'],['related_model' => 'carousel', 'related_id' => $this->id])->obj();
    }

    public function _before_save() {

        if (!empty($this->image) && is_file(config::get('root_path') . $this->image)) {

            if (!empty($this->id)) {
                $file = files::call_static()->s([], ['related_model' => 'carousel', 'related_id' => $this->id])->obj();
            }

            if (empty($file) || empty($file->id)) {

                $file = new files;
            }

            $file->save([
                'site' => registry::get('site')->id,
                'file' => $this->image,
                'name' => pathinfo($this->image, PATHINFO_BASENAME),
                'related_model' => 'carousel',
                'related_id' => empty($this->id) ? intval(carousel::call_static()->s(['id'], [], [1], ['id DESC'])->one()) + 1 : $this->id,
            ]);

            unset($this->image);
        }

        return true;
    }

    public function _before_destroy() {
        $file = files::call_static()->s([], ['related_model' => 'carousel', 'related_id' => $this->id])->obj();

        if (!empty($file->id)) {
            $file->destroy();
        }
    }

    public function get_type_name()
    {
        $_n = 'modules\\carousels\\client\\' . $this->module;

        if (class_exists($_n)) {
            $vars = get_class_vars($_n);
            return empty($vars['_name']) ? $this->module : $vars['_name'];
        }

        return '*' . $this->module . '*';
    }
}