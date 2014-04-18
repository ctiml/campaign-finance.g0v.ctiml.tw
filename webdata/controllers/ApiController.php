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
}
