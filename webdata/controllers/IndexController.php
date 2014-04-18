<?php

class IndexController extends Pix_Controller
{
    public function indexAction()
    {
        $this->view->count = count(Cell::search('id != 0'));
    }
}
