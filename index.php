<?php

include(__DIR__ . '/webdata/init.inc.php');

if (!getenv('SESSION_SECRET')) {
    die("need SESSION_SECRET");
}

Pix_Session::setAdapter('cookie', array('secret' => getenv('SESSION_SECRET'), 'cookie_domain' => ''));
Pix_Controller::addCommonHelpers();
Pix_Controller::dispatch(__DIR__ . '/webdata/');
