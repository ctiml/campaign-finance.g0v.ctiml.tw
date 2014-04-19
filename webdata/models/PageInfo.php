<?php

class PageInfo extends Pix_Table
{
    public function init()
    {
        $this->_name = 'page_info';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['row_count'] = array('type' => 'int');
    }
}
