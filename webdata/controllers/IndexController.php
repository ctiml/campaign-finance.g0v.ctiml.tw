<?php

class IndexController extends Pix_Controller
{
    public function init()
    {
        if (!$sToken = Pix_Session::get('sToken')) {
            $sToken = crc32(uniqid());
            Pix_Session::set('sToken', $sToken);
        }
        $this->view->sToken = $sToken;
    }

    public function indexAction()
    {
        if ($user_id = Pix_Session::get('user_id')) {
            $this->view->user = User::search(array('id' => $user_id))->first();
        }
        $this->view->count = KeyValue::get('cache_count');
    }
}
