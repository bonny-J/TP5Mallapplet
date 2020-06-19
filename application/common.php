<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// 应用公共文件

//请求URL内容
function curl_get($url,&$httpCode = 0){
    // 创建一个新cURL资源
    $ch = curl_init();
    // 设置URL和相应的选项
    curl_setopt($ch,CURLOPT_URL,$url);
    //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

    //不做证书校验，部署在linux环境下请改为true
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
    // 抓取URL并把它传递给浏览器
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    // 关闭cURL资源，并且释放系统资源
    curl_close($ch);
    return $file_contents;

}

//生成随机符号
function getRandChars($length){
    $strs = null;
    $strPol = "ABCDEFGHIJKLNMOPQRSTUVWSYZ0123456789abcdefghijklnmopqrstuvwxyz";
    $max =strlen($strPol) -1;
    for($i=0;$i<$length;$i++){
        $strs.= $strPol[mt_rand(0,$max)];//这里用mt_rand替换了原来的rand,提高效率
    }
    return $strs;
}