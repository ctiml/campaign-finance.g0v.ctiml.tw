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
        default:
            return trim($data);

        }
    }
}
