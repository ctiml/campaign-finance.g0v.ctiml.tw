<?php

class CellHistory extends Pix_Table
{
    public function init()
    {
        $this->_name = 'cell_history';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['page'] = array('type' => 'int');
        $this->_columns['x'] = array('type' => 'int');
        $this->_columns['y'] = array('type' => 'int');
        $this->_columns['ans'] = array('type' => 'string');
        $this->_columns['client_ip'] = array('type' => 'string');
        $this->_columns['created'] = array('type' => 'int');
        $this->_columns['user_id'] = array('type' => 'int', 'default' => 0);
        $this->_columns['apikey_id'] = array('type' => 'int', 'default' => 0);
    }
}
