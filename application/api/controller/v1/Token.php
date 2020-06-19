<?php


namespace app\api\controller\v1;


use app\api\validate\TokenGet;
use app\api\service\UserToken;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;
use think\Console;
use think\route\Rule;
use function MongoDB\BSON\toJSON;

class Token
{
    //获取token
    public function getToken($code = ''){
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);//实例化并拼接login_url
        $token = $ut->get();
        return $token;

        }

     //是否存在token
    //用于校验失效
     public function verifyToken($token = ''){
        if(!$token){
            throw new ParameterException([
                'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return json_encode(["isvalid"=> $valid]);
     }

    public function test1(){//作为调试用的，可忽略

    }

    public function gettest($code = ''){//作为调试用的，可忽略

    }
}