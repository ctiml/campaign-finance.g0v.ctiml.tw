<?php

class ApiController extends Pix_Controller
{
    public function fillcellAction()
    {
        if (!$this->checkAuthorized($_POST['sToken'], $_POST['apikey'])) {
            return $this->noview();
        }

        list(, /*api*/, /*fillcell*/, $page, $x, $y) = explode('/', $this->getURI());
        $page = intval($page);
        $x = intval($x);
        $y = intval($y);
        $ans = $_POST['ans'];

        $values = array('page' => $page, 'x' => $x, 'y' => $y);
        $cell = Cell::search($values)->first();

        if ($user_id = Pix_Session::get('user_id')) {
            $user_score = UserScore::search(array('id' => $user_id))->first();
        }
        CellHistory::insert(array_merge($values, array(
            'ans' => $ans,
            'client_ip' => $_SERVER["REMOTE_ADDR"],
            'user_id' => ($user_id) ? $user_id : 0,
            'created' => time()
        )));
        $count = intval($cell->count) + 1;
        try {
            Cell::insert(array_merge($values, array(
                'ans' => $ans,
                'count' => $count)));
        } catch (Pix_Table_DuplicateException $e) {
            $cell = Cell::search($values)->first();
            $cell->update(array('ans' => $ans, 'count' => $count));
        }
        if ($user_score) {
            $user_score->update(array('ans_count' => intval($user_score->ans_count) + 1));
        }
        return $this->noview();
    }

    public function reportunclearAction()
    {
        if (!$this->checkAuthorized($_POST['sToken'], $_POST['apikey'])) {
            return $this->noview();
        }

        list(, /*api*/, /*reportunclear*/, $page, $x, $y) = explode('/', $this->getURI());
        $page = intval($page);
        $x = intval($x);
        $y = intval($y);

        $user_id = Pix_Session::get('user_id');

        CellUnclearHistory::insert(array(
            'page' => $page,
            'x' => $x,
            'y' => $y,
            'client_ip' => $_SERVER["REMOTE_ADDR"],
            'user_id' => ($user_id) ? $user_id : 0,
            'created' => time()
        ));
        return $this->noview();
    }

    protected function checkAuthorized($token, $apikey = NULL)
    {
        if ($apikey != NULL && ApiKey::exists($apikey)) {
            return true;
        }
        if ($token && $token == Pix_Session::get('sToken')) {
            return true;
        }
        return false;
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
            // 對 client_ip 加密
            $history = array();
            foreach(array_values(CellHistory::search($values)->order('created DESC')->toArray()) as $ch) {
                $ch['encrypted_client_ip'] = crc32($ch['client_ip'] . strval(getenv(IP_CLOAK_SECRET)));
                $ch['row'] = $ch['x'];
                $ch['col'] = $ch['y'];
                unset($ch['client_ip']);
                unset($ch['user_id']);
                unset($ch['x']);
                unset($ch['y']);
                $history[] = $ch;
            }
            $ans = ($cell->count > 0 && $cell->ans == null) ? "" : $cell->ans;
            return $this->jsonp(array(
                'error' => false,
                'value' => $ans,
                'history' => $history
            ), $_GET['callback']);
        }
    }

    public function getcellsAction()
    {
        list(, /*api*/, /*getcells*/, $page) = explode('/', $this->getURI());

        if ($page == null) {
            return $this->jsonp(array('error' => 'true', 'message' => 'page not found'), $_GET['callback']);
        }

        $cells = Cell::search(array('page' => $page))->order('page, x, y ASC');
        $json = array();
        foreach ($cells as $cell) {
            array_push($json, array(
                'page' => $cell->page,
                'row' => $cell->x,
                'col' => $cell->y,
                'ans' => $cell->ans
            ));
        }
        return $this->jsonp($json, $_GET['callback']);
    }

    public function getcellcountAction()
    {
        $count = KeyValue::get('cache_count');
        return $this->jsonp(array(
            'count' => $count,
            'todo' => KeyValue::get('cache_count_todo'),
        ), $_GET['callback']);
    }

    protected function getrandomsAction()
    {
        $cells = array_values(Cell::search(1)->order('count ASC')->limit(100)->toArray());
        shuffle($cells);
        return $this->json(array_map(function($r){
            $r['img_url'] = "http://" . strval(getenv('CAMPAIGN_FINANCE_RONNY')) . "/api/getcellimage/{$r['page']}/{$r['x']}/{$r['y']}.png";
            if ($r['count'] > 0 && $r['ans'] == null) {
                $r['ans'] = "";
            }
            return $r;
        }, array_slice($cells, 0, 10)));
    }

    protected function getrandom()
    {
        $page = rand(1, PageInfo::search(1)->max('id')->id);
        // 五成的機率優先推 PagePromotion 的 Table
        $promotions = array();
        if (rand(1, 100) > 50) {
            $promotions = array_values(PagePromotion::search(1)->toArray());
            if (count($promotions) > 0) {
                $index = rand(0, count($promotions) - 1);
                $page = $promotions[$index]['id'];
            }
        }
        $page_info = PageInfo::find($page);

        $input_y = array(2, 3, 4, 5, 6, 7, 9);

        // 八成的機率隨機抓填入次數最小的
        if (!$promotions and rand(1, 100) < 80) {
        } else {
            $x = rand(2, $page_info->row_count);
            $y = $input_y[rand(0, count($input_y) - 1)];
        }

        $ans = null;

        $cell = Cell::search(array('page' => $page, 'x' => $x, 'y' => $y))->first();
        if ($cell != NULL) {
            if (rand(1, 100) < 80) {
                $cells = Cell::search(array('page'=>$page, 'y' => 9))->toArray(array('x', 'y'));
                $used_cells = array();
                foreach ($cells as $cell_array) {
                    $used_cells[intval($cell_array['x']) . '-' . intval($cell_array['y'])] = true;
                }
                foreach (range(2, $page_info->row_count) as $x) {
                    foreach (array(9) as $y) {
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

        return array($page, $x, $y, $ans, $cell->count);
    }

    public function getdonepagesAction()
    {
        return $this->jsonp(array_values(PageDone::search(1)->order('id asc')->toArray()), $_GET['callback']);
    }

    public function getrandomAction()
    {
        list($page, $x, $y, $ans, $count) = $this->getrandom();

        $api_url = "http://" . strval(getenv(CAMPAIGN_FINANCE_RONNY)) . "/api/getcellimage";
        $img_url = $api_url . "/" . $page . "/" . $x . "/" . $y . ".png";

        return $this->json(array(
            'img_url' => $img_url,
            'page' => $page,
            'x' => $x,
            'y' => $y,
            'ans' => $ans,
            'count' => $count,
        ));
    }
}
