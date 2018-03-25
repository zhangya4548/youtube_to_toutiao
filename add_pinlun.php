<?php
set_time_limit(0);

//当前目录
$path = __DIR__;
//加载必要类库
require_once($path . DIRECTORY_SEPARATOR . 'vendor/autoload.php');

//初始化
$curl = new \Curl\Curl();

//post带参数,带cookie,带头信息
$curl->setCookie('UM_distinctid', "15f89be516453d-06e75bfcd1aa4c-193e6d56-fa000-15f89be51659cb");
$curl->setCookie('uuid', "w:6c10379af8f94eb7a0a9ac16058912fc");
$curl->setCookie('login_flag', "88741a1917df2361a3a4b6c6130fa535");
$curl->setCookie('uid_tt', "64ad4234be775162e15d932320c410d9");
$curl->setCookie('sid_tt', "670daf1e22abb684ba7c3d2da205b7f7");
$curl->setCookie('_ba', "BA0.2-20171105-5110e-f81CXwI2dMiGoqFKWc1L");
$curl->setCookie('sso_login_status', "1");
$curl->setCookie('sessionid', "670daf1e22abb684ba7c3d2da205b7f7");
$curl->setCookie('sid_guard', '670daf1e22abb684ba7c3d2da205b7f7|1520351343|15552000|Sun\054 02-Sep-2018 15:49:03 GMT');
$curl->setCookie('_mp_auth_key', "3c4787d12975251031a3234c677b0d05");
$curl->setCookie('tt_webid', "6530588804427892231");
$curl->setCookie('__tea_sdk__user_unique_id', "3760032188");
$curl->setCookie('__tea_sdk__ssid', "37cecaec-2766-4c40-8e60-897e481335df");
$curl->setCookie('tt_im_token', "1521726335783893763816842547442063841170041346591967028098238022");
$curl->setCookie('_gid', "GA1.2.587528468.1521875780");
$curl->setCookie('ptcn_no', "a8bf6fddb38433e9e66cd959aa56ee1f");
$curl->setCookie('_ga', "GA1.3.24561533.1509845580");
$curl->setCookie('UM_distinctid', "15f89be516453d-06e75bfcd1aa4c-193e6d56-fa000-15f89be51659cb");
$curl->setCookie('uuid', 'w:6c10379af8f94eb7a0a9ac16058912fc');
$curl->setCookie('login_flag', "88741a1917df2361a3a4b6c6130fa535");
$curl->setCookie('uid_tt', "64ad4234be775162e15d932320c410d9");
$curl->setCookie('sid_tt', "670daf1e22abb684ba7c3d2da205b7f7");
$curl->setCookie('_ba', "BA0.2-20171105-5110e-f81CXwI2dMiGoqFKWc1L");
$curl->setCookie('sso_login_status', "1");
$curl->setCookie('sessionid', "670daf1e22abb684ba7c3d2da205b7f7");
$curl->setCookie('sid_guard', '670daf1e22abb684ba7c3d2da205b7f7|1520351343|15552000|Sun\054 02-Sep-2018 15:49:03 GMT');
$curl->setCookie('_mp_auth_key', "3c4787d12975251031a3234c677b0d05");
$curl->setCookie('tt_webid', "6530588804427892231");
$curl->setCookie('__tea_sdk__user_unique_id', "3760032188");
$curl->setCookie('__tea_sdk__ssid', "37cecaec-2766-4c40-8e60-897e481335df");
$curl->setCookie('tt_im_token', "1521726335783893763816842547442063841170041346591967028098238022");
$curl->setCookie('_gid', "GA1.2.587528468.1521875780");
$curl->setCookie('ptcn_no', "a8bf6fddb38433e9e66cd959aa56ee1f");
$curl->setCookie('_ga', "GA1.3.24561533.1509845580");
$curl->setCookie('UM_distinctid', "15f89be516453d-06e75bfcd1aa4c-193e6d56-fa000-15f89be51659cb");
$curl->setCookie('uuid', "w:6c10379af8f94eb7a0a9ac16058912fc");
$curl->setCookie('login_flag', "88741a1917df2361a3a4b6c6130fa535");
$curl->setCookie('uid_tt', "64ad4234be775162e15d932320c410d9");
$curl->setCookie('sid_tt', "670daf1e22abb684ba7c3d2da205b7f7");
$curl->setCookie('_ba', "BA0.2-20171105-5110e-f81CXwI2dMiGoqFKWc1L");
$curl->setCookie('_ga', "GA1.2.24561533.1509845580");
$curl->setCookie('_ga', "GA1.3.24561533.1509845580");
$curl->setCookie('sso_login_status', "1");
$curl->setCookie('sessionid', "670daf1e22abb684ba7c3d2da205b7f7");
$curl->setCookie('sid_guard', '670daf1e22abb684ba7c3d2da205b7f7|1520351343|15552000|Sun\054 02-Sep-2018 15:49:03 GMT');
$curl->setCookie('_mp_auth_key', "3c4787d12975251031a3234c677b0d05");
$curl->setCookie('tt_webid', "6530588804427892231");
$curl->setCookie('__tea_sdk__user_unique_id', "3760032188");
$curl->setCookie('__tea_sdk__ssid', "37cecaec-2766-4c40-8e60-897e481335df");
$curl->setCookie('tt_im_token', "1521726335783893763816842547442063841170041346591967028098238022");
$curl->setCookie('_gid', "GA1.2.587528468.1521875780");

