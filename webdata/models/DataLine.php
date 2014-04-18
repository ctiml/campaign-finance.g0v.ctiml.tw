<?php

class DataLine extends Pix_Table
{
    public function init()
    {
        $this->_name = 'data_line';
        $this->_primary = array('id');

        // 序號
        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        // 集合 ID
        $this->_columns['set_id'] = array('type' => 'int');
        // 資料內容
        $this->_columns['data'] = array('type' => 'json');

        $this->addIndex('setid_id', array('set_id', 'id'), 'unique');
    }
}
