<?php


namespace app\api\service;


use app\api\model\User;
use app\lib\enum\ScopeEnum;
use app\lib\exception\weChatException;
use think\Console;
use think\Exception;
use app\api\model\User as UserModel;
use app\lib\exception\TokenException;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        /*sprintf将内容拼接到login_url中*/
        $this->code = $code;
         $this->wxAppID = config('wx.app_id');
         $this->wxAppSecret = config('wx.app_secret');
         $this->wxLoginUrl = sprintf(config('wx.login_url'),
             $this->wxAppID,$this->wxAppSecret,$this->code);
      //  $this->wxLoginUrl = "https://api.weixin.qq.com/sns/jscode2session?appid=wx302a8698d338b90f&secret=9dffbc11b708c0633a2b01490490ec4c&js_code=".$code."&grant_type=authorization_code";
    }

    public function get(){
        /*发送给公共应用common类的http请求*/
  //      $result = curl_get($this->wxLoginUrl);
          $result =  file_get_contents($this->wxLoginUrl);//需要开启php.ini 的extension=php_openssl.dll
          $wxResult = json_decode($result,true);//将字符串转成数组。如要换成对象，把true改成false
          if(empty($wxResult)){
            throw new Exception('获取session_key及openID时异常，微信内部错误');
         }else{
            $loginFail = array_key_exists('errcode',$wxResult);// 检查数组里是否有指定的键名
            if($loginFail){
                $this->processLoginError($wxResult);
            }else{
                $result =  $this->grantToken($wxResult);
                return $result;
            }
        }
    }


    //token请求方法
    private function grantToken($wxResult){
        //拿到open_id
        //匹配数据库看看是否存在
        //如果存在则不处理，否则新建记录
        //生成令牌，准备缓存数据，写入缓存
        //把令牌返回到客户端去
        //key:令牌
        //value:wxResult,uid,scope
        $openid = $wxResult['openid'];
        $result = UserModel::getByOpenID($openid);//查找数据库
        if(!$result){
            $uid = $this->newUser($openid);
        }else{
             $uid = $result->id;
        }

        $cachedValue = $this->prepareCachedValue($wxResult,$uid);//拼接缓存信息

        $token = $this->saveToCache($cachedValue);

        return $token;

    }

    //生成令牌，写入缓存
    private function saveToCache($cachedValue){
        $key = self::generateToken();
        $value = json_encode($cachedValue);//将数组转为json格式的字符串
        $expire_in = config('setting.token_expire_in');//读取自定义的过期秒数

        //主缓存方法
        $result = cache($key, $value, $expire_in);
        if(!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }

        return $key;
    }

    private function prepareCachedValue($wxResult,$uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;//代表APP用户的权限数值

        //$cachedValue['scope'] = 32;//代表CMS用户的权限数值
        return $cachedValue;
    }

    private function newUser($openid){
        // 有可能会有异常，如果没有特别处理
        // 这里不需要try——catch
        // 全局异常处理会记录日志
        // 并且这样的异常属于服务器异常
        // 也不应该定义BaseException返回到客户端
        $data = array();
        $data['openid'] = $openid;
        $user = UserModel::create($data);
        $uids = $user->id;
        return $uids;
    }


    private function processLoginError($wxResult){
        throw new weChatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }
}