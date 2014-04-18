<?php

class Cell extends Pix_Table
{
    public function init()
    {
        $this->_name = 'cell';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['page'] = array('type' => 'int');
        $this->_columns['x'] = array('type' => 'int');
        $this->_columns['y'] = array('type' => 'int');
        $this->_columns['ans'] = array('type' => 'string');

        $this->addIndex('page_id', array('page', 'id'), 'unique');

    }
}
