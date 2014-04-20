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
        $count = CellHistory::search(array('page' => $page, 'x' => $x, 'y' => $y))->count();
        if ($cell == NULL) {
            try {
                Cell::insert(array_merge($values, array(
                    'ans' => $ans,
                    'count' => $count)));
            } catch (Pix_Table_DuplicateException $e) {
                $cell->update(array('ans' => $ans, 'count' => $count));
            }
        } else {
            $cell->ans = $ans;
            $cell->count = $count;
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
            return $this->jsonp(array(
                'error' => false,
                'value' => $cell->ans,
                'history' => array_values(CellHistory::search($values)->order('created DESC')->toArray())
            ), $_GET['callback']);
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

    public function getcellcountAction()
    {
        $count = KeyValue::get('cache_count');
        return $this->jsonp(array('count' => $count), $_GET['callback']);
    }

    protected function getrandom()
    {
        $page = rand(1, 2637);
        // 五成的機率優先推 PagePromotion 的 Table
        if (rand(1, 100) > 50) {
            $promotions = array_values(PagePromotion::search(1)->toArray());
            if (count($promotions) > 0) {
                $index = rand(0, count($promotions) - 1);
                $page = $promotions[$index]['page'];
            }
        }
        $page_info = PageInfo::find($page);

        // 八成的機率隨機抓填入次數最小的
        if (rand(1, 100) < 80) {
            $cells = array_values(Cell::search(1)->order('count ASC')->limit(100)->toArray());
            shuffle($cells);
            $cell = $cells[0];
            return array($cell['page'], $cell['x'], $cell['y'], $cell['ans'], $cell['count']);
        } else {
            $x = rand(2, $page_info->row_count);
            $y = rand(2, 7);
        }

        $ans = null;

        $cell = Cell::search(array('page' => $page, 'x' => $x, 'y' => $y))->first();
        if ($cell != NULL) {
            if (rand(1, 100) < 80) {
                $cells = Cell::search(array('page'=>$page))->toArray();
                $used_cells = array();
                foreach ($cells as $cell_array) {
                    $used_cells[intval($cell_array['x']) . '-' . intval($cell_array['y'])] = true;
                }
                foreach (range(2, $page_info->row_count) as $x) {
                    foreach (range(2, 7) as $y) {
                        if ($used_cells[$x . '-' . $y]) {
                            continue;
                        }
                        return array($page, $x, $y, $ans);
                    }
                }
                // page 滿了
                if ($pp = PagePromotion::find($page)) {
                    // 把他從 promotion 移除
                    $pp->delete();

                    // 要找一個不在 Promtion 以及 Done 的出來推一下
                    $ids = array_merge( array_values(PagePromotion::search(1)->toArray('page')), array_values(PageDone::search(1)->toArray('id')));
                    $ids = array_unique($ids);
                    sort($ids);

                    // 從小找到大找到最小的還沒做的來 promote
                    foreach ($ids as $a => $b) {
                        if ($b != $a + 1) {
                            try {
                                PagePromotion::insert(array(
                                    'id' => $a + 1,
                                    'page' => $a + 1,
                                ));
                            } catch (Pix_Table_DuplicateException $e) {
                            }
                            break;
                        }
                    }
                }
                try {
                    PageDone::insert(array('id' => $page, 'done_at' => time()));
                } catch (Pix_Table_DuplicateException $e) {
                }

                return $this->getrandom();
            }
            $ans = $cell->ans;
        }

        return array($page, $x, $y, $ans);
    }

    public function getdonepagesAction()
    {
        return $this->jsonp(array_values(PageDone::search(1)->order('id asc')->toArray()), $_GET['callback']);
    }

    public function getrandomAction()
    {
        list($page, $x, $y, $ans) = $this->getrandom();

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
