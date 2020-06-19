<?php


namespace app\api\controller\v1;
use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use think\Controller;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkScope' => [ 'only' => 'createOrUpdateAddress']
    ];




    public function createOrUpdateAddress(){
        $validate = new AddressNew();
        //$validate->goCheck();
        //根据token获取缓存里的uid
        //根据uid来查找用户数据，判断用户是否存在，不存在则抛出异常
        //获取用户在客户端提交的地址信息
        //匹配数据库是否存在，判断是添加地址还是更新地址
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user) {
            throw new UserException();
        }

        //获取客户端输入的所有参数
        $dataArray = $validate->getDataByRule(input('post.'));//进行过滤
        $userAddress = $user->address();
        if(!$userAddress){
            $user->address()->save($dataArray);
        }else{
            $user->address()->save($dataArray);
        }
        return new SuccessMessage;
        }


        public function addretest(){

            $validate = new AddressNew();
            $uid = '3';
            $user = UserModel::get($uid);
            $dataArray = [
                'mobile'=>'13128222219',
                'province'=>'广东省',
                'city'=>'广州市',
                'contry'=>'天河区',
                'detail'=>'人民大道',
                'user_id'=>$uid
            ];
            //$user->address()->save($dataArray);
            var_dump($user->address);




        }
}