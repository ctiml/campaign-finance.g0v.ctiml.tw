<?php

class CellController extends Pix_Controller
{
    public function indexAction()
    {
    }

    public function fillAction()
    {
        $this->view->cellimg = "http://" . strval(getenv(CAMPAIGN_FINANCE_RONNY)) . "/api/getcellimage/775/2/1.png";
    }
}
