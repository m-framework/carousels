<?php

namespace modules\carousels\client;

class header_carousel extends carousel {

    protected $cache = 0;

    protected $css = [
        '/css/carousel.css',
        '/css/header_carousel.css'
    ];

    public static $_name = '*Header carousel*';
}