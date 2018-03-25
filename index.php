<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/7
 * Time: 14:13
 */
header('Access-Control-Allow-Origin: *');

//当前目录
$path = __DIR__;

require_once($path.DIRECTORY_SEPARATOR.'db.php');

$name = $_POST['name'] ?? '';
$url = $_POST['url'] ?? '';

if($url){

    $arr = [
        'name' => addslashes($name),
        'url' => addslashes($url),
    ];

    phpLog('添加视频参数');
    phpLog($arr);

    $Conn = new Mysql();
    $res = $Conn ->Table('vod_xcc')->Into($arr);
    phpLog('添加视频完成');
    phpLog($res);

    exit(json_encode(['msg'=>'添加完成']));
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


