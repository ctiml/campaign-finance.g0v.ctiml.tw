<?php

class CellRow extends Pix_Table_Row
{
    public function invalidCell()
    {
        $page = $this->page;
        // 將這一格設定為不正確..以方便讓使用者重新優先輸入
        $this->delete();

        // 如果這一頁已經完成了，就把他改成未完成，並且把他加入 PagePromotion
        if ($page_done = PageDone::find($page)) {
            $page_done->delete();

            if (count(PagePromotion::search(1)) < 200) {
                try {
                    PagePromotion::insert(array(
                        'page' => $page,
                        'id' => $page,
                    ));
                } catch (Pix_Table_DuplicateException $e) {
                }
            }
        }
    }
}

class Cell extends Pix_Table
{
    public function init()
    {
        $this->_name = 'cell';
        $this->_primary = array('id');
        $this->_rowClass = 'CellRow';

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['page'] = array('type' => 'int');
        $this->_columns['x'] = array('type' => 'int');
        $this->_columns['y'] = array('type' => 'int');
        $this->_columns['ans'] = array('type' => 'string');
        // 記錄這格被輸入過幾次
        $this->_columns['count'] = array('type' => 'int');

        $this->addIndex('page_x_y', array('page', 'x', 'y'), 'unique');
        $this->addIndex('count', array('count'));
        $this->addIndex('page_id', array('page', 'id'), 'unique');
    }

    public function checkData($y, $data) {
        switch ($y) {
        case 2: // 交易日期
            $data = trim($data);
            $data = str_replace('／', '/', $data);
            if (preg_match('#^[0-9]*$#', $data) and strlen($data) == 7) {
                $data = sprintf("%3d/%02d/%02d", intval($data) / 10000, intval($data) / 100 % 100, intval($data) %     100);
            }
            if (!preg_match('#^[0-9]*/[0-9]*/[0-9]*$#', $data)) {
                return false;
            }
            return $data;

        case 3: // 收支科目
            $data = trim($data);
            switch ($data) {
            case '':
                return false;
            case '人事支出費用':
            case '人士費用支出':
            case '人事費用支出':
                return '人事費用支出';
            case '匿名捐贈':
            case '匿名':
            case '匿名捐增':
            case '匿名損贈':
                return '匿名捐贈';
            case '交通旅費支出':
            case '交通旅遊支出':
            case '交通運旅支出':
            case '交通旅運支出':
                return '交通旅運支出';
            case '個人捐贈支出':
            case '各人捐贈收入':
            case '個人損贈收入':
            case '個人捐增收入':
            case '個人捐贈收入':
                return '個人捐贈收入';
            case '租用競選辦事處支出':
                return '租用競選辦事處支';
            case '雜支支出':
            case '集會支出':
            case '宣傳支出':
            case '營利事業捐贈收入':
            case '租用宣傳車輛支出':
            case '繳庫支出':
            case '返還支出':
            case '政黨捐贈收入':
            case '其他收入':
                return $data;
            }
            return false;

        default:
            return trim($data);
        }
    }
}
