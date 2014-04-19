<?php

class PageDone extends Pix_Table
{
    public function init()
    {
        $this->_name = 'page_done';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['done_at'] = array('type' => 'int');
    }
}
