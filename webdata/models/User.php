<?php

class User extends Pix_Table
{
    public function init()
    {
        $this->_name = 'user';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['email'] = array('type' => 'string');
        $this->_columns['name'] = array('type' => 'string');
        $this->_columns['created'] = array('type' => 'int');
    }
}
