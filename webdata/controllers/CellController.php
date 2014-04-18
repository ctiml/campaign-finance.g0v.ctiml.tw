<?php

class CellController extends Pix_Controller
{
    public function indexAction()
    {
        $page = rand(1, 2000);
        $x = rand(2, 21);
        $y = rand(1, 9);
        $this->view->cellimg = "http://campaign-finance.g0v.ronny.tw/api/getcellimage/" . $page . "/" . $x . "/" . $y . ".png";
    }

    public function fillAction()
    {
        $this->view->cellimg = "http://campaign-finance.g0v.ronny.tw/api/getcellimage/775/2/1.png";
    }
}
