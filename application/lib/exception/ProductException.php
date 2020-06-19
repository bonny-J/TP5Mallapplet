<?php


namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 400;
    public $msg = '请求的product不存在';
    public $errorCode = 20000;
}