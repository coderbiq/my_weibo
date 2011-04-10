<?php
require_once 'myweibo.php';

class Test extends PHPUnit_Framework_TestCase
{
    protected $_my_weibo;

    public function setup()
    {
        $this->_my_weibo = new MyWeibo();
    }

    public function testGetNews()
    {
        $data = $this->_my_weibo->getNews();

        $this->assertEquals($data['ret'] , 0);
        $this->assertFileExists('news');
        $this->assertTrue(time()-filectime('news') < 60);
    }
}
