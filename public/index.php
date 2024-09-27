<?php
error_reporting(E_ALL);
ini_set('display_errors', false);
$basePath = dirname(__DIR__);
$host = $_SERVER["HTTP_HOST"];
if($cache=getCache($host)){
    echo $cache;
    exit;
}
$templateFileMap = [
    "index" => "index.php",
    "list" => "list.php",
    "detail" => "detail.php",
];
include "{$basePath}/db.php";
$db = new Db();
$siteConfig = $db->getSiteConfig($host);
$templateName = $siteConfig['template_name'] ?? "default";
$action = $_GET['action'] ?? "detail";
if ($action == "detail") {
    $path = $_SERVER["PATH_INFO"];
    $path = explode("/", string: $path);
    $id = end($path);
    $article = $db->getArticle(articleId: $id);
}
$templateFile = $templateFileMap[$action];
ob_start();
include "{$basePath}/template/{$templateName}/{$templateFile}";
$content=ob_get_clean();
setCache($host,$content);
echo $content;




function getCache($host)
{
    $cacheKey = $_SERVER['PATH_INFO'] . $_SERVER['QUERY_STRING'];
    $cacheKey = md5($cacheKey);
    $cache = file_get_contents("cache/{$host}/{$cacheKey}");
    return $cache;
}

function setCache($host,$content){
    $cacheKey = $_SERVER['PATH_INFO'] . $_SERVER['QUERY_STRING'];
    $cacheKey = md5($cacheKey);
    if(!is_dir("cache/{$host}")){
        mkdir("cache/{$host}",0777,true);
    }
    $cache = file_put_contents("cache/{$host}/{$cacheKey}",$content);
    return $cache;
}