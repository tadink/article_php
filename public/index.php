<?php
error_reporting(E_ALL);
ini_set('display_errors', false);
$basePath=dirname(__DIR__);
$templateFileMap = [
    "index" => "index.php",
    "list" => "list.php",
    "detail" => "detail.php",
];
$config = include  "{$basePath}/config.php";
include  "{$basePath}/db.php";

$host = $_SERVER["HTTP_HOST"];
$db = new Db($config['db']);
$siteConfig = $db->getSiteConfig($host);
$templateName = $siteConfig['template_name'] ?? "default";
$action = $_GET['action'] ?? "detail";
if ($action == "detail") {
    $path = $_SERVER["PATH_INFO"];
    $path = explode("/", $path);
    $id = end($path);
    $article=$db->getArticle($id);
}
$templateFile = $templateFileMap[$action];
include  "{$basePath}/template/{$templateName}/{$templateFile}";



