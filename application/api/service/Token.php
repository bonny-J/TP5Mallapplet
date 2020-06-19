<?php


namespace app\api\service;


use app\lib\exception\TokenException;
use think\facade\Cache;
use think\Exception;
use think\facade\Request;

class Token
{
    public static function generateToken(){
        //32个字符随机组成字符串
        //&common
        $randChars = getRandChars(32);
        //用三组字符串，进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME'];
        //salt 盐
        $salt = config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);
    }

    /*获取token内容*/
    public static function getCurrentTokenVar($key){
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if(!$vars){
            throw new TokenException();
        }
        else{
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){//检查$vars里是否有$key
                return $vars[$key];
            }
            else{
                throw new Exception('尝试获取的token变量不存在');
            }
        }
    }

    public static function getCurrentUid(){
        //token
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    //判断缓存是否有token
    public static function verifyToken($token){
        $exist = Cache::get($token);//读取缓存
        if($exist){
            return true;
        }else{
            return false;
        }
    }

    //判断权限
    public static function needScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    //排除超级管理员权限判断
    public static function needincluseScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    //检查id是否合法
    public static function isValidOperate($checkUID){
        if(!$checkUID){
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkUID){
            return true;
        }return false;
    }
}