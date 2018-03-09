<?php
set_time_limit(0);



//当前目录
$path = __DIR__;

require_once($path.DIRECTORY_SEPARATOR.'db.php');

exec("cd ". $path.DIRECTORY_SEPARATOR ."  &&  chown www:www  ".$path.DIRECTORY_SEPARATOR."*", $output);
phpLog('修改文件权限完成');


//循环读取目录下的所有文件
$name   = '';
$houzui = 'mp4';
$d      = dir($path);
while (false !== ($item = $d->read()))
{
    if ('.' === $item || '..' === $item)
    {
        continue;
    }

    //检测指定文件
    if (substr_count($item, $houzui))
    {
        $name = $item;
    }
}
$d->close();



$filepath = '';

//检测文件是否下载完
if ($name)
{

    $tmp = substr(strrchr($name, '.'), 1);
    if ($tmp === $houzui)
    {
        $filepath = $path.DIRECTORY_SEPARATOR . $name;
    }
}

$Conn = new Mysql();
var_dump($filepath);die;
if ($filepath)
{

    //获取上传地址
    list($uploadUrl, $res, $uploadId) = getUploadUrl();
    phpLog('获取饰品地址完成');
    phpLog($uploadUrl);
    phpLog($filepath);

    //上传视频
    $res = uploadMp4($filepath, $uploadUrl);
    $res = json_decode($res, true);
    phpLog('上传视频完成');
    phpLog($res);


    //获取视频信息
    $vodRes = $Conn ->Table('vod')-> Where('isuse=1')->Order('vod_id asc')->Select();
    $updateId = $vodRes['vod_id'] ?? 0;
    $fabuName = $vodRes['name'] ?? '新视频待命名';


    //发布视频文章
    $vodRes = $Conn ->Table('vod_toutiao_key')-> Where('id=1')->Select();
    $clientKey = $vodRes['client_key'] ?? '';
    $clientSecret = $vodRes['client_secret'] ?? '';
    $token        = $vodRes['access_token'] ?? '';

    ////应用key
    //$clientKey    = 'e52612939e98738b';
    //$clientSecret = 'a6850bbe8b2f9834a6c1022e455db54e';
    //$token        = '74ecd4385dc257a11ff3b8048d8ade950008';


    $fabuUrl      = 'https://mp.toutiao.com/open/new_article_post/';
    $data         = [
        'video_id'     => $uploadId, //视频id
        'video_name'   => $name,  //视频名称
        //'article_label' => '',  //视频标签	否	字符串，如果有多个标签，用";"分割，比如"abc;def"
        'video_tag'    => 'video_entertainment', //视频分类
        'title'        => $fabuName, //视频文章标题
        //'abstract'     => '测试发布视频文章简介2', //视频简介
        'save'         => 0, //1发布文章，0存草稿	否	不传为0
        'article_type' => 1, //文章类型，视频传1	是	视频文章该值必须传1
        'access_token' => $token,
        'client_key'   => $clientKey,
    ];

    $res = curl_content($fabuUrl, $data);
    $res = json_decode($res, true);
    phpLog('上传视频完成');
    phpLog($res);

    //删除文件
    if(false === empty($res['article_id'])){
        unlink($filepath);
        $res = $Conn ->Table('vod')-> Where('vod_id='.$updateId)->Edit(['isuse'=>2]);
    }

    //执行下一次下载
    $vodRes = $Conn ->Table('vod')-> Where('isuse=3')->Order('vod_id asc')->Select();
    if (true === empty($vodRes['url'])) {
        $vodRes = $Conn ->Table('vod')-> Where('isuse=0')->Order('vod_id asc')->Select();
        if (false === empty($vodRes['url'])) {

            $res = $Conn ->Table('vod')-> Where('vod_id='.$vodRes['vod_id'])->Edit(['isuse'=>3]);

            $log = $path.DIRECTORY_SEPARATOR.'log.php';

            exec("cd ". $path.DIRECTORY_SEPARATOR ."  &&  /usr/bin/youtube-dl -f 22 --proxy 'socks5://127.0.0.1:1080' ".$vodRes['url']." > ".$log." & echo $! ", $output);
            phpLog('下载视频开始');
            phpLog($output);

            $res = $Conn ->Table('vod')-> Where('vod_id='.$vodRes['vod_id'])->Edit(['isuse'=>1]);
            phpLog('下载视频完成');
            phpLog($res);
        }
    }
}






//=========================================================


