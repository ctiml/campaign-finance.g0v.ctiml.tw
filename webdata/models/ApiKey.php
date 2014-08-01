<?php

class ApiKey extends Pix_Table
{
    public function init()
    {
        $this->_name = 'api_key';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['key'] = array('type' => 'varchar', 'size' => 32);
        $this->_columns['name'] = array('type' => 'string');

        $this->addIndex('key_id', array('key', 'id'), 'unique');
    }
}
