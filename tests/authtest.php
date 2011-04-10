<?php
require_once 'myweibo.php';

class MyWeiboTest extends PHPUnit_Framework_TestCase
{
    protected $_my_weibo;

    public function setup()
    {
        $this->_my_weibo = new MyWeibo();
    }

    public function testValidateAuth()
    {
        try{
            $this->_my_weibo->validateAuth();

            $session = $this->_my_weibo->getSession();
            $this->assertType('array' , $session['last_key']);
            $this->assertTrue(isset($session['last_key']['oauth_token_secret']));
            $this->assertTrue(isset($session['last_key']['oauth_token']));
        } catch( Exception $e ) {
            $session = $this->_my_weibo->getSession();

            $this->assertFalse(isset($session['last_key']));
        }
    }

    public function testGetAuthorizeURL()
    {
        $url = $this->_my_weibo->getAuthorizeURL();

        $session = $this->_my_weibo->getSession();
        $this->assertTrue(isset($session['keys']));

        $url_2 = 'https://open.t.qq.com/cgi-bin/authorize?oauth_token=%s';
        $url_2 = sprintf($url_2 , $session['keys']['oauth_token']);
        $this->assertEquals($url , $url_2);
    }

    public function testSetLastKey()
    {
        file_get_contents('http://127.0.0.1/myweibo/collback.php?oauth_token=8542ec2982da4611999a7ed7a44008d5&oauth_verifier=205730');

        $this->_my_weibo->validateAuth();
    }
}
