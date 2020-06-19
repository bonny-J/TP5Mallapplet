<?php


namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 400;
    public $msg = '请求的用户不存在';
    public $errorCode = 20000;
}