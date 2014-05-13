<?php

class UserScore extends Pix_Table
{
    public function init()
    {
        $this->_name = 'user_score';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int');
        $this->_columns['ans_count'] = array('type' => 'int');
    }
}
