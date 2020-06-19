<?php


namespace app\api\controller\v1;
use app\api\model\User as UserModel;

class Test
{

        //换取OpenId
        public function Wx_GetOpenidByCode(){
            /*$code = "033VO2ej18kAts0KOgcj1YEeej1VO2ee";//获取code
            $appid ="wx302a8698d338b90f";
            $secret = "9dffbc11b708c0633a2b01490490ec4c";
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
            //通过code换取网页授权access_token
            $weixin =  file_get_contents($url);
            $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
            $array = get_object_vars($jsondecode);//转换成数组
            $openid = $array['openid'];//输出openid*/
            $uid = $this->addtokens('o3ehH4zGwRuO3Ed9iqCp5C4aLULs');
            return $uid;
        }

        //测试添加
        public function addtokens($openid){
            $result = UserModel::getByOpenID($openid);
           if($result==NULL){
                $data = array();
                $data['openid'] = $openid;
                $user = UserModel::create($data);
                $uid = $user->id;
            }else{
                $uid = $result->id;
            }

            return $uid;
        }

}