<?php

class IndexController extends Pix_Controller
{
    public function indexAction()
    {
        $this->view->count = KeyValue::get('cache_count');
    }
}
