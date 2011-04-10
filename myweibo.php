<?php
require_once 'txwboauth.php';

class MyWeibo
{
    const WB_AKEY = '7ba27d3d95f14eae975527de18844865';
    const WB_SKEY = '822fa578fc5134baf1025e5684d54b03';
    const COLLBACK = 'http://127.0.0.1/myweibo/collback.php';

    protected $_tx_weibo_sdk;
    protected $_collection;

    public function __construct()
    {
        $this->_tx_weibo_sdk = new WeiboOauth(self::WB_AKEY, self::WB_SKEY);

        $mongo = new Mongo();
        $db = $mongo->selectDB('myweibo');
        $this->_collection = $db->selectCollection('user');

        $this->_user = $this->_collection->findOne(array('account.tencent.name' => 'biqiang'));
        if($this->_user === null)
        {
            $this->_user = array(
                'account' => array(
                    'tencent' => array(
                        'name' => 'biqiang'
                    )
                )
            );

            $this->_collection->save($this->_user);
            $this->_user = $this->_collection->findOne(array('account.tencent.name' => 'biqiang'));
        }
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

        $params = array(
            'format' => 'json',
            'pageflag' => 0,
            'reqnum' => 20,
            'pagetime' => 0,
            'name' => 'biqiang'
        );

        $data = $this->_tx_weibo_sdk->get('http://open.t.qq.com/api/statuses/broadcast_timeline', $params);

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
#        print_r($last_key);exit();
        $session['last_key'] = $last_key;
        $this->setSession($session);
    }

    public function getSession()
    {
        $session = $this->_user['account']['tencent'];
        return $session;
    }

    public function setSession($_session)
    {
        $this->_user['account']['tencent'] = array_merge($this->_user['account']['tencent'], $_session);

        $this->_collection->save($this->_user);
    }
}