//设置浏览器头
$curl->setUserAgent("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");

//头信息
$curl->setReferrer('https://mp.toutiao.com/profile_v3/index/comment/new');
$curl->setHeader('origin', 'https://mp.toutiao.com');
$curl->setHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
$curl->setHeader('accept', 'application/json, text/plain, */*');
$curl->setHeader('authority', 'mp.toutiao.com');

//获取评论列表
$res = $curl->get('https://mp.toutiao.com/comment/?cursor=0');
if ($res->message === 'success') {

    //遍历检测没有回复的评论
    foreach ($res->data as $key => $value) {
        if ((int)$value->comment_count === 0) {

            //随机评论内容
            $comment = getComment();

            //组织回复参数
            $arr = [
                'index'            => $key,
                'dongtai_id'       => $value->dongtai_id,
                'comment_id'       => $value->comment_id,
                'user_id'          => $value->user->user_id,
                'group_source'     => $value->group->group_source,
                'group_id'         => $value->group->group_id,
                'item_id'          => $value->group->item_id,
                'cursor'           => $value->create_time,
                'type'             => $value->group->pgc_article_type,
                'content'          => $comment,
                'reply_comment_id' => $value->comment_id,
                'reply_user_id'    => $value->user->user_id,
            ];
            //var_dump($arr);
            $res = $curl->post('https://mp.toutiao.com/comment/action/', $arr);
            //var_dump($res);
            //var_dump($curl->requestHeaders); //打印请求头
            //var_dump($curl->responseHeaders);//打印返回头


            //检测请求状态
            if ($curl->error) {
                phpLog( '评论异常: ' . $curl->errorCode . ': ' . $curl->errorMessage);
            }
            // else {
            //     var_dump($curl->response);
            // }
            // var_dump(11111111);die;

            //seep 10秒
            sleep(10);
        }
    }
}


//随机获取评论
function getComment ()
{

    $arr = [
        '哈哈哈谢谢您参与进来发布评论,请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '哈哈哈你说的很对,谢谢您的评论,请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '谢谢您的评论,我们不是神，即使是，也无法做到万无一失,请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '谢谢您的评论,如果有失误，我们定当负责并努力让您满意,请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,您的满意，就是我们最大的欣慰。最大的回报。请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,有您的支持，我们会做得更好！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,有您的支持，我们会做得更好！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,认识你是我最大的福气！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论，感谢对我的支持，为您竭力尽心提供优质内容！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论，您的喜欢就是对我最大的肯定！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论，这是我前进的最大动力，有了您的支持我会越做越好！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '您的一个评论，让我有了更大的动力哦！！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,您的评论胜过千言万语！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,要不是您，我们可能现在...！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '每一天，每一刻，因为有您的支持，我才能够做到更好！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,可所谓是雪中送炭啊！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,以后我会报答的！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,有什么不妥的地方还望见谅！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
        '感谢您的评论,我就是为人民服务,请叫我雷锋！请关注我点赞支持下,我们每天都会更新更多精彩视频内容',
    ];


    $str = array_rand($arr, 1);

    return $arr[$str];
}

//调试时候用来写日志文件的函数
function phpLog ($str)
{
    $time = "\n\t" . date('Y-m-d H:i:s', time()) . "------------------------------------------------------------------------------------\n\t";
    if (is_array($str)) {
        $str = var_export($str, true);
    }
    file_put_contents('./log.php', $time . $str, FILE_APPEND);
}
