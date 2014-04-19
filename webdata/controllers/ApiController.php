<?php

class ApiController extends Pix_Controller
{
    public function fillcellAction()
    {
        list(, /*api*/, /*fillcell*/, $page, $x, $y) = explode('/', $this->getURI());
        $page = intval($page);
        $x = intval($x);
        $y = intval($y);
        $ans = $_POST['ans'];
        
        $values = array('page' => $page, 'x' => $x, 'y' => $y);
        $cell = Cell::search($values)->first();
        CellHistory::insert(array_merge($values, array(
            'ans' => $ans,
            'client_ip' => $_SERVER["REMOTE_ADDR"],
            'created' => time()
        )));
        if ($cell == NULL) {
            Cell::insert(array_merge($values, array('ans' => $ans)));
        } else {
            $cell->ans = $ans;
            $cell->save();
            echo $cell->page . "/" . $cell->x . "/" . $cell->y . " => " . $cell->ans;
        }
        return $this->noview();
    }

    public function getcellvalueAction()
    {
        list(, /*api*/, /*getcellvalue*/, $page, $x, $y) = explode('/', $this->getURI());
        $page = intval($page);
        $x = intval($x);
        $y = intval($y);

        $values = array('page' => $page, 'x' => $x, 'y' => $y);
        $cell = Cell::search($values)->first();
        if ($cell == NULL) {
            return $this->jsonp(array('error' => true, 'message' => 'not found'), $_GET['callback']);
        } else {
            return $this->jsonp(array('error' => false, 'value' => $cell->ans), $_GET['callback']);
        }
    }

    public function getcellsAction()
    {
        list(, /*api*/, /*getcells*/, $page) = explode('/', $this->getURI());

        $values = array();
        if ($page != null) {
            $values = array('page' => intval($page));
        }

        $cells = Cell::search($values)->order('page, x, y ASC');
        $json = array();
        foreach ($cells as $cell) {
            array_push($json, array(
                'page' => $cell->page,
                'x' => $cell->x,
                'y' => $cell->y,
                'ans' => $cell->ans
            ));
        }
        return $this->jsonp($json, $_GET['callback']);
    }

    public function getrandomAction()
    {
        $page = rand(1, 2500);
        $x = rand(2, 21);
        $y = rand(2, 7);
        $ans = "";

        $cell = Cell::search(array('page' => $page, 'x' => $x, 'y' => $y))->first();
        if ($cell != NULL) {
            $ans = $cell->ans;
        }

        $api_url = "http://" . strval(getenv(CAMPAIGN_FINANCE_RONNY)) . "/api/getcellimage";
        $img_url = $api_url . "/" . $page . "/" . $x . "/" . $y . ".png";

        return $this->json(array(
            'img_url' => $img_url,
            'page' => $page,
            'x' => $x,
            'y' => $y,
            'ans' => $ans
        ));
    }
}
