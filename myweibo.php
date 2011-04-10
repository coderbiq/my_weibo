<?php
require_once 'txwboauth.php';

class MyWeibo
{
    const WB_AKEY = '7ba27d3d95f14eae975527de18844865';
    const WB_SKEY = '822fa578fc5134baf1025e5684d54b03';
    const COLLBACK = 'http://127.0.0.1/myweibo/collback.php';

    protected $_tx_weibo_sdk;

    public function __construct()
    {
        $this->_tx_weibo_sdk = new WeiboOauth(self::WB_AKEY, self::WB_SKEY);
    }

    public function validateAuth()
    {
        $session = $this->getSession();

        if(!isset($session['last_key']))
            throw new Exception();
        if(!isset($session['last_key']['oauth_token_secret']))
            throw new Exception();
        if(!isset($session['last_key']['oauth_token']))
            throw new Exception();
    }

    public function getAuthorizeURL()
    {
        $keys = $this->_tx_weibo_sdk->getRequestToken(self::COLLBACK);

        $session = $this->getSession();
        $session['keys'] = $keys;
        $this->setSession($session);

        $url = $this->_tx_weibo_sdk->getAuthorizeURL($keys['oauth_token'] , false, '');

        return $url;
    }

    public function getNews()
    {
        $session = $this->getSession();
        if(!isset($session['keys']['oauth_token']))
            throw new Exception();
        if(!isset($session['keys']['oauth_token_secret']))
            throw new Exception();
        $this->_tx_weibo_sdk = new WeiboOauth(
            self::WB_AKEY, 
            self::WB_SKEY,
            $session['last_key']['oauth_token'],
            $session['last_key']['oauth_token_secret']
        );

        if(file_exists('news')&&(time()-filectime('data') < 60))
        {
            $data = file_get_contents('news');
            $data = json_decode($data);
        }
        else
        {
            $params = array(
                'format' => 'json',
                'pageflag' => 0,
                'reqnum' => 20,
                'pagetime' => 0,
                'name' => 'biqiang'
            );

            $data = $this->_tx_weibo_sdk->get('http://open.t.qq.com/api/statuses/broadcast_timeline', $params);

            if(file_exists('news')&&(!is_readable('news') || !is_writable('news')))
                chmod('data' , 0777);

            file_put_contents('news' , json_encode($data));
        }

        return $data;
    }

    public function setLastKey($_oauth_verifier)
    {
        $session = $this->getSession();
        if(!isset($session['keys']['oauth_token']))
            throw new Exception();
        if(!isset($session['keys']['oauth_token_secret']))
            throw new Exception();
        $this->_tx_weibo_sdk = new WeiboOauth(
            self::WB_AKEY, 
            self::WB_SKEY,
            $session['keys']['oauth_token'],
            $session['keys']['oauth_token_secret']
        );

        $last_key = $this->_tx_weibo_sdk->getAccessToken($_oauth_verifier);
        print_r($last_key);
        $session['last_key'] = $last_key;
        $this->setSession($session);
    }

    public function getSession()
    {
        if(!file_exists('data'))
            $session = array();
        else
        {
            $session = file_get_contents('data');
            if($session == null)
                $session = array();
            else
                $session = json_decode($session, true);
        }

        return $session;
    }

    public function setSession($_session)
    {
        $session = json_encode($_session);

        if(file_exists('data')&&(!is_readable('data') || !is_writable('data')))
            chmod('data' , 0777);

        file_put_contents('data', $session);
    }
}
