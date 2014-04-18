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
            return $this->json(array('error' => true, 'message' => 'not found'));
        } else {
            return $this->json(array('error' => false, 'value' => $cell->ans));
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
        return $this->json($json);
    }
}