/**
 * 访问网址并取得其内容
 *
 * @param $url         String 网址
 * @param $postFields  Array 将该数组中的内容用POST方式传递给网址中
 * @param $cookie_file string cookie文件
 * @param $r_or_w      string 写cookie还是读cookie或是两都都有，r读，w写，a两者，null没有cookie
 *
 * @return String 返回网址内容
 */
function curl_content($url, $postFields = null, $cookie_file = null, $r_or_w = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)'); // 模拟用户使用的浏览器
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    if ($cookie_file && ($r_or_w == 'a' || $r_or_w == 'w'))
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); // 存放Cookie信息的文件名称
    }
    if ($cookie_file && ($r_or_w == 'a' || $r_or_w == 'r'))
    {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (is_array($postFields) && 0 < count($postFields))
    {
        $postBodyString = "";
        foreach ($postFields as $k => $v)
        {
            $postBodyString .= "$k=" . urlencode($v) . "&";
        }
        unset($k, $v);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
    }

    $reponse = curl_exec($ch);
    if (curl_errno($ch))
    {
        throw new Exception(curl_error($ch), 0);
    }
    else
    {
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        /*if (200 !== $httpStatusCode){
            throw new Exception($reponse,$httpStatusCode);
            //print_r($reponse);exit;
        }*/
    }
    curl_close($ch);

    return $reponse;
}


/**
 * 获取上传地址
 * @return array
 */
function getUploadUrl(): array
{
    //当前目录
    $path = __DIR__;

    require_once($path.DIRECTORY_SEPARATOR.'db.php');

    $Conn = new Mysql();
    //应用key
    $vodRes = $Conn ->Table('vod_toutiao_key')-> Where('id=1')->Select();
    $clientKey = $vodRes['client_key'] ?? '';
    $clientSecret = $vodRes['client_secret'] ?? '';
    $token        = $vodRes['access_token'] ?? '';
    //应用key
    //$clientKey    = 'e52612939e98738b';
    //$clientSecret = 'a6850bbe8b2f9834a6c1022e455db54e';
    //$token        = '74ecd4385dc257a11ff3b8048d8ade950008';
    //单个上传
    $uploadUrl = 'https://mp.toutiao.com/open/video/get_upload_url/?access_token=' . $token . '&client_key=' . $clientKey;
    //分片上传
    //$uploadUrl = 'https://mp.toutiao.com/open/video/get_chunk_upload_info//?access_token='.$token.'&client_key='.$clientKey;
    $res = get_curl_contents($uploadUrl);
    $res = json_decode($res, true);
    if (isset($res['code']) && (int)$res['code'] !== 0)
    {
        phpLog('头条返回异常:' . $res);
    }

    $uploadUrl = $res['data']['upload_url'];
    $uploadId  = $res['data']['upload_id'];

    return array($uploadUrl, $res, $uploadId);
}


/**
 * curl上传文件
 *
 * @param $filepath  D:\git\comment\public\index.mp4
 * @param $uploadUrl https://mp.toutiao.com/open/video/get_upload_url/index.php
 * @param $formName  表单名称
 */
function uploadMp4($filepath, $uploadUrl, $formName = 'video_file')
{
    $curl = curl_init();
    if (class_exists('\CURLFile'))
    {
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        $data = array($formName => new \CURLFile(realpath($filepath)));//>=5.5
    }
    else
    {
        if (defined('CURLOPT_SAFE_UPLOAD'))
        {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
        }
        $data = array($formName => '@' . realpath($filepath));//<=5.5
    }

    curl_setopt($curl, CURLOPT_URL, $uploadUrl);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, "TEST");
    $result = curl_exec($curl);
    $error  = curl_error($curl);

    if ($error)
    {
        phpLog('视频上传失败:' . $error);
    }

    if (isset($result['code']) && (int)$result['code'] !== 0)
    {
        phpLog('视频上传失败:' . $result['message']);
    }

    return $result;
}

/**
 * 抓取的url链接内容
 *
 * @param string $url    要抓取的url链接,可以是http,https链接
 * @param int    $second 设置cURL允许执行的最长秒数
 *
 * @return mixed
 */
function get_curl_contents($url, $second = 5)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);//设置cURL允许执行的最长秒数
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//当此项为true时,curl_exec($ch)返回的是内容;为false时,curl_exec($ch)返回的是true/false

    //以下两项设置为FALSE时,$url可以为"https://login.yahoo.com"协议
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

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
