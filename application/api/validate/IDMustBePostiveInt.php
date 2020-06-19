<?php


namespace app\api\validate;

class IDMustBePostiveInt extends BaseValidate
{
    /*定义规则*/
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];

    protected $message = [
        'id' => 'id参数必须是以逗号分隔的多个正整数'
    ];

}