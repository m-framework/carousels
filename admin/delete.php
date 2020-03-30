<?php

namespace modules\carousels\admin;

use m\module;
use m\core;
use modules\carousels\models\carousel;

class delete extends module {

    public function _init()
    {
        $item = new carousel(!empty($this->get->delete) ? $this->get->delete : null);

        if (!empty($item->id) && !empty($this->user->profile) && $this->user->is_admin() && $item->destroy()) {
            core::redirect('/' . $this->conf->admin_panel_alias . '/carousels');
        }
    }
}
