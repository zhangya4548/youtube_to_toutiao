<?php

//当前目录
$path = __DIR__;

require_once($path.DIRECTORY_SEPARATOR.'db.php');

$Conn = new Mysql();


//应用key
$vodRes = $Conn ->Table('vod_xcc_key')-> Where('id=1')->Select();
$clientKey = $vodRes['client_key'] ?? '';
$clientSecret = $vodRes['client_secret'] ?? '';


if(false === empty($_GET)){
    phpLog('获取code返回');
    phpLog($_GET);

    if(false === empty($_GET['code'])){
        $tokenUrl = "https://open.snssdk.com/auth/token/?code=".$_GET['code']."&client_key=".$clientKey."&client_secret=".$clientSecret."&grant_type=authorize_code";

        $res = get_curl_contents($tokenUrl);
        phpLog('获取access_token返回');
        phpLog($res);

        $res = json_decode($res,true);
        if(isset($res['ret']) && (int)$res['ret'] === 1 ){
            phpLog('头条返回异常:'.$res);
        }

        //修改信息
        $res = $Conn ->Table('vod_xcc_key')
                     -> Where('id=1')
                     ->Edit(['access_token'=>$res['data']['access_token'], 'expires_in'=>date('Y-m-d H:i:s',$res['data']['expires_in'])]);

        print_r($res);

    }
}




/**
 * 抓取的url链接内容
 * @param string $url    要抓取的url链接,可以是http,https链接
 * @param int $second    设置cURL允许执行的最长秒数
 * @return mixed
 */
function get_curl_contents($url, $second = 5)
{
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_TIMEOUT,$second);//设置cURL允许执行的最长秒数
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);//当此项为true时,curl_exec($ch)返回的是内容;为false时,curl_exec($ch)返回的是true/false

    //以下两项设置为FALSE时,$url可以为"https://login.yahoo.com"协议
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);

    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}



/**
 * 调试时候用来写日志文件的函数
 *
 * @param filename 保存的文件名
 *
 * @author <23585472@qq.com>
 */
function phpLog($str)
{
    $time = "\n\t" . date('Y-m-d H:i:s', time()) . "------------------------------------------------------------------------------------\n\t";
    if (is_array($str))
    {
        $str = var_export($str, true);
    }
    file_put_contents('./log.php', $time . $str, FILE_APPEND);
}