
获取文章
```
$article=$db->getArticle($articleId);
echo $article["id"];
echo $article["title"];
echo $article["summary"];
echo $article["pic"];
echo $article["content"];
echo $article["author"];
echo $article["type_id"];
echo $article["type_name"];


```

获取文章列表
```
$articles=$db->getArticleList($limit = 20);

```


php代码块
```
<?php
    $a=1;
?>
```
php 条件判断
```
<?php if($a>1):?>
<?php elseif($a>2):?>
<?php else:?>
<?php endif ?>
```

php 循环
```
<?php foreach($articles as $k=>$article):?>
<?php endforeach?>
```
```
<?php for($i=0;$i<10;$i++):?>
<?php endfor?>
```