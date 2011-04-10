<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once 'myweibo.php';

$myweibo = new MyWeibo();

try{
    $myweibo->validateAuth();
    $data = $myweibo->getNews();
} catch( Exception $e ) {
    $url = $myweibo->getAuthorizeURL();
    Header(sprintf("Location:%s", $url));
}
?>
<!DOCTYPE HTML>
<html>
<body>
<style>
body { font-size: 14px; line-height: 20px; color: #333; }
img { display: block; margin: 5px;}
a {text-decoration: none; color: #456;}
a:hover { text-decoration: underline; }
div.article{
    padding: 5px;
    border: 1px solid #CCC; 
    border-top-left-radius: 4px 4px;
    border-top-right-radius: 4px 4px;
    border-bottom-right-radius: 4px 4px;
    border-bottom-left-radius: 4px 4px;
    -webkit-box-shadow: 2px 2px 2px #999;
    -moz-box-shadow: 2px 2px 2px #999;
    margin: 10px 5px;
}
div.article p{ padding: 0px; margin: 3px;}
div.article div { padding: 0px; margin: 0px; }
div.article div:nth-child(1) { width:50px; position: absolute;}
div.article div:nth-child(2) { margin-left: 60px;}
div.article div:nth-child(2) a.name { text-shadow: #AAA 1px 1px 1px; margin-bottom: 10px; }
div.article div:nth-child(2) p.time_from { text-shadow: #AAA 1px 1px 1px;font-size: 0.8em; margin-top: 10px; text-align: right;}
div.article div.source { 
    margin: 10px 0px 0px; 
    padding: 5px;
    border: 1px solid #CCC; 
    border-top-left-radius: 4px 4px;
    border-top-right-radius: 4px 4px;
    border-bottom-right-radius: 4px 4px;
    border-bottom-left-radius: 4px 4px;
}
</style>

<article>
<?php foreach($data['data']['info'] as $article): ?>
    <div class="article">
        <div><img src="<?php echo $article['head']; ?>/50" /></div>
        <div>
            <p>
                <a href="http://t.qq.com/<?php echo $article['name']; ?>" class="name">
                    <?php echo $article['nick']; ?>
                </a>：<?php echo $article['text']; ?>
            </p>
            <?php if($article['type'] == 2): ?>
            <div class="source">
                <p>
                    <a href="http://t.qq.com/<?php echo $article['source']['name']; ?>" class="name">
                        <?php echo $article['source']['nick']; ?>
                    </a>：<?php echo $article['source']['text']; ?>
                    <?php if($article['source']['image']): ?>
                        <?php foreach($article['source']['image'] as $image): ?>
                            <img src="<?php echo $image; ?>/160" />
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </p>
                <p class="time_from">
                    <?php echo date('Y年m月d日H点i分s秒' , $article['source']['timestamp']); ?>通过<?php echo $article['source']['from']; ?>发表
                </p>
            </div>
            <?php endif; ?>
            <p class="time_from">
                <?php echo date('Y年m月d日H点i分s秒' , $article['timestamp']); ?>通过<?php echo $article['from']; ?>发表
            </p>
        </div>
    </div>
<?php endforeach; ?>
</article>
</body>
</html>
