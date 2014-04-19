<?php

class PagePromotion extends Pix_Table
{
    public function init()
    {
        $this->_name = 'page_promotion';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['page'] = array('type' => 'int');
    }
}
