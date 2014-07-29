<?php

class ApiKey extends Pix_Table
{
    public function init()
    {
        $this->_name = 'api_key';
        $this->_primary = 'key';

        $this->_columns['key'] = array('type' => 'varchar', 'size' => 32);
        $this->_columns['name'] = array('type' => 'string');
    }

    public static function exists($key)
    {
        return ApiKey::find(strval($key)) != NULL;
    }
}
