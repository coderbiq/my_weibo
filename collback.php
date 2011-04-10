<?php

require_once 'myweibo.php';

$myweibo = new MyWeibo();

try{
    $myweibo->setLastKey($_REQUEST['oauth_verifier']);
    header(sprintf('Location:%s' , 'http://127.0.0.1/myweibo/index.php'));
} catch( Exception $e ) {
    print_r($e);
}
