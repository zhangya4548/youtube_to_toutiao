<?php
//当前目录
$path = __DIR__;

require_once($path.DIRECTORY_SEPARATOR.'db.php');

$Conn = new Mysql();


//应用key

$vodRes = $Conn ->Table('vod_xcc_key')-> Where('id=1')->Select();
$clientKey = $vodRes['client_key'] ?? '';
$clientSecret = $vodRes['client_secret'] ?? '';
$redirectUri = $vodRes['redirect_uri'] ?? ''; //回调地址



//获取code地址
$codeUrl = "https://open.snssdk.com/auth/authorize/?response_type=code&auth_only=1&client_key="
    .$clientKey."&redirect_uri=".$redirectUri."&state=febac09284cba";


header("location:".$codeUrl);
