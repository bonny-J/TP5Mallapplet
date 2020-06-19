<?php


namespace app\api\validate;
use think\Validate;

/*验证器*/
class TestValidate extends Validate
{
    protected $rule = [
        'name' => 'require|max:10',
        'email' => 'email'
        ];
}