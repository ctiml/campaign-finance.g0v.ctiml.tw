<?php

class CellUnclearHistory extends Pix_Table
{
    public function init()
    {
        $this->_name = 'cell_unclear_history';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['page'] = array('type' => 'int');
        $this->_columns['x'] = array('type' => 'int');
        $this->_columns['y'] = array('type' => 'int');
        $this->_columns['client_ip'] = array('type' => 'string');
        $this->_columns['created'] = array('type' => 'int');
        $this->_columns['user_id'] = array('type' => 'int', 'default' => 0);
    }
}
