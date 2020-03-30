<?php

namespace modules\carousels\client;

class clients_carousel extends carousel {

    protected $cache = false;
    protected $cache_per_page = false;

    public static $_name = '*Clients carousel*';

    protected $css = [
        '/css/clients_carousel.css'
    ];
}