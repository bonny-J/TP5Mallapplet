<?php


namespace app\lib\exception;


class weChatException extends BaseException
{
    public $code = 400;
    public $msg = '请求的weChat不存在';
    public $errorCode = 20000;
}