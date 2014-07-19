<?php

class UserController extends Pix_Controller
{
    public function init()
    {
        $this->openid = new LightOpenID($_SERVER['HTTP_HOST']);
    }

    public function googleAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->openid->identity = 'https://www.google.com/accounts/o8/id';
            $this->openid->required = array('contact/email');
            header('Location: ' . $this->openid->authUrl());
        } else {
            if ($this->openid->mode && $this->openid->mode != 'cancel' && $this->openid->validate()) {
                $attrs = $this->openid->getAttributes();
                if (isset($attrs['contact/email'])) {
                    $email = $attrs['contact/email'];
                    $user = User::search(array('email' => $email))->first();
                    if (!$user) {
                        $user = User::insert(array(
                            'name' => '',
                            'email' => $email,
                            'created' => time()
                        ));
                        UserScore::insert(array(
                            'id' => $user->id,
                            'ans_count' => 0
                        ));
                    }
                    Pix_Session::set('user_id', $user->id);
                }
            }
            return $this->redirect('/cell');
        }
        $this->noview();
    }

    public function indexAction()
    {
        $user_id = Pix_Session::get('user_id');
        $user = User::search(array('id' => $user_id));
        
        $this->noview();
    }

    public function logoutAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            Pix_Session::delete('user_id');
        }
        return $this->redirect('/cell');
    }
}
