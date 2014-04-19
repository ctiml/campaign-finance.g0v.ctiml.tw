<?php

class CellController extends Pix_Controller
{
    public function indexAction()
    {
        $page = rand(1, 2500);
        $x = rand(2, 21);
        $y = rand(1, 9);

        $cell = Cell::search(array('page' => $page, 'x' => $x, 'y' => $y))->first();
        if ($cell != NULL) {
            $this->view->cell = $cell;
        }

        $api_url = "http://" . strval(getenv(CAMPAIGN_FINANCE_RONNY)) . "/api/getcellimage";
        $this->view->cellimg = $api_url . "/" . $page . "/" . $x . "/" . $y . ".png";
        $this->view->page = $page;
        $this->view->x = $x;
        $this->view->y = $y;
    }

    public function fillAction()
    {
        $this->view->cellimg = "http://" . strval(getenv(CAMPAIGN_FINANCE_RONNY)) . "/api/getcellimage/775/2/1.png";
    }
}
