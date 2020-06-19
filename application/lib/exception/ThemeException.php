<?php


namespace app\lib\exception;


class ThemeException extends BaseException
{
    public $code = 400;
    public $msg = '请求的theme不存在';
    public $errorCode = 20000;
}