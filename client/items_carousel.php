<?php

namespace modules\carousels\client;

class items_carousel extends carousel {

    protected $cache = true;
    protected $cache_per_page = true;

    protected $css = [
        '/css/carousel.css',
        '/css/clients_carousel.css'
    ];

    public static $_name = '*Items carousel*';
}